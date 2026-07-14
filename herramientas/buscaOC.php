<?php

include_once("../include/conn.php");
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: ../noSesion.php');
    exit();
}
if (!isset($_POST['oc'])) {
    exit;
}
$link = Conectarse();

$data = array();

$rs = mysql_query("select * from saldos where saldo_oc='" . $_POST['oc'] . "';", $link);
$row = mysql_fetch_array($rs);
$data["oc"] = $row["saldo_oc"];
echo json_encode($data);
mysql_close($link);
?>