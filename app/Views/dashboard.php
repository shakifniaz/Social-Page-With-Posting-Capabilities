<?php
$title = 'Dashboard | AuthBoard';
ob_start();
?>
<h2>Welcome, <?php echo  htmlspecialchars($user['name']) ?></h2>
<p>Your email: <?= htmlspecialchars($user['email']) ?></p>
<?php
$content = ob_get_clean();
include __DIR__ . '/layout.php';
