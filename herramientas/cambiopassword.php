<!DOCTYPE html>
<!--
Sigbod John Vaccarella
-->
<html>
    <head>
        <link rel="stylesheet" href="../css/estilos.css" />
        <script src="../js/jquery-2.2.4.js" type="text/javascript"></script>
        <script src="../js/jquery.blockUI.js" type="text/javascript"></script>
        <script src="../js/tablesorter.min.js" type="text/javascript"></script>
        <link rel="shortcut icon" href="../img/favicon.ico" />
        <title>SigBod - Cambio Password</title> 
        <script type="text/javascript">
            $(document).ready(function (e) {
                $("#pw").on('submit', (function (e) {
                    e.preventDefault();
                    var data = new FormData(this); // <-- 'this' is your form element
                    $.ajax({
                        url: 'cambioPasswordAjax.php',
                        data: data,
                        cache: false,
                        contentType: false,
                        processData: false,
                        type: 'POST',
                        beforeSend: function () {
                            $.blockUI({
                                message: "<p class='centrar_cuadro_alertas'>Cargando, por favor espere...<img src='../img/loading.gif' width='200' height='140' /></p>"
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

        <?php
        session_start();
        if (!isset($_SESSION['usuario'])) {
            header('Location: ../index.php');
            exit();
        }
        ?>
        <div id="resultado">
            <form name="pw" id="pw" action="#" autocomplete="off">
                <table border="0" style="margin: 0 auto;" class="centradopw">
                    <tr>	
                        <td colspan="2" class="texto">Cambio de Password</td>
                    </tr>
                    <tr>	
                        <td class="texto6">Ingrese Password Actual:</td>
                        <td><input type="password" name="key" id="key" class="caja" autofocus required size="20"></td>
                    </tr>
                    <tr><td colspan="2" align="center"><button class="boton" type="submit" id="entrar">Entrar&nbsp;<img class="img" alt="salir" title="Salir" src="../img/check.png"></button>&nbsp;&nbsp;&nbsp;<button id="salir" type="button" class="boton" onclick="window.close()">Salir&nbsp;<img class="img" alt="salir" title="Salir" src="../img/salir.png"></button></td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                    </tr>
                </table>
            </form>
        </div>        
    </body>
</html>