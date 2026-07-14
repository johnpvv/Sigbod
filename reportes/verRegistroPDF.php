<?php
ob_start();
require_once("../dompdf/dompdf_config.inc.php"); // Cargamos la librería dompdf que hemos instalado en la carpeta dompdf
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: ../index.php');
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
        <title>SigBod - Reporte Documentos</title>   
    </head>    
    <body style="margin: -0.3in 0in 0in 0in;">
        <?php
        $date = date("d/m/Y");
        $id = $_GET["id"];
        $sql = "SELECT * FROM proveedores, documento WHERE doc_rutpv=prov_rut AND doc_id='$id'";
        $data = mysql_query($sql);
        $row = mysql_fetch_array($data);
        $sql1 = "SELECT * FROM documento, subdocumento WHERE doc_id=subdoc_id AND subdoc_id='$id' ORDER BY subdoc_numdoc DESC";
        $data1 = mysql_query($sql1);
        $contar = mysql_num_rows($data1);
        $sql2 = "SELECT * FROM usuarios WHERE usuario_rut='" . $row["doc_usuario"] . "'";
        $data2 = mysql_query($sql2);
        $row2 = mysql_fetch_array($data2);
        $sql3 = "SELECT * FROM usuarios WHERE usuario_rut='" . $row["doc_umodif"] . "'";
        $data3 = mysql_query($sql3);
        $row3 = mysql_fetch_array($data3);
		$sql4 = "SELECT usuario_institucion, uni_nombre FROM usuarios, unid_oper WHERE usuario_rut='" . $row["doc_usuario"] . "' AND uni_id=usuario_institucion";
        $data4 = mysql_query($sql4);
        $row4 = mysql_fetch_array($data4);
        echo '<table class="table3">';
        echo '<tr><td class="td1"><img src="../img/logo1.jpg" alt="logo" width="52" height="60"/></td><td class="td5">Hospital Clinico San Borja Arriarán</td><td class="td2">Fecha de Creación: ' . date("d/m/Y, H:i:s", strtotime($row["doc_fechacarga"])) . '</td></tr>';
        echo '</table>';
        echo '<table class="table4">';
        echo '<tr><td colspan="5" class="texto">REGISTRO DE DOCUMENTOS N° ' . $id . '</td></tr>';
        echo '<tr><td colspan="2" class="td4">RUT Proveedor:</td><td colspan="3" class="td6">&nbsp;' . number_format($row["prov_rut"], 0, '', '.') . "-" . $row["prov_dv"] . '</td></tr>';
        echo '<tr><td colspan="2" class="td4">Nombre Proveedor:</td><td colspan="3" class="td6">&nbsp;' . $row["prov_nombre"] . '</td></tr>';
        echo '<tr><td colspan="2" class="td9">N° Documento Principal:</td><td colspan="3" class="td6a">&nbsp;' . $row["doc_ndoc"] . '</td></tr>';
        echo '<tr><td colspan="2" class="td4">Tipo Documento Principal:</td><td colspan="3" class="td6">&nbsp;' . $row["doc_tipodoc"] . '</td></tr>';
        echo '<tr><td colspan="2" class="td4">Fecha Documento Principal:</td><td colspan="3" class="td6">&nbsp;';
        if ($row["doc_fechadoc"] != "0000-00-00") {
            echo date("d/m/Y", strtotime($row["doc_fechadoc"]));
        } else {
            echo "";
        } echo '</td></tr>';
        echo '<tr><td colspan="2" class="td4">Orden de Compra:</td><td colspan="3" class="td6">&nbsp;' . $row["doc_noc"] . '</td></tr>';
        echo '<tr><td colspan="2" class="td4">Origen:</td><td colspan="3" class="td6">&nbsp;';
        if ($row["doc_origen"] != "") {
            echo $row["doc_origen"];
        } else {
            echo "";
        }
        if ($row["doc_norigen"] != 0) {
            echo " N°" . $row["doc_norigen"];
        } else {
            echo "";
        }
        if ($row["doc_fechaorigen"] != "0000-00-00") {
            echo ",&nbsp;&nbsp;Fecha:&nbsp;" . date("d/m/Y", strtotime($row["doc_fechaorigen"]));
        } else {
            echo "";
        } echo '</td></tr>';
        echo '<tr><td colspan="2" class="td4">Monto Total Doc. Principal:</td><td colspan="3" class="td6">&nbsp;$&nbsp;' . number_format($row["doc_montototal"], 0, ',', '.') . '</td></tr>';
        echo '<tr><td colspan="2" class="td4">Creado Por:</td><td colspan="3" class="td6">&nbsp;' . $row2["usuario_nombre"] . "&nbsp;" . $row2["usuario_apellidos"] . ",&nbsp;&nbsp;" . date("d/m/Y, H:i:s", strtotime($row["doc_fechacarga"])) . '</td></tr>';
        echo '<tr><td colspan="2" class="td4">Modificado Por:</td><td colspan="3" class="td6">&nbsp;' . $row3["usuario_nombre"] . "&nbsp;" . $row3["usuario_apellidos"];
        if ($row["doc_fechamod"] != "0000-00-00 00:00:00") {
            echo ",&nbsp;&nbsp;" . date("d/m/Y, H:i:s", strtotime($row["doc_fechamod"]));
        } else {
            echo "";
        } echo '</td></tr>';
        echo '<tr><td colspan="2" class="td4">Unidad de Ingreso:</td><td colspan="3" class="td6">&nbsp;' . $row4["uni_nombre"] . '</td></tr>';
        echo '<tr><td colspan="2" class="td4">Estado:</td><td colspan="3" class="td6b">&nbsp;' . $row["doc_estado"] . '</td></tr>';
        echo '<tr><td colspan="2" class="td9">Observaciones:</td><td colspan="3" class="td6">&nbsp;' . $row["doc_obs"] . '</td></tr>';
        echo '<tr><td colspan="5" class="fondo"></td></tr>';
        if ($contar == 0) {
            echo '<tr><td colspan="5" class="td10">No hay Subdocumentos Registrados.</td></tr>';
            echo '</table>';
        } else {
            echo '<tr>
            <th width="20">Item Subdoc.</th>
             <th width="80">N° Subdocumento</th>
             <th width="120">Tipo Subdocumento</th>
             <th width="80">Fecha Subdocumento</th>
             <th width="80">Monto Total Subdocumento</th> 
            </tr>';
            $cont = 1;
            while ($row = mysql_fetch_array($data1)) {
                if ($row["subdoc_tipo"] == "Recep. de Bodega") {
                    $link1 = '<a href=http://10.9.83.120/recepcion/informes/verMinRecepcionExp.aspx?id_rec=' . $row["subdoc_numdoc"] . ' target="_blank">' . $row["subdoc_numdoc"] . '</a>';
                } else {
                    $link1 = $row["subdoc_numdoc"];
                }
                if ($row1["doc_fechadoc"] != "0000-00-00") {
                    $fechasubdoc = date("d/m/Y", strtotime($row["subdoc_fecha"]));
                } else {
                    $fechasubdoc = "";
                }
                printf("<tr><td>%s</td><td><b>%s</b></td><td><b>%s</b></td><td>%s</td><td>%s</td></tr>", $cont, $link1, $row["subdoc_tipo"], $fechasubdoc, "$ " . number_format($row["subdoc_monto"], 0, ',', '.'));
                $cont++;
            }
            echo '</table>';
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
$canvas->page_text(170, 760, "SigBod - Fecha de Impresion: " . date("d/m/Y H:i:s") . "  Página: {PAGE_NUM} de {PAGE_COUNT}", "Bold", 8, array(0, 0, 0));
$filename = "Registro_Doc_" . "N°" . $id . ".pdf";
$dompdf->stream($filename, array("Attachment" => 0)); // Enviamos el fichero PDF al navegador.
?>