<?php

session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: ../index.php');
    exit();
}
$usuario = $_SESSION["usuario"];
$query = $_SESSION['query'];
ini_set('memory_limit', '3000M');
set_time_limit(3000);
include("../include/conn.php");
$link = Conectarse();
$result = mysql_query($query, $link);
if ($query == "") {
    echo '<script>alert("No Hay Datos para Exportar");				
			history.back(-1);
	</script>';
} else {
    $header = "Rut usuario;Nombre Usuario;Email;Telefono;Perfil;Estado;Unidad;Fecha de Creacion";
    header('Content-Encoding: UTF-8');
    header('Content-Type: application/vnd.ms-excel; charset=utf-8');
    header("Content-Disposition: attachment; filename=sigbod_usuarios.csv");
    header("Pragma: no-cache");
    header("Expires: 0");
    echo $header . "\n";
    while ($row = mysql_fetch_array($result)) {
        $estado = $row["usuario_estado"];
        if ($estado == "0") {
            $estado = "No Vigente";
        } else {
            $estado = "Vigente";
        }
        echo $row["usuario_rut"] . ';' . $row["usuario_nombre"] . " " . $row["usuario_apellidos"] . ';' . $row["usuario_email"] . ';' . $row["usuario_telefono"] . ';' . $row["perfil_nombre"] . ';' . $estado . ';' . $row["uni_nombre"] . ';' . $row["usuario_fechaIngreso"] . "\n";
    }
    mysql_free_result($result);
    mysql_close($link);
}
?>