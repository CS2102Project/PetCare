<?php
	date_default_timezone_set('Asia/Singapore');
	require_once("dbh.inc.php");
    include 'header.php';
?>

<?php
	$result=pg_query($conn, "SELECT * FROM pets");
  while ($row = pg_fetch_assoc($result)) {
    echo "<div class='panel panel-warning'><div class='panel panel-heading'><h3>";
        echo "Pet ID: ".$row['pid'];
        echo "<form class='delete-form' action='viewPets.php' method='POST'>
         <input type='hidden' name = 'oId' placeholder='owner id' value='".$row['oid']."' required > 
         <input type='hidden' name = 'pName' placeholder='pet name' value='".$row['pname']."' required > 
         <input type='hidden' name = 'pType' placeholder='pet type' value='".$row['ptype']."' required > 
         <input type='hidden' name = 'pId' placeholder='pet id' value='".$row['pid']."' required > 
         <label>Update pet id</label>   
         <input type='text' name = 'newPetId' placeholder='update petId'
          value = '".$row['pid']."' required >
         <button class='btn btn-warning btn-xs' type='submit' name='update_pet'>update pet id</button>
         <button class='btn btn-warning btn-xs' type='submit' name='delete_pet'>Delete pet</button>
         </form>";
        echo "</div><div class='panel panel-body'>";
        echo "Pet Name:  ".$row['pname']." ";
        echo "<br>Pet Type:    ".$row['ptype']. " ";
        echo "<br>Owner ID:    ".$row['oid']. " ";
        echo "</div></div>";
        
  }
	
	if(isset($_POST['update_pet'])){
		$result = pg_query($conn, "UPDATE pets SET pid ='$_POST[newPetId]' 
      WHERE pname ='$_POST[pName]' AND ptype ='$_POST[pType]' AND oid = '$_POST[oId]'");
    	  if (!$result) {
          echo "<div class='alert alert-danger alert-dismissible' role='alert'>
             Update this pet ID failed.
             </div>";
         
        } else {
            echo "<div class='alert alert-success alert-dismissible' role='alert'>
              Update this pet ID successfully!
              </div>";
            echo "<meta http-equiv='refresh' content = '3'>";
      
        }
    }
	if(isset($_POST['delete_pet'])){
    $pid = $_POST['pId'];
    $result1 = pg_query($conn, "DELETE FROM bid WHERE pid = '$pid'");
    $result2 = pg_query($conn, "DELETE FROM pets WHERE pid = '$pid'");
		if (!($result2 && $result1)) {
          echo "<div class='alert alert-danger alert-dismissible' role='alert'>
             Delete pet failed.
             </div>";
         
        } else {
            echo "<div class='alert alert-success alert-dismissible' role='alert'>
              Delete pet successfully!
              </div>";
            echo "<meta http-equiv='refresh' content = '3'>";
        }
	}

?>

  

