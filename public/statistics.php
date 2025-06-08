<?php
require_once '../templates/header.php';
require_once '../templates/sidebar.php';
?>

<div class="content">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Thống kê chi tiết</h2>
            <div class="d-flex gap-2">
                <input type="date" class="form-control" id="startDate">
                <input type="date" class="form-control" id="endDate">
                <button class="btn btn-primary" onclick="updateStats()">Lọc</button>
            </div>
        </div>

        <!-- Thống kê tổng quan -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title mb-1">Tổng đơn hàng</h6>
                                <h3 class="card-text mb-0" id="totalOrders">0</h3>
                            </div>
                            <div class="fs-1 opacity-50">
                                <i class="fas fa-shopping-cart"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title mb-1">Doanh thu</h6>
                                <h3 class="card-text mb-0" id="totalRevenue">0đ</h3>
                            </div>
                            <div class="fs-1 opacity-50">
                                <i class="fas fa-dollar-sign"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title mb-1">Tổng sản phẩm</h6>
                                <h3 class="card-text mb-0" id="totalProducts">0</h3>
                            </div>
                            <div class="fs-1 opacity-50">
                                <i class="fas fa-box"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title mb-1">Tổng khách hàng</h6>
                                <h3 class="card-text mb-0" id="totalCustomers">0</h3>
                            </div>
                            <div class="fs-1 opacity-50">
                                <i class="fas fa-users"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Biểu đồ và thống kê chi tiết -->
        <div class="row">
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">Biểu đồ doanh thu</h5>
                            <div class="btn-group">
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="downloadChart('PNG')">
                                    <i class="fas fa-download"></i> PNG
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="downloadChart('PDF')">
                                    <i class="fas fa-file-pdf"></i> PDF
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <canvas id="revenueChart" style="height: 300px;"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">Đơn hàng chờ xác nhận</h5>
                    </div>
                    <div class="card-body p-0" style="max-height: 300px; overflow-y: auto;">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped mb-0">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Khách hàng</th>
                                        <th>Tổng tiền</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody id="pendingOrdersTable">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">Thống kê sản phẩm</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="productsChart" style="height: 300px;"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">Top 5 sản phẩm tồn kho</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Sản phẩm</th>
                                        <th>Danh mục</th>
                                        <th class="text-end">Tồn kho</th>
                                        <th class="text-end">Giá bán</th>
                                    </tr>
                                </thead>
                                <tbody id="topStockTable">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">Top 5 khách hàng</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Khách hàng</th>
                                        <th class="text-end">Số đơn</th>
                                        <th class="text-end">Tổng chi</th>
                                    </tr>
                                </thead>
                                <tbody id="topCustomersTable">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">Thống kê theo danh mục</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="categoryChart" style="height: 300px;"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Thêm Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
let revenueChart = null;
let categoryChart = null;
let productsChart = null;

// Hàm định dạng tiền tệ
function formatCurrency(amount) {
    return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(amount);
}

// Hàm tạo màu ngẫu nhiên
function generateRandomColors(count) {
    const colors = [];
    for (let i = 0; i < count; i++) {
        const hue = (i * (360 / count)) % 360;
        colors.push(`hsl(${hue}, 70%, 60%)`);
    }
    return colors;
}

