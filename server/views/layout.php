<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'PDGP Management Console'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="/css/style.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background-color: #0d1117;
            color: #c9d1d9;
        }
        .navbar-brand {
            font-weight: bold;
        }
        .main-content {
            flex: 1;
        }
        .sidebar {
            min-height: calc(100vh - 56px);
            background-color: #161b22;
            border-right: 1px solid #30363d;
        }
    </style>
</head>
<body data-bs-theme="dark">
    <nav class="navbar navbar-expand-lg" style="background-color: #161b22; border-bottom: 1px solid #30363d;">
        <div class="container-fluid">
            <a class="navbar-brand text-primary" href="/">
                <i class="bi bi-box-seam"></i> PDGP Console
            </a>
            <?php if (isset($_SESSION['user'])): ?>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <span class="nav-link text-secondary">
                            <i class="bi bi-person-circle"></i>
                            <?php echo htmlspecialchars($_SESSION['user']['username']); ?>
                        </span>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-danger" href="/logout">
                            <i class="bi bi-box-arrow-right"></i> Logout
                        </a>
                    </li>
                </ul>
            </div>
            <?php endif; ?>
        </div>
    </nav>

    <div class="container-fluid main-content">
        <div class="row">
            <?php if (isset($_SESSION['user']) && !isset($hideNav)): ?>
            <nav class="col-md-3 col-lg-2 d-md-block sidebar">
                <div class="position-sticky pt-3">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link <?php echo ($currentPage ?? '') === 'dashboard' ? 'active bg-primary' : 'text-light'; ?>" href="/">
                                <i class="bi bi-house-door"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo ($currentPage ?? '') === 'sites' ? 'active bg-primary' : 'text-light'; ?>" href="/sites/add">
                                <i class="bi bi-plus-circle"></i> Add Site
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-light" href="/api/template/csv" download>
                                <i class="bi bi-download"></i> Download CSV Template
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <?php else: ?>
            <main class="col-12">
            <?php endif; ?>
                <div class="py-4">
                    <?php echo $content ?? ''; ?>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <?php if (isset($scripts)) echo $scripts; ?>
</body>
</html>

