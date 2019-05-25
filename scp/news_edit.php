    <?php
        if ($ulevel < '2')
        {
            header('location: ../index.php');
            exit;
        }

        $action = $_GET['action'];
        $id = $_GET['id'];
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            if ($action == 'CreatePost')
            {
                $nptitle = trim($_POST['title']);
                $npcontent = nl2br(trim($_POST['content']));
                $nppublish = trim($_POST['publish']);
                $npcreated = date('Y-m-d h:i:s');

                if (!empty($nptitle) && !empty($npcontent))
                {
                    $sql = "INSERT INTO posts (user_id, title, body, published, created_at) VALUES (?, ?, ?, ?, ?)";
                    if ($stmt = mysqli_prepare($link, $sql))
                    {
                        mysqli_stmt_bind_param($stmt, "issis", $var1, $var2, $var3, $var4, $var5);
                        
                        $var1 = $_SESSION['id'];
                        $var2 = $nptitle;
                        $var3 = $npcontent;
                        $var4 = $nppublish;
                        $var5 = $npcreated;
                        
                        if (mysqli_stmt_execute($stmt))
                        {
                            $success[] = "News post has been created successfully!";
                        }
                        else
                        {
                            $errors[] = "There was an issue adding the news post. Check the information and try again.";
                        }
                    }
                    else
                    {
                        $errors[] = "Unable to add the news post. If this issue continues, please contact an Admin.";
                    }
                }
                else
                {
                    $errors[] = 'Missing the news post title or content fields. Please fill those fields in.';
                }
            }
            if ($action == 'EditNews')
            {
                $nptitle = trim($_POST['title']);
                $npcontent = nl2br(trim($_POST['content']));
                $nppublish = trim($_POST['publish']);
                $npupdated = date('Y-m-d h:i:s');
                $npID = trim($_POST['postID']);

                if (!empty($nptitle) && !empty($npcontent))
                {
                    $sql = "UPDATE posts SET title = ?, published = ?, body = ?, updated_at = ?, updated_by = ? WHERE id = ?";
                    if ($stmt = mysqli_prepare($link, $sql))
                    {
                        mysqli_stmt_bind_param($stmt, "sissii", $var1, $var2, $var3, $var4, $var5, $var6);
                        
                        $var5 = $_SESSION['id'];
                        $var1 = $nptitle;
                        $var3 = $npcontent;
                        $var2 = $nppublish;
                        $var4 = $npupdated;
                        $var6 = $npID;
                        
                        if (mysqli_stmt_execute($stmt))
                        {
                            $success[] = "News post has been updated successfully!";
                        }
                        else
                        {
                            $errors[] = "There was an issue updating the news post. Check the information and try again.";
                        }
                    }
                    else
                    {
                        $errors[] = "Unable to update the news post. If this issue continues, please contact an Admin.";
                    }
                }
                else
                {
                    $errors[] = 'Missing the news post title or content fields. Please fill those fields in.';
                }
            }
            if ($action == 'RemovePost')
            {
                $pid = trim($_POST['postID']);
                $sql = "DELETE FROM posts WHERE id = ?";
                if ($stmt = mysqli_prepare($link, $sql))
                {
                    mysqli_stmt_bind_param($stmt, "i", $param_id);
                    $param_id = $pid;

                    if (mysqli_stmt_execute($stmt))
                    {
                        $success[] = "The post has been removed!";
                    }
                    else
                    {
                        $errors[] = "There was an issue removing the news post.";
                    }
                }
                else
                {
                    $errors[] = "Unable to delete post information. If this issue continues, please contact an Admin.";
                }
            }
        }
    ?>
    <!-- Header -->
    <header class="w3-container" style="padding-top:22px">
        <h5><b><i class="fa fa-newspaper-o fa-fw"></i> News Post Admin</b></h5>
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
                
                if ($action == "CreatePost")
                {
                    ?>
                    <form action="?page=News_Admin&action=CreatePost" method="post">
                        <a href="?page=News_Admin" class="w3-button w3-theme-d1 w3-margin-bottom w3-red" style="margin: 4px 2px;"> Cancel</a>
                        <button class="w3-button w3-theme-d1 w3-margin-bottom w3-right w3-green" type="submit" style="margin: 4px 2px;"> Submit</button>
                        <br>
                        <div class="w3-row">
                            <h5 class="w3-bottombar w3-border-green">Create New Post</h5>
                            <label>Publish Post?:</label>
                            <select class="w3-select w3-input w3-border w3-light-grey" name="publish" required>
                                <option value="0">No</option>
                                <option value="1">Yes</option>
                            </select><br>
                            <label>Title:</label>
                            <input class="w3-input w3-border w3-light-grey" type="text" name="title" required>
                            <br>
                            <label>Content:</label><br>
                            <span>HTML Code is allowed.</span>
                            <textarea class="w3-input w3-border w3-light-grey" name="content" rows="10" cols="50" required></textarea>
                        </div>
                    </form>
                    <?php
                }
                else if ($action == "EditPost")
                {
                    $sql = "SELECT id, title, body, published FROM posts WHERE id = ?";
                    if ($stmt = mysqli_prepare($link, $sql))
                    {
                        mysqli_stmt_bind_param($stmt, "i", $id);
                        if (mysqli_stmt_execute($stmt))
                        {
                            mysqli_stmt_store_result($stmt);
                            $number_of_results = mysqli_stmt_num_rows($stmt);
                            if ($number_of_results > 0)
                            {
                                mysqli_stmt_bind_result($stmt, $eid, $etitle, $econtent, $epublished);
                                while(mysqli_stmt_fetch($stmt))
                                {
                                    $news_content = str_replace('<br />', "", $econtent);
                                    ?>
                                    <form action="?page=News_Admin&action=EditNews" method="post">
                                        <a href="?page=News_Admin" class="w3-button w3-theme-d1 w3-margin-bottom w3-red" style="margin: 4px 2px;"> Cancel</a>
                                        <button class="w3-button w3-theme-d1 w3-margin-bottom w3-right w3-green" type="submit" style="margin: 4px 2px;"> Submit</button>
                                        <br>
                                        <div class="w3-row">
                                            <h5 class="w3-bottombar w3-border-green">Edit News Post</h5>
                                            <label>Publish Post?:</label>
                                            <select class="w3-select w3-input w3-border w3-light-grey" name="publish" required>
                                                <option value="0" <?php if($epublished == 0) { echo "selected"; } ?>>No</option>
                                                <option value="1" <?php if($epublished == 1) { echo "selected"; } ?>>Yes</option>
                                            </select><br>
                                            <label>Title:</label>
                                            <input class="w3-input w3-border w3-light-grey" type="text" name="title" required value="<?php echo $etitle; ?>">
                                            <br>
                                            <label>Content:</label>
                                            <textarea class="w3-input w3-border w3-light-grey" name="content" rows="10" cols="50" required><?php echo $news_content; ?></textarea>
                                        </div>
                                        <input type="hidden" name="postID" id="postID" value="<?php echo $eid; ?>">
                                    </form>
                                    <?php
                                }
                            }
                            else
                            {
                                echo '
                                    <div class="w3-panel w3-red w3-center">
                                        <p>
                                            Unable to find the news post you are trying to edit. Please <a href="?page=News_Admin">go back</a> and try again.<br>
                                            If this problem continues, please contact the Administrator.
                                        </p>
                                    </div>
        
                                ';
                            }
                        }
                    }
                    mysqli_stmt_close($stmt);
                }else{
            ?>
            <a href="?page=News_Admin&action=CreatePost" class="w3-button w3-theme-d1 w3-margin-bottom w3-right w3-green"> Create Post</a>
            <form action="?page=Users&action=AddUser" method="post">
                <div class="w3-container">
                    <div class="w3-row">
                        <h5 class="w3-bottombar w3-border-green">Current Post</h5>
                        <table class="w3-table">
                            <tr>
                                <th>ID</th>
                                <th>Author</th>
                                <th>Title</th>
                                <th>Body</th>
                                <th>Created</th>
                                <th>Last Updated</th>
                                <th>Published</th>
                                <th>Actions:</th>
                            </tr>
                            <?php
                                $sql = "SELECT id, user_id, title, body, published, created_at, updated_at FROM posts";
                                if ($stmt = mysqli_prepare($link, $sql))
                                {
                                    if (mysqli_stmt_execute($stmt))
                                    {
                                        mysqli_stmt_store_result($stmt);
                                        $number_of_results = mysqli_stmt_num_rows($stmt);

                                        if ($number_of_results > 0)
                                        {
                                            mysqli_stmt_bind_result($stmt, $nid, $naid, $ntitle, $nbody, $npub, $ncreated, $nupdated);
                                            while(mysqli_stmt_fetch($stmt))
                                            {
                                                $word_length = strlen($nbody);
                                                echo '
                                                    <tr class="w3-hover-green">
                                                        <td>'. $nid .'</td>
                                                        <td>'. getAuthor($link, $naid) .'</td>
                                                        <td>'. $ntitle .'</td>
                                                        <td>'. substr($nbody, 0, $string_limit) . (($word_length > $string_limit) ? "..." : "") .'</td>
                                                        <td>'. $ncreated .'</td>
                                                        <td>'. $nupdated .'</td>
                                                        <td>'. (($npub == '0') ? '<li class="fa fa-times w3-text-red"></li>' : '<li class="fa fa-check w3-text-green"></li>') .'</td>
                                                        <td>
                                                            <div class="w3-row">
                                                                <div class="w3-col s4 w3-center w3-hover-dark-grey">
                                                                    
                                                                </div>
                                                                <div class="w3-col s4 w3-center w3-hover-dark-grey">
                                                                    <a href="?page=News_Admin&action=EditPost&id='. $nid .'"><li class="fa fa-edit"></li></a>
                                                                </div>
                                                                <div class="w3-col s4 w3-center w3-hover-dark-grey">
                                                                    <a href="#" onclick="removeComfirm(\''. $nid .'\', \''. $ntitle .'\')"><li class="fa fa-remove"></li></a>
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
            <form class="w3-container" action="?page=News_Admin&action=RemovePost" method="post">
                <div class="w3-section">
                    <input type="hidden" name="postID" id="postID" value="">
                    <button class="w3-button w3-block w3-green w3-section w3-padding" type="submit">Remove Post</button>
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
            document.getElementById("confirmMessage").innerHTML = "You are about to delete post titled: <b>" + name + "</b>.<br> Are you sure?";
            document.getElementById("postID").value = uid;
            document.getElementById('id02').style.display='block';
        }
    </script>