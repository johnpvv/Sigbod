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
    $header = "Familia;Codigo Producto;Nombre Producto;Unidad Medida;Referencia Proveedor;ID Conv. Marco;Codigo CENABAST;Precio Unitario;Destacado;Fecha Modificacion;Estado";
    header('Content-Encoding: UTF-8');
    header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
    header("Content-Disposition: attachment; filename=sigbod_productos.csv");
    header("Pragma: no-cache");
    header("Expires: 0");
    echo $header . "\n";
    while ($row = mysql_fetch_array($result)) {
        $tipo=$row["prd_destacado"];
            if($tipo=="1"){
                $tipo="Si";
            }else{
                $tipo="No";
            }
        echo $row["prd_fam"] . ';' . $row["prd_codigo"] . ';' . iconv("UTF-8", "WINDOWS-1252", $row["prd_glosa"]) . ';' . $row["unimed_nombre"] .';'. iconv("UTF-8", "WINDOWS-1252", $row["prd_ref"]). ';'. $row["prd_codcm"].';'. $row["prd_cenabast"]. ';' . "$ " . number_format($row["prd_precio"],2,',','.').';'.$tipo.';'.$row["prd_fecha"].';'. $row["estado_nombre"]. "\n";
    }//iconv("UTF-8", "WINDOWS-1252", $variable o $string) para convertir caracteres de utf-8 a ANSI
    mysql_free_result($result);
    mysql_close($link);
}
?>