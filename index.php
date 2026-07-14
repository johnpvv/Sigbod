<!DOCTYPE html>
<!--
Sigbod John Vaccarella
-->
<html>
    <head>
        <script type="text/javascript" src="js/validarut.js"></script>
        <script src="js/jquery-2.2.4.js" type="text/javascript"></script>
        <link rel="stylesheet" href="css/estilos.css" />
        <link rel="shortcut icon" href="img/favicon.ico" />
        <script type="text/javascript">
            function Validar() {
                if (document.entrar.rut.value.length === 0) {
                    alert('Debe Ingresar El Usuario!');
                    document.entrar.rut.focus();
                    return 0;
                }
                if (document.entrar.clave.value.length === 0) {
                    alert('Favor Ingrese La Contraseña!');
                    document.entrar.clave.focus();
                    return 0;
                }
                document.entrar.submit();
            }
        </script>
        <script type="text/javascript">
            $(document).ready(function () {
                $("#login").hide();
                $("#login").fadeIn(500);
                $("#h").hide();
                $("#h").fadeToggle(1500);
                $("#r").hide();
                $("#r").fadeToggle(2500);
                $("#i").hide();
                $("#i").fadeToggle(3500);
            });
        </script>
        <script type="text/javascript">
            function actualizar() {
                location.reload(true);
            }
            setInterval("actualizar()", 30000);//Función para actualizar cada 30 segundos(30000 milisegundos)
        </script>
        <title>SigBod - Acceso</title>       
    </head>
    <body class="fondo">
        <?php
        session_start();
        session_destroy(); //PARA ELIMINAR CUALQUIER USUARIO ACTIVO SI CAE AL LOGIN EL SISTEMA
        include "include/conn.php";
        $link = Conectarse();
        $sql = "SELECT * FROM version WHERE version_estado=1";
        $res = MySQL_query($sql, $link) or die("<center><h2 class='texto'>Ha Ocurrido un Error: </h2> <b class='texto1'>" . mysql_error() . "</b></center>");
        $row = MySQL_Fetch_array($res);
        $ver_nombre = $row["version_nombre"];
        $ver_num = $row["version_num"];
        $ver_obs = $row["version_obs"];
        $date_row = date_create($row["version_fecha"]);
        $date = date_format($date_row, 'd/m/Y');
        mysql_close($link);
        ?>
        <form name="entrar" method="post" action="validaUsuario.php">
            <table class="centrado" id="login">
                <tr>	
                    <td>&nbsp;&nbsp;&nbsp;<img src="img/logo.jpg" alt="logo" width="145" height="130" id="logo"/>&nbsp;&nbsp;</td>                	
                    <td> <p class="textograd"title="Bienvenido a SigBod">&nbsp;&nbsp;&nbsp;&nbsp;Acceso a SigBod&nbsp;&nbsp;&nbsp;&nbsp;</p></td>
                </tr>
                <tr>
                    <td class="texto1" align="right">Usuario:</td>
                    <td><input type="text" name="rut" id="rut" maxlength="12" size="20" class="caja_color" onchange="javascript:return Rut(window.document.entrar.rut.value)" autofocus="autofocus"></td> 
                </tr>                
                <tr>	
                    <td class="texto1" align="right">Contrase&ntilde;a:</td>
                    <td><input type="password" name="clave" id="clave" class="caja_color" size="20" onkeypress="if (event.keyCode == 13)
                                Validar()" autocomplete="new-password"> </td> <!-- funcion javascript para validar al presionar tecla intro -->
                </tr>
                <tr>	
                    <td colspan="2"><div style="width: 480px;">
                            <?= is_connected(); ?>
                        </div><br>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" align="center">
                        <button class="boton" type="button" id="ENTRAR" onClick="Validar()">Ingresar&nbsp;&nbsp;<img class='img' alt='acceder' title='acceder al sitio' src='img/check.png'></button>
                    </td>
                </tr>
                <tr>	
                    <td colspan="2">&nbsp;</td>
                </tr>
                <tr>	
                    <td align="left">						
                        <div style="height: 25px; display:flex;">
                            <a href="ayuda.php" id="h"><img align='center' src='img/ayuda.png' width='25' height='25' title='Ayuda y Opciones de SigBod'/></a>&nbsp;&nbsp;&nbsp;&nbsp;
                            <a href="recuPassw.php" id="r"><img align='center' src='img/exclam.png' width='25' height='25' title='Restablecer Contraseña'/></a>&nbsp;&nbsp;&nbsp;&nbsp;
                            <a href="herramientas/registroUsuario.php" id="i"><img align='center' src='img/add-user.png' width='25' height='25' title='Registrarse'/></a>
                        </div>
                    </td>
                    <td align="right">					
                        <span class="texto11a" title="<?php echo $ver_obs; ?>"> Versión: <?php echo $ver_num . " " . $ver_nombre . " f: " . $date; ?></span></td>
                </tr>
            </table>			
        </form>
    </body>	
</html>