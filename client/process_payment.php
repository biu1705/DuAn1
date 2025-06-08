<?php
session_start();
require_once 'init.php'; // hoặc file kết nối PDO chuẩn của nhóm bạn

// Bật log lỗi cho giai đoạn phát triển (có thể tắt khi chạy thật)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Lấy order_id từ URL (theo chuẩn NV3)
$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : null;
if (!$order_id) {
    header('Location: profile.php');
    exit;
}

// Lấy thông tin đơn hàng
$stmt = $pdo->prepare("
    SELECT o.*, u.username, u.email 
    FROM orders o 
    JOIN users u ON o.user_id = u.id 
    WHERE o.id = ? AND o.user_id = ?
");
$stmt->execute([$order_id, $_SESSION['user_id']]);
$order = $stmt->fetch();

if (!$order) {
    header('Location: profile.php');
    exit;
}

// Kiểm tra nếu đơn hàng đã thanh toán
if ($order['payment_status'] === 'completed') {
    $_SESSION['error'] = "Đơn hàng này đã được thanh toán";
    header('Location: order-detail.php?id=' . $order_id);
    exit;
}

// Xử lý theo phương thức thanh toán
switch ($order['payment_method']) {
    case 'bank':
        // Cập nhật trạng thái đơn hàng (nếu chưa)
        $stmt = $pdo->prepare("UPDATE orders SET payment_status = 'pending' WHERE id = ?");
        $stmt->execute([$order_id]);
        
        // Thông tin ngân hàng (tuỳ ý sửa lại tên, số tài khoản...)
        $bank_info = [
            'name' => 'NGUYEN VAN A',
            'number' => '1234567890',
            'bank' => 'VIETCOMBANK',
            'branch' => 'Chi nhánh HCM',
            'content' => "LOTSO{$order_id}"
        ];

        require_once 'header.php';
        ?>
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card shadow">
                        <div class="card-body text-center">
                            <h4 class="mb-4">Thông tin chuyển khoản</h4>
                            <div class="mb-4">
                                <p class="mb-1">Số tiền cần chuyển:</p>
                                <h3 class="text-primary"><?= number_format($order['total_price']) ?> VNĐ</h3>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <tr>
                                        <th>Tên tài khoản</th>
                                        <td><?= $bank_info['name'] ?></td>
                                    </tr>
                                    <tr>
                                        <th>Số tài khoản</th>
                                        <td class="fw-bold"><?= $bank_info['number'] ?></td>
                                    </tr>
                                    <tr>
                                        <th>Ngân hàng</th>
                                        <td><?= $bank_info['bank'] ?></td>
                                    </tr>
                                    <tr>
                                        <th>Chi nhánh</th>
                                        <td><?= $bank_info['branch'] ?></td>
                                    </tr>
                                    <tr>
                                        <th>Nội dung chuyển khoản</th>
                                        <td class="fw-bold text-danger"><?= $bank_info['content'] ?></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="alert alert-warning mt-4">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Lưu ý quan trọng:</strong><br>
                                - Vui lòng chuyển khoản chính xác số tiền và nội dung bên trên<br>
                                - Đơn hàng sẽ được xử lý trong vòng 24h sau khi chúng tôi nhận được tiền<br>
                                - Nếu cần hỗ trợ, vui lòng liên hệ hotline: <strong>1900 xxxx</strong>
                            </div>
                            <div class="mt-4">
                                <a href="order-detail.php?id=<?= $order_id ?>" class="btn btn-primary me-2">
                                    <i class="fas fa-eye me-2"></i>Xem đơn hàng
                                </a>
                                <a href="index.php" class="btn btn-outline-secondary">
                                    <i class="fas fa-home me-2"></i>Về trang chủ
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
        require_once 'footer.php';
        break;

    case 'momo':
        $_SESSION['error'] = "Phương thức thanh toán MoMo đang được phát triển";
        header('Location: order-detail.php?id=' . $order_id);
        break;

    case 'zalopay':
        $_SESSION['error'] = "Phương thức thanh toán ZaloPay đang được phát triển";
        header('Location: order-detail.php?id=' . $order_id);
        break;

    default:
        $_SESSION['error'] = "Phương thức thanh toán không hợp lệ";
        header('Location: order-detail.php?id=' . $order_id);
        break;
}
?>
