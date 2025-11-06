<?php

$base_url = "http://" . $_SERVER['HTTP_HOST'] . "/jewelry_website/public/";

include __DIR__ . '/templates/header.php'; 
?>


<link rel="stylesheet" href="../public/assets/css/style_home.css">

<section class="hero-section py-5" style="background-color: #fbefdbff;">
  <div class="container-fluid px-0">
    <div class="row g-0 align-items-center">
      
      <!-- Cột trái: Ảnh -->
      <div class="col-md-6">
        <img src="assets/images/home.jpg" 
             class="img-fluid w-100" 
             alt="Jewelry Model" style="height: 600px; object-fit: cover;">
      </div>

      <!-- Cột phải: Nội dung -->
      <div class="col-md-6 d-flex justify-content-center align-items-center">
        <div class="p-5" style="max-width: 500px; background-color: #fbefdbff;">
          <h2 class="fw-bold mb-4 display-6">Explore Jewelry-Forward Picks</h2>
          <p class="mb-4 text-secondary">
            Shine with timeless grace. 
            Shop timeless jewelry pieces curated just for you!
          </p>
          <a href="index.php?action=list" class="btn btn-outline-dark px-4 py-2">Shop Now</a>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- CATEGORY SECTION -->
<section class="py-5" style="background-color: #fbefdbff;">
    <div class="container text-center">
        <h3 class="fw-bold mb-5">Categories</h3>
        <div class="row g-4 text-center">
            <?php
            if (!empty($categories)) {
                // Ánh xạ tên category với ảnh
                $categoryImages = [
                    'Nhẫn' => 'assets/images/rings_home.jpg',
                    'Bông tai' => 'assets/images/earrings_home.jpg', 
                    'Dây chuyền' => 'assets/images/necklaces_home.jpg',
                    'Vòng tay' => 'assets/images/bracelets_home.jpg'
                ];

                foreach ($categories as $cat) {
                    $categoryName = $cat['name'] ?? 'Unknown';
                    $categoryId = $cat['cate_id'] ?? '';
                    $image = $categoryImages[$categoryName] ?? 'assets/images/products/no-image.jpg';
                    
                    // SỬA: Dùng action=list và truyền category ID
                    $link = 'index.php?action=list&category=' . $categoryId;

                    echo '
                    <div class="col-md-3 col-6">
                        <a href="'.$link.'" class="text-decoration-none">
                            <div class="category-card">
                                <img src="'.$image.'" alt="'.htmlspecialchars($categoryName).'" class="category-image">
                                <h5 class="mt-3 fw-semibold">'.htmlspecialchars($categoryName).'</h5>
                            </div>
                        </a>
                    </div>';
                }
            } else {
                echo '<div class="col-12"><p>No categories found.</p></div>';
            }
            ?>
        </div>
    </div>
</section>

<!-- TRENDING NOW SECTION -->
<section class="py-5" style="background-color: #fbefdbff;"> 
    <div class="container text-center">
        <h3 class="fw-bold mb-5">Trending Now</h3>

        <?php if (!empty($trendingProducts)): ?>
            <div id="trendingCarousel" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">
                    <?php
                    $chunks = array_chunk($trendingProducts, 4);
                    $active = 'active';

                    foreach ($chunks as $group) {
                        echo '<div class="carousel-item '.$active.'"><div class="row justify-content-center g-4">';
                        foreach ($group as $product) {
                            $price = isset($product['price']) ? $product['price'] : 0;
                            $image = $product['image_url'] ?? 'assets/images/products/no-image.jpg';
                            $soldCount = $product['total_sold'] ?? 0;
                            $productId = $product['pro_id'] ?? '';
                            
                            // SỬA: Dùng action=detail và truyền product ID
                            echo '
                            <div class="col-lg-3 col-md-4 col-sm-6">
                                <a href="index.php?action=detail&id='.$productId.'" class="text-decoration-none">
                                    <div class="product-card p-3 h-100">
                                        <img src="'.$image.'" class="product-image img-fluid mb-3" alt="'.$product['name'].'" 
                                             onerror="this.src=\'assets/images/products/no-image.jpg\'">
                                        <h6 class="product-title text-dark">'.$product['name'].'</h6>
                                        <div class="d-flex justify-content-between align-items-center mt-2">
                                            <p class="fw-bold text-primary mb-0">'.number_format($price).' đ</p>';
                            
                            // Hiển thị số lượng đã bán nếu > 0
                            if ($soldCount > 0) {
                                echo '<small class="text-muted">Đã bán: '.$soldCount.'</small>';
                            }
                            
                            echo '
                                        </div>
                                    </div>
                                </a>
                            </div>';
                        }
                        echo '</div></div>';
                        $active = '';
                    }
                    ?>
                </div>

                <!-- Controls -->
                <?php if (count($chunks) > 1): ?>
                <button class="carousel-control-prev" type="button" data-bs-target="#trendingCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon"></span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#trendingCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon"></span>
                </button>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info">
                <p class="mb-0">Chưa có sản phẩm trending. Hãy quay lại sau!</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include __DIR__ . '/templates/footer.php'; ?>