// Hàm tải dữ liệu thống kê
async function loadStats() {
    try {
        const startDate = document.getElementById('startDate').value;
        const endDate = document.getElementById('endDate').value;
        
        const response = await fetch(`../api/statistics.php?start_date=${startDate}&end_date=${endDate}`);
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        const data = await response.json();
        
        if (data.success) {
            const stats = data.data;
            
            // Cập nhật thống kê tổng quan
            document.getElementById('totalOrders').textContent = stats.totalOrders;
            document.getElementById('totalRevenue').textContent = formatCurrency(stats.totalRevenue);
            document.getElementById('totalProducts').textContent = stats.totalProducts;
            document.getElementById('totalCustomers').textContent = stats.totalCustomers;

            // Cập nhật biểu đồ doanh thu
            updateRevenueChart(stats.revenueByMonth);
            
            // Cập nhật biểu đồ danh mục
            updateCategoryChart(stats.categoryStats);

            // Cập nhật biểu đồ sản phẩm
            updateProductsChart(stats.productStats);

            // Cập nhật bảng top sản phẩm tồn kho
            const topStockHTML = stats.topStockProducts.map(product => `
                <tr onclick="window.location='product-detail.php?id=${product.id}'">
                    <td>
                        <div class="d-flex align-items-center">
                            <img src="../uploads/products/${product.image}" class="rounded" width="40" height="40">
                            <div class="ms-2">${product.name}</div>
                        </div>
                    </td>
                    <td>${product.category}</td>
                    <td class="text-end">${product.stock_quantity}</td>
                    <td class="text-end">${formatCurrency(product.price)}</td>
                </tr>
            `).join('');
            document.getElementById('topStockTable').innerHTML = topStockHTML;

            // Cập nhật bảng top khách hàng
            const topCustomersHTML = stats.topCustomers.map(customer => `
                <tr>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle bg-light text-dark d-flex align-items-center justify-content-center" style="width: 40px; height: 40px">
                                <i class="fas fa-user"></i>
                            </div>
                            <div class="ms-2">
                                <div>${customer.username}</div>
                                <div class="text-muted small">${customer.email}</div>
                            </div>
                        </div>
                    </td>
                    <td class="text-end">${customer.total_orders}</td>
                    <td class="text-end">${formatCurrency(customer.total_spent)}</td>
                </tr>
            `).join('');
            document.getElementById('topCustomersTable').innerHTML = topCustomersHTML;

            // Cập nhật bảng đơn hàng chờ xác nhận
            const pendingOrdersHTML = stats.pendingOrders.map(order => `
                <tr onclick="window.location='order-detail.php?id=${order.id}'">
                    <td>#${order.id}</td>
                    <td>${order.customer_name}</td>
                    <td>${formatCurrency(order.total_amount)}</td>
                    <td>
                        <button class="btn btn-sm btn-primary" onclick="event.stopPropagation(); confirmOrder(${order.id})">
                            <i class="fas fa-check"></i>
                        </button>
                    </td>
                </tr>
            `).join('');
            document.getElementById('pendingOrdersTable').innerHTML = pendingOrdersHTML;
        } else {
            console.error('Error:', data.message);
        }
    } catch (error) {
        console.error('Error:', error);
    }
}

// Hàm cập nhật biểu đồ doanh thu
function updateRevenueChart(data) {
    const ctx = document.getElementById('revenueChart').getContext('2d');
    
    if (revenueChart) {
        revenueChart.destroy();
    }

    // Định dạng lại tên tháng
    const labels = data.map(item => {
        const [year, month] = item.month.split('-');
        const date = new Date(year, parseInt(month) - 1);
        return date.toLocaleDateString('vi-VN', { month: 'long', year: 'numeric' });
    });
    
    const revenues = data.map(item => item.revenue);
    
    revenueChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Doanh thu',
                    data: revenues,
                    backgroundColor: 'rgba(54, 162, 235, 0.5)',
                    borderColor: 'rgb(54, 162, 235)',
                    borderWidth: 2,
                    borderRadius: 5,
                    order: 2
                },
                {
                    label: 'Xu hướng',
                    data: revenues,
                    type: 'line',
                    borderColor: 'rgb(255, 99, 132)',
                    borderWidth: 3,
                    pointBackgroundColor: 'rgb(255, 99, 132)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 6,
                    pointHoverRadius: 8,
                    fill: false,
                    tension: 0.3,
                    order: 1
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                intersect: false,
                mode: 'index'
            },
            plugins: {
                legend: {
                    labels: {
                        font: {
                            size: 12,
                            family: "'Segoe UI', Arial, sans-serif"
                        },
                        usePointStyle: true,
                        pointStyle: 'circle'
                    }
                },
                tooltip: {
                    padding: 12,
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleColor: '#ffffff',
                    bodyColor: '#ffffff',
                    borderColor: '#ffffff',
                    borderWidth: 1,
                    titleFont: {
                        size: 14,
                        family: "'Segoe UI', Arial, sans-serif"
                    },
                    bodyFont: {
                        size: 13,
                        family: "'Segoe UI', Arial, sans-serif"
                    },
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ' + formatCurrency(context.raw);
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        font: {
                            size: 12,
                            family: "'Segoe UI', Arial, sans-serif"
                        }
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.1)'
                    },
                    ticks: {
                        font: {
                            size: 12,
                            family: "'Segoe UI', Arial, sans-serif"
                        },
                        callback: function(value) {
                            return formatCurrency(value);
                        }
                    }
                }
            }
        }
    });
}

