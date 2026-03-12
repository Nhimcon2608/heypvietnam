<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($data['title']) ? $data['title'] : 'Đăng nhập quản trị' ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
</head>
<body>
<div class="login-wrapper">
    <div class="login-container">
        <div class="login-header">
            <div class="logo">
                <img src="<?php echo URL_ROOT; ?>/public/img/logoHEYP.png" alt="HeypVietNam Logo" class="circular-logo">
            </div>
            <h2>Quản Trị Hệ Thống</h2>
            <p>Vui lòng đăng nhập để tiếp tục</p>
        </div>
        
        <?php if (!empty($error)): ?>
            <div class="alert-message">
                <i class="fas fa-exclamation-circle"></i>
                <?= $error ?>
            </div>
        <?php endif; ?>

        <form action="<?php echo URL_ROOT; ?>/admin" method="post" class="login-form">
            <div class="form-group">
                <label for="username">
                    <i class="fas fa-user"></i>
                    <span>Tên đăng nhập</span>
                </label>
                <input type="text" id="username" name="username" required autocomplete="username">
            </div>
            
            <div class="form-group">
                <label for="password">
                    <i class="fas fa-lock"></i>
                    <span>Mật khẩu</span>
                </label>
                <div class="password-container">
                    <input type="password" id="password" name="password" required autocomplete="current-password">
                    <span class="password-toggle" onclick="togglePassword()">
                        <i class="fas fa-eye" id="toggleIcon"></i>
                    </span>
                </div>
            </div>

            <div class="form-action">
                <button type="submit" class="btn-login">
                    <span>Đăng nhập</span>
                    <i class="fas fa-sign-in-alt"></i>
                </button>
            </div>
        </form>

        <div class="login-footer">
            <a href="<?php echo URL_ROOT; ?>/" class="btn-back">
                <i class="fas fa-arrow-left"></i>
                <span>Quay lại trang chủ</span>
            </a>
        </div>
    </div>

    <div class="login-image">
        <div class="eco-quote">
            <h3>"Vì một Việt Nam xanh, sạch và bền vững"</h3>
            <p>HeypVietNam - Sản phẩm thân thiện với môi trường</p>
        </div>
    </div>
</div>

<script>
function togglePassword() {
    const passwordInput = document.getElementById('password');
    const toggleIcon = document.getElementById('toggleIcon');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.classList.remove('fa-eye');
        toggleIcon.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        toggleIcon.classList.remove('fa-eye-slash');
        toggleIcon.classList.add('fa-eye');
    }
}

// Thêm hiệu ứng khi trang tải xong
document.addEventListener('DOMContentLoaded', function() {
    document.body.classList.add('loaded');
});
</script>

<style>
:root {
    --primary-bg: #F5F1E8;
    --secondary-bg-1: #8B7355;
    --secondary-bg-2: #A0845C;
    --heading-color: #5D4E37;
    --text-color: #4A4A4A;
    --accent-color: #FFFFFF;
    --plant-color: #7A6B47;
    --error-color: #B85450;
    --input-bg: #FEFCF7;
    --input-border: #D4C4A8;
    --shadow-color: rgba(93, 78, 55, 0.1);
    --transition-speed: 0.3s;
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Open Sans', sans-serif;
    background-color: var(--primary-bg);
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
    position: relative;
    overflow-x: hidden;
    opacity: 0;
    transition: opacity 0.6s ease;
}

body.loaded {
    opacity: 1;
}

body::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 5px;
    background: linear-gradient(90deg, var(--secondary-bg-1), var(--secondary-bg-2));
}

h2, h3 {
    font-family: 'Montserrat', sans-serif;
    color: var(--heading-color);
}

.login-wrapper {
    display: flex;
    width: 100%;
    max-width: 1000px;
    min-height: 550px;
    background-color: var(--accent-color);
    border-radius: 12px;
    box-shadow: 0 15px 30px var(--shadow-color);
    overflow: hidden;
    animation: fadeInUp 0.8s ease;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.login-container {
    flex: 1;
    padding: 40px;
    display: flex;
    flex-direction: column;
}

.login-image {
    flex: 1;
    background-image: url('<?php echo URL_ROOT; ?>/image.png');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    padding: 20px;
}



.eco-quote {
    position: relative;
    text-align: center;
    max-width: 80%;
    padding: 20px;
    border-radius: 8px;
    background: rgba(90, 107, 0, 0.3);
    backdrop-filter: blur(5px);
    animation: fadeIn 1s ease 0.5s forwards;
    opacity: 0;
}

@keyframes fadeIn {
    to { opacity: 1; }
}

.eco-quote h3 {
    color: white;
    margin-bottom: 10px;
    font-size: 1.5rem;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.7);
}

