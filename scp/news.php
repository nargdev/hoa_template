<?php
    $id = $_GET['id'];
    $action = $_GET['action'];
?>
    <!-- Header -->
    <header class="w3-container" style="padding-top:22px">
        <h5><b><i class="fa fa-newspaper-o fa-fw"></i> News Post</b></h5>
    </header>
    
    <?php
        if ($action == "View" && !empty($id))
        {
            $sql = "SELECT user_id, title, body, created_at FROM posts WHERE id = ?";
            if ($stmt = mysqli_prepare($link, $sql))
            {
                mysqli_stmt_bind_param($stmt, "i", $id);
                if (mysqli_stmt_execute($stmt))
                {
                    mysqli_stmt_store_result($stmt);
                    if (mysqli_stmt_num_rows($stmt) > 0)
                    {
                        mysqli_stmt_bind_result($stmt, $paid, $ptitle, $pbody, $pupdated);
                        mysqli_stmt_fetch($stmt);

                        echo '
                        <div class="w3-container w3-card w3-white w3-round w3-margin"><br>
                            <img src="../images/blog.png" alt="Avatar" class="w3-left w3-circle w3-margin-right" style="width:60px">
                            <span class="w3-right w3-opacity">'. $pupdated .'</span>
                            <h4>'. getAuthor($link, $paid) .'</h4><br>
                            <p>'. $ptitle .'</p>
                            <hr class="w3-clear">
                            <p>
                                '. $pbody .'
                            </p>
                            <!--
                            <button type="button" class="w3-button w3-theme-d1 w3-margin-bottom"><i class="fa fa-thumbs-up"></i> Like</button>
                            <button type="button" class="w3-button w3-theme-d2 w3-margin-bottom"><i class="fa fa-comment"></i> Comment</button>-->
                        </div>';
                    }
                }
            }
        }else{
            $sql = "SELECT id, user_id, title, body, created_at FROM posts WHERE published = '1'";
            if ($stmt = mysqli_prepare($link, $sql))
            {
                if (mysqli_stmt_execute($stmt))
                {
                    mysqli_stmt_store_result($stmt);
                    $number_of_results = mysqli_stmt_num_rows($stmt);

                    if ($number_of_results > 0)
                    {
                        mysqli_stmt_bind_result($stmt, $pid, $paid, $ptitle, $pbody, $pupdated);
                        while(mysqli_stmt_fetch($stmt))
                        {
                            $word_length = strlen($pbody);
                            echo '
                            <div class="w3-container w3-card w3-white w3-round w3-margin"><br>
                                <img src="../images/blog.png" alt="Avatar" class="w3-left w3-circle w3-margin-right" style="width:60px">
                                <span class="w3-right w3-opacity">'. $pupdated .'</span>
                                <h4>'. getAuthor($link, $paid) .'</h4><br>
                                <p>'. $ptitle .'</p>
                                <hr class="w3-clear">
                                <p>
                                    '. substr($pbody, 0, $string_limit) . (($word_length > $string_limit) ? "..." : "") .'
                                </p>
                                <!--
                                <button type="button" class="w3-button w3-theme-d1 w3-margin-bottom"><i class="fa fa-thumbs-up"></i> Like</button>
                                <button type="button" class="w3-button w3-theme-d2 w3-margin-bottom"><i class="fa fa-comment"></i> Comment</button>-->
                                '. (($word_length > $string_limit) ? "
                                <a href=\"?page=News&action=View&id=$pid\">
                                    <button type=\"button\" class=\"w3-button w3-theme-d1 w3-margin-bottom w3-right\"> Read More</button>
                                </a>" : "") .'
                            </div>';
                        }
                    }
                }
            }
            //<p>'. $ptitle .' '. (($ppub == 1) ? '<span class="w3-padding-small w3-green w3-right"><i class="fa fa-check"></i> Published</span>' : '<span class="w3-padding-small w3-red w3-right"><i class="fa fa-times"></i> Published</span>') .'</p>
        }
    ?>