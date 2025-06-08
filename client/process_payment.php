<?php
require_once 'init.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Lấy order_id từ URL
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
        // Cập nhật trạng thái đơn hàng
        $stmt = $pdo->prepare("UPDATE orders SET payment_status = 'pending' WHERE id = ?");
        $stmt->execute([$order_id]);
        
        // Hiển thị trang thanh toán
        require_once 'header.php';
        ?>
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header text-white" style="background-color: #ff4081;">
                            <h5 class="card-title mb-0">Thanh toán đơn hàng #<?= $order_id ?></h5>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info">
                                <h6 class="alert-heading">Thông tin chuyển khoản:</h6>
                                <p class="mb-1"><strong>Ngân hàng:</strong> BIDV</p>
                                <p class="mb-1"><strong>Số tài khoản:</strong> 123456789</p>
                                <p class="mb-1"><strong>Chủ tài khoản:</strong> CÔNG TY LOTSO</p>
                                <p class="mb-1"><strong>Số tiền:</strong> <?= number_format($order['total_price']) ?> đ</p>
                                <p class="mb-0"><strong>Nội dung chuyển khoản:</strong> LOTSO<?= $order_id ?></p>
                            </div>

                            <div class="alert alert-warning">
                                <h6 class="alert-heading">Lưu ý:</h6>
                                <ul class="mb-0">
                                    <li>Vui lòng chuyển khoản đúng số tiền và nội dung như trên</li>
                                    <li>Đơn hàng sẽ được xử lý sau khi chúng tôi nhận được thanh toán</li>
                                    <li>Thời gian xử lý thanh toán có thể mất từ 5-15 phút</li>
                                </ul>
                            </div>

                            <form action="confirm-payment.php" method="POST" enctype="multipart/form-data" class="mt-4">
                                <input type="hidden" name="order_id" value="<?= $order_id ?>">
                                

                                <div class="mb-3">
                                    <label for="payment_note" class="form-label">Ghi chú (nếu có):</label>
                                    <textarea class="form-control" id="payment_note" name="payment_note" rows="3"></textarea>
                                </div>

                                <div class="d-grid gap-2">
                                    <a href="update-payment-status.php?order_id=<?= $order_id ?>" class="btn btn-primary">
                                        <i class="fas fa-check"></i> Xác nhận đã thanh toán
                                    </a>
                                    <a href="order-detail.php?id=<?= $order_id ?>" class="btn btn-outline-secondary">
                                        <i class="fas fa-arrow-left"></i> Quay lại
                                    </a>
                                </div>
                            </form>
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
