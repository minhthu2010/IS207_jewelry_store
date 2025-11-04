<?php 
include __DIR__ . '/templates/header.php';
?>

<link rel="stylesheet" href="../public/assets/css/style_checkout.css">

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
                    <input type="hidden" id="selected_items" value="<?php echo isset($_GET['selected_items']) ? htmlspecialchars($_GET['selected_items']) : ''; ?>">
                    
                    <div class="form-group">
                        <label for="fullname">Họ và Tên *</label>
                        <input type="text" id="fullname" name="fullname" 
                               value="<?php echo htmlspecialchars($customer_info['fullname'] ?? ''); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="phone">Số Điện Thoại *</label>
                        <input type="tel" id="phone" name="phone" 
                               value="<?php echo htmlspecialchars($customer_info['phone'] ?? ''); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email *</label>
                        <input type="email" id="email" name="email" 
                               value="<?php echo htmlspecialchars($customer_info['email'] ?? ''); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="address">Địa Chỉ Giao Hàng *</label>
                        <input type="text" id="address" name="address" 
                               value="<?php echo htmlspecialchars($customer_info['address'] ?? ''); ?>" 
                               placeholder="Ví dụ: 123 Đường ABC, Quận 1, TP.HCM" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="notes">Ghi Chú Đơn Hàng (Tùy chọn)</label>
                        <textarea id="notes" name="notes" rows="3" placeholder="Ghi chú về đơn hàng..."></textarea>
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

                <div id="bank-notice" class="bank-notice" style="display: none;">
                    <div class="notice-info">
                        <p>Sau khi đặt hàng, hệ thống sẽ hiển thị mã QR và thông tin chuyển khoản chi tiết.</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Order Summary -->
        <div class="order-summary">
            <div class="section">
                <h2>Đơn Hàng Của Bạn</h2>
                <div class="order-items-container">
                    <table class="order-items">
                        <thead>
                            <tr>
                                <th>Sản Phẩm</th>
                                <th>Số Lượng</th>
                                <th>Giá</th>
                            </tr>
                        </thead>
                        <tbody id="order-items-body">
                            <?php foreach ($cart_items as $item): ?>
                            <tr>
                                <td>
                                    <div class="product-info">
                                        <div class="product-image">
                                            <?php if (!empty($item['image_url'])): ?>
                                                <!-- SỬA ĐƯỜNG DẪN ẢNH SẢN PHẨM -->
                                                <img src="<?= $base_url . 'assets/images/products/' . basename($item['image_url']) ?>" 
                                                     alt="<?php echo htmlspecialchars($item['product_name']); ?>"
                                                     onerror="this.src='<?= $base_url ?>assets/images/no-image.jpg'">
                                            <?php else: ?>
                                                <!-- SỬA ĐƯỜNG DẪN ẢNH MẶC ĐỊNH -->
                                                <img src="<?= $base_url ?>assets/images/no-image.jpg" 
                                                     alt="No image">
                                            <?php endif; ?>
                                        </div>
                                        <div>
                                            <div class="product-name"><?php echo htmlspecialchars($item['product_name']); ?></div>
                                            <div class="product-variant"><?php echo htmlspecialchars($item['attributes'] ?? ''); ?></div>
                                            <?php if ($item['warranty_period']): ?>
                                                <div class="warranty-info">Bảo hành: <?php echo htmlspecialchars($item['warranty_period']); ?> tháng</div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                                <td><?php echo number_format($item['price'] * $item['quantity'], 0, ',', '.'); ?>₫</td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="price-summary">
                    <div class="summary-row">
                        <span>Tạm tính:</span>
                        <span id="subtotal"><?php echo number_format($subtotal, 0, ',', '.'); ?>₫</span>
                    </div>
                    <div class="summary-row">
                        <span>Phí vận chuyển:</span>
                        <span id="shipping-fee">
                            <?php 
                            if ($shipping_fee == 0) {
                                echo 'Miễn phí';
                            } else {
                                echo number_format($shipping_fee, 0, ',', '.') . '₫';
                            }
                            ?>
                        </span>
                    </div>                
                    <div class="summary-row summary-total">
                        <span>Tổng cộng:</span>
                        <span id="total-amount"><?php echo number_format($total, 0, ',', '.'); ?>₫</span>
                    </div>
                </div>
                
                <button class="btn" id="place-order">Đặt Hàng</button>
            </div>
        </div>
    </div>
