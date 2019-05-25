<?php
    require_once "config/config.php";
    require_once "config/functions.php";
    session_start();

    if ($_GET['action'] == 'Logout')
    {
        $_SESSION = array();
        session_destroy();
    }

    if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true)
    {
        $logged = true;
    }

    $username = $password = "";
    $login_err = array();

    if ($_SERVER["REQUEST_METHOD"] == "POST")
    {
        if (empty(trim($_POST["usrname"])))
        {
            $login_err[] = "Please enter username.";
        }
        else
        {
            $username = trim($_POST["usrname"]);
        }
    
        if (empty(trim($_POST["psw"])))
        {
            $login_err[] = "Please enter your password.";
        } else{
            $password = trim($_POST["psw"]);
        }
    
        if (empty($username_err) && empty($password_err))
        {
            $sql = "SELECT id, username, password, level FROM users WHERE username = ?";
            
            if ($stmt = mysqli_prepare($link, $sql))
            {
                mysqli_stmt_bind_param($stmt, "s", $param_username);
                
                $param_username = $username;
                
                if (mysqli_stmt_execute($stmt))
                {
                    mysqli_stmt_store_result($stmt);
                    
                    if (mysqli_stmt_num_rows($stmt) == 1)
                    {
                        mysqli_stmt_bind_result($stmt, $id, $username, $hashed_password, $ulevel);
                        if (mysqli_stmt_fetch($stmt))
                        {
                            if (password_verify($password, $hashed_password))
                            {
                                session_start();

                                $_SESSION["loggedin"] = true;
                                $_SESSION["id"] = $id;
                                $_SESSION["username"] = $username;
                                $_SESSION["level"] = $ulevel;
                                
                                header("location: index.php");
                            } else{
                                $login_err[] = "The password you entered was not valid.";
                            }
                        }
                    }
                    else
                    {
                        $login_err[] = "No account found with that username.";
                    }
                }
                else
                {
                    $login_err[] = "Oops! Something went wrong. Please try again later.";
                }
            }
            mysqli_stmt_close($stmt);
        }
    }
?>
<!DOCTYPE html>
<html>
<title><?php echo $site_title; ?></title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lato">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel='stylesheet' href='https://use.fontawesome.com/releases/v5.5.0/css/all.css' integrity='sha384-B4dIYHKNBt8Bc12p+WXckhzcICo0wtJAoU8YZTY5qE0Id1GSseTk6S+L3BlXeVIU' crossorigin='anonymous'>
<style>
body,h1,h2,h3,h4,h5,h6 {font-family: "Lato", sans-serif;}
body, html {
    height: 100%;
    color: #777;
    line-height: 1.8;
}

/* Create a Parallax Effect */
.bgimg-1, .bgimg-2, .bgimg-3, .bgimg-news {
    background-attachment: fixed;
    background-position: center;
    background-repeat: no-repeat;
    background-size: cover;
}

/* First image (Logo. Full height) */
.bgimg-1 {
    background-image: url('images/mainimage.jpg');
    min-height: 100%;
}

/* Second image (Portfolio) */
.bgimg-2 {
    background-image: url("images/boardimage.jpg");
    min-height: 400px;
}

/* Third image (Contact) */
.bgimg-3 {
    background-image: url("images/contactimage.jpg");
    min-height: 400px;
}

.bgimg-news2 {
    background-image: url("images/community_news.jpg");
    min-height: 400px;
}

.w3-wide {letter-spacing: 10px;}
.w3-hover-opacity {cursor: pointer;}

/* Turn off parallax scrolling for tablets and phones */
@media only screen and (max-device-width: 1600px) {
    .bgimg-1, .bgimg-2, .bgimg-3, .bgimg-news {
        background-attachment: scroll;
        min-height: 400px;
    }
}
</style>
<body <?php if ($_SERVER["REQUEST_METHOD"] == "POST" && $_POST['form'] == 'login') : ?> onload="document.getElementById('loginBox').style.display='block'" <?php endif; ?>>

