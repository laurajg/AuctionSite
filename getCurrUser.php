<!-- GET AND SAVE CURRENT USER -->
<?php
if ($_POST['UserID']){
	$user = $_POST['UserID'];
	try {
	 $db->beginTransaction();
	 $query = "update currUser set currUser= '$user' ";
 	 $db->exec($query);
 	  $db->commit();
 	} catch (Exception $e) {
    	try {
    		$db->rollBack();
    	} catch (PDOException $pe) {
			echo "Unable to Input Your Update User: " . $e->getMessage();
		}
	} 
}
$query = "select currUser from currUser";  
  try {
    $result = $db->query($query);
    $row = $result->fetch();
    $currUser = $row["currUser"];
  } catch (PDOException $e) {
    echo "Current user lookup failed: " . $e->getMessage();
  }
?>

