/**
 * Tìm kiếm AJAX — dropdown (header) + cập nhật kết quả (timkiem.php)
 */
(function () {
    'use strict';

    var API_URL = '../backend/search_ajax.php';
    var DEBOUNCE_MS = 320;
    var MIN_CHARS = 2;

    var debounceTimers = new WeakMap();

    function escapeHtml(str) {
        if (!str) return '';
        var d = document.createElement('div');
        d.textContent = str;
        return d.innerHTML;
    }

    function coverFallback(el) {
        el.onerror = function () {
            this.onerror = null;
            this.src = 'img/sach2.jpg';
        };
    }

    function fetchSearch(q, limit) {
        var url = API_URL + '?q=' + encodeURIComponent(q) + '&limit=' + (limit || 12);
        return fetch(url, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
        }).then(function (res) {
            if (!res.ok) throw new Error('Network');
            return res.json();
        });
    }

    function renderDropdownItems(items) {
        if (!items.length) {
            return '<div class="search-ajax-empty"><i class="fa-solid fa-face-sad-tear"></i> Không tìm thấy sách</div>';
        }
        return items
            .map(function (item) {
                return (
                    '<a href="' +
                    escapeHtml(item.url) +
                    '" class="search-ajax-item" role="option">' +
                    '<img src="' +
                    escapeHtml(item.cover) +
                    '" alt="" loading="lazy">' +
                    '<div class="sad-info">' +
                    '<div class="sad-title">' +
                    escapeHtml(item.title) +
                    '</div>' +
                    '<div class="sad-cat">' +
                    escapeHtml(item.category) +
                    '</div>' +
                    '</div>' +
                    '</a>'
                );
            })
            .join('');
    }

    function renderResultsGrid(items, keyword) {
        if (!items.length) {
            return (
                '<div class="empty-state">' +
                '<i class="fa-solid fa-face-sad-tear"></i>' +
                '<h3>Không tìm thấy kết quả</h3>' +
                '<p>Không có sách nào khớp với "<strong>' +
                escapeHtml(keyword) +
                '</strong>". Thử từ khóa khác.</p>' +
                '<a href="home.php" class="btn-home"><i class="fa-solid fa-compass"></i> Khám phá trang chủ</a>' +
                '</div>'
            );
        }
        var cards = items
            .map(function (book) {
                return (
                    '<div class="result-card">' +
                    '<a href="' +
                    escapeHtml(book.url) +
                    '">' +
                    '<img src="' +
                    escapeHtml(book.cover) +
                    '" alt="' +
                    escapeHtml(book.title) +
                    '">' +
                    '</a>' +
                    '<div class="result-card-body">' +
                    '<a href="' +
                    escapeHtml(book.url) +
                    '" class="result-card-title">' +
                    escapeHtml(book.title) +
                    '</a>' +
                    '<p class="result-card-desc">' +
                    escapeHtml(book.category) +
                    '</p>' +
                    '</div>' +
                    '<div class="result-card-footer">' +
                    '<a href="' +
                    escapeHtml(book.url) +
                    '" class="btn-read"><i class="fa-solid fa-book-open"></i> Đọc</a>' +
                    '<form action="luutruyen.php" method="POST">' +
                    '<input type="hidden" name="story_id" value="' +
                    book.id +
                    '">' +
                    '<button type="submit" class="btn-save" title="Lưu vào tủ sách"><i class="fa-solid fa-heart"></i></button>' +
                    '</form>' +
                    '</div>' +
                    '</div>'
                );
            })
            .join('');
        return '<div class="results-grid">' + cards + '</div>';
    }

    /* ── Dropdown trên header ── */
    function initHeaderSearch(form) {
        var wrap = form.closest('.search-form-wrap');
        if (!wrap) {
            wrap = document.createElement('div');
            wrap.className = 'search-form-wrap';
            form.parentNode.insertBefore(wrap, form);
            wrap.appendChild(form);
        }

        var dropdown = wrap.querySelector('.search-ajax-dropdown');
        if (!dropdown) {
            dropdown = document.createElement('div');
            dropdown.className = 'search-ajax-dropdown';
            dropdown.setAttribute('role', 'listbox');
            wrap.appendChild(dropdown);
        }

        var input = form.querySelector('input[name="q"]');
        if (!input) return;

        input.setAttribute('autocomplete', 'off');

        function closeDropdown() {
            dropdown.classList.remove('is-open');
        }

        function openDropdown() {
            dropdown.classList.add('is-open');
        }

        function showLoading() {
            dropdown.innerHTML =
                '<div class="search-ajax-loading"><i class="fa-solid fa-spinner fa-spin"></i> Đang tìm...</div>';
            openDropdown();
        }

        function runSearch() {
            var q = input.value.trim();
            if (q.length < MIN_CHARS) {
                if (q.length === 0) {
                    closeDropdown();
                } else {
                    dropdown.innerHTML =
                        '<div class="search-ajax-hint">Nhập ít nhất ' + MIN_CHARS + ' ký tự</div>';
                    openDropdown();
                }
                return;
            }

            showLoading();
            fetchSearch(q, 8)
                .then(function (data) {
                    if (!data.success) throw new Error('fail');
                    var html =
                        '<div class="sad-header">' +
                        data.count +
                        ' kết quả</div>' +
                        renderDropdownItems(data.items);
                    if (data.count > 0) {
                        html +=
                            '<div class="search-ajax-footer">' +
                            '<a href="timkiem.php?q=' +
                            encodeURIComponent(q) +
                            '">Xem tất cả kết quả <i class="fa-solid fa-arrow-right"></i></a>' +
                            '</div>';
                    }
                    dropdown.innerHTML = html;
                    dropdown.querySelectorAll('img').forEach(coverFallback);
                    openDropdown();
                })
                .catch(function () {
                    dropdown.innerHTML =
                        '<div class="search-ajax-empty">Lỗi kết nối. Thử lại sau.</div>';
                    openDropdown();
                });
        }

        input.addEventListener('input', function () {
            clearTimeout(debounceTimers.get(input));
            debounceTimers.set(
                input,
                setTimeout(runSearch, DEBOUNCE_MS)
            );
        });

        input.addEventListener('focus', function () {
            if (input.value.trim().length >= MIN_CHARS) runSearch();
        });

        form.addEventListener('submit', function (e) {
            var q = input.value.trim();
            if (q.length >= MIN_CHARS) {
                window.location.href = 'timkiem.php?q=' + encodeURIComponent(q);
                e.preventDefault();
            }
        });

        document.addEventListener('click', function (e) {
            if (!wrap.contains(e.target)) closeDropdown();
        });
    }

    /* ── Trang timkiem.php ── */
    function initPageSearch() {
        var form = document.getElementById('ajax-search-form');
        var resultsArea = document.getElementById('ajax-search-results');
        var headerBlock = document.getElementById('ajax-results-header');
        var countEl = document.getElementById('ajax-result-count');
        var kwEl = document.getElementById('ajax-result-keyword');
        var statusEl = document.getElementById('ajax-search-status');

        if (!form || !resultsArea) return;

        var input = form.querySelector('input[name="q"]');

        function setHeader(keyword, count) {
            if (!headerBlock) return;
            if (keyword.length < MIN_CHARS) {
                headerBlock.style.display = 'none';
                return;
            }
            headerBlock.style.display = 'flex';
            if (kwEl) kwEl.textContent = keyword;
            if (countEl) countEl.textContent = count + ' kết quả';
        }

        function setStatus(html) {
            if (statusEl) statusEl.innerHTML = html || '';
        }

        function runPageSearch() {
            var q = input.value.trim();

            if (q.length === 0) {
                resultsArea.innerHTML =
                    '<div class="empty-state">' +
                    '<i class="fa-solid fa-magnifying-glass"></i>' +
                    '<h3>Bắt đầu tìm kiếm</h3>' +
                    '<p>Nhập tên sách hoặc truyện vào ô tìm kiếm phía trên.</p>' +
                    '</div>';
                setHeader('', 0);
                setStatus('');
                if (history.replaceState) {
                    history.replaceState(null, '', 'timkiem.php');
                }
                return;
            }

            if (q.length < MIN_CHARS) {
                resultsArea.innerHTML =
                    '<div class="empty-state">' +
                    '<p>Nhập ít nhất ' + MIN_CHARS + ' ký tự để tìm.</p>' +
                    '</div>';
                setHeader(q, 0);
                setStatus('');
                return;
            }

            resultsArea.classList.add('is-loading');
            setStatus(
                '<span class="ajax-loading-inline"><i class="fa-solid fa-spinner fa-spin"></i> Đang tìm...</span>'
            );

            if (history.replaceState) {
                history.replaceState(null, '', 'timkiem.php?q=' + encodeURIComponent(q));
            }

            fetchSearch(q, 24)
                .then(function (data) {
                    resultsArea.classList.remove('is-loading');
                    setStatus('');
                    if (!data.success) throw new Error('fail');
                    setHeader(q, data.count);
                    resultsArea.innerHTML = renderResultsGrid(data.items, q);
                    resultsArea.querySelectorAll('img').forEach(coverFallback);
                })
                .catch(function () {
                    resultsArea.classList.remove('is-loading');
                    setStatus('');
                    resultsArea.innerHTML =
                        '<div class="empty-state"><h3>Lỗi tìm kiếm</h3><p>Vui lòng thử lại.</p></div>';
                });
        }

        input.addEventListener('input', function () {
            clearTimeout(debounceTimers.get(input));
            debounceTimers.set(
                input,
                setTimeout(runPageSearch, DEBOUNCE_MS)
            );
        });

        form.addEventListener('submit', function (e) {
            e.preventDefault();
            runPageSearch();
        });

        if (input.value.trim().length >= MIN_CHARS) {
            runPageSearch();
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('form[data-ajax-search]').forEach(initHeaderSearch);
        initPageSearch();
    });
})();
