<?php
class Statistics {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getTotalProducts() {
        try {
            $stmt = $this->conn->query("SELECT COUNT(*) as total FROM products");
            $result = $stmt->fetch();
            return $result['total'];
        } catch (PDOException $e) {
            error_log("Error getting total products: " . $e->getMessage());
            return 0;
        }
    }

    public function getTotalRevenue($startDate = null, $endDate = null) {
        try {
            $sql = "SELECT SUM(od.quantity * od.price) as total 
                   FROM orders o
                   JOIN order_items od ON o.id = od.order_id
                   WHERE o.status != 'cancelled'";
            
            if ($startDate && $endDate) {
                $sql .= " AND o.created_at BETWEEN ? AND ?";
                $stmt = $this->conn->prepare($sql);
                $stmt->execute([$startDate, $endDate]);
            } else {
                $stmt = $this->conn->query($sql);
            }
            
            $result = $stmt->fetch();
            return $result['total'] ?? 0;
        } catch (PDOException $e) {
            error_log("Error getting total revenue: " . $e->getMessage());
            return 0;
        }
    }

    public function getTotalCustomers() {
        try {
            $stmt = $this->conn->query("SELECT COUNT(*) as total FROM users WHERE role = 'customer'");
            $result = $stmt->fetch();
            return $result['total'];
        } catch (PDOException $e) {
            error_log("Error getting total customers: " . $e->getMessage());
            return 0;
        }
    }

    public function getTotalOrders($startDate = null, $endDate = null) {
        try {
            $sql = "SELECT COUNT(*) as total FROM orders";
            
            if ($startDate && $endDate) {
                $sql .= " WHERE created_at BETWEEN ? AND ?";
                $stmt = $this->conn->prepare($sql);
                $stmt->execute([$startDate, $endDate]);
            } else {
                $stmt = $this->conn->query($sql);
            }
            
            $result = $stmt->fetch();
            return $result['total'];
        } catch (PDOException $e) {
            error_log("Error getting total orders: " . $e->getMessage());
            return 0;
        }
    }

    public function getPendingOrders() {
        try {
            $sql = "SELECT o.*, u.username as customer_name
                   FROM orders o 
                   LEFT JOIN users u ON o.user_id = u.id 
                   WHERE o.status = 'pending'
                   ORDER BY o.created_at DESC";
            $stmt = $this->conn->query($sql);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error getting pending orders: " . $e->getMessage());
            return [];
        }
    }

