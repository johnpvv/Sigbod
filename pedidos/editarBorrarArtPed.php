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
$idart = $_GET['idart'];
$sol = $_GET['sol'];
$val = $_GET['val'];
$cod= $_GET['cod'];
$fechaactual = strtotime(date("Y-m-d H:i:s"));
$sql1 = "SELECT mov_fecha,mov_cuenta,mov_valorneto FROM movimiento WHERE mov_id ='$sol'";
$data1 = mysql_query($sql1);
$row1 = mysql_fetch_array($data1);
$fecha = strtotime($row1["mov_fecha"]);
$cnt = $row1["mov_cuenta"];
$neto = $row1["mov_valorneto"];

if ($idart === NULL or $sol === NULL) {
    echo '<script>alert("Error...\nNo hay datos para borrar...");history.back(-1);</script>';
} else {
    $dif = round(($fechaactual - $fecha) / (3600 * 24));
    if ($dif > 1) {
        echo '<script>alert("Error...\nNo se pueden eliminar articulos para solicitudes creadas hace más de 24 horas...");history.back(-1);</script>';
        exit();
    } else {
        if ($cnt <= 1) {
            echo '<script>alert("Error...\nNo se puede eliminar el articulo, ya que hay solo uno, se debe anular la solicitud completa...");history.back(-1);</script>';
            exit();
        } else {
            $sql = "DELETE FROM `detmovimiento` WHERE `detmov_num`='$idart'";
            MySQL_query($sql, $link)or die(mysql_error());
            $contar = mysql_affected_rows();
            if ($contar == 0) {
                echo '<script>alert("Error...\nLos Datos ingresados para borrar son inválidos...");history.back(-1);</script>';
            } else {
                $neton = $neto - $val;
                $cuenta = $cnt - 1;
                $sql2 = "UPDATE `movimiento` SET `mov_cuenta`='$cuenta',mov_valorneto='$neton' WHERE mov_id ='$sol'";
                MySQL_query($sql2, $link)or die(mysql_error());
                echo '<script>alert("Felicidades...\nSe ha borrado el articulo: '.$cod.' Exitosamente.");javascript:location.href="editarPedido.php?id='.$sol.'";</script>';
            }
        }
    }
}
mysql_close($link);
?>