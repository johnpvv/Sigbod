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
$obs = $_POST['obs'];
$id = $_POST['id'];
$fechaactual = strtotime(date("Y-m-d H:i:s"));
$sql2 = "SELECT mov_fecha FROM `movimiento` WHERE `mov_id`='$id'";
$data2 = MySQL_query($sql2, $link) or die("<img src='../img/error.png' width='15' heigth='15'>");
$row2 = mysql_fetch_array($data2);
$fecha = strtotime($row2["mov_fecha"]);
$dif = round(($fechaactual - $fecha) / (3600 * 24));
if ($dif > 1) {
    echo '<script>alert("Error...\nNo se pueden editar solicitudes creadas hace más de 24 horas...(' . $dif . ')");location.reload();</script>';
	echo "<img src='../img/error.png' width='15' heigth='15'>";
    exit();
} else {
    $sql = "UPDATE `movimiento` SET `mov_observacion`='$obs'  WHERE `mov_id`='$id'";
    mysql_query($sql, $link) or die("<script>alert('Ha ocurrido un Error al Grabar: " . mysql_error() . "');</script>");
    $cont = mysql_affected_rows();
    if ($cont == 0) {
        echo '<script>alert("Ha ocurrido un error al Grabar.");</script>';
    } else {
        echo "<img src='../img/check.png' width='18' heigth='18' title='Cargado OK'>";
        echo '<script>alert("Se han actualizado las Observaciones de la Solicitud N°' . $id . ' Correctamente...");</script>';
    }
}
mysql_close($link);
?>
