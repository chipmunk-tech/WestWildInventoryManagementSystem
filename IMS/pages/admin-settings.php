<?php
// Check if user is admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: index.php');
    exit;
}

$success = $error = '';

// Handle settings update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $conn->beginTransaction();
        
        // Update system settings
        $settings = [
            'site_name' => $_POST['site_name'] ?? 'West Wild IMS',
            'min_stock_level' => $_POST['min_stock_level'] ?? 5,
            'enable_email_notifications' => isset($_POST['enable_email_notifications']) ? 1 : 0,
            'enable_low_stock_alerts' => isset($_POST['enable_low_stock_alerts']) ? 1 : 0,
            'items_per_page' => $_POST['items_per_page'] ?? 25,
            'date_format' => $_POST['date_format'] ?? 'Y-m-d',
            'time_format' => $_POST['time_format'] ?? 'H:i'
        ];
        
        foreach ($settings as $key => $value) {
            $stmt = $conn->prepare("
                INSERT INTO settings (setting_key, setting_value) 
                VALUES (?, ?) 
                ON DUPLICATE KEY UPDATE setting_value = ?
            ");
            $stmt->execute([$key, $value, $value]);
        }
        
        // Log the activity
        $stmt = $conn->prepare("
            INSERT INTO activity_logs (user_id, action, description) 
            VALUES (?, 'update_settings', 'Updated system settings')
        ");
        $stmt->execute([$_SESSION['user_id']]);
        
        $conn->commit();
        $success = 'Settings updated successfully';
        
    } catch (Exception $e) {
        $conn->rollBack();
        $error = 'Error updating settings: ' . $e->getMessage();
    }
}

// Get current settings
$stmt = $conn->query("SELECT setting_key, setting_value FROM settings");
$settings = [];
while ($row = $stmt->fetch()) {
    $settings[$row['setting_key']] = $row['setting_value'];
}

// Set default values if not set
$settings = array_merge([
    'site_name' => 'West Wild IMS',
    'min_stock_level' => 5,
    'enable_email_notifications' => 0,
    'enable_low_stock_alerts' => 1,
    'items_per_page' => 25,
    'date_format' => 'Y-m-d',
    'time_format' => 'H:i'
], $settings);
?>

<div class="dashboard-bg">
    <div class="container-fluid px-3 px-md-4 py-4">
        <h2 class="mb-4 fw-bold text-dark">Admin Settings</h2>
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card dashboard-section" style="max-width:900px;margin:auto;">
                    <div class="card-header section-header bg-light text-dark border-bottom">
                        <i class="fas fa-cogs me-2 text-primary"></i> <span class="fw-semibold">System Settings</span>
                    </div>
                    <div class="card-body py-3">
                        <?php if ($success): ?>
                            <div class="alert alert-success"><?php echo $success; ?></div>
                        <?php endif; ?>
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>
                        
                        <form method="POST" action="">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="site_name" class="form-label">Site Name</label>
                                        <input type="text" class="form-control" id="site_name" name="site_name" 
                                               value="<?php echo htmlspecialchars($settings['site_name']); ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="min_stock_level" class="form-label">Default Minimum Stock Level</label>
                                        <input type="number" class="form-control" id="min_stock_level" name="min_stock_level" 
                                               value="<?php echo htmlspecialchars($settings['min_stock_level']); ?>" required min="1">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="items_per_page" class="form-label">Items Per Page</label>
                                        <select class="form-select" id="items_per_page" name="items_per_page">
                                            <option value="10" <?php echo $settings['items_per_page'] == 10 ? 'selected' : ''; ?>>10</option>
                                            <option value="25" <?php echo $settings['items_per_page'] == 25 ? 'selected' : ''; ?>>25</option>
                                            <option value="50" <?php echo $settings['items_per_page'] == 50 ? 'selected' : ''; ?>>50</option>
                                            <option value="100" <?php echo $settings['items_per_page'] == 100 ? 'selected' : ''; ?>>100</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="date_format" class="form-label">Date Format</label>
                                        <select class="form-select" id="date_format" name="date_format">
                                            <option value="Y-m-d" <?php echo $settings['date_format'] == 'Y-m-d' ? 'selected' : ''; ?>>YYYY-MM-DD</option>
                                            <option value="d/m/Y" <?php echo $settings['date_format'] == 'd/m/Y' ? 'selected' : ''; ?>>DD/MM/YYYY</option>
                                            <option value="m/d/Y" <?php echo $settings['date_format'] == 'm/d/Y' ? 'selected' : ''; ?>>MM/DD/YYYY</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="enable_email_notifications" 
                                                   name="enable_email_notifications" <?php echo $settings['enable_email_notifications'] ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="enable_email_notifications">Enable Email Notifications</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="enable_low_stock_alerts" 
                                                   name="enable_low_stock_alerts" <?php echo $settings['enable_low_stock_alerts'] ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="enable_low_stock_alerts">Enable Low Stock Alerts</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Save Settings
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
.form-check-input:checked {
    background-color: #0d6efd;
    border-color: #0d6efd;
}
</style> 