// Hàm cập nhật biểu đồ danh mục
function updateCategoryChart(data) {
    const ctx = document.getElementById('categoryChart').getContext('2d');
    
    if (categoryChart) {
        categoryChart.destroy();
    }
    
    categoryChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: data.map(item => item.name),
            datasets: [{
                data: data.map(item => item.revenue),
                backgroundColor: generateRandomColors(data.length)
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right',
                    labels: {
                        font: {
                            size: 11
                        }
                    }
                }
            }
        }
    });
}

// Hàm cập nhật biểu đồ sản phẩm
function updateProductsChart(data) {
    const ctx = document.getElementById('productsChart').getContext('2d');
    
    if (productsChart) {
        productsChart.destroy();
    }

    // Lọc ra các sản phẩm có số lượng bán > 0
    const soldProducts = data.filter(item => item.quantity_sold > 0);
    
    // Màu sắc đáng yêu phù hợp với theme
    const colors = [
        '#FF69B4', // Pink
        '#FFB6C1', // LightPink
        '#FFC0CB', // Pink
        '#FF1493', // DeepPink
        '#DB7093', // PaleVioletRed
        '#C71585', // MediumVioletRed
        '#FF69B4', // HotPink
        '#FFB6C1', // LightPink
        '#FFA07A', // LightSalmon
        '#FF8C00', // DarkOrange
        '#FFA500', // Orange
        '#FFD700', // Gold
        '#F0E68C', // Khaki
        '#DDA0DD', // Plum
        '#DA70D6'  // Orchid
    ];

    // Tính tổng số lượng bán
    const totalSold = soldProducts.reduce((sum, product) => sum + product.quantity_sold, 0);

    productsChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: soldProducts.map(item => item.name),
            datasets: [{
                data: soldProducts.map(item => item.quantity_sold),
                backgroundColor: colors,
                borderColor: '#ffffff',
                borderWidth: 3,
                hoverBorderColor: '#ffffff',
                hoverBorderWidth: 4,
                hoverOffset: 10
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right',
                    labels: {
                        font: {
                            size: 12,
                            family: "'Segoe UI', Arial, sans-serif"
                        },
                        padding: 15,
                        usePointStyle: true,
                        pointStyle: 'circle',
                        color: '#666'
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.raw || 0;
                            const percentage = ((value / totalSold) * 100).toFixed(1);
                            return `${label}: ${value} sản phẩm (${percentage}%)`;
                        }
                    },
                    titleFont: {
                        size: 14,
                        family: "'Segoe UI', Arial, sans-serif"
                    },
                    bodyFont: {
                        size: 13,
                        family: "'Segoe UI', Arial, sans-serif"
                    },
                    padding: 12,
                    backgroundColor: 'rgba(255, 105, 180, 0.9)',
                    titleColor: '#ffffff',
                    bodyColor: '#ffffff',
                    borderColor: '#ffffff',
                    borderWidth: 1,
                    displayColors: true,
                    boxWidth: 8,
                    boxHeight: 8,
                    boxPadding: 4,
                    usePointStyle: true
                }
            },
            cutout: '65%',
            radius: '85%',
            layout: {
                padding: 20
            },
            animation: {
                animateScale: true,
                animateRotate: true,
                duration: 1000,
                easing: 'easeInOutQuart'
            }
        }
    });
}

// Hàm xác nhận đơn hàng
async function confirmOrder(orderId) {
    try {
        const response = await fetch('../api/update_order.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                id: orderId,
                status: 'processing'
            })
        });
        
        const data = await response.json();
        if (data.success) {
            loadStats(); // Tải lại dữ liệu
        }
    } catch (error) {
        console.error('Error:', error);
    }
}

// Hàm tải lại thống kê
function updateStats() {
    loadStats();
}

// Hàm tải xuống biểu đồ
function downloadChart(type) {
    const canvas = document.getElementById('revenueChart');
    if (type === 'PNG') {
        const link = document.createElement('a');
        link.download = 'revenue-chart.png';
        link.href = canvas.toDataURL('image/png');
        link.click();
    } else if (type === 'PDF') {
        // Implement PDF download
    }
}

// Khởi tạo ngày mặc định
document.getElementById('startDate').value = new Date(Date.now() - 30 * 24 * 60 * 60 * 1000).toISOString().split('T')[0];
document.getElementById('endDate').value = new Date().toISOString().split('T')[0];

// Tải dữ liệu khi trang được tải
document.addEventListener('DOMContentLoaded', loadStats);
</script>

<?php require_once '../templates/footer.php'; ?> 