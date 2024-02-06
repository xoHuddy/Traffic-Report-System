<?php
	include_once('header.php');
	
	if (empty($_SESSION['officer'])) {
		header('Location: loginOfficer.php');
	}
	
	//Connect to the database
	$db = new PDO('mysql:host=ysjcs.net;dbname=filipehudson', "filipe.hudson", "7AVTPWLB");
	
	// Query the people table
	$query = "SELECT * FROM People";
	$stmt4 = $db->query($query);
	
	// Query the vehicle table
	$query = "SELECT * FROM Vehicle";
	$stmt5 = $db->query($query);
	
	// Query the offence table
	$query = "SELECT * FROM Offence";
	$stmt6 = $db->query($query);
	
	if (isset($_GET['incidentId'])) {
		$incident_id = htmlspecialchars($_GET['incidentId']);
		
		// Query the incident table
		$query = "SELECT I.Incident_ID, I.Incident_Date, I.Incident_Report, 
			O.Offence_description, O.Offence_ID, O.Offence_maxFine, O.Offence_maxPoints,
			P.People_ID, P.People_name, P.People_address, P.People_licence,
			V.Vehicle_ID, V.Vehicle_type, V.Vehicle_colour, V.Vehicle_licence
			FROM Incident I JOIN People P
			ON I.People_ID = P.People_ID
			JOIN Vehicle V
			ON I.Vehicle_ID = V.Vehicle_ID
			JOIN Offence O
			ON I.Offence_ID = O.Offence_ID
			WHERE Incident_ID = $incident_id";
		$stmt7 = $db->query($query);
		$res = $stmt7->fetch();
	}
	
	if (isset($_POST['make'])) {
		$make = htmlspecialchars($_POST['make']);
		$model = htmlspecialchars($_POST['model']);
		$colour = htmlspecialchars($_POST['colour']);
		$plate = htmlspecialchars($_POST['plate']);
		$offence_id = htmlspecialchars($_POST['offence']);
		$date = htmlspecialchars($_POST['date']);
		$report = htmlspecialchars($_POST['report']);
		
		if (!empty($_POST['owner'])) {
			$people_id = htmlspecialchars($_POST['owner']);
		} else if (!empty($_POST['name']) && !empty($_POST['address']) && !empty($_POST['license'])) {
			$name = htmlspecialchars($_POST['name']);
			$address = htmlspecialchars($_POST['address']);
			$license = htmlspecialchars($_POST['license']);
		
			$query = "INSERT INTO People (People_name, People_address, People_licence) 
					VALUES (?, ?, ?)";
			$stmt2 = $db->prepare($query);
			$stmt2->execute([$name, $address, $license]);
			
			$people_id = $db->lastInsertId();
		}
		
		if (!empty($_POST['vehicle'])) {
			$vehicle_id = htmlspecialchars($_POST['vehicle']);
		} else if (!empty($_POST['make']) && !empty($_POST['model']) && !empty($_POST['colour']) && !empty($_POST['plate'])) {
			$make = htmlspecialchars($_POST['make']);
			$model = htmlspecialchars($_POST['model']);
			$colour = htmlspecialchars($_POST['colour']);
			$plate = htmlspecialchars($_POST['plate']);
		
			$query = "INSERT INTO Vehicle (Vehicle_type, Vehicle_colour, Vehicle_licence) 
					VALUES (?, ?, ?)";
			$stmt2 = $db->prepare($query);
			$stmt2->execute([$make . ' ' . $model, $colour, $plate]);
			
			$vehicle_id = $db->lastInsertId();
		}
		
		$query = "INSERT INTO Incident (Vehicle_ID, People_ID, 
				Incident_Date, Incident_Report, Offence_ID)
				VALUES (?, ?, ?, ?, ?)";
		$stmt = $db->prepare($query);
		$stmt->execute([$vehicle_id, $people_id, $date, $report, $offence_id]);
	}
?>
	
	<section class="main-content">
		<h2>Edit an Incident Report</h2>
	</section>
	
	<form action="report.php" method="post">
		<div class="container">
			<label for="date"><b>Incident Date</b></label>
			<input type="date" placeholder="Enter Incident Date" name="date" id="date" value="<?=$res['Incident_Date']?>" required>
			<br><br>
			<label for="report"><b>Incident Report</b></label>
			<textarea placeholder="Enter Incident Description" name="report" id="report" required><?=$res['Incident_Report']?></textarea>
			<br><br><br><br><br><br><br><br><br><br><br><br><br><br>
			<label for="offence"><b>Offence</b></label>
			<br><br>
			<select name="offence" id="offence" size="5" required>
