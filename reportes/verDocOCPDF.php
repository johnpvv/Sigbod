<?php
ob_start();
require_once("../dompdf/dompdf_config.inc.php"); // Cargamos la librería dompdf que hemos instalado en la carpeta dompdf
session_start();
if (!isset($_SESSION['usuario'])) {
    echo '<script>alert("Error con el inicio de Sesion en el Sistema...");window.close();</script>';
    exit();
}
include("../include/conn.php");
$link = Conectarse(); //Variable de coneccion
$id = $_GET['id'];
$sql1 = "SELECT * FROM documento, proveedores WHERE doc_noc='$id' AND prov_rut=doc_rutpv ORDER BY doc_noc DESC";
$data = mysql_query($sql1);
$contar = mysql_num_rows($data);
$comilladob = '"';
$comillasim = "'";
?>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <link rel="stylesheet" href="../css/reporte.css" />
        <title>SigBod - Detalle Documentos OC</title>
    </head>
    <body  style="margin: -0.3in 0in 0in 0in;">
        <?php
        echo '<table class="table3a">';
        echo '<tr><td class="td1"><img src="../img/logo1.jpg" alt="logo" width="48" height="56"/></td><td class="td5">Hospital Clinico San Borja Arriarán</td><td class="td2">Fecha Reporte: ' . date("d/m/Y, H:i:s") . '</td></tr>';
        echo '</table>';
        echo '<table class="table4">';
        echo '<tr><td colspan="12" class="td16">DETALLE REGISTRO DE DOCUMENTOS POR ORDEN DE COMPRA</td></tr>';
        echo '<tr><td colspan="3" class="td9">Orden de Compra:</td><td colspan="9" class="td6a">&nbsp;' . $id . '</td></tr>';
        echo '<tr><td colspan="3" class="td9">Reg. Encontrados:</td><td colspan="9" class="td7">&nbsp;' . $contar . '</td></tr>';
        echo '<tr><th>N° Reg.</th><th>Num. Doc.</th><th width="40">Tipo Doc.</th><th width="140">Proveedor</th><th>Fecha Doc.</th><th width="50">Fecha creación</th><th width="50">Fecha Modif.</th><th width="50">Monto Total</th><th width="20">Cant. Subdoc.</th><th width="50">Origen Doc.</th><th width="120">Observaciones</th><th>Estado</th></tr>';
        while ($row = mysql_fetch_array($data)) {
            if ($row["doc_fechadoc"] != "0000-00-00") {
                $fechadoc = date("d/m/Y", strtotime($row["doc_fechadoc"]));
            } else {
                $fechadoc = "";
            }
            if ($row["doc_fechamod"] != "0000-00-00 00:00:00") {
                $fechamod = date("d/m/Y", strtotime($row["doc_fechamod"]));
            } else {
                $fechamod = "";
            }
            if ($row["doc_origen"] != "") {
                $origen = $row["doc_origen"];
            } else {
                $origen = "";
            }if ($row["doc_norigen"] != 0) {
                $origen = $origen . " N°" . $row["doc_norigen"];
            } else {
                $origen = $origen . "";
            } if ($row["doc_fechaorigen"] != "0000-00-00") {
                $origen = $origen . ", Fecha: " . date("d/m/Y", strtotime($row["doc_fechaorigen"]));
            } else {
                $origen = $origen . "";
            }
            $suma = $suma + $row["doc_montototal"];
            $contsubdoc = $contsubdoc + $row["doc_subdoc_cuenta"];
            $sql2 = "SELECT subdoc_tipo, subdoc_monto, subdoc_id FROM subdocumento WHERE subdoc_id='" . $row['doc_id'] . "'";
            $data2 = mysql_query($sql2);
            while ($row2 = mysql_fetch_array($data2)) {
                if ($row2["subdoc_tipo"] == "Recep. de Bodega") {
                    $rec = $rec + $row2["subdoc_monto"];
                }
                if ($row2["subdoc_tipo"] == "Nota de Credito") {
                    $dev = $dev + $row2["subdoc_monto"];
                }
            }
            printf("<tr><td><i>%s</i></td><td><b>%s</b></td><td>%s</td><td align='left'><b>%s</b></td><td>%s</td><td><b>%s</b></td><td>%s</td><td><b>$</b>%s</td><td>%s</td><td style='font-size:9px;'>%s</td><td>%s</td><td><b>%s</b></td></tr>", $row["doc_id"], $row["doc_ndoc"], $row["doc_tipodoc"], number_format($row["prov_rut"], 0, '.', '.') . "-" . $row["prov_dv"] . " " . $row["prov_nombre"], $fechadoc, date("d/m/Y, H:i", strtotime($row["doc_fechacarga"])), $fechamod, number_format($row["doc_montototal"], 0, ',', '.'), $row["doc_subdoc_cuenta"], $origen, $row["doc_obs"], $row["doc_estado"]);
        }
        echo '</table>';
        mysql_close($link);
        echo "<table class='table2'><tr><td colspan='8' class='td10b'>Total Valorizado Documentos: $<b>" . number_format($suma, 0, ',', '.') . "</b>&nbsp;&nbsp;&nbsp;Cantidad Subdocumentos:&nbsp;&nbsp;<b>" . number_format(($contsubdoc), 0, ',', '.') . "&nbsp;&nbsp;</b><br>Total Recepcionado:&nbsp;&nbsp;&nbsp;<b>$" . number_format(($rec), 0, ',', '.') . "</b>&nbsp;&nbsp;&nbsp;&nbsp;Total Anulado:&nbsp;&nbsp;&nbsp;<b>$" . number_format(($dev), 0, ',', '.') . "</b></td></tr></table>";
        ?>

    </body>    
</html>
<?php
$dompdf = new \Dompdf\Dompdf(); // Instanciamos un objeto de la clase DOMPDF.
$dompdf->set_paper("letter", "landscape"); // Definimos el tamaño y orientación del papel que queremos.
$dompdf->load_html(ob_get_clean()); // Cargamos el contenido HTML.
$dompdf->render(); // Renderizamos el documento PDF.
$canvas = $dompdf->get_canvas(); //n de pagina
$canvas->page_text(260, 590, "SigBod - Fecha de Impresion: " . date("d/m/Y H:i:s") . "  Página: {PAGE_NUM} de {PAGE_COUNT}", "Bold", 8, array(0, 0, 0));
$filename = "DocOC_" . date("d/m/Y_H:i:s") . ".pdf";
$dompdf->stream($filename, array("Attachment" => 0)); // Enviamos el fichero PDF al navegador.
?>