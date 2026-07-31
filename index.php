<?php
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'UseAgain - ' . getSetting('site_tagline', 'Buy, Sell and Donate Reusable Products');
$pageDesc  = 'UseAgain is a marketplace to buy, sell and donate reusable products — toys, furniture, books, vehicles, electronics and more. List for free, connect on WhatsApp.';
require_once __DIR__ . '/includes/header.php';

$categories = getCategories();
$featured   = getFeaturedProducts(8);
$latest     = getLatestProducts(8);
$testimonials = getTestimonials(6);
$totalProducts = countApprovedProducts();

$heroTitle = getSetting('homepage_banner_title', '');
$heroSubtitle = getSetting('homepage_banner_subtitle', '');
?>

<!-- ================= HERO ================= -->
<section class="hero">
  <div class="container hero-grid">
    <div class="hero-copy">
      <?php if ($heroTitle): ?>
        <h1><?= htmlspecialchars($heroTitle) ?></h1>
      <?php else: ?>
        <h1>Buy, sell or <span>donate</span> — give it a second life.</h1>
      <?php endif; ?>
      <p class="lead"><?= htmlspecialchars($heroSubtitle ?: 'UseAgain is a community marketplace to buy, sell and donate reusable products. List for free, browse thousands of items, and connect with buyers or sellers directly on WhatsApp.') ?></p>

      <form class="hero-search" method="get" action="<?= BASE_URL ?>/products.php">
        <input type="text" name="q" placeholder="Search for toys, furniture, books, vehicles...">
        <button type="submit">Search</button>
      </form>

      <div class="hero-actions">
        <a href="<?= BASE_URL ?>/post-product.php" class="btn btn-primary">+ Sell / Donate an Item</a>
        <a href="<?= BASE_URL ?>/products.php" class="btn btn-outline">Browse Products</a>
      </div>

      <div class="hero-stats">
        <div><strong><?= $totalProducts ?>+</strong><span>Items Listed</span></div>
        <div><strong><?= count($categories) ?></strong><span>Categories</span></div>
        <div><strong>100%</strong><span>Free to List</span></div>
      </div>
    </div>
    <div class="hero-visual">
      <img src="<?= BASE_URL ?>/assets/images/hero-illustration.svg" alt="UseAgain — buy, sell, donate and reuse marketplace">
    </div>
  </div>
</section>

<!-- ================= CATEGORY CARDS ================= -->
<section>
  <div class="container">
    <h2 class="section-title">Browse Categories</h2>
    <p class="section-sub">Find exactly what you're looking for, or list something you no longer need.</p>
    <div class="cat-grid">
      <?php foreach ($categories as $cat): ?>
        <a href="<?= BASE_URL ?>/products.php?category=<?= htmlspecialchars($cat['slug']) ?>" class="cat-card" style="--cat-color: var(--green);">
          <div class="cat-icon" style="background: var(--green-light);"><?= $cat['icon'] ?></div>
          <h4><?= htmlspecialchars($cat['name']) ?></h4>
          <span><?= countProductsInCategory($cat['id']) ?> items</span>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ================= FEATURED PRODUCTS ================= -->
<section class="alt-bg">
  <div class="container">
    <h2 class="section-title">Featured Products</h2>
    <p class="section-sub">Hand-picked and recently listed items worth a look.</p>
    <?php if (empty($featured)): ?>
      <div class="no-results">
        <h3>No products yet</h3>
        <p>Once listings are posted and approved, they'll appear here.</p>
      </div>
    <?php else: ?>
      <div class="product-grid">
        <?php foreach ($featured as $p): include __DIR__ . '/includes/product-card.php'; endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</section>

<!-- ================= LATEST PRODUCTS ================= -->
<section>
  <div class="container">
    <h2 class="section-title">Latest Products</h2>
    <p class="section-sub">Freshly listed — updated the moment a new ad is approved.</p>
    <?php if (empty($latest)): ?>
      <div class="no-results">
        <h3>Nothing here yet</h3>
        <p>Be the first to post a free ad on UseAgain.</p>
      </div>
    <?php else: ?>
      <div class="product-grid">
        <?php foreach ($latest as $p): include __DIR__ . '/includes/product-card.php'; endforeach; ?>
      </div>
      <div style="text-align:center; margin-top:36px;">
        <a href="<?= BASE_URL ?>/products.php" class="btn btn-outline">View All Products</a>
      </div>
    <?php endif; ?>
  </div>
