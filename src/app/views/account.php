<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$base_url = '/jewelry_website';

// Include database connection
require_once __DIR__ . '/../../config/config.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['customer'])) {
    header("Location: {$base_url}/public/login.php");
    exit;
}

$customerId = $_SESSION['customer']['cus_id'] ?? null;

if (!$customerId) {
    session_destroy();
    header("Location: {$base_url}/public/login.php");
    exit;
}

// Lấy thông tin đầy đủ từ DB
$stmt = $conn->prepare("SELECT fullname, email, phone, address FROM customer WHERE cus_id = :id");
$stmt->bindParam(':id', $customerId, PDO::PARAM_INT);
$stmt->execute();
$customer = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$customer) {
    session_destroy();
    header("Location: {$base_url}/public/login.php");
    exit;
}
?>


<?php include_once __DIR__ . '/templates/header.php'; ?>

<main>
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

            <!-- TAB: Thông tin cá nhân -->
            <div id="personal-info" class="tab-content active">
                <div class="section">
                    <h2>Thông tin cá nhân</h2>
                    <p class="info-text">Cập nhật thông tin cá nhân của bạn.</p>
                    <div id="messageContainer" class="message" style="display: none;"></div>

                    <form id="personalInfoForm">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="fullname">Họ và tên</label>
                                <input type="text" id="fullname" name="fullname" value="<?php echo htmlspecialchars($customer['fullname']); ?>">
                            </div>
                            <div class="form-group">
                                <label for="phone">Số điện thoại</label>
                                <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($customer['phone'] ?? ''); ?>">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($customer['email']); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="address">Địa chỉ</label>
                            <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($customer['address'] ?? ''); ?>">
                        </div>
                        
                        <div class="btn-group">
                            <button type="button" class="btn btn-edit" id="editInfoBtn">Cập nhật thông tin</button>
                            <button type="submit" class="btn btn-save" id="saveInfoBtn" style="display: none;">Lưu</button>
                            <button type="button" class="btn btn-secondary" id="cancelEditBtn" style="display: none;">Hủy</button>
                            <button type="button" class="btn btn-change-password" id="changePasswordBtn">Đổi mật khẩu</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- TAB: Đơn hàng -->
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
                        <tbody id="orderTableBody">
                            <!-- Dữ liệu đơn hàng sẽ được load bằng JS -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- MODAL Đổi mật khẩu -->
<div class="modal" id="changePasswordModal">
    <div class="modal-content">
        <h3>Đổi mật khẩu</h3>
        <div id="passwordMessage" class="password-message" style="display:none;"></div>

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

<!-- MODAL Chi tiết đơn hàng -->
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
                    <!-- Dữ liệu sản phẩm trong đơn -->
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

<link rel="stylesheet" href="<?php echo $base_url; ?>/public/assets/css/style_account.css">

<?php include_once __DIR__ . '/templates/footer.php'; ?>

