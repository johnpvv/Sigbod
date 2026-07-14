<!DOCTYPE html>
<!--
Sigbod John Vaccarella fecha creacion: 27/06/2019 hp grande 
-->
<?php
session_start();
include_once("../include/conn.php");
if (!isset($_SESSION['usuario'])) {
    header('Location: ../index.php');
    exit();
}
$_SESSION['query'] = "";
$fechaact = date("Y-m-d");
$id = $_GET["id"];
$link = Conectarse();
$sql = "SELECT DISTINCT pac_ano FROM pac ORDER BY pac_ano ASC";
$res = mysql_query($sql, $link);
?>
<html>
    <head>
        <link rel="stylesheet" href="../css/tabla.css" />
        <link rel="stylesheet" href="../css/estilos.css" />
        <link rel="shortcut icon" href="../img/favicon.ico" />
        <script src="../js/jquery-2.2.4.js" type="text/javascript"></script>
        <script src="../js/jquery.blockUI.js" type="text/javascript"></script>
        <script src="../js/tablesorter.min.js" type="text/javascript"></script>
        <title>SigBod - PAC</title> 
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
                    var anio = $("#anio").val();
                    var id = $("#id").val();
                    $.ajax({//hace la busqueda
                        type: "POST",
                        url: "buscarPACAjax.php",
                        data: {b: consulta, chk: check, chkall: chkall, chkver: chkver, anio: anio, id: id},
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
            function cambiarValor(x) {
                $("#" + x).attr("readonly", false);
                $("#" + x).select();
                $("#" + x).focus();
            }
        </script>
        <script type="text/javascript">
            function cambiaPac(i, x) {
                var id = i.replace(i, "div_" + i);
                var ano = $("#anio").val();
                var val= $('#' + i).attr('title');
                if (isNaN(x)) {
                    alert('Solo se pueden escribir números en este campo. Se vuelve a valor inicial');
                    $('#' + i).val(val);
                } else {
                    $.ajax({
                        type: "POST",
                        url: "../edicion/modificarPACAjax.php",
                        data: {id: i, val: x, ano: ano},
                        beforeSend: function () {//imagen de carga                            
                            $("#" + id).empty();
                            $("#" + id).append("<img src='../img/loader2.gif' width='15' heigth='15'>");
                        },
                        error: function () {
                            alert("error peticion ajax");
                        },
                        success: function (data) {
                            $("#" + id).empty();
                            $("#" + id).hide();
                            $("#" + id).append(data);
                            $("#" + id).show(800);
                            $("#" + i).attr("readonly", true);
                        }
                    });
                }
            }
        </script>
    </head>
    <body class="fondo">        
        <table class="table2a">
            <tr>
                <td class="texto" >PAC EN SISTEMA</td>
            </tr>
            <tr>
                <td class="texto1" style="text-align: center;">
                    <span class="texto5">Ingrese C&oacute;digo o Glosa a Buscar:
                        <input id="busqueda" name="txtcod" type="search" placeholder="escriba aqui..." autofocus size="30" class='caja' onkeypress="if (event.keyCode == 13)
                                    buscar.click();"/>&nbsp;&nbsp;&nbsp;
                        &nbsp;&nbsp;
                        <input type="checkbox" name="checkall" id="checkall" <?= $chk ?> />&nbsp;Mostrar Todos
                        &nbsp;&nbsp;&nbsp;<input type="checkbox" name="check" id="check" />&nbsp;Stock mayor a cero
                        &nbsp;&nbsp;&nbsp;<input type="checkbox" name="chkver" id="chkver" <?= $chk ?>/>&nbsp;No Asociados
                        &nbsp;&nbsp;&nbsp;Año <select name="anio" id="anio" class="texto6">
                            <option value="0">Elija Año</option>
                            <?php
                            $option = number_format($row["pac_ano"]);
                            while ($row = mysql_fetch_array($res)) {
                                echo "<option value='" . $row["pac_ano"] . "'>‌" . $row ["pac_ano"] . "</option>";
                            }
                            ?> 
                        </select>
                        &nbsp;&nbsp;&nbsp<button id="buscar" class="boton1">Buscar&nbsp;<img class="imgbtnpeq" alt="buscador" title="Buscar" src="../img/lupa.png"></button>
                    </span>
                </td>
            </tr>        
            <tr>
                <td class="texto5">
                    <button class="botonnormal" type="button" onclick="window.location = '../principal.php'">Volver al Menu Principal&nbsp;<img class="img" alt="principal" title="volver al menu principal" src="../img/casa.png"></button>&nbsp;&nbsp;&nbsp;&nbsp;<button onclick="window.location = '../exportar/exportaExcelPAC.php'" class="botonnormal">Exportar a Excel&nbsp;&nbsp;<img class='img' alt='exportar' title='Exportar a Excel los datos mostrados en pantalla' src='../img/excel3.png'></button>&nbsp;&nbsp;&nbsp;&nbsp;<button type="button" onclick="window.location = 'buscarPAC.php'" class="botonnormal">Limpiar&nbsp;&nbsp;<img class='img' alt='limpiar' title='Limpiar Pantalla' src='../img/clean.png'></button>
                </td>
            </tr>
            <input hidden="hidden" id="id" value="<?= $id ?>">
        </table>
        <div id="resultado"></div>
        <a href="javascript:void(0);" id="scroll" title="Ir Arriba" style="display: none;">Arriba<span></span></a>
    </body>
</html>