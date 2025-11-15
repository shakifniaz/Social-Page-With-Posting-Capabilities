
<!doctype html>
<html class="h-full">
<head>
    <meta charset="utf-8">
    <title><?= $title ?? 'AuthBoard' ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="/assets/style.css">
    <script src="/assets/like-system.js"></script>
</head>
<body class="h-full <?php echo !empty($_SESSION['user']) ? 'logged-in' : ''; ?>">
    <?php if (!empty($_SESSION['user'])): ?>
    <header class="main-header">
        <div class="header-container">
            <div class="header-left">
                <h1 class="logo">MetroPost</h1>
            </div>
            
            <div class="header-right">
                <div class="nav-container">
                    <?php
                    $current_page = $_SERVER['REQUEST_URI'] ?? '';
                    $is_profile = strpos($current_page, '/dashboard') !== false;
                    $active_nav = $is_profile ? 'profile' : 'posts';
                    ?>
                    <nav class="header-nav" data-active="<?php echo $active_nav; ?>">
                        <a href="/posts" class="nav-link <?php echo !$is_profile ? 'active' : ''; ?>" title="Posts">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9m0 0v12m0 0h6m-6 0h6" />
                            </svg>
                        </a>
                        <a href="/dashboard" class="nav-link <?php echo $is_profile ? 'active' : ''; ?>" title="Profile">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </a>
                    </nav>
                    
                    <a href="/logout" class="logout-btn" title="Logout">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </header>
    <?php endif; ?>

    <main class="<?php echo !empty($_SESSION['user']) ? 'main-content' : 'h-full'; ?>">
        <?php echo $content; ?>
    </main>

    <script>
        console.log('Layout loaded successfully');
    </script>
</body>
</html>