</div>

<!-- Success Modal -->
<div class="modal" id="orderSuccessModal">
    <div class="modal-content">
        <div class="success-icon">✓</div>
        <h3>Đặt Hàng Thành Công!</h3>
        <p id="success-message">Cảm ơn bạn đã đặt hàng.</p>
        
        <!-- THÊM: Hiển thị phương thức thanh toán đã chọn -->
        <div id="payment-method-display" style="margin: 15px 0; padding: 10px; background: #f8f9fa; border-radius: 5px;">
            <strong>Phương thức thanh toán:</strong> 
            <span id="selected-payment-method">Thanh toán khi nhận hàng (COD)</span>
        </div>

        <!-- Thông tin chuyển khoản - CHỈ HIỆN KHI CHỌN BANK -->
        <div id="bank-payment-info" style="display: none;">
            <div class="bank-info-modal">
                <h4>Quét Mã QR Để Thanh Toán</h4>
                
                <!-- QR Code -->
                <div class="qr-code-modal">
                    <!-- SỬA: Thêm base URL cho QR code nếu cần -->
                    <img id="qr-code-modal-image" src="" alt="QR Code"
                         onerror="console.log('Lỗi tải QR code')">
                </div>
                
                <!-- Thông tin chuyển khoản -->
                <div class="bank-details">
                    <div class="detail-row">
                        <strong>Số tiền:</strong> 
                        <span id="qr-amount">0₫</span>
                    </div>
                    <div class="detail-row">
                        <strong>Nội dung:</strong> 
                        <span>THANHTOAN<span id="qr-order-id"></span></span>
                    </div>
                    <div class="detail-row">
                        <strong>Ngân hàng:</strong> 
                        <span>Vietcombank</span>
                    </div>
                    <div class="detail-row">
                        <strong>Số tài khoản:</strong> 
                        <span class="account-number">2352 1474 1542 1331</span>
                    </div>
                    <div class="detail-row">
                        <strong>Chủ tài khoản:</strong> 
                        <span>JEWELRY STORE</span>
                    </div>
                </div>
                
                <!-- Hướng dẫn -->
                <div class="transfer-instruction">
                    <p><strong>Hướng dẫn:</strong></p>
                    <ol>
                        <li>Quét mã QR bằng ứng dụng ngân hàng</li>
                        <li>Hoặc chuyển khoản theo thông tin trên</li>
                        <li>Ghi đúng nội dung chuyển khoản</li>
                        <li>Đơn hàng sẽ được xử lý sau khi nhận được thanh toán</li>
                    </ol>
                </div>
            </div>
        </div>
        
        <div class="modal-buttons">
            <button type="button" class="btn" id="continueShopping">Tiếp Tục Mua Sắm</button>
        </div>
    </div>
</div>
<!-- Loading Overlay -->
<div class="loading-overlay" id="loadingOverlay" style="display: none;">
    <div class="loading-spinner"></div>
    <p>Đang xử lý đơn hàng...</p>
</div>

<script>

document.addEventListener('DOMContentLoaded', function() {
    
    const placeOrderBtn = document.getElementById('place-order');
    if (placeOrderBtn) {
        console.log('Tìm thấy nút đặt hàng');
        
        // CHỈ GẮN MỘT EVENT LISTENER
        placeOrderBtn.addEventListener('click', function() {
            console.log(' NÚT ĐẶT HÀNG ĐƯỢC CLICK!');
            placeOrder(); // Gọi hàm xử lý đặt hàng
        });
    } else {
        console.error('KHÔNG tìm thấy nút đặt hàng');
    }

    // Xử lý chọn phương thức thanh toán
    document.querySelectorAll('input[name="payment"]').forEach(radio => {
        radio.addEventListener('change', function() {
            const bankNotice = document.getElementById('bank-notice');
            if (this.value === 'bank') {
                bankNotice.style.display = 'block';
            } else {
                bankNotice.style.display = 'none';
            }
        });
    });
});

