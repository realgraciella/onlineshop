<?php
  include 'database/db_connect.php';
?>

<!-- ======= Header ======= -->
<header id="header" class="fixed-top header-inner-pages">
  <div class="container d-flex align-items-center justify-content-lg-between">

    <a href="customer.php" class="logo me-auto me-lg-0"><img src="assets/img/logo/lb2.png" alt="" class="img-fluid"></a>

    <nav id="navbar" class="navbar order-last order-lg-0">
      <ul>
        <li><a class="nav-link scrollto" href="customer.php">HOME</a></li>
        <li><a class="nav-link scrollto" href="customer_products.php">BROCHURE</a></li>
        <li class='dropdown'><a href='#'><span>TEAM</span></a>
          <ul>
            <li><a href='customer_agent.php'><span>My Agent</span></a></li>
          </ul>
        </li>
        <li class='dropdown'><a href='#'><span>ORDERS</span></a>
          <ul>
          <li><a href='customer_cart.php'><span>Cart</span></a></li>
            <li><a href='customer_order_status.php'><span>Order Status</span></a></li>
          </ul>
        </li>
        <li class='dropdown'><a href='#'><span>MESSAGES</span></a>
          <ul>
          <li><a href='customer_inbox.php'><span>Inbox</span></a></li>
            <li><a href='customer_inquiries.php'><span>Inquiries</span></a></li>
            <li><a href='customer_feedback.php'><span>Feedbacks</span></a></li>
          </ul>
        </li>
        <li class='dropdown'><a href='#'><span>PROFILE</span></a>
          <ul>
            <li><a href='reset_pass.php'><span>Change Password</span></a></li>
            <li><a href='login.php'><span>Logout</span></a></li>
          </ul>
        </li>
      </ul>
      <i class="bi bi-list mobile-nav-toggle"></i>
    </nav><!-- .navbar -->

  </div>
</header><!-- End Header -->

<!-- ======= JavaScript for Toggle ======= -->
<script>
  // Toggle Navbar for Mobile View
  document.addEventListener('DOMContentLoaded', () => {
    const mobileNavToggle = document.querySelector('.mobile-nav-toggle');
    const navbar = document.querySelector('#navbar');

    mobileNavToggle.addEventListener('click', () => {
      navbar.classList.toggle('navbar-mobile');
      mobileNavToggle.classList.toggle('bi-x'); // Change the icon on toggle
    });
  });
</script>
