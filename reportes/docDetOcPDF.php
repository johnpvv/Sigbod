<?php
//reporte deriva de pagina : herramientas/buscarDocDetalleOC.php
ob_start();
require_once("../dompdf/dompdf_config.inc.php"); // Cargamos la librería dompdf que hemos instalado en la carpeta dompdf
ini_set("memory_limit", "512M"); //tamaño de buffer
set_time_limit(300);
session_start();
if (!isset($_SESSION['usuario'])) {
    echo '<script>alert("Error con el inicio de Sesion en el Sistema...");window.close();</script>';
    exit();
}
$query = $_SESSION['query'];
if ($query == "") {
    echo '<script>alert("No Hay Datos para Generar el Informe");				
			window.close();
	</script>';
    exit();
} else {
    $sql = $query;
}
include_once("../include/conn.php");
$link = Conectarse(); //Variable de coneccion
?>
<html>
    <head>
        <meta charset="utf-8">
        <link rel="stylesheet" href="../css/reporte.css" />
        <title>SigBod - Detalle Documentos OC</title>   
    </head>    
    <body style="margin: -0.3in 0in 0in 0in;">
        <?php
        $sql = str_replace("GROUP BY doc_noc ASC", "GROUP BY doc_noc ORDER BY prov_nombre ASC", $sql);
        $data = mysql_query($sql);
        $contar = mysql_num_rows($data);
        if ($contar >= 200) {
            echo '<script>alert("Error, la cantidad de registros para el informe no debe superar los 1000, favor revise los filtros.");				
			window.close();
			</script>';
            exit();
        }
        //echo $sql;
        $sql2 = "SELECT usuario_institucion, uni_nombre FROM usuarios, unid_oper WHERE usuario_rut='" . $_SESSION['usuario'] . "' AND uni_id=usuario_institucion";
        $data2 = mysql_query($sql2);
        $row2 = mysql_fetch_array($data2);
        echo '<table class="table3a">';
        echo '<tr><td class="td1"><img src="../img/logo1.jpg" alt="logo" width="48" height="56"/></td><td class="td5">Hospital Clinico San Borja Arriarán</td><td class="td2">Fecha Reporte: ' . date("d/m/Y, H:i:s") . '</td></tr>';
        echo '</table>';
        echo '<table class="table4">';
        echo '<tr><td colspan="4" class="td16">DETALLE REGISTRO DE DOCUMENTOS POR OC</td></tr>';
        echo '<tr><th width="470">Proveedor</th><th width="95">Orden de Compra</th><th width="60">Cantidad Doc. Ingresados</th><th width="80">Total Valorizado</th></tr>';

        while ($row = mysql_fetch_array($data)) {
            printf("<tr><td  align='left'><b>%s</b></td><td><b>%s</b></td><td>%s</td><td>$%s</td></tr>", $row["prov_rut"] . "-" . $row["prov_dv"] . "</b>&nbsp;&nbsp;&nbsp;" . $row["prov_nombre"], $row["doc_noc"], $row["total"], number_format($row["suma"], 0, ',', '.'));
        }
        echo '</table>';
        echo"<center>_____________________________________________________________________________________________________________</center>";
        echo '<table class="table2b">';
        echo '<tr><td class="td10b"><b>Resumen:</b> Total Registros Seleccionados=&nbsp;&nbsp;' . $contar . '</b></td></tr>';
        echo '<tr><td class="td10a">Unidad de Ingreso: ' . $row2["uni_nombre"] . '</td></tr>';
        echo '</table>';
        mysql_close($link);
        ?>

    </body>    
</html>
<?php
$dompdf = new \Dompdf\Dompdf(); // Instanciamos un objeto de la clase DOMPDF.
$dompdf->set_paper("letter", "landscape"); // Definimos el tamaño y orientación del papel que queremos.
$dompdf->load_html(ob_get_clean()); // Cargamos el contenido HTML.
$dompdf->render(); // Renderizamos el documento PDF.
$canvas = $dompdf->get_canvas(); //n de pagina
$canvas->page_text(280, 580, "SigBod - Fecha de Impresion: " . date("d/m/Y H:i:s") . "  Página: {PAGE_NUM} de {PAGE_COUNT}", "Bold", 8, array(0, 0, 0));
$filename = "Detalle_Registro_Doc_" . date("dmY_His") . ".pdf";
$dompdf->stream($filename, array("Attachment" => 0)); // Enviamos el fichero PDF al navegador.
?>