<?php

session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: ../index.php');
    exit();
}
if ($_SESSION['perfil'] > 1) {
    echo '<script>alert("Su Perfil no permite la modificacion de este parámetro, Favor contacte al Administrador.");history.back(-1);</script>';
    exit();
}
include("../include/conn.php");
$link = Conectarse(); //Variable de coneccion
$id = $_POST["id"];
$key = $_POST["val"];
$ano= $_POST["ano"];
$fecha = date("Y-m-d H:i:s");

$sql = "UPDATE `pac` SET `pac_cantidad`='$key', `pac_fecha`='$fecha' WHERE pac_codigo='$id' AND pac_clave='$id$ano'";
MySQL_query($sql, $link) or die("<img src='../img/error.png' width='15' heigth='15'>");
$contar = mysql_affected_rows();
if ($contar > 0) {
    echo "<img src='../img/check.png' width='15' heigth='15' title='Cargado OK'>";
} else {
    echo "<img src='../img/error.png' width='15' heigth='15' title='error de carga'>";
}



