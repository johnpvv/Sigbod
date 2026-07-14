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
    $result = mysql_query($query, $link) or die(mysql_error());
    $header="Numero Mov.;RUT;Nombre Proveedor;Contacto;Fecha;Numero Articulos;Precio Neto;Estado Sol.;Observaciones" ;
    header('Content-Encoding: UTF-8');
    header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
    header("Content-Disposition: attachment; filename=sigbod_proveedor_detalle.csv");
    header("Pragma: no-cache");
    header("Expires: 0");    
    echo $header. "\n";
    while ($row = mysql_fetch_array($result)) {        
        $obs=str_replace("\r\n"," ",$row["mov_observacion"]); //reem´plazar saltos de linea por espacios       
        $estadosol=$row["mov_estado"];
            if($estadosol=="0"){
                $estadosol="No Vigente";
            }else{
                $estadosol="Vigente";
            }
        echo $row["mov_id"].';'.number_format($row["prov_rut"],0,'.','.')."-".$row["prov_dv"].';'.iconv("UTF-8", "WINDOWS-1252", $row["prov_nombre"]).';'.iconv("UTF-8", "WINDOWS-1252", $row["prov_nomcontacto"]) .';'.date("d/m/Y H:i:s", strtotime($row["mov_fecha"])).';'.$row["mov_cuenta"].';'. '$' . number_format($row["mov_valorneto"], 2, ',', '.').';'.$estadosol.';'. iconv("UTF-8", "WINDOWS-1252", $obs)."\n";
    }
    mysql_free_result($result);
    mysql_close($link);
}
?>