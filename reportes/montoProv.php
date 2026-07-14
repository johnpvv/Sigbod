<!DOCTYPE html>
<!-- fecha creacion 14/01/2022 hp
Sigbod John Vaccarella
-->
<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: ../index.php');
    exit();
}
$fechaact = date("Y-m-d");
$fechaprim = date("Y-01-01");
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
        <title>SigBod - Montos Transados</title>
        <script>
            $(document).ready(function () {
                var consulta;
                $("#busqueda").focus();//hacemos focus al campo de busqueda               
                $("#buscar").click(function (e) {//comprobamos si se pulsa una tecla                    
                    consulta = $("#busqueda").val().replace(/[`~!@#$%^&*()_|+\=?;:'",.<>\{\}\[\]\\\/]/gi, '');//obtenemos el texto introducido en el campo de busqueda y elimina caracteres no correctos
                    largo = $("#busqueda").val().length;
                    var chkall = $("#checkall").prop("checked");
                    var anio = $("#fecha").val();
                    var anio2 = $("#fecha2").val();
                    if (largo === 0 || largo > 2) {
                        $.ajax({//hace la busqueda
                            type: "POST",
                            url: "montoProvAjax.php",
                            data: {saldo: consulta, chkall: chkall, anio: anio, anio2: anio2},
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
                    }
                });
            });
        </script>        
        <script type="text/javascript">
            function borrar() {
                if (confirm("¿Esta Seguro que quiere borrar la orden de compra seleccionada?"))
                    return true;
                else
                    return false;
            }
        </script>
    </head>
    <body class="fondo" onload="window.setTimeout(function () {
                buscar.click()
            }, 300);">        
        <table class="table2a">
            <tr>
                <td class="texto">Montos Transados por Proveedor</td>
            </tr>
            <tr>
                <td class="texto5b">
            <center>Ingrese RUT o Proveedor a Buscar:
                <span class="texto5"><input id="busqueda" name="txtcod" type="search" placeholder="escriba aqui..." autofocus size="30" maxlength="20" class='caja' onkeypress="if (event.keyCode == 13)
                            buscar.click();"/>&nbsp;&nbsp;&nbsp;&nbsp;<button id="buscar" class="boton1">Buscar&nbsp;<img class="imgbtnpeq" alt="buscador" title="Buscar" src="../img/lupa.png"></button>&nbsp;&nbsp;&nbsp;&nbsp
                </span>
                <span class='caja_pading2'>Fecha Órdenes de compra:
                    <input type="date" class='caja_texto' name="fecha" id="fecha" step="1" min="" max="<?= $fechaact ?>" value="<?= $fechaprim ?>"/>&nbsp;&nbsp;Al&nbsp;&nbsp;
                    <input type="date" class='caja_texto' name="fecha2" id="fecha2" step="1" min="" max="<?= $fechaact ?>" value="<?= $fechaact ?>"/>&nbsp;&nbsp;&nbsp;&nbsp;
                    <input type="checkbox" name="checkall" id="checkall" <?= $chk ?> /> Mostrar Todos&nbsp;&nbsp;&nbsp;&nbsp;
                </span>
            </center>
        </td>            
    </tr>        
    <tr>
        <td class="texto5"><center><button class="botonnormal" onclick="window.location = '../principal.php'">Volver al Menu Principal&nbsp;<img class="img" alt="principal" title="volver al menu principal" src="../img/casa.png"></button>&nbsp;&nbsp;&nbsp;&nbsp;
        <button onclick="window.location = '../exportar/exportaExcelMontoProv.php?anio=<?=$fechaprim?>&anio2=<?=$fechaact?>'" class="botonnormal">Exportar a Excel&nbsp;&nbsp;<img class='img' alt='exportar' title='Exportar a Excel los datos mostrados en pantalla' src='../img/excel3.png'></button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button type="button" onclick="window.location = 'montoProv.php'" class="botonnormal">Limpiar&nbsp;&nbsp;<img class='img' alt='limpiar' title='Limpiar Pantalla' src='../img/clean.png'></button></center></td>
</tr>
</table>
<div id="resultado"></div>
<a href="javascript:void(0);" id="scroll" title="Ir Arriba" style="display: none;">Arriba<span></span></a>
</body>
</html>