<link rel="stylesheet" href="style.css">
<div id="loginModal" class="modal"> 
    <div class="modal-content-inner">
        <span class="close-btn">&times;</span> 

        <form method="POST"> 
            <h2>Đăng nhập</h2>
            
            <input type="text" name="username" placeholder="Tên đăng nhập" required>
            
            <div class="input-group">
                <input type="password" name="password" class="password-input" placeholder="Nhập mật khẩu" required>
                <i class="fa-solid fa-eye toggle-eye-icon" style="cursor: pointer;"></i>
            </div>
            
            <button type="submit">Đăng nhập</button>

            <?php if(!empty($message)): ?>
                <p class="message" style="color: #49c5aa; text-align: center; margin-top: 10px;">
                    <?php echo $message; ?>
                </p> 
            <?php endif; ?>

        <p style="margin-top: 15px; text-align: center;">
            Chưa có tài khoản? 
            <a href="javascript:void(0);" id="switch-to-register">Đăng ký</a>
            </p>
        </form>
    </div>

    <script>
        // Đoạn Script này nằm trong form để xử lý riêng việc ẩn/hiện mật khẩu
        (function() {
            // Tìm các phần tử trong phạm vi của form này thôi
            const container = document.currentScript.parentElement;
            const passwordInput = container.querySelector(".password-input");
            const toggleEye = container.querySelector(".toggle-eye-icon");

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
</div>