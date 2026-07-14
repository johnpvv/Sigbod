<!DOCTYPE html>
<!-- fecha creacion: 05/03/2019
Sigbod John Vaccarella
-->
<?php
$rutpv = $_GET['id']; //obtenemos el rut del proveedor elegido
$oc = $_GET['oc']; //obtenemos orden de compra si se ha enviado
$fechaact = date("Y-m-d");
$fini = $_GET['fini'];
$ffin = $_GET['ffin'];
$op = $_GET["op"];
if ($op == 1) {
    $btnsalir = '<button class="boton" onclick="javascript:window.close()">Salir&nbsp;<img class="img" alt="Salir" title="salir de la pantalla" src="../img/salir.png"></button>';
} else {
    $btnsalir = "";
    $op = 0;
}
?>
<html>
    <head>
        <link rel="stylesheet" href="../css/tabla.css" />
        <link rel="stylesheet" href="../css/estilos.css" />
        <script src="../js/jquery-2.2.4.js" type="text/javascript"></script>
        <script src="../js/jquery.blockUI.js" type="text/javascript"></script>
        <script src="../js/tablesorter.min.js" type="text/javascript"></script>
        <link rel="shortcut icon" href="../img/favicon.ico" />
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
                    var suma = 0;
                    $("input[type=checkbox]:checked").each(function () {// para cada checkbox "chequeado" suma=suma +1;
                        suma = suma + 1;
                    });
                    if (document.getElementById('chktodo').checked) {
                        suma = suma - 1;
                    }
                    if (suma === 0) {
                        alert("Error:\nDebe seleccionar al menos un elemento para continuar...");
                        $("html, body").animate({scrollTop: 0}, "slow");//subir el scroll al principio de la pagina
                    } else {
                        if (confirm("¿Esta Seguro que los datos estan correctos?")) {
                            document.form.submit();
                        } else {
                            return 0;
                        }
                    }
                });
            });
        </script>
        <script>
            function selectall(form) {
                var formulario = eval(form);
                var x = 0;
                for (var i = 0, len = formulario.elements.length; i < len; i++) {
                    if (formulario.elements[i].type === "checkbox") {
                        formulario.elements[i].checked = formulario.elements[0].checked;
                        ++x;
                    }
                }
                x = x - 1;
                if (document.getElementById('chktodo').checked) {//obtener si el check nombre chktodo esta marcado o no
                    $("html, body").animate({scrollTop: $(document).height()}, "slow");//bajar el scroll al final de la pagina
                    document.getElementById('obs').focus();
                    if (x === 0) {
                        alert("Error, No Hay Elementos para Seleccionar");
                    } else {
                        alert("Se han Seleccionado un Total de: " + x + " Productos");
                    }
                } else {
                    $("html, body").animate({scrollTop: 0}, "slow");//subir el scroll al principio de la pagina                    
                }
            }
        </script>
        <script type="text/javascript">
            $(document).ready(function () {
                var consulta;
                $("#busqueda").focus(); //hacemos focus al campo de busqueda               
                $("#buscar").click(function (e) {//comprobamos si se pulsa una tecla                    
                    consulta = $("#busqueda").val().replace(/[`~!@#$%^&*()_|+\=?;:'",.<>\{\}\[\]\\]/gi, '').trim(); //obtenemos el texto introducido en el campo de busqueda y elimina caracteres no correctos
                    var oc = $("#oc").val();
                    var rut = $("#rut").val();
                    var op = <?= $op ?>;
                    var tiposto = $("#tiposto").val();
                    var anio = $("#fecha").val();
                    var anio2 = $("#fecha2").val();
                    largo = $("#busqueda").val().length;
                    if (largo === 0 || largo >= 1) {
                        $.ajax({//hace la busqueda
                            type: "POST",
                            url: "crearPedidoAjax.php",
                            data: {saldo: consulta, tiposto: tiposto, oc: oc, rut: rut, anio: anio, anio2: anio2, op: op},
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
            function activaBtn(num) {
                if (num === 0) {
                    $("#capturar").attr("disabled", true);
                    $("#capturar").css("background", "black");
                    $("#capturar").css('cursor', 'no-drop');//cursor mause error
                } else {
                    $("#capturar").attr("disabled", false);
                    $("#capturar").css("background", "");
                    $("#capturar").css('cursor', 'pointer');
                }
            }
            function limpiaFecha() {
                $("#fecha").val("");
                $("#fecha2").val("");
                buscar.click();
            }
        </script>  
        <script type="text/javascript">
            function validarMax(x) {
                var sp = x.replace("caja_", "sp_");
                var ped = $("#" + x).val();
                var max = Number($("#" + sp).text());
                if (ped > max) {
                    alert("Error, el Valor ingresado, es mayor a lo Pendiente.\nSe restablece el valor Máximo Permitido.");
                    $("#" + x).val(max);
                }
            }
        </script> 
        <script type="text/javascript">
            function selDatos(x) {
                var dato = x.replace("check_", "caja_");
                $("#" + dato).select();
                $("#" + dato).focus();
            }
        </script>
        <title>SigBod - Generar Pedidos</title>
    </head>
    <body class="fondo"> 
    <body class="fondo" onload="buscar.click()">        
        <table class="table2a">
            <tr>
                <td class="texto"><img class="imgico" alt='Pedido' title='Pedido' src='../img/ped1.png'>&nbsp;&nbsp;&nbsp;GENERAR PEDIDO A PROVEEDOR</td>
            </tr>
            <tr>
                <td class="texto6">
            <center>Ingrese Texto a Buscar:
                <span class="texto5"><input id="busqueda" name="txtcod" type="search" placeholder="escriba aqui..." size="25" maxlength="20" class='caja' value="<?= $cod ?>" onkeypress="if (event.keyCode == 13)
                            buscar.click();"/>&nbsp;&nbsp;<button id="buscar" class="boton1">Buscar&nbsp;<img class="imgbtnpeq" alt="buscador" title="Buscar" src="../img/lupa.png"></button>&nbsp;&nbsp;&nbsp;
                </span>
                <span class='caja_pading2'>Tipo Stock:
                    &nbsp;
                    <select name="tiposto" class="texto5" id="tiposto" onchange="buscar.click()">
                        <option value='0' selected>‌Todos</option>
                        <option value='1'>Cero</option>
                        <option value='2'>Mayor a Cero</option>
                        <option value='3'>Producto Destacado</option>
                        <option value='4'>Stock Critico</option>
                    </select>&nbsp;&nbsp;
                    Fecha OC:
                    <input type="date" class='caja_texto' name="fecha" id="fecha" step="1" min="" max="<?= $fechaact ?>" value="<?= $fini ?>" onblur="buscar.click();"/>&nbsp;&nbsp;Al&nbsp;&nbsp;
                    <input type="date" class='caja_texto' name="fecha2" id="fecha2" step="1" min="" max="<?= $fechaact ?>" value="<?= $ffin ?>" onblur="buscar.click();"/>&nbsp;
                    <input type="button" id='limp' value="x" title="Limpiar fechas" class="boton1b" onclick="limpiaFecha();">
                    <input type="hidden" id="rut" value="<?= $rutpv ?>" />
                    <input type="hidden" id="oc" value="<?= $oc ?>" />                    
                </span>
            </center>
        </td>            
    </tr>        
</table>
<div id="resultado"></div>
<center><button class="boton" type="button" onclick="javascript:history.back()">Volver&nbsp;&nbsp;<img class='img' alt='volver' title='Volver a pantalla anterior' src='../img/undo.png'></button>&nbsp;&nbsp;&nbsp;&nbsp;<button class="boton" type="button" onclick="window.location = '../principal.php'">Volver al Menu Principal&nbsp;<img class="img" alt="principal" title="volver al menu principal" src="../img/casa.png"></button>&nbsp;&nbsp;&nbsp;&nbsp;<button class="boton" name="capturar" id="capturar" type="button">Generar&nbsp;<img class="img" alt="generar" title="Generar Pedido" src="../img/ped.png"></button>&nbsp;&nbsp;&nbsp;&nbsp;<?= $btnsalir ?></center>

<a href="javascript:void(0);" id="scroll" title="Ir Arriba" style="display: none;">Arriba<span></span></a>
</body>
</html>