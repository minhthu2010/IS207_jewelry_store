<?php
$base_url = '/jewelry_website';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Settings - D. Patel Jewelry</title>
    <link rel="stylesheet" href="<?php echo $base_url; ?>/public/assets/css/style_account.css">
</head>
<body>

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

    <div class="account-header">
        <div class="account-container">
            <div class="account-tabs">
                <div class="account-tab active" data-tab="personal-info">Thông tin cá nhân</div>
                <div class="account-tab" data-tab="my-orders">Đơn hàng của tôi</div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="content">
            <div class="success-message" id="successMessage">
                Thông tin của bạn đã được cập nhật thành công!
            </div>

            <div id="personal-info" class="tab-content active">
                <div class="section">
                    <h2>Thông tin cá nhân</h2>
                    <p class="info-text">Cập nhật thông tin cá nhân của bạn.</p>
                    
                    <form id="personalInfoForm">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="fullname">Họ và tên</label>
                                <input type="text" id="fullname" name="fullname" value="D. Patel">
                            </div>
                            <div class="form-group">
                                <label for="phone">Số điện thoại</label>
                                <input type="text" id="phone" name="phone" value="0987654321">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" value="d.patel@example.com">
                        </div>
                        
                        <div class="form-group">
                            <label for="address">Địa chỉ</label>
                            <input type="text" id="address" name="address" value="123 Đường ABC, Quận 1, TP.HCM">
                        </div>
                        
                        <div class="btn-group">
                            <button type="button" class="btn btn-secondary">Hủy</button>
                            <button type="submit" class="btn">Cập nhật thông tin</button>
                            <button type="button" class="btn btn-change-password" id="changePasswordBtn">Đổi mật khẩu</button>
                        </div>
                    </form>
                </div>
            </div>

            <div id="my-orders" class="tab-content">
                <div class="section">
                    <h2>Đơn hàng của tôi</h2>
                    <p class="info-text">Xem lịch sử và trạng thái đơn hàng của bạn.</p>
                    
                    <table class="orders-table">
                        <thead>
                            <tr>
                                <th>Mã đơn hàng</th>
                                <th>Ngày đặt</th>
                                <th>Tổng tiền</th>
                                <th>Trạng thái</th>
                                <th>Thanh toán</th>
                                <th>Chi tiết</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>#ORD-001</td>
                                <td>15/10/2023</td>
                                <td>850.000đ</td>
                                <td><span class="order-status status-completed">Hoàn thành</span></td>
                                <td><span class="payment-status payment-success">Thành công</span></td>
                                <td><button class="btn btn-detail" data-order="ORD-001">Chi tiết</button></td>
                            </tr>
                            <tr>
                                <td>#ORD-002</td>
                                <td>22/10/2023</td>
                                <td>1.250.000đ</td>
                                <td><span class="order-status status-processing">Đang xử lý</span></td>
                                <td><span class="payment-status payment-success">Thành công</span></td>
                                <td><button class="btn btn-detail" data-order="ORD-002">Chi tiết</button></td>
                            </tr>
                            <tr>
                                <td>#ORD-003</td>
                                <td>05/11/2023</td>
                                <td>620.000đ</td>
                                <td><span class="order-status status-pending">Chờ xác nhận</span></td>
                                <td><span class="payment-status payment-pending">Đang chờ</span></td>
                                <td><button class="btn btn-detail" data-order="ORD-003">Chi tiết</button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <footer>
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

    <div class="modal" id="changePasswordModal">
        <div class="modal-content">
            <h3>Đổi mật khẩu</h3>
            <form id="changePasswordForm">
                <div class="form-group">
                    <label for="currentPassword">Mật khẩu hiện tại</label>
                    <input type="password" id="currentPassword" name="currentPassword" placeholder="Nhập mật khẩu hiện tại">
                </div>
                
                <div class="form-group">
                    <label for="newPassword">Mật khẩu mới</label>
                    <input type="password" id="newPassword" name="newPassword" placeholder="Nhập mật khẩu mới">
                </div>
                
                <div class="form-group">
                    <label for="confirmNewPassword">Xác nhận mật khẩu mới</label>
                    <input type="password" id="confirmNewPassword" name="confirmNewPassword" placeholder="Nhập lại mật khẩu mới">
                </div>
                
                <div class="modal-buttons">
                    <button type="button" class="btn btn-secondary" id="cancelChangePassword">Hủy</button>
                    <button type="submit" class="btn">Đổi mật khẩu</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal" id="orderDetailModal">
        <div class="modal-content">
            <h3>Chi tiết đơn hàng <span id="orderId"></span></h3>
            <div class="order-details">
                <div class="order-info-grid">
                    <div>
                        <div class="info-group">
                            <label>Ngày đặt:</label>
                            <span id="orderDate"></span>
                        </div>
                        <div class="info-group">
                            <label>Trạng thái đơn hàng:</label>
                            <span id="orderStatus"></span>
                        </div>
                        <div class="info-group">
                            <label>Phương thức thanh toán:</label>
                            <span id="paymentMethod"></span>
                        </div>
                        <div class="info-group">
                            <label>Trạng thái thanh toán:</label>
                            <span id="paymentStatus"></span>
                        </div>
                    </div>
                    <div>
                        <div class="info-group">
                            <label>Người nhận:</label>
                            <span id="shippingFullname"></span>
                        </div>
                        <div class="info-group">
                            <label>Điện thoại:</label>
                            <span id="shippingPhone"></span>
                        </div>
                        <div class="info-group">
                            <label>Địa chỉ giao hàng:</label>
                            <span id="shippingAddress"></span>
                        </div>
                    </div>
                </div>

                <table class="order-items">
                    <thead>
                        <tr>
                            <th>Sản phẩm</th>
                            <th>Số lượng</th>
                            <th>Đơn giá</th>
                            <th>Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody id="orderItems">
                    </tbody>
                </table>
                
                <div class="order-summary">
                    <div class="summary-row">
                        <span>Tạm tính:</span>
                        <span id="subtotal"></span>
                    </div>
                    <div class="summary-row">
                        <span>Phí vận chuyển:</span>
                        <span id="shippingFee"></span>
                    </div>
                    <div class="summary-row summary-total">
                        <span>Tổng cộng:</span>
                        <span id="orderTotal"></span>
                    </div>
                </div>

                <div class="customer-notes" id="customerNotesContainer" style="display: none;">
                    <h4>Ghi chú của khách hàng:</h4>
                    <p id="customerNotes"></p>
                </div>
            </div>
            <div class="modal-buttons">
                <button type="button" class="btn" id="closeOrderDetail">Đóng</button>
            </div>
        </div>
    </div>

    <script src="script.js"></script>

<script src="<?php echo $base_url; ?>/public/assets/js/account.js"></script>
</body>
</html>