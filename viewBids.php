<?php
	date_default_timezone_set('Asia/Singapore');
	require_once("dbh.inc.php");
    include 'header.php';
?>

<?php
	$result=pg_query($conn, "SELECT * FROM bid b, availability a, pets p
		WHERE b.aid = a.aid AND p.pid = b.pid  ORDER BY b.bid ASC");
	while ($row = pg_fetch_assoc($result)) {
    echo "<div class='panel panel-warning'><div class='panel panel-heading'><h3>";
        echo "Bidder ID: ".$row['bid'];
        echo "<br>Carer ID: ".$row['cid'];
        echo "<form class='delete-form' action='viewBids.php' method='POST'>
         <input type='hidden' name = 'bId' placeholder='bidding id' value='".$row['bid']."' required > 
         <input type='hidden' name = 'aId' placeholder='avai id' value='".$row['aid']."' required > 
         <input type='hidden' name = 'pId' placeholder='pet id' value='".$row['pid']."' required > 
         <button class='btn btn-warning btn-xs' type='submit' name='delete'>Delete bid</button>
         </form>";
        echo "</div><div class='panel panel-body'>";
        echo "Pet ID:  ".$row['pid']." ";
        echo "<br>Pet Name:  ".$row['pname']." ";
        echo "<br>Pet Type:    ".$row['ptype']. " ";
        echo "<br>Status:    ".$row['status']. " ";
        echo "<br>Bidding Points:   ".$row['points']." ";;
        echo "</div></div>";
        
  }
	
	if(isset($_POST['delete'])){
		$result1 = pg_query($conn, "DELETE FROM bid WHERE bid = '$_POST[bId]' AND aid = '$_POST[aId]' AND pid = '$_POST[pId]'");
        if (!$result1) {
             echo "<div class='alert alert-danger alert-dismissible' role='alert'>
             Delete bid failed.
              </div>";
         
         } else {
             echo "<div class='alert alert-success alert-dismissible' role='alert'>
             Delete bid successfully!
             </div>";
      
              echo "<meta http-equiv='refresh' content = '3'>";
         }
	}

?>

  

