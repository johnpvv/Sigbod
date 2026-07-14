<?php
ob_start();
require_once("../dompdf/dompdf_config.inc.php"); // Cargamos la librería dompdf que hemos instalado en la carpeta dompdf
session_start();
if (!isset($_SESSION['usuario'])) {
    echo '<script>alert("Error con el inicio de Sesion en el Sistema...");window.close();</script>';
    exit();
}
$user = $_SESSION["usuario"];
include_once("../include/conn.php");
$link = Conectarse(); //Variable de coneccion
$id = $_GET["id"];
if ($id == "") {
    echo '<script>alert("Error, No se ha encontrado el artículo.");window.close();</script>';
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <link rel="stylesheet" href="../css/reporte.css" />
        <link rel="shortcut icon" href="../img/favicon.ico" />
        <title>SigBod - Ficha Articulo</title>   
    </head>    
    <body style="margin: -0.4in 0in 0in 0in;">
        <?php
        $date = date("d/m/Y");
        $sql = "SELECT * FROM productos WHERE prd_codigo='$id'"; //agrupar en parentesis los campos para que se ejecute primero la accion
        $data = mysql_query($sql);
        $row = mysql_fetch_array($data);
        $ud = $row["prd_unimed"];
        $sql2 = "SELECT * FROM unimed WHERE unimed_id='$ud'";
        $res2 = MySQL_query($sql2, $link)or die(mysql_error());
        $row2 = mysql_fetch_array($res2);
        $udp = $row["prd_prov_unimed"];
        $sql3 = "SELECT * FROM unimed WHERE unimed_id='$udp'";
        $res3 = MySQL_query($sql3, $link)or die(mysql_error());
        $row3 = mysql_fetch_array($res3);
        $uniprov=$row3["unimed_nombre"];
        if($uniprov=="--Elija Unidad--"){
            $uniprov="Sin Datos";
        }
        echo '<table class="table3">';
        echo '<tr><td class="td1"><img src="../img/logo1.jpg" alt="logo" width="52" height="60"/></td><td class="td5">Hospital Clinico San Borja Arriarán</td><td class="td2">Fecha de Emisión: ' . date("d/m/Y, h:i A") . '</td></tr>';
        echo '</table>';
        echo '<table class="table4">';
        echo '<tr><td colspan="2" class="texto">FICHA DE PRODUCTO</td></tr>';
        echo '<tr><td class="td9" style="width:180px;">Código Producto:</td><td class="td6a">&nbsp;' . $id . "</td></tr>";
        echo '<tr><td class="td9">Glosa:</td><td class="td6">&nbsp;' . $row["prd_glosa"] . "</td></tr>";
        echo '<tr><td class="td9">Unidad de Medida Local:</td><td class="td6">&nbsp;' . $row2["unimed_nombre"] . " (" . $row2["unimed_UM"] . ")</td></tr>";
        echo '<tr><td class="td9">Presentación del Proveedor:</td><td class="td6">&nbsp;' . $uniprov . "</td></tr>";
        echo '<tr><td class="td9">Descripción Ampliada:</td><td class="td7">&nbsp;' . $row["prd_glosaamp"] . "</td></tr>";
        echo '<tr><td class="td9">Referencia de Proveedor:</td><td class="td7">&nbsp;' . $row["prd_ref"] . "</td></tr>";
        echo '<tr><td class="td9">Código Convenio Marco:</td><td class="td6">&nbsp;' . $row["prd_codcm"] . "</td></tr>";
        echo '<tr><td class="td9">Código CENABAST:</td><td class="td6">&nbsp;' . $row["prd_cenabast"] . "</td></tr>";
        if ($row["prd_barcode"] != "") {
            echo '<tr><td class="td9">Código de Barras:</td><td class="td6">&nbsp;<img src="https://barcode.tec-it.com/barcode.ashx?data=' . $row["prd_barcode"] . '&code=EAN13&multiplebarcodes=false&translate-esc=true&dpi=128&imagetype=Jpg" alt="Barcode" width="160" height="70" style="padding:2px;"></td></tr>';
        } else {
            echo '<tr><td class="td9">Código de Barras:</td><td class="td6">&nbsp;</td></tr>';
        }
        echo '<tr><td class="td9">Precio Ult. Compra Neto:</td><td class="td6">&nbsp;$ ' . number_format($row["prd_precio"], 1, ',', '.') . "</td></tr>";
        echo '<tr><td colspan="2" class="fondo"></td></tr>';
        echo '<tr><td colspan="2" class="td3">Foto del Producto:</td></tr>';
        if ($row["prd_imagen"] == "") {
            $img = "../img/noimg.jpg";
        } else {
            $img = $row["prd_imagen"];
        }
        echo '<tr><td colspan="2"><img src="' . $img . '" alt="Foto Articulo" style="max-width: 300px; max-height: 350px; padding:2px;"></td></tr>';
        echo '<tr><td colspan="2" class="td3">Informacion Adicional:</td></tr>';
        $str = 'Codigo Articulo: ' . $id . ' Descripcion: ' . $row["prd_glosa"] . ', Precio Neto: $' . $row["prd_precio"] . ', Unidad de Medida: ' . $row2["unimed_nombre"];
        $str = str_replace(" ", "%20", $str);
        echo '<tr><td colspan="2"><img src="https://qrcode.tec-it.com/API/QRCode?data=' . $str . '&size=Small&errorcorrection=M" alt="QR" width="160" height="160" style="padding:2px;"></td></tr>';
        echo '</table>';
        mysql_close($link);
        ?>
    </body>    
</html>
<?php
$dompdf = new \Dompdf\Dompdf(); // Instanciamos un objeto de la clase DOMPDF.
$dompdf->set_option('enable_remote', true);
$dompdf->set_paper("letter", "portrait"); // Definimos el tamaño y orientación del papel que queremos.
$dompdf->load_html(ob_get_clean()); // Cargamos el contenido HTML.
$dompdf->render(); // Renderizamos el documento PDF.
$canvas = $dompdf->get_canvas(); //n de pagina
$canvas->page_text(170, 770, "SigBod - Fecha de Impresion: " . date("d/m/Y H:i:s") . "  Página: {PAGE_NUM} de {PAGE_COUNT}", "Bold", 8, array(0, 0, 0));
$filename = "ficha_" . date("d/m/Y_H:i:s") . ".pdf";
$dompdf->stream($filename, array("Attachment" => 0)); // Enviamos el fichero PDF al navegador.
?>

