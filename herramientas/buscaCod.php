<?php

include_once("../include/conn.php");
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: ../noSesion.php');
    exit();
}
if (!isset($_POST['val'])) {
    exit;
}
$link = Conectarse();

$data = array();

$rs = mysql_query("select * from productos, unimed where prd_codigo='" . $_POST['val'] . "' AND prd_unimed=unimed_id;", $link);
$row = mysql_fetch_array($rs);

$data["cm"] = $row["prd_codcm"];
$data["glosa"] = $row["prd_glosa"];
$data["um"] = $row["unimed_nombre"];
$data["prec"] = $row["prd_precio"];
$data["estado"]=$row["prd_estado"];

echo json_encode($data);
mysql_close($link);
?>