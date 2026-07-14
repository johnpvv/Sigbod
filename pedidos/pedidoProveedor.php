<!DOCTYPE html>
<!-- fecha creacion 04/04/2022 hp
Sigbod John Vaccarella
-->
<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: ../index.php');
    exit();
}
$fechaact = date("Y-m-d");
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
        <title>SigBod - Pedidos a Proveedores</title>
        <script>
            $(document).ready(function () {
                $("#busqueda").focus();//hacemos focus al campo de busqueda               
                $("#buscar").click(function (e) {//comprobamos si se pulsa una tecla                    
                    var consulta = $("#busqueda").val().replace(/[`~!@#$%^&*()_|+\=?;:'",.<>\{\}\[\]\\\/]/gi, '').trim();//obtenemos el texto introducido en el campo de busqueda y elimina caracteres no correctos
                    var chkall = $("#checkall").prop("checked");
                    var chksaldo = $("#checksaldo").prop("checked");
                    var chkres = $("#checkres").prop("checked");
                    var largo = $("#busqueda").val().length;
                    var anio = $("#fecha").val();
                    var anio2 = $("#fecha2").val();
                    obtenerScroll();
                    if (largo == 0 || largo > 2) {
                        $.ajax({//hace la busqueda
                            type: "POST",
                            url: "pedidoProveedorAjax.php",
                            data: {consulta: consulta, chkall: chkall, chksaldo: chksaldo, anio: anio, anio2: anio2, chkres: chkres},
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
        <script type="text/javascript">
            function limpiaFecha() {
                $("#fecha").val("");
                $("#fecha2").val("");
                buscar.click();
            }
        </script> 
    </head>
    <body class="fondo" onload="window.setTimeout(function () {
                buscar.click()
            }, 300);"><!-- pausa de 300 ms para ejecutar funcion buscar, y posicionar el scroll donde se revisó ultima vez-->
        <table class="table2a">
            <tr>
                <td class="texto"><img class="imgico" alt='Pedido' title='Pedido' src='../img/prov.png'>&nbsp;&nbsp;&nbsp;GENERAR PEDIDO POR PROVEEDORES</td>
            </tr>
            <tr>
                <td class="texto5b">Ingrese Nombre a Buscar:&nbsp;
                    <input id="busqueda" name="busqueda" type="search" placeholder="escriba aqui..." autofocus size="25" class='caja' onkeypress="if (event.keyCode == 13)
                                buscar.click();"/>&nbsp;&nbsp;
                    <span class='caja_pading2'>Fecha OC:
                        <input type="date" class='caja_texto' name="fecha" id="fecha" step="1" min="" max="<?= $fechaact ?>" value=""/>&nbsp;Al&nbsp;
                        <input type="date" class='caja_texto' name="fecha2" id="fecha2" step="1" min="" max="<?= $fechaact ?>" value=""/>
                        <input type="button" id='limp' value="x" title="Limpiar fechas" class="boton1b" onclick="limpiaFecha();">&nbsp;&nbsp;
                        <input type="checkbox" name="checkall" id="checkall"/><span class="texto5"> Mostrar Todo</span>&nbsp;&nbsp;
                        <input type="checkbox" name="checksaldo" id="checksaldo"><span class="texto5"> Con Saldo</span>&nbsp;&nbsp;
                        <input type="checkbox" name="checkres" id="checkres"><span class="texto5"> Ocultar Recientes</span>
                    </span>&nbsp;&nbsp;
                    <button id="buscar" class="boton1">Buscar&nbsp;<img class="imgbtnpeq" alt="buscador" title="Buscar" src="../img/lupa.png"></button>
                </td>
            </tr>        
            <tr>
                <td class='texto5b'>
                    <button class="botonnormal" onclick="window.location = '../principal.php'">Volver al Menu Principal&nbsp;<img class="img" alt="principal" title="volver al menu principal" src="../img/casa.png"></button>&nbsp;&nbsp;&nbsp;&nbsp;
                    <button onclick="window.location = 'pedidoEnviado.php'" class="botonnormal">Ir a Pedidos Generados&nbsp;&nbsp;<img class='img' alt='pedidos' title='Ir a la pantalla de pedidos generados' src='../img/ped1.png'></button>&nbsp;&nbsp;&nbsp;&nbsp;
                    <button onclick="window.location = 'pedidoArticulo.php'" class="botonnormal">Ir a Pedidos por Artículo&nbsp;&nbsp;<img class='img' alt='pedidoArticulo' title='Ir a la pantalla de pedidos por articulo' src='../img/mano.png'></button>&nbsp;&nbsp;&nbsp;&nbsp;
                    <button type="button" onclick="window.location = 'pedidoProveedor.php'" class="botonnormal">Limpiar&nbsp;&nbsp;<img class='img' alt='limpiar' title='Limpiar Pantalla' src='../img/clean.png'></button>
                </td>
            </tr>
        </table>
        <div id="resultado"></div>
        <a href="javascript:void(0);" id="scroll" title="Ir Arriba" style="display: none;">Arriba<span></span></a>
    </body>
</html>