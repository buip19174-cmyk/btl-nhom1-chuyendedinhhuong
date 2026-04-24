<link rel="stylesheet" href="style.css">
<div id="registerModal" class="modal"> 
    <div>
        <form method="POST"> 
            <h2>Đăng kí tài khoản</h2>
            <input type="number" name="sdt" placeholder="Số điện thoại" required >
            <input type="text" name="username" placeholder="Tên đăng nhập" required>
            <input type="email" name="email" placeholder="Email" required>

            <div class="input-group">
                <input type="password" id="password" name="password" placeholder="Nhập mật khẩu" required>
                <i class="fa-solid fa-eye toggle-eye" id="toggleEye"></i>
            </div>
            <span class="note">Mật khẩu bao gồm ít nhất 6 ký tự</span>
            <button type="submit">Đăng kí</button>

            <p class="message"><?php if (!empty($message)) echo $message; ?></p> 
        <p style="margin-top: 15px; text-align: center;">
            Đã có tài khoản? 
            <a href="javascript:void(0);" id="switch-to-login">Đăng nhập</a>
    </p>
        </form>
    </div>

    <script>
        const passwordInput = document.getElementById("password");
        const toggleEye = document.getElementById("toggleEye");

        if (passwordInput && toggleEye) {
            toggleEye.onclick = () => {
                if (passwordInput.type === "password") {
                    passwordInput.type = "text";
                    toggleEye.classList.remove("fa-eye");
                    toggleEye.classList.add("fa-eye-slash");
                } else {
                    passwordInput.type = "password";
                    toggleEye.classList.remove("fa-eye-slash");
                    toggleEye.classList.add("fa-eye");
                }
            }
        }
    </script>
</div>