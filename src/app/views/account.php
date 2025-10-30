<?php 
include __DIR__ . '/templates/header.php';
?>

<link rel="stylesheet" href="../public/assets/css/style_account.css">
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

<script>
    document.querySelectorAll('.account-tab').forEach(tab => {
    tab.addEventListener('click', function() {
        document.querySelectorAll('.account-tab').forEach(t => {
            t.classList.remove('active');
        });
        
        this.classList.add('active');
        
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.remove('active');
        });
        
        const tabId = this.getAttribute('data-tab');
        document.getElementById(tabId).classList.add('active');
    });
});

document.getElementById('personalInfoForm').addEventListener('submit', function(e) {
    e.preventDefault();
    showSuccessMessage();
});

const changePasswordBtn = document.getElementById('changePasswordBtn');
const changePasswordModal = document.getElementById('changePasswordModal');
const cancelChangePassword = document.getElementById('cancelChangePassword');

changePasswordBtn.addEventListener('click', function() {
    changePasswordModal.style.display = 'flex';
});

cancelChangePassword.addEventListener('click', function() {
    changePasswordModal.style.display = 'none';
});

window.addEventListener('click', function(e) {
    if (e.target === changePasswordModal) {
        changePasswordModal.style.display = 'none';
    }
});

document.getElementById('changePasswordForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const newPassword = document.getElementById('newPassword').value;
    const confirmNewPassword = document.getElementById('confirmNewPassword').value;
    
    if (newPassword !== confirmNewPassword) {
        alert('Mật khẩu mới và xác nhận mật khẩu không khớp!');
        return;
    }
    
    alert('Mật khẩu đã được thay đổi thành công!');
    changePasswordModal.style.display = 'none';
    this.reset();
});

function showSuccessMessage() {
    const successMessage = document.getElementById('successMessage');
    successMessage.style.display = 'block';
    
    setTimeout(function() {
        successMessage.style.display = 'none';
    }, 5000);
}

// Order detail functionality
const orderDetailModal = document.getElementById('orderDetailModal');
const closeOrderDetail = document.getElementById('closeOrderDetail');

// Sample order data based on your database schema
const orderData = {
    'ORD-001': {
        date: '15/10/2023',
        status: 'Hoàn thành',
        payment_method: 'Chuyển khoản ngân hàng',
        payment_status: 'success',
        shipping_fullname: 'Nguyễn Lan Anh',
        shipping_phone: '0987654321',
        shipping_address: '123 Đường ABC, Quận 1, TP.HCM',
        shipping_fee: '0',
        notes: 'Giao hàng giờ hành chính',
        items: [
            { 
                name: 'Nhẫn vàng 18K đính kim cương nhân tạo', 
                quantity: 1, 
                price: '5.800.000đ', 
                total: '5.800.000đ',
                warranty_start: '15/10/2023',
                warranty_end: '15/10/2024'
            },
            { 
                name: 'Dây chuyền bạc Ý 925 mặt đá Ruby', 
                quantity: 1, 
                price: '2.450.000đ', 
                total: '2.450.000đ',
                warranty_start: '15/10/2023',
                warranty_end: '15/10/2024'
            }
        ],
        subtotal: '8.250.000đ',
        total: '8.250.000đ'
    },
    'ORD-002': {
        date: '22/10/2023',
        status: 'Đang xử lý',
        payment_method: 'Thẻ tín dụng',
        payment_status: 'success',
        shipping_fullname: 'D. Patel',
        shipping_phone: '0987654321',
        shipping_address: '45 Nguyễn Huệ, Quận 1, TP.HCM',
        shipping_fee: '25.000đ',
        notes: '',
        items: [
            { 
                name: 'Bông tai vàng trắng 14K đính đá Swarovski', 
                quantity: 1, 
                price: '4.200.000đ', 
                total: '4.200.000đ',
                warranty_start: '22/10/2023',
                warranty_end: '22/10/2025'
            }
        ],
        subtotal: '4.200.000đ',
        total: '4.225.000đ'
    },
    'ORD-003': {
        date: '05/11/2023',
        status: 'Chờ xác nhận',
        payment_method: 'COD',
        payment_status: 'pending',
        shipping_fullname: 'D. Patel',
        shipping_phone: '0987654321',
        shipping_address: '123 Đường ABC, Quận 1, TP.HCM',
        shipping_fee: '0',
        notes: 'Gọi điện trước khi giao hàng',
        items: [
            { 
                name: 'Lắc tay vàng hồng 18K đính đá Citrine', 
                quantity: 1, 
                price: '3.600.000đ', 
                total: '3.600.000đ',
                warranty_start: '05/11/2023',
                warranty_end: '05/11/2024'
            },
            { 
                name: 'Hộp đựng trang sức cao cấp', 
                quantity: 1, 
                price: '250.000đ', 
                total: '250.000đ',
                warranty_start: '05/11/2023',
                warranty_end: '05/11/2024'
            }
        ],
        subtotal: '3.850.000đ',
        total: '3.850.000đ'
    }
};

