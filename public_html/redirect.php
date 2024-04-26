<?php
if (isset($_GET['redirectSubmit'])) {
    header("Location:" . $_GET['redirectTable']);
    exit();
}
header("Location:Home.php");
exit();
?>