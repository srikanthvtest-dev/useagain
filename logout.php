<?php
require_once __DIR__ . '/includes/functions.php';
logUserOut();
header('Location: ' . BASE_URL . '/index.php');
exit;
