<?php
  include 'database/db_connect.php';
?>

<!-- ======= Header ======= -->
<header id="header" class="fixed-top header-inner-pages">
<div class="container d-flex align-items-center justify-content-between">
  <a href="admin.php" class="logo"><img src="assets/img/logo/lb2.png" alt="Logo" class="img-fluid"></a>
  <nav id="navbar" class="navbar order-last order-lg-0">
      <ul>
        <li><a class="nav-link scrollto" href="admin.php">HOME</a></li>
        <li><a class="nav-link scrollto" href="admin_pos.php">BROCHURE</a></li>
        <li class='dropdown'><a href='#'><span>PRODUCTS</span></a>
          <ul>
            <li><a href='add_brand.php'><span>Add Brand</span></a></li>
            <li><a href='add_category.php'><span>Add Category</span></a></li>
            <li><a href='add_product.php'><span>Add Product</span></a></li>
            <li><a href='admin_productList.php'><span>Product List</span></a></li>
          </ul>
        </li>
        <li class='dropdown'><a href='#'><span>AGENTS</span></a>
          <ul>
            <li><a href='admin_registerAgent.php'><span>Add Sales Agent</span></a></li>
            <li><a href='admin_viewAgent.php'><span>View Sales Agent</span></a></li>
            <li><a href='admin_agentInfo.php'><span>Sales Agent Information</span></a></li>
          </ul>
        </li>
        <li class='dropdown'><a href='#'><span>ORDERS</span></a>
          <ul>
            <li><a href='admin_orderList.php'><span>Order List</span></a></li>
            <li><a href='admin_checkout.php'><span>Checkout List</span></a></li>
            <li><a href='admin_pua.php'><span>Product Under Agents</span></a></li>
            <li><a href='admin_pua_out.php'><span>PUA Cart</span></a></li>
          </ul>
        </li>
        <li class='dropdown'><a href='#'><span>MESSAGES</span></a>
          <ul>
            <li><a href='admin_viewInquiries.php'><span>Inquiries</span></a></li>
            <li><a href='admin_viewFeedbacks.php'><span>Feedbacks</span></a></li>
            <li><a href='admin_message.php'><span>Agents Message</span></a></li>
          </ul>
        </li>
        <li><a class="nav-link scrollto" href="admin_inventory.php">INVENTORY</a></li>
        <li class='dropdown'><a href='#'><span>REPORTS</span></a>
          <ul>
            <li><a href='admin_overall_reports.php'><span>Overall Report</span></a></li>
            <li><a href='admin_sagent_report.php'><span>Sales Agent Performance</span></a></li>
            <li><a href='to_return_pro.php'><span>To Return Items</span></a></li>
            <li><a href='to_pay_products.php'><span>To Pay</span></a></li>
          </ul>
        </li>
        <li class='dropdown'><a href='#'><span>PROFILE</span></a>
          <ul>
            <li><a href='reset_pass.php'><span>Change Password</span></a></li>
            <li><a href='login.php'><span>Logout</span></a></li>
          </ul>
        </li>
      </ul>
      <i class="mobile-nav-toggle bi bi-list"></i>
    </nav><!-- .navbar -->

  </div>
</header><!-- End Header -->

<!-- ======= JavaScript for Toggle ======= -->
<script>
  // Toggle Navbar for Mobile View
//   document.addEventListener('DOMContentLoaded', () => {
//   const mobileNavToggle = document.querySelector('.mobile-nav-toggle');
//   const navbar = document.querySelector('#navbar');

//   if (!mobileNavToggle || !navbar) {
//     console.error("Mobile nav toggle or navbar not found");
//     return;
//   }

//   mobileNavToggle.addEventListener('click', () => {
//     navbar.classList.toggle('navbar-mobile'); // Toggle the navbar-mobile class
//     mobileNavToggle.classList.toggle('bi-x'); // Toggle the icon
//   });
// });

</script>
