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
$check = $_POST["chk"];
$key = $_POST["key"];
if ($check == "true") {
    $var = 1;
} else {
    $var = 0;
}
$sql = "UPDATE `saldos` SET `saldo_est`='$var' WHERE saldo_id='$key'";
MySQL_query($sql, $link) or die("<img src='../img/error.png' width='15' heigth='15'>");
$contar = mysql_affected_rows();
if ($contar > 0) {
    echo "<img src='../img/check.png' width='15' heigth='15' title='Cargado OK'>";
} else {
    echo "<img src='../img/error.png' width='15' heigth='15' title='error de carga'>";
}



