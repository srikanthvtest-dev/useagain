<?php
require_once __DIR__ . '/includes/functions.php';

// Already logged in? No need to register again.
if (isUserLoggedIn()) {
    header('Location: ' . BASE_URL . '/my-account.php');
    exit;
}

$errors = [];
$old = ['name' => '', 'email' => '', 'phone' => '', 'whatsapp' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old['name']     = trim($_POST['name'] ?? '');
    $old['email']    = trim($_POST['email'] ?? '');
    $old['phone']    = trim($_POST['phone'] ?? '');
    $old['whatsapp'] = trim($_POST['whatsapp'] ?? '');
    $password        = $_POST['password'] ?? '';
    $passwordConfirm  = $_POST['password_confirm'] ?? '';

    if (!csrf_verify($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Your session expired — please try submitting the form again.';
    } elseif (isHoneypotTriggered($_POST)) {
        $errors[] = 'Submission blocked. Please try again.';
    } elseif (isRateLimited('register')) {
        $errors[] = 'Please wait a few seconds before trying again.';
    } else {
        if (mb_strlen($old['name']) < 2) $errors[] = 'Please enter your full name.';
        if (!filter_var($old['email'], FILTER_VALIDATE_EMAIL)) $errors[] = 'Please enter a valid email address.';
        if (!preg_match('/^[0-9+\-\s]{7,15}$/', $old['phone'])) $errors[] = 'Please enter a valid phone number.';
        if ($old['whatsapp'] !== '' && !preg_match('/^[0-9+\-\s]{7,15}$/', $old['whatsapp'])) $errors[] = 'Please enter a valid WhatsApp number.';
        if (strlen($password) < 8) $errors[] = 'Password must be at least 8 characters.';
        if ($password !== $passwordConfirm) $errors[] = 'Passwords do not match.';

        if (empty($errors) && findUserByEmail($old['email'])) {
            $errors[] = 'An account with this email already exists — try logging in instead.';
        }

        if (empty($errors)) {
            $whatsapp = $old['whatsapp'] !== '' ? $old['whatsapp'] : $old['phone'];
            try {
                $userId = createUser($old['name'], $old['email'], $old['phone'], $whatsapp, password_hash($password, PASSWORD_DEFAULT));
                logUserIn($userId);
                touchLastLogin($userId);
                header('Location: ' . BASE_URL . '/my-account.php?welcome=1');
                exit;
            } catch (PDOException $e) {
                // Most likely the UNIQUE(email) constraint — two signups
                // landed at almost the same moment. Fail gracefully rather
                // than showing a raw database error.
                error_log('UseAgain registration failed: ' . $e->getMessage());
                $errors[] = 'An account with this email already exists — try logging in instead.';
            }
        }
    }
}

$pageTitle = 'Create Your Account - UseAgain';
require_once __DIR__ . '/includes/header.php';
?>

<section class="auth-section">
  <div class="auth-shell">
    <div class="auth-brand-panel">
      <a href="<?= BASE_URL ?>/index.php" class="auth-logo">
        <svg width="36" height="36" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
          <circle cx="15" cy="15" r="15" fill="#fff" fill-opacity="0.15"/>
          <path d="M15 6.5c-3 0-5.6 1.7-6.9 4.2" stroke="#fff" stroke-width="2" stroke-linecap="round" fill="none"/>
          <path d="M22.8 12.8c0.5 3-0.8 6-3.5 7.9" stroke="#fff" stroke-width="2" stroke-linecap="round" fill="none"/>
          <path d="M11 22.6c-2.9-0.8-5-3.1-5.6-6" stroke="#fff" stroke-width="2" stroke-linecap="round" fill="none"/>
        </svg>
        <span>Use<strong>Again</strong></span>
      </a>
      <h2>Join the circle.</h2>
      <p>Create a free account to start buying, selling, and donating reusable products in your community.</p>
      <ul class="auth-benefits">
        <li>✔ Free to join, free to list</li>
        <li>✔ Post an ad in under two minutes</li>
        <li>✔ Reach real buyers near you</li>
        <li>✔ Donate items you no longer need</li>
      </ul>
    </div>

    <div class="auth-form-panel">
      <h1>Create Your Account</h1>
      <p class="auth-sub">Register free to post ads, and buy, sell or donate on UseAgain.</p>

      <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
          <ul style="margin:0 0 0 20px;">
            <?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <form method="post" action="<?= BASE_URL ?>/register.php">
        <?= csrf_field() ?>
        <div style="position:absolute; left:-9999px;" aria-hidden="true">
          <label for="<?= HONEYPOT_FIELD ?>">Leave this field empty</label>
          <input type="text" id="<?= HONEYPOT_FIELD ?>" name="<?= HONEYPOT_FIELD ?>" tabindex="-1" autocomplete="off">
        </div>

        <div class="form-group">
          <label for="name">Full Name *</label>
          <input type="text" id="name" name="name" value="<?= htmlspecialchars($old['name']) ?>" required autofocus>
        </div>

        <div class="form-group">
          <label for="email">Email Address *</label>
          <input type="email" id="email" name="email" value="<?= htmlspecialchars($old['email']) ?>" required>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="phone">Phone Number *</label>
            <input type="tel" id="phone" name="phone" value="<?= htmlspecialchars($old['phone']) ?>" required placeholder="10-digit mobile number">
          </div>
          <div class="form-group">
            <label for="whatsapp">WhatsApp Number</label>
            <input type="tel" id="whatsapp" name="whatsapp" value="<?= htmlspecialchars($old['whatsapp']) ?>" placeholder="Same as phone if left blank">
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="password">Password *</label>
            <input type="password" id="password" name="password" required minlength="8">
          </div>
          <div class="form-group">
            <label for="password_confirm">Confirm Password *</label>
            <input type="password" id="password_confirm" name="password_confirm" required minlength="8">
          </div>
        </div>
        <p class="form-note">At least 8 characters.</p>

        <button type="submit" class="btn btn-primary btn-block">Create Account</button>
      </form>

      <p class="auth-switch">Already have an account? <a href="<?= BASE_URL ?>/login.php">Log in</a></p>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
