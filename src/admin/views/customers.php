<?php
include __DIR__ . '/templates/header.php';
include __DIR__ . '/templates/sidebar.php';
include __DIR__ . '/templates/topbar.php';
?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Quản lý khách hàng</h1>

    <form method="GET" class="row g-3 mb-3">
        <div class="col-md-4">
            <input type="text" name="search" class="form-control" placeholder="Tìm theo tên, SĐT hoặc địa chỉ" 
                   value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
        </div>
        <div class="col-md-3">
            <input type="number" name="order_count" class="form-control" placeholder="Lọc theo số đơn hàng" 
                   value="<?= htmlspecialchars($_GET['order_count'] ?? '') ?>">
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary w-100">Tìm kiếm</button>
        </div>
        <div class="col-md-2">
            <a href="customers.php" class="btn btn-secondary w-100">Làm mới</a>
        </div>
    </form>

    <div class="card shadow">
        <div class="card-body">
            <table class="table table-bordered table-hover" id="customersTable">
                <thead class="table-primary">
                    <tr>
                        <th>ID</th>
                        <th>Họ tên</th>
                        <th>Email</th>
                        <th>Điện thoại</th>
                        <th>Địa chỉ</th>
                        <th>Số đơn hàng</th>
                        <th>Ngày tạo</th>
                        <th>Trạng thái</th> 
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($customers)) : ?>
                        <?php foreach ($customers as $cus) : ?>
                            <tr class="customer-row" 
                                data-id="<?= $cus['cus_id'] ?>" 
                                data-name="<?= htmlspecialchars($cus['fullname']) ?>"
                                style="cursor: pointer;">
                                <td><?= $cus['cus_id'] ?></td>
                                <td><?= htmlspecialchars($cus['fullname']) ?></td>
                                <td><?= htmlspecialchars($cus['email']) ?></td>
                                <td><?= htmlspecialchars($cus['phone']) ?></td>
                                <td><?= htmlspecialchars($cus['address']) ?></td>
                                <td><?= $cus['order_count'] ?></td>
                                <td><?= $cus['created_at'] ?></td>
                                <td>
                                    <?php if ($cus['status'] == 1): ?>
                                        <button class="btn btn-sm btn-success toggle-status" data-id="<?= $cus['cus_id'] ?>" data-status="1">
                                            Đang hoạt động
                                        </button>
                                    <?php else: ?>
                                        <button class="btn btn-sm btn-secondary toggle-status" data-id="<?= $cus['cus_id'] ?>" data-status="0">
                                            Đã khóa
                                        </button>
                                    <?php endif; ?>
                                </td>

                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted">Không có khách hàng nào</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- MODAL: Danh sách đơn hàng của khách hàng -->
<div class="modal fade" id="ordersModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Đơn hàng của <span id="customerName"></span></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <table class="table table-bordered" id="ordersTable">
          <thead>
            <tr>
              <th>Mã đơn</th>
              <th>Ngày đặt</th>
              <th>Tổng tiền</th>
              <th>Trạng thái</th>
              <th>Thanh toán</th>
              <th>Chi tiết</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- MODAL: Chi tiết đơn hàng -->
<div class="modal fade" id="orderDetailModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Chi tiết đơn hàng <span id="orderId"></span></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" id="orderDetailBody">
        <!-- nội dung chi tiết đơn hàng load bằng JS -->
      </div>
    </div>
  </div>
</div>

