<?php
ob_start();
ini_set("memory_limit", "256M"); //tamaño de buffer
set_time_limit(600);
require_once("../dompdf/dompdf_config.inc.php"); // Cargamos la librería dompdf que hemos instalado en la carpeta dompdf
session_start();
if (!isset($_SESSION['usuario'])) {
    echo '<script>alert("Error con el inicio de Sesion en el Sistema...");window.close();</script>';
    exit();
}
$user = $_SESSION["usuario"];
$query = $_SESSION['query'];
$idseg = $_GET["id"];

if ($query == "" && $idseg == "") {
    echo '<script>alert("No Hay Datos para Exportar");				
			window.close();</script>';
    exit();
} else {
    $sql = $query;
}
if ($idseg <> "") {
	$sql = "SELECT * FROM seguimiento, usuarios WHERE seg_resp=usuario_id AND seg_id='$idseg'";
}
?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <link rel="stylesheet" href="../css/reporte.css" />
        <link rel="shortcut icon" href="../img/favicon.ico" />
        <title>SigBod - Reporte Seguimiento de Documentos</title>   
    </head>    
    <body style="margin: -0.3in 0in 0in 0in;">
        <?php
        include("../include/conn.php");
        $link = Conectarse();
        echo '<table class="table3">';
        echo '<tr><td class="td1"><img src="../img/logo1.jpg" alt="logo" width="48" height="56"/></td><td class="td5">Hospital Clinico San Borja Arriarán</td><td class="td2">Fecha Consulta: ' . date("d/m/Y, h:i A") . '</td></tr>';
        echo '</table>';
        echo '<table class="table4">';
        echo '<tr><td class="texto">REPORTE SEGUIMIENTO DE DOCUMENTOS</td></tr>';
        echo '</table>';
        $result = mysql_query($sql, $link);
        if ($contar >= 50) {
            echo '<script>alert("Error, la cantidad de registros para el informe no debe superar los 50, favor revise los filtros.");window.close();</script>';
            exit();
        }
        while ($row = mysql_fetch_array($result)) {
            $id = $row["seg_id"];
            $estado = $row["seg_estado"];
            if ($estado == 0) {
                $est = " <b style='color:red;'>(Cerrado)</b>";
            } else {
                $est = "";
            }
            $descrip= str_replace("\n", "<br>*", $row["seg_descrip"]);
            echo '<table class="table4">';
            echo '<tr><td colspan="2" class="td16a" style="color:Red;">ID Seguimiento: ' . $row["seg_id"] . '</td></tr>';
            echo '<tr><td class="td9a">NOMBRE SEGUIMIENTO:</td>';
            echo '<td class="td6c"><b>' . $row["seg_nombre"] . '</b>' . $est . '</td></tr>';
            echo '<tr><td class="td9a">DESCRIPCION:</td>';
            echo '<td class="td6c" style="font-size:11px;">&nbsp;' . $descrip . '</td></tr>';
            echo '<tr><td class="td9a">NOMBRE RESPONSABLE:</td>';
            echo '<td class="td6c">&nbsp;<b>' . $row["usuario_nombre"] . ' ' . $row["usuario_apellidos"] . '</b></td></tr>';
            echo '<tr><td class="td9a">FECHA VENCIMIENTO:</td>';
            echo '<td class="td6c">&nbsp;<b>' . date("d/m/Y", strtotime($row["seg_fven"])) . '</b></td></tr>';
            echo '</table>';
            echo '<table class="table5">';
            echo '<tr>
             <td style="width:30px;"><b>ID Doc.</b></td>
             <td class="td17a"><b>Tipo Documento</b></td>
             <td class="td17a"><b>N° Documento</b></td>
             <td class="td17a"><b>N° OC</b></td>
             <td class="td17a"><b>RUT Prov.</b></td>
             <td class="td18a"><b>Nombre Prov.</b></td>
             <td class="td17a"><b>Monto</b></td>
             <td class="td17a"><b>Estado</b></td>
            </tr>';
            $sql1 = "SELECT * FROM seguimiento_detalle WHERE seg_id='$id'";
            $res1 = MySQL_query($sql1, $link)or die(mysql_error());
            $cont1= mysql_num_rows($res1);
            while ($row1 = mysql_fetch_array($res1)) {
                $codigo = $row1["seg_id_doc"];
                $sql2 = "SELECT * FROM documento WHERE doc_id='$codigo'";
                $res2 = MySQL_query($sql2, $link) or die(mysql_error());
                $row2 = MySQL_Fetch_array($res2);
                $rut = $row2["doc_rutpv"];
                $sql3 = "SELECT * FROM proveedores WHERE prov_rut='$rut'";
                $res3 = MySQL_query($sql3, $link) or die(mysql_error());
                $row3 = MySQL_Fetch_array($res3);
				
                printf("<tr><td>%s</td><td>%s</td><td class='td13'>%s</td><td><b>%s</b></td><td>%s</td><td>%s</td><td>%s</td><td><b>%s</b></td></tr>", $codigo, $row2["doc_tipodoc"], $row2["doc_ndoc"], $row2["doc_noc"], number_format($rut, 0, ',', '.') . "-" . $row3["prov_dv"], $row3["prov_nombre"], "$ " . number_format($row2["doc_montototal"], 0, ',', '.'), $row2["doc_estado"]);
            }
            echo"<tr><td colspan='8'>Total Registros Seguimiento: <b>".$cont1."</b></td></tr>";
            echo '</table>';
            echo "<br>";
        }
        mysql_close($link);
        ?>
    </body>    
</html>
<?php
$dompdf = new \Dompdf\Dompdf(); // Instanciamos un objeto de la clase DOMPDF.
$dompdf->set_paper("letter", "portrait"); // Definimos el tamaño y orientación del papel que queremos.
$dompdf->load_html(ob_get_clean()); // Cargamos el contenido HTML.
$dompdf->render(); // Renderizamos el documento PDF.
$canvas = $dompdf->get_canvas(); //n de pagina
$canvas->page_text(285, 770, "Página: {PAGE_NUM} de {PAGE_COUNT}", "Bold", 8, array(0, 0, 0));
$filename = "Sigbod_seguimiento.pdf";
$dompdf->stream($filename, array("Attachment" => 0)); // Enviamos el fichero PDF al navegador.
?>