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
$link = Conectarse(); //Variable de coneccion
?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <link rel="stylesheet" href="../css/reporte.css" />
        <link rel="shortcut icon" href="../img/favicon.ico" />
        <title>SigBod - Cotización</title>   
    </head>    
    <body style="margin: -0.3in 0in 0in 0in;">
        <?php
        $date = date("d/m/Y");
        $i = $_POST["contador"]; //cantidad de elementos totales
        $comentario = $_POST["txtcom"];
        $contart = $_POST["contart"];
        $totalneto = $_POST["txttotalneto"];
        $iva = $_POST["txtiva"];
        $totalfinal = $_POST["txttotalfinal"];
        $codigo = [];
        $cm = [];
        $glosa = [];
        $um = [];
        $precunit = [];
        $cant = [];
        $prectotal = [];
        $sql = "SELECT * FROM usuarios WHERE usuario_rut='$user'"; //agrupar en parentesis los campos para que se ejecute primero la accion
        $data = mysql_query($sql);
        $row = mysql_fetch_array($data);
        $nombreusuario = $row["usuario_nombre"] . " " . $row["usuario_apellidos"];
        for ($j = 0; $j < $i; $j++) {
            $codigo[$j] = $_POST["txtcod_" . $j]; //array con los codigos
            $cm[$j] = $_POST["txtcm_" . $j]; //array con los codigos de cm
            $glosa[$j] = $_POST["txtglosa_" . $j]; //array con las glosas
            $um[$j] = $_POST["txtum_" . $j]; //array con la unidad de medida
            $precunit[$j] = $_POST["txtprec_" . $j]; //array con los precios 
            $cant[$j] = $_POST["txtreq_" . $j]; //array con la cantidad 
            $prectotal[$j] = $_POST["txtprectotal_" . $j];
        }
        echo '<table class="table3">';
        echo '<tr><td class="td1"><img src="../img/logo1.jpg" alt="logo" width="52" height="60"/></td><td class="td5">Hospital Clinico San Borja Arriarán</td><td class="td2">Fecha de Emisión: ' . date("d/m/Y, h:i A") . '</td></tr>';
        echo '</table>';
        echo '<table class="table4">';
        echo '<tr><td colspan="7" class="texto">COTIZACIÓN</td></tr>';
        echo '<tr><td colspan="2" class="td9">Usuario Generador:</td><td colspan="5" class="td7">&nbsp;' . $nombreusuario;
        echo '<tr><td colspan="2" class="td9">Observaciones y/o Comentarios:</td><td colspan="5" class="td6">&nbsp;' . $comentario;
        echo '<tr><td colspan="2" class="td9">N° Articulos:</td><td colspan="5" class="td6">&nbsp;' . $contart;
        echo '<tr><td colspan="7" class="fondo"></td></tr>';
        echo '<tr><td colspan="7" class="td9">Por medio de la presente, tengo el agrado de cotizar a Usted el(los) siguiente(s) producto(s) y/o servicio(s):&nbsp;&nbsp;&nbsp;</td></tr>';
        echo '<tr>
             <th width="50">C&oacute;digo</th>
             <th width="50">Codigo CM</th>
             <th width="200">Descripción</th>
             <th width="40">U. M.</th>
             <th width="50">Precio Neto</th>
             <th width="50">Cantidad</th>
             <th width="65">Total Neto Articulo</th> 
            </tr>';
        for ($k = 0; $k < count($codigo); $k++) {
            if ($codigo[$k] == "") {
                
            } else {
                printf("<tr><td class='td10'>%s</td><td>%s</td><td class='td7'>%s</td><td><b>%s</b></td><td>%s</td><td><b>%s</b></td><td>%s</td></tr>", $codigo[$k], $cm[$k], $glosa[$k], $um[$k], '$ ' . number_format($precunit[$k], 0, ',', '.'), number_format($cant[$k], 0, ',', '.'), '$ ' . number_format($prectotal[$k], 0, ',', '.'));
            }
        }
        echo '<tr><td colspan="7" class="fondo"></td></tr>';
        echo '<tr><td colspan="6" class="td4">Total NETO:</td><td class="td4"><b> $ ' . number_format($totalneto, 0, ',', '.') . '</b></td></tr>';
        echo '<tr><td colspan="6" class="td4">IVA 19%:</td><td class="td4"><b> $ ' . number_format($iva, 0, ',', '.') . '</b></td></tr>';
        echo '<tr><td colspan="6" class="td4"><b>Total Final:</b></td><td class="td4"><span class="texto4"> $ ' . number_format($totalfinal, 0, ',', '.') . '</span></td></tr>';
        echo '</table>';
        echo "<br>";
        echo '<table class="table2">';
        echo '<tr><td class="td3"><b>Nota:</b>&nbsp;&nbsp;La presente cotización tiene una validez de 15 días, desde la fecha de emisión.</td></tr>';
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
$canvas->page_text(160, 760, "SigBod - Fecha de Impresion: " . date("d/m/Y H:i:s") . "  Página: {PAGE_NUM} de {PAGE_COUNT}", "Bold", 10, array(0, 0, 0));
$filename = "Cotizacion_" . date("d/m/Y_H:i:s") . ".pdf";
$dompdf->stream($filename, array("Attachment" => 0)); // Enviamos el fichero PDF al navegador.
?>

