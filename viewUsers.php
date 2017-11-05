<?php
	date_default_timezone_set('Asia/Singapore');
	require_once("dbh.inc.php");
    include 'header.php';
?>

<?php
	$result=pg_query($conn, "SELECT * FROM users WHERE uid <> 'admin'");
	while ($row = pg_fetch_assoc($result)) {
    echo "<div class='panel panel-warning'><div class='panel panel-heading'><h3>";
        echo "User ID: ".$row['uid'];
        echo "<form class='delete-form' action='viewUsers.php' method='POST'>
         <input type='hidden' name='uId' placeholder='user id' value='".$row['uid']."' required >    
         <input type='number' min='0' name='newPoints' placeholder='update points' 
         	value = '".$row['points']."' required >
         <button class='btn btn-warning btn-xs' type='submit' name='pointUpdate'>Update points</button>
         <button class='btn btn-warning btn-xs' type='submit' name='userDelete'>Delete user</button>
         </form>";
        echo "</div><div class='panel panel-body'>";
        echo "User Password:  ".$row['password']." ";
        echo "<br>User Bidding Points:    ".$row['points']. " ";
        echo "</div></div>";
        
  }
	
	if(isset($_POST['pointUpdate'])){
		$newPoint = $_POST['newPoints'];
		$uid = $_POST['uId'];
		$resulta = pg_query($conn, "UPDATE users SET points ='$newPoint'
		  WHERE uid = '$uid'");
		if (!$resulta) {
          echo "<div class='alert alert-danger alert-dismissible' role='alert'>
             Update this user's bidding points failed.
             </div>";
         
        } else {
            echo "<div class='alert alert-success alert-dismissible' role='alert'>
              Update this user's bidding points successfully!
              </div>";
            echo "<meta http-equiv='refresh' content = '3'>";
      
        }
	}
	if(isset($_POST['userDelete'])){
		$uid = $_POST['uId'];
		$result1=pg_query($conn, "DELETE FROM bid WHERE bid='$uid'");
		$result2=pg_query($conn, "DELETE FROM availability WHERE cid ='$uid'");
		$result3=pg_query($conn, "DELETE FROM pets WHERE oid = '$uid'");
		$result4=pg_query($conn, "DELETE FROM users WHERE uid = '$uid'");
		if($result4){
			  echo "<div class='alert alert-success alert-dismissible' role='alert'>
              Delete user successfully!
              </div>";
            echo "<meta http-equiv='refresh' content = '3'>";
         
        } else {
        	echo "<div class='alert alert-danger alert-dismissible' role='alert'>
             Delete user failed.
             </div>";
        }
      
	}

?>

  

