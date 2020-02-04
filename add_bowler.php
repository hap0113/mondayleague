<!doctype html>
<?php session_start();
include '../MondayLeague/database/connBowl.php'; ?>
<html>
<head>
<meta charset="utf-8">
<title>Add Bowler</title>
<style type="text/css">
.padding {
	padding-top: 12px;
}
</style>
</head>

<body>
<h3>Team
<?php
	include 'api/season.php';
	
	include 'api/weeknumber.php';
	
	//echo $thisweek;
	
	//echo "<a href=index.php> Season ".$thisSeason."</a>";
	
	$q = intval($_GET['q']);
	
	$sqlTeam = "SELECT TeamID, TeamName FROM `tblTeam` WHERE TeamID = ".$q;
	$resultTeam = $conn->query($sqlTeam);
		if ($resultTeam->num_rows>0){
		while ($rowTeam = $resultTeam->fetch_assoc()){
			echo $rowTeam["TeamName"]."</h3>";}
		}
	if (isset($_SESSION['username'])&&(!isset($_POST['add']))){
		echo '<form id="add_bowler" name="add_bowler" method="post">
  <div id="heading">Add New Bowler</div>
   <div id="name">
     <label for="first_name">First Name:</label>
	   <input type="text" name="first_name" required>
     <label for="last_name">Last Name:</label>
	   <input type="text" name="last_name" required>
   </div>
  <div id="others">
    <label for="birth_date">Birth Date:</label>
	   <input type="text" name="birth_date">
	   <label for="gender">Gender:</label>
	   <input type="radio" value="F" name="gender">Female
    <input type="radio" value="M" name="gender" required>Male
   </div>
   <div id="buttons">
     <button type="submit" name="add">add</button>
     <button type="reset">cancel</button>
   </div>
</form>';
	} elseif (isset($_POST['add'], $_SESSION['username'])){
			$fname = $_POST['first_name'];
			$lname = $_POST['last_name'];
			$bday = $_POST['birth_date'];
			$gender = $_POST['gender'];
			$sql_add_bowler = "INSERT INTO tblBowlers (FirstName, LastName, BirthDate, Gender) VALUES ('$fname', '$lname', '$bday', '$gender')";
			if ($conn->query($sql_add_bowler) === TRUE) {
				$last_id = $conn->insert_id;
				//echo "New bowler added";
				$sql_add_bowler_team = "INSERT INTO tblTeamMembers (TeamID, MemberID, SeasonID) VALUES ($q, $last_id, $thisSeason)";
				if ($conn->query($sql_add_bowler_team)===TRUE){
					//echo "add as team member";
					$sql_init_hdcp = "INSERT INTO tblHandicap (BowlerID, Pinfall, Games, WeekNumber, SeasonID, Username) VALUES ($last_id, 0, 0, $thisweek, $thisSeason, 'hap0113')";
					if ($conn->query($sql_init_hdcp)===TRUE){
						//echo "initial handicap added.";
					}
				}
			} else {
				echo "Error: " . $sql . "<br>" . $conn->error;
			}
			echo "<div>".$fname. " ".$lname." was added.</div>";
			echo "<table class='padding'><tr colspan='2'>Team Members</tr>";
			$sql = "SELECT * FROM `tblTeamMembers` INNER JOIN tblBowlers ON tblTeamMembers.MemberID = tblBowlers.bowlerID INNER JOIN tblTeam ON tblTeamMembers.TeamID = tblTeam.TeamID WHERE tblTeamMembers.SeasonID = '".$_SESSION['season']."' AND tblTeamMembers.TeamID = '".$q."'";
			$result1 = $conn->query($sql);
			if ($result1->num_rows>0){
				while ($row1 = $result1->fetch_assoc()){
					echo "<tr>";
					echo "<td>" . $row1['FirstName'] . "</td>";
					echo "<td>" . $row1['LastName'] . "</td>";
					echo "</tr>";
				}
			}
			echo "</table>";
	} else {
		echo "<a href=../MondayLeague/users/login.php?location=".urlencode($_SERVER['REQUEST_URI']).">Login</a> to add bowler.";
	}
	?>
	


</body>
</html>