<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: ../index.php');
    exit();
    $cambio=$_SESSION["cambiar"];
    if($cambio==0){
        echo "<script>alert('Error, no ha validado la contraseña anterior.')</script>";
        exit();
    }
}
?>
<html>
    <head>
        <title>Sigbod - Cambio Password</title>
        <script type="text/javascript" src="../js/validarut.js"></script>
        <link rel="shortcut icon" href="../img/favicon.ico" />
        <link rel="stylesheet" href="../css/estilos.css" />
    </head>
    <body class="fondo">
        <form name="insertar" method="post" action="insertapw.php" autocomplete="off">
            <table class="centradopw">
                <tr>
                    <td colspan="2" class="texto">Cambio de Password</td>
                </tr>
                <tr>	
                    <td class="texto6">Ingrese Nueva Password:</td>
                    <td><input type="password" name="pass" class="caja" size="15" autocomplete="new-password">&nbsp;*&nbsp;</td>
                </tr>
                <tr>	
                    <td class="texto6">Repetir Password:</td>
                    <td><input type="password" name="pass1" class="caja" size="15" autocomplete="new-password">&nbsp;*&nbsp;</td>
                </tr>
                <tr>
                    <td colspan="2"><center><button class="boton" type="button" onClick="validar()" id="cambiar">Cambiar&nbsp;&nbsp;<img class='img' alt='Cambiar' title='Cambiar' src='../img/check.png'></button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <button class="boton" type="reset">Limpiar&nbsp;&nbsp;<img class='img' alt='limpiar' title='Limpiar Pantalla' src='../img/clean.png'></button></center></td>
                </tr>
                <tr>	
                    <td colspan="2" class="texto11a" align="right">(*) Campos Requeridos&nbsp;</td>                        
                </tr>
            </table>
        </form>
        <script>
            function validar() {
                if (document.insertar.pass.value !== document.insertar.pass1.value) {
                    alert('Las contraseñas no son iguales');
                    document.insertar.pass.focus();
                    return 0;
                }
                else {
                    if (document.insertar.pass.value.length === 0) {
                        alert('Digite una Clave');
                        document.insertar.pass.focus();
                        return 0;
                    }
                    if (document.insertar.pass1.value.length === 0) {
                        alert('Repita la Clave');
                        document.insertar.pass1.focus();
                        return 0;
                    }
                }
                document.insertar.submit();
            }
        </script>
    </body>
</html>