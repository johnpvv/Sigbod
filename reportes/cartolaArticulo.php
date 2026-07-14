<!DOCTYPE html>
<!--
Sigbod John Vaccarella
-->
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
            $(document).ready(function () {
                $("#tablamov").tablesorter();
            });
        </script>
        <title>SigBod - Cartola de Movimientos</title>   
    </head>   
    <body class="fondo">
        <?php
        session_start();
        if (!isset($_SESSION['usuario'])) {
            header('Location: ../index.php');
            exit();
        }
        if ($_GET['id'] == "") {
            echo"<script>alert('Error, no hay datos para mostrar.');javascript:history.go(-1)</script>";
            exit();
        }
        include("../include/conn.php");
        $link = Conectarse(); //Variable de conexion
        $cod = $_GET['id'];
        $fechaini = $_GET['fini'];
        $fechafin = $_GET['ffin']." 23:59:59";
        $op = $_GET['op'];
        $comilladob = '"';
        $comillasim = "'";
        $rango = "AND mov_fecha BETWEEN '$fechaini' AND '$fechafin'"; //prueba para buscar por rango de fechas        
        $filtrofecha = '</td><td colspan="2" class="texto8">INTERVALO DE FECHAS:</td><td colspan="4" class="texto4"><span class="texto6"></span>&nbsp;&nbsp;<b>' . date("d/m/Y", strtotime($fechaini)) . '&nbsp;&nbsp;</b><b>&nbsp;&nbsp;AL&nbsp;&nbsp;</b>&nbsp;&nbsp;<b>' . date("d/m/Y", strtotime($fechafin)) . '</b>&nbsp;&nbsp;</td></tr>';
        if ($fechaini == "" or $fechafin == "") {
            $rango = "";
            $filtrofecha = "</td></tr>";
            $cols = "colspan='9'";
        } else {
            $cols = "colspan='3'";
        }
        $sql = "SELECT * FROM productos, unimed, movimiento, detmovimiento, proveedores,tipomov,estado, stock WHERE (detmov_codigoprd='$cod') AND prd_codigo='$cod' AND stock_codigo='$cod' AND prd_unimed=unimed_id AND prov_rut=mov_rutprv AND mov_tipo=tipomov_id AND mov_estado=estado_id AND detmov_id=mov_id " . $rango . " ORDER BY mov_id DESC";
        $res = MySQL_query($sql, $link)or die(mysql_error());
        $res1 = MySQL_query($sql, $link)or die(mysql_error());
        $row1 = mysql_fetch_array($res1);
        $contar = mysql_num_rows($res);
        $estado = $row1["mov_estado"];
        if ($estado == "0") {
            $estado = " class='color_rojo'";
        } else {
            $estado = " class='texto5'";
        }
        if ($contar == 0) {
            echo '<script>alert("El codigo ingresado es invalido, o no ha tenido movimientos en las fechas seleccionadas");
                    history.back(-1);</script>';
            $op = 2;
        }
        if ($op == 2) {
            echo '<script>window.close();</script>';
        }
	$codlink="<a href=../edicion/modificarArticulo.php?id=" . $cod . "&tipo=1 target='_blank' title='Ver detalle Articulo' onclick=" . $comilladob . "window.open(this.href, this.target, " . $comillasim . "height=620, width=800 left=300 top=20" . $comillasim . "); return false;" . $comilladob . ">" . $cod . "</a>";
        echo '<table class="table2" align="center">';
        echo '<tr><td colspan="11" class="texto">CARTOLA DE ART&Iacute;CULO</td></tr>';
        echo '<tr><td colspan="2" class="texto8">CODIGO ARTICULO:</td><td colspan="9" class="texto9"><span class="texto6"></span><b>' . $codlink . '</b></td></tr>';
        echo '<tr><td colspan="2" class="texto8">GLOSA:</td><td colspan="9" class="texto9"><span class="texto6"> </span>' . $row1["prd_glosa"] . '</td></tr>';
        echo '<tr><td colspan="2" class="texto8">UNIDAD DE MEDIDA:</td><td colspan="9" class="texto9"><span class="texto6"> </span>' . $row1["unimed_nombre"] . '</td></tr>';
        echo '<tr><td colspan="2" class="texto8">N° MOVIMIENTOS:</td><td ' . $cols . ' class="texto9"><span class="texto6"> </span>' . $contar;
        echo $filtrofecha;
        if ($row1["prd_estado"] == 1) {
            $estadoart = "VIGENTE";
        } else {
            $estadoart = "NO VIGENTE";
        }
        $res2 = MySQL_query("call sp_obtenerSaldo('$cod')", $link)or die(mysql_error());
        $row2 = mysql_fetch_array($res2);
        //echo '<tr><td colspan="11" class="texto8c"></td></tr>';
        echo '<tr><td colspan="2" class="texto8">ESTADO ARTICULO:</td><td colspan="3" class="texto9"><span class="texto6"> </span>' . $estadoart . '</td><td colspan="2" class="texto8">STOCK ACTUAL:</td><td class="texto9">&nbsp;&nbsp;<b>' . $row1["stock_cantidad"] . '</b></td><td colspan="2" class="texto8">SALDO DISPONIBLE EN ORDENES DE COMPRA:</td><td class="texto9">&nbsp;&nbsp;<b>'.number_format($row2["V"],0, '', '.').'</b></td></tr>';
        echo '<tr><td colspan="11" class="texto8c"></td></tr>';
        echo '<tr><td colspan="11" class="texto7">DETALLE DE MOVIMIENTOS</td></tr></table>';
        echo '<table class="table2" id="tablamov">';
        echo '<thead><tr><th width="60">N° Solicitud</th><th width="100">Tipo Movimiento</th><th width="100">Fecha Solicitud</th><th width="120">Orden de Compra</th><th width="250">Proveedor</th><th width="80">Cantidad Solicitada</th><th width="80">Valor Neto Unitario</th><th width="50">Estado Solicitud</th><th width="50" data-sorter="false" data-filter="false">Acciones:</th></tr></thead>';
        while ($row = mysql_fetch_array($res)) {
            $estado = $row["mov_estado"];
            if ($estado == "0") {
                $estado = " class='color_rojo'";
            } else {
                $estado = " class='texto5'";
            }
            printf("<tr><td><b>%s</b></td><td>%s</td><td>%s</td><td><b>%s</b></td><td align='left'><b>%s</b></td><td><b>%s</b></td><td><b>%s</b></td><td" . $estado . ">%s</td><td>%s</td></tr>", "SOL-" . $row["mov_id"], $row["tipomov_nombre"], date("d/m/Y H:i:s", strtotime($row["mov_fecha"])), $row["detmov_oc"], "<a href=../edicion/modificarProveedor.php?id=" . $row["prov_rut"] . "&option=1 target='_blank' title='Ver detalle proveedor' onclick=" . $comilladob . "window.open(this.href, this.target, " . $comillasim . "height=560, width=800 left=300 top=100" . $comillasim . "); return false;" . $comilladob . ">" . number_format($row["prov_rut"], 0, '.', '.') . "-" . $row["prov_dv"] . "</a>&nbsp;" . " " . $row["prov_nombre"], number_format($row["detmov_cantidad"], 0, ',', '.'), '$' . number_format($row["detmov_precio"], 1, ',', '.'), $row["estado_nombre"], "<a href=../reportes/pedidoPDF.php?id=" . $row["mov_id"] . " target='_blank'><img border='0' alt='imprimir reporte' title='imprimir reporte pedido a: " . $row["prov_nombre"] . "' src='../img/pdf.png' width='30' height='28'></a>");
        }
        echo '<tr><td colspan="11" class="texto8a"></td></tr>';
        if ($op != 0) {
            echo '<tr><td colspan="11" class="texto5"><center><input type="button" value="Salir" class="boton" onclick="window.close()"/>&nbsp;&nbsp;&nbsp;&nbsp;';
        } else {
            echo '<tr><td colspan="11" class="texto5"><center><input type="button" value="Volver" class="boton" onclick="javascript:history.back(-1)"/>&nbsp;&nbsp;&nbsp;<input type="button" value="Volver al Menu Principal" class="boton" onclick="window.location = ' . $comillasim . '../principal.php' . $comillasim . '"/>&nbsp;&nbsp;&nbsp;&nbsp;';
        }
        echo '<input type="button" name="cartolapdf" id="pdf" value="Generar PDF" class="boton" onclick="window.open(' . $comillasim . '../reportes/cartolaPDF.php?id=' . $cod . '&fechaini=' . $fechaini . '&fechafin=' . $fechafin . $comillasim . ',' . $comillasim . '_blank' . $comillasim . ',' . $comillasim . 'scrollbars=yes,statusbar=no,resizable=no,width=860,height=620 left=200,top=30' . $comillasim . ')"/></center></td></tr>';
        echo '</table>';
        mysql_close($link);
        ?>
        <a href="javascript:void(0);" id="scroll" title="Ir Arriba" style="display: none;">Arriba<span></span></a>
    </body>
</html>