<!DOCTYPE html>
<!-- fecha creacion 12/10/2021 hp
Sigbod John Vaccarella
-->
<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: ../index.php');
    exit();
}
$op = $_GET["op"];
$oc1 = $_GET["oc"];
$dt = $_GET["data"];
if ($op === "1") {
    $btn = "<button class='boton' type='button' onClick='window.close();'>Salir&nbsp;<img class='img' alt='Salir' title='Cerrar Ventana' src='../img/salir.png'></button>";
} else {
    $btn = "<button class='boton' type='button' onClick='history.go(-1);'>Volver&nbsp;<img class='img' alt='volver' title='Volver a pantalla anterior' src='../img/undo.png'></button>";
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
        <script type="text/javascript">
            function copiarPorta(x) {
                $("#div").hide();
                var $temp = $("<input>");
                $("body").append($temp);
                $temp.val($(x).val()).select();
                document.execCommand("copy");
                $temp.remove();
                $("#div").empty();
                $("#div").append("<img src='../img/check.png' width='15' heigth='15' title='copiado OK'>");
                $("#div").show("800");
            }
        </script>
        <script type="text/javascript"> //consultar datos y autollenar campos del codigo
            $(document).ready(function () {
                var oc = "<?= $oc1 ?>";
                var op = "<?= $op ?>";
				var dt= "<?= $dt?>";
                $.ajax({
                    url: "decodeJsonOCAjax.php",
                    type: "POST",
                    data: {oc: oc, op: op, dt:dt},
                    beforeSend: function () {//imagen de carga                            
                        $.blockUI({
                            message: "<p class='centrar_cuadro_alertas'>Conectando a Mercado Público, favor espere...<img src='../img/clock.gif' width='140' height='140' /></p>"
                        });
                    },
                    error: function () {
                        alert("Error: No se ha podido Conectar, Favor intente más tarde.");
                        $.unblockUI();
                        $("#resultado").append("<center><br><span class='texto6'>Error con la descarga de datos, favor intente mas tarde.</span><br><br><?= $btn ?></center>");
                    },
                    success: function (data) {
                        $("#resultado").empty();
                        $("#resultado").append(data);
                        $.unblockUI();
                    }
                });
            });
        </script>
        <script type="text/javascript">
            var click = 0;
            function mostrarDet() {
                if (click == 0) {
                    $("#table").show("900");
                    click = click + 1;
                } else {
                    $("#table").hide("300");
                    click = 0;
                }
            }
        </script>
        <title>SigBod - Detalle Orden de compra</title>
    </head>
    <body class="fondo">
        <table class="table3" align="center">
            <tr><td colspan="2" class="texto"><img class="imgico" alt='OC' title='OC' src='../img/mp1.png'>&nbsp;&nbsp;&nbsp;Información Avanzada Orden de Compra N° <?= $oc1 ?></td></tr>            
        </table>
        <div id="resultado"></div>
        <a href="javascript:void(0);" id="scroll" title="Ir Arriba" style="display: none;">Arriba<span></span></a>
        <br>   
    </body>
</html>