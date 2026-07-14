<?php
ob_start();
require_once("../dompdf/dompdf_config.inc.php"); // Cargamos la librería dompdf que hemos instalado en la carpeta dompdf
ini_set("memory_limit", "1024M"); //tamaño de buffer
set_time_limit(1000);
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
        <link rel="shortcut icon" href="../img/favicon.ico" />
        <title>SigBod - Detalle Documentos</title>   
    </head>    
    <body  style="margin: -0.2in -0.3in -0.2in -0.3in;"><!-- x : top x: rigth, x: bottom, x: left -->
        <?php
        $sql = str_replace("doc_id DESC", "prov_nombre, doc_ndoc ASC", $sql);
        $data = mysql_query($sql);
        $contar = mysql_num_rows($data);
        if ($contar >= 300) {
            echo '<script>alert("Error, la cantidad de registros para el informe no debe superar los 300, favor revise los filtros.");				
			window.close();
			</script>';
            exit();
        }
        $sql2 = "SELECT usuario_institucion, uni_nombre FROM usuarios, unid_oper WHERE usuario_rut='" . $_SESSION['usuario'] . "' AND uni_id=usuario_institucion";
        $data2 = mysql_query($sql2);
        $row2 = mysql_fetch_array($data2);
        $cont = 0;
        $cont1 = 0;
        $cont2 = 0;
        $cont3 = 0;
        $cont0 = 0;
        echo '<table class="table3a">';
        echo '<tr><td class="td1"><img src="../img/logo1.jpg" alt="logo" width="46" height="54"/></td><td class="td5">Hospital Clinico San Borja Arriarán</td><td class="td2">Fecha Reporte: ' . date("d/m/Y, H:i:s") . '</td></tr>';
        echo '</table>';
        echo '<table class="table4">';
        echo '<tr><td colspan="10" class="td16">DETALLE REGISTRO DE DOCUMENTOS</td></tr>';
        echo '<tr><th>N° Reg.</th><th>N° Documento</th><th>Tipo Documento</th><th width="150">Proveedor</th><th>Fecha Doc.</th><th>Fecha creación</th><th width="60">Orden de Compra</th><th width="60">Monto Total</th><th width="70">Origen Doc.</th><th width="140">Observaciones</th></tr>';

        while ($row = mysql_fetch_array($data)) {
            $id = $row["doc_id"];
            $estado = $row["doc_estado"];
            if ($row["doc_fechadoc"] != "0000-00-00") {
                $fechadoc = date("d/m/Y", strtotime($row["doc_fechadoc"]));
            } else {
                $fechadoc = "";
            }
            if ($row["doc_origen"] != "") {
                $orig = $row["doc_origen"];
            } else {
                $orig = $orig . "";
            }
            if ($row["doc_norigen"] != 0) {
                $orig = $orig . " N°" . $row["doc_norigen"];
            } else {
                $orig = $orig . "";
            }
            if ($row["doc_fechaorigen"] != "0000-00-00") {
                $orig = $orig . ",&nbsp;&nbsp;Fecha:&nbsp;" . date("d/m/Y", strtotime($row["doc_fechaorigen"]));
            } else {
                $orig = $orig . "";
            }
            if ($estado == "") {
                $clas = ' style = "background-color:#ffd6d6;"';
            } else {
                $clas = "";
            }
            printf('<tr' . $clas . '><td style="background-color:#cccccc;font-size:10px;">%s</td><td style="font-size:14px;"><b>%s</b></td><td>%s</td><td class="td7">%s</td><td><b>%s</b></td><td>%s</td><td>%s</td><td><b>$</b>%s</td><td style="font-size:10px;">%s</td><td>%s</td></tr>', $id, $row["doc_ndoc"], $row["doc_tipodoc"], "<b>" . $row["prov_rut"] . "-" . $row["prov_dv"] . "</b>&nbsp;&nbsp;&nbsp;" . $row["prov_nombre"], $fechadoc, date("d/m/Y, H:i:s", strtotime($row["doc_fechacarga"])), $row["doc_noc"], number_format($row["doc_montototal"], 0, ',', '.'), $orig, $row["doc_obs"]);
            if ($estado == "Archivado") {
                echo '<tr><td style="background-color:#cccccc;text-align:right;font-size:15px;"><img src="../img/arrowr.png" alt="arrow" width="15" height="11"/></td><td colspan="9" class="td7">Estado Actual: <b style="color:#FF33E6;">' . strtoupper($estado) . '</b></td></tr>';
                $cont0 = $cont0 + 1;
            } else {
                if ($estado == "Recepcionado") {
                    $sql1 = "SELECT subdoc_tipo, subdoc_fecha, subdoc_numdoc FROM subdocumento WHERE subdoc_id='$id' AND subdoc_tipo='Recep. de Bodega'";
                    $clas1 = ' style = "color:blue;"';
                    $cont = $cont + 1;
                }
                if ($estado == "Anulado") {
                    $sql1 = "SELECT subdoc_tipo, subdoc_fecha, subdoc_numdoc FROM subdocumento WHERE subdoc_id='$id' AND subdoc_tipo='Nota de Credito'";
                    $clas1 = ' style = "color:red;"';
                    $cont1 = $cont1 + 1;
                }
                if ($estado == "Devuelto") {
                    $sql1 = "SELECT subdoc_tipo, subdoc_fecha, subdoc_numdoc FROM subdocumento WHERE subdoc_id='$id' AND subdoc_tipo='Devolucion'";
                    $clas1 = ' style = "color:green;"';
                    $cont2 = $cont2 + 1;
                }
                if ($estado != "") {
                    $data1 = mysql_query($sql1);
                    $row1 = mysql_fetch_array($data1);
                }
                if ($estado == "") {
                    echo '<tr' . $clas . '><td style="background-color:#cccccc;text-align:right;font-size:15px;"><img src="../img/arrowrd.png" alt="arrow" width="22" height="11"/></td><td colspan="9" class="td7">Estado Actual: <b>PENDIENTE</b>';
                    $cont3 = $cont3 + 1;
                } else {
                    echo '<tr><td style="background-color:#cccccc;text-align:right;font-size:15px;"><img src="../img/arrowbd.png" alt="arrow" width="22" height="12"/></td><td colspan="9" class="td7">Estado Actual: <b' . $clas1 . '>' . strtoupper($estado) . '</b>, Con Documento: <b>' . $row1["subdoc_tipo"] . '</b>, Numero: <b>' . $row1["subdoc_numdoc"] . '</b>, Fecha: ' . date("d/m/Y", strtotime($row1["subdoc_fecha"])) . ' </td></tr>';
                }
            }
            $sum = $sum + $row["doc_montototal"];
        }
        echo '</table>';
        echo"<center>__________________________________________________________________________________________________________________</center>";
        echo '<table class="table2b">';
        echo '<tr><td class="td10b"><b>Resumen:</b> (Recepcionados= ' . $cont . ', Anulados= ' . $cont1 . ', Devueltos= ' . $cont2 . ', Pendientes= ' . $cont3 . ', Archivados= ' . $cont0 . ')<b> &nbsp;&nbsp;&nbsp; Total Registros Seleccionados=&nbsp;&nbsp;' . $contar . '</b></td></tr>';
        echo '<tr><td class="td10a">Total Valorizado Documentos Seleccionados: $' . number_format($sum, 0, ',', '.') . ', &nbsp;&nbsp;Unidad de Ingreso: ' . $row2["uni_nombre"] . '</td></tr>';
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
$canvas->page_text(285, 588, "SigBod - Fecha de Impresion: " . date("d/m/Y H:i:s") . "  Página: {PAGE_NUM} de {PAGE_COUNT}", "Bold", 7, array(0, 0, 0));
$filename = "Detalle_Registro_Doc_(" . date("d-m-Y_H.i") . ").pdf";
$dompdf->stream($filename, array("Attachment" => 0)); // Enviamos el fichero PDF al navegador.
?>