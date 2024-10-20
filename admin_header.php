<?php
  include 'config.php';
?>

<!-- ======= Header ======= -->
  <header id="header" class="fixed-top header-inner-pages">
    <div class="container d-flex align-items-center justify-content-lg-between">

      <!-- Uncomment below if you prefer to use an image logo -->
      <!-- <a href="index.html" class="logo me-auto me-lg-0"><img src="assets/img/logo.jpg" alt="" class="img-fluid"></a> -->

      <nav id="navbar" class="navbar order-last order-lg-0">
        <ul>
          <li><a class="nav-link scrollto " href="admin.php">HOME</a></li>
          <li class='dropdown'><a href='#'><span>SERVICES</span></a>
            <ul>
              <li ><a href='add_category.php'><span>Add Category</span></a></li>
              <li ><a href='add_services.php'><span>Add Services</span></a></li>
            </ul>
          </li>
          <li><a class="nav-link scrollto" href="adminAppointments.php">APPOINTMENTS</a></li>
          <li class='dropdown'><a href='#'><span>STAFFS</span></a>
            <ul>
              <li ><a href='admin_add_staff.php'><span>Add Staff</span></a></li>
              <li ><a href='admin_view_staff.php'><span>View Staffs</span></a></li>
              <li ><a href='admin_leave_req.php'><span>Leave Requests</span></a></li>
            </ul>
          </li>
          <li class='dropdown'><a href='#'><span>CUSTOMERS</span></a>
            <ul>
              <li ><a href='admin_add_cust.php'><span>Add Customer</span></a></li>
              <li ><a href='admin_view_cust.php'><span>View Customer</span></a></li>
            </ul>
          </li>
          <li class='dropdown'><a href='#'><span>MESSAGES</span></a>
            <ul>
              <li ><a href='admin_view_inquiries.php'><span>Inquiries</span></a></li>
              <li ><a href='admin_view_feedbacks.php'><span>Feedbacks</span></a></li>
            </ul>
          </li>
          <li class='dropdown'><a href='#'><span>PRODUCTS</span></a>
            <ul>
              <li ><a href='add_product.php'><span>Add Product</span></a></li>
              <li ><a href='add_product_category.php'><span>Add Product Category</span></a></li>
              <li ><a href='add_brand.php'><span>Add Product Brand</span></a></li>
            </ul>
          </li>
          <li class='dropdown'><a href='#'><span>PROFILE</span></a>
            <ul>
              <li ><a href='#'><span>Change Password</span></a></li>
              <li ><a href='#'><span>Logout</span></a></li>
            </ul>
          </li>
        </ul>
        <i class="bi bi-list mobile-nav-toggle"></i>
      </nav><!-- .navbar -->

    </div>
  </header><!-- End Header -->