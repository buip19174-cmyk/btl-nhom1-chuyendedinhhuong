<?php
/**
 * Cấu hình demo thanh toán VietQR (QR image URL).
 * Đổi thông tin ngân hàng tại đây khi demo / báo cáo.
 */
define('PAYMENT_BANK', 'MB');
define('PAYMENT_ACCOUNT', '0123456789');
define('PAYMENT_ACCOUNT_NAME', 'KEWE PLATFORM');

/** Gói nạp hợp lệ: coin => VND (1 coin = 100 VND) */
function payment_valid_packs(): array
{
    return [10, 30, 50, 100, 200, 500];
}

function payment_vnd_for_coins(int $coins): int
{
    return $coins * 100;
}

/** Tạo mã đơn nạp duy nhất */
function payment_generate_order_id(): string
{
    return 'KEWE' . date('ymdHis') . random_int(1000, 9999);
}

/**
 * URL ảnh QR VietQR (img.vietqr.io).
 * @see https://www.vietqr.io/
 */
function payment_vietqr_image_url(int $amount, string $addInfo): string
{
    $bank    = PAYMENT_BANK;
    $account = PAYMENT_ACCOUNT;
    $name    = rawurlencode(PAYMENT_ACCOUNT_NAME);
    $info    = rawurlencode($addInfo);

    return "https://img.vietqr.io/image/{$bank}-{$account}-qr_only.png"
         . "?amount={$amount}&addInfo={$info}&accountName={$name}";
}
