<?php
// Get all issued items that haven't been returned
$stmt = $conn->query("
    SELECT si.*, i.name as item_name, i.unit, c.name as category_name 
    FROM stock_issues si 
    JOIN items i ON si.item_id = i.id 
    JOIN categories c ON i.category_id = c.id 
    WHERE si.returned_at IS NULL 
    ORDER BY si.issued_at DESC
");
$issued_items = $stmt->fetchAll();

$success = $error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $issue_id = $_POST['issue_id'] ?? '';
    $return_notes = $_POST['return_notes'] ?? '';
    
    if (empty($issue_id)) {
        $error = 'Please select an item to return';
    } else {
        try {
            $conn->beginTransaction();
            
            // Get the issue details
            $stmt = $conn->prepare("
                SELECT si.*, i.name as item_name, i.id as item_id 
                FROM stock_issues si 
                JOIN items i ON si.item_id = i.id 
                WHERE si.id = ?
            ");
            $stmt->execute([$issue_id]);
            $issue = $stmt->fetch();
            
            if (!$issue) {
                throw new Exception('Issue record not found');
            }
            
            // Update the stock issue record
            $stmt = $conn->prepare("
                UPDATE stock_issues 
                SET returned_at = NOW(), 
                    return_notes = ? 
                WHERE id = ?
            ");
            $stmt->execute([$return_notes, $issue_id]);
            
            // Update item quantity
            $stmt = $conn->prepare("
                UPDATE items 
                SET quantity = quantity + ? 
                WHERE id = ?
            ");
            $stmt->execute([$issue['quantity_issued'], $issue['item_id']]);
            
            // Record the stock movement
            $stmt = $conn->prepare("
                INSERT INTO stock_movements (item_id, movement_type, quantity, reference_type, created_by, notes) 
                VALUES (?, 'in', ?, 'return', ?, ?)
            ");
            $stmt->execute([
                $issue['item_id'], 
                $issue['quantity_issued'], 
                $_SESSION['user_id'], 
                "Returned from: " . $issue['issued_to']
            ]);
            
            // Log the activity
            $stmt = $conn->prepare("
                INSERT INTO activity_logs (user_id, action, description) 
                VALUES (?, 'return_item', ?)
            ");
            $stmt->execute([
                $_SESSION['user_id'], 
                "Returned {$issue['item_name']} from {$issue['issued_to']}"
            ]);
            
            $conn->commit();
            $success = 'Item returned successfully';
            
            // Clear form data
            $_POST = array();
            
            // Refresh the issued items list
            $stmt = $conn->query("
                SELECT si.*, i.name as item_name, i.unit, c.name as category_name 
                FROM stock_issues si 
                JOIN items i ON si.item_id = i.id 
                JOIN categories c ON i.category_id = c.id 
                WHERE si.returned_at IS NULL 
                ORDER BY si.issued_at DESC
            ");
            $issued_items = $stmt->fetchAll();
            
            // Refresh the items list for the dropdown
            $stmt = $conn->query("
                SELECT i.*, c.name as category_name 
                FROM items i 
                JOIN categories c ON i.category_id = c.id 
                WHERE i.quantity > 0 
                ORDER BY i.name
            ");
            $items = $stmt->fetchAll();
            
        } catch (Exception $e) {
            $conn->rollBack();
            $error = 'Error returning item: ' . $e->getMessage();
        }
    }
}
?>

<div class="dashboard-bg">
    <div class="container-fluid px-3 px-md-4 py-4">
        <h2 class="mb-4 fw-bold text-dark">Return Item</h2>
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card dashboard-section" style="max-width:900px;margin:auto;">
                    <div class="card-header section-header bg-light text-dark border-bottom">
                        <i class="fas fa-undo me-2 text-primary"></i> <span class="fw-semibold">Return Item</span>
                    </div>
                    <div class="card-body py-3">
                        <?php if ($success): ?>
                            <div class="alert alert-success"><?php echo $success; ?></div>
                        <?php endif; ?>
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>
                        
                        <?php if (empty($issued_items)): ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i> No items are currently issued out.
                            </div>
                        <?php else: ?>
                            <form method="POST" action="">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="issue_id" class="form-label">Select Issued Item *</label>
                                            <select class="form-select" id="issue_id" name="issue_id" required>
                                                <option value="">Select Item</option>
                                                <?php foreach ($issued_items as $item): ?>
                                                    <option value="<?php echo $item['id']; ?>" 
                                                            <?php echo (isset($_POST['issue_id']) && $_POST['issue_id'] == $item['id']) ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars(
                                                            $item['item_name'] . ' (' . $item['category_name'] . ') - ' .
                                                            'Issued to: ' . $item['issued_to'] . ' - ' .
                                                            'Quantity: ' . $item['quantity_issued'] . ' ' . $item['unit'] . ' - ' .
                                                            'Date: ' . date('Y-m-d H:i', strtotime($item['issued_at']))
                                                        ); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="return_notes" class="form-label">Return Notes</label>
                                            <textarea class="form-control" id="return_notes" name="return_notes" rows="3"><?php echo htmlspecialchars($_POST['return_notes'] ?? ''); ?></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-undo"></i> Return Item
                                        </button>
                                        <button type="reset" class="btn btn-secondary">
                                            <i class="fas fa-times"></i> Reset
                                        </button>
                                    </div>
                                </div>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
body, .dashboard-bg {
    background: #f7f9fb !important;
}
.dashboard-bg {
    min-height: 100vh;
    width: 100vw;
    position: absolute;
    left: 0; top: 0;
    z-index: 0;
}
.dashboard-section {
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    margin-bottom: 0;
    background: #fff;
}
.section-header {
    font-size: 1.05rem;
    font-weight: 500;
    border-radius: 10px 10px 0 0;
    padding: 0.75rem 1.25rem;
    margin-bottom: 0;
    display: flex;
    align-items: center;
    background: #f5f7fa !important;
    border-bottom: 1px solid #e3e6ea;
}
.card-body {
    padding: 1.25rem;
}
@media (max-width: 991.98px) {
    .dashboard-section {
        margin-bottom: 1rem;
    }
}
@media (max-width: 767.98px) {
    .container-fluid {
        padding-left: 0.5rem;
        padding-right: 0.5rem;
    }
}
</style> 