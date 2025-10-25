// Xử lý chọn phương thức thanh toán
document.querySelectorAll('.payment-method').forEach(method => {
    method.addEventListener('click', function() {
        document.querySelectorAll('.payment-method').forEach(m => {
            m.classList.remove('selected');
        });
        this.classList.add('selected');
        const radio = this.querySelector('input[type="radio"]');
        radio.checked = true;
    });
});

// Xử lý đặt hàng
document.getElementById('place-order').addEventListener('click', function() {
    const form = document.getElementById('checkout-form');
    if (form.checkValidity()) {
        // Hiển thị modal thành công
        document.getElementById('orderSuccessModal').style.display = 'block';
        // Ngăn scroll khi modal hiển thị
        document.body.style.overflow = 'hidden';
    } else {
        alert('Vui lòng điền đầy đủ thông tin bắt buộc!');
        // Highlight các trường bắt buộc chưa điền
        const requiredFields = form.querySelectorAll('input[required]');
        requiredFields.forEach(field => {
            if (!field.value) {
                field.style.borderColor = 'red';
            }
        });
    }
});

// Xử lý modal
document.getElementById('continueShopping').addEventListener('click', function() {
    window.location.href = '/jewelry_website';
});

document.getElementById('viewOrderDetail').addEventListener('click', function() {
    window.location.href = '/jewelry_website/account';
});

// Đóng modal khi click bên ngoài
window.addEventListener('click', function(event) {
    const modal = document.getElementById('orderSuccessModal');
    if (event.target === modal) {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto'; // Khôi phục scroll
    }
});

// Đóng modal bằng ESC key
document.addEventListener('keydown', function(event) {
    const modal = document.getElementById('orderSuccessModal');
    if (event.key === 'Escape' && modal.style.display === 'block') {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }
});

// Reset border color khi người dùng bắt đầu nhập
document.querySelectorAll('input[required]').forEach(input => {
    input.addEventListener('input', function() {
        if (this.value) {
            this.style.borderColor = '#ddd';
        }
    });
});