<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$config["dbuser"] = "ora_jge02";			// change "cwl" to your own CWL
$config["dbpassword"] = "a78444452";	// change to 'a' + your student number
$config["dbserver"] = "dbhost.students.cs.ubc.ca:1522/stu";
$db_conn = NULL;
$success = true;
$show_debug_alert_messages = False;

?>

<html>

<head>
	<title>Home</title>
</head>

<body>
	<h2>Reset</h2>
	<p>If you wish to reset the table press on the reset button. If this is the first time you're running this page, you MUST use reset</p>

	<form method="POST" action="Home.php">
		<input type="hidden" id="resetTablesRequest" name="resetTablesRequest">
		<p><input type="submit" value="Reset" name="reset"></p>
	</form>

	<hr />

    <h2>Choose Table</h2>
    <p>Choose the table in the database you would like to work on.</p>
    <form method="GET" action="redirect.php">
        <label for="table">Choose a Table</label>
        <select id="table" name="redirectTable">
            <option value="Country.php">Country</option>
            <option value="FoodServiceFrom.php">FoodServiceFrom</option>
            <option value="FoodProductSell.php">FoodProductSell</option>
            <!-- <option value="GroceryStore.php">GroceryStore</option> -->
            <!-- <option value="Restaurant.php">Restaurant</option> -->
            <!-- <option value="ContainIngredient.php">ContainIngredient</option> -->
            <option value="Purchase.php">Purchase</option>
            <!-- <option value="NutritionBenefits.php">NutritionBenefits</option> -->
            <!-- <option value="IncludeNutrition.php">IncludeNutrition</option> -->
            <!-- <option value="IngredientExpiration.php">IngredientExpiration</option> -->
            <!-- <option value="DiseaseSolutions.php">DiseaseSolutions</option> -->
            <!-- <option value="AgriculturalDisease.php">AgriculturalDisease</option> -->
            <!-- <option value="Affect.php">Affect</option> -->
            <!-- <option value="SymptomEffects.php">SymptomEffects</option> -->
            <!-- <option value="HealthIssue.php">HealthIssue</option> -->
            <!-- <option value="Medicine.php">Medicine</option> -->
            <!-- <option value="Cause.php">Cause</option> -->
            <!-- <option value="Treatment.php">Treatment</option> -->
        </select>
        <input type="submit" name="redirectSubmit">
    </form>


	<hr />

	<h2>Search Food Product Information</h2>
	<p>Put the Food Name, Search ingredients and their nutritional benefits.</p>

	<form method="GET" action="Home.php">
    <input type="hidden" id="foodProductName" name="foodProductName">
    Food Name: <input type="text" name="foodName"> <br /><br />
    

    <input type="submit" value="Search" name="SearchSubmit"></p>
	</form>


<hr />

<h2>Find out How Many Health Issues Food Can Cause</h2>


<form method="GET" action="Home.php">
<input type="hidden" id="foodIssuesName" name="foodIssuesName">

Issues Number: <input type="number" name="foodIssuesNumber"> <br /><br />

<input type="submit" value="Search" name="IssuesSubmit"></p>

</form>


<hr />

<h2>Search Food with Most Nutrient</h2>


<form method="GET" action="Home.php">
<input type="hidden" id="Nutrientfood" name="Nutrientfood">

<!-- Nutrient: <input type="text" name="Nutrientfood"> <br /><br /> -->

<input type="submit" value="Search" name="NutNameSubmit"></p>

</form>



<hr />

<h2>Find out the Maximum Average of the Amount of Nutrient Served by a Food Service</h2>


<form method="POST" action="Home.php">
<input type="hidden" id="nutrientQueryRequest" name="nutrientQueryRequest">

Nutrient: <input type="text" name="nutrientName"> <br /><br />

<input type="submit" value="Search" name="getNutrient"></p>

</form>

