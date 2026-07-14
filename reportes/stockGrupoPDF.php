<?php
ob_start();
ini_set("memory_limit", "128M"); //tamaño de buffer
set_time_limit(600);// Cargamos la librería dompdf que hemos instalado en la carpeta dompdf
require_once("../dompdf/dompdf_config.inc.php");
session_start();
if (!isset($_SESSION['usuario'])) {
    echo '<script>alert("Error con el inicio de Sesion en el Sistema...");window.close();</script>';
    exit();
}
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
        <title>SigBod - Reportes Stock Grupo</title>   
    </head>    
    <body style="margin: -0.3in 0in 0in 0in;">
        <?php
        include("../include/conn.php");
        $link = Conectarse();
        $contdef=0;
        $contnorm=0;
        if ($contar >= 100) {
            echo '<script>alert("Error, la cantidad de registros para el informe no debe superar los 100, favor revise los filtros.");				
			window.close();
	</script>';
            exit();
        }
        echo '<table class="table3">';
        echo '<tr><td class="td1"><img src="../img/logo1.jpg" alt="logo" width="52" height="60"/></td><td class="td5">Hospital Clinico San Borja Arriarán</td><td class="td2">Fecha Consulta: ' . date("d/m/Y, h:i A") . '</td></tr>';
        echo '</table>';
        echo '<table class="table4">';
        echo '<tr><td colspan="7" class="texto">REPORTE NIVELES DE STOCK POR GRUPO</td></tr>';
        echo '<tr>
             <td width="50">C&oacute;digo</td>
             <td width="270">Descripcion Artículo</td>
             <td width="40">U. M.</td>
             <td width="40">Stock Actual</td>
             <td width="40"><b>Stock Critico</b></td>
             <td width="40"><b>% Faltante</b></td>
             <td width="45"><b>Análisis</b></td>
            </tr>';
        $res = MySQL_query($sql, $link)or die(mysql_error());
        $contar = mysql_num_rows($res);
        while ($row = mysql_fetch_array($res)) {
            $codigo = $row["grupo_prd_det_codigo"];
            $id= $row["grupo_prd_id_id"];
            $sql1 = "SELECT * FROM tiponivel WHERE tiponivel_codigo='$codigo'";
            $res1 = MySQL_query($sql1, $link) or die(mysql_error());
            $contar1 = mysql_num_rows($res1);
            $row1 = MySQL_Fetch_array($res1);
            $sql2 = "SELECT * FROM productos, unimed, stock WHERE prd_codigo='$codigo' AND unimed_id=prd_unimed AND stock_codigo=prd_codigo";
            $res2 = MySQL_query($sql2, $link) or die(mysql_error());
            $row2 = MySQL_Fetch_array($res2);
            $stock = $row2["stock_cantidad"];            
            if ($contar1 == 0) {
                $nivel = '<b style = "color:red; font-size: 9px;">No se han Definido niveles de Stock</b>';
                $msj = '<b style = "color:red; font-size: 9px;">No se Puede Calcular</b>';
                $porcentaje = '<b style = "color:red; font-size: 9px;">No se Puede Calcular</b>';
            } else {
                $nivel = $row1["tiponivel_critico"];                
                if ($stock <= $nivel) {
                    $msj = "<img class='imgcentrorpt' alt='error' title='deficiente' src='../img/error1.png'><br>Realizar Pedido";
                    $porcentaje = number_format((((($stock / $nivel) * 100) - 100) * -1), 1, ',', '.') . "%";
                    $contdef++;
                } else {
                    $msj = "<img class='imgcentrorpt' alt='OK' title='aceptable' src='../img/check1.png'><br>Stock Aceptable";
                    $porcentaje = "0%";
                    $contnorm++;
                }
            }
            printf("<tr><td class='td13'>%s</td><td class='td3'>%s</td><td>%s</td><td><b>%s</b></td><td>%s</td><td><b>%s</b></td><td>%s</td></tr>", $codigo, $row2["prd_glosa"], $row2["unimed_nombre"], number_format($stock, 0, ',', '.'), $nivel, $porcentaje, $msj);
        }
        echo '</table>';
        echo '<table class="table2">';
        echo '<tr><td class="td10"><center>ID Grupo: '.$id.',&nbsp;&nbsp;Stock Normal= '.$contnorm.' &nbsp;&nbsp;&nbsp;Stock Deficiente= '.$contdef.'&nbsp;&nbsp;&nbsp; (Total Artículos del Grupo:&nbsp;' . $contar . ') </center></td></tr>';
        echo '</table>';
        //echo $sql;
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
$filename = "Sigbod_stockGrupo.pdf";
$dompdf->stream($filename, array("Attachment" => 0)); // Enviamos el fichero PDF al navegador.
?>