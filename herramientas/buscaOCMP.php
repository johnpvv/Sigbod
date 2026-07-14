<?php

set_time_limit(100);
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: ../noSesion.php');
    exit();
}
ini_set('display_errors', false);
if (!isset($_POST['oc'])) {
    exit;
}
include_once("../include/conn.php");
$link = Conectarse(); //Variable de coneccion
$rs = mysql_query("SELECT * FROM const_config WHERE const_cat ='ticket' AND const_est='1'", $link); //constantes de configuracion, reconoce todos los categoria ticket guardados
$row1 = mysql_fetch_array($rs);
$ticket = $row1["const_val"];

$data = json_decode(file_get_contents('https://api.mercadopublico.cl/servicios/v2/publico/ordenesdecompra.json?codigo=' . $_POST['oc'] . '&ticket='.$ticket),true);

echo json_encode($data);

?>