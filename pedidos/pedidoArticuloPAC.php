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
$anio = date("Y");
$id = $_GET["op"];
if ($id == 1) {
    $chk = "checked";
    $chk1 = "selected";
    $dir = "buscar.click()";
} else {
    $chk = "";
    $chk1 = "";
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
        <script src="../js/jquery.tablesorter.pager.js"></script> 
        <title>SigBod - Pedidos por PAC</title> 
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
                    var chkall = $("#checkall").prop("checked");
                    var anio = $("#anio").val();
					var anio2 = $("#anio2").val();
                    var tiposto = $("#tiposto").val();
                    $.ajax({//hace la busqueda
                        type: "POST",
                        url: "pedidoArticuloPACAjax.php",
                        data: {b: consulta, chkall: chkall, anio: anio, tiposto: tiposto, anio2: anio2},
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
    </head>
    <body class="fondo" onload="<?= $dir ?>">        
        <table class="table2a">
            <tr>
                <td class="texto" ><img class="imgico" alt='Pedido' title='Pedido' src='../img/car.png'>&nbsp;&nbsp;&nbsp;PEDIDO POR ARTÍCULO, BASADO EN PAC</td>
            </tr>
            <tr>
                <td class="texto5" style="text-align: center;">
                    <span class="texto5">Ingrese texto a Buscar:
                        <input id="busqueda" name="txtcod" type="search" placeholder="escriba aqui..." autofocus size="25" class='caja' onkeypress="if (event.keyCode == 13)
                    buscar.click();"/>&nbsp;&nbsp;
                        <button id="buscar" class="boton1">Buscar&nbsp;<img class="imgbtnpeq" alt="buscador" title="Buscar" src="../img/lupa.png"></button>
                        &nbsp;&nbsp;Tipo Stock: <select name="tiposto" class="texto5" id="tiposto" onchange="buscar.click()">
                            <option value='0' selected>‌Todos</option>
                            <option value='1'>Cero</option>
                            <option value='2'>Mayor a Cero</option>
                            <option value='3'>Producto Destacado</option>
                            <option value='4'>Stock Critico</option>
                            <option value='5' <?= $chk1 ?>>Stock Menor al PAC</option>
                            <option value='6'>Stock Menor a PAC y con OC</option>
							<option value='7'>Stock faltante mayor al 50% con OC</option>
                        </select>
                        &nbsp;&nbsp;
                        Año <select name="anio" id="anio" class="texto6">
                            <option value="0">Elija Año</option>
                            <?php
                            while ($row = mysql_fetch_array($res)) {
                                $op = $row["pac_ano"];
                                if ($anio == $op) {
                                    $sel = "selected";
                                } else {
                                    $sel = "";
                                }
                                echo "<option value='" . $op . "' " . $sel . ">‌" . $op . "</option>";
                            }
                            ?> 
                        </select>
						Año OC: 
						 <input id="anio2" name="anio2" type="text" size="3" class='texto6' value="2022"/>
                        &nbsp;&nbsp;
                    </span>
                    <input type="checkbox" name="checkall" id="checkall" <?= $chk ?> />&nbsp;Mostrar Todos
                </td>
            </tr>        
            <tr>
                <td class="texto5">
                    <button class="botonnormal" type="button" onclick="window.location = '../principal.php'">Volver al Menu Principal&nbsp;<img class="img" alt="principal" title="volver al menu principal" src="../img/casa.png"></button>&nbsp;&nbsp;&nbsp;&nbsp;
                    <button onclick="window.location = '../exportar/exportaExcelPACPed.php'" class="botonnormal">Exportar a Excel&nbsp;&nbsp;<img class='img' alt='exportar' title='Exportar a Excel los datos mostrados en pantalla' src='../img/excel3.png'></button>&nbsp;&nbsp;&nbsp;&nbsp;
                    <button onclick="window.location = 'pedidoEnviado.php'" class="botonnormal">Ir a Pedidos Generados&nbsp;&nbsp;<img class='img' alt='pedidos' title='Ir a la pantalla de pedidos generados' src='../img/ped1.png'></button>&nbsp;&nbsp;&nbsp;&nbsp;
                    <button onclick="window.location = 'pedidoProveedor.php'" class="botonnormal">Ir a Pedidos por Proveedor&nbsp;&nbsp;<img class='img' alt='pedidoProveedor' title='Ir a la pantalla de pedidos por Proveedor' src='../img/oc.png'></button>&nbsp;&nbsp;&nbsp;&nbsp;
                    <button type="button" onclick="window.location = 'pedidoArticuloPAC.php'" class="botonnormal">Limpiar&nbsp;&nbsp;<img class='img' alt='limpiar' title='Limpiar Pantalla' src='../img/clean.png'></button>
                </td>
            </tr>
        </table>
        <div id="resultado"></div>
        <a href="javascript:void(0);" id="scroll" title="Ir Arriba" style="display: none;">Arriba<span></span></a>
    </body>
</html>