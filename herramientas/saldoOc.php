<!DOCTYPE html>
<!-- fecha creacion 24-03-2019 mini hp
Sigbod John Vaccarella
-->
<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: ../index.php');
    exit();
}
if (!empty($_GET["rut"])) {
    $rut = $_GET["rut"];
    $saldo=" checked";
} else {
    $rut = "";
    $saldo="";
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
$_SESSION['query'] = "";
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
        <title>SigBod - Saldos OC</title>
        <script type="text/javascript">
            function limpiaFecha(){
                $("#fecha").val("");
                $("#fecha2").val("");
                buscar.click();
            }
        </script> 
        <script type="text/javascript">
            function enviaReport(){
                var fini= $("#fecha").val();
                var ffin= $("#fecha2").val();
                if(fini=="" || ffin==""){
                    alert("Error, debe elegir las Fechas...");
					$("#fecha").css("background", "red");
					$("#fecha2").css("background", "red");
                }else{
                window.location = '../exportar/exportaExcelpedDetalleCons.php?fini='+fini+'&ffin='+ffin;
                }
            } 
        </script> 
                <script>
            $(document).ready(function () {
                var consulta;
                $("#busqueda").focus();//hacemos focus al campo de busqueda               
                $("#buscar").click(function (e) {//comprobamos si se pulsa una tecla                    
                    consulta = $("#busqueda").val().replace(/[`~!@#$%^&*()_|+\=?;:'",.<>\{\}\[\]\\\/]/gi, '').trim();//obtenemos el texto introducido en el campo de busqueda y elimina caracteres no correctos
                    var chkall = $("#checkall").prop("checked");
                    var chksaldo = $("#checksaldo").prop("checked");
                    var chkest = $("#chkest").prop("checked");
                    var chknul = $("#chknul").prop("checked");
                    var anio = $("#fecha").val();
                    var anio2 = $("#fecha2").val();
                    largo = $("#busqueda").val().length;
                    if (largo == 0 || largo > 2) {
                        $.ajax({//hace la busqueda
                            type: "POST",
                            url: "saldoOcAjax.php",
                            data: {saldo: consulta, chkall: chkall, chksaldo: chksaldo, anio: anio, anio2: anio2, chkest:chkest, chknul:chknul},
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
                                $.unblockUI();
                            }
                        });
                    }
                });
            });
        </script>
		<script type="text/javascript">
            function buscaCod(x){                
				$("#busqueda").val("");
                $("#busqueda").val(x);
                buscar.click();
            }
        </script> 
    </head>
    <body class="fondo" onload="<?= $dir ?>">
        <table class="table2a">
            <tr>
                <td class="texto"><img class="imgico" alt='Saldos' title='Saldos' src='../img/prod.png'>&nbsp;&nbsp;&nbsp;Saldos de Orden de Compra</td>
            </tr>
            <tr>
                <td class="texto1">
                    <center>Ingrese Código, Glosa , OC, o Proveedor a Buscar:&nbsp;&nbsp;
                    <input id="busqueda" name="txtcod" type="search" placeholder="escriba aqui..." autofocus size="50" maxlength="40" class='caja' value="<?= $rut ?>" onkeypress="if (event.keyCode == 13)
                    buscar.click();"/>&nbsp;&nbsp;&nbsp;&nbsp;<button id="buscar" class="boton1">Buscar&nbsp;<img class="imgbtnpeq" alt="buscador" title="Buscar" src="../img/lupa.png"></button>&nbsp;&nbsp;&nbsp;&nbsp;                
                </center>
            <center>
                <span class='caja_pading2'>Fecha Orden de compra:
                    <input type="date" class='caja_texto' name="fecha" id="fecha" step="1" min="" max="<?= $fechaact ?>" value="<?= $fini ?>"/>&nbsp;&nbsp;Al&nbsp;&nbsp;
                    <input type="date" class='caja_texto' name="fecha2" id="fecha2" step="1" min="" max="<?= $fechaact ?>" value="<?= $ffin ?>"/>
                    <input type="button" id='limp' value="x" title="Limpiar fechas" class="boton1b" onclick="limpiaFecha();">&nbsp;&nbsp;&nbsp;&nbsp;
                    <input type="checkbox" name="checkall" id="checkall" <?= $saldo ?> /><span class="texto5"> Mostrar Todos</span>&nbsp;&nbsp;
                    <input type="checkbox" name="checksaldo" id="checksaldo" onchange="buscar.click()"><span class="texto5"> Sólo Ordenes con Saldo</span>&nbsp;&nbsp;
                    <input type="checkbox" name="chkest" id="chkest"/><span class="texto5"> Items Anulados</span>&nbsp;&nbsp;
                    <input type="checkbox" name="chknul" id="chknul"/><span class="texto5"> Códigos no Asociados</span>&nbsp;
                </span>
            </center>
        </td>
    </tr>        
    <tr>
        <td class="texto5"><center><button class="botonnormal" onclick="window.location = '../principal.php'">Volver al Menu Principal&nbsp;<img class="img" alt="principal" title="volver al menu principal" src="../img/casa.png"></button>&nbsp;&nbsp;&nbsp;&nbsp;<button onclick="window.location = '../exportar/exportaExcelpedDetalle.php'" class="botonnormal">Exportar a Excel&nbsp;&nbsp;<img class='img' alt='exportar' title='Exportar a Excel los datos mostrados en pantalla' src='../img/excel3.png'></button>&nbsp;&nbsp;&nbsp;&nbsp;<button onclick="enviaReport();" class="botonnormal">Informe Consolidado Saldos&nbsp;&nbsp;<img class='img' alt='exportar' title='Exportar a Excel los datos solicitados' src='../img/excel2.png'></button>&nbsp;&nbsp;&nbsp;&nbsp;<button type="button" onclick="window.location = 'saldoOc.php'" class="botonnormal">Limpiar&nbsp;&nbsp;<img class='img' alt='limpiar' title='Limpiar Pantalla' src='../img/clean.png'></button></center></td>
</tr>
</table>
<div id="resultado"></div>
<a href="javascript:void(0);" id="scroll" title="Ir Arriba" style="display: none;">Arriba<span></span></a>
</body>
</html>