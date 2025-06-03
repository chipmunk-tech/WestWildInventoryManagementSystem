<?php
$success = $error = '';

// Handle category operations BEFORE any output
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';
    switch ($action) {
        case 'add':
            $name = $_POST['name'] ?? '';
            $description = $_POST['description'] ?? '';
            if (empty($name)) {
                $error = 'Category name is required';
            } else {
                try {
                    $stmt = $conn->prepare("INSERT INTO categories (name, description) VALUES (?, ?)");
                    $stmt->execute([$name, $description]);
                    $stmt = $conn->prepare("INSERT INTO activity_logs (user_id, action, description) VALUES (?, 'add_category', ?)");
                    $stmt->execute([$_SESSION['user_id'], "Added new category: $name"]);
                    $success = 'Category added successfully';
                } catch (PDOException $e) {
                    if ($e->getCode() == 23000) {
                        $error = 'Category name already exists';
                    } else {
                        $error = 'Error adding category: ' . $e->getMessage();
                    }
                }
            }
            break;
        case 'edit':
            $id = $_POST['id'] ?? '';
            $name = $_POST['name'] ?? '';
            $description = $_POST['description'] ?? '';
            if (empty($id) || empty($name)) {
                $error = 'Category ID and name are required';
            } else {
                try {
                    $stmt = $conn->prepare("UPDATE categories SET name = ?, description = ? WHERE id = ?");
                    $stmt->execute([$name, $description, $id]);
                    $stmt = $conn->prepare("INSERT INTO activity_logs (user_id, action, description) VALUES (?, 'edit_category', ?)");
                    $stmt->execute([$_SESSION['user_id'], "Updated category: $name"]);
                    $success = 'Category updated successfully';
                } catch (PDOException $e) {
                    if ($e->getCode() == 23000) {
                        $error = 'Category name already exists';
                    } else {
                        $error = 'Error updating category: ' . $e->getMessage();
                    }
                }
            }
            break;
        case 'delete':
            $id = $_POST['id'] ?? '';
            $force_delete = isset($_POST['force_delete']) && $_POST['force_delete'] === 'true';
            if (empty($id)) {
                $error = 'Category ID is required';
            } else {
                try {
                    $stmt = $conn->prepare("SELECT COUNT(*) FROM items WHERE category_id = ?");
                    $stmt->execute([$id]);
                    $itemCount = $stmt->fetchColumn();
                    if ($itemCount > 0 && !$force_delete) {
                        $error = 'Cannot delete category that has items assigned to it';
                    } else {
                        $stmt = $conn->prepare("SELECT name FROM categories WHERE id = ?");
                        $stmt->execute([$id]);
                        $categoryName = $stmt->fetchColumn();
                        if ($itemCount > 0 && $force_delete) {
                            $stmt = $conn->prepare("DELETE FROM items WHERE category_id = ?");
                            $stmt->execute([$id]);
                        }
                        $stmt = $conn->prepare("DELETE FROM categories WHERE id = ?");
                        $stmt->execute([$id]);
                        $stmt = $conn->prepare("INSERT INTO activity_logs (user_id, action, description) VALUES (?, 'delete_category', ?)");
                        $stmt->execute([$_SESSION['user_id'], "Deleted category: $categoryName" . ($itemCount > 0 && $force_delete ? ' (and all its items)' : '')]);
                        $success = 'Category deleted successfully' . ($itemCount > 0 && $force_delete ? ' (and all its items)' : '');
                    }
                } catch (Exception $e) {
                    $error = 'Error deleting category: ' . $e->getMessage();
                }
            }
            break;
    }
    // After handling POST actions, set session variables for feedback and redirect
    if ($success) {
        $_SESSION['feedback_message'] = $success;
        $_SESSION['feedback_type'] = 'success';
        header('Location: ?page=manage-categories');
        exit;
    }
    if ($error) {
        $_SESSION['feedback_message'] = $error;
        $_SESSION['feedback_type'] = 'danger';
        header('Location: ?page=manage-categories');
        exit;
    }
}

