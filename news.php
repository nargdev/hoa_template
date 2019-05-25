<?php
    $action = $_GET['action'];
    $id = $_GET['id'];
?>
<div class="bgimg-news w3-padding-large" style="background-image: url('images/community_news.jpg');">
    <div class="w3-display-container" style="height: 400px;">
      <div class="w3-display-middle">
        <span class="w3-xxlarge w3-text-white w3-wide w3-black w3-padding">Community News Posting</span>
      </div>
    </div>
    
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
                            <div class="w3-container w3-padding" style="width: 75%; margin: 10px auto 0px auto;">
                                <a href="index.php?page=News" class="w3-button w3-teal">Back To All Post</a>
                            </div>

                            <div class="w3-container w3-white w3-card w3-round w3-padding" style="width: 75%; margin: 10px auto 0px auto;"><br>
                                <img src="images/blog.png" alt="Avatar" class="w3-left w3-circle w3-margin-right" style="width:60px">
                                <span class="w3-right w3-opacity">'. $pupdated .'</span>
                                <h4>'. getAuthor($link, $paid) .'</h4><br>
                                <p>'. $ptitle .'</p>
                                <hr class="w3-clear">
                                <p>
                                    '. $pbody .'
                                </p>
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
                    if (mysqli_stmt_num_rows($stmt) > 0)
                    {
                        mysqli_stmt_bind_result($stmt, $pid, $paid, $ptitle, $pbody, $pupdated);
                        while(mysqli_stmt_fetch($stmt))
                        {
                            $word_length = strlen($pbody);
                            echo '
                                <div class="w3-container w3-white w3-card w3-round w3-padding" style="width: 75%; margin: 10px auto 0px auto;"><br>
                                    <img src="images/blog.png" alt="Avatar" class="w3-left w3-circle w3-margin-right" style="width:60px">
                                    <span class="w3-right w3-opacity">'. $pupdated .'</span>
                                    <h4>'. getAuthor($link, $paid) .'</h4><br>
                                    <p>'. $ptitle .'</p>
                                    <hr class="w3-clear">
                                    <p>
                                        '. substr($pbody, 0, $string_limit) . (($word_length > $string_limit) ? "..." : "") .'
                                    </p>
                                    '. (($word_length > $string_limit) ? "<a href=\"?page=News&action=View&id=$pid\" class=\"w3-right w3-button\">read more</a>" : "") .'
                                </div>';
                        }
                    }
                }
            }
        }
    ?>
</div>