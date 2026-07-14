<?php
include_once("../include/conn.php");
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: ../index.php');
    exit();
}
if ($_SESSION['perfil']>1) {
	echo '<script>alert("Su Perfil no permite la visualizacion de esta pantalla, Favor contacte al Administrador.");history.back(-1);</script>';
	echo '<center><button class="boton" onclick="javascript:window.close()">Salir&nbsp;<img class="img" alt="Salir" title="salir de la pantalla de edicion" src="../img/salir.png"></button></center>';
	exit();
}
$link = Conectarse();
$oc= $_GET['oc'];
if ($oc===NULL) {
    echo '<script>alert("Error...\nNo hay datos para borrar...");
				history.back(-1);</script>';
} else {
    $sql = "DELETE FROM `saldos` WHERE `saldo_oc`='$oc'";
    MySQL_query($sql, $link)or die("Ha ocurrido un error...".mysql_error());
    $contar = mysql_affected_rows();
    if ($contar == 0) {
        echo '<script>alert("Error...\nLos Datos ingresados para borrar son Inválidos...);
				history.back(-1);</script>';
    } else {
        echo '<script>alert("Felicidades...\nSe han Eliminado: '.$contar.' registro(s) de la orden de Compra: '.$oc.' Exitosamente.");
				history.back(-1);</script>';
    }
}
mysql_close($link);
?>