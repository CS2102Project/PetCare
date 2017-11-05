<?php
	date_default_timezone_set('Asia/Singapore');
	require_once("dbh.inc.php");
    include 'header.php';
?>

<?php
	$result=pg_query($conn, "SELECT * FROM availability");

	while ($row = pg_fetch_assoc($result)) {
    echo "<div class='panel panel-warning'><div class='panel panel-heading'><h3>";
        echo "Availability ID: ".$row['aid'];
        echo "<form class='delete-form' action='viewAvai.php' method='POST'>
         <input type='hidden' name='aId' value='".$row['aid']."' required >    
         <label>Update starting time</label>   
         <input type='text' name = 'newStart' placeholder='update start time'
         	 value = '".$row['afrom']."' required >
         <label> Update ending time</label>
         <input type='text' name = 'newEnd' placeholder='update end time'
         	 value = '".$row['ato']."' required >
         <button class='btn btn-warning btn-xs' type='submit' name='update_avai'>
         	Update availability</button>
         <button class='btn btn-warning btn-xs' type='submit' name='delete_avai'>
         	Delete availability</button>
         </form>";
        echo "</div><div class='panel panel-body'>";
        echo "Carer ID :  ".$row['cid']." ";
        echo "<br>Prefered Pet Type:    ".$row['ptype']. " ";
        echo "<br>From:   ".$row['afrom']. " ";
        echo "<br>To: ".$row['ato']. " ";
        echo "</div></div>";
        
  }
	
	if(isset($_POST['update_avai'])){
		$start = $_POST['newStart'];
		$end = $_POST['newEnd'];
		$aid = $_POST['aId'];
		$result=pg_query($conn, "UPDATE availability SET afrom ='$start', ato='$end'
			 WHERE aid='$aid'");
		if(!$result) {
         	echo "<div class='alert alert-danger alert-dismissible' role='alert'>
             Update availability time failed.
             </div>";
        } else {
        	echo "<div class='alert alert-success alert-dismissible' role='alert'>
              Update availability time successfully!
              </div>";
            echo "<meta http-equiv='refresh' content = '3'>";
        }
	}
	if(isset($_POST['delete_avai'])){
		$aid = $_POST['aId'];
		$result1=pg_query($conn, "DELETE FROM bid WHERE aid='$aid'");
		$result2=pg_query($conn, "DELETE FROM availability WHERE aid='$aid'");
		if(!$result2){
			echo "<div class='alert alert-danger alert-dismissible' role='alert'>
             Delete availability failed.
             </div>";        
        } else {
             echo "<div class='alert alert-success alert-dismissible' role='alert'>
              Delete availability successfully!
              </div>";
            echo "<meta http-equiv='refresh' content = '3'>";
        }
	}
?>

  

