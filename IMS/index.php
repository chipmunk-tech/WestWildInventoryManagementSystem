<?php
session_start();
require_once 'config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id']) && basename($_SERVER['PHP_SELF']) != 'login.php') {
    header('Location: login.php');
    exit;
}

// Function to get issued items
function getIssuedItems($conn, $item_id) {
    $stmt = $conn->prepare("
        SELECT si.*, u.username as issued_by_name, i.name as item_name
        FROM stock_issues si
        JOIN users u ON si.issued_by = u.id
        JOIN items i ON si.item_id = i.id
        WHERE si.item_id = ? AND si.returned_at IS NULL
    ");
    $stmt->execute([$item_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Function to check if item is consumable
function isConsumableItem($item_name) {
    $consumable_keywords = ['water', 'coca', 'cola', 'drink', 'bottle', 'food', 'snack', 'candy', 'chocolate', 'biscuit', 'coffee', 'tea'];
    $item_name_lower = strtolower($item_name);
    
    foreach ($consumable_keywords as $keyword) {
        if (strpos($item_name_lower, $keyword) !== false) {
            return true;
        }
    }
    return false;
}

// Handle add, edit and delete actions before any output
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                $name = $_POST['name'] ?? '';
                $category_id = $_POST['category_id'] ?? '';
                $quantity = $_POST['quantity'] ?? 0;
                $unit = $_POST['unit'] ?? '';
                $supplier = $_POST['supplier'] ?? '';
                $min_stock_level = $_POST['min_stock_level'] ?? 5;
                if (empty($name) || empty($category_id) || empty($unit)) {
                    $_SESSION['feedback_message'] = 'Please fill in all required fields';
                    $_SESSION['feedback_type'] = 'danger';
                    header('Location: ?page=add-item');
                    exit;
                }
                try {
                    $conn->beginTransaction();
                    $stmt = $conn->prepare("
                        INSERT INTO items (name, category_id, quantity, unit, supplier, min_stock_level) 
                        VALUES (?, ?, ?, ?, ?, ?)
                    ");
                    $stmt->execute([$name, $category_id, $quantity, $unit, $supplier, $min_stock_level]);
                    $item_id = $conn->lastInsertId();
                    $stmt = $conn->prepare("
                        INSERT INTO stock_movements (item_id, movement_type, quantity, reference_type, created_by, notes) 
                        VALUES (?, 'in', ?, 'initial', ?, 'Initial stock entry')
                    ");
                    $stmt->execute([$item_id, $quantity, $_SESSION['user_id']]);
                    $stmt = $conn->prepare("
                        INSERT INTO activity_logs (user_id, action, description) 
                        VALUES (?, 'add_item', ?)
                    ");
                    $stmt->execute([$_SESSION['user_id'], "Added new item: $name"]);
                    $conn->commit();
                    $_SESSION['feedback_message'] = 'Item added successfully';
                    $_SESSION['feedback_type'] = 'success';
                } catch (Exception $e) {
                    $conn->rollBack();
                    $_SESSION['feedback_message'] = 'Error adding item: ' . $e->getMessage();
                    $_SESSION['feedback_type'] = 'danger';
                }
                header('Location: ?page=add-item');
                exit;
                break;
            case 'edit':
                $id = $_POST['id'] ?? '';
                $name = $_POST['name'] ?? '';
                $category_id = $_POST['category_id'] ?? '';
                $quantity = $_POST['quantity'] ?? 0;
                $unit = $_POST['unit'] ?? '';
                $supplier = $_POST['supplier'] ?? '';
                $min_stock_level = $_POST['min_stock_level'] ?? 5;
                
                if (empty($id) || empty($name) || empty($category_id) || empty($unit)) {
                    $_SESSION['feedback_message'] = 'Please fill in all required fields';
                    $_SESSION['feedback_type'] = 'danger';
                    header('Location: ?page=view-items');
                    exit;
                }
                
                try {
                    $conn->beginTransaction();
                    
                    // Get old item data for logging
                    $stmt = $conn->prepare("SELECT * FROM items WHERE id = ?");
                    $stmt->execute([$id]);
                    $oldItem = $stmt->fetch();
                    
                    // Update the item
                    $stmt = $conn->prepare("
                        UPDATE items 
                        SET name = ?, category_id = ?, quantity = ?, unit = ?, supplier = ?, min_stock_level = ?
                        WHERE id = ?
                    ");
                    $stmt->execute([$name, $category_id, $quantity, $unit, $supplier, $min_stock_level, $id]);
                    
                    // Record stock movement if quantity changed
                    if ($oldItem['quantity'] != $quantity) {
                        $movement_type = $quantity > $oldItem['quantity'] ? 'in' : 'out';
                        $movement_quantity = abs($quantity - $oldItem['quantity']);
                        
                        $stmt = $conn->prepare("
                            INSERT INTO stock_movements (item_id, movement_type, quantity, reference_type, created_by, notes) 
                            VALUES (?, ?, ?, 'edit', ?, ?)
                        ");
                        $stmt->execute([
                            $id, 
                            $movement_type, 
                            $movement_quantity, 
                            $_SESSION['user_id'],
                            "Stock adjusted during item edit"
                        ]);
                    }
                    
                    // Log the activity
                    $stmt = $conn->prepare("
                        INSERT INTO activity_logs (user_id, action, description) 
                        VALUES (?, 'edit_item', ?)
                    ");
                    $stmt->execute([$_SESSION['user_id'], "Edited item: $name"]);
                    
                    $conn->commit();
                    $_SESSION['feedback_message'] = 'Item updated successfully';
                    $_SESSION['feedback_type'] = 'success';
                    
                } catch (Exception $e) {
                    $conn->rollBack();
                    $_SESSION['feedback_message'] = 'Error updating item: ' . $e->getMessage();
                    $_SESSION['feedback_type'] = 'danger';
                }
                
                header('Location: ?page=view-items');
                exit;
                break;

            case 'delete':
                $id = $_POST['id'] ?? '';
                $force_delete = isset($_POST['force_delete']) && $_POST['force_delete'] === 'true';
                
                if (empty($id)) {
                    $_SESSION['feedback_message'] = 'Item ID is required';
                    $_SESSION['feedback_type'] = 'danger';
                    header('Location: ?page=view-items');
                    exit;
                }
                
                try {
                    $conn->beginTransaction();
                    
                    // Get item details first
                    $stmt = $conn->prepare("SELECT name FROM items WHERE id = ?");
                    $stmt->execute([$id]);
                    $itemName = $stmt->fetchColumn();
                    
                    // Check if item has any stock issues
                    $issuedItems = getIssuedItems($conn, $id);
                    $hasIssues = !empty($issuedItems);
                    
                    // If item is consumable, treat it as if it has no issues
                    $isConsumable = isConsumableItem($itemName);
                    
                    if ($hasIssues && !$force_delete && !$isConsumable) {
                        // Store issued items info in session for display
                        $_SESSION['issued_items'] = $issuedItems;
                        $_SESSION['delete_item_id'] = $id;
                        $_SESSION['feedback_message'] = 'This item has active stock issues. Please return all items first or force delete.';
                        $_SESSION['feedback_type'] = 'warning';
                        header('Location: ?page=view-items');
                        exit;
                    }
                    
                    // If force deleting or consumable, mark all issues as returned
                    if (($hasIssues && $force_delete) || $isConsumable) {
                        $stmt = $conn->prepare("
                            UPDATE stock_issues 
                            SET returned_at = CURRENT_TIMESTAMP,
                                return_notes = ?
                            WHERE item_id = ? AND returned_at IS NULL
                        ");
                        $return_notes = $isConsumable ? 
                            'Item marked as consumed' : 
                            'Item deleted from system';
                        $stmt->execute([$return_notes, $id]);
                    }
                    
                    // Delete related records first
                    $stmt = $conn->prepare("DELETE FROM stock_movements WHERE item_id = ?");
                    $stmt->execute([$id]);
                    
                    $stmt = $conn->prepare("DELETE FROM stock_issues WHERE item_id = ?");
                    $stmt->execute([$id]);
                    
                    // Delete the item
                    $stmt = $conn->prepare("DELETE FROM items WHERE id = ?");
                    $stmt->execute([$id]);
                    
                    // Log the activity
                    $stmt = $conn->prepare("
                        INSERT INTO activity_logs (user_id, action, description) 
                        VALUES (?, 'delete_item', ?)
                    ");
                    $stmt->execute([
                        $_SESSION['user_id'], 
                        "Deleted item: $itemName" . 
                        ($force_delete ? " (Force deleted with active issues)" : "") .
                        ($isConsumable ? " (Consumable item)" : "")
                    ]);
                    
                    $conn->commit();
                    $_SESSION['feedback_message'] = 'Item deleted successfully' . 
                        ($force_delete ? ' (Force deleted)' : '') .
                        ($isConsumable ? ' (Consumable item)' : '');
                    $_SESSION['feedback_type'] = 'success';
                    
                } catch (Exception $e) {
                    $conn->rollBack();
                    $_SESSION['feedback_message'] = 'Error deleting item: ' . $e->getMessage();
                    $_SESSION['feedback_type'] = 'danger';
                }
                
                header('Location: ?page=view-items');
                exit;
                break;
        }
    }
}

// --- Export block for reports (moved from pages/reports.php) ---
if (isset($_GET['page']) && $_GET['page'] === 'reports' && isset($_GET['export']) && $_GET['export'] === 'excel') {
    $category_id = $_GET['category_id'] ?? '';
    $date_from = $_GET['date_from'] ?? '';
    $date_to = $_GET['date_to'] ?? '';
    $search = $_GET['search'] ?? '';
    require_once 'config/database.php';
    $query = "
        SELECT 
            i.id,
            i.name as item_name,
            c.name as category_name,
            i.quantity as current_stock,
            i.unit,
            i.min_stock_level,
            COALESCE(SUM(CASE WHEN sm.movement_type = 'in' THEN sm.quantity ELSE 0 END), 0) as total_in,
            COALESCE(SUM(CASE WHEN sm.movement_type = 'out' THEN sm.quantity ELSE 0 END), 0) as total_out,
            MAX(sm.created_at) as last_updated
        FROM items i
        JOIN categories c ON i.category_id = c.id
        LEFT JOIN stock_movements sm ON i.id = sm.item_id
        WHERE 1=1
    ";
    $params = [];
    if ($category_id) {
        $query .= " AND i.category_id = ?";
        $params[] = $category_id;
    }
    if ($date_from) {
        $query .= " AND sm.created_at >= ?";
        $params[] = $date_from . ' 00:00:00';
    }
    if ($date_to) {
        $query .= " AND sm.created_at <= ?";
        $params[] = $date_to . ' 23:59:59';
    }
    if ($search) {
        $query .= " AND (i.name LIKE ? OR c.name LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }
    $query .= " GROUP BY i.id ORDER BY i.name";
    $conn = new PDO('mysql:host=localhost;dbname=west_wild_ims', 'root', '');
    $stmt = $conn->prepare($query);
    $stmt->execute($params);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=items_report.csv');
    $output = fopen('php://output', 'w');
    if (!empty($rows)) {
        fputcsv($output, array_keys($rows[0]));
        foreach ($rows as $row) {
            fputcsv($output, $row);
        }
    }
    fclose($output);
    exit;
}

// Get current page
$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';

// Header
include 'includes/header.php';

// Main content
switch ($page) {
    case 'dashboard':
        include 'pages/dashboard.php';
        break;
    case 'add-item':
        include 'pages/add-item.php';
        break;
    case 'view-items':
        include 'pages/view-items.php';
        break;
    case 'issue-item':
        include 'pages/issue-item.php';
        break;
    case 'return-item':
        include 'pages/return-item.php';
        break;
    case 'manage-categories':
        include 'pages/manage-categories.php';
        break;
    case 'reports':
        include 'pages/reports.php';
        break;
    case 'profile':
        include 'pages/profile.php';
        break;
    default:
        include 'pages/dashboard.php';
}

// Footer
include 'includes/footer.php'; 