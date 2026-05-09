document.addEventListener('DOMContentLoaded', () => {
    // --- 1. KHAI BÁO CÁC PHẦN TỬ (MODAL) ---
    const regModal = document.getElementById("registerModal");
    const regBtn = document.getElementById("openRegisterModal");

    const loginModal = document.getElementById("loginModal");
    const loginBtn = document.getElementById("openRegisterModal2");

    // --- [MỚI] 1.1. KHAI BÁO PHẦN TỬ MENU NGƯỜI DÙNG ---
    const userProfile = document.getElementById('userProfile');
    const userDropdown = document.getElementById('userDropdown');

    // --- 2. HÀM DÙNG CHUNG ĐỂ ĐÓNG/MỞ ---
    const openModal = (targetModal) => {
        if (targetModal) {
            targetModal.style.setProperty('display', 'flex', 'important');
        }
    };

    const closeModal = (targetModal) => {
        if (targetModal) {
            targetModal.style.display = "none";
        }
    };

    // --- 3. LOGIC CHUYỂN ĐỔI GIỮA 2 FORM ---
    const switchToReg = document.getElementById("switch-to-register");
    const switchToLogin = document.getElementById("switch-to-login");

    if (switchToReg) {
        switchToReg.addEventListener('click', (e) => {
            e.preventDefault();
            closeModal(loginModal);
            openModal(regModal);
        });
    }

    if (switchToLogin) {
        switchToLogin.addEventListener('click', (e) => {
            e.preventDefault();
            closeModal(regModal);
            openModal(loginModal);
        });
    }

    // --- 4. GÁN SỰ KIỆN MỞ TỪ NAVBAR ---
    if (regBtn) {
        regBtn.addEventListener('click', (e) => {
            e.preventDefault();
            openModal(regModal);
        });
    }

    if (loginBtn) {
        loginBtn.addEventListener('click', (e) => {
            e.preventDefault();
            openModal(loginModal);
        });
    }

    // --- [MỚI] 4.1. GÁN SỰ KIỆN CHO MENU DROP-DOWN ---
    if (userProfile && userDropdown) {
        userProfile.addEventListener('click', (e) => {
            e.stopPropagation(); // Ngăn sự kiện lan ra ngoài làm đóng menu ngay lập tức
            userDropdown.classList.toggle('active');
        });
    }

    // --- 5. GÁN SỰ KIỆN ĐÓNG ---
    document.querySelectorAll('.close-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            closeModal(regModal);
            closeModal(loginModal);
        });
    });

    window.addEventListener('click', (event) => {
        // Đóng các Modal
        if (event.target === regModal) closeModal(regModal);
        if (event.target === loginModal) closeModal(loginModal);

        // [MỚI] Đóng Dropdown khi click ra ngoài vùng Menu
        if (userDropdown && !userProfile.contains(event.target)) {
            userDropdown.classList.remove('active');
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === "Escape") {
            closeModal(regModal);
            closeModal(loginModal);
            // [MỚI] Nhấn Esc cũng tắt luôn Menu Dropdown
            if (userDropdown) userDropdown.classList.remove('active');
        }
    });
});