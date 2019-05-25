<?php
	function existsInArray ($needle, $array)
	{
		$index = (string)array_key_exists($needle, $array);
		return strlen($index) > 0;
	}
	
	function getAuthor ($link, $id)
	{
	    $sql = "SELECT firstname FROM users WHERE id = ?";
	    if ($stmt = mysqli_prepare($link, $sql))
	    {
	        mysqli_stmt_bind_param($stmt, "i", $uid);
	        $uid = $id;
	        if (mysqli_stmt_execute($stmt))
	        {
	            mysqli_stmt_store_result($stmt);
	            if (mysqli_stmt_num_rows($stmt) == 1)
	            {
	                mysqli_stmt_bind_result($stmt, $firstname);
	                if (mysqli_stmt_fetch($stmt))
	                {
	                    return $firstname;
	                }
	            }
	        }
	    }
	}
	
	function passwordCheck ($link, $id, $pass)
	{
	    $sql = "SELECT password FROM users WHERE id = ?";
	    if ($stmt = mysqli_prepare($link, $sql))
	    {
	        mysqli_stmt_bind_param($stmt, "i", $uid);
	        $uid = $id;
	        if (mysqli_stmt_execute($stmt))
	        {
	            mysqli_stmt_store_result($stmt);
	            if (mysqli_stmt_num_rows($stmt) == 1)
	            {
	                mysqli_stmt_bind_result($stmt, $password);
	                if (mysqli_stmt_fetch($stmt))
	                {
	                    if (password_verify($pass, $password))
	                    {
	                        return 'true';
	                    }
	                    else
	                    {
	                        return 'Incorrect password. Please try again.';
	                    }
	                }
	                else
	                {
	                    return 'There was an issue fetching MySQLi for password check.';
	                }
	            }
	            else
	            {
	                return 'There was an issue locating users account for password check.';
	            }
	        }
	        else
	        {
	            return 'There was an issue executing MySQLi for password check.';
	        }
	    }
	    else
	    {
	        return 'There was an issue preparing MySQLi for password check.';
	    }
	}
?>