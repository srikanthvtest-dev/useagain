<?php
require_once __DIR__ . '/includes/functions.php';

$errors = [];
$done = false;
$old = ['name' => '', 'email' => '', 'phone' => '', 'subject' => '', 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($old as $key => $_) { $old[$key] = trim($_POST[$key] ?? ''); }

    if (!csrf_verify($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Your session expired — please try again.';
    } elseif (isHoneypotTriggered($_POST)) {
        $errors[] = 'Submission blocked. Please try again.';
    } elseif (isRateLimited('contact')) {
        $errors[] = 'Please wait a few seconds before trying again.';
    } else {
        if (mb_strlen($old['name']) < 2) $errors[] = 'Please enter your name.';
        if (!filter_var($old['email'], FILTER_VALIDATE_EMAIL)) $errors[] = 'Please enter a valid email.';
        if (mb_strlen($old['message']) < 10) $errors[] = 'Please write a message of at least 10 characters.';

        if (empty($errors)) {
            createContactMessage($old['name'], $old['email'], $old['phone'], $old['subject'], $old['message']);
            $done = true;
            $old = ['name' => '', 'email' => '', 'phone' => '', 'subject' => '', 'message' => ''];
        }
    }
}

$pageTitle = 'Contact Us - UseAgain';
require_once __DIR__ . '/includes/header.php';
?>
<section style="padding-top:50px; padding-bottom:80px;">
  <div class="container" style="max-width:600px;">
    <h1 class="section-title" style="text-align:left;">Get in Touch</h1>
    <p class="section-sub" style="text-align:left;">Questions, feedback, or a report to file? We'd love to hear from you.</p>

    <?php if ($done): ?>
      <div class="alert alert-success">Thanks for reaching out! We'll get back to you soon.</div>
    <?php endif; ?>
    <?php if (!empty($errors)): ?>
      <div class="alert alert-error">
        <ul style="margin:0 0 0 20px;"><?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul>
      </div>
    <?php endif; ?>

    <form class="form-card" method="post" action="<?= BASE_URL ?>/contact.php">
      <?= csrf_field() ?>
      <div style="position:absolute; left:-9999px;" aria-hidden="true">
        <label for="<?= HONEYPOT_FIELD ?>">Leave this field empty</label>
        <input type="text" id="<?= HONEYPOT_FIELD ?>" name="<?= HONEYPOT_FIELD ?>" tabindex="-1" autocomplete="off">
      </div>
      <div class="form-row">
        <div class="form-group">
          <label for="name">Name *</label>
          <input type="text" id="name" name="name" value="<?= htmlspecialchars($old['name']) ?>" required>
        </div>
        <div class="form-group">
          <label for="email">Email *</label>
          <input type="email" id="email" name="email" value="<?= htmlspecialchars($old['email']) ?>" required>
        </div>
      </div>
      <div class="form-group">
        <label for="phone">Phone</label>
        <input type="tel" id="phone" name="phone" value="<?= htmlspecialchars($old['phone']) ?>">
      </div>
      <div class="form-group">
        <label for="subject">Subject</label>
        <input type="text" id="subject" name="subject" value="<?= htmlspecialchars($old['subject']) ?>">
      </div>
      <div class="form-group">
        <label for="message">Message *</label>
        <textarea id="message" name="message" rows="5" required><?= htmlspecialchars($old['message']) ?></textarea>
      </div>
      <button type="submit" class="btn btn-primary btn-block">Send Message</button>
    </form>
  </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
