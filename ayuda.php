<!DOCTYPE html>
<!--
Sigbod John Vaccarella
-->
<html>
    <head>
        <link rel="stylesheet" href="css/estilos.css" />
        <link rel="stylesheet" href="css/tabla.css" />
        <link rel="shortcut icon" href="img/favicon.ico" />
        <script src="js/jquery-2.2.4.js" type="text/javascript"></script>
        <script src="js/tablesorter.min.js" type="text/javascript"></script>
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
        <script>
            $(document).ready(function () {
                $("#logo").hide();
                $("#logo").fadeIn(1000);
                $.ajax({//hace la busqueda
                    type: "POST",
                    url: "ayudaAjax.php",
                    beforeSend: function () {//imagen de carga                                  
                        $("#resultado").html("<img src='img/load.gif' width='40' height='40'> Buscando y Cargando Datos...");
                    },
                    error: function () {
                        alert("error peticion ajax");
                    },
                    success: function (data) {
                        $("#resultado").empty();
                        $("#resultado").html(data);
                        $("#resultado").hide();
                        $("#resultado").fadeIn();
                    }
                });
            });
        </script> 
        <title>SigBod - Ayuda</title>       
    </head>
    <body class="fondo">
        <table class="table2a">
            <tr>
                <td colspan="2" class="texto"><img class="imgico" alt='Ayuda' title='Ayuda' src='img/ayuda.png'>&nbsp;&nbsp;&nbsp;Ayuda de SigBod</td>
            </tr>
            <tr>
                <td><img src="img/logo.jpg" alt="Sigbod" class="caja" id="logo"/></td>
                <td class="texto6">
                    <strong>SigBod</strong> es un sistema experimental que busca mejorar la             
                    gestión de pedidos a proveedores de una instituci&oacute;n, optimizando dicho proceso 
                    y permitiendo llevar un registro detallado de las fechas, cantidades y proveedores
                    a los cuales se requiere enviar solicitudes de despacho. <strong>SigBod</strong> est&aacute; en constante revisi&oacute;n y 
                    atravesando procesos de mejoras, por lo que pretende ser al corto y mediano plazo un sistema que 
                    aporte un valor agregado a las labores de bodega y gesti&oacute;n de abastecimientos.
                    <br><br><br><br>
                    <p style="text-align: right;font-size: 13px;margin-bottom: 1px;">Creado por John Vaccarella Valenzuela. © Santiago, 11 de febrero de 2019.</p>
                </td>
            </tr>
            <tr>
                <td class="texto0b" colspan="2">Documentos y Manuales para el Usuario</td>

            </tr>
            <tr>
                <td class="caja_color" colspan="2"><div id="resultado">2</div></td>                               
            </tr>
            <tr>
                <td colspan="2"><button class="boton" type="button" onclick="javascript:history.back(-1)">Volver&nbsp;&nbsp;<img class='img' alt='volver' title='Volver a pantalla anterior' src='img/undo.png'></button></td>
            </tr>
        </table>
        <a href="javascript:void(0);" id="scroll" title="Ir Arriba" style="display: none;">Arriba<span></span></a>
    </body>
</html>