<div id="sidebar" class="sidebar">
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.php">
        <div class="sidebar-brand-icon"><i class="fas fa-gem"></i></div>
        <div class="sidebar-brand-text mx-2">Jewelry Store</div>
    </a>

    <hr class="sidebar-divider">
    <div class="nav-item active">
        <a class="nav-link" href="index.php">
            <i class="fas fa-fw fa-tachometer-alt"></i> <span>Dashboard</span>
        </a>
    </div>

    <hr class="sidebar-divider">
    <div class="sidebar-heading">Quản lý</div>

    
    <div class="nav-item">
        <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseProducts">
            <i class="fas fa-fw fa-gem"></i><span>Sản phẩm</span><i class="fas fa-fw fa-angle-down float-end"></i>
        </a>
        <div id="collapseProducts" class="collapse">
            <div class="collapse-inner">
                <a class="collapse-item" href="products.php">Tất cả sản phẩm</a>
                <a class="collapse-item" href="categories.php">Danh mục sản phẩm</a>
            </div>
        </div>
    </div>

    <div class="nav-item"><a class="nav-link" href="orders.php"><i class="fas fa-fw fa-shopping-cart"></i>Đơn hàng</a></div>
    
    <li class="nav-item">
        <a class="nav-link" href="customers.php">
            <i class="bi bi-people"></i>
            <span>Khách hàng</span>
        </a>
    </li>

</div>
