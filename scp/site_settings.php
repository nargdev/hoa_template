<?php
        if ($_SESSION['level'] <= '3')
        {
            header('location: index.php');
            exit;
        }

        if ($_SERVER["REQUEST_METHOD"] == "POST")
        {
            $ntitle = trim($_POST['website_title']); 
            $nfooter = trim($_POST['website_footer']);
            $nemail = trim($_POST['contact_email']);
            $ncstatus = trim($_POST['contact_mode']);
            $nsstatus = trim($_POST['website_mode']);
            $nmessage = trim($_POST['website_message']);


            if (empty(trim($_POST["psw"])))
            {
                $errors[] = "Please enter your password to save changes.";
            } else{
                $psw = trim($_POST["psw"]);
            }

            if (empty($errors))
            {
                $sql = "SELECT id, username, password FROM users WHERE username = ?";
                if ($stmt = mysqli_prepare($link, $sql))
                {
                    mysqli_stmt_bind_param($stmt, "s", $param_username);
                    
                    $param_username = $uname;
                    
                    if (mysqli_stmt_execute($stmt))
                    {
                        mysqli_stmt_store_result($stmt);
                        
                        if (mysqli_stmt_num_rows($stmt) == 1)
                        {
                            mysqli_stmt_bind_result($stmt, $id, $uname, $hashed_password);
                            if (mysqli_stmt_fetch($stmt))
                            {
                                if (password_verify($psw, $hashed_password))
                                {
                                    $sql = "UPDATE site_settings SET site_status = '$nsstatus', site_message = '$nmessage', site_title = '$ntitle', site_footer = '$nfooter', contact_email = '$nemail', contact_status = '$ncstatus' WHERE id = '1'";
                                    if (mysqli_query($link, $sql))
                                    {
                                        $success[] = "Information updated successfully!";

                                        $site_status = $nsstatus;
                                        $site_message = $nmessage;
                                        $site_title = $ntitle;
                                        $site_footer = $nfooter;
                                        $contact_email = $nemail;
                                        $contact_status = $ncstatus;
                                    }
                                    else
                                    {
                                        $errors[] = "Unable to update website information. If this issue continues, please contact an Admin.";
                                    }
                                }
                                else
                                {
                                    $errors[] = "The password you entered was not valid. No changes were made.";
                                }
                            }
                        }
                    }
                    else
                    {
                        $errors[] = "Oops! Something went wrong. Please try again later.";
                    }
                }
                mysqli_stmt_close($stmt);
            }
        }
    ?>
    <!-- Header -->
    <header class="w3-container" style="padding-top:22px">
        <h5><b><i class="fas fa-cogs fa-fw"></i> Website Settings</b></h5>
    </header>
    
    <div class="w3-container w3-dark-grey w3-padding-32">
        <div class="w3-row">
            <form action="index.php?page=Site_Settings" method="post">
                <div class="w3-container">
                    <?php if (!empty($errors)) : ?>
                    <div class="w3-panel w3-red">
                        <p>
                        <?php echo implode('</p></div><div class="w3-panel w3-red"><p>', $errors); ?>
                        </p>
                    </div>
                    <?php elseif (!empty($success)) : ?>
                    <div class="w3-panel w3-green">
                        <p>
                        <?php echo implode('</p></div><div class="w3-panel w3-green"><p>', $success); ?>
                        </p>
                    </div>
                    <?php endif; ?>
                    <div class="w3-row">
                        <h5 class="w3-bottombar w3-border-green">Basic Settings</h5>
                        <div class="w3-col s6 w3-padding">
                            <label>Website Title:</label>
                            <input class="w3-input w3-border w3-light-grey" type="text" name="website_title" value="<?php echo $site_title; ?>">
                            <br>
                            <label>Footer:</label>
                            <textarea class="w3-input w3-border" style="resize:none" name="website_footer"><?php echo $site_footer; ?></textarea>
                            <br>
                        </div>
                        <div class="w3-col s6 w3-padding">
                            <label>Contact Us E-mail:</label>
                            <input class="w3-input w3-border w3-light-grey" type="text" name="contact_email" value="<?php echo $contact_email; ?>">
                            <br>
                            <label>Contact Us Form:</label>
                            <select class="w3-select" name="contact_mode">
                                <option value="1" <?php if ($contact_status == 1) : echo 'selected'; endif; ?>>Offline</option>
                                <option value="2" <?php if ($contact_status == 2) : echo 'selected'; endif; ?>>Online</option>
                            </select>
                        </div>
                    </div>
                </div>
                <br>
                <div class="w3-container">
                    <h5 class="w3-bottombar w3-border-orange">Website Status</h5>
                    <div class="w3-row">
                        <div class="w3-col s6 w3-padding">
                            <label>Maintenance Mode:</label>
                            <select class="w3-select" name="website_mode">
                                <option value="1" <?php if ($site_status == 1) : echo 'selected'; endif; ?>>Online</option>
                                <option value="2" <?php if ($site_status == 2) : echo 'selected'; endif; ?>>Offline</option>
                            </select>
                        </div>
                        <div class="w3-col s6 w3-padding">
                            <label>Maintenance Mode Message:</label>
                            <textarea class="w3-input w3-border" style="resize:none" name="website_message"><?php echo $site_message; ?></textarea>
                        </div>
                    </div>
                </div>
                <br>
                <div class="w3-container">
                    <h5 class="w3-bottombar w3-border-red">Current Password</h5>
                    <div class="w3-center">
                        <label><b>In order to save these changes, please input your current password:</b></label>
                        <input class="w3-input w3-border w3-light-grey" style="display: block; margin: 0 auto; width: 350px;" type="password" name="psw" required>
                        <button class="w3-button w3-section w3-green w3-ripple"> Submit</button></p>
                    </div>
                </div>
            </form>
        </div>
    </div>