<!-- THÊM JAVASCRIPT TRỰC TIẾP VÀO ĐÂY -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('=== ACCOUNT PAGE SCRIPT STARTED ===');

    /* ----------- HÀM HIỂN THỊ THÔNG BÁO ----------- */
    function showMessage(text, type = 'error') {
        const container = document.getElementById('messageContainer');
        if (!container) return;
        container.textContent = text;
        container.className = 'message ' + type;
        container.style.display = 'block';
        setTimeout(() => (container.style.display = 'none'), 1000);
    }

    function showPasswordMessage(message, type = 'error') {
        const msgBox = document.getElementById('passwordMessage');
        if (!msgBox) return;
        msgBox.textContent = message;
        msgBox.className = 'password-message ' + type;
        msgBox.style.display = 'block';
        setTimeout(() => (msgBox.style.display = 'none'), 1000);
    }

    function showSuccessMessage() {
        const successMessage = document.getElementById('successMessage');
        if (!successMessage) return;
        successMessage.style.display = 'block';
        setTimeout(() => (successMessage.style.display = 'none'), 1000);
    }

    /* ----------- TAB CHUYỂN ĐỔI ----------- */
    document.querySelectorAll('.account-tab').forEach(tab => {
        tab.addEventListener('click', function() {
            document.querySelectorAll('.account-tab').forEach(t => t.classList.remove('active'));
            this.classList.add('active');

            document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
            const tabId = this.getAttribute('data-tab');
            document.getElementById(tabId).classList.add('active');
        });
    });

    /* ----------- QUẢN LÝ FORM THÔNG TIN ----------- */
    const personalInfoForm = document.getElementById('personalInfoForm');
    const editInfoBtn = document.getElementById('editInfoBtn');
    const saveInfoBtn = document.getElementById('saveInfoBtn');
    const cancelEditBtn = document.getElementById('cancelEditBtn');
    const changePasswordBtn = document.getElementById('changePasswordBtn');
    let originalFormData = {};

    function setFormReadonly(isReadonly) {
        const inputs = personalInfoForm.querySelectorAll('input');
        inputs.forEach(input => {
            if (isReadonly) input.setAttribute('readonly', true);
            else input.removeAttribute('readonly');
        });
    }

    function storeOriginalValues() {
        originalFormData = {};
        const inputs = personalInfoForm.querySelectorAll('input');
        inputs.forEach(input => (originalFormData[input.name] = input.value));
    }

    function restoreOriginalValues() {
        const inputs = personalInfoForm.querySelectorAll('input');
        inputs.forEach(input => {
            if (originalFormData[input.name] !== undefined) {
                input.value = originalFormData[input.name];
            }
        });
    }

    // Khi trang load: khóa form & lưu dữ liệu ban đầu
    storeOriginalValues();
    setFormReadonly(true);

    editInfoBtn.addEventListener('click', function() {
        setFormReadonly(false);
        document.body.classList.add('editing-mode');
        personalInfoForm.querySelector('input').focus();
    });

    cancelEditBtn.addEventListener('click', function() {
        restoreOriginalValues();
        setFormReadonly(true);
        document.body.classList.remove('editing-mode');
    });

    /* ----------- CẬP NHẬT THÔNG TIN ----------- */
    personalInfoForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        const data = Object.fromEntries(new FormData(this).entries());

        try {
            const res = await fetch('../app/controllers/updateCustomerController.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });

            if (!res.ok) throw new Error('Network error');
            const result = await res.json();

            if (result.success) {
                setFormReadonly(true);
                document.body.classList.remove('editing-mode');
                Object.assign(originalFormData, data);
                showSuccessMessage();
            } else {
                showMessage(result.message || 'Cập nhật thất bại!');
            }
        } catch (err) {
            console.error(err);
            showMessage('Đã xảy ra lỗi khi cập nhật!');
        }
    });

    /* ----------- POPUP ĐỔI MẬT KHẨU ----------- */
    const changePasswordModal = document.getElementById('changePasswordModal');
    const cancelChangePassword = document.getElementById('cancelChangePassword');
    const changePasswordForm = document.getElementById('changePasswordForm');

    changePasswordBtn.addEventListener('click', function() {
        const isReadonly = personalInfoForm.querySelector('input').hasAttribute('readonly');
        if (!isReadonly) {
            showPasswordMessage('Vui lòng lưu thông tin trước khi đổi mật khẩu!');
            return;
        }
        changePasswordModal.style.display = 'flex';
    });

    cancelChangePassword.addEventListener('click', function() {
        changePasswordModal.style.display = 'none';
        changePasswordForm.reset();
    });

    window.addEventListener('click', function(e) {
        if (e.target === changePasswordModal) {
            changePasswordModal.style.display = 'none';
            changePasswordForm.reset();
        }
    });

    changePasswordForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        const currentPassword = document.getElementById('currentPassword').value.trim();
        const newPassword = document.getElementById('newPassword').value.trim();
        const confirmNewPassword = document.getElementById('confirmNewPassword').value.trim();

        if (newPassword !== confirmNewPassword) {
            showPasswordMessage('Mật khẩu mới và xác nhận mật khẩu không khớp!');
            return;
        }
        if (newPassword.length < 6) {
            showPasswordMessage('Mật khẩu mới phải có ít nhất 6 ký tự!');
            return;
        }

        try {
            const res = await fetch('../app/controllers/changePasswordController.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ currentPassword, newPassword })
            });

            if (!res.ok) throw new Error('Network error');
            const result = await res.json();

            if (result.success) {
                showPasswordMessage(result.message, 'success');
                setTimeout(() => {
                    changePasswordModal.style.display = 'none';
                    changePasswordForm.reset();
                }, 1000);
            } else {
                showPasswordMessage(result.message || 'Không thể đổi mật khẩu!', 'error');
            }
        } catch (err) {
            console.error(err);
            showPasswordMessage('Đã xảy ra lỗi khi đổi mật khẩu!');
        }
    });

    console.log('=== ACCOUNT PAGE SCRIPT LOADED SUCCESSFULLY ===');

    /* ----------- LOAD LỊCH SỬ ĐƠN HÀNG ----------- */
