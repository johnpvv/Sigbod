<!DOCTYPE html>
<!-- fecha creacion: 14/06/2019
Sigbod John Vaccarella
-->
<html>
    <head>
        <link rel="stylesheet" href="../css/tabla.css" />
        <link rel="stylesheet" href="../css/estilos.css" />
        <link rel="shortcut icon" href="../img/favicon.ico" />
        <script src="../js/jquery-2.2.4.js" type="text/javascript"></script>
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
                $("#capturar").click(function () {
                    if (confirm("¿Esta Seguro que los datos estan correctos?")) {
                        document.form.submit();
                    } else {
                        return 0;
                    }
                });
            });
        </script>
        <script type="text/javascript">
            function borrar() {
                if (confirm("¿Esta Seguro que quiere borrar este Producto del Nivel de Stock?"))
                    return true;
                else
                    return false;
            }
        </script>		
        <title>SigBod - Editar Niveles de Stock</title>
    </head>
    <body class="fondo">
        <?php
        include_once("../include/conn.php");
        session_start();
        if (!isset($_SESSION['usuario'])) {
            header('Location: ../index.php');
            exit();
        }
        $link = Conectarse();
        $fam = $_GET['fam']; //obtenemos la familia elegida elegido
        $cod = $_GET['cod']; //obtenemos codigo si se ha enviado
        if ($cod == null) {
            $varsql = "";
            $btn = ' value="Volver atras"  onclick="javascript:history.back(-1)" ';
            $opc = 0;
        } else {
            $varsql = " AND `prd_codigo`='$cod'";
            $btn = ' value="Salir"  onclick="javascript:window.close()"';
            $opc = 1;
        }
        if ($opc == 0) {
            $opv = "history.back(-1)";
        } else {
            $opv = "window.close()";
        }
        $sql = "SELECT `prd_codigo`, `prd_glosa`, `prd_unimed`,`prd_precio`,`unimed_nombre`,`tiponivel_codigo`,`tiponivel_fam`,`tiponivel_max`,`tiponivel_min`,`tiponivel_critico`,`stock_cantidad` FROM `productos`,`tiponivel`,`unimed`, stock WHERE `prd_codigo` = `tiponivel_codigo` AND `prd_unimed`= `unimed_id` AND prd_codigo=`stock_codigo` AND prd_fam='$fam' " . $varsql . " ORDER BY tiponivel_codigo";
        $result = mysql_query($sql, $link);
        $cuenta = mysql_num_rows($result);
        if ($cuenta == 0) {
            echo '<script>alert("Los Datos ingresados no son validos, Favor intente Nuevamente...");
				' . $opv . ';</script>';
            $_SESSION['query'] = "";
        } else {
            $_SESSION['query'] = $sql;
        }
        ?>
        <form id="form" name="form" action="../edicion/modificarNivelStockBD.php" method="post">
            <table class="table1">
                <tr>
                    <td colspan="9" class="texto"><center><img border='0' alt='Nivel Stock' title='NivelStock' src='../img/level.png' width='23' height='23'>&nbsp;EDITAR NIVELES DE STOCK</center></td>
                </tr>
                <tr>                 
                    <td colspan="9" class="texto1b"><center>FAMILIA:  <b class="texto0b"> <?php echo $fam; ?></b><br> Cantidad de registros encontrados: <b><?php echo $cuenta; ?></b></center></td>
                </tr>
                <tr>
                    <td colspan="9" class="texto8c"></td>
                </tr>
                <tr class="texto5">
                    <th width="80">Código Interno</th>
                    <th width="500">Glosa</th>
                    <th width="80">Unidad de Medida</th>
                    <th width="70">Stock Actual</th>
                    <th width="70">Stock Crítico</th>
                    <th width="70">Stock Mínimo</th>
                    <th width="70">Stock Máximo</th>
                    <th width="130">Precio Unitario</th>
                    <th width="30">Acciones</th>
                </tr>
                <?php
                $cont = 0;
                while ($row = mysql_fetch_array($result)) {
                    printf("<tr><td><b>%s</b></td><td align='left'>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td><b>$  %s</b></td><td>%s</td></tr>", '<input type="textbox" class="caja2" readonly size="7" name="txtcod_' . $cont . '" value="' . $row["prd_codigo"] . '"/>', $row["prd_glosa"], $row["unimed_nombre"], $row["stock_cantidad"], '<input type="textbox" class="caja_rojo" size="6" value="' . $row["tiponivel_critico"] . '"name="txtcri_' . $cont . '">', '<input type="textbox" class="caja_azul" size="6" value="' . $row["tiponivel_min"] . '"name="txtmin_' . $cont . '">', '<input type="textbox" class="caja_color" size="6" value="' . $row["tiponivel_max"] . '" name="txtmax_' . $cont . '">', number_format($row["prd_precio"], 2, '.', ''), "<a href=../edicion/borrarNivelArticulo.php?id=" . $row["prd_codigo"] . "&opc=" . $opc . " onclick='return borrar()'><img border='0' alt='borrar nivel stock articulo' title='Borrar Nivel stock articulo: " . $row["prd_codigo"] . "' src='../img/trash.png' width='23' height='23'></a>");
                    $cont++;
                }
                mysql_free_result($result);
                mysql_close($link);
                $_SESSION["pedido"] = 1;
                ?>           
                <tr><td colspan="9" class="texto8c">
                        <input type="hidden" name="contador" id="contador" value="<?php echo $cont ?>" />
                        <input type="hidden" name="opc" id="opc" value="<?php echo $opc ?>" />
                    </td>
                </tr>
                <tr>
                    <td colspan="9" class="texto6"><input type="button" name="capturar" id="capturar" value="Modificar" class="boton" >&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" value="Exportar Resultados a Excel" class="boton" onclick="window.location = '../exportar/exportaTipoNivelStock.php'"/>&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" value="Volver al Menu Principal" class="boton" onclick="window.location = '../principal.php'"/>&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" class="boton" <?php echo $btn ?>></td>
                </tr>
            </table></form>
        <a href="javascript:void(0);" id="scroll" title="Ir Arriba" style="display: none;">Arriba<span></span></a>
    </body>
</html>