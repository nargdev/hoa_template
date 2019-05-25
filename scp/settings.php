    <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST")
        {
            $nuname = $nfname = $nlname = $naddress = $nemail = $npass = $psw = '';

            if (empty(trim($_POST["username"])))
            {
                $nuname = $uname;
            }
            else
            {
                $nuname = trim($_POST["username"]);
            }
            if (empty(trim($_POST["firstname"])))
            {
                $nfname = $fname;
            }
            else
            {
                $nfname = trim($_POST["firstname"]);
            }
            if (empty(trim($_POST["lastname"])))
            {
                $nlname = $lname;
            }
            else
            {
                $nlname = trim($_POST["lastname"]);
            }
            if (empty(trim($_POST["address"])))
            {
                $naddress = $address;
            }
            else
            {
                $naddress = trim($_POST["address"]);
            }
            if (empty(trim($_POST["email"])))
            {
                $nemail = $email;
            }
            else
            {
                $nemail = trim($_POST["email"]);
            }

            if (!empty(trim($_POST["newpsw"])))
            {
                if (trim($_POST['newpsw']) == trim($_POST['newpsw2']))
                {
                    $npass = trim($_POST['newpsw']);
                }
                else
                {
                    $errors[] = "New passwords do not match!";
                }
            }

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
                                    if ($stmt = mysqli_prepare($link, "UPDATE users SET username = ?, firstname = ?, lastname = ?, email = ?, address = ? WHERE id = ?"))
                                    {
                                        mysqli_stmt_bind_param($stmt, "sssssi", $nuname, $nfname, $nlname, $nemail, $naddress, $id);
                                        if (mysqli_stmt_execute($stmt))
                                        {
                                            $success[] = "Information updated successfully!";
                                            $_SESSION['username'] = $nuname;
                                            
                                            $uname = $nuname;
                                            $fname = $nfname;
                                            $lname = $nlname;
                                            $address = $naddress;
                                            $email = $nemail;
                                            
                                            mysqli_stmt_close($stmt);
                                        }
                                        else
                                        {
                                            $errors[] = "Unable to update information. If this issue continues, please contact an Admin.";
                                        }
                                    }

                                    if (!empty($npass))
                                    {
                                        $param_password = password_hash($npass, PASSWORD_DEFAULT);
                                        if ($stmt = mysqli_prepare($link, "UPDATE users SET password = ? WHERE id = ?"))
                                        {
                                            mysqli_stmt_bind_param($stmt, "si", $param_password, $id);
                                            if (mysqli_stmt_execute($stmt))
                                            {
                                                $success[] = "Password updated successfully!";
                                            }
                                            else
                                            {
                                                $errors[] = "Unable to update password. If this issue continues, please contact an Admin.";
                                            }
                                        }
                                    }
                                }
                                else
                                {
                                    $errors[] = "The password you entered was not valid. No changes were made.";
                                }
                            }
                        }
                        else
                        {
                            $errors[] = "No account found with that username. If this continues, please contact an admin.";
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
        <h5><b><i class="fas fa-cogs fa-fw"></i> My Settings</b></h5>
    </header>
    
    <div class="w3-container w3-dark-grey w3-padding-32">
        <div class="w3-row">
            <form action="index.php?page=Settings" method="post">
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
                        <h5 class="w3-bottombar w3-border-green">Contact Information</h5>
                        <div class="w3-col s6 w3-padding">
                            <label>First Name:</label>
                            <input class="w3-input w3-border w3-light-grey" type="text" name="firstname" value="<?php echo $fname; ?>">
                            <br>
                            <label>Last Name:</label>
                            <input class="w3-input w3-border w3-light-grey" type="text" name="lastname" value="<?php echo $lname; ?>">
                            <br>
                            <label>Address:</label>
                            <input class="w3-input w3-border w3-light-grey" type="text" name="address" value="<?php echo $address; ?>">
                            <br>
                        </div>
                        <div class="w3-col s6 w3-padding">
                            <label>Username:</label>
                            <input class="w3-input w3-border w3-light-grey" type="text" name="username" value="<?php echo $uname; ?>">
                            <br>
                            <label>E-mail:</label>
                            <input class="w3-input w3-border w3-light-grey" type="text" name="email" value="<?php echo $email; ?>">
                            <br>
                        </div>
                    </div>
                </div>
                <br>
                <div class="w3-container">
                    <h5 class="w3-bottombar w3-border-orange">Change Password</h5>
                    <p>Only fill out this section IF you wish to change your password.</p>
                    <div class="w3-row">
                        <div class="w3-col s6 w3-padding">
                            <label>New Password:</label>
                            <input class="w3-input w3-border w3-light-grey" type="password" name="newpsw">
                        </div>
                        <div class="w3-col s6 w3-padding">
                            <label>Confirm Password:</label>
                            <input class="w3-input w3-border w3-light-grey" type="password" name="newpsw2">
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