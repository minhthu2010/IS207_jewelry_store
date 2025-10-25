<?php include __DIR__ . '/templates/header.php'; ?>
<link rel="stylesheet" href="/jewelry_website/public/assets/css/style.css">

<div class="container py-5 product-container">
  <div class="row g-5">
    <!-- Ảnh sản phẩm -->
    <div class="col-md-6 text-center">
      <?php if (!empty($product['images'])): ?>
        <img 
          src="<?= htmlspecialchars($product['images'][0]) ?>" 
          class="img-fluid border product-image mb-3" 
          alt="<?= htmlspecialchars($product['name']) ?>"
          style="max-height: 400px; object-fit: cover;"
        >
        <div class="d-flex justify-content-center gap-2 flex-wrap">
          <?php foreach ($product['images'] as $img): ?>
            <img 
              src="<?= htmlspecialchars($img) ?>" 
              width="80" 
              height="80" 
              class="border rounded" 
              style="object-fit: cover;"
            >
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <img 
          src="/jewelry_website/public/assets/images/no-image.jpg" 
          class="img-fluid border product-image" 
          alt="No image"
          style="max-height: 400px; object-fit: cover;"
        >
      <?php endif; ?>
    </div>

    <!-- Thông tin sản phẩm -->
    <div class="col-md-6 product-info">
      <p class="mb-2">
        <a href="/jewelry_website/public/index.php">Home</a> /
        <a href="/jewelry_website/public/index.php?action=list">All Products</a> /
        <span><?= htmlspecialchars($product['name']) ?></span>
      </p>

      <h3 class="mb-3"><?= htmlspecialchars($product['name']) ?></h3>

      <h4 class="text-muted mb-3">
        $<?= number_format($product['price'], 2) ?>
      </h4>

      <p class="mb-4"><?= nl2br(htmlspecialchars($product['description'])) ?></p>

      <?php if (!empty($product['variants'])): ?>
        <div class="mb-3">
          <label class="form-label fw-bold">Size:</label>
          <select class="form-select w-auto d-inline-block">
            <?php foreach ($product['variants'] as $v): ?>
              <option>
                <?= htmlspecialchars($v['size'] ?: 'Default') ?> - $<?= number_format($v['price'], 2) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
      <?php endif; ?>

      <button class="btn btn-outline-dark px-4 py-2 mt-3 add-to-cart">Add to Cart</button>
    </div>
  </div>

  <!-- Reviews -->
  <hr class="my-5">
  <div class="row">
    <div class="col-md-8 mx-auto">
      <h4 class="mb-4 text-center">Customer Reviews</h4>
      <?php if (!empty($product['reviews'])): ?>
        <?php foreach ($product['reviews'] as $r): ?>
          <div class="review-box mb-4 p-3 border rounded">
            <strong><?= htmlspecialchars($r['fullname']) ?></strong>
            <small class="text-muted float-end"><?= date("M d, Y", strtotime($r['created_at'])) ?></small>
            <div class="text-warning mb-2">
              <?php for ($i = 1; $i <= 5; $i++): ?>
                <i class="<?= $i <= $r['rating'] ? 'fas' : 'far' ?> fa-star"></i>
              <?php endfor; ?>
            </div>
            <p><?= htmlspecialchars($r['comment']) ?></p>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p class="text-muted text-center">Chưa có đánh giá nào cho sản phẩm này.</p>
      <?php endif; ?>
    </div>
  </div>
</div>

<?php include __DIR__ . '/templates/footer.php'; ?>
