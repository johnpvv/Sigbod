<?php
ob_start();
require_once("../dompdf/dompdf_config.inc.php"); // Cargamos la librería dompdf que hemos instalado en la carpeta dompdf
session_start();
if (!isset($_SESSION['usuario'])) {
    echo '<script>alert("Error con el inicio de Sesion en el Sistema...");window.close();</script>';
    exit();
}
include_once("../include/conn.php");
include("../include/NumeroAletras.php"); // convertir numeros a letras
$link = Conectarse(); //Variable de coneccion
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
        $date = date("d/m/Y");
        $rutpv = $_GET["id"];
        $ocomp = $_GET["oc"];
        if ($rutpv == "" || $ocomp == "") {
            echo '<script>alert("Error... No hay Datos para generar el Informe..");window.close();</script>';
            exit();
        }
        $sql = "SELECT `prd_codigo`,`prd_codcm`, `prd_glosa`,`prd_precio`,`prov_nomcontacto`,`prd_unimed`,`unimed_nombre`, `saldo_oc`, `saldo_rutprov`, `prov_nombre`,`prov_direccion`,`prov_rut`,`prov_dv`, `saldo_codigo`,`saldo_fechaoc`, `saldo_pendiente`,`saldo_recibido`,`saldo_solicitado`,`saldo_preciounit`,`saldo_est` FROM `productos`,`proveedores`,`saldos`,`unimed` WHERE `prd_codigo` = `saldo_codigo` AND `prd_unimed`= `unimed_id` AND `prov_rut`= `saldo_rutprov` AND `saldo_rutprov`='$rutpv' AND `saldo_oc`='$ocomp' ORDER BY saldo_codigo";
        $result = mysql_query($sql, $link);
        $row = mysql_fetch_array($result);
        $cuenta = mysql_num_rows($result);
        $result1 = mysql_query($sql, $link);
        $result2 = mysql_query("SELECT * FROM oc_obs WHERE oc_obs_oc='$ocomp' ORDER BY oc_obs_id DESC LIMIT 1", $link);
        $row2 = mysql_fetch_array($result2);
        $comentario = $row2["oc_obs_det"];
        echo '<table class="table3">';
        echo '<tr><td class="td1"><img src="../img/logo1.jpg" alt="logo" width="52" height="60"/></td><td class="td5">Hospital Clinico San Borja Arriarán</td><td class="td2">Fecha de Emisión: ' . date("d/m/Y, H:i:s", strtotime($row["saldo_fechaoc"])) . '</td></tr>';
        echo '</table>';
        echo '<table class="table4">';
        echo '<tr><td colspan="8" class="texto">ORDEN DE COMPRA N°: ' . $ocomp . '</td></tr>';
        echo '<tr><td colspan="2" class="td9">RUT Proveedor:</td><td colspan="6" class="td6">&nbsp;' . number_format($rutpv, 0, '', '.') . "-" . $row["prov_dv"];
        echo '<tr><td colspan="2" class="td9">Nombre Proveedor:</td><td colspan="6" class="td6">&nbsp;' . $row["prov_nombre"];
        echo '<tr><td colspan="2" class="td9">Dirección:</td><td colspan="6" class="td6">&nbsp;' . $row["prov_direccion"];
        echo '<tr><td colspan="2" class="td9">Nombre Representante:</td><td colspan="6" class="td6">&nbsp;' . $row["prov_nomcontacto"];
        echo '<tr><td colspan="2" class="td9">Observaciones y/o Comentarios:</td><td colspan="6" class="td6">&nbsp;<i style="color:red">' . $comentario . '</i>';
        echo '<tr><td colspan="2" class="td9">N° Articulos:</td><td colspan="6" class="td6">&nbsp;' . $cuenta;
        echo '<tr><td colspan="8" class="fondo"></td></tr>';
        echo '<tr>
             <th width="50">C&oacute;digo</th>
             <th width="50">Codigo CM</th>
             <th width="180">Descripción</th>
             <th width="30">U. M.</th>
             <th width="65">Precio Neto</th>
             <th width="40">Cantidad Solicitada</th>
             <th width="78">Total Neto Articulo</th> 
             <th width="10">Est.</th> 
            </tr>';
        while ($row1 = mysql_fetch_array($result1)) {
            $totalneto = $row1["saldo_solicitado"] * $row1["saldo_preciounit"];
            $est = $row1["saldo_est"];
            if ($est == 0) {
                $estoc = "<img class='imgcentrorpt' alt='OK' title='Item Vigente' src='../img/check1.png'>";
            } else {
                $estoc = "<img class='imgcentrorpt' alt='anulado' title='item Nulo' src='../img/error1.png'>";
            }
            printf("<tr><td class='td10'>%s</td><td>%s</td><td class='td7'>%s</td><td><b>%s</b></td><td>%s</td><td><b>%s</b></td><td>%s</td><td>%s</td></tr>", $row1["prd_codigo"], $row1["prd_codcm"], $row1["prd_glosa"], $row1["unimed_nombre"], '$ ' . number_format($row1["saldo_preciounit"], 2, ',', '.'), number_format($row1["saldo_solicitado"], 0, ',', '.'), '$ ' . number_format($totalneto, 0, ',', '.'), $estoc);
            $suma = $suma + $totalneto;
			$pend= $pend + $row1["saldo_pendiente"];
        }
        echo '<tr><td colspan="8" class="fondo"></td></tr>';
        echo '<tr><td colspan="6" class="td4">Total NETO:</td><td colspan="2" class="td4"><b> $ ' . number_format($suma, 0, ',', '.') . '</b></td></tr>';
        echo '<tr><td colspan="6" class="td4">IVA 19%:</td><td colspan="2" class="td4"><b> $ ' . number_format($suma * 0.19, 0, ',', '.') . '</b></td></tr>';
        echo '<tr><td colspan="6" class="td4"><b>Total Final:</b>&nbsp;&nbsp;' . convertir(number_format(($suma * 1.19), 0, '', '')) . ' PESOS.</td><td colspan="2" class="td4"><span class="texto4"> $ ' . number_format($suma * 1.19, 0, ',', '.') . '</span></td></tr>';
        echo '</table>';
        echo "<br>";
		if($pend==0){
			$pend= " (Orden de compra sin Pendientes) ";
		}else{
			$pend= " (Orden de compra en proceso de recepción) ";
		}		
        echo '<table class="table2">';
        echo '<tr><td class="td3"><b>Nota:</b>&nbsp;&nbsp;La presente Orden de Compra Contiene Sólo los articulos que se encuentran con saldos disponibles para recepcionar en Bodega. No representa la orden de Compra de Mercadopublico.<br><b>'.$pend.'</b></td></tr>';
        echo '</table>';
        echo "&nbsp;<br>&nbsp;<br>&nbsp;<br>&nbsp;<br>&nbsp;<br>&nbsp;<br>";
        echo '<table class="table4" style="border: hidden;background-color: #fff;">';
        echo '<tr style="border: hidden"><td style="border: hidden"><b>____________________________</b></td><td style="border: hidden"><b>____________________________</b></td><td style="border: hidden"><b>____________________________</b></td></tr>';
        echo '<tr style="border: hidden;background-color: #fff;"><td style="border: hidden"><b>Firma Dirección</b></td><td style="border: hidden"><b>Firma Depto. Finanzas</b></td><td style="border: hidden"><b>Firma Depto. G. Abastecimiento</b></td></tr>';
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
$canvas = $dompdf->get_canvas(); //n de pagina
$canvas->page_text(180, 770, "SigBod - Fecha de Impresion: " . date("d/m/Y H:i:s") . "  Página: {PAGE_NUM} de {PAGE_COUNT}", "Bold", 8, array(0, 0, 0));
$filename = "OC_" . "N°" . $ocomp . ".pdf";
$dompdf->stream($filename, array("Attachment" => 0)); // Enviamos el fichero PDF al navegador.*/
?>
