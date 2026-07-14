<!DOCTYPE html>
<!--
Sigbod John Vaccarella fecha de creacion 28/04/2019 hp grande
-->
<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: ../index.php');
    exit();
    $_SESSION['query'] = "";
}
$id = $_GET["op"];
if ($id == 1) {
    $ops = " checked";
    $dir = "buscar.click()";
}else{
    $ops = "";
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
                var chkoc;
                var chkrec;
                $('#buscar').click(function () {
                    chkoc = $('input:radio[name=chkoc]:checked').val();
                    var check = $("#chkrec").prop("checked");
                    //alert(check);
                    if (check === true) {
                        chkrec = $("#chkrec").val();
                    } else {
                        chkrec = "";
                    }
                    $.ajax({//hace la busqueda
                        type: "POST",
                        url: "nivelStockAjax.php<?=$voc?>",
                        data: {chkoc: chkoc, chkrec: chkrec},
                        beforeSend: function () {
                            $.blockUI({
                                message: "<p class='centrar_cuadro_alertas'>Actualizando, por favor espere...<img src='../img/loading.gif' width='200' height='150' /></p>"
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
        <title>SigBod - Niveles de Stock</title>   
    </head>   
    <body class="fondo" onload="<?= $dir ?>">        
        <table class="table2">
            <tr>
                <td class="texto"><img border='0' alt='Nivel Stock' title='NivelStock' src='../img/level.png' width='23' height='23'>&nbsp;NIVELES DE STOCK</td>
            </tr>
            <tr>			
                <td class="texto5a"><span class='caja_pading'><input type="radio" name="chkoc" id="chkoc" value='1'>
                        CON Orden de Compra&nbsp;&nbsp;
                        <input type="radio" name="chkoc" id="chksinoc" value='2'>                   
                        SIN Orden de Compra&nbsp;&nbsp;
                        <input type="radio" name="chkoc" id="chkall" value='4' <?= $ops ?>>                   
                        Mostrar Todos</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <input type="checkbox" name="chkrec" id="chkrec" value='3'> 
                    Mostrar Sólo productos SIN solicitudes Recientes&nbsp;&nbsp;&nbsp;&nbsp;
                    <button id="buscar" class="boton1">Buscar&nbsp;<img class="imgbtnpeq" alt="buscador" title="Buscar" src="../img/lupa.png"></button></td>                
            
            </tr>
            <tr>
                <td class="texto6"><button class="botonnormal" onclick="window.location = '../principal.php'">Volver al Menu Principal&nbsp;<img class="img" alt="principal" title="volver al menu principal" src="../img/casa.png"></button>&nbsp;&nbsp;&nbsp;&nbsp;<button onclick="window.location = '../exportar/exportaNivelStock.php'" class="botonnormal">Exportar a Excel&nbsp;&nbsp;<img class='img' alt='exportar' title='Exportar a Excel los datos mostrados en pantalla' src='../img/excel3.png'></button>&nbsp;&nbsp;&nbsp;&nbsp;<button id="capturar" class="botonnormal" onclick="window.open('../reportes/nivelStockPDF.php', '_blank', 'scrollbars=yes,statusbar=no,resizable=no,width=860,height=620 left=200,top=30')">Generar Informe en PDF&nbsp;&nbsp;<img class="img" alt="pdf" title="Exporta los datos actuales a formato PDF" src="../img/pdf1.png"></button></td>
            </tr>
        </table>            
        <div id="resultado"></div>
        <a href="javascript:void(0);" id="scroll" title="Ir Arriba" style="display: none;">Arriba<span></span></a>
    </body>
</html>