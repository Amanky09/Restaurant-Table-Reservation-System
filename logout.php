<?php
require_once __DIR__ . '/auth.php';
require_csrf();
$_SESSION = [];
session_destroy();
header('Location: login.php');
