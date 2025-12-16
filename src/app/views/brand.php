<link rel="stylesheet" href="../public/assets/css/style_brand.css">
<?php
$base_url = "http://" . $_SERVER['HTTP_HOST'] . "/jewelry_website/public/";
include __DIR__ . '/templates/header.php';
?>

<main class="brand-page">
    <div class="link-container">
        <nav class="breadcrumb-nav mb-4">
            <a href="<?= $base_url ?>index.php" class="breadcrumb-link">Trang chủ</a> &nbsp; › &nbsp;
            <span>Thương hiệu</span>
        </nav>
    </div>

    <!-- Hero Section -->
    <section class="hero">
        <div class="wrap">
            <div class="hero-left">
                <div class="img-frame">
                    <img src="<?= $base_url ?>assets/images/rings_home.jpg" alt="Trang sức Lara">
                </div>
            </div>

            <div class="hero-right">
                <h1>Câu chuyện thương hiệu</h1>
                <p class="lead">Lara Jewelry là điểm đến cho những ai trân trọng vẻ đẹp tinh tế và sự sang trọng đẳng cấp.</p>
                <p>
                    Chúng tôi chuyên tạo ra những món trang sức độc đáo được chế tác thủ công bởi các nghệ nhân lành nghề.
                    Mỗi tác phẩm là sự kết hợp hoàn hảo giữa truyền thống và hiện đại, mang đậm dấu ấn cá nhân và sự sáng tạo không ngừng.
                </p>
                <p>
                    Với sứ mệnh bảo tồn nghệ thuật chế tác trang sức, chúng tôi luôn đảm bảo chất lượng và tính thẩm mỹ trong từng chi tiết.
                </p>
            </div>
        </div>
    </section>

    <!-- Values Section -->
    <section class="values">
        <div class="values-wrap">
            <h2>Giá trị cốt lõi</h2>
            <div class="grid">
                <div class="card">
                    <h3>Chất lượng đỉnh cao</h3>
                    <p>Nguyên liệu chọn lọc & kiểm soát chất lượng nghiêm ngặt.</p>
                </div>
                <div class="card">
                    <h3>Chế tác thủ công</h3>
                    <p>Nghệ nhân tay nghề cao tạo nên từng chi tiết.</p>
                </div>
                <div class="card">
                    <h3>Đam mê sáng tạo</h3>
                    <p>Luôn đổi mới để mang đến thiết kế độc đáo.</p>
                </div>
            </div>
        </div>
    </section>
</main>

<?php
include __DIR__ . '/templates/footer.php';
?>
