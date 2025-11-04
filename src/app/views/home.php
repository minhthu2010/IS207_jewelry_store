<?php include __DIR__ . '/templates/header.php'; ?>
<link rel="stylesheet" href="../public/assets/css/style_home.css">
<section class="hero-section py-5" style="background-color: #fbefdbff;">
  <div class="container-fluid px-0">
    <div class="row g-0 align-items-center">
      
      <!-- Cột trái: Ảnh -->
      <div class="col-md-6">
        <img src="assets/images/home.jpg" 
             class="img-fluid w-100" 
             alt="Jewelry Model">
      </div>

      <!-- Cột phải: Nội dung -->
      <div class="col-md-6 d-flex justify-content-center align-items-center">
        <div class="p-5" style="max-width: 500px; background-color: #fbefdbff;">
          <h2 class="fw-bold mb-4 display-6" >Explore Jewelry-Forward Picks</h2>
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
                $categories = [
                    ['name' => 'Nhẫn', 'image' => 'assets/images/rings_home.jpg'],
                    ['name' => 'Bông tai', 'image' => 'assets/images/rings_home.jpg'],
                    ['name' => 'Dây chuyền', 'image' => 'assets/images/necklaces_home.jpg'],
                    ['name' => 'Vòng tay', 'image' => 'assets/images/necklaces_home.jpg'],
                ];

                foreach ($categories as $cat) {
                // tạo đường dẫn có tham số category
                  $link = '?page=product&category=' . strtolower($cat['name']);

                    echo '
                      <div class="col-md-3">
                        <a href="'.$link.'" class="text-decoration-none">
                          <div class="category-card">
                            <img src="'.$cat['image'].'" alt="'.$cat['name'].'">
                            <h5 class="mt-2">'.$cat['name'].'</h5>
                          </div>
                        </a>
                      </div>';
                  }
                ?>
            </div>
        </div>
    </section>

    <!-- TRENDING NOW SECTION -->
    <section class="py-5" style="background-color: #fbefdbff;"> 
        <div class="container text-center">
            <h3 class="fw-bold mb-5">Trending Now</h3>

            <div id="trendingCarousel" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">

                    <?php
                    $products = [
                        ['name' => '14KT Yellow Gold Diamond Hoop Earrings', 'price' => 'Rs. 4,554.00', 'image' => 'assets/images/rings_home.jpg'],
                        ['name' => 'Ruby Drop Earrings', 'price' => 'Rs. 4,554.00', 'image' => 'assets/images/rings_home.jpg'],
                        ['name' => 'Gold Chain Necklace', 'price' => 'Rs. 4,554.00', 'image' => 'assets/images/rings_home.jpg'],
                        ['name' => 'Pearl Pendant', 'price' => 'Rs. 4,554.00', 'image' => 'assets/images/rings_home.jpg'],
                        ['name' => 'Diamond Mangalsutra', 'price' => 'Rs. 4,554.00', 'image' => 'assets/images/rings_home.jpg'],
                        ['name' => 'Rose Gold Earrings', 'price' => 'Rs. 4,554.00', 'image' => 'assets/images/p6.jpg'],
                    ];

                    $chunks = array_chunk($products, 5);
                    $active = 'active';

                    foreach ($chunks as $group) {
                        echo '<div class="carousel-item '.$active.'"><div class="row justify-content-center g-3">';
                        foreach ($group as $p) {
                            echo '
                            <div class="col-md-2">
                                <div class="product-card p-3">
                                    <img src="'.$p['image'].'" class="img-fluid mb-3" alt="'.$p['name'].'">
                                    <h6>'.$p['name'].'</h6>
                                    <p class="fw-bold text-primary">'.$p['price'].'</p>
                                </div>
                            </div>';
                        }
                        echo '</div></div>';
                        $active = '';
                    }
                    ?>
                </div>

                <!-- Controls -->
                <button class="carousel-control-prev" type="button" data-bs-target="#trendingCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon"></span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#trendingCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon"></span>
                </button>
            </div>
        </div>
    </section>

<?php include __DIR__ . '/templates/footer.php'; ?>
