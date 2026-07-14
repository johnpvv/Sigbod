<?php

include_once("../include/conn.php");
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: ../noSesion.php');
    exit();
}
$link = Conectarse();
$codbr = $_POST["val"];
$cod = $_POST['cod'];
if ($cod == "" or $codbr == "") {
    echo '<script>alert("Error...\nEl codigo ingresado es invalido, o se encuentra Inactivo");
				history.back(-1);</script>';
} else {
    $sql = "UPDATE productos SET prd_barcode='$codbr' WHERE prd_codigo='$cod'";
    mysql_query($sql, $link) or die("<center><h2 class='texto'>Ha Ocurrido un Error: </h2> " . mysql_error() . "<br/><input type='button' value='Volver Atras' class='boton' onclick='javascript:history.back(-1)'></center>");
}
mysql_close($link);
?>