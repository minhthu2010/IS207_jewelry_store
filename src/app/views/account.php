<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$base_url = '/jewelry_website';

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
<link rel="stylesheet" href="../public/assets/css/style_account.css">

<main class="account-page-main">
    <div class="account-page-container">
        <!-- Header với tabs -->
        <div class="account-page-header">
            <div class="account-page-header-content">
                <div class="account-tabs-container">
                    <div class="account-tabs">
                        <button class="account-tab active" data-tab="personal-info">
                            <span>Thông tin cá nhân</span>
                        </button>
                        <button class="account-tab" data-tab="my-orders">
                            <span>Đơn hàng của tôi</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="account-page-content-wrapper">
            <div class="account-page-content">
                <!-- Success Message -->
                <div class="account-success-message" id="successMessage">
                    <span>Thông tin của bạn đã được cập nhật thành công!</span>
                </div>

                <!-- TAB: Thông tin cá nhân -->
                <div id="personal-info" class="account-tab-content active">
                    <div class="account-section">
                        <div class="account-section-header">
                            <h2 class="account-section-title">Thông tin cá nhân</h2>
                            <p class="account-section-subtitle">Cập nhật thông tin cá nhân của bạn</p>
                        </div>
                        
                        <div id="messageContainer" class="account-message" style="display: none;"></div>

                        <form id="personalInfoForm" class="account-form">
                            <div class="account-form-grid">
                                <div class="account-form-group">
                                    <label for="fullname" class="account-form-label">Họ và tên</label>
                                    <input type="text" id="fullname" name="fullname" 
                                           class="account-form-input" 
                                           value="<?php echo htmlspecialchars($customer['fullname']); ?>"
                                           readonly>
                                </div>
                                
                                <div class="account-form-group">
                                    <label for="phone" class="account-form-label">Số điện thoại</label>
                                    <input type="text" id="phone" name="phone" 
                                           class="account-form-input" 
                                           value="<?php echo htmlspecialchars($customer['phone'] ?? ''); ?>"
                                           readonly>
                                </div>
                            </div>
                            
                            <div class="account-form-single">
                                <div class="account-form-group">
                                    <label for="email" class="account-form-label">Email</label>
                                    <input type="email" id="email" name="email" 
                                           class="account-form-input" 
                                           value="<?php echo htmlspecialchars($customer['email']); ?>"
                                           readonly>
                                </div>
                                
                                <div class="account-form-group">
                                    <label for="address" class="account-form-label">Địa chỉ</label>
                                    <input type="text" id="address" name="address" 
                                           class="account-form-input" 
                                           value="<?php echo htmlspecialchars($customer['address'] ?? ''); ?>"
                                           readonly>
                                </div>
                            </div>
                            
                            <div class="account-form-actions">
                                <button type="button" class="account-btn account-btn-edit" id="editInfoBtn">
                                    <span>Cập nhật thông tin</span>
                                </button>
                                <button type="submit" class="account-btn account-btn-save" id="saveInfoBtn" style="display: none;">
                                    <span>Lưu thông tin</span>
                                </button>
                                <button type="button" class="account-btn account-btn-cancel" id="cancelEditBtn" style="display: none;">
                                    <span>Hủy bỏ</span>
                                </button>
                                <button type="button" class="account-btn account-btn-change-password" id="changePasswordBtn">
                                    <span>Đổi mật khẩu</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- TAB: Đơn hàng -->
                <div id="my-orders" class="account-tab-content">
                    <div class="account-section">
                        <div class="account-section-header">
                            <h2 class="account-section-title">Đơn hàng của tôi</h2>
                            <p class="account-section-subtitle">Xem lịch sử và trạng thái đơn hàng của bạn</p>
                        </div>
                        
                        <div class="account-orders-container">
                            <table class="account-orders-table">
                                <thead>
                                    <tr>
                                        <th class="order-id">Mã đơn hàng</th>
                                        <th class="order-date">Ngày đặt</th>
                                        <th class="order-total">Tổng tiền</th>
                                        <th class="order-status">Trạng thái</th>
                                        <th class="order-payment">Thanh toán</th>
                                        <th class="order-actions">Chi tiết</th>
                                    </tr>
                                </thead>
                                <tbody id="orderTableBody">
                                    <!-- Dữ liệu đơn hàng sẽ được load bằng JS -->
                                    <tr>
                                        <td colspan="6" class="account-loading">
                                            <div class="account-loading-content">
                                                <span>Đang tải đơn hàng...</span>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- MODAL Đổi mật khẩu -->
