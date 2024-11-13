<?php
  include 'database/db_connect.php';
?>

<!-- ======= Header ======= -->
  <header id="header" class="fixed-top header-inner-pages">
    <div class="container d-flex align-items-center justify-content-lg-between">

    <a href="admin.php" class="logo me-auto me-lg-0"><img src="assets\img\logo\l1.png" alt="" class="img-fluid"></a>

      <nav id="navbar" class="navbar order-last order-lg-0">
        <ul>
          <li><a class="nav-link scrollto " href="admin.php">HOME</a></li>
          <li><a class="nav-link scrollto " href="admin_products.php">BROCHURE</a></li>
          <li class='dropdown'><a href='#'><span>PRODUCTS</span></a>
            <ul>
              <li ><a href='add_brand.php'><span>Add Brand</span></a></li>
              <li ><a href='add_category.php'><span>Add Category</span></a></li>
              <li ><a href='add_product.php'><span>Add Product</span></a></li>
              <li ><a href='admin_productList.php'><span>Product List</span></a></li>
            </ul>
          </li>
          <li class='dropdown'><a href='#'><span>AGENTS</span></a>
            <ul>
              <li ><a href='admin_registerAgent.php'><span>Add Sales Agent</span></a></li>
              <li ><a href='admin_viewAgent.php'><span>View Sales Agent</span></a></li>
              <li ><a href='admin_agentInfo.php'><span>Sales Agent Information</span></a></li>
            </ul>
          </li>
          <li class='dropdown'><a href='#'><span>ORDERS</span></a>
            <ul>
              <li ><a href='admin_registerAgent.php'><span>Order List</span></a></li>
              <li ><a href='admin_viewAgent.php'><span>Wishlist</span></a></li>
            </ul>
          </li>         
          <li class='dropdown'><a href='#'><span>MESSAGES</span></a>
            <ul>
              <li ><a href='admin_viewInquiries.php'><span>Inquiries</span></a></li>
              <li ><a href='admin_viewFeedbacks.php'><span>Feedbacks</span></a></li>
            </ul>
          </li>
          <li class='dropdown'><a href='#'><span>PROFILE</span></a>
            <ul>
              <li ><a href='reset_pass.php'><span>Change Password</span></a></li>
              <li ><a href='login.php'><span>Logout</span></a></li>
            </ul>
          </li>
        </ul>
        <i class="bi bi-list mobile-nav-toggle"></i>
      </nav><!-- .navbar -->

    </div>
  </header><!-- End Header -->