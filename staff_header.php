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
          <li><a class="nav-link scrollto " href="staff.php">HOME</a></li>
          <li class='dropdown'><a href='#'><span>PROFILE</span></a>
            <ul>
              <li ><a href='staff_acc.php'><span>View Profile</span></a></li>
              <li ><a href='#'><span>Edit Profile</span></a></li>
            </ul>
          </li>
          <li><a class="nav-link scrollto" href="staff_view_app.php">APPOINTMENTS</a></li>
          <li class='dropdown'><a href='#'><span>LEAVE</span></a>
            <ul>
              <li ><a href='staff_leave_req.php'><span>Apply Leave</span></a></li>
              <li ><a href='staff_view_leave_req.php'><span>Leave Status</span></a></li>
            </ul>
          </li>          
          <li class='dropdown'><a href='#'><span>SETTINGS</span></a>
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