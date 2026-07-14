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
$cont=  strpos($query, "nivelstock_contador");
$result = mysql_query($query, $link);
if ($query == "") {
    echo '<script>alert("No Hay Datos para Exportar");				
			history.back(-1);
	</script>';
} else {
    $header = "Codigo Articulo;Glosa;Presentacion;N de Resultados;Fecha Ultimo Analisis";
    header('Content-Encoding: UTF-8');
    header('Content-Type: application/vnd.ms-excel; charset=utf-8');
    header("Content-Disposition: attachment; filename=sigbod_analisis_stock.csv");
    header("Pragma: no-cache");
    header("Expires: 0");
    echo $header . "\n";    
    while ($row = mysql_fetch_array($result)) {
        if($cont==0){
            $tipo="nivelstock_cuentatotal";
        }else{
            $tipo="nivelstock_contador";
        }
        echo $row["nivelstock_cod"]. ';' . $row["prd_glosa"]. ';' . $row["unimed_nombre"]. ';' . $row[$tipo]. ';' . date("d/m/Y h:i:s A", strtotime($row["nivelstock_fecha"]))  . "\n";
    }
    mysql_free_result($result);
    mysql_close($link);
}
?>