<hr />




	<?php
	function isSafeInput($input) {
		
		$forbiddenKeywords = array("INSERT", "DROP", "UPDATE", "DELETE", "TRUNCATE", "ALTER", "CREATE", "GRANT", "REVOKE", "EXECUTE");
	
		$inputUpper = strtoupper($input);
	
		foreach ($forbiddenKeywords as $keyword) {
			if (strpos($inputUpper, $keyword) !== false) {
				return false; 
			}
		}
	
		return true; 
	}



	function debugAlertMessage($message)
	{
		global $show_debug_alert_messages;

		if ($show_debug_alert_messages) {
			echo "<script type='text/javascript'>alert('" . $message . "');</script>";
		}
	}

	function executePlainSQL($cmdstr)
	{	global $db_conn, $success;

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
	{	global $db_conn, $success;
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

	function printNutsResult($result) 
	{
		echo "<br>Retrieved data from table:<br>";
		echo "<table>";
		echo "<tr>
		<th>FPNAME</th>
		<th></th>
		<th>SUM AMOUNT</th>
	  </tr>";

		while ($row = OCI_Fetch_Array($result, OCI_ASSOC)) {
			echo "<tr>
			<td>" . $row["FPNAME"] . "</td> <td></td>  <td>" . $row["SUMA"] . "</td>
			
			</tr>";
		}

		echo "</table>";



	}

	function printNutrientResult($result)
	{

		$numRows = oci_num_rows($result);


		echo "<br>Retrieved data from table:<br>";
		echo "<table>";
		echo "<tr>
		<th>FPNAME</th>
		<th></th>
		<th>AVGAMOUNT</th>
	  </tr>";

		while ($row = OCI_Fetch_Array($result, OCI_ASSOC)) {
			echo "<tr>
			<td>" . $row["FPNAME"] . "</td> <td></td>  <td>" . $row["MAXAVGNUTRIENTAMOUNT"] . "</td>
			
			</tr>";
		}

		echo "</table>";
	}

	function printIssuesResult($result) 
	{
		

		echo "<br>Retrieved data from table:<br>";
		echo "<table>";
		echo "<tr>
		<th>FPNAME</th>
		<th></th>
		<th>Issues Number</th>
	
	  </tr>";
	  while ($row = OCI_Fetch_Array($result, OCI_ASSOC)) {
		echo "<tr>
		<td>" . $row["FPNAME"] . "</td> <td></td>  <td>" . $row["COUNTISSUES"] . "</td> 
		
		</tr>";
	}

	echo "</table>";

	  
	}

	function printResult($result)
	{

		$numRows = oci_num_rows($result);


		echo "<br>Retrieved data from table:<br>";
		echo "<table>";
		echo "<tr>
		<th>FPNAME</th>
		<th></th>
		<th>CHEM NAME</th>
		<th></th>
		<th>Benefits</th>
	  </tr>";

		while ($row = OCI_Fetch_Array($result, OCI_ASSOC)) {
			echo "<tr>
			<td>" . $row["FPNAME"] . "</td> <td></td>  <td>" . $row["CHEMNAME"] . "</td> <td> </td> <td> " . $row["BENEFITS"] . "</td>
			
			</tr>";
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

	function handleSearchRequest() 
	{
		global $db_conn;
		 $Fname = $_GET['foodName'];
		if(isSafeInput($Fname)){
			$result = executePlainSQL("SELECT *
			FROM FoodProductSell FDS
			JOIN ContainIngredient CTI ON FDS.FPName = CTI.FPName
			JOIN IncludeNutrition ICN ON CTI.ProduceID = ICN.ProduceID
			JOIN NutritionBenefits NB ON ICN.ChemName = NB.ChemName
			WHERE FDS.FPName = '$Fname'");
			printResult($result);
		} else {
			echo "<br>Can not include Sensitive Words<br>";
		}
		
		
	}

	function handleFoodIssuesRequest() 
	{
		global $db_conn;
		$Number =$_GET['foodIssuesNumber'];
		$result = executePlainSQL("SELECT DISTINCT FPNAME,Count(ISSUENAME) as countIssues FROM CAUSE Group by  FPNAME having Count(ISSUENAME) > '$Number'");
		printIssuesResult($result);
	}



function getMaxAverageNutrientAmount($nutrientName) {
	global $db_conn;

	$sql = "
		SELECT FS.FSName, MAX(AvgNutrition.Amount) as MaxAvgNutrientAmount
		FROM FoodServiceFrom FS
		JOIN (
			SELECT FPS.FSID, AVG(INut.Amount) AS Amount
			FROM FoodProductSell FPS
			JOIN ContainIngredient CI ON FPS.FPName = CI.FPName
			JOIN IncludeNutrition INut ON CI.ProduceID = INut.ProduceID
			WHERE INut.ChemName = '$nutrientName'
			GROUP BY FPS.FSID
		) AvgNutrition ON FS.FSID = AvgNutrition.FSID
		GROUP BY FS.FSName
	";

	return executePlainSQL($sql);
}

function handleNutFoodRequest() {
		global $db_conn;
		// $Nutrinet =$_GET['Nutrientfood'];
		$result = executePlainSQL("SELECT DISTINCT CN.FPNAME, SUM(INC.Amount) as suma FROM CONTAININGREDIENT CN,INCLUDENUTRITION INC WHERE CN.ProduceID = INC.ProduceID Group by  CN.FPNAME" );
		printNutsResult($result);
}

function handleGetNutrientRequest() {
	if (isset($_POST['nutrientName'])) {
		$nutrientName = $_POST['nutrientName'];
		$results = getMaxAverageNutrientAmount($nutrientName);

		printNutrientResult($results);
	}
}
	

	function handleResetRequest()
	{
		global $db_conn;
        $query = file_get_contents('createscript.sql');
        $stid = oci_parse($db_conn, $query);
        oci_execute($stid);
		oci_commit($db_conn);
	}

	function handlePOSTRequest()
	{
		if (connectToDB()) {
			if (array_key_exists('resetTablesRequest', $_POST)) {
				handleResetRequest();
			} else if (array_key_exists('nutrientQueryRequest', $_POST)) {
				handleGetNutrientRequest();
			}

			disconnectFromDB();
		}
	}

	function handleGETRequest() 
	{
		if (connectToDB()) {
			if (array_key_exists('foodProductName', $_GET)) {
				handleSearchRequest();
			} elseif (array_key_exists('foodIssuesName', $_GET)) {
				handleFoodIssuesRequest();
			} elseif (array_key_exists('Nutrientfood',$_GET))	{
				handleNutFoodRequest();
			}

			disconnectFromDB();
		}
	}
	

	if (isset($_POST['reset']) || isset($_POST['getNutrient'])) {
        handlePOSTRequest();
    } else if (isset($_GET['SearchSubmit']) || (isset($_GET['IssuesSubmit'])) || (isset($_GET['NutNameSubmit'])) ) {
		handleGETRequest();
	}
	?>
</body>

</html>
