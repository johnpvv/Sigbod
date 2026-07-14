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
$fechaactual = strtotime(date("Y-m-d H:i:s"));
$key = $_POST["key"];
$neto = $_POST["neto"];
$cant = $_POST["cant"];
$id = $_POST["ids"];

if ($key == "") {
    echo "<img src='../img/error.png' width='15' heigth='15' title='error de carga'>";
    exit();
} else {
    $sql = "SELECT * FROM `detmovimiento` WHERE `detmov_num`='$key'";
    $data = MySQL_query($sql, $link) or die("<img src='../img/error.png' width='15' heigth='15'>");
    $row = mysql_fetch_array($data);
    $cod = $row["detmov_codigoprd"];
    $oc = $row["detmov_oc"];
    $sql1 = "SELECT saldo_pendiente FROM `saldos` WHERE `saldo_clave`='$oc$cod'";
    $data1 = MySQL_query($sql1, $link) or die("<img src='../img/error.png' width='15' heigth='15'>");
    $row1 = mysql_fetch_array($data1);
    $saldo = $row1["saldo_pendiente"];
    if ($cant > $saldo) {
        echo "<script>alert('Error: el valor ingresado, es mayor a lo pendiente en la orden de compra: " . $oc . ", (" . $saldo . " unidades), se restablece al valor inicial.');location.reload();</script>";
    } else {
        $sql2 = "SELECT * FROM `movimiento` WHERE `mov_id`='$id'";
        $data2 = MySQL_query($sql2, $link) or die("<img src='../img/error.png' width='15' heigth='15'>");
        $row2 = mysql_fetch_array($data2);
        $fecha = strtotime($row2["mov_fecha"]);
        $dif = round(($fechaactual - $fecha) / (3600 * 24));
        if ($dif > 1) {
            echo '<script>alert("Error...\nNo se pueden editar articulos para solicitudes creadas hace más de 24 horas...(' . $dif . ')");location.reload();</script>';
            exit();
        } else {
            $sql3 = "UPDATE `detmovimiento` SET `detmov_cantidad`='$cant',`detmov_edit`='1' WHERE `detmov_num`='$key'";
            mysql_query($sql3, $link) or die("<img src='../img/error.png' width='15' heigth='15'>");
            $contar .= mysql_affected_rows();
            $sql4 = "UPDATE `movimiento` SET `mov_valorneto`='$neto' WHERE `mov_id`='$id'";
            mysql_query($sql4, $link) or die("<img src='../img/error.png' width='15' heigth='15'>");
            $contar .= mysql_affected_rows();
            if ($contar > 0) {
                echo "<script>alert('Se ha Actualizado el valor registrado.');</script>";
                echo "<img src='../img/check.png' width='15' heigth='15' title='Cargado OK'>";                
            } else {
                echo "<img src='../img/error.png' width='15' heigth='15' title='error de carga'>";
            }
        }
    }
}
mysql_close($link);
?>

