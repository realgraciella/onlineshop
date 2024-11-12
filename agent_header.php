<?php
  include 'database/db_connect.php';
?>

<!-- ======= Header ======= -->
  <header id="header" class="fixed-top header-inner-pages">
    <div class="container d-flex align-items-center justify-content-lg-between">

    <a href="admin.php" class="logo me-auto me-lg-0"><img src="assets\img\logo\l1.png" alt="" class="img-fluid"></a>

      <nav id="navbar" class="navbar order-last order-lg-0">
        <ul>
          <li><a class="nav-link scrollto " href="agent.php">HOME</a></li>
          <li class='dropdown'><a href='#'><span>BROCHURE</span></a>
            <ul>
              
            </ul>
          </li>
          <li class='dropdown'><a href='#'><span>CLIENT</span></a>
            <ul>
              <li ><a href='agent_registerClient.php'><span>Add Client</span></a></li>
              <li ><a href='agent_viewClient.php'><span>View Client</span></a></li>
              <li ><a href='agent_clientInfo.php'><span>Client Information</span></a></li>
            </ul>
          </li>         
          <li class='dropdown'><a href='#'><span>MESSAGES</span></a>
            <ul>
              <li ><a href='agent_view_inquiries.php'><span>Inbox</span></a></li>
              <li ><a href='agent_view_feedbacks.php'><span>Feedbacks</span></a></li>
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