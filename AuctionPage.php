<!DOCTYPE html>


<html>
<head>
<meta charset="UTF-8" />
<title> User Profile </title>
<link rel="stylesheet" type="text/css" href="auction.css" />  
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

<div id="leftBlock">

<div id="header2">
	<h1> Your Auction Activity </h1>
</div>   <!-- end header div -->

<br/>

<div id="bids">
<div class="titleRow">Your Items you have put up for Sale: </div>

<?php
echo "<table class=\"bidsTable\"> <tr> <th> ItemID </th> <th> Item Name </th> <th> Current Bid
   		 </th> <th> Number of Bids </th> <th> Start Date </th> <th> End Date </th>
   		 <th> Status </th> <th> Highest Bidder </th> </tr>";

 try {
    $query = "select * from Item where UserID='$currUser' and '$currTime' >= StartDate order by EndDate";
    $result = $db->query($query);
    
    while($row = $result->fetch()) {
   		  if($currTime>=$row["EndDate"]){
   		 		$auctionStatus="closed";
    		 } else if($currTime<$row["StartDate"]) {
 				$auctionStatus="not yet open";
 			 } else if($row["BuyPrice"] == $row["CurrentBid"]) {
   				$auctionStatus= "closed: buy price paid";
   			 } else {
   		   		 $auctionStatus = "open";
   			 }
   		 $AuctionID = $row["ItemID"];
		$currBid = $row["CurrentBid"];
		$query = "select UserID from Bid where ItemID='$AuctionID' and Amount = '$currBid'";
		$result2 = $db->query($query);
		$row2 = $result2->fetch();
		$leader = $row2["UserID"];
		
		echo "<tr> <td>" . $row["ItemID"] . "</td><td>" . $row["itemName"] . "</td><td>"
		. $row["CurrentBid"] . "</td><td>" . $row["NumBids"] . "</td><td>"
		. $row["StartDate"] . "</td><td>" . $row["EndDate"] . "</td><td>"
		. $auctionStatus . "</td><td>" . $leader . "</td></tr>";
		
    }
echo "</table><br/>";
} catch (PDOException $e) {
	echo "Error Message - Your Auctions Lookup Failed: " . $e->getMessage();
}
?>
&nbsp;
<!-- <p><b> Change the current time to view the max bid at a given time </b> </p> -->


<div class="titleRow"> Your Current Bids on Open Auctions: </div>

<?php


echo "<table  class=\"bidsTable\"> <tr> <th> ItemID </th> <th> Item Name </th> <th> Your Bid 
		</th> <th> Your Bid Time </th><th> Current Bid </th> <th> Number of Bids </th> <th> Buy Price </th>
		<th> Start Date </th> <th> End Date </th> <th> Status </th> <th> Current Leader
		</th> </tr>";
   		 
 $UserID = $_POST['UserID'];

 try {
    
    $query = "select distinct * from Bid B1 join Item using(ItemID) group by ItemID, UserID having
     B1.UserID='$currUser' and timeBid <= '$currTime' and '$currTime'< EndDate and (CurrentBid<>BuyPrice or BuyPrice is null)
     and not exists(select * from Bid B2 where B1.ItemID=B2.ItemID and B2.timeBid <= B1.timeBid and B2.Amount > B1.Amount)
         ";
    
    $result = $db->query($query);
    
  
    while($row = $result->fetch()) {
   		  if($currTime>=$row["EndDate"]){
   		 	$auctionStatus="closed";
   		 } else if($currTime<$row["StartDate"]) {
   		 	$auctionStatus="not yet open";
   		 } else if($row["BuyPrice"] == $row["CurrentBid"]) {
   		 	$auctionStatus="closed - buy price paid";
   		 } else {
   		    $auctionStatus = "open";
   		 }
		$AuctionID = $row["ItemID"];
		$currBid = $row["CurrentBid"];
		$query = "select UserID from Bid where ItemID='$AuctionID' and Amount = '$currBid'";
	
		try {
			$result2 = $db->query($query);
			$row2 = $result2->fetch();
			$leader = $row2["UserID"];
		} catch (PDOException $e) {
			echo "Can't Find Leader UserID " . $e->getMessage();
		}
		
		
		
		$buyPrice = $row["BuyPrice"];
		if ($buyPrice == null){
			$buyPrice="none";
		}
		
		echo "<tr> <td>" . $AuctionID . "</td><td>" . $row["itemName"] . "</td><td>"
		. $row["Amount"] . "</td><td>" . $row["timeBid"] . "</td><td>" . $currBid
		. "</td><td>" . $row["NumBids"] . "</td><td>" . $buyPrice . "</td><td>"
		. $row["StartDate"] . "</td><td>" . $row["EndDate"]  . "</td><td>"
		. $auctionStatus . "</td><td>" . $leader . "</td></tr>";
		
    }
	echo "</table><br/>";
} catch (PDOException $e) {
	echo "Error Message - Your Bids on Open Auction Lookup failed: " . $e->getMessage();
}

