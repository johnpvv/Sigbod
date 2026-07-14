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

if ($query == "") {
    echo '<script>alert("No Hay Datos para Exportar");				
			history.back(-1);
	</script>';
} else {
    $result = mysql_query($query, $link);
    $header = "Rut Proveedor;Nombre Proveedor;Orden de Compra;Documentos Registrados;Total Valorizado";
    header('Content-Encoding: UTF-8');
    header('Content-Type: application/vnd.ms-excel; charset=utf-8');
    header("Content-Disposition: attachment; filename=sigbod_documentos_det.csv");
    header("Pragma: no-cache");
    header("Expires: 0");
    echo $header . "\n";
    while ($row = mysql_fetch_array($result)) {
        echo $row["prov_rut"] . "-" . $row["prov_dv"] . ';' . iconv("UTF-8", "WINDOWS-1252", $row["prov_nombre"]) . ';' . $row["doc_noc"] . ';' . $row["total"] . ';$' . number_format($row["suma"], 0, ',', '.'). ';'. "\n";
    }
    mysql_free_result($result);
    mysql_close($link);
}
?>