<!-- Navbar (sit on top) -->
<div class="w3-top">
  <div class="w3-bar w3-white" id="myNavbar">
    <a class="w3-bar-item w3-button w3-hover-black w3-hide-medium w3-hide-large w3-right" href="javascript:void(0);" onclick="toggleFunction()" title="Toggle Navigation Menu">
      <i class="fa fa-bars"></i>
    </a>
    <a href="index.php#home" class="w3-bar-item w3-button">HOME</a>
    <a href="index.php#news" class="w3-bar-item w3-button w3-hide-small"><i class="fas fa-newspaper"></i> COMMUNITY NEWS</a>
        <a href="index.php#members" class="w3-bar-item w3-button w3-hide-small"><i class="fa fa-group"></i> BOARD MEMBERS</a>
    <a href="index.php#contact" class="w3-bar-item w3-button w3-hide-small"><i class="fa fa-envelope"></i> CONTACT</a>
    <?php if ($_SESSION['loggedin'] == true) : ?>
        <a href="index.php?action=Logout" class="w3-button w3-hover-red w3-bar-item w3-hide-small w3-right">
            <i class="fas fa-sign-out-alt"></i> Logout  
        </a>
        <?php if ($_SESSION['level'] == 0) : ?>
        <a href="#" class="w3-bar-item w3-hide-small w3-right w3-button w3-hover-red">
            <i class="fa fa-clock-o"></i> Account Pending Approval
        </a>
        <?php else : ?>
        <a href="scp/index.php?page=Home" class="w3-bar-item w3-hide-small w3-right w3-button w3-hover-red">
            <i class="fas fa-cogs"></i> Account Settings
        </a>
        <a href="scp/index.php?page=Payment" class="w3-bar-item w3-hide-small w3-right w3-button w3-hover-red">
            <i class="fas fa-money-check-alt"></i> HOA Dues 
        </a>
        <?php endif; ?>
    <?php else : ?>
    <a href="#" class="w3-bar-item w3-button w3-hide-small w3-right w3-hover-red" onclick="document.getElementById('loginBox').style.display='block'">
      <i class=""></i> Login
    </a>
    <?php endif; ?>
  </div>

  <!-- Navbar on small screens -->
  <div id="navDemo" class="w3-bar-block w3-white w3-hide w3-hide-large w3-hide-medium">
    <a href="index.php#news" class="w3-bar-item w3-button" onclick="toggleFunction()">COMMUNITY NEWS</a>
    <a href="index.php#members" class="w3-bar-item w3-button" onclick="toggleFunction()">BOARD MEMBERS</a>
    <a href="index.php#contact" class="w3-bar-item w3-button" onclick="toggleFunction()">CONTACT</a>
    <?php if ($_SESSION['loggedin'] == true && $_SESSION['level'] == 0) : ?>
    <a href="#" class="w3-bar-item w3-button">Account Pending Approval</a>
    <a href="index.php?action=Logout" class="w3-bar-item w3-button">Logout</a>
    <?php elseif ($_SESSION['loggedin'] == true && $_SESSION['level'] >= 1) : ?>
    <a href="scp/index.php" class="w3-bar-item w3-button">Account Settings</a>
    <a href="scp/index.php?page=Payment" class="w3-bar-item w3-button">HOA Dues</a>
    <a href="index.php?action=Logout" class="w3-bar-item w3-button">Logout</a>
    <?php else : ?>
    <a href="#" class="w3-button" onclick="document.getElementById('loginBox').style.display='block'">Login</a>
    <?php endif; ?>
  </div>
</div>

