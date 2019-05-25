<?php
    define('DB_SERVER', 'localhost');
    define('DB_USERNAME', '');
    define('DB_PASSWORD', '');
    define('DB_NAME', '');
     
     date_default_timezone_set('America/New_York');
     
    $link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
     
    if($link === false)
    {
        die("ERROR: Could not connect. " . mysqli_connect_error());
    }
    
    $site_status = $site_message = $site_title = $site_footer = $contact_email = $contact_status = '';
    
    $sql = "SELECT site_status, site_message, site_title, site_footer, contact_email, contact_status FROM site_settings";
    if ($stmt = mysqli_prepare($link, $sql))
    {
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $var1, $var2, $var3, $var4, $var5, $var6);
        
        while (mysqli_stmt_fetch($stmt))
        {
            $site_status = $var1;
            $site_message = $var2;
            $site_title = $var3;
            $site_footer = $var4;
            $contact_email = $var5;
            $contact_status = $var6;
        }
    }
    mysqli_stmt_close($stmt);
    
    $string_limit = 250;
?>
