<!DOCTYPE html>
<!--
Sigbod John Vaccarella
-->
<?php
include_once("../include/conn.php");
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: ../noSesion.php');
    exit();
}
$user = $_SESSION["usuario"];
$date = date("d-m-Y H:i");
$link = Conectarse();
$id = $_POST['id'];
$obs = $_POST["obs"];
$obs = $obs . " <b>(Fecha Anulacion: " . $date . ", Usuario: " . $user . ")</b>";
$sql = "UPDATE `movimiento` SET `mov_observacion`='$obs' , `mov_estado`='0' WHERE `mov_id`='$id'";
mysql_query($sql, $link) or die("<script>alert('Ha ocurrido un Error al Grabar: " . mysql_error() . "');</script>");
$cont = mysql_affected_rows();
if ($cont == 0) {
    echo '<script>alert("Ha ocurrido un error al Grabar.");</script>';
} else {
    echo '<script>alert("La Solicitud N°' . $id . ' se ha Anulado correctamente...");history.back(-1);</script>';
}
mysql_close($link);
?>