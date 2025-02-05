<?php
  include 'database/db_connect.php';
?>

<!-- ======= Header ======= -->
  <header id="header" class="fixed-top header-inner-pages">
    <div class="container d-flex align-items-center justify-content-lg-between">

    <a href="agent.php" class="logo me-auto me-lg-0"><img src="assets\img\logo\lb2.png" alt="" class="img-fluid"></a>

      <nav id="navbar" class="navbar order-last order-lg-0">
        <ul>
          <li><a class="nav-link scrollto " href="agent.php">HOME</a></li>
          <li class='dropdown'><a href='agent_dashboard.php'><span>DASHBOARD</span></a></li>
          <li class='dropdown'><a href='agent_products.php'><span>BROCHURE</span></a></li>
          <li class='dropdown'><a href='#'><span>CUSTOMERS</span></a>
            <ul>
              <li ><a href='agent_registerClient.php'><span>Add Customer</span></a></li>
              <li ><a href='agent_viewClient.php'><span>View Customer</span></a></li>
              <li ><a href='agent_clientInfo.php'><span>Customer Information</span></a></li>
            </ul>
          </li>
          <li class='dropdown'><a href='#'><span>ORDERS</span></a>
            <ul>
              <li ><a href='agent_cart.php'><span>Cart</span></a></li>
              <li ><a href='agent_orders.php'><span>Orders</span></a></li>
              <li ><a href='agent_corders.php'><span>Customer Order</span></a></li>
            </ul>
          </li>          
          <li class='dropdown'><a href='#'><span>MESSAGES</span></a>
            <ul>
              <li ><a href='agent_inbox.php'><span>Inbox</span></a></li>
              <li ><a href='agent_inquire.php'><span>Inquries and Feedback</span></a></li>
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