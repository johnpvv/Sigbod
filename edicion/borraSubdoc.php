<?php

include_once("../include/conn.php");
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: ../index.php');
    exit();
}

$link = Conectarse();
$usuario = $_SESSION["usuario"];
$date = date("Y-m-d H:i:s");
$id = $_GET['id'];
$rut = $_GET['rut'];
$cont = $_GET['cont'];
$idclave = $_GET["clv"];
$tipo = $_GET["tipo"];
$cod = str_replace($rut, "", $id);
if ($id === NULL or $rut === NULL) {
    echo '<script>alert("Error...\nNo hay datos para borrar...");history.back(-1);</script>';
} else {
    $sql = "DELETE FROM `subdocumento` WHERE `subdoc_clave`='$id'";
    MySQL_query($sql, $link)or die(mysql_error());
    $contar = mysql_affected_rows();
    if ($contar == 0) {
        echo '<script>alert("Error...\nLos Datos ingresados para borrar son inválidos...);history.back(-1);</script>';
    } else {
        if ($tipo == 1) {
            $sql1 = "UPDATE `documento` SET `doc_subdoc_cuenta`= '" . ($cont - 1) . "', `doc_estado`='', `doc_umodif`='$usuario',`doc_fechamod`='$date' WHERE `doc_id`='$idclave'";
        } else {
            $sql1 = "UPDATE `documento` SET `doc_subdoc_cuenta`= '" . ($cont - 1) . "', `doc_umodif`='$usuario',`doc_fechamod`='$date' WHERE `doc_id`='$idclave'";
        }
        MySQL_query($sql1, $link)or die(mysql_error());
        echo '<script>alert("Felicidades...\nEl Subdocumento: ' . $cod . ' se ha borrado Exitosamente.");history.back(-1);</script>';
    }
}
mysql_close($link);
?>