</section>

<!-- ================= STATISTICS ================= -->
<section class="alt-bg">
  <div class="container">
    <div class="stats-band">
      <div class="stat-item">
        <div class="stat-icon" style="background:var(--green-light); color:var(--green-dark);">📦</div>
        <strong><?= $totalProducts ?>+</strong>
        <span>Items Listed</span>
      </div>
      <div class="stat-item">
        <div class="stat-icon" style="background:var(--blue-light); color:var(--blue-dark);">🗂️</div>
        <strong><?= count($categories) ?></strong>
        <span>Categories</span>
      </div>
      <div class="stat-item">
        <div class="stat-icon" style="background:var(--green-light); color:var(--green-dark);">🎁</div>
        <strong>Free</strong>
        <span>Donations Welcome</span>
      </div>
      <div class="stat-item">
        <div class="stat-icon" style="background:var(--blue-light); color:var(--blue-dark);">💬</div>
        <strong>Direct</strong>
        <span>WhatsApp Contact</span>
      </div>
    </div>
  </div>
</section>

<!-- ================= HOW IT WORKS ================= -->
<section>
  <div class="container">
    <h2 class="section-title">How It Works</h2>
    <p class="section-sub">Buying, selling or donating on UseAgain takes just a few minutes.</p>
    <div class="how-grid">
      <div class="how-step">
        <div class="num">1</div>
        <h4>Post Your Ad</h4>
        <p>Register, then list your item for sale or donation with photos and details — completely free.</p>
      </div>
      <div class="how-step">
        <div class="num">2</div>
        <h4>Get Approved</h4>
        <p>Our team quickly reviews each listing before it goes live, keeping the marketplace trustworthy.</p>
      </div>
      <div class="how-step">
        <div class="num">3</div>
        <h4>Connect on WhatsApp</h4>
        <p>Interested buyers message you directly on WhatsApp — no middlemen, no waiting around.</p>
      </div>
      <div class="how-step">
        <div class="num">4</div>
        <h4>Meet & Exchange</h4>
        <p>Agree on details, meet safely, and give your item a second life instead of the landfill.</p>
      </div>
    </div>
  </div>
</section>

<!-- ================= TESTIMONIALS ================= -->
<?php if (!empty($testimonials)): ?>
<section class="alt-bg">
  <div class="container">
    <h2 class="section-title">Customer Testimonials</h2>
    <p class="section-sub">What people are saying about buying, selling and donating on UseAgain.</p>
    <div class="testimonial-grid">
      <?php foreach ($testimonials as $t): ?>
        <div class="testimonial-card">
          <div class="stars"><?= str_repeat('★', max(0, min(5, (int)$t['rating']))) ?></div>
          <p class="quote">&ldquo;<?= htmlspecialchars($t['message']) ?>&rdquo;</p>
          <div class="testimonial-person">
            <div class="avatar"><?= strtoupper(substr($t['name'], 0, 1)) ?></div>
            <div>
              <strong><?= htmlspecialchars($t['name']) ?></strong>
              <span><?= htmlspecialchars($t['location']) ?></span>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- ================= DONATE BANNER ================= -->
<section>
  <div class="container">
    <div class="donate-banner">
      <h2>Have something to give away?</h2>
      <p>Toys, books, furniture, clothing or anything still in usable condition can make a real difference to someone else. List it as a donation — completely free.</p>
      <div style="display:flex; gap:14px; justify-content:center; flex-wrap:wrap;">
        <a href="<?= BASE_URL ?>/post-product.php" class="btn btn-primary">Donate an Item</a>
        <a href="https://wa.me/<?= htmlspecialchars(getSetting('whatsapp_number', WHATSAPP_NUMBER)) ?>?text=<?= rawurlencode('Hi, I would like to donate an item on UseAgain.') ?>" target="_blank" rel="noopener" class="btn btn-whatsapp">💬 WhatsApp Us</a>
      </div>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
