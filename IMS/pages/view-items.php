<?php
// Get filter parameters
$category_id = $_GET['category_id'] ?? '';
$search = $_GET['search'] ?? '';

// Get all categories for filter
$stmt = $conn->query("SELECT * FROM categories ORDER BY name");
$categories = $stmt->fetchAll();

// Build the query
$query = "
    SELECT i.*, c.name as category_name,
           (SELECT COUNT(*) FROM stock_issues si WHERE si.item_id = i.id AND si.returned_at IS NULL) as issued_count
    FROM items i
    JOIN categories c ON i.category_id = c.id
    WHERE 1=1
";

$params = [];

if ($category_id) {
    $query .= " AND i.category_id = ?";
    $params[] = $category_id;
}

if ($search) {
    $query .= " AND (i.name LIKE ? OR c.name LIKE ? OR i.supplier LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$query .= " ORDER BY i.name";

// Execute the query
$stmt = $conn->prepare($query);
$stmt->execute($params);
$items = $stmt->fetchAll();

// Feedback modal logic for item actions
if (isset($success) && $success) {
    $_SESSION['feedback_message'] = $success;
    $_SESSION['feedback_type'] = 'success';
    header('Location: ?page=view-items');
    exit;
}
if (isset($error) && $error) {
    $_SESSION['feedback_message'] = $error;
    $_SESSION['feedback_type'] = 'danger';
    header('Location: ?page=view-items');
    exit;
}
?>

<div class="dashboard-bg">
    <div class="container-fluid px-3 px-md-4 py-4">
        <h2 class="mb-4 fw-bold text-dark">View Items</h2>
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card dashboard-section">
                    <div class="card-header section-header bg-light text-dark border-bottom d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-boxes me-2 text-primary"></i> <span class="fw-semibold">Items</span></span>
                        <a href="?page=add-item" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add New Item
                        </a>
                    </div>
                    <div class="card-body py-3">
                        <!-- Filters -->
                        <form method="GET" action="" class="mb-4">
                            <input type="hidden" name="page" value="view-items">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="category_id" class="form-label">Category</label>
                                        <select class="form-select" id="category_id" name="category_id">
                                            <option value="">All Categories</option>
                                            <?php foreach ($categories as $category): ?>
                                                <option value="<?php echo $category['id']; ?>" 
                                                        <?php echo $category_id == $category['id'] ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($category['name']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="search" class="form-label">Search</label>
                                        <input type="text" class="form-control" id="search" name="search" 
                                               placeholder="Search by item name, category, or supplier"
                                               value="<?php echo htmlspecialchars($search); ?>">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="mb-3">
                                        <label class="form-label">&nbsp;</label>
                                        <button type="submit" class="btn btn-primary w-100">
                                            <i class="fas fa-search"></i> Search
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <!-- Items Table -->
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Item Name</th>
                                        <th>Category</th>
                                        <th>Current Stock</th>
                                        <th>Unit</th>
                                        <th>Issued</th>
                                        <th>Min Stock</th>
                                        <th>Supplier</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($items as $item): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($item['name']); ?></td>
                                            <td><?php echo htmlspecialchars($item['category_name']); ?></td>
                                            <td><?php echo $item['quantity']; ?></td>
                                            <td><?php echo htmlspecialchars($item['unit']); ?></td>
                                            <td><?php echo $item['issued_count']; ?></td>
                                            <td><?php echo $item['min_stock_level']; ?></td>
                                            <td><?php echo htmlspecialchars($item['supplier']); ?></td>
                                            <td>
                                                <?php if ($item['quantity'] <= 0): ?>
                                                    <span class="badge bg-danger">Out of Stock</span>
                                                <?php elseif ($item['quantity'] <= $item['min_stock_level']): ?>
                                                    <span class="badge bg-warning">Low Stock</span>
                                                <?php else: ?>
                                                    <span class="badge bg-success">In Stock</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-sm btn-primary edit-item" 
                                                            data-id="<?php echo $item['id']; ?>"
                                                            data-name="<?php echo htmlspecialchars($item['name']); ?>"
                                                            data-category="<?php echo $item['category_id']; ?>"
                                                            data-quantity="<?php echo $item['quantity']; ?>"
                                                            data-unit="<?php echo htmlspecialchars($item['unit']); ?>"
                                                            data-min-stock="<?php echo $item['min_stock_level']; ?>"
                                                            data-supplier="<?php echo htmlspecialchars($item['supplier']); ?>">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-danger delete-item"
                                                            data-id="<?php echo $item['id']; ?>"
                                                            data-name="<?php echo htmlspecialchars($item['name']); ?>">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Item Modal -->
<div class="modal fade" id="editItemModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="index.php?page=edit-item">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id" id="edit_id">
                
                <div class="modal-header">
                    <h5 class="modal-title">Edit Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_name" class="form-label">Item Name *</label>
                        <input type="text" class="form-control" id="edit_name" name="name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_category_id" class="form-label">Category *</label>
                        <select class="form-select" id="edit_category_id" name="category_id" required>
                            <option value="">Select Category</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo $category['id']; ?>">
                                    <?php echo htmlspecialchars($category['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_quantity" class="form-label">Quantity *</label>
                        <input type="number" class="form-control" id="edit_quantity" name="quantity" min="0" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_unit" class="form-label">Unit *</label>
                        <select class="form-select" id="edit_unit" name="unit" required>
                            <option value="pcs">Pieces</option>
                            <option value="packs">Packs</option>
                            <option value="liters">Liters</option>
                            <option value="kg">Kilograms</option>
                            <option value="boxes">Boxes</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_min_stock_level" class="form-label">Minimum Stock Level</label>
                        <input type="number" class="form-control" id="edit_min_stock_level" name="min_stock_level" min="0">
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_supplier" class="form-label">Supplier</label>
                        <input type="text" class="form-control" id="edit_supplier" name="supplier">
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Item</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Item Modal -->
<div class="modal fade" id="deleteItemModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="index.php?page=delete-item">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" id="delete_id">
                
                <div class="modal-header">
                    <h5 class="modal-title">Delete Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                
                <div class="modal-body">
                    <p>Are you sure you want to delete the item "<span id="delete_name"></span>"?</p>
                    <p class="text-danger">This action cannot be undone.</p>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete Item</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Initialize DataTable
    $('.table').DataTable({
        order: [[0, 'asc']],
        pageLength: 25,
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rtip'
    });
    
    // Handle edit button click
    $('.edit-item').click(function() {
        const id = $(this).data('id');
        const name = $(this).data('name');
        const category = $(this).data('category');
        const quantity = $(this).data('quantity');
        const unit = $(this).data('unit');
        const minStock = $(this).data('min-stock');
        const supplier = $(this).data('supplier');
        
        $('#edit_id').val(id);
        $('#edit_name').val(name);
        $('#edit_category_id').val(category);
        $('#edit_quantity').val(quantity);
        $('#edit_unit').val(unit);
        $('#edit_min_stock_level').val(minStock);
        $('#edit_supplier').val(supplier);
        
        $('#editItemModal').modal('show');
    });
    
    // Handle delete button click
    $('.delete-item').click(function() {
        const id = $(this).data('id');
        const name = $(this).data('name');
        
        $('#delete_id').val(id);
        $('#delete_name').text(name);
        
        $('#deleteItemModal').modal('show');
    });
});
</script>

<?php if (isset($_SESSION['feedback_message'])): ?>
<script>
$(document).ready(function() {
    var type = '<?php echo $_SESSION['feedback_type']; ?>';
    var message = '<?php echo addslashes($_SESSION['feedback_message']); ?>';
    var title = (type === 'success') ? 'Success' : (type === 'danger' ? 'Error' : 'Info');
    $('#feedbackModalLabel').text(title);
    $('#feedbackModalBody').html('<div class="alert alert-' + type + ' mb-0">' + message + '</div>');
    var feedbackModal = new bootstrap.Modal(document.getElementById('feedbackModal'));
    feedbackModal.show();
});
</script>
<?php unset($_SESSION['feedback_message'], $_SESSION['feedback_type']); endif; ?>

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
.btn-group {
    gap: 0.25rem;
}
.modal-dialog-centered {
    display: flex;
    align-items: center;
    min-height: calc(100% - 1rem);
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

<?php if (isset($_SESSION['issued_items']) && isset($_SESSION['delete_item_id'])): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var modal = new bootstrap.Modal(document.getElementById('issuedItemsModal'));
    modal.show();
});
</script>
<?php 
// Clear the session variables after showing the modal
unset($_SESSION['issued_items']);
unset($_SESSION['delete_item_id']);
endif; 
?> 