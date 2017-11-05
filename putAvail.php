<?php
	date_default_timezone_set('Asia/Singapore');
	require_once("dbh.inc.php");
	require_once("text.inc.php");
    include 'header.php';
    session_start();
?>

<?php
echo"
<div>
      <form class='form-signin' action='".post_avail($conn)."' method='POST'>
        <h2 class='form-signin-heading'>Be a carer! Post your availability here!</h2>
        <input type='text' name='ptype' class='form-control' placeholder='Pet type' required autofocus><br>
        <h4 class='form-signin-heading'>From</h4>
        <input type='date' name='afrom' class='form-control' required><br>
        <h4 class='form-signin-heading'>To</h4>
        <input type='date' name='ato' class='form-control' required><br>
        <button class='btn btn-lg btn-warning btn-block' type='submit' name='postAvailSubmit'>SUBMIT</button>
      </form>
</div>"; 
echo "<div>
  <h2 class='form-signin-heading'>Bidding on going </h2>
  </div>";
getOnGoingAvail($conn);
echo "<div>
  <h2 class='form-signin-heading'>My caring history </h2>
  </div>";
  getAvailHistory($conn);
?>

<?php
// Submit an availability
function post_avail($conn) {
	if (isset($_POST['postAvailSubmit'])){
		$cid = $_SESSION['uid'];
		$ptype = $_POST['ptype'];
		$afrom = $_POST['afrom'];
		$ato = $_POST['ato'];
		$sql = "INSERT INTO availability (cid, ptype, afrom, ato) VALUES ('$cid', '$ptype', '$afrom', '$ato')";
		$result = pg_query($conn, $sql);
		if ($result) {
			echo "<div class='alert alert-success alert-dismissible' role='alert'>
			  Post successfully!
			</div>";
		} else {
			echo "<div class='alert alert-danger alert-dismissible' role='alert'>
			  Post failed. Please try again.
			</div>";
		}
		
	}
}

// Get bidding on going availabilities, which are the availabilities that bidder is not set
function getOnGoingAvail($conn){
	$cid = $_SESSION['uid'];
	$sql = "SELECT * FROM availability WHERE cid = '$cid'";
	$result = pg_query($conn, $sql);
	while ($row = pg_fetch_assoc($result)) {
		// Check whether this availability is closed
		$aid = $row['aid'];
		$sql2 = "SELECT * FROM bid WHERE aid = '$aid' AND status = 'successful'";
		$result2 = pg_query($conn, $sql2);

		// There is no bid successful for this availability, this availability is bidding on going
		if (pg_num_rows($result2) == 0) {
				echo "<div class='panel panel-warning'><div class='panel panel-heading'><h3>";
				echo $row['ptype'];
				echo "</div><div class='panel panel-body'>";
				echo "From    ".$row['afrom']."</h3>"."  to  ".$row['ato'];
				echo "<form class='delete-form' method='POST' action='".delete($conn)."'>
				<input type='hidden' name='aid' value='".$row['aid']."'>
				<button type=submit name = 'AvailDelete' class='btn btn-warning btn-xs'>DELETE</button>
			</form>
			<form class='edit-form' method='POST' action='".update($conn)."'>
				<input type='hidden' name='aid' value='".$row['aid']."'>
				<input type='text' name='ptype' value='".$row['ptype']."'>
				<input type='date' name='afrom' value='".$row['afrom']."'>
				<input type='date' name='ato' value='".$row['ato']."'>
				<button type=submit name = 'AvailUpdate' class='btn btn-warning btn-xs'>EDIT</button>
			</form>";
			showBidders($conn, $row['aid']);
		echo "</div></div>";
	}
		
	}
	
}

