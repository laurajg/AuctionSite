<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8" />
<title> Browse Auctions </title>
<link rel="stylesheet" type="text/css" href="auction.css" />  
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js">
</script>
</head>
<body>
<?php 
  include ('./sqlitedb.php');
  include ('./getCurrTime.php');
  include ('./getCurrUser.php');
  include ('./getNewBid.php');
?>

<?php 
  include ('./navbar.html');
?>
<div id="main">
<div id="header">
<form method="POST" action="BrowsePage.php">
  		<?php 
    		include ('./lookup.html');
  		?>
</form>
</div>   <!-- end header div -->


<div id="leftBlock">

<div id="bids">
<div class="titleRow"> Search Results </div>

<?php
   			$ID = $_POST["AuctionID"];
   			$Category = $_POST["Category"];
   			$Name = $_POST["Name"];
   			$KeyWord = $_POST["keyword"];
   			$min = $_POST["Min"];
   			$max = $_POST["Max"];
   			$status = $_POST["Status"];

			$Name2 = $Name . " ";
			#$Name2 = " " . $Name;

   			$Category = $Category . " ";


   			if($_POST["Order"]) {
   				$order = $_POST["Order"];
   			} else {
   				$order = "EndDate";
   			}
   			
   			$query = "select ItemID, itemName, CurrentBid, NumBids, StartDate, EndDate, Group_Concat(cat, ' | ') as t from Item left outer join Category using(ItemID) group by ItemID having 1 = 1";
   			if ($ID) {
   				$query .=  " and ItemID = '$ID'";
   			}
  			if($Category) {
				$query .= " and t like '%$Category%'";
			}
			if($Name) {
				$query .= " and (itemName like '%$Name%' or itemName like '%$Name2%')";
			} if($KeyWord) {
				$query .= " and itemName like '%KeyWord%'";
			} if($min) {
				$query .= " and CurrentBid >= '$min'";
			} if($max) {
				$query .= " and CurrentBid <= '$max'";
			} if($status == "open") {
				$query .= " and EndDate > '$currTime'";
			} if($status == "closed") {
				$query .= " and EndDate <= '$currTime'";
			}
			$query .= " and '$currTime' > StartDate";

			$query .= " order by " . $order;
	
  			try {
    			$result = $db->query($query);
    			
    			echo "<table class=\"bidsTable\"> <tr><th> Status </th><th> Item ID </th> <th> Item Name </th>
    			<th> Current Bid </th> <th> Categories</th><th> Number of Bids </th></tr>";

    			while ($row = $result->fetch()) {
    			
    				 $AuctionID = $row["ItemID"];
					 $currBid = $row["CurrentBid"];
					 
    			 	 if($currTime>=$row["EndDate"]){
   		 				$auctionStatus="closed";
   					 } else if($currTime<$row["StartDate"]) {
   		 				$auctionStatus="not yet open";
   		 			 } else if($row["BuyPrice"] == $row["CurrentBid"]) {
   		 				$auctionStatus= "closed: buy price paid";
   					 } else {
   		   				 $auctionStatus = "open";
   					 }
   					
   					
					
					echo "<tr><td>" . $auctionStatus . "</td>";
    				echo "<td>" . $AuctionID . "</td>";
    				echo "<td>". $row["itemName"] . "</td>";
    				echo "<td>" . $currBid . "</td> ";
   					echo "<td>" . $row["t"] . "</td>";
   					echo "<td>" . $row["NumBids"] . "</td>";
   				
				}
  				echo "</table>";
  			} catch (PDOException $e) {
   				 echo "Auction Lookup failed: " . $e->getMessage();
 			 }
  
		?>


</div> <!--end bids section div -->

</div> <!--end leftBlock div -->


<div id="rightBlock">

<div class="titleRow"> Time: </div>

<?php
  echo "<p><b>Current Time is: </b>" . $currTime . "</p>";  
?>

<b> Update Time: </b>
<form method="POST" action="BrowsePage.php">
  <?php 
    include ('./timetable.html');
  ?>
</form>




<div class="titleRow"> User Information </div>
	<?php 
	  include ('./userInfo.php');
	?>
 <p>
<b> Enter UserID to change User: </b>
<form method="POST" action="BrowsePage.php">
  <?php 
   echo "<input type=\"text\" name=\"UserID\" />";
	echo "<input type=\"submit\" value=\"Update User\"/>";
  ?>
</form>
</p>

<div class="titleRow"> Place a Bid: </div>
<form method="POST" action="BrowsePage.php">
  <?php 
    include ('./inputBid.html');
  ?>
  
</form>


</div> <!-- end right block div -->

</div> <!--end main div -->
<?php
$db = null;
?>
</body>
</html>