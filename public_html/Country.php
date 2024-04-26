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
    <title>Country</title>
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

<form method="POST" action="Country.php">
		<input type="hidden" id="resetTablesRequest" name="resetTablesRequest">
		<p><input type="submit" value="Reset" name="reset"></p>
	</form>

<hr />

<h2>Insert Values into Country Table</h2>
<form method="POST" action="Country.php">
    <input type="hidden" id="insertQueryRequest" name="insertQueryRequest">
    Country Name: <input type="text" name="countryName"> <br /><br />
    Population: <input type="text" name="population"><br /><br />
    Continent: <input type="text" name="continent"> <br /><br />

    <input type="submit" value="Insert" name="insertSubmit"></p>
</form>

<hr />

<h2>Delete Values From Country Table</h2>
<form method="POST" action="Country.php">
    <input type="hidden" id="deleteQueryRequest" name="deleteQueryRequest">
    Country Name: <input type="text" name="countryName"> <br /><br />
    Population: <input type="text" name="population">(>, <, =, >=, <=, <>)</ input><br /><br />
    Continent: <input type="text" name="continent"> <br /><br />

    <input type="submit" value="Delete" name="deleteSubmit"></p>
</form>

<hr />

<h2>Update Population of Country</h2>

<form method="POST" action="Country.php">
    <input type="hidden" id="updateQueryRequest" name="updateQueryRequest">
    Country: <input type="text" name="country"> <br /><br />
    New Population: <input type="text" name="newPopulation"> <br /><br />

    <input type="submit" value="Update" name="updateSubmit"></p>
</form>

<hr />

<h2>Count the Tuples in Country</h2>
<form method="GET" action="Country.php">
    <input type="hidden" id="countTupleRequest" name="countTupleRequest">
    <input type="submit" name="countTuples"></p>
</form>

<hr />

<h2>Display Tuples in Country</h2>
<form method="GET" action="Country.php">
    <input type="hidden" id="displayTuplesRequest" name="displayTuplesRequest">
    <input type="submit" name="displayTuples"></p>
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
    
   

    echo "<br>Retrieved data from Country:<br>";
    echo "<table>";
    echo "<tr><th>CountryName</th><th>Population</th><th>Continent</th></tr>";

    while ($row = OCI_Fetch_Array($result, OCI_ASSOC)) {
        echo "<tr><td>" . $row["COUNTRYNAME"] . "</td><td>" . $row["POPULATION"] . "</td><td>" . $row["CONTINENT"] . "</td></tr>"; 
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

function handleBackRequest()
{
    header("Home.php");
}

function handleResetRequest()
	{
		global $db_conn;
		executePlainSQL("DROP TABLE Country cascade constraints");

		echo "<br> creating new table <br>";
		executePlainSQL("CREATE TABLE Country (
            CountryName CHAR(20) PRIMARY KEY,
            Population REAL NOT NULL,
            Continent CHAR(20) NOT NULL
        )");
		oci_commit($db_conn);
	}

function handleInsertRequest()
{
    global $db_conn;

    $tuple = array(
        ":bind1" => $_POST['countryName'],
        ":bind2" => $_POST['population'],
        ":bind3" => $_POST['continent'],
    );

    $alltuples = array(
        $tuple
    );

    executeBoundSQL("insert into Country values (:bind1, :bind2, :bind3)", $alltuples);
    oci_commit($db_conn);
}

function handleDeleteRequest()
{
    global $db_conn;
    if (empty($_POST['countryName']) && empty($_POST['population']) && empty($_POST['continent'])) {
        echo("Must enter at least one condition.");
    } else {
    if (!empty($_POST['countryName'])) {
        executePlainSQL("DELETE from Country where CountryName = '".$_POST['countryName']."'");
    }
    if (!empty($_POST['population'])){
        executePlainSQL("DELETE from Country where Population".$_POST['population']);
    }
    if (!empty($_POST['continent'])) {
        executePlainSQL("DELETE from Country where Continent = '".$_POST['continent']."'");
    }
    oci_commit($db_conn);
}
}

function handleUpdateRequest()
{
    global $db_conn;

    $old_name = $_POST['country'];
    $new_name = $_POST['newPopulation'];

    executePlainSQL("UPDATE Country SET Population='" . $new_name . "' WHERE CountryName='" . $old_name . "'");
    oci_commit($db_conn);
}

function handleCountRequest()
{
    global $db_conn;

    $result = executePlainSQL("SELECT Count(*) FROM Country");



    if (($row = oci_fetch_row($result)) != false) {
        echo "<br> The number of tuples in Country: " . $row[0] . "<br>";
    }
}

function handleDisplayRequest()
{
    global $db_conn;
    $result = executePlainSQL("SELECT * FROM Country");
    printResult($result);
}

function handlePOSTRequest()
{
    if (connectToDB()) {
        if (array_key_exists('updateQueryRequest', $_POST)) {
            handleUpdateRequest();
        } else if (array_key_exists('insertQueryRequest', $_POST)) {
            handleInsertRequest();
        } else if (array_key_exists('resetTablesRequest', $_POST)) {
            handleResetRequest();
        } else if (array_key_exists('deleteQueryRequest', $_POST)) {
            handleDeleteRequest();
        }

        disconnectFromDB(array_key_exists('insertQueryRequest', $_POST));
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

if (isset($_POST['reset']) || isset($_POST['updateSubmit']) || isset($_POST['insertSubmit']) || isset($_POST['deleteSubmit'])) {
    handlePOSTRequest();
} else if (isset($_GET['countTupleRequest']) || isset($_GET['displayTuplesRequest'])|| isset($_GET['backRequest'])) {
    handleGETRequest();
}

?>
</body>

</html>
