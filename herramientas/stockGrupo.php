<?php
$timeant = microtime(true); //calcular el tiempo de ejecucion, capturo tiempo inicial
session_start();
include("../include/conn.php");
$link = Conectarse(); //Variable de conexion
if (!isset($_SESSION['usuario'])) {
    header('Location: ../index.php');
    exit();
}
?>

<html>
    <head>
        <link rel="stylesheet" href="../css/tabla.css" />
        <link rel="stylesheet" href="../css/estilos.css" />
        <link rel="shortcut icon" href="../img/favicon.ico" />            
        <script src="../js/jquery-2.2.4.js" type="text/javascript"></script>
        <script src="../js/jquery.blockUI.js" type="text/javascript"></script>
        <script src="../js/tablesorter.min.js" type="text/javascript"></script>
        <script type="text/javascript">
            $(document).ready(function () {
                $(window).scroll(function () {
                    if ($(this).scrollTop() > 100) {
                        $('#scroll').fadeIn();
                    } else {
                        $('#scroll').fadeOut();
                    }
                });
                $('#scroll').click(function () {
                    $("html, body").animate({scrollTop: 0}, 600);
                    return false;
                });
            });
        </script>        
        <script type="text/javascript">
            function borrar() {
                if (confirm("¿Esta Seguro que quiere borrar este Producto del Grupo Actual?"))
                    return true;
                else
                    return false;
            }
        </script>
        <title>SigBod - stock por grupo</title>   
    </head>   
    <body class="fondo">
        <table class="table2">
            <tr>
                <td class="texto"><img border='0' alt='Nivel Stock' title='NivelStock' src='../img/level.png' width='23' height='23'>&nbsp;STOCK POR GRUPO DE ARTÍCULOS</td>
            </tr>
        </table>
        <?php
        $id = $_GET["id"];
        $cont = 0;
        $sql3 = "SELECT * FROM grupo_productos_detalle WHERE grupo_prd_id_id='$id' ORDER BY grupo_prd_det_codigo";
        $_SESSION['query'] = $sql3;
        $res3 = MySQL_query($sql3, $link)or die(mysql_error());
        $cuenta= mysql_num_rows($res3);
        echo '<table class="table2" id="nivelstock">';
        echo '<tr class="texto5"><thead>';
        echo '<th width = "80">Código Producto</th>';
        echo '<th width="365">Glosa</th>';
        echo '<th width = "70">Unidad de Medida</th>';
        echo '<th width="60" class="color_amarillo">Stock Actual</th>';
        echo '<th width = "60">Stock Critico</th>';
        echo '<th width="90">Analisis</th>';
        echo '<th width = "90">Fecha Ultima Solicitud</th>';
        echo '<th width="70">Folio Solicitud</th>';
        echo '<th width = "60"> Cantidad Solicitada</th>';
        echo '<th width="170" data-sorter="false" data-filter="false">Acciones</th></thead></tr>';

        while ($row3 = mysql_fetch_array($res3)) {
            $cod = $row3["grupo_prd_det_codigo"];
            $idcod=$row3["grupo_prd_det_id"];
            $sql = "SELECT * FROM productos, stock, unimed WHERE prd_codigo='$cod' AND prd_codigo=stock_codigo AND prd_unimed=unimed_id ORDER BY prd_codigo"; //mostrar solo los que tienen oc
            //$_SESSION['query'] = $sql;
            //echo $sql . "<br>"; //$_SESSION['query'] = $sql;
            $fecha = date("Y-m-d H:i:s");
            $fechanum = (strtotime(date("Y-m-d H:i:s")));
            $comilladob = '"';
            $comillasim = "'";
            $res = MySQL_query($sql, $link) or die(mysql_error());
            while ($row = mysql_fetch_array($res)) {
                $stock = $row["stock_cantidad"];
                $sql4 = "SELECT * FROM `tiponivel` WHERE `tiponivel_codigo`='$cod'";
                $res4 = MySQL_query($sql4, $link) or die(mysql_error());
                $contar4 = mysql_num_rows($res4);
                if ($contar4 == 0) {
                    $nivel = '<b style = "color:red; font-size: 9px;">No se han Definido niveles de Stock</b>';
                    $msj= '<b style = "color:red; font-size: 9px;">No se Puede Calcular</b>';
                    $nv = "<a href=../edicion/crearNivelStock.php?fam=" . substr($cod, 0, 3) . "&cod=" . $cod . " target='_blank '><img border='0  ' alt='crear nivel' title='crear nivel stock' src='../img/add.png' width='20' height='20'></a>";
                } else {
                    $row4 = MySQL_Fetch_array($res4);
                    $nivel = $row4["tiponivel_critico"];
                    $nv = "<a href=../edicion/modificarNivelStock.php?fam=" . substr($cod, 0, 3) . "&cod=" . $cod . " target='_blank '><img border='0  ' alt='editar nivel' title='editar nivel stock' src='../img/edit.png' width='20' height='20'></a>";
                    if ($stock <= $nivel) {
                        $msj = "<img class='imgcentro' alt='error' title='deficiente' src='../img/alert.png'><br>Realizar Pedido Ahora";
                    } else {
                        $msj = "<img class='imgcentro' alt='OK' title='aceptable' src='../img/check.png'><br>Stock Aceptable";
                    }
                }
                $sql1 = "SELECT * FROM detmovimiento, movimiento WHERE detmov_codigoprd = '$cod' AND mov_id=detmov_id ORDER by detmov_id desc LIMIT 1"; //seleccionar el ultimo registro de atras para adelante
                $res1 = MySQL_query($sql1, $link) or die(mysql_error());
                $contar = mysql_num_rows($res1);
                $sql2 = "SELECT * FROM saldos WHERE saldo_codigo='$cod' AND saldo_pendiente > 0 AND saldo_est=0";
                $res2 = MySQL_query($sql2, $link) or die(mysql_error());
                $contar2 = mysql_num_rows($res2);
                if ($contar2 == 0) {
                    $codoc = "<img border='0' alt='error' title='No Hay Ordenes de compra para el codigo " . $cod . "' src='../img/error.png' width='20' height='20'>";
                } else {
                    $codoc = "<a href=../pedidos/PedidoArticulo.php?codprd=" . $cod . "&op=1 target='_blank'><img border='0' alt='solicitar solo el articulo' title='Crear Solicitud' src='../img/pedido.png' width='20' height='22'></a>";
                }
                if ($contar == 0) {
                    $cant = "Sin Datos";
                    $fecha_mov = "<b>Sin Datos</b>";
                    $id_mov = "<b>Sin Datos</b>";
                    $sol = "<img border='0' alt='Documento no Encontrado' title='Documento no Encontrado' src='../img/noenc.png' width='24' height='20'>";
                    $cart = "<img border='0' alt='sin movimientos' title='Sin Movimientos que mostrar' src='../img/info.png' width='20' height='20'></a>";
                    $modcod = "<a href=../edicion/ModificarArticulo.php?id=" . $row["prd_codigo"] . "&tipo=1 target='_blank' title='Editar Articulo' onclick=" . $comilladob . "window.open(this.href, this.target, " . $comillasim . "height=620, width=800 left=250 top=30" . $comillasim . "); return false;" . $comilladob . "><img border='0' alt='editar articulo' title='Editar articulo en ventana externa' src='../img/edit1.png' width='20' height='20'></a>";
                    $borra= '<a href="../edicion/borrarArticuloGrupo.php?id=' . $idcod . '&grp=' . $id . '&cont=' . $cuenta . '&cod=' . $cod . '" onclick="return borrar();"><img border="0" alt="borrar articulo" title="borrar articulo" src="../img/papelera.png" width="20" height="20"></a>';                    
                } else {
                    $row1 = MySQL_Fetch_array($res1);
                    $cant = number_format($row1["detmov_cantidad"], 0, ',  ', '.   ');
                    $fecha_mov = date("d/m/Y h:i A", strtotime($row1["mov_fecha"]));
                    $id_mov = "SOL-" . $row1["detmov_id"];
                    $sol = "<a href=../reportes/pedidoPDF.php?id=" . str_replace("SOL-", "", $id_mov) . " target=' _blank'><img border='  0' alt='Ver solicitud' title='Ver Solicitud' src='../img/pdf.png' width='24' height='22'></a>";
                    $cart = "<a href=../reportes/cartolaArticulo.php?id=" . $row["prd_codigo"] . "><img border='0' alt='movimiento articulo' title='ver movimientos articulo: " . $row["prd_codigo"] . "' src='../img/lupan.png' width='20' height='20'></a>";
                    $modcod = "<a href=../edicion/ModificarArticulo.php?id=" . $row["prd_codigo"] . "&tipo=1 target='_blank' title='Editar Articulo' onclick=" . $comilladob . "window.open(this.href, this.target, " . $comillasim . "height=620, width=800 left=250 top=30" . $comillasim . "); return false;" . $comilladob . "><img border='0' alt='editar articulo' title='Editar articulo en ventana externa' src='../img/edit1.png' width='20' height='20'></a>";
                    $borra= '<a href="../edicion/borrarArticuloGrupo.php?id=' . $idcod . '&grp=' . $id . '&cont=' . $cuenta . '&cod=' . $cod . '" onclick="return borrar();"><img border="0" alt="borrar articulo" title="borrar articulo" src="../img/papelera.png" width="20" height="20"></a>';
                }
                printf("<tr><td><b>%s</b></td><td align='left'>%s</td><td>%s</td><td class='color_amarillo'><b>%s</b></td><td>%s</td><td><b>%s</b></td><td>%s</td><td>%s</td><td><b>%s</b></td><td>%s</td></tr>", $cod, $row["prd_glosa"], $row["unimed_nombre"], $stock, $nivel, $msj, $fecha_mov, $id_mov, $cant, $sol . "&nbsp;|&nbsp;" . $codoc . "&nbsp;|&nbsp;" . $cart . "&nbsp;|&nbsp;" . $nv . "&nbsp;|&nbsp;" . $modcod."&nbsp;|&nbsp;".$borra);

                $cont = $cont + 1;
            }
        }
        $timedesp = microtime(true); //calculo tiempo final
        $time = round($timedesp - $timeant, 2); //resto los tiempos y obtengo la demora de la ejecucucion
        echo '</tr>';
        echo '<table class="table2">';
        echo '<tr>';
        echo '<td colspan="10" class="texto8b">Se han encontrado: ' . $cont . ' Registros. (La consulta ha tardado: ' . $time . ' Segundos)</td>';
        echo '</tr>';
        echo '</table>';
        echo '<script type="text/javascript">$(document).ready(function () {$("#nivelstock").tablesorter();});</script>';
        mysql_close($link);
        ?>
        <table class="table2">
            <tr>
                <td class="texto6"><button class="botonnormal" onclick="window.location = '../principal.php'">Volver al Menu Principal&nbsp;<img class="img" alt="principal" title="volver al menu principal" src="../img/casa.png"></button>&nbsp;&nbsp;&nbsp;&nbsp;<button onclick="history.go(-1)" class="botonnormal">Volver Atras&nbsp;<img class='img' alt='volver' title='Volver a pantalla Anterior' src='../img/undo.png'></button>&nbsp;&nbsp;&nbsp;&nbsp;<button id="capturar" class="botonnormal" onclick="window.open('../reportes/stockGrupoPDF.php', '_blank', 'scrollbars=yes,statusbar=no,resizable=no,width=860,height=620 left=200,top=30')">Generar Informe en PDF&nbsp;<img class="img" alt="pdf" title="Exporta los datos actuales a formato PDF" src="../img/pdf1.png"></button></td>
            </tr>
        </table>            
        <a href="javascript:void(0);" id="scroll" title="Ir Arriba" style="display: none;">Arriba<span></span></a>
    </body>
</html>