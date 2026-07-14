<?php

session_start();
if (!isset($_SESSION['usuario'])) {
    echo '<script>alert("Error con el inicio de Sesion en el Sistema...");history.back(-1);</script>';
    exit();
}
$usuario = $_SESSION["usuario"];
$query = $_SESSION['query_solicitudes'];
$dia = $_SESSION["dia1"];
ini_set('memory_limit', '3000M');
set_time_limit(3000);
include("../include/conn.php");
$link = Conectarse();
if ($query == "") {
    echo '<script>alert("No Hay Datos para Exportar");history.back(-1);</script>';
} else {
    $result = mysql_query($query, $link);
    $fechaactual = $_SESSION["fecha1"];
    $header = "Codigo Producto;Nombre Producto;Unidad Medida;Fecha Solicitud;Orden de Compra; RUT Proveedor; Nombre Proveedor;Folio Solicitud Interna;Cantidad Solicitada;Precio Neto OC;Total c/IVA";
    header('Content-Encoding: UTF-8');
    header('Content-Type: application/vnd.ms-excel; charset=utf-8');
    header("Content-Disposition: attachment; filename=sigbod_Productos_Solicitados.csv");
    header("Pragma: no-cache");
    header("Expires: 0");
    echo $header . "\n";
    while ($row = mysql_fetch_array($result)) {
        $codigo = $row["prd_codigo"];
        $id = $row["mov_id"];
        $fechamov = $row["mov_fecha"];
        echo $codigo . ';' . iconv("UTF-8", "WINDOWS-1252", $row["prd_glosa"]) . ';' . $row["unimed_nombre"] . ';' . date("d/m/Y", strtotime($fechamov)) . ';' . $row["detmov_oc"] . ';' . $row["prov_rut"] . ';' . iconv("UTF-8", "WINDOWS-1252", $row["prov_nombre"]) . ';' . $id . ';' . $row["detmov_cantidad"] . ';' . number_format($row["detmov_precio"], 2, ',', '.') . ';' . number_format(($row["detmov_precio"] * $row["detmov_cantidad"]) * 1.19, 2, ',', '.') . ';' . "\n";
    }
    mysql_free_result($result);
    mysql_close($link);
}
?>