<script>

    
    document.addEventListener("DOMContentLoaded", function() {
        console.log("DOM đã tải xong - JavaScript đang hoạt động");

        // Hàm helper
        function getOrderStatus(s) {
            const map = {0:"Chờ xác nhận",1:"Đã xác nhận",2:"Đang giao",3:"Hoàn thành",4:"Đã hủy"};
            return map[s] || "Không xác định";
        }

        function getPaymentStatus(s) {
            if(s==="success") return "<span class='text-success'>Đã thanh toán</span>";
            if(s==="pending") return "<span class='text-warning'>Chờ thanh toán</span>";
            return "<span class='text-danger'>Thất bại</span>";
        }

        // Load chi tiết đơn hàng
        async function loadOrderDetail(orderId){
            try{
                console.log("Đang tải chi tiết đơn hàng:", orderId);
                const res = await fetch('/jewelry_website/admin/controllers/getOrderDetailController.php?order_id=' + orderId);

                const data = await res.json();
                if(!data.success) return alert(data.message);
                const o = data.order;
                const items = data.items.map(i =>
                    `<tr><td>${i.product_name} ${i.size||''}</td><td>${i.quantity}</td><td>${parseFloat(i.price_at_purchase).toLocaleString()}₫</td><td>${parseFloat(i.total_item).toLocaleString()}₫</td></tr>`
                ).join("");

                document.getElementById("orderDetailBody").innerHTML = `
                    <p><strong>Ngày đặt:</strong> ${new Date(o.order_date).toLocaleDateString('vi-VN')}</p>
                    <p><strong>Trạng thái:</strong> ${getOrderStatus(o.status)}</p>
                    <p><strong>Phương thức thanh toán:</strong> ${o.payment_method}</p>
                    <hr>
                    <table class="table table-bordered">
                    <thead><tr><th>Sản phẩm</th><th>Số lượng</th><th>Đơn giá</th><th>Thành tiền</th></tr></thead>
                    <tbody>${items}</tbody>
                    </table>
                    <p class="text-end"><strong>Tổng cộng: ${parseFloat(o.total).toLocaleString()}₫</strong></p>
                `;
                
                // Hiển thị modal chi tiết đơn hàng
                const orderDetailModal = new bootstrap.Modal(document.getElementById('orderDetailModal'));
                orderDetailModal.show();
            }catch(err){
                console.error("Lỗi khi tải chi tiết đơn hàng:", err);
            }
        }

        // Gắn sự kiện click cho từng dòng khách hàng - CHỈ MỘT LẦN
        document.querySelectorAll('.customer-row').forEach(row => {
            row.addEventListener('click', function(e) {
                // Ngăn chặn khi click vào các nút hành động
                if (e.target.closest('.delete-customer')) {
                    return;
                }
                
                const customerId = this.dataset.id;
                const customerName = this.dataset.name;
                console.log("Click vào khách hàng:", customerId, customerName);
                
                loadCustomerOrders(customerId, customerName);
            });

            // Hiệu ứng hover
            row.style.cursor = 'pointer';
            row.addEventListener('mouseenter', function() {
                this.style.backgroundColor = '#f8f9fa';
            });
            row.addEventListener('mouseleave', function() {
                this.style.backgroundColor = '';
            });
        });

        // Hàm tải đơn hàng của khách hàng
        async function loadCustomerOrders(customerId, customerName) {
            document.getElementById("customerName").textContent = customerName;

            try {
                console.log("Đang gọi API với customer_id:", customerId);
                const res = await fetch('/jewelry_website/admin/controllers/getCustomerOrdersController.php?customer_id=' + customerId);

                const data = await res.json();
                console.log("Kết quả API:", data);
                
                const tbody = document.querySelector("#ordersTable tbody");
                tbody.innerHTML = "";

                if (!data.success || data.orders.length === 0) {
                    tbody.innerHTML = "<tr><td colspan='6' class='text-center text-muted'>Không có đơn hàng</td></tr>";
                } else {
                    data.orders.forEach(o => {
                        const tr = document.createElement("tr");
                        tr.innerHTML = `
                            <td>#${o.order_id}</td>
                            <td>${new Date(o.order_date).toLocaleDateString('vi-VN')}</td>
                            <td>${parseFloat(o.total).toLocaleString()}₫</td>
                            <td>${getOrderStatus(o.status)}</td>
                            <td>${getPaymentStatus(o.payment_status)}</td>
                            <td><button class="btn btn-sm btn-primary view-detail" data-id="${o.order_id}">Xem</button></td>
                        `;
                        tbody.appendChild(tr);
                    });

                    // Thêm sự kiện cho nút xem chi tiết
                    tbody.querySelectorAll('.view-detail').forEach(btn => {
                        btn.addEventListener('click', (e) => {
                            e.stopPropagation();
                            console.log("Click vào nút xem chi tiết:", btn.dataset.id);
                            loadOrderDetail(btn.dataset.id);
                        });
                    });
                }

                // Hiển thị modal
                const ordersModal = new bootstrap.Modal(document.getElementById('ordersModal'));
                ordersModal.show();
                console.log("Modal đã hiển thị");

            } catch (err) {
                console.error("Lỗi khi tải đơn hàng:", err);
                alert("Có lỗi xảy ra khi tải đơn hàng");
            }
        }

        // Thay đổi trạng thái khách hàng
        document.querySelectorAll('.toggle-status').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            e.preventDefault();
            
            const cusId = this.dataset.id;
            const currentStatus = this.dataset.status;

            const confirmText = currentStatus === "1" 
                ? "Bạn có chắc chắn muốn khóa tài khoản này không?"
                : "Bạn có chắc chắn muốn kích hoạt lại tài khoản này không?";

            Swal.fire({
                title: 'Xác nhận',
                text: confirmText,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Đồng ý',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('/jewelry_website/admin/customers.php?action=toggle_status&cus_id=' + cusId, { 
                        method: "GET" 
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('Thành công!', data.message, 'success').then(() => location.reload());
                        } else {
                            Swal.fire('Lỗi!', data.message, 'error');
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        Swal.fire('Lỗi!', 'Có lỗi xảy ra khi cập nhật trạng thái', 'error');
                    });
                }
            });
        });
    });

    });


</script>
<?php include __DIR__ . '/templates/footer.php'; ?>
