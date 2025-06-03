<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>West Wild IMS</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link href="assets/css/style.css" rel="stylesheet">
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- DataTables -->
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top shadow">
        <div class="container-fluid">
            <a class="navbar-brand d-flex align-items-center" href="?page=dashboard">
                <i class="fas fa-boxes me-2"></i>
                <span>West Wild IMS</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
                <div class="navbar-nav ms-auto align-items-lg-center">
                    <a class="nav-link px-3 d-flex align-items-center <?php echo $page == 'dashboard' ? 'active' : ''; ?>" href="?page=dashboard">
                        <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                    </a>
                    <a class="nav-link px-3 d-flex align-items-center <?php echo $page == 'add-item' ? 'active' : ''; ?>" href="?page=add-item">
                        <i class="fas fa-plus-circle me-2"></i> Add Item
                    </a>
                    <a class="nav-link px-3 d-flex align-items-center <?php echo $page == 'issue-item' ? 'active' : ''; ?>" href="?page=issue-item">
                        <i class="fas fa-hand-holding me-2"></i> Issue Item
                    </a>
                    <a class="nav-link px-3 d-flex align-items-center <?php echo $page == 'return-item' ? 'active' : ''; ?>" href="?page=return-item">
                        <i class="fas fa-undo me-2"></i> Return Item
                    </a>
                    <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                    <a class="nav-link px-3 d-flex align-items-center <?php echo $page == 'admin-settings' ? 'active' : ''; ?>" href="?page=admin-settings">
                        <i class="fas fa-cogs me-2"></i> Admin Settings
                    </a>
                    <?php endif; ?>
                    <a class="nav-link px-3 d-flex align-items-center <?php echo $page == 'manage-categories' ? 'active' : ''; ?>" href="?page=manage-categories">
                        <i class="fas fa-tags me-2"></i> Manage Categories
                    </a>
                    <a class="nav-link px-3 d-flex align-items-center <?php echo $page == 'reports' ? 'active' : ''; ?>" href="?page=reports">
                        <i class="fas fa-chart-bar me-2"></i> Reports
                    </a>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <div class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user-circle me-2"></i>
                                <span><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="?page=profile"><i class="fas fa-cog me-2"></i>Settings</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                            </ul>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>
    <div class="container-fluid main-content pt-4">
        <div class="row"> 