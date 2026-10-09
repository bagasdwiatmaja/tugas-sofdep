<?php
require_once __DIR__ . '/app/helpers/bootstrap.php';
if (!empty($_SESSION['user'])) {
    redirect($_SESSION['user']['role'] === 'admin' ? '/sistem-kasir/admin/dashboard.php' : '/sistem-kasir/kasir/dashboard.php');
}
redirect('/sistem-kasir/auth/login.php');
