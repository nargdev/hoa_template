    <?php
        if ($ulevel < '3')
        {
            header('location: ../index.php');
            exit;
        }

        $levels = array('Pending', 'Member', 'Board Member', 'President', 'Administrator');
        
        $action = $_GET['action'];
        $id = $_GET['id'];
        
        if ($action == 'ApproveUser')
        {
            $sql = "UPDATE users SET level = '1' WHERE id = ?";
            if ($stmt = mysqli_prepare($link, $sql))
            {
                mysqli_stmt_bind_param($stmt, "i", $var1);
                
                $var1 = $id;

                if (mysqli_stmt_execute($stmt))
                {
                    $success[] = "User account has been approved!";
                }
                else
                {
                    $errors[] = "There was an issue approving the user account. Check the information and try again.";
                }
            }
            else
            {
                $errors[] = "Unable to approve the users account. If this issue continues, please contact an Admin.";
            }
            mysqli_stmt_close($stmt);
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            if ($action == 'AddUser')
            {
                $nuname = trim($_POST['username']);
                $nupass = trim($_POST['password']);

                if (empty(trim($_POST['psw'])))
                {
                    $errors[] = "Please enter your password to save changes.";
                }
                else
                {
                    $psw = trim($_POST['psw']);
                }
                
                if (empty($errors))
                {
                    $pCheck = passwordCheck($link, $_SESSION['id'], $psw);
                    if ($pCheck == 'true')
                    {
                        if (!empty($nuname) && !empty($nupass))
                        {
                            $sql = "SELECT id FROM users WHERE username = ?";
                            if ($stmt = mysqli_prepare($link, $sql))
                            {
                                mysqli_stmt_bind_param($stmt, "s", $param_username);
                                $param_username = $nuname;
                                
                                if (mysqli_stmt_execute($stmt))
                                {
                                    mysqli_stmt_store_result($stmt);
                                    if (mysqli_stmt_num_rows($stmt) == 1)
                                    {
                                        $errors[] = "That username is already taken.";
                                    }
                                }
                            }
                            mysqli_stmt_close($stmt);
                            
                            if (empty($errors))
                            {
                                $sql = "INSERT INTO users (username, password) VALUES (?, ?)";
                                if ($stmt = mysqli_prepare($link, $sql))
                                {
                                    mysqli_stmt_bind_param($stmt, "ss", $var1, $var2);
                                    
                                    $var1 = $nuname;
                                    $var2 = password_hash($nupass, PASSWORD_DEFAULT);
                                    
                                    if (mysqli_stmt_execute($stmt))
                                    {
                                        $success[] = "New user has been added successfully!";
                                    }
                                    else
                                    {
                                        $errors[] = "There was an issue adding the new user. Check the information and try again.";
                                    }
                                }
                                else
                                {
                                    $errors[] = "Unable to add the new user. If this issue continues, please contact an Admin.";
                                }
                            }
                        }
                        else
                        {
                            $errors[] = 'Missing the new users Username or Password. Please fill those fields in.';
                        }
                    }
                    else
                    {
                        $errors[] = $pCheck;
                    }
                }
            }
            if ($action == 'EditUser')
            {
                $nuname = $nfname = $nlname = $naddress = $nemail = $nlevel = '';
    
                $nuname = trim($_POST["username"]);
                $nfname = trim($_POST["firstname"]);
                $nlname = trim($_POST["lastname"]);
                $naddress = trim($_POST["address"]);
                $nemail = trim($_POST["email"]);
                $nlevel = trim($_POST["level"]);


                if (empty(trim($_POST['psw'])))
                {
                    $errors[] = "Please enter your password to save changes.";
                }
                else
                {
                    $psw = trim($_POST['psw']);
                }

                if (empty($errors))
                {
                    $pCheck = passwordCheck($link, $_SESSION['id'], $psw);
                    if ($pCheck == 'true')
                    {
                        if ($stmt = mysqli_prepare($link, "UPDATE users SET username = ?, firstname = ?, lastname = ?, email = ?, address = ?, level = ? WHERE id = ?"))
                        {
                            mysqli_stmt_bind_param($stmt, "ssssssi", $nuname, $nfname, $nlname, $nemail, $naddress, $nlevel, $id);
                            if (mysqli_stmt_execute($stmt))
                            {
                                $success[] = $nuname . " information updated successfully!";
                            }
                            else
                            {
                                $errors[] = "Unable to update information. If this issue continues, please contact an Admin.";
                            }
                        }
                        mysqli_stmt_close($stmt);
                    }
                    else
                    {
                        $errors[] = $pCheck;
                    }
                }
            }
            if ($action == 'RemoveUser')
            {
                $ruid = trim($_POST['usersID']);

                if (empty(trim($_POST['psw'])))
                {
                    $errors[] = "Please enter your password to save changes.";
                }
                else
                {
                    $psw = trim($_POST['psw']);
                }
                
                if (empty($errors))
                {
                    $pCheck = passwordCheck($link, $_SESSION['id'], $psw);
                    if ($pCheck == 'true')
                    {
                        if (!empty($ruid))
                        {
                            $sql = "SELECT id FROM users WHERE id = ?";
                            if ($stmt = mysqli_prepare($link, $sql))
                            {
                                mysqli_stmt_bind_param($stmt, "i", $param_id);
                                $param_id = $ruid;
                                
                                if (mysqli_stmt_execute($stmt))
                                {
                                    mysqli_stmt_store_result($stmt);
                                    if (mysqli_stmt_num_rows($stmt) != 1)
                                    {
                                        $errors[] = "Unable to find user to remove.";
                                    }
                                }
                            }
                            mysqli_stmt_close($stmt);
                            
                            if (empty($errors))
                            {
                                $sql = "DELETE FROM users WHERE id = ?";
                                if ($stmt = mysqli_prepare($link, $sql))
                                {
                                    mysqli_stmt_bind_param($stmt, "i", $param_id);
                                    
                                    $param_id = $ruid;

                                    if (mysqli_stmt_execute($stmt))
                                    {
                                        $success[] = "The User has been removed!";
                                    }
                                    else
                                    {
                                        $errors[] = "There was an issue removing the new user. Check the information and try again.";
                                    }
                                }
                                else
                                {
                                    $errors[] = "Unable to update users information. If this issue continues, please contact an Admin.";
                                }
                            }
                        }
                        else
                        {
                            $errors[] = 'Missing the users information needed to remove them. If this issue continues, please contact an Admin.';
                        }
                    }
                    else
                    {
                        $errors[] = $pCheck;
                    }
                }
            }
        }
    ?>
    <!-- Header -->
    <header class="w3-container" style="padding-top:22px">
        <h5><b><i class="fas fa-user-cog fa-fw"></i> User Configuration</b></h5>
    </header>
    
    <div class="w3-container w3-dark-grey w3-padding-32">
        <div class="w3-row">
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
            <?php 
                endif;
                
                if ($action == "EditUser")
                {
                    $sql = "SELECT id, firstname, lastname, username, email, address, level FROM users WHERE id = ?";
                    if ($stmt = mysqli_prepare($link, $sql))
                    {
                        mysqli_stmt_bind_param($stmt, "i", $id);
                        if (mysqli_stmt_execute($stmt))
                        {
                            mysqli_stmt_store_result($stmt);
                            if (mysqli_stmt_num_rows($stmt) > 0)
                            {
                                mysqli_stmt_bind_result($stmt, $eid, $efname, $elname, $euname, $eemail, $eaddress, $elevel);
                                while(mysqli_stmt_fetch($stmt))
                                {
                                    echo '
                                        <form action="?page=Users&action=EditUser&id='. $_GET['id'] .'" method="post">
                                            <div class="w3-container">
                                                <h5 class="w3-bottombar w3-border-green">Editing User: '. $euname .'</h5>
                                                <div class="w3-row">
                                                    <div class="w3-col s6 w3-padding">
                                                        <label>First Name</label>
                                                        <input class="w3-input" type="text" name="firstname" value="'. $efname .'">
                                                        <br>
                                                        <label>Last Name</label>
                                                        <input class="w3-input" type="text" name="lastname" value="'. $elname .'">
                                                        <br>
                                                        <label>E-mail</label>
                                                        <input class="w3-input" type="text" name="email" value="'. $eemail .'">
                                                        <br>
                                                        <label>Address</label>
                                                        <input class="w3-input" type="text" name="address" value="'. $eaddress .'">
                                                    </div>
                                                    <div class="w3-col s6 w3-padding">
                                                        <label>Username</label>
                                                        <input class="w3-input" type="text" name="username" value="'. $euname .'">
                                                        <br>
                                                        <label>Level</label>
                                                        <select class="w3-select" name="level">
                                                            <option value="0" '. (($elevel == "0") ? "selected" : "" ) .'>'. $levels[0] .'</option>
                                                            <option value="1" '. (($elevel == "1") ? "selected" : "" ) .'>'. $levels[1] .'</option>
                                                            <option value="2" '. (($elevel == "2") ? "selected" : "" ) .'>'. $levels[2] .'</option>
                                                            <option value="3" '. (($elevel == "3") ? "selected" : "" ) .'>'. $levels[3] .'</option>                                                    
                                                            <option value="4" '. (($elevel == "4") ? "selected" : "" ) .'>'. $levels[4] .'</option>
                                                        </select>
                                                    </div>
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
                                        </form>';
                                }
                            }
                            else
                            {
                                echo '
                                    <div class="w3-panel w3-red w3-center">
                                        <p>
                                            Unable to find the user you are trying to edit. Please <a href="?page=Users">go back</a> and try again.<br>
                                            If this problem continues, please contact the Administrator.
                                        </p>
                                    </div>
        
                                ';
                            }
                        }
                    }
                    mysqli_stmt_close($stmt);
                }
                else
                {
            ?>
            <form action="?page=Users&action=AddUser" method="post">
                <div class="w3-container">
                    <div class="w3-row">
                        <h5 class="w3-bottombar w3-border-green">Current Users</h5>
                        <table class="w3-table">
                            <tr>
                                <th>First Name</th>
                                <th>Last Name</th>
                                <th>Username</th>
                                <th>E-mail</th>
                                <th>Address</th>
                                <th>Level</th>
                                <th>Actions:</th>
                            </tr>
                            <?php
                                $sql = "SELECT id, firstname, lastname, username, email, address, level FROM users";
                                if ($stmt = mysqli_prepare($link, $sql))
                                {
                                    if (mysqli_stmt_execute($stmt))
                                    {
                                        mysqli_stmt_store_result($stmt);
                                        $number_of_results = mysqli_stmt_num_rows($stmt);

                                        if ($number_of_results > 0)
                                        {
                                            mysqli_stmt_bind_result($stmt, $eid, $efname, $elname, $euname, $eemail, $eaddress, $elevel);
                                            while(mysqli_stmt_fetch($stmt))
                                            {
                                                echo '
                                                    <tr class="w3-hover-green">
                                                        <td>'. $efname .'</td>
                                                        <td>'. $elname .'</td>
                                                        <td>'. $euname .'</td>
                                                        <td>'. $eemail .'</td>
                                                        <td>'. $eaddress .'</td>
                                                        <td>'. $levels[$elevel] .' '. (($elevel == '0') ? '<li class="fa fa-warning w3-text-yellow"></li>' : '') .'</td>
                                                        <td>
                                                            <div class="w3-row">
                                                                <div class="w3-col s4 w3-center w3-hover-dark-grey">
                                                                    '. (($elevel == '0') ? '<a href="?page=Users&action=ApproveUser&id='. $eid .'"><li class="fa fa-check"></li></a>' : '') .'
                                                                </div>
                                                                <div class="w3-col s4 w3-center w3-hover-dark-grey">
                                                                    <a href="?page=Users&action=EditUser&id='. $eid .'"><li class="fa fa-edit"></li></a>
                                                                </div>
                                                                <div class="w3-col s4 w3-center w3-hover-dark-grey">
                                                                    <a href="#" onclick="removeComfirm(\''. $eid .'\', \''. $euname .'\')"><li class="fa fa-remove"></li></a>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                ';
                                            }
                                        }
                                    }
                                }
                            ?>
                        </table>
                    </div>
                </div>
                <br>
                <div class="w3-container">
                    <h5 class="w3-bottombar w3-border-orange">Add User</h5>
                    <div class="w3-row">
                        <div class="w3-col s6 w3-padding">
                            <label>Username</label>
                            <input class="w3-input" type="text" name="username">
                        </div>
                        <div class="w3-col s6 w3-padding">
                            <label>Temporary Password</label>
                            <input class="w3-input" type="password" name="password">
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
            <?php
                }
            ?>
        </div>
    </div>
    <div id="id02" class="w3-modal">
        <div class="w3-modal-content w3-card-4 w3-animate-zoom" style="max-width:600px">
            <div class="w3-center"><br>
                <span onclick="document.getElementById('id02').style.display='none'" class="w3-button w3-xlarge w3-transparent w3-display-topright" title="Close Modal">×</span>
                <p id="confirmMessage"></p>
            </div>
            <form class="w3-container" action="?page=Users&action=RemoveUser" method="post">
                <div class="w3-container w3-border-top w3-padding-16 w3-light-grey w3-center">
                    <label>Please confirm your password:</label>
                    <input class="w3-input w3-border w3-white" style="display: block; margin: 0 auto; width: 350px;" type="password" name="psw" required>
                </div>
                <div class="w3-section">
                    <input type="hidden" name="usersID" id="usersID" value="">
                    <button class="w3-button w3-block w3-green w3-section w3-padding" type="submit">Remove User</button>
                </div>
            </form>
            <div class="w3-container w3-border-top w3-padding-16 w3-light-grey">
                <button onclick="document.getElementById('id02').style.display='none'" type="button" class="w3-button w3-red">Cancel</button>
            </div>
        </div>
    </div>
    <script>
        function removeComfirm (uid, name)
        {
            document.getElementById("confirmMessage").innerHTML = "You are about to delete this user: <b>" + name + "</b>. Are you sure?";
            document.getElementById("usersID").value = uid;
            document.getElementById('id02').style.display='block';
        }
    </script>