<div class="account-modal" id="changePasswordModal">
    <div class="account-modal-overlay" id="changePasswordModalOverlay"></div>
    <div class="account-modal-content">
        <div class="account-modal-header">
            <h3 class="account-modal-title">Đổi mật khẩu</h3>
            <button type="button" class="account-modal-close" id="closePasswordModal">
                ×
            </button>
        </div>
        
        <div id="passwordMessage" class="account-message" style="display:none;"></div>

        <form id="changePasswordForm" class="account-form">
            <div class="account-form-group">
                <label for="currentPassword" class="account-form-label">Mật khẩu hiện tại</label>
                <input type="password" id="currentPassword" name="currentPassword" 
                       class="account-form-input" 
                       placeholder="Nhập mật khẩu hiện tại"
                       required>
            </div>
            
            <div class="account-form-group">
                <label for="newPassword" class="account-form-label">Mật khẩu mới</label>
                <input type="password" id="newPassword" name="newPassword" 
                       class="account-form-input" 
                       placeholder="Nhập mật khẩu mới (ít nhất 6 ký tự)"
                       required>
            </div>
            
            <div class="account-form-group">
                <label for="confirmNewPassword" class="account-form-label">Xác nhận mật khẩu mới</label>
                <input type="password" id="confirmNewPassword" name="confirmNewPassword" 
                       class="account-form-input" 
                       placeholder="Nhập lại mật khẩu mới"
                       required>
            </div>
            
            <div class="account-modal-actions">
                <button type="button" class="account-btn account-btn-cancel" id="cancelChangePassword">
                    <span>Hủy bỏ</span>
                </button>
                <button type="submit" class="account-btn account-btn-primary">
                    <span>Đổi mật khẩu</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL Chi tiết đơn hàng -->
<div class="account-modal" id="orderDetailModal">
    <div class="account-modal-overlay" id="orderDetailModalOverlay"></div>
    <div class="account-modal-content account-modal-large">
        <div class="account-modal-header">
            <h3 class="account-modal-title">Chi tiết đơn hàng <span id="orderId"></span></h3>
            <button type="button" class="account-modal-close" id="closeOrderDetailModal">
                ×
            </button>
        </div>
        
        <div class="account-order-details">
            <div class="account-order-info-grid">
                <div class="account-order-info-column">
                    <div class="account-info-group">
                        <label>Ngày đặt:</label>
                        <span id="orderDate"></span>
                    </div>
                    <div class="account-info-group">
                        <label>Trạng thái đơn hàng:</label>
                        <span id="orderStatus"></span>
                    </div>
                    <div class="account-info-group">
                        <label>Phương thức thanh toán:</label>
                        <span id="paymentMethod"></span>
                    </div>
                    <div class="account-info-group">
                        <label>Trạng thái thanh toán:</label>
                        <span id="paymentStatus"></span>
                    </div>
                </div>
                <div class="account-order-info-column">
                    <div class="account-info-group">
                        <label>Người nhận:</label>
                        <span id="shippingFullname"></span>
                    </div>
                    <div class="account-info-group">
                        <label>Điện thoại:</label>
                        <span id="shippingPhone"></span>
                    </div>
                    <div class="account-info-group">
                        <label>Địa chỉ giao hàng:</label>
                        <span id="shippingAddress"></span>
                    </div>
                </div>
            </div>

            <div class="account-order-items-container">
                <h4 class="account-order-items-title">Sản phẩm đã đặt</h4>
                <table class="account-order-items">
                    <thead>
                        <tr>
                            <th class="product-name">Sản phẩm</th>
                            <th class="product-quantity">Số lượng</th>
                            <th class="product-price">Đơn giá</th>
                            <th class="product-total">Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody id="orderItems">
                        <!-- Dữ liệu sản phẩm trong đơn -->
                    </tbody>
                </table>
            </div>
            
            <div class="account-order-summary">
                <div class="account-summary-row">
                    <span>Tạm tính:</span>
                    <span id="subtotal"></span>
                </div>
                <div class="account-summary-row">
                    <span>Phí vận chuyển:</span>
                    <span id="shippingFee"></span>
                </div>
                <div class="account-summary-row account-summary-total">
                    <span>Tổng cộng:</span>
                    <span id="orderTotal"></span>
                </div>
            </div>

            <div class="account-customer-notes" id="customerNotesContainer" style="display: none;">
                <h4 class="account-notes-title">Ghi chú của khách hàng:</h4>
                <p id="customerNotes"></p>
            </div>
        </div>
        
        <div class="account-modal-actions">
            <button type="button" class="account-btn account-btn-primary" id="closeOrderDetail">
                <span>Đóng</span>
            </button>
        </div>
    </div>