?>
<div class="titleRow"> Closed Auctions You placed bids on: </div>

<?php

echo "<table  class=\"bidsTable\"> <tr> <th> ItemID </th> <th> Item Name </th> <th> Your Last Bid 
		</th> <th> Your Bid Time </th><th> Winning Bid </th> <th> Number of Bids </th> <th> Buy Price </th>
		<th> Start Date </th> <th> End Date </th> <th> Status </th> <th> Winner </th> </tr>";
   		 
 try {
     
    $query = "select distinct * from Bid B1 join Item using(ItemID) group by ItemID, UserID having
    B1.UserID='$currUser' and timeBid <= '$currTime' and ('$currTime'>= EndDate or CurrentBid=BuyPrice) and 
     not exists(select * from Bid B2 where B1.ItemID=B2.ItemID 
    and B2.timeBid <= B1.timeBid and B2.Amount > B1.Amount) and B1.timeBid<=EndDate";
    
    
    
   
    $result = $db->query($query);
    
    while($row = $result->fetch()) {
   		 if($currTime>=$row["EndDate"]){
   		 	$auctionStatus="closed";
   		 } else if($currTime<$row["StartDate"]) {
   		 	$auctionStatus="not yet open";
   		 } else if($row["BuyPrice"] == $row["CurrentBid"]) {
   		 	$auctionStatus="closed - buy price paid";
   		 } else {
   		    $auctionStatus = "open";
   		 }
		$AuctionID = $row["ItemID"];
		$currBid = $row["CurrentBid"];
		
		$query = "select UserID from Bid where ItemID='$AuctionID' and Amount = '$currBid'";
		try {
			$result2 = $db->query($query);
			$row2 = $result2->fetch();
			$winner = $row2["UserID"];
		} catch (PDOException $e) {
			echo "Can't Find Winner UserID " . $e->getMessage();
		}
		
		$buyPrice = $row["BuyPrice"];
		if ($buyPrice == null){
			$buyPrice="none";
		}
		
		echo "<tr> <td>" . $AuctionID . "</td><td>" . $row["itemName"] . "</td><td>"
		. $row["Amount"] . "</td><td>" . $row["timeBid"] . "</td><td>" . $currBid
		. "</td><td>" . $row["NumBids"] . "</td><td>"  . $buyPrice . "</td><td>"
		. $row["StartDate"] . "</td><td>" . $row["EndDate"]  . "</td><td>"
		. $auctionStatus . "</td><td>" . $winner . "</td></tr>";
		
    }
echo "</table><br/>";
} catch (PDOException $e) {
	echo "Error Message - You Bids on Closed Auction Lookup Failed: " . $e->getMessage();
}

?>
&nbsp;
<!--<p><b> Select 'Place Bid' to  bid again or bid on another item </b> </p> -->

</div> <!--end bids section div -->

</div> <!--end leftBlock div -->


<div id="rightBlock">

<div class="titleRow"> Time: </div>

<?php
  echo "<p><b>Current Time is: </b>" . $currTime . "</p>";  
?>

<b> Update Time: </b>
<form method="POST" action="AuctionPage.php">
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
<form method="POST" action="AuctionPage.php">
  <?php 
   echo "<input type=\"text\" name=\"UserID\" />";
	echo "<input type=\"submit\" value=\"Update User\" />";
  ?>
</form>
<br/>


<div class="titleRow"> Place a Bid: </div>
<form method="POST" action="AuctionPage.php">
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