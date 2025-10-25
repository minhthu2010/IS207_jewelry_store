<?php
$base_url = '/jewelry_website';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh Toán - D. Patel Jewelry</title>
    <link rel="stylesheet" href="<?php echo $base_url; ?>/public/assets/css/style_checkout.css">
</head>
<body>
    <header class="main-header">
        <div class="header-container">
            <div class="logo">D. Patel</div>
            <ul class="nav-menu">
                <li><a href="#">Shop All</a></li>
                <li><a href="#">About</a></li>
                <li><a href="#">Contact</a></li>
            </ul>
            <div class="header-icons">
                <div class="search-icon">Q</div>
                <div class="account-icon">
                    <div class="user-avatar">DP</div>
                    <span>Account</span>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <div class="container">
        <div class="checkout-header">
            <h1>Thanh Toán Đơn Hàng</h1>
            <p>Hoàn tất thông tin để hoàn thành đơn hàng của bạn</p>
        </div>

        <div class="checkout-content">
            <!-- Checkout Form -->
            <div class="checkout-form">
                <div class="section">
                    <h2>Thông Tin Giao Hàng</h2>
                    <form id="checkout-form">
                        <div class="form-group">
                            <label for="fullname">Họ và Tên *</label>
                            <input type="text" id="fullname" name="fullname" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="phone">Số Điện Thoại *</label>
                            <input type="tel" id="phone" name="phone" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email *</label>
                            <input type="email" id="email" name="email" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="address">Địa Chỉ Giao Hàng *</label>
                            <input type="text" id="address" name="address" placeholder="Ví dụ: 123 Đường ABC, Quận 1, TP.HCM" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="notes">Ghi Chú Đơn Hàng (Tùy chọn)</label>
                            <textarea id="notes" name="notes" rows="3"></textarea>
                        </div>
                    </form>
                </div>
                
                <div class="section">
                    <h2>Phương Thức Thanh Toán</h2>
                    <div class="payment-methods">
                        <div class="payment-method">
                            <input type="radio" id="cod" name="payment" value="cod" checked>
                            <label for="cod">
                                Thanh toán khi nhận hàng (COD)
                            </label>
                        </div>
                        
                        <div class="payment-method">
                            <input type="radio" id="bank" name="payment" value="bank">
                            <label for="bank">
                                Chuyển khoản ngân hàng
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Order Summary -->
            <div class="order-summary">
                <div class="section">
                    <h2>Đơn Hàng Của Bạn</h2>
                    <table class="order-items">
                        <thead>
                            <tr>
                                <th>Sản Phẩm</th>
                                <th>Số Lượng</th>
                                <th>Giá</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="product-info">
                                        <div class="product-image">Hình ảnh</div>
                                        <div>
                                            <div class="product-name">Vòng Tay Vàng 18K</div>
                                            <div class="product-variant">Size: 16cm</div>
                                        </div>
                                    </div>
                                </td>
                                <td>1</td>
                                <td>5.250.000₫</td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="product-info">
                                        <div class="product-image">Hình ảnh</div>
                                        <div>
                                            <div class="product-name">Nhẫn Bạc Đính Đá</div>
                                            <div class="product-variant">Size: 12</div>
                                        </div>
                                    </div>
                                </td>
                                <td>2</td>
                                <td>1.800.000₫</td>
                            </tr>
                        </tbody>
                    </table>
                    
                    <div class="price-summary">
                        <div class="summary-row">
                            <span>Tạm tính:</span>
                            <span>7.050.000₫</span>
                        </div>
                        <div class="summary-row">
                            <span>Phí vận chuyển:</span>
                            <span>30.000₫</span>
                        </div>
                        <div class="summary-row">
                            <span>Giảm giá:</span>
                            <span>0₫</span>
                        </div>
                        <div class="summary-row summary-total">
                            <span>Tổng cộng:</span>
                            <span>7.080.000₫</span>
                        </div>
                    </div>
                    
                    <button class="btn" id="place-order">Đặt Hàng</button>
                </div>
            </div>
        </div>
    </div>

    <footer class="main-footer">
    <div class="footer-content">
        <div class="footer-section">
            <h3>Về chúng tôi</h3>
            <ul>
                <li><a href="#">Giới thiệu</a></li>
                <li><a href="#">Đội ngũ</a></li>
                <li><a href="#">Tin tức</a></li>
            </ul>
        </div>
        <div class="footer-section">
            <h3>Liên hệ</h3>
            <ul>
                <li><a href="#">Hỗ trợ</a></li>
                <li><a href="#">FAQ</a></li>
                <li><a href="#">Liên hệ</a></li>
            </ul>
        </div>
        <div class="footer-section">
            <h3>Tài khoản</h3>
            <ul>
                <li><a href="#">Đăng nhập</a></li>
                <li><a href="#">Đăng ký</a></li>
                <li><a href="#">Quên mật khẩu</a></li>
            </ul>
        </div>
    </div>
    <div class="copyright">
        &copy; 2023 D. Patel. Tất cả các quyền được bảo lưu.
    </div>
</footer>

    <!-- Success Modal -->
    <div class="modal" id="orderSuccessModal">
        <div class="modal-content">
            <div class="success-icon">✓</div>
            <h3>Đặt Hàng Thành Công!</h3>
            <p>Cảm ơn bạn đã đặt hàng. Mã đơn hàng của bạn là: <strong>#ORD-004</strong></p>
            <p>Chúng tôi sẽ liên hệ với bạn trong thời gian sớm nhất để xác nhận đơn hàng.</p>
            <div class="modal-buttons">
                <button type="button" class="btn" id="continueShopping">Tiếp Tục Mua Sắm</button>
                <button type="button" class="btn btn-secondary" id="viewOrderDetail">Xem Chi Tiết Đơn Hàng</button>
            </div>
        </div>
    </div>

    <script src="<?php echo $base_url; ?>/public/assets/js/checkout.js"></script>
</body>
</html>