<?php
    if ($site_status == 1)
    {
        include 'maintenance_mode.php';
    }
    else if ($_GET['page'] == "News")
    {
        include 'news.php';
    }else{
?>
<!-- First Parallax Image with Logo Text -->
<div class="bgimg-1 w3-display-container w3-opacity-min" id="home">
  <div class="w3-display-bottommiddle w3-padding-64" style="white-space:nowrap;">
    <span class="w3-center w3-padding-large w3-black w3-xlarge w3-wide w3-animate-opacity w3-hide-small"><?php echo $site_title; ?></span>
  </div>
</div>

<!-- Container (About Section) -->
<div class="w3-content w3-container w3-padding-64" id="news">
    <h3 class="w3-center">Community News</h3>
    <p class="w3-center"><em>Stay up to date on what's going on!</em></p>

    <div class="w3-row">
    <?php
        $sql = "SELECT id, title, user_id, created_at, body FROM posts WHERE published = '1' ORDER BY id DESC LIMIT 3";
        if ($stmt = mysqli_prepare($link, $sql))
        {
            if (mysqli_stmt_execute($stmt))
            {
                mysqli_stmt_store_result($stmt);
                if (mysqli_stmt_num_rows($stmt) > 0)
                {
                    mysqli_stmt_bind_result($stmt, $bid, $btitle, $baid, $bcreated, $bbody);
                    while(mysqli_stmt_fetch($stmt))
                    {
                        $title_length = strlen($btitle);
                        $word_length = strlen($bbody);
                        echo '
                        <div class="w3-third w3-container w3-padding-16">
                            <div class="w3-left">'. substr($btitle, 0, 20) . (($title_length > 20) ? "..." : "") .'<br>'. getAuthor($link, $baid) .'</div>
                            <div class="w3-clear"></div>
                            <p>'. substr($bbody, 0, $string_limit) . (($word_length > $string_limit) ? "..." : "") .'</p>
                            '. (($word_length > $string_limit) ? "<div class=\"w3-right\"><a href=\"?page=News&action=View&id=$bid\">read more</a></div>" : "") .'
                        </div>';
                    }
                }
            }
        }
    ?>
    </div>
    <a href="?page=News" class="w3-button w3-block w3-teal">View All Post</a>
</div>

<!-- Second Parallax Image with Portfolio Text -->
<div class="bgimg-2 w3-display-container w3-opacity-min">
  <div class="w3-display-middle">
    <span class="w3-xxlarge w3-text-white w3-wide">BOARD MEMBERS</span>
  </div>
</div>

<!-- Container (Portfolio Section) -->
<div class="w3-content w3-container w3-padding-64" id="members">
    <div class="w3-third">
        <div class="w3-container w3-padding-16">
            <div class="w3-card-4">
                <img src="images/img_avatar3.png" alt="Avatar" style="width:100%">
                <div class="w3-container w3-center">
                    <p>Scott R. / President</p>
                </div>
            </div>
        </div>
    </div>
    <div class="w3-third">
        <div class="w3-container w3-padding-16">
            <div class="w3-card-4">
                <img src="images/img_avatar3.png" alt="Avatar" style="width:100%">
                <div class="w3-container w3-center">
                    <p>Sarah W. / Vice President</p>
                </div>
            </div>
        </div>
    </div>
    <div class=" w3-third">
        <div class="w3-container w3-padding-16">
            <div class="w3-card-4">
                <img src="images/img_avatar3.png" alt="Avatar" style="width:100%">
                <div class="w3-container w3-center">
                    <p>Kelly C. / Treasurer</p>
                </div>
            </div>
        </div>
    </div>
    <div class=" w3-third">
        <div class="w3-container w3-padding-16">
            <div class="w3-card-4">
                <img src="images/img_avatar3.png" alt="Avatar" style="width:100%">
                <div class="w3-container w3-center">
                    <p>Andrew B. / Architectual & Landscaping</p>
                </div>
            </div>
        </div>
    </div>
    <div class=" w3-third">
        <div class="w3-container w3-padding-16">
            <div class="w3-card-4">
                <img src="images/img_avatar3.png" alt="Avatar" style="width:100%">
                <div class="w3-container w3-center">
                    <p>Wendy M. / Secretary</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal for full size images on click-->
<div id="modal01" class="w3-modal w3-black" onclick="this.style.display='none'">
  <span class="w3-button w3-large w3-black w3-display-topright" title="Close Modal Image"><i class="fa fa-remove"></i></span>
  <div class="w3-modal-content w3-animate-zoom w3-center w3-transparent w3-padding-64">
    <img id="img01" class="w3-image">
    <p id="caption" class="w3-opacity w3-large"></p>
  </div>
</div>

<?php
    
    if ($contact_status == 2)
    {
?>
<!-- Third Parallax Image with Portfolio Text -->
<div class="bgimg-3 w3-display-container w3-opacity-min">
  <div class="w3-display-middle">
     <span class="w3-xxlarge w3-text-dark-gray w3-wide">CONTACT US</span>
  </div>
</div>

<?php
    function _e( $string )
    {
        return htmlentities($string, ENT_QUOTES, 'UTF-8', false);
    }
    
    $whitelist = array('Name', 'Email', 'Message');
    $email_address = $contact_email;
    $subject = 'New Contact Form Submission';
    
    $errors = array();
    $fields = array();
    
    if (!empty($_POST))
    {
        if (strtolower($_POST['human']) !== 'woodcreek reserve')
        {
            $errors[] = 'Security Check Failed.';
        }
        
        if (!empty($_POST['email']) && !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL))
        {
            $errors[] = 'That is not a valid email address.';
        }
        
        foreach ($whitelist as $key)
        {
            $fields[$key] = $_POST[$key];
        }
        
        foreach ($fields as $field => $data)
        {
            if (empty($data))
            {
                $errors[] = 'Please enter your ' . $field . '.';
            }
        }
        
        if (empty($errors))
        {
            $subject = 'Website Form Message';
            
            $headers = "From: " . strip_tags($fields['Email']) . "\r\n";
            $headers .= "Reply-To: ". strip_tags($fields['Email']) . "\r\n";
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
            
            $message = '<html><body>';
            $message .= 'We have received a message from '. $fields['Name'] .' ('. $fields['Email'] .')<br><br>';
            $message .= 'Message:<br> '. $fields['Message'];
            $message .= '<html><body>';
            $message .= "</body></html>";

            $sent = mail($email_address, $subject, $message, $headers);
        }
    }
?>

<!-- Container (Contact Section) -->
<div class="w3-content w3-container w3-padding-64" id="contact">
  <h3 class="w3-center">Question or Concern?</h3>
  <p class="w3-center"><em>We'd love your feedback!</em></p>

    <?php if (!empty($errors)) : ?>
        <div class="w3-panel w3-red">
            <p>
                <?php echo implode('</p></div><div class="w3-panel w3-red"><p>', $errors); ?>
            </p>
        </div>
    <?php elseif ($sent) : ?>
        <div class="w3-panel w3-green">
            <p>Thank you! Your message has been sent to the HOA team.</p>
        </div>
    <?php endif; ?>
    <i class="fa fa-envelope fa-fw w3-hover-text-black w3-xlarge w3-center"></i> Email: <?php echo $contact_email; ?><br>
      <form role="form" method="post" action="index.php#contact">
        <div class="w3-row-padding" style="margin:0 -16px 8px -16px;">
          <div class="w3-half">
            <input class="w3-input w3-border" type="text" placeholder="Name" required name="Name" value="<?php echo isset($fields['Name']) ? _e($fields['Name']) : '' ?>">
          </div>
          <div class="w3-half">
            <input class="w3-input w3-border" type="text" placeholder="Email" required name="Email" value="<?php echo isset($fields['Email']) ? _e($fields['Email']) : '' ?>">
          </div>
        </div>
        <input class="w3-input w3-border" type="text" placeholder="Message" required name="Message" value="<?php echo isset($fields['Message']) ? _e($fields['Message']) : '' ?>"><br>
            <div class="w3-half">
                <label for="security">Security Question:</label>
                <input class="w3-input w3-border" id="security" type="text" placeholder="What is the Community name?" required name="human">
            </div>
            <div class="w3-half">
                <button class="w3-button w3-black w3-right w3-section" type="submit">
                  <i class="fa fa-paper-plane"></i> SEND MESSAGE
                </button>
            </div>
      </form>
</div>
<?php
    }
    }
