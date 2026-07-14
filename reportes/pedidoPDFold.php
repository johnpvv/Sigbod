<?php
ob_start();
require_once("../dompdf/dompdf_config.inc.php");// Cargamos la librería dompdf que hemos instalado en la carpeta dompdf
session_start();
if (!isset($_SESSION['usuario'])) {
    echo '<script>alert("Error con el inicio de Sesion en el Sistema...");window.close();</script>';
    exit();
}
$id = $_GET["id"];
include_once("../include/conn.php");
$link = Conectarse();
?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <link rel="stylesheet" href="../css/reporte.css" />
        <link rel="shortcut icon" href="../img/favicon.ico" />
        <title>SigBod - Reporte Pedidos</title>   
    </head>    
    <body>
        <?php
        if (is_int($id / 100)) {
            $es = '<img src="../img/estrella.png" alt="logo" width="20" height="20"/>'; //cada 100 solicitudes aparece
        } else {
            $es = "";
        }
        $sql1 = "SELECT * FROM movimiento, proveedores, usuarios, estado WHERE (mov_id ='$id') AND usuario_rut=mov_usuario AND mov_rutprv=prov_rut AND mov_estado=estado_id"; //agrupar en parentesis los campos para que se ejecute primero la accion
        $data1 = mysql_query($sql1);
        $cuenta = mysql_num_rows($data1);
        if ($cuenta == 0) {
            echo '<script>alert("Los Datos ingresados no son validos, Favor intente Nuevamente...");history.back(-1);</script>';
            exit();
        }
        $row1 = mysql_fetch_array($data1);
        $pv = $row1["prov_nombre"]; //obtengo el nombre del proveedor para crear nomkbre de archivo pdf
        $estado = $row1["mov_estado"];
        if ($estado == 0) {
            $estado = " (Anulada)";
        } else {
            $estado = "";
        }
        echo '<table class="table3">';
        echo '<tr><td class="td1"><img src="../img/logo1.jpg" alt="logo" width="52" height="60"/></td><td class="td5">Hospital Clinico San Borja Arriarán</td><td class="td2">Fecha Solicitud: ' . date("d/m/Y, h:i A", strtotime($row1["mov_fecha"])) . '</td></tr>';
        echo '</table>';
        echo '<table class="table4">';
        echo '<tr><td colspan="2" class="texto">SOLICITUD INTERNA DE PEDIDO A PROVEEDOR N°  ' . $id . ' / ' . date("Y", strtotime($row1["mov_fecha"])) . $estado . '&nbsp;' . $es . '</td></tr>';
        echo '<tr><td colspan="2" class="texto5">Datos Generales del Proveedor:</td></tr>';
        echo '<tr><td colspan="2" class="texto6"><b>R.U.T. : </b> &nbsp;&nbsp;' . number_format($row1["prov_rut"], 0, ',', '.') . '-' . $row1["prov_dv"] . '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b> Nombre :  </b>&nbsp;&nbsp;' . $pv . '</td></tr>';
        echo '<tr><td colspan="2" class="texto5">Datos del Solicitante:</td></tr>';
        echo '<tr><td class="texto6"><b>Nombre: </b>&nbsp;&nbsp;' . $row1["usuario_nombre"] . ' ' . $row1["usuario_apellidos"] . '</td><td class="texto6"><b> Unidad Solicitante : </b>&nbsp;&nbsp;' . $row1["usuario_institucion"] . '</td></tr>';
        echo '<tr><td class="texto6"><b>Telefono : </b>&nbsp;&nbsp;' . $row1["usuario_telefono"] . '</td><td class="texto6"> <b>Email: </b>&nbsp;&nbsp;' . $row1["usuario_email"] . '</td></tr>';
        echo '<tr><td colspan="2" class="texto6"><b>Direccion de entrega:</b>&nbsp;&nbsp;' . $row1["usuario_direccion"] . '</td></tr>';
		echo '</table>';
        echo '<table class="table4">';
        echo '<tr><td colspan="7" class="texto5">********DETALLE********</td></tr>';
        echo '<tr><th width="40">Codigo Interno</th><th width="60">Codigo CM</th><th width="150">Glosa</th><th width="20">Empaque</th><th width="55">Precio Unitario Neto Referencial</th><th width="20">Cantidad Requerida</th><th width="80">Orden de Compra MercadoPúblico</th></tr>';
        $sql = "SELECT * FROM movimiento, detmovimiento, productos, unimed WHERE (mov_id ='$id') AND mov_id=detmov_id AND detmov_codigoprd=prd_codigo AND prd_unimed=unimed_id"; //agrupar en parentesis los campos para que se ejecute primero la accion
        $data = mysql_query($sql);
        while ($row = mysql_fetch_array($data)) {
            echo '<tr><td><b>' . $row["detmov_codigoprd"] . '</b></td><td>' . '<a class="texto7" href="http://www.mercadopublico.cl/TiendaFicha/Ficha?idProducto=' . $row["prd_codcm"] . '" target="_blank" title="Revisar codigo en CM">' . $row["prd_codcm"] . '</a>' . '</td><td class="td3">' . $row["prd_glosa"] . '
		 </td><td>' . $row["unimed_nombre"] . '</td><td>$ ' . number_format($row["detmov_precio"], 1, ',', '.') . '</td><td><b>' . number_format($row["detmov_cantidad"], 0, ',', '.') . '</b></td>
		 <td style="color:red;font-weight:600;">' . $row["detmov_oc"] . '</td></tr>';
            $sum = $sum + ((float) $row["detmov_precio"] * (float) $row["detmov_cantidad"]);
        }
        echo '<tr><td colspan="7" class="fondo"></td></tr>';
        echo '<tr><td colspan="6" class="td4">Total NETO:</td><td class="td4"><b> $ ' . number_format($sum, 0, ',', '.') . '</b></td></tr>';
        echo '<tr><td colspan="6" class="td4">IVA 19%:</td><td class="td4"><b> $ ' . number_format(($sum * 0.19), 0, ',', '.') . '</b></td></tr>';
        echo '<tr><td colspan="6" class="td4"><b>Total Solicitado (IVA Incluido):</b></td><td class="td4"><span class="texto4"> $ ' . number_format(($sum * 1.19), 0, ',', '.') . '</span></td></tr>';
        echo '</table>';
        echo '<br>';
        if ($row1["mov_observacion"] <> "") {
            echo '<table class="table2">';
            echo '<tr><td class="td3"><b><u>OBSERVACIONES:</u></b>&nbsp;&nbsp;' . str_replace("\n", "<br>", $row1["mov_observacion"]) . '</td></tr>';
            echo '</table>';
        }
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
$canvas->page_text(200, 760, "SigBod - Fecha de Impresion: " . date("d/m/Y H:i:s") . "  Página: {PAGE_NUM} de {PAGE_COUNT}", "Bold", 8, array(0, 0, 0));
$pv1 = str_replace(" ", "_", $pv); //quitar espacios en el nombre del proveedor y cambiarlos por guiones bajos
$filename = "Solicitud_N_" . $id . "_" . $pv1 . ".pdf";
$dompdf->stream($filename, array("Attachment" => 0)); // Enviamos el fichero PDF al navegador.
?>

