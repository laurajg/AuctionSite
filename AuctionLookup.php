<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8" />
<title> Lookup Auction by Item ID</title>
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

<div id="main">


<div id="topBar">

<div id="header3">
<form method="POST" action="AuctionLookup.php">
	<b> Auction ID:  </b> 
	<input type="text" name="AuctionID" > 
	<input type="submit" value="Lookup">
</form>
</div>


</div> <!-- end top bar div -->

		 
			 
<div id="lowerBlock">

<div class="titleRow"> Item Name </div>

<?php
   	$ID = $_POST["AuctionID"];

	$query = "select itemName from Item where ItemID='$ID' and StartDate<='$currTime'";
	
	try {
    	$result = $db->query($query);
		$row = $result->fetch();
		echo  "<p>" . $row["itemName"] . "</p>";
	} catch (PDOException $e) {
		echo "Name Lookup Failed: " . $e->getMessage();
	}		
?>




<?php

	echo "<div class=\"titleRow\"> Item Details </div>";

   	$ID = $_POST["AuctionID"];
   	
   	$query = "select count(*) as count from Item where ItemID='$ID' and StartDate<='$currTime'";
   	
   	try {
   		$result = $db->query($query);
   		$row = $result->fetch();
   		
   	
   		if($row["count"]>0){			
   	$query = "select ItemID, UserID, CurrentBid, BuyPrice, NumBids, StartDate, EndDate, Description, Group_Concat(cat, ' | ') as t from Item left outer join Category using(ItemID) group by ItemID having ItemID='$ID'";
	
  	try {
    	$result = $db->query($query);
    			
    	echo "<table class=\"lookupTable3\"> <tr><th> Status </th><th> Item ID </th>
    	 <th> Current Bid </th> <th> Buy Price </th>
    	 <th> Number of Bids </th> <th> Start Date </th> <th> End Date </th> 
    	  <th> Categories</th><th> Highest Bidder </th></tr>";

    	$row = $result->fetch();
    			
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
   			
		$query = "select UserID from Bid where ItemID='$AuctionID' and Amount = '$currBid'";
		try {
			$result2 = $db->query($query);
			$row2 = $result2->fetch();
			$leader = $row2["UserID"];
   		} catch (PDOException $e) {
   			 echo "Leader Lookup failed: " . $e->getMessage();
 		 }
 		
 		$seller = $row["UserID"];
 			
		echo "<tr><td>" . $auctionStatus . "</td>";
    	echo "<td>" . $AuctionID . "</td>";
    	echo "<td>" . $currBid . "</td> ";
    	echo "<td>". $row["BuyPrice"] . "</td>";
    	echo "<td>" . $row["NumBids"] . "</td>";
   		echo "<td>" . $row["StartDate"] . "</td>";
   		echo "<td>" . $row["EndDate"] . "</td>";
   		echo "<td>" . $row["t"] . "</td>";
   		echo "<td>" . $leader . "</td></tr></table><br/>";
  
  	} catch (PDOException $e) {
   		 echo "Auction Lookup failed: " . $e->getMessage();
 	}
	} else {
		echo "Enter a valid auction ID in order to get Item Information. Note that you can only lookup auctions which have already started. </p>";
	}
	
	}catch (PDOException $e) {
   		 echo "Auction Lookup failed: " . $e->getMessage();
 	}
 	
	echo "<div class=\"titleRow\"> Seller Information </div>";

	$query3 = "select * from Users where UserID='$seller'";
	
	try {
		$result3 = $db->query($query3);
		$row3 = $result3->fetch();
		
		echo "<table class=\"lookupTable3\"> <tr><th> Seller ID </th><th> Seller Rating </th>
    	<th> Seller Location </th> <th> Seller Country </th> </tr>";
    
   		echo "<tr><td>" . $seller . "</td><td>" . $row3["Rating"] . "</td><td>" .  $row3["Location"] . "</td><td>" .  $row3["Country"] . "</td></tr>";
    
    	echo "</table><br/>";
    
	} catch(PDOException $e){
		echo "seller information lookup failed " . $e->getMessage();
	}

	
  	
?>



<div class="titleRow"> Item Description </div>

<?php
   	$ID = $_POST["AuctionID"];

	$query = "select description from Item where ItemID='$ID' and StartDate<='$currTime'";
	
	try {
    	$result = $db->query($query);
		$row = $result->fetch();
		echo "<p>" . $row["Description"] . "</p>";
	} catch (PDOException $e) {
		echo "Description Lookup Failed: " . $e->getMessage();
	}		
?>



<div class="titleRow"> Bid History </div>

			
<?php 
	$ID = $_POST["AuctionID"];
   	
   	$query = "select * from Bid B1 where B1.ItemID='$ID' and B1.timeBid<='$currTime' and not exists(select * from Bid B2 where B1.ItemID=B2.ItemID and B2.timeBid <= B1.timeBid and B2.Amount > B1.Amount) and not exists (select * from Bid B3 join Item using(ItemID) where
    				  B1.ItemID=B3.ItemID and B3.timeBid<B1.timeBid and  B3.Amount=Item.BuyPrice) and not exists(select * from Bid B4 where B4.ItemID=B1.ItemID and B4.timeBid < B1.timeBid and B4.Amount=B1.Amount)  order by timeBid";
   	 
	try {
    	$result = $db->query($query);
    			
    	echo "<table class=\"lookupTable3\"> <tr><th> Item ID </th><th> Bidder ID </th>
    	<th> Bid Time </th> <th> Bid Amount </th></tr>";

    	while ($row = $result->fetch()) {	
   								
    		echo "<td>" . $row["ItemID"] . "</td>";
    		echo "<td>". $row["UserID"] . "</td>";
    		echo "<td>". $row["timeBid"] . "</td>";
    		echo "<td>". $row["Amount"] . "</td></tr>";
   				
		}
  		echo "</table><br/>";
  	} catch (PDOException $e) {
		 echo "Auction Lookup failed: " . $e->getMessage();
 	}
?>

</div> <!--end lower section div -->



</div> <!--end main div -->

<?php
$db = null;
?>
</body>
</html>