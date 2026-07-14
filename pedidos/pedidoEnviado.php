<!DOCTYPE html>
<!-- fecha creacion 20-03-2019 hp grande
Sigbod John Vaccarella
-->
<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: ../index.php');
    exit();
}
$_SESSION['query'] = "";
$fechaact = date("Y-m-d");
$fechaini = date("Y-01-01");
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
        <title>SigBod - Pedidos Enviados</title>
        <script>
            $(document).ready(function () {
                var consulta;
                $("#busqueda").focus();//hacemos focus al campo de busqueda                
                $("#buscar").click(function (e) {//comprobamos si se pulsa//hacemos focus al campo de busqueda una tecla                    
                    consulta = $("#busqueda").val().replace(/[`~!@#$%^&*()_|+\=?;:'",.<>\{\}\[\]\\\/]/gi, '');//obtenemos el texto introducido en el campo de busqueda y elimina caracteres no correctos                   
                    var chkall = $("#checkall").prop("checked");
                    var chkval = $("#checkval").prop("checked");
                    var anio = $("#fecha").val();
                    var anio2 = $("#fecha2").val();
                    $.ajax({//hace la busqueda
                        type: "POST",
                        url: "pedidoEnviadoAjax.php",
                        data: {b: consulta, chkall: chkall, chkval: chkval, anio: anio, anio2: anio2},
                        beforeSend: function () {//imagen de carga                            
                            $.blockUI({
                                message: "<p class='centrar_cuadro_alertas'>Buscando, por favor espere...<img src='../img/loading.gif' width='200' height='150' /></p>"
                            });
                        },
                        error: function () {
                            alert("error peticion ajax");
                        },
                        success: function (data) {
                            $("#resultado").empty();
                            $("#resultado").append(data);
                            $.unblockUI();
                        }
                    });
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
    </head>
    <body class="fondo">        
        <table class="table2a">
            <tr>
                <td class="texto">Listado de Pedidos Generados</td>
            </tr>
            <tr>
                <td class="texto5b">Ingrese Nombre, RUT o Mov. a Buscar:&nbsp;
                    <span class="texto5b"><input id="busqueda" name="txtbusqueda" type="search" placeholder="escriba aqui..." autofocus size="25" class='caja' onkeypress="if (event.keyCode == 13)
                            buscar.click();"/>&nbsp;&nbsp;&nbsp;<button id="buscar" class="boton1">Buscar&nbsp;<img class="imgbtnpeq" alt="buscador" title="Buscar" src="../img/lupa.png"></button>&nbsp;&nbsp;
                        <span class='caja_pading2'>Fecha Mov:
                            <input type="date" class='caja_texto' name="fecha" id="fecha" step="1" min="" max="<?= $fechaact ?>" value="<?= $fechaini ?>"/>&nbsp;Al&nbsp;
                            <input type="date" class='caja_texto' name="fecha2" id="fecha2" step="1" min="" max="<?= $fechaact ?>" value="<?= $fechaact ?>"/>
                            <input type="button" id='limp' value="x" title="Limpiar fechas" class="boton1b" onclick="limpiaFecha();">&nbsp;&nbsp;
                            <input type="checkbox" name="checkall" id="checkall" /> Mostrar Todos&nbsp;&nbsp;
                            <input type="checkbox" name="checkval" id="checkval" /> Solo Vigentes
                        </span>
                    </span>
                </td>
            </tr>        
            <tr>
                <td class="texto5">
                    <button class="botonnormal" onclick="window.location = '../principal.php'">Volver al Menu Principal&nbsp;<img class="img" alt="principal" title="volver al menu principal" src="../img/casa.png"></button>&nbsp;&nbsp;&nbsp;&nbsp;
                    <button onclick="window.location = '../exportar/exportaExcelPedidos.php'" class="botonnormal">Exportar a Excel&nbsp;&nbsp;<img class='img' alt='exportar' title='Exportar a Excel los datos mostrados en pantalla' src='../img/excel3.png'></button>&nbsp;&nbsp;&nbsp;&nbsp;
                    <button type="button" onclick="window.location = 'pedidoEnviado.php'" class="botonnormal">Limpiar&nbsp;&nbsp;<img class='img' alt='limpiar' title='Limpiar Pantalla' src='../img/clean.png'></button>
                </td>
            </tr>
        </table>
        <div id="resultado"></div>
        <a href="javascript:void(0);" id="scroll" title="Ir Arriba" style="display: none;">Arriba<span></span></a>
    </body>
</html>