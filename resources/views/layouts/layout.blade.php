<!DOCTYPE html>
<html lang="en">
    
<head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="A panel for admins">
        <meta name="author" content="5Star Company">
        <title>MCD Admin Panel</title>

        <!-- Bootstrap core CSS -->
        <link href="assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet">

        <!-- Custom fonts for this template -->
        <link href="assets/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">

        <!-- Custom fonts for this template -->
        <link href="assets/plugins/themify/css/themify.css" rel="stylesheet" type="text/css">

        <!-- Angular Tooltip Css -->
        <link href="assets/plugins/angular-tooltip/angular-tooltips.css" rel="stylesheet">
        <link rel="stylesheet" href="assets/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">

        <!-- Page level plugin CSS -->
        <link href="assets/dist/css/animate.css" rel="stylesheet">

        <!-- Custom styles for this template -->
        <link href="assets/dist/css/adminfier.css" rel="stylesheet">
        <link href="assets/dist/css/adminfier-responsive.css" rel="stylesheet">

        <!-- Custom styles for Color -->
        <link id="jssDefault" rel="stylesheet" href="assets/dist/css/skins/default.css">
    </head>

    <body class="fixed-nav sticky-footer <?php echo $theme ?? 'green-skin' ?>" id="page-top">
    
        <!-- ===============================
            Navigation Start
        ====================================-->
        <nav class="navbar navbar-expand-lg navbar-light fixed-top" id="mainNav">
            
            <!-- Start Header -->
            <header class="header-logo">
                <a class="nav-link text-center mr-lg-3 hidden-xs" id="sidenavToggler"><i class="ti-align-left"></i></a>
                <img src="../uploads/logo.png" alt="user-img" width="36" class="img-circle">
            </header>
            <!-- End Header -->
            
            <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
              <span class="ti-align-left"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarResponsive">
                 
                <!-- =============== Start Side Menu ============== -->
                <div class="navbar-side">
                  <ul class="navbar-nav side-navbar" id="exampleAccordion">
                  
                    <!-- Start Dashboard-->
                    <li class="nav-item" data-toggle="tooltip" data-placement="right" title="Projects">
                      <a class="nav-link" href="dashboard.php">
                        <i class="ti i-cl-2 ti-layers"></i>
                        <span class="nav-link-text">Dashboard</span>
                        
                      </a>
                    </li>
                        <!-- Start UI Elements -->
                    <li class="nav-item" data-toggle="tooltip" data-placement="right" title="Components">
                      <a class="nav-link nav-link-collapse collapsed" data-toggle="collapse" href="#Components" data-parent="#exampleAccordion">
                        <i class="ti ti-user"></i>
                        <span class="nav-link-text">Manage Users</span>
                      </a>
                      <ul class="sidenav-second-level collapse" id="Components">
                      
                            <li>
                          <a href="viewusers.php">View All Users</a>
                        </li>
                        
                        <li>
                          <a href="createuser.php">Create User</a>
                        </li>
                        
                        
                      </ul>
                    </li>
                    <!-- End UI Elements -->
                    <!-- Start projects -->
                    <li class="nav-item" data-toggle="tooltip" data-placement="right" title="Projects">
                      <a class="nav-link" href="creditwallet.php">
                        <i class="ti i-cl-2 ti-wallet"></i>
                        <span class="nav-link-text">Credit Wallet</span>
                        
                      </a>
                    </li>
                    <!-- End Projects -->
                    
                    
                    
                    <!-- Start UI Elements -->
                    <li class="nav-item" data-toggle="tooltip" data-placement="right" title="Components">
                      <a class="nav-link nav-link-collapse collapsed" data-toggle="collapse" href="#Componen" data-parent="#exampleAccordion">
                        <i class="ti ti-money"></i>
                        <span class="nav-link-text">Manage Deposits</span>
                      </a>
                      <ul class="sidenav-second-level collapse" id="Componen">
                      
                            <li>
                          <a href="viewdeposits.php">Deposited Funds</a>
                        </li>
                        
                        <li>
                          <a href="managedeposits.php">Manage Deposits</a>
                        </li>
                        
                        
                      </ul>
                    </li>
                    <!-- End UI Elements -->
                    
                        <!-- Start UI Elements -->
                    <li class="nav-item" data-toggle="tooltip" data-placement="right" title="Components">
                      <a class="nav-link nav-link-collapse collapsed" data-toggle="collapse" href="#Component" data-parent="#exampleAccordion">
                        <i class="ti ti-user"></i>
                        <span class="nav-link-text">Manage Products</span>
                      </a>
                      <ul class="sidenav-second-level collapse" id="Component">
                      
                            <li>
                          <a href="viewproducts.php">View All Products</a>
                        </li>
                        
                        <li>
                          <a href="createproduct.php">Create New Product</a>
                        </li>
                        
                        
                      </ul>
                    </li>
                    <!-- End UI Elements -->
                    
                    <!-- Start UI Elements -->
                    <li class="nav-item" data-toggle="tooltip" data-placement="right" title="Components">
                      <a class="nav-link nav-link-collapse collapsed" data-toggle="collapse" href="#Compone" data-parent="#exampleAccordion">
                        <i class="ti ti-support"></i>
                        <span class="nav-link-text">Support Tickets</span>
                      </a>
                      <ul class="sidenav-second-level collapse" id="Compone">
                      
                            <li>
                          <a href="alltickets.php">View All Tickets</a>
                        </li>
                        
                        <li>
                          <a href="activetickets.php">Opened Ticket</a>
                        </li>
                        
                        
                      </ul>
                    </li>
                    <!-- End UI Elements -->
                    
                    <!-- Start UI Elements -->
                    <li class="nav-item" data-toggle="tooltip" data-placement="right" title="Components">
                      <a class="nav-link nav-link-collapse collapsed" data-toggle="collapse" href="#Compon" data-parent="#exampleAccordion">
                        <i class="ti ti-credit-card"></i>
                        <span class="nav-link-text">Payment Gateways</span>
                      </a>
                      <ul class="sidenav-second-level collapse" id="Compon">
                      
                            <li>
                          <a href="cryptogateway.php">Crypto Currency</a>
                        </li>
                        <li>
                          <a href="creditcard.php">Credit/Debit Card</a>
                        </li>
                        <li>
                          <a href="banktransfer.php">Bank Transfer</a>
                        </li>
                        
                        
                      </ul>
                    </li>
                    <!-- End UI Elements -->
                    
                    <!-- End Dashboard -->
                    
                    <!-- Start projects -->
                    <li class="nav-item" data-toggle="tooltip" data-placement="right" title="Projects">
                      <a class="nav-link" href="systemsettings.php">
                        <i class="ti i-cl-2 ti-settings"></i>
                        <span class="nav-link-text">System Settings</span>
                        </a>
                    </li>
                    <!-- End Projects -->
                    
                    
                    
                    <!-- End Projects -->
                        <!-- Start UI Elements -->
                    <li class="nav-item" data-toggle="tooltip" data-placement="right" title="Components">
                      <a class="nav-link nav-link-collapse collapsed" data-toggle="collapse" href="#Componens" data-parent="#exampleAccordion">
                        <i class="ti ti-lock"></i>
                        <span class="nav-link-text">Manage Admin</span>
                      </a>
                      <ul class="sidenav-second-level collapse" id="Componens">
                      
                            <li>
                          <a href="editprofile.php">Edit Admin Profile</a>
                        </li>
                        
                        <li>
                          <a href="updatepassword.php">Update Password</a>
                        </li>
                        
                        
                      </ul>
                    </li>
                    <!-- End UI Elements -->
                    
                    
                    <!-- End Advance Pages -->
                    
                    
                    
                    
                    <!-- Start Help & Support -->
                    <li class="nav-item" data-toggle="tooltip" data-placement="right" title="Help-Support">
                        <a class="nav-link" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="ti ti-power-off"></i>
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                        @csrf
                                    </form>

                    </li>
                    <!-- End Help & Support -->
                  </ul>
              </div>
             <!-- =============== End Side Menu ============== -->
              
              <!-- =============== Search Bar ============== -->
              <ul class="navbar-nav ml-left">
                <li class="nav-item">
                  <form class="form-inline my-2 my-lg-0 mr-lg-2">
                    <div class="input-group">
                        <span class="input-group-btn">
                            <button class="btn btn-primary" type="button">
                              <i class="ti-search"></i>
                            </button>
                        </span>
                      <input class="form-control" type="text" placeholder="Type In TO Search">
                    </div>
                  </form>
                </li>
              </ul>
              <!-- =============== End Search Bar ============== -->
              
              <!-- =============== Header Rightside Menu ============== -->
              <ul class="navbar-nav ml-auto">
              
                
                
                
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle mr-lg-0 user-img a-topbar__nav a-nav" id="userDropdown" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <img src="../uploads/logo.png" alt="user-img" width="36" class="img-circle">
                    </a>
                  
                    <ul class="dropdown-menu dropdown-user animated flipInX" aria-labelledby="userDropdown">
                        <li class="top-header-dropdown green-bg">
                            <div class="header-user-pic">
                                <img src="../uploads/logo.png" alt="user-img" width="36" class="img-circle">
                            </div>
                            <div class="header-user-det">
                                <span class="a-dropdown--title">Admin</span>
                                </div>
                        </li>
                        <li><a class="dropdown-item" href="systemsettings.php"><i class="ti-settings"></i> System Setting</a></li>
                        <li><a class="dropdown-item" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="fa fa-power-off"></i> Logout</a></li>
                    </ul>
                </li>
              </ul>
              <!-- =============== End Header Rightside Menu ============== -->
            </div>
            <button class="w3-button w3-teal w3-xlarge w3-right" onclick="openRightMenu()"><i class="spin fa fa-cog" aria-hidden="true"></i></button>
        </nav>
        <!-- =====================================================
                            End Navigations
        ======================================================= -->
            @yield('content')

    <!-- Footer -->
            <footer class="sticky-footer">
              <div class="container">
                <div class="text-center">
                  <small class="font-15">All rights reserved - 5Star Inn Company
                     <i class="fa fa-copyright cl-danger"></i>{{ $dateYear ?? '2019' }}</small>
                </div>
              </div>
            </footer>
            <!-- /Footer -->
            
            <!-- Switcher Start -->
            <div class="w3-ch-sideBar w3-bar-block w3-card-2 w3-animate-right" style="display:none;right:0;" id="rightMenu">
                <div class="rightMenu-scroll">
                
                    <button onclick="closeRightMenu()" class="w3-bar-item w3-button w3-large theme-bg">Setting Pannel <i class="ti-close"></i></button>
                    <div class="right-ch-sideBar" id="side-scroll">
                        <div class="user-box">
                        
                            <div class="profile-img">
                                <img src="../uploads/logo.png" alt="user">
                                <!-- this is blinking heartbit-->
                                <div class="notify setp"> <span class="heartbit"></span> <span class="point"></span> </div>
                            </div>
                            <div class="profile-text">
                                <h4>Admin</h4>
                                 <a href="updatepassword.php" class="bg-info-light"><i class="ti-settings"></i></a>
                                 <a onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="bg-danger-light"><i class="ti-power-off"></i></a>
                            </div>
                            
                            <div class="tabbable-line">
                            
                                <ul class="nav nav-tabs ">
                                
                                    <li class="active">
                                        <a class="bg-primary-light" href="#options" data-toggle="tab">
                                        <i class="ti-palette"></i> </a>
                                    </li>
                                    
                                    <li>
                                        <a class="bg-danger-light" href="#notification" data-toggle="tab">
                                        <i class="ti-bell"></i> </a>
                                    </li>
                                    
                                    <li>
                                        <a class="bg-success-light" href="#all-messages" data-toggle="tab">
                                        <i class="ti-comment-alt"></i> </a>
                                    </li>
                                    
                                </ul>
                                
                                <div class="tab-content">                                 
                                        
                                    </div>
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                
                </div>
            </div>
            <!-- /Switcher -->
    
            
            
            <!-- Scroll to Top Button-->  
            <a class="scroll-to-top rounded cl-white theme-bg" href="#page-top">
              <i class="ti-angle-double-up"></i>
            </a>
            <script src="assets/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.js"></script>
            
            <!-- Bootstrap core JavaScript-->
            <script src="assets/plugins/jquery/jquery.min.js"></script>
            <script src="assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
            
            <!-- Core plugin JavaScript-->
            <script src="assets/plugins/jquery-easing/jquery.easing.min.js"></script>
            
             <!-- Slick Slider Js -->
            <script src="assets/plugins/slick-slider/slick.js"></script>
            
            <!-- Slim Scroll -->
            <script src="assets/plugins/slim-scroll/jquery.slimscroll.min.js"></script>
            <!-- ChartJS -->
            <script src="assets/plugins/chart.js/Chart.bundle.js"></script>
            <script src="assets/plugins/chart.js/Chart.js"></script>
            
            <!-- Angular Tooltip -->
            <script src="assets/plugins/angular-tooltip/angular.js"></script>
            <script src="assets/plugins/angular-tooltip/angular-tooltips.js"></script>
            <script src="assets/plugins/angular-tooltip/index.js"></script>
            
            <!-- Custom Chartjs JavaScript -->
            
            <!-- Custom scripts for all pages -->
            <script src="assets/dist/js/adminfier.js"></script>
            <script src="assets/dist/js/jQuery.style.switcher.js"></script>
            <script>
                
                $(document).ready(function() {
                    $('#styleOptions').styleSwitcher();
                });
      
              $('.dropdown-toggle').dropdown()
 
        $(function () {
    "use strict";
    // Bar chart
    new Chart(document.getElementById("bar-chart"), {
        type: 'bar',
        data: {
          labels: ["Rave", "Paystack", "Payant", "Bank Transfer"],
          datasets: [
            {
              label: "Counts",
              backgroundColor: ["#ff3355","#3333ff","#000044","#e2b35b"],
              data: [<?php echo $rave ?? ''; ?>,<?php echo $paystack ?? ''; ?>,<?php echo $payant; ?>,<?php echo $banktransfer ?? ''; ?>]
            }
          ]
        },
        options: {
          legend: { display: false },
          title: {
            display: true,
            text: 'Payment Chart Total ('+<?php echo $total_fund; ?>+')'
          }
        }
    });


    // line second
}); 

// Horizental Bar Chart
    new Chart(document.getElementById("bar-chart-horizontal"), {
        type: 'horizontalBar',
        data: {
          labels: ["Data", "Airtime", "Tv", "Auto Charge", "Sim Swap"],
          datasets: [
            {
              label: "Count",
              backgroundColor: ["#0000FF","#00FF00","#e40503", "#229944", "#382710"],
              data: [<?php echo $data ?? '' ; ?>,<?php echo $airtime ?? '' ; ?>,<?php echo $tv ?? '' ; ?>, <?php echo $autocharge ?? '' ; ?>, <?php echo $simswap ?? '0' ; ?>]
            }
          ]
        },
        options: {
          legend: { display: false },
          title: {
            display: true,
            text: 'Transactions Chart'
          }
        }
    });

                function openRightMenu() {
                    document.getElementById("rightMenu").style.display = "block";
                }
                function closeRightMenu() {
                    document.getElementById("rightMenu").style.display = "none";
                }

                $(document).ready(function() {
                    $('#styleOptions').styleSwitcher();
                });

            $('.dropdown-toggle').dropdown()
   
            
                
            </script>
        

            
      </div>
      <!-- Wrapper -->
      
    </body>

</html>