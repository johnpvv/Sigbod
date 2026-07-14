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
    $header = "RUT;Nombre Proveedor;Giro;Nombre_Contacto;Telefono_Contacto;Email;Estado;fecha_Creacion";
    header('Content-Encoding: UTF-8');
    header('Content-Type: application/vnd.ms-excel; charset=utf-8');
    header("Content-Disposition: attachment; filename=sigbod_proveedor.csv");
    header("Pragma: no-cache");
    header("Expires: 0");
    echo $header . "\n";
    while ($row = mysql_fetch_array($result)) {
        $estado = $row["prov_estado"];
        if ($estado == "0") {
            $estado = "No Vigente";
        } else {
            $estado = "Vigente";
        }
        echo number_format($row["prov_rut"], 0, '.', '.') . "-" . $row["prov_dv"] . ';' . iconv("UTF-8", "WINDOWS-1252", $row["prov_nombre"]) . ';' . iconv("UTF-8", "WINDOWS-1252", $row["prov_giro"]) . ';' . iconv("UTF-8", "WINDOWS-1252", $row["prov_nomcontacto"]) . ';' . $row["prov_telefono"] .';' . $row["prov_email"] . ';' . $estado . ';' . date("d/m/Y", strtotime($row["prov_fechacreacion"])) . "\n";
    }
    mysql_free_result($result);
    mysql_close($link);
}
?>