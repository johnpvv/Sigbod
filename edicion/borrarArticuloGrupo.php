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
$grp = $_GET['grp'];
$cont = $_GET['cont'];
$cod=$_GET['cod'];
if ($id === NULL or $grp === NULL) {
    echo '<script>alert("Error...\nNo hay datos para borrar...");history.back(-1);</script>';
} else {
    $sql = "DELETE FROM `grupo_productos_detalle` WHERE `grupo_prd_id_id`='$grp' AND `grupo_prd_det_id`='$id'";
    MySQL_query($sql, $link)or die(mysql_error());
    $contar = mysql_affected_rows();
    if ($contar == 0) {
        echo '<script>alert("Error...\nLos Datos ingresados para borrar son inválidos...);history.back(-1);</script>';
    } else {
        $sql1 = "UPDATE `grupo_productos` SET `grupo_prd_cuenta`= '" . ($cont - 1) . "' WHERE `grupo_prd_id`='$grp'";
    }
    MySQL_query($sql1, $link)or die(mysql_error());
    echo '<script>alert("Felicidades...\nEl Articulo seleccionado: '.$cod.' se ha borrado Exitosamente.");history.back(-1);</script>';
}
mysql_close($link);
?>