<?php
ob_start();
require_once("../dompdf/dompdf_config.inc.php"); // Cargamos la librería dompdf que hemos instalado en la carpeta dompdf
session_start();
if (!isset($_SESSION['usuario'])) {
    echo '<script>alert("Error con el inicio de Sesion en el Sistema...");</script>';
    exit();
}
$user = $_SESSION["usuario"];
include_once("../include/conn.php");
$link = Conectarse(); //Variable de coneccion
$cod = $_GET['id'];
$sql = "SELECT prd_glosa,unimed_nombre,prd_codigo FROM productos, unimed WHERE (prd_codigo='$cod') AND prd_unimed=unimed_id";
$res = MySQL_query($sql, $link)or die(mysql_error());
$row = MySQL_Fetch_array($res);
?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <title>SigBod - Etiquetas</title>   
    </head>    
    <body style="margin: -0.48in -0.45in -0.7in -0.45in;">
        <div style="text-align:center;font-family: Sans-Serif;font-size:9px">Bodega Insumos Clinicos</div>
        <div style="text-align:center;font-family: Sans-Serif;font-size:43px;font-weight: 700;border: 1px solid #000000; border-radius: 1px; padding: -6px;"><?= $row["prd_codigo"] ?></div>
        <div style="text-align:center;font-family: Sans-Serif;font-size:9px;padding-bottom: 1px;"><?= substr($row["prd_glosa"], 0, 120); ?></div>
        <div style="text-align:center;font-family: Sans-Serif;font-size:9px"><b>(<?= $row["unimed_nombre"] ?>)</b></div>
    </body>    
</html>
<?php
mysql_close($link);

$dompdf = new \Dompdf\Dompdf(); // Instanciamos un objeto de la clase DOMPDF.
$dompdf->set_paper(array(0, 0, 142.2637795276, 85.35826771653), 'portrait'); // Definir 5cm ancho por 3 cm alto.
$dompdf->load_html(ob_get_clean()); // Cargamos el contenido HTML.
$dompdf->render(); // Renderizamos el documento PDF.
$filename = "etiqueta_" . date("d/m/Y_H:i:s") . ".pdf";
$dompdf->stream($filename, array("Attachment" => 0)); // Enviamos el fichero PDF al navegador.
?>

