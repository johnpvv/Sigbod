<?php
ob_start();
require_once("../dompdf/dompdf_config.inc.php"); // Cargamos la librería dompdf que hemos instalado en la carpeta dompdf
session_start();
if (!isset($_SESSION['usuario'])) {
    echo '<script>alert("Error con el inicio de Sesion en el Sistema...");</script>';
    exit();
}
$user = $_SESSION["usuario"];
$cod = $_GET['id'];
?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
		<title>SigBod - Etiquetas</title>
		<script src="../js/jquery-2.2.4.js" type="text/javascript"></script>
	</head> 
    <body style="margin: -0.48in -0.45in -0.7in -0.45in;">
        <div style="text-align:center;font-family: Sans-Serif;font-size:9px;padding-bottom: 2px;">Bodega Insumos Clinicos</div>
        <div style="text-align:center;"><img src="https://qrcode.tec-it.com/API/QRCode?data=<?=$cod?>&size=Medium&errorcorrection=M" id="img" style="width:85px;height:85px;"></div>
    </body>    
</html>
<?php

$dompdf = new \Dompdf\Dompdf(); // Instanciamos un objeto de la clase DOMPDF.
$dompdf->set_option('enable_remote', true);
$dompdf->set_paper(array(0, 0, 142.2637795276, 85.35826771653), 'portrait'); // Definir 5cm ancho por 3 cm alto.
$dompdf->load_html(ob_get_clean()); // Cargamos el contenido HTML.
$dompdf->render(); // Renderizamos el documento PDF.
$filename = "etiqueta_" . date("d/m/Y_H:i:s") . ".pdf";
$dompdf->stream($filename, array("Attachment" => 0)); // Enviamos el fichero PDF al navegador.
?>

