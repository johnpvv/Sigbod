<?php
session_start();
if (!isset($_SESSION['usuario']) && isset($_SESSION['cambiar'])) {//cambiar para saber si viene de la creacion de usuario desde el login
    header('Location: ../index.php');
    exit();
}
include_once("../include/conn.php");
if (!isset($_POST['dato'])) {
    exit;
}
$link = Conectarse();
$data = array();

$rs = mysql_query("SELECT * FROM usuarios WHERE usuario_rut='" . $_POST['dato'] ."'", $link);
$row = mysql_fetch_array($rs);

$data["nombre"] = $row["usuario_nombre"]." ".$row["usuario_apellidos"];

echo json_encode($data);
mysql_close($link);
?>