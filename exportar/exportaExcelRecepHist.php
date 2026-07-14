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
    $header = "OC Mercadopublico;OC Interna;Num. MOV;Estado;Bodega;Rut Proveedor;Nombre Prov;Fecha MOV;N_Documento;Tipo Documento;Observaciones OC;";
    header('Content-Encoding: UTF-8');
    header('Content-Type: application/vnd.ms-excel; charset=utf-8');
    header("Content-Disposition: attachment; filename=sigbod_recep_historico.csv");
    header("Pragma: no-cache");
    header("Expires: 0");
    echo $header . "\n";
    while ($row = mysql_fetch_array($result)) {
        $obs=str_replace("\r\n"," ",trim($row["recep_hist_obs"])); //reem´plazar saltos de linea por espacios        
        $obs=str_replace(";"," ",$obs); //reem´plazar puntos y coma por guion bajo
        $obs=str_replace(".\n"," ",$obs);    
        $obs=str_replace(":\n"," ",$obs); 
        $obs=str_replace(",\n"," ",$obs);
        $obs=str_replace(")\n"," ",$obs);
        echo $row["recep_hist_chilecompra"] . ';' . $row["recep_hist_ocinter"] . ';' . $row["recep_hist_numov"] . ';' . $row["recep_hist_est_glosa"] . ';' . $row["bodega_nombre"] . ';' . number_format($row["prov_rut"], 0, '.', '.') . "-" . $row["prov_dv"] . ';' . $row["prov_nombre"] . ';' . date("d/m/Y H:i:s", strtotime($row["recep_hist_fechamov"])) . ';' . $row["recep_hist_numdoc"] . ';' . $row["tipodoc_glosa"] .';' . iconv("UTF-8", "WINDOWS-1252", $obs). "\n";
    }
    mysql_free_result($result);
    mysql_close($link);
}
?>