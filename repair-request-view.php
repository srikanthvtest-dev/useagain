<?php
require_once __DIR__ . '/includes/functions.php';

if (!isUserLoggedIn()) {
    header('Location: ' . BASE_URL . '/login.php?redirect=' . urlencode('/repair-request-view.php?id=' . (int) ($_GET['id'] ?? 0)));
    exit;
}

$user = currentUser();
$requestId = (int) ($_GET['id'] ?? 0);
$request = $requestId ? getRepairRequestById($requestId) : null;

// Owner-only — never let one user view another user's repair request.
if (!$request || (int) $request['user_id'] !== (int) $user['id']) {
    header('Location: ' . BASE_URL . '/my-account.php#repair-requests');
    exit;
}

$problems = getRepairRequestProblems($requestId);
$customerImages = getRepairRequestImages($requestId, 'customer');
$beforeImages = getRepairRequestImages($requestId, 'before');
$afterImages = getRepairRequestImages($requestId, 'after');
$history = getRepairStatusHistory($requestId);

$repairStatusLabels = [
    'submitted' => ['label' => 'Submitted — finding a technician', 'class' => 'status-pending'],
    'assigned' => ['label' => 'Technician Assigned', 'class' => 'status-pending'],
    'accepted' => ['label' => 'Accepted by Technician', 'class' => 'status-approved'],
    'rejected_by_technician' => ['label' => 'Finding another technician', 'class' => 'status-pending'],
    'inspection' => ['label' => 'Inspection Scheduled', 'class' => 'status-approved'],
    'in_progress' => ['label' => 'Repair In Progress', 'class' => 'status-approved'],
    'waiting_for_parts' => ['label' => 'Waiting for Parts', 'class' => 'status-pending'],
    'completed' => ['label' => 'Completed', 'class' => 'status-sold'],
    'cancelled' => ['label' => 'Cancelled', 'class' => 'status-removed'],
];
$st = $repairStatusLabels[$request['status']] ?? ['label' => $request['status'], 'class' => 'status-pending'];

$pageTitle = 'Repair Request - UseAgain';
require_once __DIR__ . '/includes/header.php';
?>

<section style="padding-top:30px; padding-bottom:80px;">
  <div class="container" style="max-width:760px;">
    <a href="<?= BASE_URL ?>/my-account.php#repair-requests" class="small">&larr; Back to My Repair Requests</a>

    <div class="form-card" style="margin-top:16px;">
      <?php if (($_GET['updated'] ?? '') === '1'): ?>
        <div class="alert alert-success" style="margin-bottom:16px;">✅ Your repair request has been updated.</div>
      <?php endif; ?>
      <span class="status-pill <?= $st['class'] ?>"><?= htmlspecialchars($st['label']) ?></span>
      <h1 style="margin-top:10px;"><?= $request['category_icon'] ?> <?= htmlspecialchars($request['title']) ?></h1>
      <p class="muted"><?= htmlspecialchars($request['category_name']) ?><?= $request['subcategory_name'] ? ' &raquo; ' . htmlspecialchars($request['subcategory_name']) : '' ?> · Submitted <?= timeAgo($request['created_at']) ?></p>

      <?php if ($request['status'] === 'submitted'): ?>
        <div class="alert alert-success" style="margin-top:16px;">✏️ You can still edit this request — no technician has been assigned yet.</div>
        <a href="<?= BASE_URL ?>/repair-request-edit.php?id=<?= $requestId ?>" class="btn btn-primary" style="margin-top:8px;">Edit Request</a>
      <?php else: ?>
        <div class="repair-estimate-note" style="margin-top:16px;">🔒 This request is now read-only — a technician has been assigned, so editing is disabled.</div>
      <?php endif; ?>

      <?php if ($problems): ?>
        <div style="margin-top:20px;">
          <strong>Reported Problems:</strong>
          <ul style="margin:8px 0 0 20px;">
            <?php foreach ($problems as $p): ?><li><?= htmlspecialchars($p['title']) ?></li><?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <?php if ($request['description']): ?>
        <p style="white-space:pre-line; margin-top:16px;"><strong>Additional Description:</strong> <?= htmlspecialchars($request['description']) ?></p>
      <?php endif; ?>

      <table class="admin-table" style="margin-top:20px;">
        <tr><th>Location</th><td><?= htmlspecialchars($request['address']) ?>, <?= htmlspecialchars($request['area']) ?>, <?= htmlspecialchars($request['city']) ?>, <?= htmlspecialchars($request['state']) ?> - <?= htmlspecialchars($request['pincode']) ?></td></tr>
        <tr><th>Contact</th><td><?= htmlspecialchars($request['contact_number']) ?> &middot; WhatsApp: <?= htmlspecialchars($request['whatsapp_number']) ?></td></tr>
        <tr><th>Technician</th><td><?= $request['technician_shop_name'] ? htmlspecialchars($request['technician_shop_name']) : '<span class="muted">Not yet assigned</span>' ?></td></tr>
      </table>

      <?php if ($request['quotation_amount']): ?>
        <div class="repair-estimate-note" style="margin-top:16px;">
          💰 Estimated Quote: <strong><?= htmlspecialchars(formatPrice($request['quotation_amount'])) ?></strong><?= $request['quotation_notes'] ? ' — ' . htmlspecialchars($request['quotation_notes']) : '' ?><br>
          The quotation is an estimate. The final quotation will be provided after inspection.
        </div>
      <?php endif; ?>

      <?php if ($customerImages): ?>
        <h4 style="margin-top:20px; margin-bottom:10px;">Your Photos</h4>
        <div style="display:flex; gap:10px; flex-wrap:wrap;">
          <?php foreach ($customerImages as $img): ?><img src="<?= htmlspecialchars($img['url']) ?>" style="width:110px; height:85px; object-fit:cover; border-radius:8px;"><?php endforeach; ?>
        </div>
      <?php endif; ?>

      <?php if ($beforeImages || $afterImages): ?>
        <h4 style="margin-top:20px; margin-bottom:10px;">Technician's Photos</h4>
        <div style="display:flex; gap:20px; flex-wrap:wrap;">
          <?php if ($beforeImages): ?>
            <div><p class="muted" style="margin-bottom:6px;">Before</p><div style="display:flex; gap:8px;"><?php foreach ($beforeImages as $img): ?><img src="<?= htmlspecialchars($img['url']) ?>" style="width:100px; height:80px; object-fit:cover; border-radius:8px;"><?php endforeach; ?></div></div>
          <?php endif; ?>
          <?php if ($afterImages): ?>
            <div><p class="muted" style="margin-bottom:6px;">After</p><div style="display:flex; gap:8px;"><?php foreach ($afterImages as $img): ?><img src="<?= htmlspecialchars($img['url']) ?>" style="width:100px; height:80px; object-fit:cover; border-radius:8px;"><?php endforeach; ?></div></div>
          <?php endif; ?>
        </div>
      <?php endif; ?>

      <?php if ($history): ?>
        <h4 style="margin-top:20px; margin-bottom:10px;">Status History</h4>
        <ul style="list-style:none; padding:0; margin:0;">
          <?php foreach ($history as $h): ?>
            <li style="padding:8px 0; border-bottom:1px solid var(--card-border, #E2E8F0); font-size:0.88rem;">
              <strong><?= htmlspecialchars($repairStatusLabels[$h['status']]['label'] ?? $h['status']) ?></strong>
              <span class="muted"> — <?= timeAgo($h['created_at']) ?></span>
              <?php if ($h['note']): ?><div class="muted"><?= htmlspecialchars($h['note']) ?></div><?php endif; ?>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
