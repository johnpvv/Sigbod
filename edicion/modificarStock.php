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
            function enviar() {
                if (confirm("¿Esta Seguro que los datos estan correctos?")) {
                    document.form.submit();
                } else {
                    return false;
                }
            }
        </script>
        <title>SigBod - Editar Stock</title>
    </head>
    <body>
        <?php
        include_once("../include/conn.php");
        session_start();
        if (!isset($_SESSION['usuario'])) {
            header('Location: ../index.php');
            exit();
        }
        if ($_SESSION['perfil'] > 1) {
            echo '<script>alert("Su Perfil no permite la visualizacion de esta pantalla, Favor contacte al Administrador.");history.back(-1);</script>';
            echo '<center><button class="boton" onclick="javascript:window.close()">Salir&nbsp;<img class="img" alt="Salir" title="salir de la pantalla de edicion" src="../img/salir.png"></button></center>';
            exit();
        }
        $link = Conectarse();
        $opc = $_GET['tipo']; //obtenemos la familia elegida elegido
        $cod = $_GET['id']; //obtenemos codigo si se ha enviado
        if ($cod == null) {
            $varsql = "";
            $btn = 'Volver atras';
            $opc = 0;
        } else {
            $varsql = " AND `prd_codigo`='$cod'";
            $btn = 'Salir';
            $opc = 1;
        }
        if ($opc == 0) {
            $opv = "history.back(-1)";
        } else {
            $opv = "window.close()";
        }
        $sql = "SELECT `prd_codigo`, `prd_glosa`, `prd_unimed`,`prd_precio`,`unimed_nombre`,`stock_cantidad` FROM `productos`,`unimed`, stock WHERE `prd_unimed`= `unimed_id` AND prd_codigo=`stock_codigo` " . $varsql;
        $result = mysql_query($sql, $link);
        $cuenta = mysql_num_rows($result);
        if ($cuenta == 0) {
            echo '<script>alert("Los Datos ingresados no son validos, Favor intente Nuevamente...");
				' . $opv . ';</script>';
            $_SESSION['query'] = "";
            exit();
        } else {
            $_SESSION['query'] = $sql;
        }
        ?>
    <center><form id="form" name="form" action="../edicion/modificarStockBD.php" method="post" onsubmit= "return enviar()">
            <table class="table4a">
                <tr>
                    <td colspan="6" class="texto">EDITAR STOCK</td>
                </tr>                
                <tr class="texto5">
                    <th width="50">Código Interno</th>
                    <th width="100">Glosa</th>
                    <th width="50">Unidad de Medida</th>
                    <th width="50">Stock Actual</th>
                    <th width="70">Stock Modificado</th>
                    <th width="60">Precio Unitario</th>
                </tr>
                <?php
                $cont = 0;
                while ($row = mysql_fetch_array($result)) {
                    printf("<tr><td><b>%s</b></td><td align='left'>%s</td><td>%s</td><td class='color_amarillo'>%s</td><td>%s</td><td>%s</td></tr>", '<input type="textbox" class="caja2" readonly size="7" name="txtcod_' . $cont . '" value="' . $row["prd_codigo"] . '"/>', $row["prd_glosa"], $row["unimed_nombre"], '<input type="textbox" class="caja2" readonly size="5" name="txtant_' . $cont . '" value="' . $row["stock_cantidad"] . '"/>','<input type="textbox" class="caja_rojo" size="7" name="txtstock_' . $cont . '" value="" required autofocus>',"$". number_format($row["prd_precio"], 2, '.', ','));
                    $cont++;
                }
                mysql_free_result($result);
                mysql_close($link);
                $_SESSION["pedido"] = 1;
                ?>           
                <tr><td colspan="6" class="texto8c">
                        <input type="hidden" name="contador" id="contador" value="<?php echo $cont ?>" />
                        <input type="hidden" name="opc" id="opc" value="<?php echo $opc ?>" />
                    </td>
                </tr>
                <tr>
                    <td colspan="6" class="texto6">
                    <center><button type="submit" class="boton">Modificar&nbsp;&nbsp;<img class='img' alt='modificar' title='Modificar' src='../img/ped1.png'></button>&nbsp;&nbsp;&nbsp;&nbsp;
                        <button class="boton" type="button" onclick="<?php echo $opv ?>"><?php echo $btn ?>&nbsp;<img class="img" alt="Salir" title="salir de la pantalla de edicion" src="../img/salir.png"></button></center></td>
                </tr>
            </table></form></center>
        <a href="javascript:void(0);" id="scroll" title="Ir Arriba" style="display: none;">Arriba<span></span></a>
    </body>
</html>