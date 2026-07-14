<?php
ob_start();
ini_set("memory_limit", "512M"); //tamaño de buffer
set_time_limit(600); // Cargamos la librería dompdf que hemos instalado en la carpeta dompdf
require_once("../dompdf/dompdf_config.inc.php");
session_start();
if (!isset($_SESSION['usuario'])) {
    echo '<script>alert("Error con el inicio de Sesion en el Sistema...");window.close();</script>';
    exit();
}
$user = $_SESSION["usuario"];
$query = $_SESSION['query_solicitudes'];
$fechahoy = strtotime(date("Y-m-d H:i:s"));
if ($query == "") {
    echo '<script>alert("No Hay Datos para Exportar");
  window.close();
  </script>';
    exit();
} else {
    $sql = $query;
}
//echo "Preparando Informe...Favor espere!";
?>

<html>
    <head>
        <meta charset="utf-8">
        <link rel="stylesheet" href="../css/reporte.css" />
        <link rel="shortcut icon" href="../img/favicon.ico" />
        <title>SigBod - Reportes Productos Solicitados</title>   
    </head>    
    <body style="margin: -0.3in 0in 0in 0in;">
        <?php
        include("../include/conn.php");
        $link = Conectarse();
        $res = MySQL_query($sql, $link)or die(mysql_error());
        $contar = mysql_num_rows($res);
        if ($contar >= 1000) {
            echo '<script>alert("Error, la cantidad de registros para el informe no debe superar los 1000, favor revise los filtros.");window.close();</script>';
            exit();
        }
        echo '<table class="table3a">';
        echo '<tr><td class="td1"><img src="../img/logo1.jpg" alt="logo" width="52" height="60"/></td><td class="td5">Hospital Clinico San Borja Arriarán</td><td class="td2">Fecha Consulta: ' . date("d/m/Y, h:i A") . '</td></tr>';
        echo '</table>';
        echo '<table class="table4">';
        echo '<tr><td colspan="9" class="texto">REPORTE PRODUCTOS SOLICITADOS</td></tr>';
        echo '<tr><td colspan="9" class="texto2">Unidad de Ingreso: ' . $_SESSION["unidad"] . '</td></tr>';
        echo '<tr>
             <td width="50">C&oacute;digo</td>
             <td width="180">Descripcion Artículo</td>
             <td width="40">U. M.</td>             
             <td width="60">Fecha Solicitud</td>
            <td width="60">Orden de Compra</td>
            <td width="180">Proveedor</td>
            <td width="40">Folio Solicitud</td>
            <td width="40">Cantidad Solicitada</td>
            <td width="60"><b>Total c/IVA</b></td>
            </tr>';
        while ($row = mysql_fetch_array($res)) {
            $codigo = $row["prd_codigo"];
            $valor = ($row["detmov_precio"] * $row["detmov_cantidad"]) * 1.19;
            printf("<tr><td class='td13'>%s</td><td class='td3'>%s</td><td>%s</td><td><b>%s</b></td><td>%s</td><td><b>%s</b></td><td>%s</td><td><b>%s</b></td><td>$%s</td></tr>", $codigo, $row["prd_glosa"], $row["unimed_nombre"], date("d/m/Y h:i A", strtotime($row["mov_fecha"])), $row["detmov_oc"], $row["prov_nombre"], $row["mov_id"], $row["detmov_cantidad"], number_format($valor, 0, ',', '.'));
            $cont++;
            $sum = $sum + $valor;
        }
        echo '</table>';
        echo '<table class="table2">';
        echo '<tr><td class="td10"><center>Total Artículos:&nbsp;&nbsp;' . $cont . '&nbsp;&nbsp;&nbsp;&nbsp;Total Valorizado C/IVA: $' . number_format($sum, 0, ',', '.') . '</center></td></tr>';
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
$canvas->page_text(280, 580, "SigBod - Fecha de Impresion: " . date("d/m/Y H:i:s") . "  Página: {PAGE_NUM} de {PAGE_COUNT}", "Bold", 7, array(0, 0, 0));
$filename = "Sigbod_por_recepcionar_" . $fechahoy . ".pdf";
$dompdf->stream($filename, array("Attachment" => 0)); // Enviamos el fichero PDF al navegador.
?>