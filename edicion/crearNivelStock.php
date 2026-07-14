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
        <title>SigBod - Niveles de Stock</title>
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
        $sql = "SELECT `prd_codigo`, `prd_glosa`, `prd_unimed`,`prd_precio`,`unimed_nombre`,`stock_cantidad` FROM `productos`,`unimed`, stock WHERE `prd_unimed`= `unimed_id` AND prd_codigo=`stock_codigo` AND prd_fam='$fam' " . $varsql . " ";
        $result = mysql_query($sql, $link);
        $cuenta = mysql_num_rows($result);
        if ($cuenta == 0) {
            echo '<script>alert("Los Datos ingresados no son validos, Favor intente Nuevamente...");
				' . $opv . ';</script>';
            $_SESSION['query'] = "";
        } else {
            $_SESSION['query'] = $sql;
        }
        //echo $sql;
        ?>
        <form id="form" name="form" action="crearNivelStockAjax.php" id="form" method="post">
            <table class="table1">
                <tr>
                    <td colspan="9" class="texto"><center><img border='0' alt='Nivel Stock' title='NivelStock' src='../img/level.png' width='23' height='23'>&nbsp;CREAR NIVEL DE STOCK</center></td>
                </tr>
                <tr>                 
                    <td colspan="9" class="texto1b"><center>FAMILIA:  <b class="texto0b"> <?php echo $fam; ?></b></center></td>
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
                </tr>
                <?php
                while ($row = mysql_fetch_array($result)) {
                    $glosa = $row["prd_glosa"];
                    $unimed = $row["unimed_nombre"];
                    $stock = $row["stock_cantidad"];
                    $precio = $row["prd_precio"];
                }
                ?>
                <tr>
                    <td><b><?= $cod ?></b></td>
                    <td align='left'><?= $glosa ?></td>
                    <td><?= $unimed ?></td>
                    <td><b><?= $stock ?></b></td>
                    <td><input type="textbox" class="caja_rojo" size="6" value="" name="txtcri" id="txtcri"></td>
                    <td><input type="textbox" class="caja_azul" size="6" value="" name="txtmin" id="txtmin"></td>
                    <td><input type="textbox" class="caja_color" size="6" value="" name="txtmax" id="txtmax"></td>
                    <td><b>$&nbsp;<?= $precio ?></b></td>                   
                </tr>            
                <tr>
                    <td colspan="9" class="texto6">
                        <input type="button" name="capturar" id="capturar" value="Grabar" class="boton" >&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" value="Volver al Menu Principal" class="boton" onclick="window.location = '../principal.php'"/>&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" class="boton" <?php echo $btn ?>>
                        <input type="hidden" name="cod" value="<?=$cod?>">
                        <input type="hidden" name="fam" value="<?=$fam?>">
                    </td>
                </tr>
            </table>
        </form>
        <?php
        mysql_free_result($result);
        mysql_close($link);
        $_SESSION["pedido"] = 1;
        ?>
        <div id="resultado"></div>
        <a href="javascript:void(0);" id="scroll" title="Ir Arriba" style="display: none;">Arriba<span></span></a>
    </body>
</html>