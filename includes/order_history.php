<?php
function addOrderStatusHistory($pdo, $order_id, $status, $note = '', $user_id = null) {
    try {
        $stmt = $pdo->prepare("
            INSERT INTO order_status_history 
            (order_id, status, note, created_by) 
            VALUES (?, ?, ?, ?)
        ");
        $result = $stmt->execute([$order_id, $status, $note, $user_id]);
        
        if (!$result) {
            error_log("Lỗi khi thêm lịch sử đơn hàng: " . print_r($stmt->errorInfo(), true));
            return false;
        }
        
        return $pdo->lastInsertId();
    } catch (Exception $e) {
        error_log("Lỗi trong addOrderStatusHistory: " . $e->getMessage());
        return false;
    }
}

function getOrderStatusHistory($pdo, $order_id) {
    try {
        // Lấy tất cả lịch sử đơn hàng từ bảng order_status_history
        $stmt = $pdo->prepare("
            SELECT 
                h.*,
                COALESCE(u.username, 'Hệ thống') as created_by_name,
                CASE 
                    WHEN h.status = 'pending' THEN 'Chờ xử lý'
                    WHEN h.status = 'processing' THEN 'Đang xử lý'
                    WHEN h.status = 'shipping' THEN 'Đang giao hàng'
                    WHEN h.status = 'delivered' THEN 'Đã giao hàng'
                    WHEN h.status = 'completed' THEN 'Hoàn thành'
                    WHEN h.status = 'canceled' THEN 'Đã hủy'
                    ELSE h.status
                END as status_text
            FROM order_status_history h
            LEFT JOIN users u ON h.created_by = u.id
            WHERE h.order_id = ?
            ORDER BY h.created_at ASC
        ");
        $stmt->execute([$order_id]);
        $history = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Nếu không có lịch sử, tạo một bản ghi mặc định từ bảng orders
        if (empty($history)) {
            $orderStmt = $pdo->prepare("SELECT status, created_at FROM orders WHERE id = ?");
            $orderStmt->execute([$order_id]);
            $order = $orderStmt->fetch(PDO::FETCH_ASSOC);
            
            if ($order) {
                $status = $order['status'];
                $history[] = [
                    'id' => null,
                    'order_id' => $order_id,
                    'status' => $status,
                    'status_text' => [
                        'pending' => 'Chờ xử lý',
                        'processing' => 'Đang xử lý',
                        'shipping' => 'Đang giao hàng',
                        'delivered' => 'Đã giao hàng',
                        'completed' => 'Hoàn thành',
                        'canceled' => 'Đã hủy'
                    ][$status] ?? $status,
                    'note' => [
                        'pending' => 'Đơn hàng đã được tiếp nhận và đang chờ xử lý',
                        'processing' => 'Đơn hàng đang được xử lý',
                        'shipping' => 'Đơn hàng đang được vận chuyển',
                        'delivered' => 'Đơn hàng đã được giao thành công',
                        'completed' => 'Đơn hàng đã hoàn tất',
                        'canceled' => 'Đơn hàng đã bị hủy'
                    ][$status] ?? 'Đã cập nhật trạng thái đơn hàng',
                    'created_at' => $order['created_at'],
                    'created_by' => null,
                    'created_by_name' => 'Hệ thống'
                ];
            }
        } 
        // Nếu có lịch sử, cập nhật ghi chú cho từng mục
        else {
            $status_descriptions = [
                'pending' => 'Đơn hàng đã được tiếp nhận và đang chờ xử lý',
                'processing' => 'Đơn hàng đang được xử lý',
                'shipping' => 'Đơn hàng đang được vận chuyển',
                'delivered' => 'Đơn hàng đã được giao thành công',
                'completed' => 'Đơn hàng đã hoàn tất',
                'canceled' => 'Đơn hàng đã bị hủy'
            ];
            
            foreach ($history as &$item) {
                if (empty($item['note']) || strpos($item['note'], 'Cập nhật trạng thái từ') === 0) {
                    $item['note'] = $status_descriptions[$item['status']] ?? 'Đã cập nhật trạng thái đơn hàng';
                }
            }
            unset($item); // Hủy tham chiếu
        }
        
        return $history;
    } catch (Exception $e) {
        error_log("Lỗi trong getOrderStatusHistory: " . $e->getMessage());
        return [];
    }
}

// Hàm tự động thêm bản ghi lịch sử khi tạo đơn hàng mới
function initOrderStatusHistory($pdo, $order_id, $user_id = null) {
    try {
        // Lấy trạng thái hiện tại của đơn hàng
        $stmt = $pdo->prepare("SELECT status FROM orders WHERE id = ?");
        $stmt->execute([$order_id]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$order) {
            error_log("Không tìm thấy đơn hàng #$order_id");
            return false;
        }
        
        $current_status = $order['status'];
        
        // Kiểm tra xem đã có lịch sử nào chưa
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM order_status_history WHERE order_id = ?");
        $stmt->execute([$order_id]);
        
        if ($stmt->fetchColumn() == 0) {
            // Nếu chưa có, thêm bản ghi đầu tiên với trạng thái hiện tại
            $note = $current_status === 'pending' ? 'Đơn hàng được tạo' : 'Trạng thái hiện tại';
            return addOrderStatusHistory($pdo, $order_id, $current_status, $note, $user_id);
        }
        
        return true;
    } catch (Exception $e) {
        error_log("Lỗi trong initOrderStatusHistory: " . $e->getMessage());
        return false;
    }
}
?>
