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
$sql = "SELECT * FROM documento d WHERE NOT EXISTS (SELECT NULL FROM proveedores P WHERE d.doc_rutpv=P.prov_rut)";
$data = mysql_query($sql);

$header = "N_Registro.;N_Documento Principal;Tipo Documento Principal;Rut Proveedor;Nombre Proveedor;Fecha Doc. Principal;Fecha creacion Sigbod;Fecha MODIFICACION;Orden de Compra;Monto Doc. Principal;Origen Doc.;N_Origen;Fecha Origen;N_Subdocumento;Tipo SubDocumento;Fecha Subdocumento;Monto Subdocumento;Estado doc. Principal;Observaciones;URL;";
header('Content-Encoding: UTF-8');
header('Content-Type: application/vnd.ms-excel; charset=utf-8');
header("Content-Disposition: attachment; filename=sigbod_documento_no_asoc.csv");
header("Pragma: no-cache");
header("Expires: 0");
echo $header . "\n";

while ($row = mysql_fetch_array($data)) {
    $sum = $sum + $row["doc_montototal"];
    $fmod = $row["doc_fechamod"];
    if ($fmod == "0000-00-00 00:00:00") {
        $fmod = "";
    } else {
        $fmod = date("d/m/Y H:i:s", strtotime($row["doc_fechamod"]));
    }
    $sql1 = "SELECT * FROM subdocumento WHERE subdoc_id='" . $row["doc_id"] . "'";
    $result1 = mysql_query($sql1, $link);
    $cont = mysql_num_rows($result1);
    if ($cont != 0) {
        while ($row1 = mysql_fetch_array($result1)) {
            $fechasub = date("d/m/Y", strtotime($row1["subdoc_fecha"]));
            $montosub = number_format($row1["subdoc_monto"], 0, ',', '.');
            echo $row["doc_id"] . ';' . $row["doc_ndoc"] . ';' . iconv("UTF-8", "WINDOWS-1252", $row["doc_tipodoc"]) . ';' . $row["doc_rutpv"] . ';' . iconv("UTF-8", "WINDOWS-1252", $row["prov_nombre"]) . ';' . date("d/m/Y", strtotime($row["doc_fechadoc"])) . ';' . date("d/m/Y H:i:s", strtotime($row["doc_fechacarga"])) . ';' . $fmod . ';' . $row["doc_noc"] . ';' . number_format($row["doc_montototal"], 0, ',', '.') . ';' . $row["doc_origen"] . ';' . $row["doc_norigen"] . ';' . $row["doc_fechaorigen"] . ';' . $row1["subdoc_numdoc"] . ';' . iconv("UTF-8", "WINDOWS-1252", $row1["subdoc_tipo"]) . ';' . $fechasub . ';' . $montosub . ';' . $row["doc_estado"] . ';' . str_replace("\r\n", "", iconv("UTF-8", "WINDOWS-1252", $row["doc_obs"])) . ';' . $row["doc_url"] . "\n";
        }
    } else {
        $fechasub = "";
        $montosub = "";
        echo $row["doc_id"] . ';' . $row["doc_ndoc"] . ';' . iconv("UTF-8", "WINDOWS-1252", $row["doc_tipodoc"]) . ';' . $row["doc_rutpv"]  . ';' . iconv("UTF-8", "WINDOWS-1252", $row["prov_nombre"]) . ';' . date("d/m/Y", strtotime($row["doc_fechadoc"])) . ';' . date("d/m/Y H:i:s", strtotime($row["doc_fechacarga"])) . ';' . $fmod . ';' . $row["doc_noc"] . ';' . number_format($row["doc_montototal"], 0, ',', '.') . ';' . $row["doc_origen"] . ';' . $row["doc_norigen"] . ';' . $row["doc_fechaorigen"] . ';' . $row1["subdoc_numdoc"] . ';' . iconv("UTF-8", "WINDOWS-1252", $row1["subdoc_tipo"]) . ';' . $fechasub . ';' . $montosub . ';' . $row["doc_estado"] . ';' . str_replace("\r\n", "", iconv("UTF-8", "WINDOWS-1252", $row["doc_obs"])) . ';' . $row["doc_url"] . "\n";
    }
}
echo ";;;;;;;;Total Valorizado:;$" . number_format($sum, 0, ',', '.');
mysql_free_result($data);
mysql_close($link);
?>