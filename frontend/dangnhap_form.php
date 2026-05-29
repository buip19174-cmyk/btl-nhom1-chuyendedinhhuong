
<div class="modal-content-inner">
    <span class="close-btn">&times;</span> 

    <form method="POST" action="home.php"> 
        <h2>Đăng nhập</h2>
        <input type="hidden" name="redirect" value="<?= htmlspecialchars($_GET['redirect'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        
        <input type="text" name="username" placeholder="Tên đăng nhập" required>
        
        <div class="input-group">
            <input type="password" name="password" class="password-input" placeholder="Nhập mật khẩu" required>
            <i class="fa-solid fa-eye toggle-eye-icon" style="cursor: pointer;"></i>
        </div>
        
        <button type="submit" name="login">Đăng nhập</button>

        <?php if(!empty($login_message)): ?>
    <p class="message" style="color: #49c5aa; text-align: center; margin-top: 10px;">
        <?php echo htmlspecialchars($login_message); ?>
    </p> 
<?php endif; ?>

        <p style="margin-top: 15px; text-align: center;">
            Chưa có tài khoản? 
            <a href="javascript:void(0);" id="switch-to-register">Đăng ký</a>
        </p>
    </form>
</div>

<script>
    (function() {
        var allModals = document.querySelectorAll('#loginModal');
        var container = allModals[allModals.length - 1];
        var passwordInput = container.querySelector(".password-input");
        var toggleEye = container.querySelector(".toggle-eye-icon");

        if (passwordInput && toggleEye) {
            toggleEye.onclick = function() {
                if (passwordInput.type === "password") {
                    passwordInput.type = "text";
                    this.classList.remove("fa-eye");
                    this.classList.add("fa-eye-slash");
                } else {
                    passwordInput.type = "password";
                    this.classList.remove("fa-eye-slash");
                    this.classList.add("fa-eye");
                }
            };
        }
    })();
</script>
