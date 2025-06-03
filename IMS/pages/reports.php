<?php
// Simple CSV export for items - must be at the very top before any output
if (isset($_GET['export']) && $_GET['export'] === 'excel') {
    // Build the same query as below for export
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
    $conn = new PDO('mysql:host=localhost;dbname=west_wild_ims', 'root', ''); // adjust if needed
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

// Get filter parameters
$category_id = $_GET['category_id'] ?? '';
$date_from = $_GET['date_from'] ?? '';
$date_to = $_GET['date_to'] ?? '';
$search = $_GET['search'] ?? '';

// Get all categories for filter
$stmt = $conn->query("SELECT * FROM categories ORDER BY name");
$categories = $stmt->fetchAll();

// Build the query
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

// Execute the query
$stmt = $conn->prepare($query);
$stmt->execute($params);
$items = $stmt->fetchAll();
?>

<div class="dashboard-bg">
    <div class="container-fluid px-3 px-md-4 py-4">
        <h2 class="mb-4 fw-bold text-dark">Inventory Reports</h2>
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card dashboard-section" style="max-width:1100px;margin:auto;">
                    <div class="card-header section-header bg-light text-dark border-bottom">
                        <i class="fas fa-file-alt me-2 text-primary"></i> <span class="fw-semibold">Reports Filter</span>
                    </div>
                    <div class="card-body py-3">
                        <!-- Filters -->
                        <form method="GET" action="" class="mb-4">
                            <div class="row">
                                <div class="col-md-3">
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
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="date_from" class="form-label">Date From</label>
                                        <input type="date" class="form-control" id="date_from" name="date_from" 
                                               value="<?php echo $date_from; ?>">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="date_to" class="form-label">Date To</label>
                                        <input type="date" class="form-control" id="date_to" name="date_to" 
                                               value="<?php echo $date_to; ?>">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="search" class="form-label">Search</label>
                                        <input type="text" class="form-control" id="search" name="search" 
                                               value="<?php echo htmlspecialchars($search); ?>" 
                                               placeholder="Search items...">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-filter"></i> Apply Filters
                                    </button>
                                    <a href="?page=reports" class="btn btn-secondary">
                                        <i class="fas fa-undo"></i> Reset
                                    </a>
                                    <a href="?page=reports&export=excel" class="btn btn-success mb-3">Export Items to Excel</a>
                                    <a href="#" class="btn btn-info mb-3" onclick="printReportTable(); return false;"><i class="fas fa-print"></i> Print Report</a>
                                </div>
                            </div>
                        </form>
                        <!-- Report Table -->
                        <div class="table-responsive" id="reportTableWrapper">
                            <table class="table table-hover align-middle mb-0" id="reportTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>Item Name</th>
                                        <th>Category</th>
                                        <th>Current Stock</th>
                                        <th>Unit</th>
                                        <th>Total In</th>
                                        <th>Total Out</th>
                                        <th>Last Updated</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($items as $item): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($item['item_name']); ?></td>
                                            <td><?php echo htmlspecialchars($item['category_name']); ?></td>
                                            <td><?php echo $item['current_stock']; ?></td>
                                            <td><?php echo htmlspecialchars($item['unit']); ?></td>
                                            <td><?php echo $item['total_in']; ?></td>
                                            <td><?php echo $item['total_out']; ?></td>
                                            <td><?php echo $item['last_updated']; ?></td>
                                            <td>
                                                <?php if ($item['current_stock'] < $item['min_stock_level']): ?>
                                                    <span class="badge bg-warning text-dark">Low Stock</span>
                                                <?php else: ?>
                                                    <span class="badge bg-success">OK</span>
                                                <?php endif; ?>
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
@media print {
    body * { visibility: hidden !important; }
    #reportTableWrapper, #reportTableWrapper * { visibility: visible !important; }
    #reportTableWrapper {
        position: absolute;
        left: 0;
        top: 0;
        width: 100vw;
        background: #fff;
        z-index: 9999;
        padding: 20px;
    }
    /* Hide buttons, forms, and navigation inside the wrapper */
    #reportTableWrapper .btn,
    #reportTableWrapper form,
    #reportTableWrapper .section-header,
    #reportTableWrapper .card-header,
    #reportTableWrapper .alert {
        display: none !important;
    }
}
</style>

<script>
$(document).ready(function() {
    // Initialize DataTable
    $('.datatable').DataTable({
        order: [[0, 'asc']],
        pageLength: 25,
        dom: 'Bfrtip',
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ]
    });
});

function printReportTable() {
    window.print();
}
</script> 