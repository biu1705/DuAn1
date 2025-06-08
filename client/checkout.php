<?php
require_once 'init.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Kiểm tra giỏ hàng
if (empty($_SESSION['cart'])) {
    header('Location: cart.php');
    exit;
}

// Xử lý đặt hàng
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validate dữ liệu
        if (empty($_POST['name']) || empty($_POST['phone']) || empty($_POST['address'])) {
            throw new Exception('Vui lòng điền đầy đủ thông tin giao hàng');
        }

        if (empty($_POST['payment_method'])) {
            throw new Exception('Vui lòng chọn phương thức thanh toán');
        }

        // Kiểm tra sản phẩm tồn tại và tính tổng tiền
        $total = 0;
        $valid_products = [];
        
        foreach ($_SESSION['cart'] as $item) {
            // Kiểm tra sản phẩm có tồn tại trong database
            $stmt = $pdo->prepare("SELECT id, price FROM products WHERE id = ?");
            $stmt->execute([$item['product_id']]);
            $product = $stmt->fetch();
            
            if (!$product) {
                throw new Exception('Một số sản phẩm trong giỏ hàng không tồn tại');
            }
            
            $total += $product['price'] * $item['quantity'];
            
            // Lưu thông tin sản phẩm hợp lệ
            $valid_products[] = [
                'id' => $product['id'],
                'price' => $product['price'],
                'quantity' => $item['quantity']
            ];
        }

        // Tạo đơn hàng
        $stmt = $pdo->prepare("
            INSERT INTO orders (
                user_id, 
                shipping_name, 
                shipping_phone, 
                shipping_address, 
                payment_method, 
                total_price, 
                status,
                payment_status
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        // Xác định trạng thái
        $status = 'pending'; // Mặc định là pending cho mọi phương thức thanh toán
        $payment_status = 'pending';
        
        $stmt->execute([
            $_SESSION['user_id'], 
            $_POST['name'], 
            $_POST['phone'], 
            $_POST['address'], 
            $_POST['payment_method'], 
            $total,
            $status,
            $payment_status
        ]);
        
        $order_id = $pdo->lastInsertId();

        // Thêm chi tiết đơn hàng
        $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        
        foreach ($valid_products as $product) {
            $stmt->execute([$order_id, $product['id'], $product['quantity'], $product['price']]);
        }

        // Xóa giỏ hàng
        $_SESSION['cart'] = [];
        
        // Xử lý chuyển hướng dựa trên phương thức thanh toán
        switch ($_POST['payment_method']) {
            case 'bank':
                $_SESSION['payment_order_id'] = $order_id;
                $_SESSION['payment_method'] = $_POST['payment_method'];
                header("Location: order-detail.php?id=$order_id");
                break;
                
            case 'cod':
                header("Location: thank-you.php?order_id=$order_id");
                break;
                
            case 'momo':
            case 'zalopay':
                throw new Exception("Phương thức thanh toán " . strtoupper($_POST['payment_method']) . " đang được phát triển");
                break;
                
            default:
                throw new Exception("Phương thức thanh toán không hợp lệ");
                break;
        }
        exit;

    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

require_once 'header.php';

// Tính tổng tiền cho hiển thị
$total = 0;
foreach ($_SESSION['cart'] as $item) {
    $total += $item['price'] * $item['quantity'];
}

// Lấy thông tin người dùng
$stmt = $pdo->prepare("SELECT username, phone, address FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();
?>

<div class="container py-5">
    <h1 class="mb-4">Thanh toán</h1>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">Thông tin giao hàng</h5>
                </div>
                <div class="card-body">
                    <form id="checkoutForm" method="POST">
                        <div class="mb-3">
                            <label for="name" class="form-label">Họ tên</label>
                            <input type="text" class="form-control" id="name" name="name" 
                                   value="<?= htmlspecialchars($user['username'] ?? '') ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label">Số điện thoại</label>
                            <input type="tel" class="form-control" id="phone" name="phone" 
                                   value="<?= htmlspecialchars($user['phone'] ?? '') ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label">Địa chỉ giao hàng</label>
                            <textarea class="form-control" id="address" name="address" rows="3" required><?= htmlspecialchars($user['address'] ?? '') ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Phương thức thanh toán</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" id="cod" value="cod" checked>
                                <label class="form-check-label" for="cod">
                                    <i class="fas fa-money-bill-wave"></i> Thanh toán khi nhận hàng (COD)
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" id="bank" value="bank">
                                <label class="form-check-label" for="bank">
                                    <i class="fas fa-university"></i> Chuyển khoản ngân hàng
                                </label>
                                <div class="bank-info mt-2 ms-4" style="display: none;">
                                    <div class="alert alert-info">
                                        <p class="mb-1"><strong>Thông tin chuyển khoản:</strong></p>
                                        <p class="mb-1">Ngân hàng: <strong>Vietcombank</strong></p>
                                        <p class="mb-1">Số tài khoản: <strong>1234567890</strong></p>
                                        <p class="mb-1">Chủ tài khoản: <strong>LOTSO SHOE STORE</strong></p>
                                        <p class="mb-1">Chi nhánh: <strong>Hồ Chí Minh</strong></p>
                                        <p class="mb-0">Nội dung chuyển khoản: <strong>LOTSO [Số điện thoại]</strong></p>
                                    </div>
                                </div>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" id="momo" value="momo">
                                <label class="form-check-label" for="momo">
                                    <img src="../assets/images/momo.png" alt="MoMo" height="20"> Ví MoMo
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" id="zalopay" value="zalopay">
                                <label class="form-check-label" for="zalopay">
                                    <img src="../assets/images/zalopay.png" alt="ZaloPay" height="20"> Ví ZaloPay
                                </label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            Đặt hàng (<?= number_format($total) ?> đ)
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">Đơn hàng</h5>
                </div>
                <div class="card-body">
                    <?php foreach ($_SESSION['cart'] as $item): ?>
                        <div class="d-flex justify-content-between mb-2">
                            <div>
                                <h6 class="mb-0"><?= htmlspecialchars($item['name']) ?></h6>
                                <small class="text-muted">SL: <?= $item['quantity'] ?></small>
                            </div>
                            <div class="text-end">
                                <?= number_format($item['price'] * $item['quantity']) ?> đ
                            </div>
                        </div>
                    <?php endforeach; ?>
                    
                    <hr>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <div>Tạm tính:</div>
                        <div class="text-end"><?= number_format($total) ?> đ</div>
                    </div>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <div>Phí vận chuyển:</div>
                        <div class="text-end">Miễn phí</div>
                    </div>
                    
                    <hr>
                    
                    <div class="d-flex justify-content-between">
                        <div><strong>Tổng cộng:</strong></div>
                        <div class="text-end"><strong><?= number_format($total) ?> đ</strong></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Hiển thị/ẩn thông tin ngân hàng khi chọn phương thức thanh toán
document.querySelectorAll('input[name="payment_method"]').forEach(input => {
    input.addEventListener('change', function() {
        const bankInfo = document.querySelector('.bank-info');
        if (this.value === 'bank') {
            bankInfo.style.display = 'block';
        } else {
            bankInfo.style.display = 'none';
        }
    });
});
</script>

<?php require_once 'footer.php'; ?>
