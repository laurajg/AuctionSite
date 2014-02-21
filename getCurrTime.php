<!-- GET AND SAVE CURRENT TIME -->
 
<?php
    $MM = $_POST["MM"];
    $dd = $_POST["dd"];
    $yyyy = $_POST["yyyy"];
    $HH = $_POST["HH"];
    $mm = $_POST["mm"];
    $ss = $_POST["ss"];    
    
    if($MM==2 and $dd>29) {
    	echo "Try again, Febuary can't have more than 29 days";
    } else if ($MM==2 and $dd==29 and ($yyyy % 4) != 0) {
    	echo "Try again, February can't have 29 days if it isnt a leap year";
    } else if ($dd==31 and ($MM==9 or $MM==4 or $MM==6 or $MM==11)) {
    	echo "Try again, the month you selected only has 30 days";
    } else if($_POST["MM"]) {
      $selectedtime = $yyyy."-".$MM."-".$dd." ".$HH.":".$mm.":".$ss;
      	try {
      		$db->beginTransaction();
      		$query = "update Time set currTime = '$selectedtime' ";
    		 $db->exec($query);
    		$db->commit();
    		echo "The Time has successfully been updated to '$selectedtime'";
      	} catch (Exception $e) {
    			try {
    				$db->rollBack();
    			} catch (PDOException $pe) {
					echo "Current Time update failed: " . $e->getMessage();
				}
		}
    }
?>
  
<?php
$query = "select currTime from Time";  
  try {
    $result = $db->query($query);
    $row = $result->fetch();
    $currTime = $row["currTime"];
  } catch (PDOException $e) {
    echo "Current time Lookup failed: " . $e->getMessage();
  }
?>