// Get availability history, which are the availabilities that bidder is already set
function getAvailHistory($conn){
	$cid = $_SESSION['uid'];
	$sql = "SELECT a.aid, a.ato, a.afrom, a.ptype FROM availability a, bid b WHERE a.cid = '$cid' AND a.aid = b.aid AND b.status = 'successful'";
	$result = pg_query($conn, $sql);
	while ($row = pg_fetch_assoc($result)) {
				echo "<div class='panel panel-warning'><div class='panel panel-heading'><h3>";
				echo $row['ptype'];
				echo "</div><div class='panel panel-body'>";
				echo "From    ".$row['afrom']."</h3>"."  to  ".$row['ato'];
			showSuccessfulBidders($conn, $row['aid']);
		echo "</div></div>";
	}
	
}


// Show the bidders in an closed availability
function showSuccessfulBidders($conn, $aid) {
	$sql = "SELECT * FROM bid WHERE aid = '$aid'";
	$result = pg_query($conn, $sql);
	while ($row = pg_fetch_assoc($result)) {
		echo "<br>Bidder: ".$row['bid'].", Bid points: ".$row['points'].", Status: ".$row['status'];
	}
}

// Show the bidders bidding for an availability
function showBidders($conn, $aid) {
	$sql = "SELECT * FROM bid WHERE aid = '$aid'";
	$result = pg_query($conn, $sql);
	while ($row = pg_fetch_assoc($result)) {
		echo "<div><br>Bidder: ".$row['bid'].", Bid points: ".$row['points'].", Status: ".$row['status'];
		echo "<form method='POST' action='".chooseBidder($conn)."'>
				<input type='hidden' name='bid' value='".$row['bid']."'>
				<input type='hidden' name='aid' value='".$row['aid']."'>
				<input type='hidden' name='pid' value='".$row['pid']."'>
				<input type='hidden' name='points' value='".$row['points']."'>
				<button type=submit name = 'ChooseBidderButton' class='btn btn-warning btn-xs'>Choose this bidder</button>
			</form></div>";
	}
}

// The carer choose a bidder for one availability
function chooseBidder($conn) {
	if (isset($_POST['ChooseBidderButton'])) {
		$bid = $_POST['bid'];
		$aid = $_POST['aid'];
		$pid = $_POST['pid'];
		$points = $_POST['points'];

		// Make other bids fail
		$sql2 = "UPDATE bid SET status = 'failed' where aid ='$aid'";
		$result = pg_query($conn, $sql2);

		// Make the bid successful
		$sql1 = "UPDATE bid SET status = 'successful' where bid ='$bid' AND aid ='$aid' AND pid ='$pid'";
		$result = pg_query($conn, $sql1);

		// Get points left
		$sql3 = "SELECT * FROM users SET where uid ='$bid'";
		$result = pg_query($conn, $sql3);
		$row = pg_fetch_assoc($result);
		$pointsLeft = $row['points'] - $points;

		// Deduct points of bidder
		$sql4 = "UPDATE users SET points = '$pointsLeft' where uid ='$bid'";
		$result = pg_query($conn, $sql4);
		
		header("Location: putAvail.php");
		
		}
}
	function delete($conn){
		if (isset($_POST['AvailDelete'])) {
		$aid = $_POST['aid'];

		$sql1 = "DELETE FROM bid WHERE aid ='$aid'";
		$result = pg_query($conn, $sql1);

		$sql = "DELETE FROM availability WHERE aid ='$aid'";
		$result = pg_query($conn, $sql);
		
		header("Location: putAvail.php");
		
		}
	}
	function update($conn){
		if (isset($_POST['AvailUpdate'])) {
		$aid = $_POST['aid'];
		$ptype = $_POST['ptype'];
		$afrom = $_POST['afrom'];
		$ato = $_POST['ato'];
		$sql = "UPDATE availability SET ptype='$ptype', afrom='$afrom', ato='$ato' where aid ='$aid'";
		$result = pg_query($conn, $sql);
		$sql2 = "UPDATE bid SET ptype='$ptype', afrom='$afrom', ato='$ato' where aid ='$aid'";
		$result = pg_query($conn, $sql2);
		header("Location: putAvail.php");
		}
	}
	
?>

<html>
<body background="images/dogflower.jpg">
</body>
</html>
