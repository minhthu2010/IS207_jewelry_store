<?php include __DIR__ . '/templates/header.php'; ?>
<link rel="stylesheet" href="../public/assets/css/style.css">

<main class="product-page py-4">
  <div class="container">
    <!-- Breadcrumb -->
    <nav class="breadcrumb-nav mb-4">
      <a href="#" class="breadcrumb-link">Home</a> &nbsp; › &nbsp;
      <span>All Products</span>
    </nav>

    <h2 class="mb-4">All Products</h2>

    <div class="row">
      <!-- Sidebar -->
      <aside class="col-md-3">
        <h5 class="mb-3">Browse by</h5>
        <ul class="list-unstyled mb-4">
          <li><a href="#">All Products</a></li>
          <li><a href="#">Handbags</a></li>
          <li><a href="#">Hats</a></li>
          <li><a href="#">Jewelry</a></li>
        </ul>

        <h5 class="mb-3">Filter by</h5>
        <div class="filter-section mb-4">
          <label class="form-label">Price</label>
          <input type="range" class="form-range" min="30" max="120" value="75">
          <div class="d-flex justify-content-between">
            <span>$30</span>
            <span>$120</span>
          </div>
        </div>

        <div class="filter-section mb-4">
          <label class="form-label">Color</label>
          <button class="btn btn-sm btn-outline-dark w-100 text-start">+</button>
        </div>

        <div class="filter-section mb-4">
          <label class="form-label">Length (inches)</label>
          <button class="btn btn-sm btn-outline-dark w-100 text-start">+</button>
        </div>
      </aside>

      <!-- Product grid -->
      <section class="col-md-9">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <span>9 products</span>
          <div>
            <label for="sort" class="form-label me-2">Sort by:</label>
            <select id="sort" class="form-select form-select-sm d-inline-block w-auto">
              <option>Recommended</option>
              <option>Price: Low to High</option>
              <option>Price: High to Low</option>
            </select>
          </div>
        </div>

        <div class="row g-4">
  <?php if (!empty($products)): ?>
    <?php foreach ($products as $product): ?>
      <div class="col-md-4">
        <div class="card border-0 bg-transparent">
          <a href="/jewelry_website/public/index.php?action=detail&id=<?= $product['pro_id'] ?>">
            <img 
              src="<?= !empty($product['image_url']) ? htmlspecialchars($product['image_url']) : '/jewelry_website/public/assets/images/no-image.jpg' ?>" 
              class="card-img-top" 
              alt="<?= htmlspecialchars($product['name']) ?>" 
              style="height: 300px; object-fit: cover;"
            >
          </a>
          <div class="card-body text-center">
            <p class="mb-1"><?= htmlspecialchars($product['name']) ?></p>
            <p class="text-muted">$<?= number_format($product['price'], 2) ?></p>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  <?php else: ?>
    <p>Không có sản phẩm nào để hiển thị.</p>
  <?php endif; ?>
</div>




      </section>
    </div>
  </div>
</main>

<?php include __DIR__ . '/templates/footer.php'; ?>
