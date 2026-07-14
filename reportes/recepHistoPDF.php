<?php
ob_start();
require_once("../dompdf/dompdf_config.inc.php"); // Cargamos la librería dompdf que hemos instalado en la carpeta dompdf
session_start();
if (!isset($_SESSION['usuario'])) {
    echo '<script>alert("Error con el inicio de Sesion en el Sistema...");window.close();</script>';
    exit();
}
include_once("../include/conn.php");
$link = Conectarse(); //Variable de coneccion
?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <link rel="stylesheet" href="../css/reporte.css" />
        <link rel="shortcut icon" href="../img/favicon.ico" />
        <style>
            #footer { position: fixed; left: 0px; bottom: 50px; right: 0px; height: 50px; }
        </style>
        <title>SigBod - Reportes</title>   
    </head>    
    <body style="margin: -0.3in 0in 0.3in 0in;">
        <?php
        $date = date("d/m/Y");
        $anio = $_GET["anio"];
        $ocomp = $_GET["oc"];
        $nmov = $_GET["nmov"];
        $sql = "SELECT `recep_hist_numov`,`recep_hist_ocinter`,`recep_hist_chilecompra`,`bodega_nombre`,`tipodoc_glosa`,`recep_hist_obs`,`recep_hist_fechamov`,
        `recep_hist_numdoc`,`recep_hist_rutpv`,`recep_hist_anooc`,`recep_hist_anomov`,`recep_hist_est_glosa`,`recep_hist_user`,`prov_rut`,`prov_dv`,
        `prov_nombre`,`recep_hist_det_codprd`,`recep_hist_det_glosa`,`recep_hist_det_cant`,`recep_hist_det_um`,`recep_hist_det_precio`
        FROM recep_historico RH INNER JOIN proveedores P ON P.prov_rut = RH.recep_hist_rutpv INNER JOIN bodegas B ON B.bodega_codigo=RH.recep_hist_bod
        INNER JOIN recep_historico_estado RE ON RE.recep_hist_est_cod=RH.recep_hist_estado INNER JOIN recep_historico_tipodoc RT ON RT.tipodoc_cod=RH.recep_hist_tipodoc
        LEFT JOIN recep_historico_detalle RD ON RD.recep_hist_det_numov=RH.recep_hist_numov AND RD.recep_hist_det_ocint=RH.recep_hist_ocinter AND RD.recep_hist_det_anomov=RH.recep_hist_anomov
        WHERE RH.recep_hist_numov = '$nmov' AND RH.recep_hist_ocinter = '$ocomp' AND RH.recep_hist_anomov = '$anio'";
        $result = mysql_query($sql, $link);
        //echo $sql;
        $row = mysql_fetch_array($result);
        $cuenta = mysql_num_rows($result);
        $result1 = mysql_query($sql, $link);
        echo '<table class="table3">';
        echo '<tr><td class="td1"><img src="../img/logo1.jpg" alt="logo" width="52" height="60"/></td><td class="td5">Hospital Clinico San Borja Arriarán</td><td class="td2">Fecha Movimiento: ' . date("d/m/Y, H:i:s", strtotime($row["recep_hist_fechamov"])) . '</td></tr>';
        echo '</table>';
        echo '<table class="table4">';
        echo '<tr><td colspan="4" class="texto">GUIA DE ENTRADA A BODEGA N°: ' . $nmov . ' / ' . $anio . '</td></tr>';
        echo '<tr><td class="td9">Proveedor:</td><td colspan="3" class="td6">&nbsp;' . number_format($row["prov_rut"], 0, '', '.') . "-" . $row["prov_dv"] . "&nbsp;&nbsp;&nbsp;" . $row["prov_nombre"] . "</td></tr>";
        echo '<tr><td class="td9">N° OC Chilecompras:</td><td class="td6">&nbsp;' . $row["recep_hist_chilecompra"] . "</td><td class='td9'>N° Orden Interna: </td><td class='td6'>&nbsp;" . $ocomp . "</td></tr>";
        echo '<tr><td class="td9">N° Documento:</td><td class="td6a">&nbsp;' . $row["recep_hist_numdoc"] . "</td><td class='td9'>Tipo Documento: </td><td class='td6'>&nbsp;" . $row["tipodoc_glosa"] . "</td></tr>";
        echo '<tr><td class="td9">Bodega Receptora:</td><td class="td6">&nbsp;' . $row["bodega_nombre"] . "</td><td class='td9'>Usuario: </td><td class='td6'>&nbsp;" . $row["recep_hist_user"] . "</td></tr>";
        echo '<tr><td class="td9">Estado OC:</td><td colspan="3" class="td6">&nbsp;' . $row["recep_hist_est_glosa"] . "</td></tr>";
        echo '<tr><td class="td9">Observaciones OC:</td><td colspan="3" class="td6">&nbsp;' . $row["recep_hist_obs"] . "</td></tr>";
        echo '<tr><td colspan="4" class="fondo"></td></tr>';
        echo '</table>';
        echo '<table class="table4">';
        echo '<tr>
             <th width="50">C&oacute;digo</th>
             <th width="230">Descripción</th>
             <th width="40">U. M.</th>
             <th width="65">Precio Neto</th>
             <th width="40">Cantidad Recibida</th>
             <th width="70">Total Neto Articulo</th> 
            </tr>';
        if ($row["recep_hist_det_codprd"] == "") {
            echo "<tr><td colspan ='6' class='td10'>No se encontraron Registros que Mostrar.</td></tr></table>";
        } else {
            while ($row1 = mysql_fetch_array($result1)) {
                $totalneto = $row1["recep_hist_det_cant"] * $row1["recep_hist_det_precio"];
                printf("<tr><td class='td10'>%s</td><td class='td7'>%s</td><td><b>%s</b></td><td>%s</td><td><b>%s</b></td><td>%s</td></tr>", $row1["recep_hist_det_codprd"], $row1["recep_hist_det_glosa"], $row1["recep_hist_det_um"], '$ ' . number_format($row1["recep_hist_det_precio"], 2, ',', '.'), number_format($row1["recep_hist_det_cant"], 0, ',', '.'), '$ ' . number_format($totalneto, 0, ',', '.'));
                $suma = $suma + $totalneto;
            }
            echo '</table>';
            echo '<table class="table4a">';
            echo '<tr><td class="td4">Total NETO:</td><td class="td4"><b> $ ' . number_format($suma, 0, ',', '.') . '</b></td></tr>';
            echo '<tr><td class="td4">IVA 19%:</td><td class="td4"><b> $ ' . number_format($suma * 0.19, 0, ',', '.') . '</b></td></tr>';
            echo '<tr><td class="td4"><b>Total Final:</b></td><td class="td4"><span class="texto4"> $ ' . number_format($suma * 1.19, 0, ',', '.') . '</span></td></tr>';
            echo '</table>';
        }
        mysql_close($link);
        echo '<div id="footer">';
        echo "<center>___________________________________</center>";
        echo "<center><span class='td10'>FIRMA RESPONSABLE</span></center><span class='td10a'>&nbsp;</span>";
        echo '</div>';
        ?>
    </body>    
</html>
<?php
$dompdf = new \Dompdf\Dompdf(); // Instanciamos un objeto de la clase DOMPDF.
$dompdf->set_paper("letter", "portrait"); // Definimos el tamaño y orientación del papel que queremos.
$dompdf->load_html(ob_get_clean()); // Cargamos el contenido HTML.
$dompdf->render(); // Renderizamos el documento PDF.
$canvas = $dompdf->get_canvas(); //n de pagina
$canvas->page_text(190, 770, "SigBod - Fecha de Impresion: " . date("d/m/Y H:i:s") . "  Página: {PAGE_NUM} de {PAGE_COUNT}", "Bold", 8, array(0, 0, 0));
$filename = "recep_Hist" . "N°" . $nmov . ".pdf";
$dompdf->stream($filename, array("Attachment" => 0)); // Enviamos el fichero PDF al navegador.
?>