// Get all categories with item counts
$stmt = $conn->query("
    SELECT c.*, COUNT(i.id) as item_count 
    FROM categories c 
    LEFT JOIN items i ON c.id = i.category_id 
    GROUP BY c.id 
    ORDER BY c.name
");
$categories = $stmt->fetchAll();
?>

<div class="dashboard-bg">
    <div class="container-fluid px-3 px-md-4 py-4">
        <h2 class="mb-4 fw-bold text-dark">Manage Categories</h2>
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card dashboard-section" style="max-width:900px;margin:auto;">
                    <div class="card-header section-header bg-light text-dark border-bottom d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-tags me-2 text-success"></i> <span class="fw-semibold">Categories</span></span>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                            <i class="fas fa-plus"></i> Add New Category
                        </button>
                    </div>
                    <div class="card-body py-3">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Name</th>
                                        <th>Description</th>
                                        <th>Items</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($categories as $category): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($category['name']); ?></td>
                                            <td><?php echo htmlspecialchars($category['description']); ?></td>
                                            <td><?php echo $category['item_count']; ?></td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-primary edit-category" 
                                                        data-id="<?php echo $category['id']; ?>"
                                                        data-name="<?php echo htmlspecialchars($category['name']); ?>"
                                                        data-description="<?php echo htmlspecialchars($category['description']); ?>">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-danger delete-category"
                                                        data-id="<?php echo $category['id']; ?>"
                                                        data-name="<?php echo htmlspecialchars($category['name']); ?>"
                                                        data-item-count="<?php echo $category['item_count']; ?>">
                                                    <i class="fas fa-trash"></i>
                                                </button>
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

<!-- Add Category Modal -->
<div class="modal fade" id="addCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="">
                <input type="hidden" name="action" value="add">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Category Name *</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Category</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Category Modal -->
<div class="modal fade" id="editCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id" id="edit_id">
                
                <div class="modal-header">
                    <h5 class="modal-title">Edit Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_name" class="form-label">Category Name *</label>
                        <input type="text" class="form-control" id="edit_name" name="name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_description" class="form-label">Description</label>
                        <textarea class="form-control" id="edit_description" name="description" rows="3"></textarea>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Category</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Category Modal -->
<div class="modal fade" id="deleteCategoryModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" id="delete_id">
                <input type="hidden" name="force_delete" id="force_delete" value="false">
                <div class="modal-header">
                    <h5 class="modal-title">Delete Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete the category "<span id="delete_name"></span>"?</p>
                    <p class="text-danger" id="delete_warning"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger" id="delete_confirm_btn">Delete Category</button>
                </div>
            </form>
        </div>
    </div>
</div>

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

<script>
$(document).ready(function() {
    // Initialize DataTable
    $('.table').DataTable({
        order: [[0, 'asc']],
        pageLength: 25,
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rtip',
        drawCallback: function() {
            // Reattach event handlers after table redraw
            attachEventHandlers();
        }
    });
    
    function attachEventHandlers() {
        // Handle edit button click
        $('.edit-category').click(function() {
            const id = $(this).data('id');
            const name = $(this).data('name');
            const description = $(this).data('description');
            
            $('#edit_id').val(id);
            $('#edit_name').val(name);
            $('#edit_description').val(description);
            
            $('#editCategoryModal').modal('show');
        });
        
        // Handle delete button click
        $('.delete-category').click(function() {
            const id = $(this).data('id');
            const name = $(this).data('name');
            const itemCount = $(this).data('item-count');
            $('#delete_id').val(id);
            $('#delete_name').text(name);
            if (itemCount > 0) {
                $('#delete_warning').text('This category has ' + itemCount + ' item(s). Deleting this category will also delete all its items. This action cannot be undone.');
                $('#force_delete').val('true');
            } else {
                $('#delete_warning').text('This action cannot be undone.');
                $('#force_delete').val('false');
            }
            $('#deleteCategoryModal').modal('show');
        });
    }
    
    // Initial attachment of event handlers
    attachEventHandlers();
});
</script>

<style>
body {
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