async function loadOrders() {
    try {
        const res = await fetch('../app/controllers/getOrdersController.php');
        const data = await res.json();
        if (!data.success) return;

        const tbody = document.getElementById('orderTableBody');
        tbody.innerHTML = '';

        data.orders.forEach(o => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>#${o.order_id}</td>
                <td>${new Date(o.order_date).toLocaleDateString('vi-VN')}</td>
                <td>${parseFloat(o.total).toLocaleString()}₫</td>
                <td>${getOrderStatus(o.status)}</td>
                <td>
                    <span class="payment-status ${getPaymentStatusClass(o.payment_status)}">
                        ${getPaymentStatusText(o.payment_status)}
                    </span>
                </td>

                <td><button class="btn btn-detail view-detail" data-id="${o.order_id}">Xem</button></td>

            `;
            tbody.appendChild(tr);
        });

        document.querySelectorAll('.view-detail').forEach(btn => {
            btn.addEventListener('click', () => loadOrderDetail(btn.dataset.id));
        });
    } catch (err) {
        console.error(err);
    }
}
function getPaymentStatusClass(status) {
    switch (status) {
        case 'success': return 'payment-success';
        case 'pending': return 'payment-pending';
        case 'failed':  return 'payment-failed';
        default:        return '';
    }
}

function getPaymentStatusText(status) {
    switch (status) {
        case 'success': return 'Đã thanh toán';
        case 'pending': return 'Chờ thanh toán';
        case 'failed':  return 'Thanh toán thất bại';
        default:        return 'Không xác định';
    }
}

function getOrderStatus(status) {
    const map = {
        0: 'Chờ xác nhận',
        1: 'Đã xác nhận',
        2: 'Đang giao',
        3: 'Hoàn thành',
        4: 'Đã hủy'
    };
    return map[status] || 'Không xác định';
}

/* ----------- XEM CHI TIẾT ĐƠN HÀNG ----------- */
async function loadOrderDetail(orderId) {
    try {
        const res = await fetch(`../app/controllers/getOrderDetailController.php?order_id=${orderId}`);
        const data = await res.json();
        if (!data.success) return alert(data.message);

        const o = data.order;
        document.getElementById('orderId').textContent = '#' + o.order_id;
        document.getElementById('orderDate').textContent = new Date(o.order_date).toLocaleDateString('vi-VN');
        document.getElementById('orderStatus').textContent = getOrderStatus(o.status);
        document.getElementById('paymentMethod').textContent = o.payment_method || 'Không rõ';
        document.getElementById('paymentStatus').textContent = o.payment_status === 'success' ? 'Đã thanh toán' : 'Chưa thanh toán';
        document.getElementById('shippingFullname').textContent = o.shipping_fullname;
        document.getElementById('shippingPhone').textContent = o.shipping_phone;
        document.getElementById('shippingAddress').textContent = o.shipping_address;
        document.getElementById('shippingFee').textContent = parseFloat(o.shipping_fee).toLocaleString() + '₫';
        document.getElementById('orderTotal').textContent = parseFloat(o.total).toLocaleString() + '₫';
        document.getElementById('customerNotesContainer').style.display = o.notes ? 'block' : 'none';
        document.getElementById('customerNotes').textContent = o.notes || '';

        const tbody = document.getElementById('orderItems');
        tbody.innerHTML = '';
        data.items.forEach(i => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${i.product_name} ${i.size ? `(${i.size})` : ''}</td>
                <td>${i.quantity}</td>
                <td>${parseFloat(i.price_at_purchase).toLocaleString()}₫</td>
                <td>${parseFloat(i.total_item).toLocaleString()}₫</td>
            `;
            tbody.appendChild(tr);
        });

        document.getElementById('orderDetailModal').style.display = 'flex';
    } catch (err) {
        console.error(err);
    }
}

document.getElementById('closeOrderDetail').addEventListener('click', () => {
    document.getElementById('orderDetailModal').style.display = 'none';
});

loadOrders();

});
</script>