// Hàm xử lý đặt hàng
function placeOrder() {
    console.log('=== placeOrder function called ===');
    
    const loadingOverlay = document.getElementById('loadingOverlay');
    const placeOrderBtn = document.getElementById('place-order');
    
    if (!loadingOverlay || !placeOrderBtn) {
        console.error('Không tìm thấy loadingOverlay hoặc placeOrderBtn');
        return;
    }
    
    // Validate form
    const form = document.getElementById('checkout-form');
    if (!form.checkValidity()) {
        alert('Vui lòng điền đầy đủ thông tin bắt buộc!');
        
        // Highlight các trường bắt buộc chưa điền
        const requiredFields = form.querySelectorAll('input[required]');
        requiredFields.forEach(field => {
            if (!field.value) {
                field.style.borderColor = 'red';
            }
        });
        return;
    }
    
    loadingOverlay.style.display = 'flex';
    placeOrderBtn.disabled = true;

    // Lấy dữ liệu form
    const formData = {
        shipping_info: {
            fullname: document.getElementById('fullname').value,
            phone: document.getElementById('phone').value,
            email: document.getElementById('email').value,
            address: document.getElementById('address').value
        },
        payment_method: document.querySelector('input[name="payment"]:checked').value,
        selected_cart_items: document.getElementById('selected_items').value.split(',').filter(id => id),
        notes: document.getElementById('notes').value
    };

    console.log('Dữ liệu gửi đi:', formData);

    // Gửi request
    fetch('index.php?action=checkout/process', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(formData)
    })
    .then(response => {
        console.log('Response status:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('Dữ liệu nhận được:', data);
        
        loadingOverlay.style.display = 'none';
        placeOrderBtn.disabled = false;
        
        if (data.success) {
            console.log('ĐẶT HÀNG THÀNH CÔNG');
            
            // Hiển thị phương thức thanh toán
            const paymentMethodDisplay = document.getElementById('selected-payment-method');
            const paymentMethod = formData.payment_method;
            const paymentMethodText = paymentMethod === 'cod' 
                ? 'Thanh toán khi nhận hàng (COD)' 
                : 'Chuyển khoản ngân hàng';
            paymentMethodDisplay.textContent = paymentMethodText;

            // Hiển thị thông báo thành công
            const successMessage = document.getElementById('success-message');
            const bankPaymentInfo = document.getElementById('bank-payment-info');
            
            successMessage.innerHTML = `Cảm ơn bạn đã đặt hàng. Mã đơn hàng của bạn là: <strong>#ORD-${data.order_id}</strong>`;
            
            // Nếu thanh toán ngân hàng, hiển thị QR code
            if (data.transaction_data && paymentMethod === 'bank') {
                console.log('Hiển thị QR code');
                // SỬA: Thêm base URL cho QR code nếu cần
                const qrCodeSrc = data.transaction_data.qr_code.startsWith('http') 
                    ? data.transaction_data.qr_code 
                    : '<?= $base_url ?>' + data.transaction_data.qr_code;
                document.getElementById('qr-code-modal-image').src = qrCodeSrc;
                document.getElementById('qr-amount').textContent = data.transaction_data.amount;
                document.getElementById('qr-order-id').textContent = data.order_id;
                bankPaymentInfo.style.display = 'block';
            } else {
                bankPaymentInfo.style.display = 'none';
            }

            document.getElementById('orderSuccessModal').style.display = 'block';
            document.body.style.overflow = 'hidden';
            
        } else {
            console.error('Lỗi từ server:', data.message);
            alert('Lỗi: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Lỗi fetch:', error);
        loadingOverlay.style.display = 'none';
        placeOrderBtn.disabled = false;
        alert('Có lỗi xảy ra khi đặt hàng: ' + error.message);
    });
}

// Xử lý modal buttons
document.getElementById('continueShopping')?.addEventListener('click', function() {
    window.location.href = 'index.php';
});

</script>
<?php include __DIR__ . '/templates/footer.php'; ?>
