<?php
$query = "select * from Users where UserID='$currUser' ";

 try {
    $result = $db->query($query);
    $row = $result->fetch();
    
    if (!empty($row["Rating"])) {
   	 	echo "<b> UserID: </b>" . $currUser . "<br/>";
   	 	echo "<b> Rating: </b>" . $row["Rating"] . "<br/>";
    
    	if (!empty($row["Location"]) and !empty($row["Country"])) {
    	    echo "<b>Location: </b>" . $row["Location"] . "<br/>";
    		echo "<b>Country: </b>" . $row["Country"] . "<br/>";
    	} else if (!empty($row["Location"])) {
    		echo "<b>Location: </b>" . $row["Location"] . "<br/>";
    		echo "<b>Country: </b> None given <br/>";
    		echo "<a href=\"newUser.php\"> Add your Country </a>";
    	} else if (!empty($row["Country"])) {
    		echo "<b>Country: </b>" . $row["Country"] . "<br/>";
    		echo "<b>Location: </b> None given <br/>";
   			echo "<a href=\"newUser.php\"> Add your Location </a>";
    	} else {
   			echo "<b>Location: </b> None given <br/>";
   			echo "<b>Country: </b> None given <br/>";
   			echo "<a href=\"newUser.php\"> Add your Location and Country </a>";
   		}
    }
    
    else {
    echo "<p>We don't have you in our system. Please enter a valid User ID to continue </p>";
    echo "If you do not have a User ID please " . "<a href=\"newUser.php\"> Create an account </a> to proceed </p>";
    }
    
} catch (PDOException $e) {
	echo "Error Message - Your User Information Lookup Failed: " . $e->getMessage();
}
?>