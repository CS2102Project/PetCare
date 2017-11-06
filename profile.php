  <?php
    date_default_timezone_set('Asia/Singapore');
    require_once("dbh.inc.php");
    include 'header.php';
  ?>

  <?php
  $uid = $_SESSION['uid'];
  echo "<div>
  <h2 class='form-signin-heading'>This is the profile page for $uid!</h2>
  </div>";


  $result1 = pg_query($conn, "SELECT * FROM users WHERE uid = '$uid'");
  $result2 = pg_query($conn, "SELECT SUM(b.points) FROM users u, bid b 
          WHERE u.uid = b.bid AND u.uid = '$uid' AND b.status = 'pending' GROUP BY b.bid");
  $row1 = pg_fetch_assoc($result1);
  $row2 = pg_fetch_assoc($result2);
  $pointsRemain = $row1[points] - $row2[sum];

  echo "<div>
  <h2 class='form-signin-heading'>My bidding points:  $pointsRemain </h2>
  </div>";

  echo"
  <div>
      <form class='form-signin' action='profile.php' method='POST'>
        <h2 class='form-signin-heading'>Change my password</h2>
        <input type='text' name='new_password' class='form-control' placeholder='New Password' required autofocus>
        <input type='text' name='re_enter_password' class='form-control' placeholder='Enter the new password again' required>
        <button class='btn btn-lg btn-warning btn-block' type='submit' name='changePasswordSubmit'>Change Password</button>
      </form>
  </div>";

    if (isset($_POST['changePasswordSubmit'])) {
      if ($_POST[new_password] <> $_POST[re_enter_password]) {
        echo "<div class='alert alert-danger alert-dismissible' role='alert'>
        Two passwords are different!!
        </div>";

      } else {
        pg_query($conn, "UPDATE users SET password = '$_POST[new_password]' WHERE uid = '$uid'");
      
        echo "<div class='alert alert-success alert-dismissible' role='alert'>
        Update password successfully!
      </div>";
      }
    }


  echo "<div>
  <form class='form-signin' action='profile.php' method='POST'>
    <h2 class='form-signin-heading'>My pets</h2>
  </form>
  </div>";  
  $result = pg_query($conn, "SELECT * FROM pets WHERE oid = '$uid' ORDER BY pid ASC" );
  while ($row = pg_fetch_assoc($result)) {
        echo "<div class='panel panel-warning'><div class='panel panel-heading'><h3>";
        echo "Pet ID: ".$row['pid'];
        echo "<form class='delete-form' action='profile.php' method='POST'>
         <label>Update pet name</label>
         <input type ='hidden' name='pId' placeholder='pet id' value='".$row['pid']."' required >
         <input type ='text' name = 'pName' placeholder = 'pet name' value = '".$row['pname']."' required>
         <input type ='text' name = 'pType' placeholder = 'pet type' value = '".$row['ptype']."' required>
         <button class='btn btn-warning btn-xs' type='submit' name='update_pet'>Update this pet</button>
         <button class='btn btn-warning btn-xs' type='submit' name='delete_pet'>Delete this pet</button>
         </form>";
        echo "</div><div class='panel panel-body'>";
        echo "Pet Name:  ".$row['pname']." ";
        echo "<br>Pet Type:    ".$row['ptype']. " ";
        echo "</div></div>";
        
  }
    

  if (isset($_POST['update_pet'])) {
  $result1 = pg_query($conn, "UPDATE pets SET
   pname = '$_POST[pName]', ptype = '$_POST[pType]' 
   WHERE pid = '$_POST[pId]' ");
  if (!$result1) {
        echo "<div class='alert alert-danger alert-dismissible' role='alert'>
        update pet information failed!
        </div>";
  } else {
         echo "<div class='alert alert-success alert-dismissible' role='alert'>
         Update pet information successfully!
         </div>";

        echo "<meta http-equiv='refresh' content = '3'>";      
  }
}
  if (isset($_POST['delete_pet'])) {
    $result1 = pg_query($conn, "DELETE FROM pets WHERE pid = '$_POST[pId]'");
    if (!$result1) {
      echo "<div class='alert alert-danger alert-dismissible' role='alert'>
        Delete failed, this pet is in the bidding list!!
       </div>";
           
        } else {
            echo "<div class='alert alert-success alert-dismissible' role='alert'>
        Delete pet successfully!
      </div>";
     
      echo "<meta http-equiv='refresh' content = '3'>";
        }
  }

  

  echo "<div class='panel panel-warning'><div class='panel panel-heading'><h2>";
        echo "Add a new pet";
        echo "<form class='delete-form' action='profile.php' method='POST'>";
        echo "</div><div class='panel panel-body'>      
         <input type ='text' name = 'pId' placeholder = 'Pet ID' required>
         <input type ='text' name = 'pName' placeholder = 'Pet Name' required>
         <input type ='text' name = 'pType' placeholder = 'Pet Type' required>
         <button class='btn btn-warning btn-lg' type='submit' name='add_pet'>Add a pet</button>
         </form>";
        echo"</div></div>";


  if (isset($_POST['add_pet'])) {
    $result1 = pg_query($conn, "INSERT INTO pets VALUES ('$_POST[pId]', 
      '$_POST[pName]', '$_POST[pType]', '$uid')");
    if (!$result1) {
        echo "<div class='alert alert-danger alert-dismissible' role='alert'>
        Add failed, the pet ID has already existed!!
         </div>";
         
    } else {
         echo "<div class='alert alert-success alert-dismissible' role='alert'>
         Add pet successfully!
         </div>";
      
      echo "<meta http-equiv='refresh' content = '3'>";
    }
  }

  $result3 = pg_query($conn, "SELECT * 
  FROM availability a, bid b
  WHERE a.aid = b.aid
  AND b.bid = '$uid'
  AND b.status = 'pending'
  ORDER BY b.pid ASC");

  echo "<div>
  <form class='form-signin' action='profile.php' method='POST'>
  <h2 class='form-signin-heading'>All slots I am bidding now</h2>
  </form>
  </div>";

  while ($row = pg_fetch_assoc($result3)) {
    echo "<div class='panel panel-warning'><div class='panel panel-heading'><h3>";
        echo "Pet ID: ".$row['pid'];
        echo "<form class='delete-form' action='profile.php' method='POST'>
         <input type='hidden' name='pId' placeholder='pet id' value='".$row['pid']."' required >
         <input type='hidden' name='aId' placeholder='avail id' value='".$row['aid']."' required >
         <input type='hidden' name='old_points' placeholder='old points' 
            value='".$row['points']."' required >
         <input type='number' min='0' name='newPoints' placeholder='update points' 
            value = '".$row['points']."' required >
         <button class='btn btn-warning btn-xs' type='submit' name='bidUpdate'>Update bid</button>
         <button class='btn btn-warning btn-xs' type='submit' name='bidDelete'>Quit this bid</button>
         </form>";
        echo "</div><div class='panel panel-body'>";
        echo "Carer:  ".$row['cid']." ";
        echo "<br>From:   ".$row['afrom']."</h3>"."  to  ".$row['ato'];
        echo "<br>Points: ".$row['points']." ";
        echo "<br>Status: ".$row['status']." ";
        echo "</div></div>";
        
  }

  if (isset($_POST['bidUpdate'])) {
      $aid = $_POST['aId'];
      $pid = $_POST['pId'];
      $points = $_POST['newPoints'];
      $old_points = $_POST['old_points'];
      $bid = $_SESSION['uid'];

      //Check enough points
      $result3a = pg_query($conn, "SELECT * FROM users WHERE uid = '$uid'"); 
      $result3c = pg_query($conn, "SELECT SUM(b.points) FROM users u, bid b 
          WHERE u.uid = b.bid AND u.uid = '$uid' AND b.status = 'pending' GROUP BY b.bid");
      $userRow = pg_fetch_assoc($result3a);
      $userRow1 = pg_fetch_assoc($result3c);
      $totalPoints = $userRow[points];
      $usedPoints = $userRow1[sum];
      $pointsCanUse = $totalPoints - $usedPoints + $old_points;
      $pointsLeft = $pointsCanUse - $points;

      if ($pointsLeft >= 0) {
        $result3b = pg_query($conn, "UPDATE bid SET points = '$points' 
          WHERE aid = '$aid' AND pid = '$pid' AND bid = '$bid'");
        
        if (!$result3b) {
          echo "<div class='alert alert-danger alert-dismissible' role='alert'>
             Change bidding points failed.
             </div>";
         
        } else {
            echo "<div class='alert alert-success alert-dismissible' role='alert'>
              Change bidding points successfully!
              </div>";
            echo "<meta http-equiv='refresh' content = '3'>";
      
        }
      } else { // User doesn't have enough points
          echo "<div><div class='alert alert-danger alert-dismissible' role='alert'>
            Points you have: $pointsCanUse. Points you bid: $points. You don't have enough points. Bid failed.
            </div></div>";
    }
  }

  if (isset($_POST['bidDelete'])) {
      $aid = $_POST['aId'];
      $pid = $_POST['pId'];
      $bid = $_SESSION['uid'];
      $result3c = pg_query($conn, "DELETE FROM bid  WHERE aid = '$aid' AND pid = '$pid' AND bid = '$bid'");
      if (!$result3c) {
          echo "<div class='alert alert-danger alert-dismissible' role='alert'>
             Quit failed.
             </div>";
         
        } else {
            echo "<div class='alert alert-success alert-dismissible' role='alert'>
              Quit successfully!
              </div>";
            echo "<meta http-equiv='refresh' content = '3'>";
        }
  }

  $result4 = pg_query($conn, "SELECT * 
  FROM availability a, bid b
  WHERE a.aid = b.aid
  AND b.bid = '$uid'
  AND b.status = 'successful'
  ORDER BY b.pid ASC");

  echo "<div>
  <form class='form-signin' action='profile.php' method='POST'>
  <h2 class='form-signin-heading'>My bidding history</h2>
  </form>
  </div>";

  while ($row = pg_fetch_assoc($result4)) {
    echo "<div class='panel panel-warning'><div class='panel panel-heading'><h3>";
        echo "Pet ID: ".$row['pid'];
        echo "</div><div class='panel panel-body'>";
        echo "Carer:  ".$row['cid']." ";
        echo "<br>From    ".$row['afrom']."</h3>"."  to  ".$row['ato'];
        echo "<br>Points: ".$row['points']." ";
        echo "<br>Status: ".$row['status']." ";
        echo "</div></div>";
  }

  $result4 = pg_query($conn, "SELECT * 
  FROM availability a, bid b
  WHERE a.aid = b.aid
  AND b.bid = '$uid'
  AND b.status = 'failed'
  ORDER BY b.pid ASC");

  while ($row = pg_fetch_assoc($result4)) {
    echo "<div class='panel panel-warning'><div class='panel panel-heading'><h3>";
        echo "Pet ID: ".$row['pid'];
        echo "</div><div class='panel panel-body'>";
        echo "Carer:  ".$row['cid']." ";
        echo "<br>From    ".$row['afrom']."</h3>"."  to  ".$row['ato'];
        echo "<br>Points: ".$row['points']." ";
        echo "<br>Status: ".$row['status']." ";
        echo "</div></div>";
  }

?>

<html>
<body background="images/dogs.jpg">
</body>
</html>