    public function getTopStockProducts($limit = 5) {
        try {
            $sql = "SELECT p.*, c.name as category, p.quantity as stock_quantity 
                   FROM products p
                   LEFT JOIN categories c ON p.category_id = c.id
                   ORDER BY p.quantity DESC
                   LIMIT :limit";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error getting top stock products: " . $e->getMessage());
            return [];
        }
    }

    public function getTopCustomers($limit = 5) {
        try {
            $sql = "SELECT 
                        u.id, u.username, u.email,
                        COUNT(o.id) as total_orders,
                        SUM(od.quantity * od.price) as total_spent
                    FROM users u
                    LEFT JOIN orders o ON u.id = o.user_id
                    LEFT JOIN order_items od ON o.id = od.order_id
                    WHERE u.role = 'customer'
                    GROUP BY u.id
                    ORDER BY total_spent DESC
                    LIMIT :limit";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error getting top customers: " . $e->getMessage());
            return [];
        }
    }

    public function getRevenueByMonth($startDate = null, $endDate = null) {
        try {
            $sql = "SELECT 
                        DATE_FORMAT(o.created_at, '%Y-%m') as month,
                        SUM(od.quantity * od.price) as revenue
                    FROM orders o
                    JOIN order_items od ON o.id = od.order_id
                    WHERE o.status != 'cancelled'";
            
            if ($startDate && $endDate) {
                $sql .= " AND o.created_at BETWEEN ? AND ?";
                $params = [$startDate, $endDate];
            }
            
            $sql .= " GROUP BY DATE_FORMAT(o.created_at, '%Y-%m')
                      ORDER BY month ASC";
            
            if ($startDate && $endDate) {
                $stmt = $this->conn->prepare($sql);
                $stmt->execute($params);
            } else {
                $stmt = $this->conn->query($sql);
            }
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error getting revenue by month: " . $e->getMessage());
            return [];
        }
    }

    public function getCategoryStats() {
        try {
            $sql = "SELECT 
                        c.name,
                        COUNT(DISTINCT o.id) as total_orders,
                        SUM(od.quantity * od.price) as revenue
                    FROM categories c
                    LEFT JOIN products p ON c.id = p.category_id
                    LEFT JOIN order_items od ON p.id = od.product_id
                    LEFT JOIN orders o ON od.order_id = o.id
                    WHERE (o.status != 'cancelled' OR o.status IS NULL)
                    GROUP BY c.id
                    ORDER BY revenue DESC";
            $stmt = $this->conn->query($sql);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error getting category stats: " . $e->getMessage());
            return [];
        }
    }

    public function getProductStats() {
        try {
            $sql = "SELECT 
                        p.name,
                        SUM(od.quantity) as quantity_sold
                    FROM products p
                    LEFT JOIN order_items od ON p.id = od.product_id
                    LEFT JOIN orders o ON od.order_id = o.id
                    WHERE (o.status != 'cancelled' OR o.status IS NULL)
                    GROUP BY p.id
                    ORDER BY quantity_sold DESC";
            $stmt = $this->conn->query($sql);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error getting product stats: " . $e->getMessage());
            return [];
        }
    }

    public function getTotalCategories() {
        try {
            $stmt = $this->conn->query("SELECT COUNT(*) as total FROM categories WHERE deleted_at IS NULL");
            $result = $stmt->fetch();
            return $result['total'];
        } catch (PDOException $e) {
            error_log("Error getting total categories: " . $e->getMessage());
            return 0;
        }
    }

    public function getTotalUsers() {
        try {
            $stmt = $this->conn->query("SELECT COUNT(*) as total FROM users WHERE deleted_at IS NULL");
            $result = $stmt->fetch();
            return $result['total'];
        } catch (PDOException $e) {
            error_log("Error getting total users: " . $e->getMessage());
            return 0;
        }
    }

    public function getRecentOrders($limit = 5) {
        try {
            $sql = "SELECT o.*, u.username, u.email 
                   FROM orders o 
                   LEFT JOIN users u ON o.user_id = u.id 
                   ORDER BY o.created_at DESC 
                   LIMIT ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$limit]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error getting recent orders: " . $e->getMessage());
            return [];
        }
    }

    public function getTopProducts($limit = 5) {
        try {
            $sql = "SELECT p.*, 
                          COUNT(od.product_id) as order_count,
                          SUM(od.quantity) as total_quantity
                   FROM products p
                   LEFT JOIN order_items od ON p.id = od.product_id
                   WHERE p.deleted_at IS NULL
                   GROUP BY p.id
                   ORDER BY total_quantity DESC
                   LIMIT ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$limit]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error getting top products: " . $e->getMessage());
            return [];
        }
    }

    public function getMonthlyRevenue() {
        try {
            $sql = "SELECT 
                        DATE_FORMAT(o.created_at, '%Y-%m') as month,
                        COUNT(DISTINCT o.id) as total_orders,
                        SUM(od.quantity * od.price) as revenue
                    FROM orders o
                    JOIN order_items od ON o.id = od.order_id
                    WHERE o.status != 'cancelled'
                    GROUP BY DATE_FORMAT(o.created_at, '%Y-%m')
                    ORDER BY month DESC
                    LIMIT 12";
            $stmt = $this->conn->query($sql);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error getting monthly revenue: " . $e->getMessage());
            return [];
        }
    }

    public function getOrderStatusStats() {
        try {
            $sql = "SELECT 
                        status,
                        COUNT(*) as count
                    FROM orders
                    GROUP BY status";
            $stmt = $this->conn->query($sql);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error getting order status stats: " . $e->getMessage());
            return [];
        }
    }
} 