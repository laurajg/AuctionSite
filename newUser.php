<!DOCTYPE html>

<html>
<head>
<meta charset="UTF-8" />
<title> Add or Update User Account </title>
<link rel="stylesheet" type="text/css" href="auction.css" />  
</script>
</head>
<body>

<?php 
  include ('./sqlitedb.php');
  include ('./getCurrTime.php');
?>

<?php 
  include ('./navbar.html');
?>

<!-- GET AND SAVE CURRENT USER -->
<?php
$query = "select currUser from currUser";  
  try {
    $result = $db->query($query);
    $row = $result->fetch();
    $currUser = $row["currUser"];
  } catch (PDOException $e) {
    echo "Current user query failed: " . $e->getMessage();
  }
?>
  
<?php
	if ($_POST['newUserID']) {
		$name = $_POST['newUserID'];
		$location = $_POST['newUserLocation'];
		$country = $_POST['newUserCountry'];
		
		try {
			$query = "select max(Rating) as Rating from Users";
			$result = $db->query($query);
    		$row = $result->fetch();		
    		$rating = $row["Rating"];
			$rating += 1;
		} catch (PDOException $e) {
			echo "Unable to Create User Rating " . $e->getMessage();
		}
	
		try {
			$db->beginTransaction();
			$query = "Insert into Users values('$name', '$rating', '$location', '$country')";
			$db -> exec($query);
			$query2 = "update currUser set currUser= '$name' ";
			$db -> exec($query2);
			$db->commit();
			echo "Your Account has been successfully setup and you are logged in";
			
		} catch (Exception $e) {
    		try {
    			$db->rollBack();
    		} catch (PDOException $pe) {
				echo "Unable to Create User Account " . $e->getMessage();
	   		 }
		} 	
	}	
?>


<?php
	if ($_POST['updateUserLocation']) {
		$location = $_POST['updateUserLocation'];		
		try {
			$db->beginTransaction();
			$query = "update Users set Location = '$location' where UserID='$currUser'";
			$db->exec($query);
			 $db->commit();
			echo "Your Location has been successfully updated. &nbsp&nbsp&nbsp&nbsp";
		} 
		catch (Exception $e) {
    		try {
    			$db->rollBack();
    		} catch (PDOException $pe) {
					echo "Unable to Update User Location " . $e->getMessage();
	   		 }
		} 
	}
	
	if ($_POST['updateUserCountry']) {
		$country = $_POST['updateUserCountry'];
		try {
			$db->beginTransaction();
			$query = "update Users set Country = '$country' where UserID='$currUser'";
			$db->exec($query);
			 $db->commit();
			echo "Your Country has been successfully updated";
		} 
		catch (Exception $e) {
    		try {
    			$db->rollBack();
    		} catch (PDOException $pe) {
					echo "Unable to Update User Country " . $e->getMessage();
	   		 }
		} 
		
	}
	
?>

<div id="main">

<div id="leftBlock">

<div class="titleRow"> Add a New User Account: </div>

<form method="POST" action="newUser.php">
<p>
(NOTE: Your rating will start as the lowest out of all existing users) </p>
<table>
<tr> <td class="bold"> User ID: </td> <td> <input type="text" name="newUserID"/> </td></tr>
<tr> <td class="bold"> Location: </td> <td><input type="text" name="newUserLocation"/> </td></tr>
<tr> <td class="bold"> Country:</td> <td> <input type="text" name="newUserCountry"/> </td></tr>
</table>
<input type="submit" value="Submit">
</form>

<br/>
<div class="titleRow"> Update Your Account Information: </div>

<form method="POST" action="newUser.php">
<p> You can update either your location, country or both. Be aware that in order to <br/>
place an item for sale you must have both a location and country </p>
<table>
<tr> <td class="bold"> Location: </td> <td><input type="text" name="updateUserLocation"/> </td></tr>
<tr> <td class="bold"> Country:</td> <td> <input type="text" name="updateUserCountry"/> </td></tr>

</table>
<input type="submit" value="Submit">

</form>

</div> <!-- end left block div -->



<div id="rightBlock">

<div class="titleRow"> Time: </div>

<?php
  echo "<p><b>Current Time is: </b>" . $currTime . "</p>";  
?>

<b> Update Time: </b>
<form method="POST" action="newUser.php">
  <?php 
    include ('./timetable.html');
  ?>
</form>


</div> <!-- end right block div -->


</div> <!-- end main div -->
</body>
</html>