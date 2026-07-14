<?php
session_start();
if (isset($_SESSION["cambiar"])) {
    header('Location: ../index.php');
    exit();
}
?>
<html>
    <head>
        <title>Sigbod - Recuperar Password</title>
        <script type="text/javascript" src="js/validarut.js"></script>
        <link rel="shortcut icon" href="img/favicon.ico" />
        <link rel="stylesheet" href="css/estilos.css" />
        <script src="js/jquery-2.2.4.js" type="text/javascript"></script>
        <script src="js/jquery.blockUI.js" type="text/javascript"></script>
        <script type="text/javascript">
            $(document).ready(function (e) {
                $("#entrar").on('submit', (function (e) {
                    e.preventDefault();
                    var data = new FormData(this); // <-- 'this' is your form element
                    $.ajax({
                        url: 'recuPasswAjax.php',
                        data: data,
                        cache: false,
                        contentType: false,
                        processData: false,
                        type: 'POST',
                        beforeSend: function () {
                            $.blockUI({
                                message: "<p class='centrar_cuadro_alertas'>Restableciendo Clave, Favor Espere...<img src='img/loading.gif' width='200' height='140' /></p>"
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
                }));
            });
        </script>        
    </head>
    <body class="fondo">
        <form name="entrar" id="entrar" method='post' enctype="multipart/form-data">
            <table class="centradorcpw">
                <tr>
                    <td colspan="2" class="texto"><img class="imgico" alt='clave' title='clave' src='img/pass.png'>&nbsp;&nbsp;&nbsp;Restaurar Clave</td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                </tr>
                <tr>	
                    <td class="texto1">Ingrese Su RUT:</td>
                    <td><input type="text" name="rut" id="rut" maxlength=12" class="caja_texto" size="17" onchange="javascript:return Rut(window.document.entrar.rut.value)" autofocus="autofocus" required>&nbsp;&nbsp;*&nbsp;&nbsp;&nbsp;</td>
                </tr>
                <tr>	
                    <td class="texto1">Ingrese Fecha de Nacimiento:</td>
                    <td><input type="date" name="date" id="date" class="caja_texto" required>&nbsp;&nbsp;*</td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td colspan="2" style="text-align: center;"><button class="boton" type="submit" id="cambiar">Restablecer&nbsp;&nbsp;<img class='img' alt='restablecer' title='Restablecer' src='img/check.png'></button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <button class="boton" onclick="window.location = 'index.php'" type="button">Volver&nbsp;<img class="img" alt="volver" title="volver" src="img/Undo.png"></button></td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                </tr>
                <tr>	
                    <td colspan="2" class="texto11a" align="left">(*) Campos Requeridos</td>                        
                </tr>
            </table>
        </form>
        <div id="resultado"></div>
    </body>
</html>