// Add event listeners to detail buttons
document.querySelectorAll('.btn-detail').forEach(button => {
    button.addEventListener('click', function() {
        const orderId = this.getAttribute('data-order');
        showOrderDetails(orderId);
    });
});

// Show order details in modal
function showOrderDetails(orderId) {
    const order = orderData[orderId];
    
    document.getElementById('orderId').textContent = '#' + orderId;
    document.getElementById('orderDate').textContent = order.date;
    document.getElementById('orderStatus').textContent = order.status;
    document.getElementById('paymentMethod').textContent = order.payment_method;
    
    // Set payment status with appropriate styling
    const paymentStatusElement = document.getElementById('paymentStatus');
    paymentStatusElement.textContent = getPaymentStatusText(order.payment_status);
    paymentStatusElement.className = 'payment-status ' + getPaymentStatusClass(order.payment_status);
    
    document.getElementById('shippingFullname').textContent = order.shipping_fullname;
    document.getElementById('shippingPhone').textContent = order.shipping_phone;
    document.getElementById('shippingAddress').textContent = order.shipping_address;
    
    const orderItemsContainer = document.getElementById('orderItems');
    orderItemsContainer.innerHTML = '';
    
    order.items.forEach(item => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>
                <div>${item.name}</div>
                <div class="warranty-info">Bảo hành: ${item.warranty_start} - ${item.warranty_end}</div>
            </td>
            <td>${item.quantity}</td>
            <td>${item.price}</td>
            <td>${item.total}</td>
        `;
        orderItemsContainer.appendChild(row);
    });
    
    document.getElementById('subtotal').textContent = order.subtotal;
    document.getElementById('shippingFee').textContent = order.shipping_fee === '0' ? 'Miễn phí' : order.shipping_fee + 'đ';
    document.getElementById('orderTotal').textContent = order.total;
    
    // Show customer notes if available
    const notesContainer = document.getElementById('customerNotesContainer');
    const notesElement = document.getElementById('customerNotes');
    if (order.notes && order.notes.trim() !== '') {
        notesElement.textContent = order.notes;
        notesContainer.style.display = 'block';
    } else {
        notesContainer.style.display = 'none';
    }
    
    orderDetailModal.style.display = 'flex';
}

// Helper functions for payment status
function getPaymentStatusText(status) {
    switch(status) {
        case 'pending': return 'Đang chờ';
        case 'success': return 'Thành công';
        case 'failed': return 'Thất bại';
        default: return status;
    }
}

function getPaymentStatusClass(status) {
    switch(status) {
        case 'pending': return 'payment-pending';
        case 'success': return 'payment-success';
        case 'failed': return 'payment-failed';
        default: return '';
    }
}

// Close order detail modal
closeOrderDetail.addEventListener('click', function() {
    orderDetailModal.style.display = 'none';
});

window.addEventListener('click', function(e) {
    if (e.target === orderDetailModal) {
        orderDetailModal.style.display = 'none';
    }
});
</script>
<?php include __DIR__ . '/templates/footer.php'; ?>

