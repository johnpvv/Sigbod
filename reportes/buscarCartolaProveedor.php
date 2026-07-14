<!DOCTYPE html>
<!--
Sigbod John Vaccarella
-->
<?php
include_once("../include/conn.php");
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: ../index.php');
    exit();
}
$rut1 = $_COOKIE["rut"];
$date = date("Y-m-d", strtotime(date("Y-m-d") . "+ 1 days"));
?>
<html>
    <head>
        <link rel="stylesheet" href="../css/tabla.css" />
        <link rel="stylesheet" href="../css/estilos.css" />        
        <link rel="shortcut icon" href="../img/favicon.ico" />
        <script src="../js/jquery-2.2.4.js" type="text/javascript"></script>
        <script type="text/javascript" src="../js/validarut.js"></script>
        <title>SigBod - Cartola Articulos</title>
        <script type="text/javascript">
            function buscarCod() {
                var cod = $("#rutpv").val();
                if (cod === "" || cod.length <= 6) {
                    alert("Debe Introducir Un RUT Valido Ej. 12345678");
                    document.cart.rut.focus();
                    return 0;
                } else {
                    cart.action = "../reportes/pedidoProvDetalle.php";
                    cart.submit();
                }
            }
        </script>
    </head>
    <body class="fondo">
        <form id="cart" name="cart" method="GET">
            <table class="table">
                <tr>
                    <td class="texto" colspan="2">CARTOLA DE MOVIMIENTOS POR PROVEEDOR</td>
                </tr>
                <tr>
                    <td class="texto1">Ingrese RUT (sin guión ni dígito verificador):</td>
                    <td><input type="text" name="rutpv" id="rutpv" class="texto6" value="<?php echo $rut1; ?>" autofocus size="20" maxlength="9" onkeypress="if (event.keyCode === 13)
                                buscarCod();"/>&nbsp;&nbsp;&nbsp;<a onclick="window.open('listarBuscaProv.php', '_blank', 'scrollbars=yes,statusbar=no,resizable=no,width=720,height=480 left=300,top=50')"><img alt='buscador' class="imgnormal" title='Buscador' src='../img/lupa.png'></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" value="Limpiar" class="boton1" title="refresca la pagina" onclick="location.href = 'buscarCartolaProveedor.php'"/></td>
                </tr>
                <tr>
                    <td class="texto1">Fecha Inicio:&nbsp;&nbsp;<input type="date" class='texto1a' name="fini" step="1" min="2018-01-01" max="<?= $date ?>" value="2019-01-01" required />&nbsp;<img align='center' src='../img/datepicker.png' width='35' height='35' title="Seleccionar fecha inicio"/></td><td class="texto1">
                        Fecha Fin:&nbsp;&nbsp;<input type="date" class='texto1a' name="ffin" step="1" min="2019-01-01" max="2021-12-31" value="<?= $date ?>"/>&nbsp;<img align='center' src='../img/datepicker.png' width='35' height='35' title="Seleccionar fecha fin" required /> </td>   
                </tr>                
                <tr>
                    <td colspan="2"><center> <button class="boton" type="button" onclick="buscarCod()">Buscar&nbsp;<img class="img" alt="buscador" title="Buscar Movimientos" src="../img/lupa.png"></button> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
                    <button class="boton" type="button" onClick='history.go(-1);'>Volver&nbsp;<img class="img" alt="volver" title="Volver a pantalla anterior" src="../img/undo.png"></button></center></</td>
                </tr>
            </table>
        </form>
    </body>
</html>