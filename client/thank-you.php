<?php
require_once 'init.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Kiểm tra order_id
if (!isset($_GET['order_id'])) {
    header('Location: index.php');
    exit;
}

$order_id = filter_var($_GET['order_id'], FILTER_VALIDATE_INT);
if (!$order_id) {
    header('Location: index.php');
    exit;
}

try {
    // Lấy thông tin đơn hàng
    $stmt = $pdo->prepare("
        SELECT o.*, u.username, u.email, u.phone 
        FROM orders o 
        LEFT JOIN users u ON o.user_id = u.id 
        WHERE o.id = ? AND o.user_id = ?
    ");
    $stmt->execute([$order_id, $_SESSION['user_id']]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        header('Location: index.php');
        exit;
    }

    // Lấy chi tiết đơn hàng
    $stmt = $pdo->prepare("
        SELECT oi.*, p.name as product_name, p.image as product_image 
        FROM order_items oi 
        LEFT JOIN products p ON oi.product_id = p.id 
        WHERE oi.order_id = ?
    ");
    $stmt->execute([$order_id]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    error_log($e->getMessage());
    header('Location: index.php');
    exit;
}

require_once 'header.php';
?>

<div class="container py-5">
    <div class="text-center mb-5">
        <i class="fas fa-check-circle text-success" style="font-size: 5rem;"></i>
        <h1 class="mt-3">Cảm ơn bạn đã đặt hàng!</h1>
        <p class="lead">Đơn hàng #<?= $order_id ?> của bạn đã được xác nhận.</p>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">Thông tin đơn hàng</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6>Thông tin giao hàng</h6>
                            <p><strong>Họ tên:</strong> <?= htmlspecialchars($order['shipping_name']) ?></p>
                            <p><strong>Số điện thoại:</strong> <?= htmlspecialchars($order['shipping_phone']) ?></p>
                            <p><strong>Địa chỉ:</strong> <?= htmlspecialchars($order['shipping_address']) ?></p>
                        </div>
                        <div class="col-md-6">
                            <h6>Chi tiết đơn hàng</h6>
                            <p><strong>Mã đơn hàng:</strong> #<?= $order_id ?></p>
                            <p><strong>Ngày đặt:</strong> <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></p>
                            <p><strong>Phương thức thanh toán:</strong> 
                                <?php
                                echo match($order['payment_method']) {
                                    'cod' => 'Thanh toán khi nhận hàng',
                                    'bank' => 'Chuyển khoản ngân hàng',
                                    'momo' => 'Ví MoMo',
                                    'zalopay' => 'Ví ZaloPay',
                                    default => 'Không xác định'
                                };
                                ?>
                            </p>
                            <p><strong>Trạng thái:</strong> 
                                <span class="badge bg-<?php
                                    echo match($order['status']) {
                                        'pending' => 'secondary',
                                        'processing' => 'warning',
                                        'completed' => 'success',
                                        'canceled' => 'danger',
                                        default => 'secondary'
                                    };
                                ?>">
                                    <?php
                                    echo match($order['status']) {
                                        'pending' => 'Chờ xử lý',
                                        'processing' => 'Đang xử lý',
                                        'completed' => 'Hoàn thành',
                                        'canceled' => 'Đã hủy',
                                        default => 'Không xác định'
                                    };
                                    ?>
                                </span>
                            </p>
                        </div>
                    </div>

                    <h6>Sản phẩm đã đặt</h6>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Sản phẩm</th>
                                    <th>Đơn giá</th>
                                    <th>Số lượng</th>
                                    <th class="text-end">Thành tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($items as $item): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <?php if ($item['product_image']): ?>
                                                <img src="../uploads/<?= htmlspecialchars($item['product_image']) ?>" 
                                                     alt="<?= htmlspecialchars($item['product_name']) ?>" 
                                                     class="me-2" style="width: 50px; height: 50px; object-fit: cover;">
                                            <?php endif; ?>
                                            <span><?= htmlspecialchars($item['product_name']) ?></span>
                                        </div>
                                    </td>
                                    <td><?= number_format($item['price']) ?> đ</td>
                                    <td><?= $item['quantity'] ?></td>
                                    <td class="text-end"><?= number_format($item['price'] * $item['quantity']) ?> đ</td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="text-end"><strong>Tổng cộng:</strong></td>
                                    <td class="text-end"><strong><?= number_format($order['total_price']) ?> đ</strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <div class="text-center">
                <a href="index.php" class="btn btn-primary me-2">
                    <i class="fas fa-home"></i> Trang chủ
                </a>
                <a href="order-detail.php?id=<?= $order_id ?>" class="btn btn-outline-primary">
                    <i class="fas fa-list"></i> Xem đơn hàng của tôi
                </a>
            </div>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>
