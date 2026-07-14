<?php

include_once("../include/conn.php");
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: ../index.php');
    exit();
}
if ($_SESSION['perfil'] > 1) {
    echo '<script>alert("Su Perfil no permite la visualizacion de esta pantalla, Favor contacte al Administrador.");history.back(-1);</script>';
    echo '<center><button class="boton" onclick="javascript:window.close()">Salir&nbsp;<img class="img" alt="Salir" title="salir de la pantalla de edicion" src="../img/salir.png"></button></center>';
    exit();
}
$link = Conectarse();
$cod = $_GET['id'];
$oc = $_GET['oc'];
$cnt = $_GET['cnt'];
if ($cnt <= 1) {
    $varret = "history.go(-2)";
} else {
    $varret = "history.back(-1)";
}
if ($cod === NULL or $oc === NULL) {
    echo '<script>alert("Error...\nNo hay datos para borrar...");
				history.back(-1);</script>';
} else {
    $sql = "DELETE FROM `saldos` WHERE `saldo_clave`='$oc$cod'";
    MySQL_query($sql, $link)or die(mysql_error());
    $contar = mysql_affected_rows();
    if ($contar == 0) {
        echo '<script>alert("Error...\nLos Datos ingresados para borrar son inválidos...);
				history.back(-1);</script>';
    } else {
        echo '<script>alert("Felicidades...\nEl codigo: ' . $cod . ' se ha borrado de la orden de Compra: ' . $oc . ' Exitosamente.");
				' . $varret . ';</script>';
    }
}
mysql_close($link);
?>