.eco-quote p {
    font-style: italic;
    font-size: 0.9rem;
    color: rgba(255, 255, 255, 0.95);
    text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.7);
}

.login-header {
    text-align: center;
    margin-bottom: 30px;
}

.logo {
    margin-bottom: 15px;
    display: flex;
    justify-content: center;
}

.logo img.circular-logo {
    height: 80px;
    width: 80px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid var(--secondary-bg-1);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    animation: pulse 2s infinite alternate;
}

@keyframes pulse {
    from { transform: scale(1); }
    to { transform: scale(1.05); }
}

.login-header h2 {
    margin-bottom: 8px;
    font-weight: 600;
    color: var(--heading-color);
}

.login-header p {
    color: var(--text-color);
    font-size: 0.9rem;
}

.alert-message {
    background-color: rgba(231, 76, 60, 0.1);
    border-left: 4px solid var(--error-color);
    padding: 12px 15px;
    margin-bottom: 20px;
    color: var(--error-color);
    border-radius: 4px;
    display: flex;
    align-items: center;
    font-size: 0.9rem;
    animation: shake 0.5s ease;
}

@keyframes shake {
    0%, 100% { transform: translateX(0); }
    20%, 60% { transform: translateX(-5px); }
    40%, 80% { transform: translateX(5px); }
}

.alert-message i {
    margin-right: 10px;
}

.login-form {
    margin-top: 20px;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: flex;
    align-items: center;
    font-size: 0.9rem;
    font-weight: 600;
    margin-bottom: 8px;
    color: var(--heading-color);
}

.form-group label i {
    margin-right: 8px;
    color: var(--secondary-bg-1);
}

.form-group input {
    width: 100%;
    padding: 12px 15px;
    border: 1px solid var(--input-border);
    border-radius: 6px;
    background-color: var(--input-bg);
    font-size: 0.95rem;
    transition: all var(--transition-speed) ease;
}

.form-group input:focus {
    outline: none;
    border-color: var(--secondary-bg-1);
    box-shadow: 0 0 0 3px rgba(90, 107, 0, 0.15);
}

.password-container {
    position: relative;
}

.password-toggle {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    cursor: pointer;
    color: #666;
    transition: color var(--transition-speed) ease;
}

.password-toggle:hover {
    color: var(--secondary-bg-1);
}

.form-action {
    margin-top: 30px;
}

.btn-login {
    width: 100%;
    padding: 13px 20px;
    background: linear-gradient(135deg, var(--secondary-bg-1), var(--plant-color));
    color: white;
    border: none;
    border-radius: 6px;
    font-family: 'Montserrat', sans-serif;
    font-weight: 600;
    font-size: 1rem;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all var(--transition-speed) ease;
    position: relative;
    overflow: hidden;
}

.btn-login span {
    position: relative;
    z-index: 2;
}

.btn-login i {
    margin-left: 8px;
    position: relative;
    z-index: 2;
    transition: transform var(--transition-speed) ease;
}

.btn-login:hover i {
    transform: translateX(3px);
}

.btn-login::after {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: left 0.8s ease;
}

.btn-login:hover::after {
    left: 100%;
}

.btn-login:hover {
    box-shadow: 0 5px 15px rgba(0, 100, 0, 0.3);
    transform: translateY(-2px);
}

.login-footer {
    margin-top: auto;
    text-align: center;
    padding-top: 30px;
}

.btn-back {
    display: inline-flex;
    align-items: center;
    color: var(--text-color);
    font-size: 0.9rem;
    text-decoration: none;
    padding: 8px 15px;
    border-radius: 6px;
    transition: all var(--transition-speed) ease;
}

.btn-back i {
    margin-right: 6px;
    transition: transform var(--transition-speed) ease;
}

.btn-back:hover {
    background-color: rgba(0, 0, 0, 0.05);
    color: var(--secondary-bg-1);
}

.btn-back:hover i {
    transform: translateX(-3px);
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .login-wrapper {
        flex-direction: column;
    }
    
    .login-image {
        display: none;
    }
    
    .login-container {
        padding: 30px 20px;
    }
}
</style>
</body>
</html> 