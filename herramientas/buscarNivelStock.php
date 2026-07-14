<!DOCTYPE html>
<!-- fecha creacion 05/06/2020 hp
Sigbod John Vaccarella
-->
<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: ../index.php');
    exit();
}
$_SESSION['query'] = "";
if(!empty($_GET["op"])){
    $op = $_GET["op"];
}else{
    $op=0;
}
$cod = $_GET["cod"];
if ($cod != "") {
    if ($op == 1) {
        $dir = "buscar.click()";
        $btnvolver = '<button class="botonnormal" onclick="javascript:window.close()">Salir&nbsp;<img class="img" alt="Salir" title="salir de la pantalla" src="../img/salir.png"></button>';
    } else {
        $dir = "";
        $btnvolver = "";
        echo "<script>alert('Error, los datos ingresados son Invalidos');history.go(-1)</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <link rel="stylesheet" href="../css/tabla.css" />
        <link rel="stylesheet" href="../css/estilos.css" />
        <link rel="shortcut icon" href="../img/favicon.ico" />          
        <script src="../js/jquery-2.2.4.js" type="text/javascript"></script>
        <script src="../js/jquery.blockUI.js" type="text/javascript"></script>
        <script src="../js/tablesorter.min.js" type="text/javascript"></script>
        <script type="text/javascript">
            $(document).ready(function () {
                $('#capturar').hide();
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
            function borrar() {
                if (confirm("¿Esta Seguro que quiere borrar este Producto del Nivel de Stock?"))
                    return true;
                else
                    return false;
            }
        </script>
        <script type="text/javascript">
            $(document).ready(function () {
                var consulta;
                $("#busqueda").focus(); //hacemos focus al campo de busqueda               
                $("#buscar").click(function (e) {//comprobamos si se pulsa una tecla                    
                    consulta = $("#busqueda").val().replace(/[`~!@#$%^&*()_|+\=?;:'",.<>\{\}\[\]\\]/gi, '').trim(); //obtenemos el texto introducido en el campo de busqueda y elimina caracteres no correctos
                    var chkall = $("#checkall").prop("checked");
                    var tiposto = $("#tiposto").val();
                    largo = $("#busqueda").val().length;
                    if (largo === 0 || largo >= 1) {
                        $.ajax({//hace la busqueda
                            type: "POST",
                            url: "buscarNivelStockAjax.php",
                            data: {saldo: consulta, chkall: chkall, tiposto: tiposto},
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
            function actualizar(x) {
                var flag = 0;
                var cri = $("#txtcri_" + x).val().trim();
                var min = $("#txtmin_" + x).val().trim();
                var max = $("#txtmax_" + x).val().trim();
                var cod = $("#txtcod_" + x).val();
                if (cri == "" || isNaN(cri) || cri == 0) {
                    alert("Error...\nEl Stock Critico no puede ser cero, y debe ser un numero entero");
                    $("#txtcri_" + x).val("");
                    flag = 0;
                    exit;
                } else {
                    flag = 1;
                }
                if (min == "" || isNaN(min) || min == 0) {
                    alert("Error...\nEl Stock Minimo no puede ser cero, y debe ser un numero entero");
                    $("#txtmin_" + x).val("");
                    flag = 0;
                    exit;
                } else {
                    flag = 1;
                }
                if (max == "" || isNaN(max) || max == 0) {
                    alert("Error...\nEl Stock Maximo no puede ser cero,  y debe ser un numero entero");
                    $("#txtmax_" + x).val("");
                    flag = 0;
                    exit;
                } else {
                    flag = 1;
                }
                if (flag = !0) {
                    $.ajax({//hace la busqueda
                        type: "POST",
                        url: "../edicion/buscarNivelStockAjaxBD.php",
                        data: {cri: cri, min: min, max: max, cod: cod},
                        beforeSend: function () {//imagen de carga                            
                            $("#" + x).html("<img src='../img/loader2.gif' width='20' height='20' />");//imagen de carga
                        },
                        error: function () {
                            alert("error peticion ajax");
                        },
                        success: function (data) {
                            $("#" + x).empty();
                            $("#" + x).append(data);
                        }
                    });
                }
            }
        </script>          
        <script type="text/javascript">
            function borrar() {
                if (confirm("¿Esta Seguro que quiere borrar el nivel de Stock Seleccionado?"))
                    return true;
                else
                    return false;
            }
        </script>
        <script type="text/javascript">
            function mostrar() {
                var tiposto = $("#tiposto").val();
                if (tiposto > 0) {
                    $('#capturar').show();
                    buscar.click();
                } else {
                    $('#capturar').hide();
                    buscar.click();
                }
            }
        </script>
        <title>SigBod - Editar Niveles de Stock</title>
    </head>
    <body class="fondo" onload="<?= $dir ?>">        
        <table class="table2a">
            <tr>
                <td class="texto"><img border='0' alt='Nivel Stock' title='NivelStock' src='../img/level.png' width='23' height='23'>&nbsp;EDITAR NIVELES DE STOCK</td>
            </tr>
            <tr>
                <td class="texto1">
            <center>Ingrese Texto a Buscar:
                <span class="texto5"><input id="busqueda" name="txtcod" type="search" placeholder="escriba aqui..." size="30" maxlength="20" class='caja' value="<?=$cod?>" onkeypress="if (event.keyCode == 13)
                    buscar.click();"/>&nbsp;&nbsp;&nbsp;&nbsp;<button id="buscar" class="boton1">Buscar&nbsp;<img class="imgbtnpeq" alt="buscador" title="Buscar" src="../img/lupa.png"></button>&nbsp;&nbsp;&nbsp;&nbsp;

                </span>
                <span class='caja_pading2'>Tipo de Stock:
                    &nbsp;
                    <select name="tiposto" class="texto5" id="tiposto" onchange="mostrar()">
                        <option value='0' selected>‌Elija una Opción</option>
                        <option value='1'>Minimo</option>
                        <option value='2'>Critico</option>
                        <option value='3'>Maximo</option>
                    </select>&nbsp;&nbsp;&nbsp;&nbsp;                    
                    <input type="checkbox" name="checkall" id="checkall" <?= $chk ?> /> Mostrar Todos

                </span>
            </center>
        </td>            
    </tr>        
    <tr>
        <td class="texto5"><center><button class="botonnormal" onclick="window.location = '../principal.php'">Volver al Menu Principal&nbsp;<img class="img" alt="principal" title="volver al menu principal" src="../img/casa.png"></button>&nbsp;&nbsp;&nbsp;&nbsp;
        <button onclick="window.location = '../exportar/exportaTipoNivelStock.php'" class="botonnormal">Exportar a Excel&nbsp;&nbsp;<img class='img' alt='exportar' title='Exportar a Excel los datos mostrados en pantalla' src='../img/excel3.png'></button>&nbsp;&nbsp;&nbsp;&nbsp;<?= $btnvolver ?></center></td>
</tr>
</table>
<div id="resultado"></div>
<a href="javascript:void(0);" id="scroll" title="Ir Arriba" style="display: none;">Arriba<span></span></a>
</body>
</html>