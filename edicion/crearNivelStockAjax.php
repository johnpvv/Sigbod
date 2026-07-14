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
$cod = $_POST["cod"];
$fam = $_POST["fam"];
$cri = $_POST["txtcri"];
$min = $_POST["txtmin"];
$max = $_POST["txtmax"];
$date = date("y-m-d H:i:s");

$sql2 = "INSERT IGNORE INTO `tiponivel` (`tiponivel_codigo`,`tiponivel_fam`,`tiponivel_critico`,`tiponivel_min`,`tiponivel_max`,`tiponivel_fecha`)VALUES('$cod','$fam','$cri','$min','$max','$date')";
mysql_query($sql2, $link);
$contar = mysql_affected_rows();
    if ($contar == 0) {
        echo '<script>alert("Error...\nLos Datos ingresados son inválidos...);
				history.back(-1);</script>';
    } else {
        echo '<script>alert("Felicidades...\nEl codigo: ' . $cod . ' se ha Creado Exitosamente.");
				window.close();</script>';
    }
?>