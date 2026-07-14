<!DOCTYPE html>
<!-- fecha creacion 30/07/2021 hp
Sigbod John Vaccarella
-->
<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: ../index.php');
    exit();
}
if (!empty($_GET["op"])) {
    $optpag = '<button type="button" class="botonnormal" onclick="javascript:history.back(-1)">Volver&nbsp;<img class="img" alt="Volver" title="Volver" src="../img/undo.png"></button>';
} else {
    $optpag = "";
}
if (!empty($_GET["rut"])) {
    $rut = $_GET["rut"];
    $saldo = " selected";
} else {
    $rut = "";
    $saldo = "";
}
if (!empty($_GET["fini"])) {
    $fini = $_GET["fini"];
} else {
    $fini = "";
}
if (!empty($_GET["ffin"])) {
    $ffin = $_GET["ffin"];
} else {
    $ffin = "";
}
if ($rut != "") {
    $dir = "buscar.click()";
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
        <title>SigBod - Estado Orden de Compra</title>
        <script>
            $(document).ready(function () {
                var consulta;
                $("#busqueda").focus();//hacemos focus al campo de busqueda               
                $("#buscar").click(function (e) {//comprobamos si se pulsa una tecla                    
                    consulta = $("#busqueda").val().replace(/[`~!@#$%^&*()_|+\=?;:'",.<>\{\}\[\]\\\/]/gi, '');//obtenemos el texto introducido en el campo de busqueda y elimina caracteres no correctos
                    var largo = $("#busqueda").val().length;
                    var chkall = $("#chkall").val();
                    var anio = $("#fecha").val();
                    var anio2 = $("#fecha2").val();
                    if (largo === 0 || largo > 2) {
                        $.ajax({//hace la busqueda
                            type: "POST",
                            url: "buscarOcSaldoAjax.php",
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
            }, 300);"><!-- pausa de 300 ms para ejecutar funcion buscar-->
        <table class="table2a">
            <tr>
                <td class="texto">Estado Ordenes de Compra</td>
            </tr>
            <tr>
                <td class="texto5b">Ingrese OC, RUT o Proveedor a Buscar:
                    <span class="texto5"><input id="busqueda" name="txtcod" type="search" placeholder="escriba aqui..." autofocus size="25" maxlength="20" class='caja' value="<?= $rut ?>" onkeypress="if (event.keyCode == 13)
                                buscar.click();"/>&nbsp;&nbsp;&nbsp;&nbsp;<button id="buscar" class="boton1">Buscar&nbsp;<img class="imgbtnpeq" alt="buscador" title="Buscar" src="../img/lupa.png"></button>&nbsp;&nbsp;
                    </span>
                    <span class='caja_pading2'>Fecha OC:
                        <input type="date" class='caja_texto' name="fecha" id="fecha" step="1" min="" max="<?= $fechaact ?>" value="<?= $fini ?>"/>&nbsp;Al&nbsp;
                        <input type="date" class='caja_texto' name="fecha2" id="fecha2" step="1" min="" max="<?= $fechaact ?>" value="<?= $ffin ?>"/>&nbsp;&nbsp;
                        &nbsp;&nbsp;Estado:
                        <select name="chkall" class="caja_texto" id="chkall">
                            <option value='0'>‌--Elija Opcion--</option>
                            <option value='1'>‌OC Completa</option>
                            <option value='2'>OC Parcial</option>
                            <option value='3'>‌OC Sin Recepcion</option>
                            <option value='4' <?= $saldo ?>>‌Todos</option>
                        </select>
                    </span>
                </td>            
            </tr>        
            <tr>
                <td class="texto5">
                    <button class="botonnormal" onclick="window.location = '../principal.php'">Volver al Menu Principal&nbsp;<img class="img" alt="principal" title="volver al menu principal" src="../img/casa.png"></button>&nbsp;&nbsp;&nbsp;&nbsp;
                    <button onclick="window.location = '../exportar/exportaExcelOc.php'" class="botonnormal">Exportar a Excel&nbsp;&nbsp;<img class='img' alt='exportar' title='Exportar a Excel los datos mostrados en pantalla' src='../img/excel3.png'></button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?= $optpag ?></td>
            </tr>
        </table>
        <div id="resultado"></div>
        <a href="javascript:void(0);" id="scroll" title="Ir Arriba" style="display: none;">Arriba<span></span></a>
    </body>
</html>