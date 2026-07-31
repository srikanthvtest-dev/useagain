<?php
require_once __DIR__ . '/includes/functions.php';

if (!isUserLoggedIn()) {
    header('Location: ' . BASE_URL . '/login.php?redirect=' . urlencode('/my-account.php'));
    exit;
}

$user = currentUser();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_verify($_POST['csrf_token'] ?? '')) {
    $productId = (int) ($_POST['product_id'] ?? 0);
    $owned = $productId ? getProductByIdForUser($productId, $user['id']) : null;

    if ($owned) {
        $action = $_POST['action'] ?? '';
        if ($action === 'toggle_sold_out') {
            toggleSoldOut($productId);
        } elseif ($action === 'delete') {
            deleteProductCompletely($productId);
        }
    }
    header('Location: ' . BASE_URL . '/my-account.php#listings');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'mark_notifications_read') {
    markAllNotificationsRead('user', $user['id']);
    header('Location: ' . BASE_URL . '/my-account.php');
    exit;
}

$myProducts = getProductsByUser($user['id']);

$pageTitle = 'My Account - UseAgain';
require_once __DIR__ . '/includes/header.php';

$statusLabels = [
    'pending'            => ['label' => 'Pending Review',   'class' => 'status-pending'],
    'approved'           => ['label' => 'Live',             'class' => 'status-approved'],
    'rejected'           => ['label' => 'Rejected',         'class' => 'status-rejected'],
    'changes_requested'  => ['label' => 'Changes Requested','class' => 'status-pending'],
    'sold'               => ['label' => 'Sold',             'class' => 'status-sold'],
    'removed'            => ['label' => 'Hidden by Admin',  'class' => 'status-removed'],
];
?>

<section style="padding-top:40px; padding-bottom:80px;">
  <div class="container">
    <?php if (($_GET['welcome'] ?? '') === '1'): ?>
      <div class="alert alert-success">🎉 Welcome to UseAgain, <?= htmlspecialchars(explode(' ', $user['name'])[0]) ?>! You're all set to sell or donate your first item.</div>
    <?php endif; ?>
    <?php if (($_GET['posted'] ?? '') === '1'): ?>
      <div class="alert alert-success">✅ Your listing has been submitted and is pending admin approval.</div>
    <?php endif; ?>
    <?php if (($_GET['updated'] ?? '') === '1'): ?>
      <div class="alert alert-success">✅ Your listing has been updated.</div>
    <?php endif; ?>

    <div class="account-grid">
      <div class="account-card">
        <div class="avatar" style="width:64px; height:64px; font-size:1.4rem; margin-bottom:14px;"><?= strtoupper(substr($user['name'], 0, 1)) ?></div>
        <h2 style="margin-bottom:4px;"><?= htmlspecialchars($user['name']) ?></h2>
        <p style="color:var(--gray-500); margin-bottom:20px;">Member since <?= date('M Y', strtotime($user['created_at'])) ?></p>
        <ul class="pd-meta">
          <li><strong>Email</strong><span><?= htmlspecialchars($user['email']) ?></span></li>
          <li><strong>Phone</strong><span><?= htmlspecialchars($user['phone']) ?></span></li>
          <li><strong>WhatsApp</strong><span><?= htmlspecialchars($user['whatsapp_number']) ?></span></li>
        </ul>
        <a href="<?= BASE_URL ?>/post-product.php" class="btn btn-primary btn-block" style="margin-top:20px;">+ Sell / Donate an Item</a>
        <a href="<?= BASE_URL ?>/logout.php" class="btn btn-outline btn-block" style="margin-top:10px;">Logout</a>

        <?php $notifications = getNotifications('user', $user['id'], 5); ?>
        <?php if ($notifications): ?>
          <h4 style="margin-top:26px; margin-bottom:10px;">Recent Activity</h4>
          <ul class="notif-list">
            <?php foreach ($notifications as $n): ?>
              <li class="<?= $n['is_read'] ? '' : 'unread' ?>"><?= htmlspecialchars($n['message']) ?><span class="muted" style="display:block; font-size:0.75rem;"><?= timeAgo($n['created_at']) ?></span></li>
            <?php endforeach; ?>
          </ul>
          <form method="post"><?= csrf_field() ?><input type="hidden" name="action" value="mark_notifications_read"><button type="submit" class="btn btn-outline btn-sm btn-block" style="margin-top:8px;">Mark All Read</button></form>
        <?php endif; ?>
      </div>

      <div id="listings">
        <h2 class="section-title" style="text-align:left;">My Listings (<?= count($myProducts) ?>)</h2>
        <?php if (empty($myProducts)): ?>
          <div class="no-results">
            <h3>You haven't posted anything yet</h3>
            <p>List your first item for sale or donation — it's free.</p>
            <a href="<?= BASE_URL ?>/post-product.php" class="btn btn-primary" style="margin-top:16px;">+ Sell / Donate an Item</a>
          </div>
        <?php else: ?>
          <?php foreach ($myProducts as $p): $st = $statusLabels[$p['status']] ?? ['label' => $p['status'], 'class' => 'status-pending']; ?>
            <div class="admin-card">
              <div class="admin-card-head">
                <div>
                  <span class="status-pill <?= $st['class'] ?>"><?= htmlspecialchars($st['label']) ?></span>
                  <?php if ($p['sold_out']): ?><span class="status-pill status-rejected">Sold Out</span><?php endif; ?>
                  <h3 style="margin-top:8px;"><?= htmlspecialchars($p['title']) ?></h3>
                  <p style="color:var(--gray-500); font-size:0.88rem;">
                    <?= htmlspecialchars($p['category_name']) ?> &raquo; <?= htmlspecialchars($p['subcategory_name']) ?>
                    · <?= $p['listing_type'] === 'donate' ? 'Donation' : htmlspecialchars(formatPrice($p['price']) ?? 'Price on request') ?>
                    · <?= (int) $p['views'] ?> views
                  </p>
                  <?php if (in_array($p['status'], ['rejected', 'changes_requested'], true) && !empty($p['rejection_reason'])): ?>
                    <p style="color:#B42323; font-size:0.85rem; margin-top:6px;">Reason: <?= htmlspecialchars($p['rejection_reason']) ?></p>
                  <?php endif; ?>
                </div>
                <div class="admin-row-actions">
                  <?php if ($p['status'] === 'approved'): ?>
                    <a href="<?= BASE_URL ?>/product-details.php?slug=<?= urlencode($p['slug']) ?>" class="btn btn-outline btn-sm">View</a>
                  <?php endif; ?>
                  <a href="<?= BASE_URL ?>/edit-product.php?id=<?= $p['id'] ?>" class="btn btn-outline btn-sm">Edit</a>
                  <?php if ($p['status'] === 'approved'): ?>
                    <form method="post" style="display:inline;">
                      <?= csrf_field() ?>
                      <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
                      <input type="hidden" name="action" value="toggle_sold_out">
                      <button type="submit" class="btn btn-sm <?= $p['sold_out'] ? 'btn-primary' : 'btn-outline' ?>"><?= $p['sold_out'] ? 'Mark Available' : 'Mark Sold Out' ?></button>
                    </form>
                  <?php endif; ?>
                  <form method="post" style="display:inline;" onsubmit="return confirm('Delete this listing permanently?');">
                    <?= csrf_field() ?>
                    <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
                    <input type="hidden" name="action" value="delete">
                    <button type="submit" class="btn btn-sm btn-outline" style="color:#B42323; border-color:#B42323;">Delete</button>
                  </form>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
