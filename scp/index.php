<?php
    require_once "../config/config.php";
    require_once "../config/functions.php";

    session_start();
    
    $errors = array();
    $success = array();

    
    if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["level"] == 0)
    {
        header("location: ../index.php");
        exit;
    }

    $id = $_SESSION['id'];
    $uname = $fname = $lname = $address = $email = $ulevel = '';

    $sql = "SELECT username, firstname, lastname, address, email, level FROM users WHERE id = ?";
    if ($stmt = mysqli_prepare($link, $sql))
    {
        mysqli_stmt_bind_param($stmt, "i", $id);
        if (mysqli_stmt_execute($stmt))
        {
            mysqli_stmt_bind_result($stmt, $var1, $var2, $var3, $var4, $var5, $var6);
            
            while (mysqli_stmt_fetch($stmt))
            {
                $uname = $var1;
                $fname = $var2;
                $lname = $var3;
                $address = $var4;
                $email = $var5;
                $ulevel = $var6;
            }
        }
    }
    mysqli_stmt_close($stmt);

	$apages = array(
    'Home' => 'news.php',
    'News' => 'news.php',
    'News_Admin' => 'news_edit.php',
    'Payment' => 'payment.php',
    'Settings' => 'settings.php',
    'Users' => 'users.php',
    'Site_Settings' => 'site_settings.php');

	$page = $_GET['page'];
?>

<!DOCTYPE html>
<html>
<title>HOA User Panel</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Raleway">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.5.0/css/all.css" integrity="sha384-B4dIYHKNBt8Bc12p+WXckhzcICo0wtJAoU8YZTY5qE0Id1GSseTk6S+L3BlXeVIU" crossorigin="anonymous">
<style>
html,body,h1,h2,h3,h4,h5 {font-family: "Raleway", sans-serif}
</style>
<body class="w3-light-grey">

<!-- Top container -->
<div class="w3-bar w3-top w3-black w3-large" style="z-index:4">
  <button class="w3-bar-item w3-button w3-hide-large w3-hover-none w3-hover-text-light-grey" onclick="w3_open();"><i class="fa fa-bars"></i> Menu</button>
  <span class="w3-bar-item w3-right">Woodcreek Reserve HOA</span>
</div>

<!-- Sidebar/menu -->
<nav class="w3-sidebar w3-collapse w3-white w3-animate-left" style="z-index:3;width:300px;" id="mySidebar"><br>
  <div class="w3-container w3-row">
    <div class="w3-col s4">
      <img src="../images/avatar2.png" class="w3-circle w3-margin-right" style="width:46px">
    </div>
    <div class="w3-col s8 w3-bar">
      <span>Welcome, <strong><?php echo $_SESSION['username']; ?></strong></span><br>
      <a href="../index.php" class="w3-bar-item w3-button"><i class="fa fa-home"></i></a>
      <a href="index.php?page=Settings" class="w3-bar-item w3-button"><i class="fa fa-user"></i></a>
      <a href="../index.php?action=Logout" class="w3-bar-item w3-button"><i class="fas fa-sign-out-alt" alt="Logout"></i></a>
    </div>
  </div>
  <hr>
  <div class="w3-container">
    <h5>Dashboard</h5>
  </div>
  <div class="w3-bar-block">
    <a href="#" class="w3-bar-item w3-button w3-padding-16 w3-hide-large w3-dark-grey w3-hover-black" onclick="w3_close()" title="close menu"><i class="fa fa-remove fa-fw"></i> Close Menu</a>
<!--    <a href="index.php?page=Home" class="w3-bar-item w3-button w3-padding <?php if ($page == '' || $page == 'Home') : echo 'w3-blue'; endif; ?>"><i class="fa fa-users fa-fw"></i> Overview</a>-->
    <a href="index.php?page=News" class="w3-bar-item w3-button w3-padding <?php if ($page == 'News') : echo 'w3-blue'; endif; ?>"><i class="fa fa-bell fa-fw"></i> News</a>
    <a href="index.php?page=Payment" class="w3-bar-item w3-button w3-padding <?php if ($page == 'Payment') : echo 'w3-blue'; endif; ?>"><i class="fas fa-money-check-alt fa-fw"></i> Dues</a>
    <a href="index.php?page=Settings" class="w3-bar-item w3-button w3-padding <?php if ($page == 'Settings') : echo 'w3-blue'; endif; ?>"><i class="fa fa-cog fa-fw"></i> Account Settings</a><br><br>
  </div>
  <?php if ($ulevel >= 2) : ?>
  <div class="w3-container">
    <h5>Administration</h5>
  </div>
  <div class="w3-bar-block">
    <a href="index.php?page=News_Admin" class="w3-bar-item w3-button w3-padding <?php if ($page == 'News_Admin') : echo 'w3-blue'; endif; ?>"><i class="fa fa-bell fa-fw"></i> Edit News</a>
  <?php if ($ulevel >= 3) : ?>
    <a href="index.php?page=Users" class="w3-bar-item w3-button w3-padding <?php if ($page == 'Users') : echo 'w3-blue'; endif; ?>"><i class="fas fa-user-cog"></i> Edit Users</a>
    <a href="index.php?page=Site_Settings" class="w3-bar-item w3-button w3-padding <?php if ($page == 'Site_Settings') : echo 'w3-blue'; endif; ?>"><i class="fa fa-cog fa-fw"></i> Website Settings</a><br><br>
  </div>
  <?php endif; ?>
  <?php endif; ?>
</nav>


<!-- Overlay effect when opening sidebar on small screens -->
<div class="w3-overlay w3-hide-large w3-animate-opacity" onclick="w3_close()" style="cursor:pointer" title="close side menu" id="myOverlay"></div>

<!-- !PAGE CONTENT! -->
<div class="w3-main" style="margin-left:300px;margin-top:43px;">

    <?php
    	if ($page)
    	{
    		if (existsInArray($page, $apages))
    		{
    			include($apages[$page]);
    		}
    		else
    		{
        		include('news.php');
    		}
    	}
    	else
    	{
    		include('news.php');
    	}
    ?>

  <!-- Footer -->
  <footer class="w3-container w3-padding-16 w3-light-grey">
    <h4>FOOTER</h4>
    <p>
    Developed & Modified by <a href="https://www.dubosesolutions.com" target="_blank">DuBose Solutions</a><br>
    Powered by <a href="https://www.w3schools.com/w3css/default.asp" target="_blank">w3.css</a><br>
    Icons made by <a href="https://www.freepik.com/" title="Freepik">Freepik</a> from <a href="https://www.flaticon.com/" title="Flaticon">www.flaticon.com</a> is licensed by <a href="http://creativecommons.org/licenses/by/3.0/" title="Creative Commons BY 3.0" target="_blank">CC 3.0 BY</a>
    </p>
  </footer>

  <!-- End page content -->
</div>

        <script>
            var mySidebar = document.getElementById("mySidebar");
            var overlayBg = document.getElementById("myOverlay");
            
            function w3_open() {
                if (mySidebar.style.display === 'block')
                {
                    mySidebar.style.display = 'none';
                    overlayBg.style.display = "none";
                }
                else
                {
                    mySidebar.style.display = 'block';
                    overlayBg.style.display = "block";
                }
            }
            
            function w3_close()
            {
                mySidebar.style.display = "none";
                overlayBg.style.display = "none";
            }
        </script>
    </body>
</html>
<?php
    mysqli_close($link);
?>
