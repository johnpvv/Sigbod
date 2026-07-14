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
$query = $_SESSION['query'];
$fechahoy = strtotime(date("Y-m-d H:i:s"));
$fechaactual = $_SESSION["fecha1"];
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
        <title>SigBod - Reportes Productos por Ingresar</title>   
    </head>    
    <body style="margin: -0.3in 0in 0in 0in;">
        <?php
        include("../include/conn.php");
        $link = Conectarse();
        $res = MySQL_query($sql, $link)or die(mysql_error());
        $contar = mysql_num_rows($res);
        $dia = $_SESSION["dia1"];
        if ($contar >= 1000) {
            echo '<script>alert("Error, la cantidad de registros para el informe no debe superar los 1000, favor revise los filtros.");				
			window.close();
			</script>';
            exit();
        }

        echo '<table class="table3a">';
        echo '<tr><td class="td1"><img src="../img/logo1.jpg" alt="logo" width="52" height="60"/></td><td class="td5">Hospital Clinico San Borja Arriarán</td><td class="td2">Fecha Consulta: ' . date("d/m/Y, h:i A") . '</td></tr>';
        echo '</table>';
        echo '<table class="table4">';
        echo '<tr><td colspan="10" class="texto">REPORTE PRODUCTOS PENDIENTES DE RECIBIR</td></tr>';
        echo '<tr><td colspan="10" class="texto2">Unidad de Ingreso: ' . $_SESSION["unidad"] . '</td></tr>';
        echo '<tr>
             <td width="50">C&oacute;digo</td>
             <td width="180">Descripcion Artículo</td>
             <td width="40">U. M.</td>
             <td width="40">Stock Actual</td>
             <td width="40"><b>Stock Critico</b></td>
             <td width="60">Fecha Ultima Solicitud</td>
            <td width="60">Orden de Compra</td>
            <td width="150">Proveedor</td>
            <td width="40">Folio Solicitud</td>
            <td width="40"> Cantidad Solicitada</td>
            </tr>';
        while ($row = mysql_fetch_array($res)) {
            $codigo = $row["prd_codigo"];
            $sql6 = "SELECT * FROM detmovimiento, movimiento, proveedores WHERE detmov_codigoprd = '$codigo' AND mov_id=detmov_id AND mov_rutprv=prov_rut ORDER BY detmov_id desc LIMIT 1"; //seleccionar el ultimo registro de atras para adelante
            $res6 = MySQL_query($sql6, $link) or die(mysql_error());
            $row6 = mysql_fetch_array($res6);
            $fechamov = strtotime($row6["mov_fecha"]);
            $dif6 = round(($fechaactual - $fechamov) / (3600 * 24));
            if ($dif6 < $dia) {
                printf("<tr><td class='td13'>%s</td><td class='td3'>%s</td><td>%s</td><td><b>%s</b></td><td>%s</td><td>%s</td><td><b>%s</b></td><td>%s</td><td><b>%s</b></td><td>%s</td></tr>", $codigo, $row["prd_glosa"], $row["unimed_nombre"], number_format($row["stock_cantidad"], 0, ',', '.'), $row["tiponivel_critico"], date("d/m/Y h:i A", strtotime($row6["mov_fecha"])), $row6["detmov_oc"], $row6["prov_nombre"], $row6["mov_id"], $row6["detmov_cantidad"]);
                $cont++;
            }
        }
        echo '</table>';
        echo '<table class="table2">';
        echo '<tr><td class="td10"><center>Total Artículos:&nbsp;&nbsp;' . $cont . '</center></td></tr>';
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
$canvas->page_text(280, 765, "Página: {PAGE_NUM} de {PAGE_COUNT}", "Bold", 10, array(0, 0, 0));
$filename = "Sigbod_por_recepcionar_" . $fechahoy . ".pdf";
$dompdf->stream($filename, array("Attachment" => 0)); // Enviamos el fichero PDF al navegador.
?>