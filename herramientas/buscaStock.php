<?php

include_once("../include/conn.php");
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: ../noSesion.php');
    exit();
}
if (!isset($_POST['cod'])) {
    exit;
}
$link = Conectarse();

$data = array();

$rs = mysql_query("select * from stock Where stock_codigo='" . $_POST['cod'] . "'", $link);
$row = mysql_fetch_array($rs);

$data["stock"] = $row["stock_cantidad"];
$data["codigo"] = $row["stock_codigo"];

echo json_encode($data);
mysql_close($link);
?>