</div>

<!-- Loading Overlay -->
<div class="account-loading-overlay" id="loadingOverlay">
    <div class="account-loading-content">
        <div class="account-loading-spinner"></div>
        <p>Đang xử lý...</p>
    </div>
</div>

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
        container.className = 'account-message ' + type;
        container.style.display = 'block';
        setTimeout(() => (container.style.display = 'none'), 3000);
    }

    function showPasswordMessage(message, type = 'error') {
        const msgBox = document.getElementById('passwordMessage');
        if (!msgBox) return;
        msgBox.textContent = message;
        msgBox.className = 'account-message ' + type;
        msgBox.style.display = 'block';
        setTimeout(() => (msgBox.style.display = 'none'), 3000);
    }

    function showSuccessMessage() {
        const successMessage = document.getElementById('successMessage');
        if (!successMessage) return;
        successMessage.style.display = 'block';
        setTimeout(() => (successMessage.style.display = 'none'), 3000);
    }

    /* ----------- TAB CHUYỂN ĐỔI ----------- */
    document.querySelectorAll('.account-tab').forEach(tab => {
        tab.addEventListener('click', function() {
            if (this.classList.contains('active')) return;
            
            document.querySelectorAll('.account-tab').forEach(t => t.classList.remove('active'));
            this.classList.add('active');

            document.querySelectorAll('.account-tab-content').forEach(c => c.classList.remove('active'));
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
            if (isReadonly) {
                input.setAttribute('readonly', true);
                input.classList.add('readonly');
            } else {
                input.removeAttribute('readonly');
                input.classList.remove('readonly');
            }
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
        document.body.classList.add('account-editing-mode');
        personalInfoForm.querySelector('input').focus();
    });

    cancelEditBtn.addEventListener('click', function() {
        restoreOriginalValues();
        setFormReadonly(true);
        document.body.classList.remove('account-editing-mode');
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
                document.body.classList.remove('account-editing-mode');
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
    const closePasswordModal = document.getElementById('closePasswordModal');
    const changePasswordForm = document.getElementById('changePasswordForm');
    const changePasswordModalOverlay = document.getElementById('changePasswordModalOverlay');

    function showPasswordModal() {
        changePasswordModal.style.display = 'block';
        document.body.style.overflow = 'hidden';
    }

    function hidePasswordModal() {
        changePasswordModal.style.display = 'none';
        changePasswordForm.reset();
        document.body.style.overflow = 'auto';
    }

    changePasswordBtn.addEventListener('click', function() {
        const isReadonly = personalInfoForm.querySelector('input').hasAttribute('readonly');
        if (!isReadonly) {
            showPasswordMessage('Vui lòng lưu thông tin trước khi đổi mật khẩu!');
            return;
        }
        showPasswordModal();
    });

    cancelChangePassword.addEventListener('click', hidePasswordModal);
    closePasswordModal.addEventListener('click', hidePasswordModal);
    changePasswordModalOverlay.addEventListener('click', hidePasswordModal);

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
                    hidePasswordModal();
                }, 1500);
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

            if (data.orders.length === 0) {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td colspan="6" class="account-empty">
                        <div class="account-empty-content">
                            <p>Bạn chưa có đơn hàng nào</p>
                        </div>
                    </td>
                `;
                tbody.appendChild(tr);
                return;
            }

        
            data.orders.forEach(o => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td class="order-id">#${o.order_id}</td>
                    <td class="order-date">${new Date(o.order_date).toLocaleDateString('vi-VN')}</td>
                    <td class="order-total">${parseFloat(o.total).toLocaleString()}₫</td>
                    <td class="order-status">${getOrderStatus(o.status)}</td>
                    <td class="order-payment">
                        <span class="account-payment-status ${getPaymentStatusClass(o.payment_status)}">
                            ${getPaymentStatusText(o.payment_status)}
                        </span>
                    </td>
                    <td class="order-actions">
                        <button class="account-btn account-btn-detail view-detail" data-id="${o.order_id}">
                            <span>Xem</span>
                        </button>

                        ${
                            o.status == 0
                            ? `
                            <button class="account-btn-cancel-order cancel-order" data-id="${o.order_id}">
                                <span>Hủy đơn hàng</span>
                            </button>
                            `
                            : ''
                        }
                    </td>

                `;
                tbody.appendChild(tr);
            });

            document.querySelectorAll('.view-detail').forEach(btn => {
                btn.addEventListener('click', () => loadOrderDetail(btn.dataset.id));
            });

            document.querySelectorAll('.cancel-order').forEach(btn => {
                btn.addEventListener('click', () => {
                    const orderId = btn.dataset.id;
                    cancelOrder(orderId);
                });
            });
        } catch (err) {
            console.error(err);
            const tbody = document.getElementById('orderTableBody');
            tbody.innerHTML = `
                <tr>
                    <td colspan="6" class="account-error">
                        <div class="account-error-content">
                            <p>Có lỗi khi tải đơn hàng</p>
                        </div>
                    </td>
                </tr>
            `;
        }
    }

    function getPaymentStatusText(status) {
        switch (status) {
            case 'success': return 'Đã thanh toán';
            case 'pending': return 'Chờ thanh toán';
            case 'failed':  return 'Thất bại';
            default:        return 'Không xác định';
        }
    }

    function getOrderStatus(status) {
        const map = {
            0: 'Chờ xác nhận',
            1: 'Đã xác nhận',
            2: 'Đã hủy'
        };
        return map[status] || 'Không xác định';
    }

    function getPaymentStatusClass(status) {
        switch (status) {
            case 'success': return 'account-payment-success';
            case 'pending': return 'account-payment-pending';
            case 'failed':  return 'account-payment-failed';
            default:        return '';
        }
    }

    function getPaymentStatusText(status) {
        switch (status) {
            case 'success': return 'Đã thanh toán';
            case 'pending': return 'Chờ thanh toán';
            case 'failed':  return 'Thất bại';
            default:        return 'Không xác định';
        }
    }

    /* ----------- XEM CHI TIẾT ĐƠN HÀNG ----------- */
    async function loadOrderDetail(orderId) {
        try {
            const loadingOverlay = document.getElementById('loadingOverlay');
            loadingOverlay.style.display = 'flex';

            const res = await fetch(`../app/controllers/getOrderDetailController.php?order_id=${orderId}`);
            const data = await res.json();
            
            loadingOverlay.style.display = 'none';
            
            if (!data.success) {
                alert(data.message || 'Không thể tải chi tiết đơn hàng');
                return;
            }

            const o = data.order;
            document.getElementById('orderId').textContent = '#' + o.order_id;
            document.getElementById('orderDate').textContent = new Date(o.order_date).toLocaleDateString('vi-VN');
            document.getElementById('orderStatus').textContent = getOrderStatus(o.status);
            document.getElementById('paymentMethod').textContent = o.payment_method || 'Không rõ';
            document.getElementById('paymentStatus').textContent = getPaymentStatusText(o.payment_status);
            document.getElementById('shippingFullname').textContent = o.shipping_fullname;
            document.getElementById('shippingPhone').textContent = o.shipping_phone;
            document.getElementById('shippingAddress').textContent = o.shipping_address;
            document.getElementById('shippingFee').textContent = parseFloat(o.shipping_fee).toLocaleString() + '₫';
            document.getElementById('orderTotal').textContent = parseFloat(o.total).toLocaleString() + '₫';
            
            if (o.notes) {
                document.getElementById('customerNotesContainer').style.display = 'block';
                document.getElementById('customerNotes').textContent = o.notes;
            } else {
                document.getElementById('customerNotesContainer').style.display = 'none';
            }

            const tbody = document.getElementById('orderItems');
            tbody.innerHTML = '';
            data.items.forEach(i => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td class="product-name">
                        <div class="account-product-info">
                            <span class="account-product-name">${i.product_name} ${i.size ? `(${i.size})` : ''}</span>
                        </div>
                    </td>
                    <td class="product-quantity">${i.quantity}</td>
                    <td class="product-price">${parseFloat(i.price_at_purchase).toLocaleString()}₫</td>
                    <td class="product-total">${parseFloat(i.total_item).toLocaleString()}₫</td>
                `;
                tbody.appendChild(tr);
            });

            document.getElementById('orderDetailModal').style.display = 'block';
            document.body.style.overflow = 'hidden';
        } catch (err) {
            console.error(err);
            document.getElementById('loadingOverlay').style.display = 'none';
            alert('Có lỗi khi tải chi tiết đơn hàng');
        }
    }

    // Close order detail modal
    document.getElementById('closeOrderDetail').addEventListener('click', () => {
        document.getElementById('orderDetailModal').style.display = 'none';
        document.body.style.overflow = 'auto';
    });

    document.getElementById('closeOrderDetailModal').addEventListener('click', () => {
        document.getElementById('orderDetailModal').style.display = 'none';
        document.body.style.overflow = 'auto';
    });

    document.getElementById('orderDetailModalOverlay').addEventListener('click', () => {
        document.getElementById('orderDetailModal').style.display = 'none';
        document.body.style.overflow = 'auto';
    });

    async function cancelOrder(orderId) {
    // Sử dụng SweetAlert2 thay vì confirm mặc định
        Swal.fire({
            title: 'Xác nhận',
            text: "Bạn có chắc chắn muốn hủy đơn hàng này không?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Đồng ý',
            cancelButtonText: 'Hủy'
        }).then(async (result) => {
            if (!result.isConfirmed) return;

            try {
                const loadingOverlay = document.getElementById('loadingOverlay');
                loadingOverlay.style.display = 'flex';

                const res = await fetch('../app/controllers/cancelOrderController.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ order_id: orderId })
                });

                const data = await res.json();
                loadingOverlay.style.display = 'none';

                if (data.success) {
                    Swal.fire('Thành công!', 'Đơn hàng đã được hủy thành công', 'success');
                    loadOrders(); // reload lại danh sách
                } else {
                    Swal.fire('Lỗi!', data.message || 'Không thể hủy đơn hàng', 'error');
                }
            } catch (err) {
                console.error(err);
                loadingOverlay.style.display = 'none';
                Swal.fire('Lỗi!', 'Có lỗi xảy ra khi hủy đơn hàng', 'error');
            }
        });
    }



    // Load orders khi trang được load
    loadOrders();

});
</script>
