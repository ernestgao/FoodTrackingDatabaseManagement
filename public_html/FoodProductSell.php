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
	<title>Purchase</title>
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

	<form method="POST" action="oracle-template.php">
		<input type="hidden" id="resetTablesRequest" name="resetTablesRequest">
		<p><input type="submit" value="Reset" name="reset"></p>
	</form>

	<hr />

	<h2>Find the Food Services who Purchased All the Given Produce</h2>
	<form method="POST" action="Purchase.php">
		<input type="hidden" id="divQueryRequest" name="divQueryRequest">
		Produce IDs (seperate by comma): <input type="text" name="ids"> <br /><br />

		<input type="submit" value="Find" name="divSubmit"></p>
	</form>

	<hr />

	<h2>Find All Purchases</h2>
	<form method="GET" action="Purchase.php">
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
		echo "<br>Retrieved data from Purchase:<br>";
		echo "<table>";
		echo "<tr><th>PRODUCEID</th><th>INGREDIENTNAME</th><th>FSID</th></tr>";

		while ($row = OCI_Fetch_Array($result, OCI_ASSOC)) {
			echo "<tr><td>" . $row["PRODUCEID"] . "</td><td>" . $row["INAME"] . "</td><td>" . $row["FSID"] . "</td></tr>"; 
		}

		echo "</table>";
	}

	function printDivResult($result)
	{
		echo "<br>Retrieved data:<br>";
		echo "<table>";
		echo "<tr><th>FSID</th><th>FSNAME</th><th>FSADDRESS</th></tr>";

		while ($row = OCI_Fetch_Array($result, OCI_ASSOC)) {
			echo "<tr><td>" . $row["FSID"] . "</td><td>" . $row["FSNAME"] . "</td><td>" . $row["FSADDRESS"] . "</td></tr>";
		}

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
			$e = OCI_Error();
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

	function handleResetRequest()
	{
		global $db_conn;

		executePlainSQL("DROP TABLE demoTable");

		echo "<br> creating new table <br>";
		executePlainSQL("CREATE TABLE demoTable (id int PRIMARY KEY, name char(30))");
		oci_commit($db_conn);
	}

	function handleDivRequest()
	{
		global $db_conn;

		if (empty($_POST['ids'])) {
			echo("Please enter at least one Produce ID");
		} else {
			$result = executePlainSQL("SELECT FSID, FSNAME, FSADDRESS
			From FOODSERVICEFROM
			WHERE NOT EXISTS(SELECT ProduceID
							 FROM ContainIngredient
							 WHERE ContainIngredient.ProduceID IN (".$_POST['ids'].")
							 MINUS
							 SELECT P.ProduceID
							 From Purchase P
							 WHERE P.FSID = FoodServiceFrom.FSID)");
			printDivResult($result);
		}
	}

	function handleDisplayRequest()
	{
		global $db_conn;
		$result = executePlainSQL("SELECT * FROM ContainIngredient INNER JOIN Purchase P on ContainIngredient.ProduceID = P.ProduceID");
		printResult($result);
	}

	function handleBackRequest()
{
    header("Home.php");
}

	function handlePOSTRequest()
	{
		if (connectToDB()) {
			if (array_key_exists('resetTablesRequest', $_POST)) {
				handleResetRequest();
			} else if (array_key_exists('divQueryRequest', $_POST)) {
				handleDivRequest();
			}

			disconnectFromDB();
		}
	}

	function handleGETRequest()
	{
		if (connectToDB()) {
			if (array_key_exists('displayTuples', $_GET)) {
				handleDisplayRequest();
			} elseif (array_key_exists('back', $_GET)) {
				handleBackRequest();
			}

			disconnectFromDB();
		}
	}

	if (isset($_POST['reset'])|| isset($_POST['divSubmit'])) {
		handlePOSTRequest();
	} else if (isset($_GET['displayTuplesRequest'])|| isset($_GET['backRequest'])) {
		handleGETRequest();
	}
	?>
</body>

</html>
