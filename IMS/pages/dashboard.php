<?php
// Get total items count
$stmt = $conn->query("SELECT COUNT(*) FROM items");
$total_items = $stmt->fetchColumn();

// Get total categories count
$stmt = $conn->query("SELECT COUNT(*) FROM categories");
$total_categories = $stmt->fetchColumn();

// Get low stock items (quantity < min_stock_level)
$stmt = $conn->query("SELECT COUNT(*) FROM items WHERE quantity < min_stock_level");
$low_stock_items = $stmt->fetchColumn();

// Get recent stock issues
$stmt = $conn->query("
    SELECT si.*, i.name as item_name, u.username as issued_by_name 
    FROM stock_issues si 
    JOIN items i ON si.item_id = i.id 
    JOIN users u ON si.issued_by = u.id 
    ORDER BY si.issued_at DESC 
    LIMIT 5
");
$recent_issues = $stmt->fetchAll();

// Get recent activities
$stmt = $conn->query("
    SELECT al.*, u.username 
    FROM activity_logs al 
    JOIN users u ON al.user_id = u.id 
    ORDER BY al.created_at DESC 
    LIMIT 5
");
$recent_activities = $stmt->fetchAll();
?>

<div class="dashboard-bg">
    <div class="container-fluid px-3 px-md-4 py-4">
        <h2 class="mb-4 fw-bold text-dark">Dashboard Overview</h2>
        <!-- Stats Cards Row -->
        <div class="row g-3 mb-4">
            <div class="col-12 col-sm-6 col-lg-4">
                <div class="card dashboard-card text-center h-100 p-2">
                    <div class="card-body d-flex flex-column align-items-center justify-content-center p-3">
                        <i class="fas fa-box fa-2x text-primary mb-2"></i>
                        <div class="dashboard-label text-primary">TOTAL ITEMS</div>
                        <div class="dashboard-value"><?php echo $total_items; ?></div>
                        <a href="?page=view-items" class="btn btn-outline-primary btn-sm mt-2">View Items</a>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-4">
                <div class="card dashboard-card text-center h-100 p-2">
                    <div class="card-body d-flex flex-column align-items-center justify-content-center p-3">
                        <i class="fas fa-tags fa-2x text-success mb-2"></i>
                        <div class="dashboard-label text-success">CATEGORIES</div>
                        <div class="dashboard-value text-success"><?php echo $total_categories; ?></div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-4">
                <div class="card dashboard-card text-center h-100 p-2">
                    <div class="card-body d-flex flex-column align-items-center justify-content-center p-3">
                        <i class="fas fa-exclamation-triangle fa-2x text-warning mb-2"></i>
                        <div class="dashboard-label text-warning">LOW STOCK ITEMS</div>
                        <div class="dashboard-value text-warning"><?php echo $low_stock_items; ?></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Stock Issues Section -->
        <div class="row mb-3">
            <div class="col-12">
                <div class="card dashboard-section">
                    <div class="card-header section-header bg-light text-dark border-bottom">
                        <i class="fas fa-hand-holding me-2 text-primary"></i> <span class="fw-semibold">Recent Stock Issues</span>
                    </div>
                    <div class="card-body py-3">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Item</th>
                                        <th>Quantity</th>
                                        <th>Issued To</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recent_issues as $issue): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($issue['item_name']); ?></td>
                                        <td><?php echo htmlspecialchars($issue['quantity_issued']); ?></td>
                                        <td><?php echo htmlspecialchars($issue['issued_to']); ?></td>
                                        <td><?php echo date('Y-m-d H:i', strtotime($issue['issued_at'])); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activities Section -->
        <div class="row">
            <div class="col-12">
                <div class="card dashboard-section">
                    <div class="card-header section-header bg-light text-dark border-bottom">
                        <i class="fas fa-history me-2 text-info"></i> <span class="fw-semibold">Recent Activities</span>
                    </div>
                    <div class="card-body py-3">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>User</th>
                                        <th>Action</th>
                                        <th>Description</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recent_activities as $activity): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($activity['username']); ?></td>
                                        <td><?php echo htmlspecialchars($activity['action']); ?></td>
                                        <td><?php echo htmlspecialchars($activity['description']); ?></td>
                                        <td><?php echo date('Y-m-d H:i', strtotime($activity['created_at'])); ?></td>
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
.dashboard-card {
    border: none;
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    min-height: 140px;
    background: #fff;
    transition: box-shadow 0.2s;
}
.dashboard-card:hover {
    box-shadow: 0 4px 18px rgba(0,0,0,0.10);
}
.dashboard-label {
    font-size: 1rem;
    font-weight: 600;
    letter-spacing: 0.5px;
    margin-bottom: 0.3rem;
    text-transform: uppercase;
}
.dashboard-value {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 0;
    color: #222;
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
.dashboard-section {
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    margin-bottom: 0;
    background: #fff;
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
    .dashboard-card {
        margin-bottom: 1rem;
    }
    .dashboard-section {
        margin-bottom: 1rem;
    }
    .dashboard-value {
        font-size: 1.5rem;
    }
}
@media (max-width: 767.98px) {
    .container-fluid {
        padding-left: 0.5rem;
        padding-right: 0.5rem;
    }
    .dashboard-card {
        min-height: 110px;
    }
    .table td, .table th {
        font-size: 0.9rem;
    }
}
</style> 