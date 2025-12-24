<?php
// views/dang-nhap.php
// Biến $error_message được truyền từ AuthController->showLoginForm()
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Đăng nhập - Hệ thống Quản lý Thiết bị THCS</title>
    <link rel="stylesheet" href="views/css/dang-nhap.css" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <!-- FAVICON - LOGO NHỎ TRÊN TAB TRÌNH DUYỆT -->
    <link rel="icon" type="image/x-icon" href="img/favicon.ico">
    <!-- Nếu bạn dùng file PNG thì thay bằng dòng dưới -->
    <link rel="icon" type="image/png" href="views/img/UniqueLogo.jpg">
</head>

<body>
    <div class="shell">
        <main class="login-panel">
            <section class="card">
                <div class="brand-header">
                    <div class="logo">Trường THCS Unique</div>
                    <p class="tagline">Hệ thống Quản lý Thiết bị</p>
                </div>
                <h1>Đăng nhập</h1>
                <form id="login-form" method="POST" action="index.php?action=doLogin" novalidate>
                    <div class="field">
                        <label for="username">Tài khoản</label>
                        <input id="username" name="username" type="text" autocomplete="username" required
                            placeholder="Nhập tài khoản..." />
                    </div>
                    <div class="field">
                        <label for="password">Mật khẩu</label>
                        <div class="password-wrap">
                            <input id="password" name="password" type="password" autocomplete="current-password"
                                required placeholder="••••••" />
                            <button type="button" id="toggle-pass" class="toggle-pass" aria-label="Hiện/ẩn mật khẩu">
                                <svg id="icon-eye" xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" viewBox="0 0 24 24">
                                    <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z" />
                                    <circle cx="12" cy="12" r="3" />
                                </svg>
                            </button>
                        </div>
                        <div id="caps-hint" class="caps-hint" aria-live="polite" hidden>⚠️ Caps Lock đang bật</div>
                    </div>
                    <button type="submit" id="btn-login" class="btn-primary">Đăng nhập</button>
                    <p id="error" class="error" role="alert" aria-live="assertive"
                        <?php echo empty($error_message) ? 'hidden' : ''; ?>>
                        <?php echo htmlspecialchars($error_message); ?>
                    </p>
                    <p class="sub">Quên mật khẩu? <a href="#" id="contact-admin">Liên hệ Quản trị viên</a></p>
                </form>
                <div class="footer-note-card">© 2025 THCS Unique</div>
            </section>
        </main>
    </div>
    <script src="views/js/dang-nhap.js"></script>
    <?php if (!empty($error_message)): ?>
    <script>
    if (typeof showLoginError === 'function') {
        showLoginError('<?php echo htmlspecialchars($error_message, ENT_QUOTES, 'UTF-8'); ?>');
    }
    </script>
    <?php endif; ?>

</body>

</html>