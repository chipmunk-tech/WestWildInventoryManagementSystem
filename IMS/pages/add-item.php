<?php
// Get all categories
$stmt = $conn->query("SELECT * FROM categories ORDER BY name");
$categories = $stmt->fetchAll();

// The POST handling and redirection is now handled in index.php
// This file only contains the form for adding items and displays feedback

?>

<div class="dashboard-bg">
    <div class="container-fluid px-3 px-md-4 py-4">
        <h2 class="mb-4 fw-bold text-dark">Add New Item</h2>
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card dashboard-section" style="max-width:900px;margin:auto;">
                    <div class="card-header section-header bg-light text-dark border-bottom">
                        <i class="fas fa-plus-square me-2 text-primary"></i> <span class="fw-semibold">Add Item</span>
                    </div>
                    <div class="card-body py-3">
                        <form method="POST" action="">
                            <input type="hidden" name="action" value="add">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Item Name *</label>
                                        <input type="text" class="form-control" id="name" name="name" required 
                                               value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="category_id" class="form-label">Category *</label>
                                        <select class="form-select" id="category_id" name="category_id" required>
                                            <option value="">Select Category</option>
                                            <?php foreach ($categories as $category): ?>
                                                <option value="<?php echo $category['id']; ?>" 
                                                        <?php echo (isset($_POST['category_id']) && $_POST['category_id'] == $category['id']) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($category['name']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="quantity" class="form-label">Initial Quantity *</label>
                                        <input type="number" class="form-control" id="quantity" name="quantity" 
                                               min="0" required value="<?php echo htmlspecialchars($_POST['quantity'] ?? '0'); ?>">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="unit" class="form-label">Unit *</label>
                                        <select class="form-select" id="unit" name="unit" required>
                                            <option value="">Select Unit</option>
                                            <option value="pcs" <?php echo (isset($_POST['unit']) && $_POST['unit'] == 'pcs') ? 'selected' : ''; ?>>Pieces</option>
                                            <option value="packs" <?php echo (isset($_POST['unit']) && $_POST['unit'] == 'packs') ? 'selected' : ''; ?>>Packs</option>
                                            <option value="liters" <?php echo (isset($_POST['unit']) && $_POST['unit'] == 'liters') ? 'selected' : ''; ?>>Liters</option>
                                            <option value="kg" <?php echo (isset($_POST['unit']) && $_POST['unit'] == 'kg') ? 'selected' : ''; ?>>Kilograms</option>
                                            <option value="boxes" <?php echo (isset($_POST['unit']) && $_POST['unit'] == 'boxes') ? 'selected' : ''; ?>>Boxes</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="min_stock_level" class="form-label">Minimum Stock Level</label>
                                        <input type="number" class="form-control" id="min_stock_level" name="min_stock_level" 
                                               min="0" value="<?php echo htmlspecialchars($_POST['min_stock_level'] ?? '5'); ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="supplier" class="form-label">Supplier</label>
                                        <input type="text" class="form-control" id="supplier" name="supplier" 
                                               value="<?php echo htmlspecialchars($_POST['supplier'] ?? ''); ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Save Item
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
    // Add custom unit option
    $('#unit').on('change', function() {
        if ($(this).val() === 'custom') {
            const customUnit = prompt('Please enter custom unit:');
            if (customUnit) {
                $(this).append(new Option(customUnit, customUnit, true, true));
            } else {
                $(this).val('');
            }
        }
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