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
    $header = "N_Registro.;N_Documento Principal;Tipo Documento Principal;Rut Proveedor;Nombre Proveedor;Fecha Doc. Principal;Orden de Compra;Monto Doc. Principal;Estado doc. Principal;Observaciones;URL;";
    header('Content-Encoding: UTF-8');
    header('Content-Type: application/vnd.ms-excel; charset=utf-8');
    header("Content-Disposition: attachment; filename=sigbod_Seguimiento.csv");
    header("Pragma: no-cache");
    header("Expires: 0");
    while ($row = mysql_fetch_array($result)) {
        $id = $row["seg_id"];
        $estado = $row["seg_estado"];
        $descrip= str_replace("\n", ";", $row["seg_descrip"]);
        if ($estado == 0) {
            $est = "(Cerrado)";
        } else {
            $est = "";
        }
        echo 'ID Seguimiento: ' . ';' . $row["seg_id"] . "\n";
        echo 'NOMBRE SEGUIMIENTO: ' . ';' . $row["seg_nombre"] . $est . "\n";
        echo 'DESCRIPCION: ' . ';' . $descrip . "\n";
        echo 'FECHA VENCIMIENTO: ' . ';' . date("d/m/Y", strtotime($row["seg_fven"])) . "\n";
        echo "\n";
        echo $header . "\n";
        $sql1 = "SELECT * FROM seguimiento_detalle WHERE seg_id='$id'";
        $res1 = MySQL_query($sql1, $link)or die(mysql_error());
        $cont1 = mysql_num_rows($res1);
        while ($row1 = mysql_fetch_array($res1)) {
            $codigo = $row1["seg_id_doc"];
            $sql2 = "SELECT * FROM documento WHERE doc_id='$codigo'";
            $res2 = MySQL_query($sql2, $link) or die(mysql_error());
            $row2 = MySQL_Fetch_array($res2);
            $rut = $row2["doc_rutpv"];
            $sql3 = "SELECT * FROM proveedores WHERE prov_rut='$rut'";
            $res3 = MySQL_query($sql3, $link) or die(mysql_error());
            $row3 = MySQL_Fetch_array($res3);
            echo $row2["doc_id"] . ';' . $row2["doc_ndoc"] . ';' . iconv("UTF-8", "WINDOWS-1252", $row2["doc_tipodoc"]) . ';' . $row3["prov_rut"] . "-" . $row3["prov_dv"] . ';' . iconv("UTF-8", "WINDOWS-1252", $row3["prov_nombre"]) . ';' . date("d/m/Y", strtotime($row2["doc_fechadoc"])) . ';' . $row2["doc_noc"] . ';' . number_format($row2["doc_montototal"], 0, ',', '.') . ';' . $row2["doc_estado"] . ';' . str_replace("\r\n", "", iconv("UTF-8", "WINDOWS-1252", $row2["doc_obs"])) . ';' . $row2["doc_url"] . "\n";

            $sum = $sum + $row2["doc_montototal"];
        }
    }
    echo ";;;;;;Total Valorizado:;$" . number_format($sum, 0, ',', '.');
    mysql_free_result($result);
    mysql_close($link);
}
?>