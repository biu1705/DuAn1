<?php
// Bật ghi log lỗi chi tiết
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

header('Content-Type: application/json');

try {
    // Giỏ hàng rỗng
    if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    
    // Kiểm tra dữ liệu POST
    if (!isset($_POST['quantity']) || !is_array($_POST['quantity'])) {
        throw new Exception('Dữ liệu không hợp lệ');
    }

    // Cập nhật GIẢN ĐƠN - chỉ số lượng, không cần kiểm tra CSDL
    $updated = false;
    
    foreach ($_POST['quantity'] as $product_id => $quantity) {
        $product_id = (int)$product_id;
        $quantity = (int)$quantity;
        
        // Nếu sản phẩm có trong giỏ hàng và số lượng hợp lệ
        if (isset($_SESSION['cart'][$product_id]) && $quantity > 0) {
            $_SESSION['cart'][$product_id]['quantity'] = $quantity;
            $updated = true;
        } 
        // Nếu số lượng bằng 0, xóa sản phẩm
        else if (isset($_SESSION['cart'][$product_id]) && $quantity <= 0) {
            unset($_SESSION['cart'][$product_id]);
            $updated = true;
        }
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Đã cập nhật giỏ hàng thành công'
    ]);

} catch (Exception $e) {
    error_log('Cart update error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} 
