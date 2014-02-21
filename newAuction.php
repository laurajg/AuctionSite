<!DOCTYPE html>



<html>
<head>
<meta charset="UTF-8" />
<title> Add New Auction </title>
<link rel="stylesheet" type="text/css" href="auction.css" />  
</script>
</head>
<body>

<?php 
   include ('./sqlitedb.php');
  include ('./getCurrTime.php');
  include ('./getCurrUser.php');
?>

<?php 
  include ('./navbar.html');
?>

<!-- GET AND SAVE  end TIME -->
<?php
    $MM = $_POST["MMe"];
    $dd = $_POST["dde"];
    $yyyy = $_POST["yyyye"];
    $HH = $_POST["HHe"];
    $mm = $_POST["mme"];
    $ss = $_POST["sse"];    
    
    if($_POST["MMe"]) {
      $endTime = $yyyy."-".$MM."-".$dd." ".$HH.":".$mm.":".$ss;
    }
?>
  
<?php
	if ($_POST['ItemName'] and $_POST['startingPrice'] and $endTime and ($_POST['cat1']!=none) and ($_POST['cat2']!=none)){
		$name = $_POST['ItemName'];
		$startingPrice = $_POST['startingPrice'];
		$start = $_POST['start'];
		$end = $_POST['end'];
		$description = $_POST['description'];
	
		try {
			$query = "select max(ItemID) as ID from Item";
			$result = $db->query($query);
    		$row = $result->fetch();		
    		$ID = $row["ID"];
			$ID += 1;
		} catch (PDOException $e) {
			echo "Unable to Generate Item ID " . $e->getMessage();
		}
	
		if($_POST['buy']) {
			$buyPrice = $_POST['buy'];
		} else {
			$buyPrice = null;
		}

		try {
			$query = "Insert into Item values('$ID', '$currUser', '$name', '$startingPrice',
		 	    '$buyPrice', '$startingPrice', 0, '$currTime', '$endTime', '$description')";
			$db -> exec($query);
			
			$cat1 = $_POST['cat1'];
			$cat2 = $_POST['cat2'];
			$cat3 = $_POST['cat3'];

			if($cat1 != "none") {
				try {
					$query = "Insert into Category values('$ID', '$cat1')";
					$db -> exec($query);
				} catch (Exception $e) {
    				try {
    					$db->rollBack();
    				} catch (PDOException $pe) {
						echo "Unable to Input Your First Category " . $e->getMessage();
					}
	   		 	}
			}
					
			if($cat2 != "none"){
				try {
					$query = "Insert into Category values('$ID', '$cat2')";
					$db -> exec($query);
				} catch (Exception $e) {
    				try {
    					$db->rollBack();
    				} catch (PDOException $pe) {
						echo "Unable to Input Your Second Category " . $e->getMessage();
					}
	   		 	}
			} if($cat3 != "none"){
				try {
					$query = "Insert into Category values('$ID', '$cat3')";
					$db -> exec($query);
				} catch (Exception $e) {
    				try {
    					$db->rollBack();
    				} catch (PDOException $pe) {
						echo "Unable to Input Your Third Category " . $e->getMessage();
					}
	   		 	}
			}
			echo "Your auction has been successfully added  &nbsp;&nbsp;&nbsp;&nbsp;";
		
		} catch (Exception $e) {
    				try {
    					$db->rollBack();
    				} catch (PDOException $pe) {
						echo "Unable to Create Auction " . $e->getMessage();
					}
	   		 	}
	
	} else if ($_POST['ItemName'] or $_POST['startingPrice'] or $endTime) {
		echo "You need to enter values for all information not marked optional";
	}
	 
?>

<div id="main">

<div id="leftBlock">

<div class="titleRow"> Add a New Auction: </div>

<form method="POST" action="newAuction.php">
<p>
(NOTE: All auctions start from the current time, if you would like your <br/>
 auction to begin later you may do so by changing the current time). </p>

<table>
<tr> <td class="bold"> Item Name: </td> <td> <input type="text" name="ItemName"/> </td></tr>
<tr> <td class="bold">Starting Price: </td> <td><input type="text" name="startingPrice"/> </td></tr>
<tr> <td class="bold">Buy Price (Optional):</td> <td> <input type="text" name="buy"/> </td></tr>
 <tr> <td class="bold">
End Date:    </td> <td>
<?php 
	include ('./timetable2.html');
?>
</td></tr>
<tr> <td class="bold">
Description (Optional):  </td> <td> <textarea name="description" rows="4" cols="50"> </textarea> </td></tr>
 <tr> <td class="bold">
 Category (two required, a third is optional): </td>
