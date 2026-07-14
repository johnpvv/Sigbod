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

$rs = mysql_query("SELECT * FROM proveedores WHERE prov_rut='" . $_POST['val'] ."'", $link);
$row = mysql_fetch_array($rs);

$data["dv"] = $row["prov_dv"];
$data["nombre"] = $row["prov_nombre"];
$data["giro"] = $row["prov_giro"];
$data["contacto"] = $row["prov_nomcontacto"];
$data["dir"] = $row["prov_direccion"];

echo json_encode($data);
mysql_close($link);
?>