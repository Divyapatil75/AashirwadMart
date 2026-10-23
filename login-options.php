<?php
require_once __DIR__.'/includes/functions.php';
render_header('Choose login');
?>
<div class="auth-wrap login-options-wrap">
  <div class="form-card login-options-card">
    <span class="eyebrow">WELCOME TO AASHIRWAD MART</span>
    <h1>Choose your login</h1>
    <p class="muted">Select the account type you want to use. Customer and admin accounts have separate login pages and dashboards.</p>
    <div class="login-choice-list">
      <a class="login-choice" href="<?=e(url('login.php'))?>">
        <span class="login-choice-icon">🛍️</span><span><strong>Customer Login</strong><small>Shop products, manage your cart and wishlist, and view your orders.</small></span><span class="login-choice-arrow">→</span>
      </a>
      <a class="login-choice" href="<?=e(url('admin/login.php'))?>">
        <span class="login-choice-icon">🔐</span><span><strong>Admin Login</strong><small>Manage products, categories, orders, payments and delivery PIN codes.</small></span><span class="login-choice-arrow">→</span>
      </a>
    </div>
    <p class="form-foot">New customer? <a href="<?=e(url('register.php'))?>">Create an account</a></p>
  </div>
</div>
<?php render_footer(); ?>
