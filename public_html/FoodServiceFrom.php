<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$config["dbuser"] = "ora_jge02";
$config["dbpassword"] = "a78444452";
$config["dbserver"] = "dbhost.students.cs.ubc.ca:1522/stu";
$db_conn = NULL;

$success = true;

$show_debug_alert_messages = False;
?>

<html>

<head>
	<title>FoodServiceFrom</title>
</head>

<body>
<h2>Back To Home</h2>
<form method="GET" action="Home.php">
    <input type="hidden" id="backRequest" name="backRequest">
    <p><input type="submit" value="Back" name="back"></p>
</form>

<hr />

	<h2>Reset</h2>
	<p>If you wish to reset the table press on the reset button. If this is the first time you're running this page, you MUST use reset</p>

	<form method="POST" action="FoodServiceFrom.php">
		<input type="hidden" id="resetTablesRequest" name="resetTablesRequest">
		<p><input type="submit" value="Reset" name="reset"></p>
	</form>

	<hr />

	<h2>Select From FoodServiceFrom Table</h2>
	<form method="POST" action="FoodServiceFrom.php">
		<input type="hidden" id="selectQueryRequest" name="selectQueryRequest">
		Name: <input type="text" name="name"> <br /><br />
		Time(HH:MI:AM): <input type="text" name="time"> <br /><br />
		Location: <input type="text" name="location"> <br /><br />
		Country: <input type="text" name="country"> <br /><br />

		<input type="submit" value="Select" name="selectSubmit"></p>
	</form>

	<hr />

	<h2>Projection of FoodServiceFrom Table</h2>
	<form method="POST" action="FoodServiceFrom.php">
		<input type="hidden" id="projectionQueryRequest" name="projectionQueryRequest">
		<label for="FSID">FSID:</label>
        <input type="checkbox" id="FSID" name="attributes[]" value="FSID"><br>
        
        <label for="FSName">FSName:</label>
        <input type="checkbox" id="FSName" name="attributes[]" value="FSName"><br>
        
        <label for="FSAddress">FSAddress:</label>
        <input type="checkbox" id="FSAddress" name="attributes[]" value="FSAddress"><br>
        
        <label for="Hours">Hours:</label>
        <input type="checkbox" id="Hours" name="attributes[]" value="Hours"><br>
        
        <label for="CountryName">CountryName:</label>
        <input type="checkbox" id="CountryName" name="attributes[]" value="CountryName"><br>
        
        <input type="submit" value="Projection" name="projectionSubmit">
	</form>

	<hr />

	<h2>Count the Tuples in DemoTable</h2>
	<form method="GET" action="FoodServiceFrom.php">
		<input type="hidden" id="countTupleRequest" name="countTupleRequest">
		<input type="submit" name="countTuples"></p>
	</form>

	<hr />

	<h2>Display Tuples in DemoTable</h2>
	<form method="GET" action="FoodServiceFrom.php">
		<input type="hidden" id="displayTuplesRequest" name="displayTuplesRequest">
		<input type="submit" name="displayTuples"></p>
	</form>

	<hr />


	<?php

	function debugAlertMessage($message)
	{
		global $show_debug_alert_messages;

		if ($show_debug_alert_messages) {
			echo "<script type='text/javascript'>alert('" . $message . "');</script>";
		}
	}

	function executePlainSQL($cmdstr)
	{
		global $db_conn, $success;

		$statement = oci_parse($db_conn, $cmdstr);
		
		if (!$statement) {
			echo "<br>Cannot parse the following command: " . $cmdstr . "<br>";
			$e = OCI_Error($db_conn);
			echo htmlentities($e['message']);
			$success = False;
		}

		$r = oci_execute($statement, OCI_DEFAULT);
		if (!$r) {
			echo "<br>Cannot execute the following command: " . $cmdstr . "<br>";
			$e = oci_error($statement);
			echo htmlentities($e['message']);
			$success = False;
		}

		return $statement;
	}

	function executeBoundSQL($cmdstr, $list)
	{
		global $db_conn, $success;
		$statement = oci_parse($db_conn, $cmdstr);

		if (!$statement) {
			echo "<br>Cannot parse the following command: " . $cmdstr . "<br>";
			$e = OCI_Error($db_conn);
			echo htmlentities($e['message']);
			$success = False;
		}

		foreach ($list as $tuple) {
			foreach ($tuple as $bind => $val) {
				
				oci_bind_by_name($statement, $bind, $val);
				unset($val);
			}

			$r = oci_execute($statement, OCI_DEFAULT);
			if (!$r) {
				echo "<br>Cannot execute the following command: " . $cmdstr . "<br>";
				$e = OCI_Error($statement);
				echo htmlentities($e['message']);
				echo "<br>";
				$success = False;
			}
		}
	}

	function printResult($result)
	{
		echo "<br>Retrieved data from FoodServiceFrom:<br>";
		echo "<table>";
		echo "<tr><th>FSID</th><th>FSNAME</th><th>FSADDRESS</th><th>HOURS</th><th>COUNTRYNAME</th></tr>";

		while ($row = OCI_Fetch_Array($result, OCI_ASSOC)) {
			echo "<tr><td>" . $row["FSID"] . "</td><td>" . $row["FSNAME"] . "</td><td>" . $row["FSADDRESS"] . "</td><td>" . $row["HOURS"] . "</td><td>" . $row["COUNTRYNAME"] . "</td></tr>";
		}
		echo "</table>";
	}

	function printResultAttr($result, $attributes)
	{
		echo "<br>Retrieved data from FoodServiceFrom:<br>";
		echo "<table>";
		echo "<tr>";
		foreach ($attributes as $attr) {
			echo("<th>". strtoupper($attr) ."</th>");
		}
		echo "</tr>";

		while ($row = OCI_Fetch_Array($result, OCI_ASSOC)) {
			echo "<tr>";
			foreach($attributes as $attr) {
				echo("<td>". $row[strtoupper($attr)] ."</td>");
			}
		}
		echo "</tr>";
		echo "</table>";
	}

	function connectToDB()
	{
		global $db_conn;
		global $config;

		$db_conn = oci_connect($config["dbuser"], $config["dbpassword"], $config["dbserver"]);

		if ($db_conn) {
			debugAlertMessage("Database is Connected");
			return true;
		} else {
			debugAlertMessage("Cannot connect to Database");
			$e = OCI_Error(); // For oci_connect errors pass no handle
			echo htmlentities($e['message']);
			return false;
		}
	}

	function disconnectFromDB()
	{
		global $db_conn;

		debugAlertMessage("Disconnect from Database");
		oci_close($db_conn);
	}

	function handleBackRequest()
{
    header("Home.php");
}

	function handleResetRequest()
	{
		global $db_conn;

		executePlainSQL("DROP TABLE FoodServiceFrom CASCADE CONSTRAINTS");

		echo "<br> creating new table <br>";
		executePlainSQL("CREATE TABLE FOODSERVICEFROM (
			FSID INT PRIMARY KEY,
			FSNAME CHAR(20),
			FSADDRESS VARCHAR(150),
			HOURS VARCHAR(150),
			COUNTRYNAME CHAR(20) NOT NULL,
			FOREIGN KEY (COUNTRYNAME) REFERENCES COUNTRY(COUNTRYNAME),
			UNIQUE(FSNAME,FSADDRESS)
		)");
		oci_commit($db_conn);
	}

	function handleSelectRequest()
	{
		global $db_conn;

		if (empty($_POST['name']) && empty($_POST['time']) && empty($_POST['location']) && empty($_POST['country'])) {
			$result = executePlainSQL("SELECT * FROM FoodServiveFrom");
			printResult($result);
		} else {
			$query = "SELECT * FROM FoodServiceFrom WHERE ";
			if (!empty($_POST['name'])) {
				$query = $query . "FSNAME = '".$_POST['name']."'";
				if (!empty($_POST['time'])||!empty($_POST['location'])||!empty($_POST['country'])) {
					$query = $query . " AND ";
				}
			}
			if (!empty($_POST['time'])) {
				$query = $query . "TO_DATE(SUBSTR(Hours, 1, INSTR(Hours, '-', 1) - 2), 'HH:MI:AM') <= TO_DATE('".$_POST['time']."', 'HH:MI:AM')
				AND TO_DATE(SUBSTR(Hours, INSTR(Hours, '-', 1) + 2), 'HH:MI:AM') >= TO_DATE('".$_POST['time']."', 'HH:MI:AM')";
				if (!empty($_POST['location'])||!empty($_POST['country'])) {
					$query = $query . " AND ";
				}
			}
			if (!empty($_POST['location'])) {
				$query = $query . "FSADDRESS LIKE '%".$_POST['location']."%'";
				if (!empty($_POST['country'])) {
					$query = $query . " AND ";
				}
			}
			if (!empty($_POST['country'])) {
				$query = $query . "COUNTRYNAME = '".$_POST['country']."'";
			}
			$result = executePlainSQL($query);
			printResult($result);
		}
	}

	function handleProjectionRequest()
	{
		global $db_conn;

		if (empty($_POST['attributes'])) {
			echo("Please select at least one attribute.");
		} else {
			$selectedAttributes = implode(", ", $_POST['attributes']);
			$result = executePlainSQL("SELECT " . $selectedAttributes . " From FoodServiceFrom");
			printResultAttr($result, $_POST['attributes']);
		}
	}

	function handleCountRequest()
	{
		global $db_conn;

		$result = executePlainSQL("SELECT Count(*) FROM FoodServiceFrom");

		if (($row = oci_fetch_row($result)) != false) {
			echo "<br> The number of tuples in FoodServiceFrom: " . $row[0] . "<br>";
		}
	}

	function handleDisplayRequest()
	{
		global $db_conn;
		$result = executePlainSQL("SELECT * FROM FoodServiceFrom");
		printResult($result);
	}

	function handlePOSTRequest()
	{
		if (connectToDB()) {
			if (array_key_exists('resetTablesRequest', $_POST)) {
				handleResetRequest();
			} else if (array_key_exists('selectQueryRequest', $_POST)) {
				handleSelectRequest();
			} else if (array_key_exists('projectionQueryRequest', $_POST)) {
				handleProjectionRequest();
			}
			disconnectFromDB();
		}
	}

	function handleGETRequest()
	{
		if (connectToDB()) {
			if (array_key_exists('countTuples', $_GET)) {
				handleCountRequest();
			} elseif (array_key_exists('displayTuples', $_GET)) {
				handleDisplayRequest();
			} elseif (array_key_exists('back', $_GET)) {
				handleBackRequest();
			}

			disconnectFromDB();
		}
	}

	if (isset($_POST['reset']) || isset($_POST['selectSubmit']) || isset($_POST['projectionSubmit'])) {
		handlePOSTRequest();
	} else if (isset($_GET['countTupleRequest']) || isset($_GET['displayTuplesRequest'])|| isset($_GET['backRequest'])) {
		handleGETRequest();
	}

	?>
</body>

</html>