?>
<!-- Footer -->
<footer class="w3-center w3-black w3-padding-64 w3-opacity w3-hover-opacity-off">
  <a href="#home" class="w3-button w3-light-grey"><i class="fa fa-arrow-up w3-margin-right"></i>To the top</a>
    <?php echo $site_footer; ?>
    <p>
        Developed & Modified by <a href="https://www.dubosesolutions.com" target="_blank">DuBose Solutions</a><br>
        Powered by <a href="https://www.w3schools.com/w3css/default.asp" target="_blank">w3.css</a><br>
        Icons made by <a href="https://www.freepik.com/" title="Freepik">Freepik</a> from <a href="https://www.flaticon.com/" title="Flaticon">www.flaticon.com</a> is licensed by <a href="http://creativecommons.org/licenses/by/3.0/" title="Creative Commons BY 3.0" target="_blank">CC 3.0 BY</a>
    </p>
</footer>

    <div id="loginBox" class="w3-modal">
        <div class="w3-modal-content w3-card-4 w3-animate-zoom" style="max-width:600px">
            <div class="w3-center"><br>
                <span onclick="document.getElementById('loginBox').style.display='none'" class="w3-button w3-xlarge w3-transparent w3-display-topright" title="Close Modal">×</span>
                <h3>Woodcreek Reserve</h3>
            </div>
            <?php if (!empty($login_err)) : ?>
            <div class="w3-panel w3-red">
                <p>
                <?php echo implode('</p></div><div class="w3-panel w3-red"><p>', $login_err); ?>
                </p>
            </div>
            <?php elseif ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_SESSION['id'])) : ?>
            <div class="w3-panel w3-green">
                <p>Logged In! Reloading page now.</p>
            </div>
            <?php endif; ?>
            <form class="w3-container" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="w3-section">
                    <label><b>Username</b></label>
                    <input class="w3-input w3-border w3-margin-bottom" type="text" placeholder="Enter Username" name="usrname" value="<?php echo $username; ?>" required>
                    <label><b>Password</b></label>
                    <input class="w3-input w3-border" type="password" placeholder="Enter Password" name="psw" required>
                    <button class="w3-button w3-block w3-green w3-section w3-padding" type="submit">Login</button>
                    <!--<input class="w3-check w3-margin-top" type="checkbox" checked="checked"> Remember me-->
                </div>
                <input type="hidden" name="form" value="login">
            </form>
            <div class="w3-container w3-border-top w3-padding-16 w3-light-grey">
                <button onclick="document.getElementById('loginBox').style.display='none'" type="button" class="w3-button w3-red">Cancel</button>
                <span class="w3-right w3-padding w3-hide-small">Forgot <a href="#">password?</a></span>
            </div>
        </div>
    </div>
 
<script>
// Modal Image Gallery
function onClick(element) {
  document.getElementById("img01").src = element.src;
  document.getElementById("modal01").style.display = "block";
  var captionText = document.getElementById("caption");
  captionText.innerHTML = element.alt;
}

// Used to toggle the menu on small screens when clicking on the menu button
function toggleFunction() {
    var x = document.getElementById("navDemo");
    if (x.className.indexOf("w3-show") == -1) {
        x.className += " w3-show";
    } else {
        x.className = x.className.replace(" w3-show", "");
    }
}
</script>

</body>
</html>
<?php
    mysqli_close($link);
?>