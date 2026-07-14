<!DOCTYPE html>
<!--
Sigbod John Vaccarella fecha creacion: 27/06/2019 hp grande 
-->
<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: ../index.php');
    exit();
}
$_SESSION['query'] = "";
$fechaact = date("Y-m-d");
$id = $_GET["op"];
if ($id == 1) {
    $chk = "checked";
    $dir = "buscar.click()";
} else {
    $chk = "";
    $dir = "";
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
        <title>SigBod - Stock</title> 
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
                var consulta;
                $("#busqueda").focus();//hacemos focus al campo de busqueda                
                $("#buscar").click(function (e) {//comprobamos si se pulsa una tecla
                    consulta = $("#busqueda").val().replace(/[`~!@#$%^&*()_|+\=?;:'",.<>\{\}\[\]\\\/]/gi, '');//obtenemos el texto introducido en el campo de busqueda y elimina caracteres no correctos
                    var check = $("#check").prop("checked");
                    var chkall = $("#checkall").prop("checked");
                    var chkver = $("#chkver").prop("checked");
                    var anio = $("#fecha").val();
                    var anio2 = $("#fecha2").val();
                    $.ajax({//hace la busqueda
                        type: "POST",
                        url: "buscarStockAjax.php",
                        data: {b: consulta, chk: check, chkall: chkall, chkver: chkver, anio: anio, anio2: anio2},
                        beforeSend: function () {
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
    <body class="fondo" onload="<?= $dir ?>">        
        <table class="table2a">
            <tr>
                <td class="texto" >STOCK EN SISTEMA</td>
            </tr>
            <tr>
                <td class="texto5" style="text-align: center;">
                    <span class="texto5">Ingrese Texto a Buscar:
                        <input id="busqueda" name="txtcod" type="search" placeholder="escriba aqui..." autofocus size="25" class='caja' onkeypress="if (event.keyCode == 13)buscar.click();"/>&nbsp;
                        <button id="buscar" class="boton1">Buscar&nbsp;<img class="imgbtnpeq" alt="buscador" title="Buscar" src="../img/lupa.png"></button>&nbsp;&nbsp;
                        <input type="checkbox" name="checkall" id="checkall" <?= $chk ?> />&nbsp;Mostrar Todos
                        &nbsp;<input type="checkbox" name="check" id="check" />&nbsp;Stock mayor a cero
                        &nbsp;<input type="checkbox" name="chkver" id="chkver" <?= $chk ?>/>&nbsp;No Asociados
                    </span>
                    <span class='caja_pading2'>Modificado:
                        <input type="date" class='caja_texto' name="fecha" id="fecha" step="1" min="" max="<?= $fechaact ?>" value=""/>&nbsp;Al&nbsp;
                        <input type="date" class='caja_texto' name="fecha2" id="fecha2" step="1" min="" max="<?= $fechaact ?>" value=""/>&nbsp;
                        <input type="button" id='limp' value="x" title="Limpiar fechas" class="boton1b" onclick="limpiaFecha();">&nbsp;
                    </span>
                </td>
            </tr>        
            <tr>
                <td class="texto5">
                    <button class="botonnormal" onclick="javascript:history.back(-1)">Volver&nbsp;&nbsp;<img class='img' alt='volver' title='Volver a pantalla anterior' src='../img/undo.png'></button>&nbsp;&nbsp;&nbsp;&nbsp;<button onclick="window.location = '../exportar/exportaExcelStock.php'" class="botonnormal">Exportar a Excel&nbsp;&nbsp;<img class='img' alt='exportar' title='Exportar a Excel los datos mostrados en pantalla' src='../img/excel3.png'></button>&nbsp;&nbsp;&nbsp;&nbsp;<button id="capturar" class="botonnormal" onclick="window.open('../reportes/stockPDF.php', '_blank', 'scrollbars=yes,statusbar=no,resizable=no,width=860,height=620 left=200,top=30')">Generar Informe en PDF&nbsp;&nbsp;<img class="img" alt="pdf" title="Exporta los datos actuales a formato PDF" src="../img/pdf1.png"></button>&nbsp;&nbsp;&nbsp;&nbsp;<button id="rand" class="botonnormal" onclick="window.open('../reportes/stockPDF.php?op=1', '_blank', 'scrollbars=yes,statusbar=no,resizable=no,width=860,height=620 left=200,top=30')">Generar Aleatorio PDF&nbsp;&nbsp;<img class="img" alt="pdf" title="genera Selectivo PDF" src="../img/rand.png"></button>
                </td>
            </tr>
        </table>
        <div id="resultado"></div>
        <a href="javascript:void(0);" id="scroll" title="Ir Arriba" style="display: none;">Arriba<span></span></a>
    </body>
</html>