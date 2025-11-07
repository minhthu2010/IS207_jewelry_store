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
            <table class="table table-bordered table-hover">
                <thead class="table-primary">
                    <tr>
                        <th>ID</th>
                        <th>Họ tên</th>
                        <th>Email</th>
                        <th>Điện thoại</th>
                        <th>Địa chỉ</th>
                        <th>Số đơn hàng</th>
                        <th>Ngày tạo</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($customers)) : ?>
                        <?php foreach ($customers as $cus) : ?>
                            <tr>
                                <td><?= $cus['cus_id'] ?></td>
                                <td><?= htmlspecialchars($cus['fullname']) ?></td>
                                <td><?= htmlspecialchars($cus['email']) ?></td>
                                <td><?= htmlspecialchars($cus['phone']) ?></td>
                                <td><?= htmlspecialchars($cus['address']) ?></td>
                                <td><?= $cus['order_count'] ?></td>
                                <td><?= $cus['created_at'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted">Không có khách hàng nào</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . '/templates/footer.php'; ?>
