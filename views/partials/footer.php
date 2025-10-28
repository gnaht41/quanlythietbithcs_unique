<?php
// File: views/partials/footer.php
/*
 * Biến $js_file phải được định nghĩa ở file cha
 * TRƯỚC KHI gọi require_once 'partials/footer.php';
 */
?>
<footer>© 2025 Hệ thống Quản lý Thiết bị</footer>

<script src="js/<?php echo htmlspecialchars($js_file ?? 'default.js'); ?>"></script>
</body>

</html>