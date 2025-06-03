<?php
// Get all items with their current stock
$stmt = $conn->query("
    SELECT i.*, c.name as category_name 
    FROM items i 
    JOIN categories c ON i.category_id = c.id 
    WHERE i.quantity > 0 
    ORDER BY i.name
");
$items = $stmt->fetchAll();

$success = $error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $item_id = $_POST['item_id'] ?? '';
    $quantity = $_POST['quantity'] ?? 0;
    $issued_to = $_POST['issued_to'] ?? '';
    $expected_return_at = $_POST['expected_return_at'] ?? '';
    $notes = $_POST['notes'] ?? '';
    
    if (empty($item_id) || empty($quantity) || empty($issued_to) || empty($expected_return_at)) {
        $error = 'Please fill in all required fields';
    } else if (!isset($_SESSION['user_id'])) {
        $error = 'You must be logged in to issue items';
    } else {
        try {
            // Verify user exists
            $stmt = $conn->prepare("SELECT id FROM users WHERE id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            if (!$stmt->fetch()) {
                throw new Exception('Invalid user session. Please log in again.');
            }
            
            // Check if enough stock is available
            $stmt = $conn->prepare("SELECT quantity, name FROM items WHERE id = ?");
            $stmt->execute([$item_id]);
            $item = $stmt->fetch();
            
            if ($item['quantity'] < $quantity) {
                $error = "Not enough stock available. Current stock: {$item['quantity']}";
            } else {
                try {
                    $conn->beginTransaction();
                    
                    // Update item quantity
                    $stmt = $conn->prepare("
                        UPDATE items 
                        SET quantity = quantity - ? 
                        WHERE id = ?
                    ");
                    $stmt->execute([$quantity, $item_id]);
                    
                    // Record the stock issue
                    $stmt = $conn->prepare("
                        INSERT INTO stock_issues (item_id, quantity_issued, issued_to, issued_by, expected_return_at, notes) 
                        VALUES (?, ?, ?, ?, ?, ?)
                    ");
                    $stmt->execute([$item_id, $quantity, $issued_to, $_SESSION['user_id'], $expected_return_at, $notes]);
                    
                    // Record the stock movement
                    $stmt = $conn->prepare("
                        INSERT INTO stock_movements (item_id, movement_type, quantity, reference_type, created_by, notes) 
                        VALUES (?, 'out', ?, 'issue', ?, ?)
                    ");
                    $stmt->execute([$item_id, $quantity, $_SESSION['user_id'], "Issued to: $issued_to"]);
                    
                    // Log the activity
                    $stmt = $conn->prepare("
                        INSERT INTO activity_logs (user_id, action, description) 
                        VALUES (?, 'issue_item', ?)
                    ");
                    $stmt->execute([$_SESSION['user_id'], "Issued {$item['name']} to $issued_to"]);
                    
                    $conn->commit();
                    $success = 'Item issued successfully';
                    
                    // Clear form data
                    $_POST = array();
                    
                    // Refresh the items list
                    $stmt = $conn->query("
                        SELECT i.*, c.name as category_name 
                        FROM items i 
                        JOIN categories c ON i.category_id = c.id 
                        WHERE i.quantity > 0 
                        ORDER BY i.name
                    ");
                    $items = $stmt->fetchAll();
                } catch (Exception $e) {
                    if ($conn->inTransaction()) {
                        $conn->rollBack();
                    }
                    $error = 'Error issuing item: ' . $e->getMessage();
                }
            }
        } catch (Exception $e) {
            $error = 'Error checking stock: ' . $e->getMessage();
        }
    }
}
?>

<div class="dashboard-bg">
    <div class="container-fluid px-3 px-md-4 py-4">
        <h2 class="mb-4 fw-bold text-dark">Issue Item</h2>
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card dashboard-section" style="max-width:900px;margin:auto;">
                    <div class="card-header section-header bg-light text-dark border-bottom">
                        <i class="fas fa-hand-holding me-2 text-primary"></i> <span class="fw-semibold">Issue Item</span>
                    </div>
                    <div class="card-body py-3">
                        <?php if ($success): ?>
                            <div class="alert alert-success"><?php echo $success; ?></div>
                        <?php endif; ?>
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>
                        <form method="POST" action="" id="issueForm">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="item_id" class="form-label">Select Item *</label>
                                        <select class="form-select" id="item_id" name="item_id" required>
                                            <option value="">Select Item</option>
                                            <?php foreach ($items as $item): ?>
                                                <option value="<?php echo $item['id']; ?>" 
                                                        data-stock="<?php echo $item['quantity']; ?>"
                                                        data-unit="<?php echo $item['unit']; ?>"
                                                        <?php echo (isset($_POST['item_id']) && $_POST['item_id'] == $item['id']) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($item['name'] . ' (' . $item['category_name'] . ') - Stock: ' . $item['quantity'] . ' ' . $item['unit']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="quantity" class="form-label">Quantity to Issue *</label>
                                        <input type="number" class="form-control" id="quantity" name="quantity" 
                                               min="1" required value="<?php echo htmlspecialchars($_POST['quantity'] ?? ''); ?>">
                                        <small class="text-muted">Available stock: <span id="availableStock">0</span> <span id="stockUnit"></span></small>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="issued_to" class="form-label">Issued To *</label>
                                        <input type="text" class="form-control" id="issued_to" name="issued_to" 
                                               required value="<?php echo htmlspecialchars($_POST['issued_to'] ?? ''); ?>"
                                               placeholder="Enter name or department">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="expected_return_at" class="form-label">Expected Return Date & Time *</label>
                                        <input type="datetime-local" class="form-control" id="expected_return_at" name="expected_return_at" 
                                               required value="<?php echo htmlspecialchars($_POST['expected_return_at'] ?? ''); ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="notes" class="form-label">Notes</label>
                                        <textarea class="form-control" id="notes" name="notes" rows="3"><?php echo htmlspecialchars($_POST['notes'] ?? ''); ?></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-hand-holding"></i> Issue Item
                                    </button>
                                    <button type="reset" class="btn btn-secondary">
                                        <i class="fas fa-undo"></i> Reset
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Update available stock when item is selected
    $('#item_id').on('change', function() {
        const selectedOption = $(this).find('option:selected');
        const stock = selectedOption.data('stock');
        const unit = selectedOption.data('unit');
        $('#availableStock').text(stock);
        $('#stockUnit').text(unit);
        $('#quantity').attr('max', stock);
    });
    // Validate quantity before form submission
    $('#issueForm').on('submit', function(e) {
        const quantity = parseInt($('#quantity').val());
        const availableStock = parseInt($('#availableStock').text());
        if (quantity > availableStock) {
            e.preventDefault();
            alert('Cannot issue more than available stock!');
        }
    });
});
</script>

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
.table {
    margin-bottom: 0;
}
.table th {
    font-weight: 600;
    background-color: #f8f9fa;
    border-bottom: 2px solid #dee2e6;
}
.table td {
    vertical-align: middle;
    font-size: 0.97rem;
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
    .table td, .table th {
        font-size: 0.9rem;
    }
}
</style> 