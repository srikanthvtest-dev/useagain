<?php
require_once __DIR__ . '/includes/functions.php';

$filters = [
    'q'              => trim($_GET['q'] ?? ''),
    'category'       => trim($_GET['category'] ?? ''),
    'subcategory'    => trim($_GET['subcategory'] ?? ''),
    'listing_type'   => trim($_GET['listing_type'] ?? ''),
    'condition_type' => trim($_GET['condition_type'] ?? ''),
    'min_price'      => trim($_GET['min_price'] ?? ''),
    'max_price'      => trim($_GET['max_price'] ?? ''),
    'city'           => trim($_GET['city'] ?? ''),
    'sort'           => trim($_GET['sort'] ?? 'newest'),
];

$page = max(1, (int) ($_GET['page'] ?? 1));
$result = getProductsForBrowse($filters, $page, PRODUCTS_PER_PAGE);
$products = $result['products'];
$total = $result['total'];
$totalPages = max(1, (int) ceil($total / PRODUCTS_PER_PAGE));

$categories = getCategories();
$activeCategory = $filters['category'] ? getCategoryBySlug($filters['category']) : null;
$subcategories = $activeCategory ? getSubcategories($activeCategory['id']) : [];

$pageTitle = ($filters['q'] !== '' ? '"' . $filters['q'] . '" — ' : '') . 'Browse Products - UseAgain';
require_once __DIR__ . '/includes/header.php';

function buildBrowseLink(array $overrides) {
    $params = array_merge($_GET, $overrides);
    unset($params['page']);
    return BASE_URL . '/products.php?' . http_build_query($params);
}
?>

<section style="padding-top:30px; padding-bottom:80px;">
  <div class="container">
    <div class="browse-layout">
      <!-- ================= FILTER SIDEBAR ================= -->
      <aside class="filters-panel">
        <form method="get" action="<?= BASE_URL ?>/products.php">
          <?php if ($filters['q']): ?><input type="hidden" name="q" value="<?= htmlspecialchars($filters['q']) ?>"><?php endif; ?>
          <h3>Filters</h3>

          <div class="form-group">
            <label for="f_category">Category</label>
            <select id="f_category" name="category" onchange="this.form.submit()">
              <option value="">All Categories</option>
              <?php foreach ($categories as $cat): ?>
                <option value="<?= htmlspecialchars($cat['slug']) ?>" <?= $filters['category'] === $cat['slug'] ? 'selected' : '' ?>><?= $cat['icon'] ?> <?= htmlspecialchars($cat['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <?php if ($subcategories): ?>
            <div class="form-group">
              <label for="f_subcategory">Sub Category</label>
              <select id="f_subcategory" name="subcategory">
                <option value="">All</option>
                <?php foreach ($subcategories as $sub): ?>
                  <option value="<?= htmlspecialchars($sub['slug']) ?>" <?= $filters['subcategory'] === $sub['slug'] ? 'selected' : '' ?>><?= htmlspecialchars($sub['name']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          <?php endif; ?>

          <div class="form-group">
            <label for="f_listing_type">Listing Type</label>
            <select id="f_listing_type" name="listing_type">
              <option value="">Sell &amp; Donate</option>
              <option value="sell" <?= $filters['listing_type'] === 'sell' ? 'selected' : '' ?>>For Sale</option>
              <option value="donate" <?= $filters['listing_type'] === 'donate' ? 'selected' : '' ?>>Donations</option>
            </select>
          </div>

          <div class="form-group">
            <label for="f_condition">Condition</label>
            <select id="f_condition" name="condition_type">
              <option value="">Any</option>
              <?php foreach (['New', 'Like New', 'Good', 'Fair'] as $c): ?>
                <option value="<?= $c ?>" <?= $filters['condition_type'] === $c ? 'selected' : '' ?>><?= $c ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label for="f_min">Min Price</label>
              <input type="number" id="f_min" name="min_price" value="<?= htmlspecialchars($filters['min_price']) ?>">
            </div>
            <div class="form-group">
              <label for="f_max">Max Price</label>
              <input type="number" id="f_max" name="max_price" value="<?= htmlspecialchars($filters['max_price']) ?>">
            </div>
          </div>

          <div class="form-group">
            <label for="f_city">City</label>
            <input type="text" id="f_city" name="city" value="<?= htmlspecialchars($filters['city']) ?>">
          </div>

          <button type="submit" class="btn btn-primary btn-block">Apply Filters</button>
          <a href="<?= BASE_URL ?>/products.php" class="btn btn-outline btn-block" style="margin-top:8px;">Clear Filters</a>
        </form>
      </aside>

      <!-- ================= RESULTS ================= -->
      <div>
        <div class="browse-toolbar">
          <span><?= number_format($total) ?> result<?= $total === 1 ? '' : 's' ?><?= $filters['q'] ? ' for "' . htmlspecialchars($filters['q']) . '"' : '' ?></span>
          <form method="get" action="<?= BASE_URL ?>/products.php" class="sort-form">
            <?php foreach ($_GET as $k => $v): if ($k === 'sort' || $k === 'page') continue; ?>
              <input type="hidden" name="<?= htmlspecialchars($k) ?>" value="<?= htmlspecialchars($v) ?>">
            <?php endforeach; ?>
            <label for="sort" class="small-muted">Sort by</label>
            <select id="sort" name="sort" onchange="this.form.submit()">
              <option value="newest" <?= $filters['sort'] === 'newest' ? 'selected' : '' ?>>Newest First</option>
              <option value="price_low" <?= $filters['sort'] === 'price_low' ? 'selected' : '' ?>>Price: Low to High</option>
              <option value="price_high" <?= $filters['sort'] === 'price_high' ? 'selected' : '' ?>>Price: High to Low</option>
              <option value="most_viewed" <?= $filters['sort'] === 'most_viewed' ? 'selected' : '' ?>>Most Viewed</option>
            </select>
          </form>
        </div>

        <?php if (empty($products)): ?>
          <div class="no-results">
            <h3>No products matched your filters</h3>
            <p>Try widening your search, or check back soon — new ads are added all the time.</p>
            <a href="<?= BASE_URL ?>/products.php" class="btn btn-outline" style="margin-top:16px;">Clear Filters</a>
          </div>
        <?php else: ?>
          <div class="product-grid">
            <?php foreach ($products as $p): include __DIR__ . '/includes/product-card.php'; endforeach; ?>
          </div>

          <?php if ($totalPages > 1): ?>
            <div class="pagination">
              <?php if ($page > 1): ?><a href="<?= buildBrowseLink(['page' => $page - 1]) ?>" class="btn btn-outline btn-sm">&larr; Prev</a><?php endif; ?>
              <span class="small-muted">Page <?= $page ?> of <?= $totalPages ?></span>
              <?php if ($page < $totalPages): ?><a href="<?= buildBrowseLink(['page' => $page + 1]) ?>" class="btn btn-outline btn-sm">Next &rarr;</a><?php endif; ?>
            </div>
          <?php endif; ?>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