<td>
<select class="cat" name="cat1">
	<option value="none"> &nbsp </option>
	<option value="Comics"> Comics </option>
	<option value="Collectibles"> Collectibles </option>
	<option value="Clothing &amp; Accessories"> Clothing </option>
	<option value="Decorative &amp; Holiday"> Decorative and Holiday </option>
	<option value="DVD"> DVD </option>
	<option value="Footwear"> Footwear </option>
	<option value="Girls"> Girls </option>
	<option value="Infants"> Infants </option>
	<option value="Intimates"> Intimates </option>
	<option value="Kitchenware"> Kitchenware </option>
	<option value="Memorabilia"> Memorabilia </option>
	<option value="Men"> Men </option>
	<option value="Misses"> Misses </option>
	<option value="Movies &amp; Television"> Movies and Television </option>
	<option value="Outerwear"> Outerwear </option>
	<option value="Other"> Other </option>
	<option value="Personal Care"> Personal Care </option>
	<option value="Photos"> Photos </option>
	<option value="Pop Culture"> Pop Culture </option>
	<option value="Posters"> Posters </option>
	<option value="Shirts"> Shirts </option>
	<option value="Superhero"> Superhero </option>
	<option value="Sweaters"> Sweaters </option>
	<option value="Television"> Television </option>
	<option value="Toys &amp; Hobbies"> Toys and Hobbies </option>
	<option value="VHS"> VHS </option>
	<option value="Video, Film"> Video, Film</option>
	<option value="Women"> Women </option>
</select>
</td></tr>
 <tr> <td> </td><td>
<select class="cat" name="cat2">
	<option value="none"> &nbsp </option>
	<option value="Comics"> Comics </option>
	<option value="Collectibles"> Collectibles </option>
	<option value="Clothing &amp; Accessories"> Clothing </option>
	<option value="Decorative &amp; Holiday"> Decorative and Holiday </option>
	<option value="DVD"> DVD </option>
	<option value="Footwear"> Footwear </option>
	<option value="Girls"> Girls </option>
	<option value="Infants"> Infants </option>
	<option value="Intimates"> Intimates </option>
	<option value="Kitchenware"> Kitchenware </option>
	<option value="Memorabilia"> Memorabilia </option>
	<option value="Men"> Men </option>
	<option value="Misses"> Misses </option>
	<option value="Movies &amp; Television"> Movies and Television </option>
	<option value="Outerwear"> Outerwear </option>
	<option value="Other"> Other </option>
	<option value="Personal Care"> Personal Care </option>
	<option value="Photos"> Photos </option>
	<option value="Pop Culture"> Pop Culture </option>
	<option value="Posters"> Posters </option>
	<option value="Shirts"> Shirts </option>
	<option value="Superhero"> Superhero </option>
	<option value="Sweaters"> Sweaters </option>
	<option value="Television"> Television </option>
	<option value="Toys &amp; Hobbies"> Toys and Hobbies </option>
	<option value="VHS"> VHS </option>
	<option value="Video, Film"> Video, Film</option>
	<option value="Women"> Women </option>
</select>
</td></tr>
 <tr> <td> </td><td>
<select class="cat" name="cat3">
	<option value="none"> &nbsp </option>
	<option value="Comics"> Comics </option>
	<option value="Collectibles"> Collectibles </option>
	<option value="Clothing &amp; Accessories"> Clothing </option>
	<option value="Decorative &amp; Holiday"> Decorative and Holiday </option>
	<option value="DVD"> DVD </option>
	<option value="Footwear"> Footwear </option>
	<option value="Girls"> Girls </option>
	<option value="Infants"> Infants </option>
	<option value="Intimates"> Intimates </option>
	<option value="Kitchenware"> Kitchenware </option>
	<option value="Memorabilia"> Memorabilia </option>
	<option value="Men"> Men </option>
	<option value="Misses"> Misses </option>
	<option value="Movies &amp; Television"> Movies and Television </option>
	<option value="Outerwear"> Outerwear </option>
	<option value="Other"> Other </option>
	<option value="Personal Care"> Personal Care </option>
	<option value="Photos"> Photos </option>
	<option value="Pop Culture"> Pop Culture </option>
	<option value="Posters"> Posters </option>
	<option value="Shirts"> Shirts </option>
	<option value="Superhero"> Superhero </option>
	<option value="Sweaters"> Sweaters </option>
	<option value="Television"> Television </option>
	<option value="Toys &amp; Hobbies"> Toys and Hobbies </option>
	<option value="VHS"> VHS </option>
	<option value="Video, Film"> Video, Film</option>
	<option value="Women"> Women </option>
</select>
</td></tr>
</table>
<input type="submit" value="Submit">

</form>
</div>

<div id="rightBlock">

<div class="titleRow"> Time: </div>
	<?php
	  echo "<p><b>Current Time is: </b>" . $currTime . "</p>";  
	?>

<b> Update Time: </b>
<form method="POST" action="newAuction.php">
  <?php 
    include ('./timetable.html');
  ?>
</form>


<div class="titleRow"> User Information </div>
<?php
  include ('./userInfo.php');
?>

 <br/>
<b> Enter UserID to change User: </b>
<form method="POST" action="newAuction.php">
  <?php 
   echo "<input type=\"text\" name=\"UserID\" />";
	echo "<input type=\"submit\" value=\"Update User\"/>";
  ?>
</form>
<br/>

  
</form>


</div> <!-- end right block div -->
</div>   <!-- end main div -->

</body>
</html>