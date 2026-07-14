<?php
ob_start();
ini_set("memory_limit", "512M"); //tamaño de buffer
set_time_limit(600);
require_once("../dompdf/dompdf_config.inc.php");// Cargamos la librería dompdf que hemos instalado en la carpeta dompdf
session_start();
if (!isset($_SESSION['usuario'])) {
    echo '<script>alert("Error con el inicio de Sesion en el Sistema...");window.close();</script>';
    exit();
}
$user = $_SESSION["usuario"];
$query = $_SESSION['query'];
if ($query == "") {
    echo '<script>alert("No Hay Datos para Exportar");				
			window.close();
	</script>';
    exit();
} else {
    $sql = $query;
}
?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <link rel="stylesheet" href="../css/reporte.css" />
        <link rel="shortcut icon" href="../img/favicon.ico" />
        <title>SigBod - Reportes Nivel Stock</title>   
    </head>    
    <body style="margin: -0.3in 0in 0in 0in;">
        <?php
        include("../include/conn.php");
        $link = Conectarse();
        $res = MySQL_query($sql, $link)or die(mysql_error());
        $contar = mysql_num_rows($res);
        if ($contar >= 700) {
            echo '<script>alert("Error, la cantidad de registros para el informe no debe superar los 700, favor revise los filtros.");				
			window.close();
	</script>';
            exit();
        }
        echo '<table class="table3">';
        echo '<tr><td class="td1"><img src="../img/logo1.jpg" alt="logo" width="52" height="60"/></td><td class="td5">Hospital Clinico San Borja Arriarán</td><td class="td2">Fecha Consulta: ' . date("d/m/Y, h:i A") . '</td></tr>';
        echo '</table>';
        echo '<table class="table4">';
        echo '<tr><td colspan="7" class="texto">REPORTE NIVELES DE STOCK INSUFICIENTES</td></tr>';
        echo '<tr>
             <td width="50">C&oacute;digo</td>
             <td width="270">Descripcion Artículo</td>
             <td width="40">U. M.</td>
             <td width="40">Stock Actual</td>
             <td width="40"><b>Stock Critico</b></td>
             <td width="40"><b>% Faltante</b></td>
             <td width="45"><b>Tipo de Quiebre</b></td>
            </tr>';
        while ($row = mysql_fetch_array($res)) {
            $codigo=$row["prd_codigo"];
            $sql1 = "SELECT * FROM nivelstock WHERE nivelstock_cod='$codigo'";
            $res1 = MySQL_query($sql1, $link) or die(mysql_error());
            $row1 = MySQL_Fetch_array($res1);
            $contador = $row1["nivelstock_contador"];
            if ($contador < 15) {
                $tipo = '<b style = "color:green;">Nuevo</b>';
            } else if ($contador >= 15 && $contador <= 30) {
                $tipo = '<b style = "color:blue;">Reciente</b>';
            } else {
                $tipo = '<b style = "color:red;">Antiguo</b>';
            }
            printf("<tr><td class='td13'>%s</td><td class='td3'>%s</td><td>%s</td><td><b>%s</b></td><td>%s</td><td><b>%s</b></td><td>%s</td></tr>", $codigo, $row["prd_glosa"], $row["unimed_nombre"], number_format($row["stock_cantidad"], 0, ',', '.'), $row["tiponivel_critico"],number_format((((($row["stock_cantidad"]/$row["tiponivel_critico"])*100)-100)*-1), 1, ',', '.')."%", $tipo);
        }
        echo '</table>';
        echo '<table class="table2">';
        echo '<tr><td class="td10"><center>Total Artículos:&nbsp;&nbsp;' . $contar . '</center></td></tr>';
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
$canvas->page_text(280, 765, "Página: {PAGE_NUM} de {PAGE_COUNT}", "Bold", 10, array(0, 0, 0));
$filename = "Sigbod_Nivelstock.pdf";
$dompdf->stream($filename, array("Attachment" => 0)); // Enviamos el fichero PDF al navegador.
?>