<?php
require_once __DIR__ . '/includes/functions.php';

$slug = trim($_GET['slug'] ?? '');
$product = $slug !== '' ? getProductBySlug($slug) : null;

$viewerUser = currentUser();
$isOwner = $viewerUser && $product && (int) $product['user_id'] === (int) $viewerUser['id'];

if (!$product || ($product['status'] !== 'approved' && !$isOwner)) {
    http_response_code(404);
    $pageTitle = 'Product Not Found - UseAgain';
    require_once __DIR__ . '/includes/header.php';
    ?>
    <section style="padding:60px 0; text-align:center;">
      <div class="container">
        <h1 class="section-title">Product Not Found</h1>
        <p class="section-sub">This listing may have been removed, sold, or is still awaiting approval.</p>
        <a href="<?= BASE_URL ?>/products.php" class="btn btn-primary">Browse Other Products</a>
      </div>
    </section>
    <?php
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

if ($product['status'] === 'approved' && !$isOwner) {
    incrementProductViews($product['id']);
}

$related = getRelatedProducts($product['category_id'], $product['id'], 4);
$isDonate = $product['listing_type'] === 'donate';
$waNumber = preg_replace('/\D/', '', $product['whatsapp_number']);
$waMessage = rawurlencode('Hi, I\'m interested in "' . $product['title'] . '" listed on UseAgain.');

$pageTitle = $product['title'] . ' - UseAgain';
$pageDesc = mb_substr(strip_tags($product['description']), 0, 160);
require_once __DIR__ . '/includes/header.php';
?>

<section style="padding-top:30px; padding-bottom:80px;">
  <div class="container">
    <?php if ($product['status'] !== 'approved'): ?>
      <div class="alert <?= $product['status'] === 'rejected' ? 'alert-error' : 'alert-success' ?>" style="margin-bottom:24px;">
        <?php if ($product['status'] === 'pending'): ?>
          This is a preview — your listing is still awaiting admin approval.
        <?php elseif ($product['status'] === 'rejected'): ?>
          This listing was rejected<?= $product['rejection_reason'] ? ': ' . htmlspecialchars($product['rejection_reason']) : '.' ?>
        <?php else: ?>
          This listing is currently <?= htmlspecialchars($product['status']) ?> and only visible to you.
        <?php endif; ?>
      </div>
    <?php endif; ?>

    <div class="pd-layout">
      <!-- Gallery -->
      <div class="pd-gallery">
        <?php $images = $product['images'] ?: ['https://placehold.co/700x525/EEF2F0/3A443E?text=No+Image']; ?>
        <div class="pd-main-image">
          <img id="pdMainImage" src="<?= htmlspecialchars($images[0]) ?>" alt="<?= htmlspecialchars($product['title']) ?>">
        </div>
        <?php if (count($images) > 1): ?>
          <div class="pd-thumbs">
            <?php foreach ($images as $i => $img): ?>
              <img src="<?= htmlspecialchars($img) ?>" class="<?= $i === 0 ? 'active' : '' ?>" onclick="document.getElementById('pdMainImage').src=this.src; document.querySelectorAll('.pd-thumbs img').forEach(function(el){el.classList.remove('active')}); this.classList.add('active');">
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>

      <!-- Details -->
      <div class="pd-details">
        <?php if (!empty($product['sold_out'])): ?>
          <span class="badge badge-sold-out" style="position:static; display:inline-block; margin-bottom:10px;">Sold Out</span>
        <?php elseif ($isDonate): ?>
          <span class="badge badge-donate" style="position:static; display:inline-block; margin-bottom:10px;">Donation</span>
        <?php endif; ?>
        <h1><?= htmlspecialchars($product['title']) ?></h1>
        <div class="price" style="font-size:1.6rem; margin:10px 0;"><?= $isDonate ? '🎁 Free — Donation' : htmlspecialchars(formatPrice($product['price']) ?? 'Price on request') ?></div>

        <p class="location" style="margin-bottom:16px;">📍 <?= htmlspecialchars($product['area']) ?>, <?= htmlspecialchars($product['city']) ?>, <?= htmlspecialchars($product['state']) ?>
          &middot; Posted <?= timeAgo($product['created_at']) ?> &middot; <?= (int) $product['views'] ?> views</p>

        <div style="display:flex; gap:8px; flex-wrap:wrap; margin-bottom:20px;">
          <span class="badge-condition" style="position:static;"><?= htmlspecialchars($product['condition_type']) ?></span>
          <span class="cat-tag"><?= htmlspecialchars($product['category_icon']) ?> <?= htmlspecialchars($product['subcategory_name']) ?></span>
        </div>

        <?php if (!empty($product['sold_out'])): ?>
          <div class="alert alert-error" style="margin-bottom:20px;">This item has already been sold / given away and is no longer available.</div>
          <div style="display:flex; flex-direction:column; gap:10px; margin-bottom:20px;">
            <button type="button" class="btn btn-block" disabled style="opacity:0.5; cursor:not-allowed; background:var(--gray-100); color:var(--gray-500); border:1px solid var(--card-border);">💬 Chat on WhatsApp — Sold Out</button>
            <button type="button" class="btn btn-outline btn-block" disabled style="opacity:0.5; cursor:not-allowed;">📞 Call Seller — Sold Out</button>
          </div>
        <?php else: ?>
          <div style="display:flex; flex-direction:column; gap:10px; margin-bottom:20px;">
            <a href="https://wa.me/<?= htmlspecialchars($waNumber) ?>?text=<?= $waMessage ?>" target="_blank" rel="noopener" class="btn btn-whatsapp btn-block">💬 Chat on WhatsApp</a>
            <a href="tel:<?= htmlspecialchars($product['contact_number']) ?>" class="btn btn-outline btn-block">📞 Call Seller</a>
          </div>
        <?php endif; ?>

        <ul class="pd-meta">
          <li><strong>Category</strong><span><?= htmlspecialchars($product['category_name']) ?></span></li>
          <li><strong>Sub Category</strong><span><?= htmlspecialchars($product['subcategory_name']) ?></span></li>
          <li><strong>Condition</strong><span><?= htmlspecialchars($product['condition_type']) ?></span></li>
          <li><strong>Listing Type</strong><span><?= $isDonate ? 'Donation' : 'For Sale' ?></span></li>
        </ul>
      </div>
    </div>

    <div class="form-card" style="margin-top:30px;">
      <h3 style="margin-bottom:10px;">Description</h3>
      <p style="white-space:pre-line; color:var(--gray-700);"><?= htmlspecialchars($product['description']) ?></p>
    </div>

    <?php if ($related): ?>
      <h2 class="section-title" style="margin-top:50px;">Similar Products</h2>
      <div class="product-grid">
        <?php foreach ($related as $p): include __DIR__ . '/includes/product-card.php'; endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