<?php 
			while ($row = $stmt6->fetch(PDO::FETCH_ASSOC)) {
?>
				<option value="<?=$row['Offence_ID']?>"
<?php 
					if ($row['Offence_ID'] == $res['Offence_ID']) {
?>				
						selected
<?php
					}
?>				
				>
				
				<?=$row['Offence_description'] . ' ($' . $row['Offence_maxFine'] . ')'?>

				</option>
<?php
			}
?>
			</select><br><br>
			
			<label for="vehicle"><b>Vehicle</b></label>
			<br><br>
			<select name="vehicle" id="vehicle" size="5" required>
<?php 
			while ($row = $stmt5->fetch(PDO::FETCH_ASSOC)) {
?>
				<option value="<?=$row['Vehicle_ID']?>"
<?php 
					if ($row['Vehicle_ID'] == $res['Vehicle_ID']) {
?>				
						selected
<?php
					}
?>				
				><?=$row['Vehicle_type'] . ' (' . $row['Vehicle_licence'] . ')'?></option>
<?php
			}
?>
			</select><br><br>
			<p>Can't find the vehicle? Add a new one below:</p>
			
			<label for="make"><b>Vehicle Make</b></label>
			<input type="text" placeholder="Enter Vehicle Make" name="make" id="make" value="<?=$res['Vehicle_type']?>">
			<br><br>
			<label for="model"><b>Vehicle Model</b></label>
			<input type="text" placeholder="Enter Vehicle Model" name="model" id="model" value="<?=$res['Vehicle_type']?>">
			<br><br>
			<label for="colour"><b>Vehicle Colour</b></label>
			<input type="text" placeholder="Enter Vehicle Colour" name="colour" id="colour" value="<?=$res['Vehicle_colour']?>">
			<br><br>
			<label for="plate"><b>Vehicle License Plate Number</b></label>
			<input type="text" placeholder="Enter License Plate Number" name="plate" id="plate" value="<?=$res['Vehicle_licence']?>">
			<br><br>
			
			<label for="owner"><b>Person Involved</b></label>
			<br><br>
			<select name="owner" id="owner" size="5" required>
<?php 
			while ($row = $stmt4->fetch(PDO::FETCH_ASSOC)) {
?>
				<option value="<?=$row['People_ID']?>"
<?php 
					if ($row['People_ID'] == $res['People_ID']) {
?>				
						selected
<?php
					}
?>				
				><?=$row['People_name'] . ' (' . $row['People_licence'] . ')'?></option>
<?php
			}
?>
			</select><br><br>
			<p>Can't find the person? Add a new one below:</p>
			<label for="name"><b>Person's Name</b></label>
			<input type="text" placeholder="Enter Owner's Name" name="name" id="name">
			<br><br>
			<label for="address"><b>Person's Address</b></label>
			<input type="text" placeholder="Enter Owner's Address" name="address" id="address">
			<br><br>
			<label for="license"><b>Person's License Number</b></label>
			<input type="text" placeholder="Enter Driving License Number" name="license" id="license">
			<br><br>
			<button type="submit">Add</button>
		</div>
	</form>
	
<?php	
	if (isset($_POST['make'])) {
		if ($stmt->rowCount()) {
			echo('The report has been successfuly edited');
		} else {
			echo('Something went wrong. Try again');
		}
	}
?>

<?php
	include_once('footer.php');
?>

<script>
	var inp1 = document.getElementById("name");
	var inp2 = document.getElementById("address");
	var inp3 = document.getElementById("license");
	var inp4 = document.getElementById("make");
	var inp5 = document.getElementById("model");
	var inp6 = document.getElementById("colour");
	var inp7 = document.getElementById("plate");
	
	function disablePersonSelect() {
		var typedIn = inp1.value != "" || inp2.value != "" || inp3.value != "";
		document.getElementById("owner").disabled = typedIn;
		document.getElementById("name").required = typedIn;
		document.getElementById("address").required = typedIn;
		document.getElementById("license").required = typedIn;
	}
	
	function disableVehicleSelect() {
		var typedIn = inp4.value != "" || inp5.value != "" || inp6.value != "" || inp7.value != "";
		document.getElementById("vehicle").disabled = typedIn;
		document.getElementById("make").required = typedIn;
		document.getElementById("model").required = typedIn;
		document.getElementById("colour").required = typedIn;
		document.getElementById("plate").required = typedIn;
	}
	
	inp1.oninput = function () {disablePersonSelect()};
	inp2.oninput = function () {disablePersonSelect()};
	inp3.oninput = function () {disablePersonSelect()};
	
	inp4.oninput = function() {disableVehicleSelect()};
	inp5.oninput = function() {disableVehicleSelect()};
	inp6.oninput = function() {disableVehicleSelect()};
	inp7.oninput = function() {disableVehicleSelect()};
</script>
