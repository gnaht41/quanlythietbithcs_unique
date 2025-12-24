<?php
// File: views/partials/footer.php
/*
 * Biến $js_file phải được định nghĩa ở file cha
 * TRƯỚC KHI gọi require_once 'partials/footer.php';
 */
?>
<footer>© 2025 Hệ thống Quản lý Thiết bị</footer>

<script src="js/main.js"></script>

<?php if (isset($js_file) && !empty($js_file)): ?>
<script src="js/<?php echo htmlspecialchars($js_file); ?>"></script>
<?php endif; ?>

</body>

</html>