<?php
require_once __DIR__ . '/includes/functions.php';

if (isUserLoggedIn()) {
    header('Location: ' . BASE_URL . '/my-account.php');
    exit;
}

$redirectTo = safeRedirectPath($_GET['redirect'] ?? $_POST['redirect'] ?? '', '/my-account.php');
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!csrf_verify($_POST['csrf_token'] ?? '')) {
        $error = 'Your session expired — please try again.';
    } elseif (isRateLimited('login')) {
        $error = 'Please wait a few seconds before trying again.';
    } else {
        $user = findUserByEmail($email);
        // Same generic message whether the email doesn't exist or the
        // password is wrong — never reveal which one it was.
        if (!$user || !password_verify($password, $user['password_hash'])) {
            $error = 'Incorrect email or password.';
        } elseif ($user['status'] !== 'active') {
            $error = 'Your account is currently suspended. Please contact support.';
        } else {
            logUserIn($user['id']);
            touchLastLogin($user['id']);
            header('Location: ' . BASE_URL . $redirectTo);
            exit;
        }
    }
}

$pageTitle = 'Login - UseAgain';
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
      <h2>Give it a second life.</h2>
      <p>Join a community marketplace built around buying, selling, and donating things that still have plenty of use left in them.</p>
      <ul class="auth-benefits">
        <li>✔ Reach buyers near you instantly</li>
        <li>✔ List an item in under two minutes</li>
        <li>✔ Every listing reviewed before it goes live</li>
        <li>✔ Chat directly over WhatsApp — no middlemen</li>
      </ul>
    </div>

    <div class="auth-form-panel">
      <h1>Welcome Back</h1>
      <p class="auth-sub">Log in to post ads and manage your listings.</p>

      <?php if ($error): ?><div class="alert alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>

	<form method="post" action="<?= BASE_URL ?>/login.php">
    <?= csrf_field() ?>
    <input type="hidden" name="redirect" value="<?= htmlspecialchars($redirectTo) ?>">

    <div class="form-group">
        <label for="email">Email Address</label>
        <input type="email"
               id="email"
               name="email"
               required
               autofocus
               placeholder="you@example.com">
    </div>

    <div class="form-group">
        <label for="password">Password</label>
        <input type="password"
               id="password"
               name="password"
               required
               placeholder="••••••••">
    </div>

    <button type="submit" class="btn btn-primary btn-block">
        Log In
    </button>

    <div style="text-align:center;margin:20px 0;color:#888;font-size:14px;">
        OR
    </div>

    <a href="<?= BASE_URL ?>/google-login.php?redirect=<?= urlencode($redirectTo) ?>"
       class="google-login-btn">
        <img src="https://developers.google.com/identity/images/g-logo.png"
             alt="Google"
             width="20"
             height="20">
        Continue with Google
    </a>
	</form>

      <p class="auth-switch">Don't have an account? <a href="<?= BASE_URL ?>/register.php">Create one for free</a></p>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
