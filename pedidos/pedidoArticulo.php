<!DOCTYPE html>
<!-- fecha creacion 24-03-2019 mini hp
Sigbod John Vaccarella
-->
<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: ../index.php');
    exit();
}
if (!empty($_GET["op"])) {
    $op = $_GET["op"];
} else {
    $op = 0;
}
$cod = $_GET["codprd"];
$fechaact = date("Y-m-d");
if ($cod != "") {
    if ($op == 1) {
        $btnvolver = '<button class="botonnormal" onclick="javascript:window.close()">Salir&nbsp;<img class="img" alt="Salir" title="salir de la pantalla" src="../img/salir.png"></button>';
    } else {
        $btnvolver = "";
        echo "<script>alert('Error, los datos ingresados son Invalidos');history.go(-1)</script>";
    }
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
        <script src="../js/jquery.tablesorter.pager.js"></script>
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
        <title>SigBod - Pedidos Por Articulo</title>
        <script>
            $(document).ready(function () {
                $("#busqueda").focus();//hacemos focus al campo de busqueda               
                $("#buscar").click(function (e) {//comprobamos si se pulsa una tecla                    
                    var consulta = $("#busqueda").val().replace(/[`~!@#$%^&*()_|+\=?;:'",.<>\{\}\[\]\\\/]/gi, '').trim();//obtenemos el texto introducido en el campo de busqueda y elimina caracteres no correctos
                    var chkall = $("#checkall").prop("checked");
                    var chksaldo = $("#checksaldo").prop("checked");
                    var chkest = $("#chkest").prop("checked");
                    var chknul = $("#chknul").prop("checked");
                    var anio = $("#fecha").val();
                    var anio2 = $("#fecha2").val();
                    var opc =<?= $op ?>;
                    var largo = $("#busqueda").val().length;
                    obtenerScroll();
                    if (largo == 0 || largo > 2) {
                        $.ajax({//hace la busqueda
                            type: "POST",
                            url: "pedidoArticuloAjax.php",
                            data: {saldo: consulta, chkall: chkall, opc: opc, chksaldo: chksaldo, anio: anio, anio2: anio2, chkest: chkest, chknul: chknul},
                            beforeSend: function ()
                            {
                                $.blockUI({
                                    message: "<p class='centrar_cuadro_alertas'>Buscando, por favor espere...<img src='../img/ajax-loading.gif' width='270' height='180' /></p>"
                                });
                            },
                            error: function () {
                                alert("error peticion ajax");
                            },
                            success: function (data) {
                                $("#resultado").empty();
                                $("#resultado").append(data);
                                obtenerScroll();
                                $.unblockUI();
                            }
                        });
                    }
                });
            });
        </script>
        <script type="text/javascript">
            function limpiaFecha() {
                $("#fecha").val("");
                $("#fecha2").val("");
                buscar.click();
            }
        </script> 
        <script type="text/javascript">
            function buscaCod(x) {
                $("#busqueda").val("");
                $("#busqueda").val(x);
                buscar.click();
            }
        </script> 
        <script>
            jQuery(window).scroll(function () {
                var scroll = jQuery(this).scrollTop();
                document.cookie = "scrollp=" + scroll + ";max-age=1200;";
            });
            function obtenerScroll() {
                var scroll = document.cookie.replace(/(?:(?:^|.*;\s*)scrollp\s*\=\s*([^;]*).*$)|^.*$/, "$1");//obtener scroll desde cookie
                if (scroll != "" || scroll > 0) {
                    $("html, body").animate({scrollTop: scroll}, 200);
                }
            }
        </script>
    </head>
    <body class="fondo" onload="window.setTimeout(function () {
                buscar.click()
            }, 200);">
        <table class="table2a">
            <tr>
                <td class="texto"><img class="imgico" alt='Pedido' title='Pedido' src='../img/car.png'>&nbsp;&nbsp;&nbsp;GENERAR PEDIDO POR ARTÍCULOS</td>
            </tr>
            <tr>
                <td class="texto6">Ingrese Código, Glosa , OC, o Proveedor a Buscar:&nbsp;&nbsp;
                    <input id="busqueda" name="txtcod" type="search" placeholder="escriba aqui..." autofocus size="50" maxlength="20" class='caja' value="<?= $cod ?>" onkeypress="if (event.keyCode == 13)
                                buscar.click();"/>&nbsp;&nbsp;&nbsp;&nbsp;<button id="buscar" class="boton1">Buscar&nbsp;<img class="imgbtnpeq" alt="buscador" title="Buscar" src="../img/lupa.png"></button>&nbsp;&nbsp;&nbsp;&nbsp;<br>                
                    <span class='caja_pading2'>Fecha Orden de compra:
                        <input type="date" class='caja_texto' name="fecha" id="fecha" step="1" min="" max="<?= $fechaact ?>" value=""/>&nbsp;&nbsp;Al&nbsp;&nbsp;
                        <input type="date" class='caja_texto' name="fecha2" id="fecha2" step="1" min="" max="<?= $fechaact ?>" value=""/>
                        <input type="button" id='limp' value="x" title="Limpiar fechas" class="boton1b" onclick="limpiaFecha();">&nbsp;&nbsp;&nbsp;&nbsp;
                        <input type="checkbox" name="checkall" id="checkall" /><span class="texto5"> Mostrar Todos</span>&nbsp;&nbsp;
                        <input type="checkbox" name="checksaldo" id="checksaldo" onchange="buscar.click()" checked><span class="texto5"> Sólo Ordenes con Saldo</span>&nbsp;&nbsp;
                        <input type="checkbox" name="chkest" id="chkest"/><span class="texto5"> Items Anulados</span>&nbsp;&nbsp;
                        <input type="checkbox" name="chknul" id="chknul"/><span class="texto5"> Códigos no Asociados</span>&nbsp;
                    </span>
                </td>
            </tr>        
            <tr>
                <td class="texto5">
                    <button class="botonnormal" onclick="window.location = '../principal.php'">Volver al Menu Principal&nbsp;<img class="img" alt="principal" title="volver al menu principal" src="../img/casa.png"></button>&nbsp;&nbsp;&nbsp;&nbsp;
                    <button onclick="window.location = 'pedidoEnviado.php'" class="botonnormal">Ir a Pedidos Generados&nbsp;&nbsp;<img class='img' alt='pedidos' title='Ir a la pantalla de pedidos generados' src='../img/ped1.png'></button>&nbsp;&nbsp;&nbsp;&nbsp;
                    <button onclick="window.location = 'pedidoProveedor.php'" class="botonnormal">Ir a Pedidos por Proveedor&nbsp;&nbsp;<img class='img' alt='pedidoProveedor' title='Ir a la pantalla de pedidos por Proveedor' src='../img/oc.png'></button>&nbsp;&nbsp;&nbsp;&nbsp;
                    <button type="button" onclick="window.location = 'pedidoArticulo.php'" class="botonnormal">Limpiar&nbsp;&nbsp;<img class='img' alt='limpiar' title='Limpiar Pantalla' src='../img/clean.png'></button>&nbsp;&nbsp;&nbsp;&nbsp;
                    <?= $btnvolver ?>            
                </td>
            </tr>
        </table>
        <div id="resultado"></div>
        <a href="javascript:void(0);" id="scroll" title="Ir Arriba" style="display: none;">Arriba<span></span></a>
    </body>
</html>