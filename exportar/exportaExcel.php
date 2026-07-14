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
    $header = "Familia;Codigo Producto;Nombre Producto;Unidad Medida;Referencia Proveedor;ID Conv. Marco;Codigo CENABAST;Precio Unitario;Estado";
    header('Content-Encoding: UTF-8');
    header('Content-Type: application/vnd.ms-excel; charset=utf-8');
    header("Content-Disposition: attachment; filename=sigbod.csv");
    header("Pragma: no-cache");
    header("Expires: 0");
    echo $header . "\n";
    while ($row = mysql_fetch_array($result)) {
        $estado=$row["prd_estado"];
            if($estado=="0"){
                $estado="No Vigente";
            }else{
                $estado="Vigente";
            }
        echo $row["prd_fam"] . ';' . $row["prd_codigo"] . ';' . $row["prd_glosa"] . ';' . $row["prd_unimed"] .';'. $row["prd_ref"]. ';'. $row["prd_codcm"].';'. $row["prd_cenabast"]. ';' . "$ " . number_format($row["prd_precio"],2,',','.').';'. $estado. "\n";
    }
    mysql_free_result($result);
    mysql_close($link);
}
?>