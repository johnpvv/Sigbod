<?php
ob_start();
require_once("../dompdf/dompdf_config.inc.php");// Cargamos la librería dompdf que hemos instalado en la carpeta dompdf
session_start();
if (!isset($_SESSION['usuario'])) {
    echo '<script>alert("Error con el inicio de Sesion en el Sistema...");window.close();</script>';
    exit();
}
$user = $_SESSION["usuario"];
include_once("../include/conn.php");
?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <link rel="stylesheet" href="../css/reporte.css" />
        <link rel="shortcut icon" href="../img/favicon.ico" />
        <title>SigBod - Reportes</title>   
    </head>    
    <body style="margin: -0.3in 0in 0in 0in;">
        <?php
        $link = Conectarse(); //Variable de conexion
        $cod = $_GET['id'];
        $fechaini = $_GET['fechaini'];
        $fechafin = $_GET['fechafin'];
        $comilladob = '"';
        $comillasim = "'";
        $rango = "AND mov_fecha BETWEEN '$fechaini' AND '$fechafin'"; //prueba para buscar por rango de fechas        
        $filtrofecha = '</td><td colspan="2" class="td9">INTERVALO DE FECHAS:</td><td colspan="3" class="td12">&nbsp;' . date("d/m/Y", strtotime($fechaini)) . '&nbsp;al&nbsp;' . date("d/m/Y", strtotime($fechafin)) . '</td></tr>';
        if ($fechaini == "" or $fechafin == "") {
            $rango = "";
            $filtrofecha = "</td></tr>";
            $cols = "colspan='7'";
        } else {
            $cols = "colspan='2'";
        }
        $sql = "SELECT * FROM productos, unimed, movimiento, detmovimiento, proveedores,tipomov,estado, stock WHERE (detmov_codigoprd='$cod') AND prd_codigo='$cod' AND stock_codigo='$cod' AND prd_unimed=unimed_id AND prov_rut=mov_rutprv AND mov_tipo=tipomov_id AND mov_estado=estado_id AND detmov_id=mov_id " . $rango." ORDER BY mov_id desc";
        $res = MySQL_query($sql, $link)or die(mysql_error());
        $res1 = MySQL_query($sql, $link)or die(mysql_error());
        $row1 = mysql_fetch_array($res1);
        $contar = mysql_num_rows($res);
        $estado = $row1["mov_estado"];
		$nombre=$row1["prd_glosa"];
        echo '<table class="table3">';
        echo '<tr><td class="td1"><img src="../img/logo1.jpg" alt="logo" width="52" height="60"/></td><td class="td5">Hospital Clinico San Borja Arriarán</td><td class="td2">Fecha Consulta: ' . date("d/m/Y, h:i A") . '</td></tr>';
        echo '</table>';
        echo '<table class="table4">';
        echo '<tr><td colspan="9" class="texto">CARTOLA DE ARTÍCULO</td></tr>';
        echo '<tr><td colspan="2" class="td9">CODIGO ARTICULO:</td><td colspan="7" class="td6">&nbsp;<b>' . $cod . '</b></td></tr>';
        echo '<tr><td colspan="2" class="td9">GLOSA:</td><td colspan="7" class="td6">&nbsp;' . $row1["prd_glosa"] . '</td></tr>';
        echo '<tr><td colspan="2" class="td9">UNIDAD DE MEDIDA:</td><td colspan="2" class="td6">&nbsp;' . $row1["unimed_nombre"] . '</td><td colspan="2" class="td9">STOCK ACTUAL:</td><td colspan="3" class="td6"><span class="td9"> </span><b style="color:#FF0000;">' . $row1["stock_cantidad"] . '</b></td></tr>';
        echo '<tr><td colspan="2" class="td9">N° MOVIMIENTOS:</td><td ' . $cols . ' class="td6">&nbsp;' . $contar;
        echo $filtrofecha;
        echo '<tr><td colspan="9" class="texto5">DETALLE DE MOVIMIENTOS</td></tr>';
        echo '<tr><th width="30">N° Solicitud</th><th width="60">Tipo Movimiento</th><th width="60">Fecha Solicitud</th><th width="80">Orden de Compra</th><th width="60">RUT</th><th>Proveedor</th><th width="20">Cantidad Solicitada</th><th width="40">Valor Neto Unitario</th><th width="40">Estado Solicitud</th></tr>';
        while ($row = mysql_fetch_array($res)) {
            $estado = $row["mov_estado"];
            if ($estado == "0") {
                $estado = " class='td13'";
            } else {
                $estado = " class='td14'";
            }
            printf("<tr><td><b>%s</b></td><td>%s</td><td>%s</td><td><b>%s</b></td><td>%s</td><td class='td7'><b>%s</b></td><td><b>%s</b></td><td><b>%s</b></td><td ".$estado.">%s</td></tr>", $row["mov_id"], $row["tipomov_nombre"], date("d/m/Y H:i:s", strtotime($row["mov_fecha"])), $row["detmov_oc"], number_format($row["prov_rut"], 0, '.', '.') . "-" . $row["prov_dv"] , $row["prov_nombre"], number_format($row["detmov_cantidad"], 0, ',', '.'), '$' . number_format($row["detmov_precio"], 0, ',', '.'), $row["estado_nombre"]);
        }
        echo '</table>';
        mysql_close($link);
        ?>
    </body>    
</html>
<?php
$dompdf = new \Dompdf\Dompdf(); // Instanciamos un objeto de la clase DOMPDF.
$dompdf->set_paper("letter", "portrait"); // Definimos el tamaño y orientación del papel que queremos.
$dompdf->load_html(ob_get_clean()); // Cargamos el contenido HTML.
$dompdf->render(); // Renderizamos el documento PDF.
$canvas = $dompdf->get_canvas();//n de pagina
$canvas->page_text(160, 760, "SigBod - Fecha de Impresion: ".date("d/m/Y H:i:s")."  Página: {PAGE_NUM} de {PAGE_COUNT}", "Bold", 10, array(0,0,0));
$filename = "Cartola_Articulo_".$cod.".pdf";
$dompdf->stream($filename, array("Attachment" => 0)); // Enviamos el fichero PDF al navegador.
?>

