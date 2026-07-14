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
$usuario = $_SESSION["usuario"];
$date = date("Y-m-d H:i:s");
$id = $_GET['id'];
$idseg=$_GET['idseg'];
$cont = $_GET['cont'];
$doc = $_GET['doc'];
if ($id === NULL or $idseg === NULL) {
    echo '<script>alert("Error...\nNo hay datos para borrar...");history.back(-1);</script>';
} else {
    $sql = "DELETE FROM `seguimiento_detalle` WHERE `seg_id_det`='$id'";
    MySQL_query($sql, $link)or die(mysql_error());
    $contar = mysql_affected_rows();
    if ($contar == 0) {
        echo '<script>alert("Error...\nLos Datos ingresados para borrar son inválidos...);history.back(-1);</script>';
    } else {
        $sql1 = "UPDATE `seguimiento` SET `seg_cant`= '" . ($cont - 1) . "' WHERE `seg_id`='$idseg'";
    }
    MySQL_query($sql1, $link)or die(mysql_error());
    echo '<script>alert("Felicidades...\nEl Documento seleccionado N°: '.$doc.' se ha borrado Exitosamente.");history.back(-1);</script>';
}
mysql_close($link);
?>