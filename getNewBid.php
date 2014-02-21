<!-- Update and New Bids -->
<?php
	if ($_POST["BidItemID"]){
		$bidder = $currUser;
		$amount = $_POST["BidAmount"];
		$bidItem = $_POST["BidItemID"];
	
	 try {
		$db->beginTransaction();
		$query = "Insert into Bid values('$bidItem', '$bidder', '$currTime', '$amount') ";
    	$db->exec($query);
    	$db->commit();
    	echo "You have successfully placed a new bid";
    } catch (Exception $e) {
    	try {
    		$db->rollBack();
    	} catch (PDOException $pe) {
	    	echo "Bid failed: " . $e->getMessage();
	    }
  }
	}

?>

