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
$cod = $_GET['id'];
$opc = $_GET["opc"];
if ($opc == 0) {
    $opv = "history.back(-1)";
} else {
    $opv = "window.close()";
}
if ($cod === NULL) {
    echo '<script>alert("Error...\nNo hay datos para borrar...");
				history.back(-1);</script>';
} else {
    $sql1 = "UPDATE `productos` SET `prd_destacado`= 0 WHERE `prd_codigo`='$cod'";
    MySQL_query($sql1, $link)or die(mysql_error());
    $sql = "DELETE FROM `tiponivel` WHERE `tiponivel_codigo`='$cod'";
    MySQL_query($sql, $link)or die(mysql_error());
    $contar = mysql_affected_rows();
    if ($contar == 0) {
        echo '<script>alert("Error...\nEl codigo ingresado es invalido, o se encuentra Inactivo");
				history.back(-1);</script>';
    } else {
        echo '<script>alert("Felicidades...\nEl codigo: ' . $cod . ' se ha borrado de los niveles de stock exitosamente.");
				' . $opv . ';</script>';
    }
}
mysql_close($link);
?>