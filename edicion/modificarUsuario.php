<!DOCTYPE html>
<!--
Sigbod John Vaccarella
-->
<?php
session_start();
include_once("../include/conn.php");
if (!isset($_SESSION['usuario'])) {
    header('Location: ../index.php');
    exit();
}
if ($_SESSION['perfil'] > 1) {
    echo '<script>alert("Su Perfil no permite la visualizacion de esta pantalla, Favor contacte al Administrador.");history.back(-1);</script>';
    exit();
}
$link = Conectarse();
$fecha = $date = date("Y-m-d");
$id = $_GET['id'];

$sql = "SELECT * FROM usuarios WHERE usuario_id='$id'";
$res = MySQL_query($sql, $link)or die(mysql_error());
$contar = mysql_num_rows($res);

$sql1 = "SELECT * FROM estado";
$res1 = MySQL_query($sql1, $link)or die(mysql_error());

$sql2 = "SELECT * FROM perfiles";
$res2 = MySQL_query($sql2, $link)or die(mysql_error());

$sql3 = "SELECT bodega_dir, bodega_id FROM bodegas GROUP BY bodega_dir ORDER BY bodega_dir";
$res3 = mysql_query($sql3) or die(mysql_error());

$sql4 = "SELECT uni_nombre, uni_id FROM unid_oper ORDER BY uni_nombre";
$res4 = mysql_query($sql4) or die(mysql_error());

if ($contar == 0) {
    echo '<script>alert("El usuario ingresado no Existe, o se encuentra Inactivo");
				history.back(-1);</script>';
}
$row = MySQL_Fetch_array($res);
?>
<html>
    <head>
        <script type="text/javascript" src="js/validarut.js"></script>
        <link rel="stylesheet" href="../css/estilos.css" />
        <link rel="stylesheet" href="../css/tabla.css" />
        <script src="../js/jquery-2.2.4.js" type="text/javascript"></script>
        <script src="../js/jquery.blockUI.js" type="text/javascript"></script>
        <link rel="shortcut icon" href="../img/favicon.ico" />
        <title>SigBod - Editar Usuarios</title>
        <script type="text/javascript">
            function validar() {
                if (confirm("¿Esta Seguro que los datos estan correctos?")) {
                    return true;
                } else {
                    return false;
                }
            }
        </script>
        <script type="text/javascript">
            $(document).ready(function (e) {
                $("#modificar").on('submit', (function (e) {
                    e.preventDefault();
                    if (validar()) {
                        $.ajax({
                            url: 'modificarUsuarioAjax.php',
                            data: $("#modificar").serialize(),
                            type: 'POST',
                            beforeSend: function () {
                                $.blockUI({
                                    message: "<p class='centrar_cuadro_alertas'>Grabando datos usuario, por favor espere...<img src='../img/loading.gif' width='200' height='140' /></p>"
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
                }));
            });
        </script>
    </head>
    <body class="fondo">
        <form name="modificar" id="modificar" method="post" autocomplete="off">
            <table class="table">
                <tr>
                    <td class="texto" colspan="2"><img class="imgico" alt='Usuario' title='Usuario' src='../img/user.png'>&nbsp;&nbsp;&nbsp;MODIFICAR USUARIOS</td>
                </tr>
                <tr>
                    <td class="texto1">RUT:</td>
                    <td><input type="text" name="txtrut" class="caja" value="<?= $row["usuario_rut"] ?>" size="10" readonly></td>
                </tr>
                <tr>
                    <td class="texto1">Nombres:</td>
                    <td><input type="text" name="txtnombre"  class="caja" value="<?= $row["usuario_nombre"] ?>" size="30" required></td>
                </tr>
                <tr>
                    <td class="texto1">Apellidos:</td>
                    <td><input type="text" name="txtapellido" class="caja"  value="<?= $row["usuario_apellidos"] ?>" size="30" required></td>
                </tr>
                <tr>
                    <td class="texto1">Teléfono</td>
                    <td><input type="text" name="txtfono" class="caja"  value="<?= $row["usuario_telefono"] ?>" size="20" required></td>
                </tr>
                <tr>
                    <td class="texto1">E-mail:</td>
                    <td><input type="text" name="txtmail" class="caja"  value="<?= $row["usuario_email"] ?>" size="30" required></td>
                </tr>
                <tr>
                    <td class="texto1">Instituci&oacute;n o Unidad:</td>
                    <td>
                        <select name="txtinstitucion" class="caja" required>
                            <option value="">Seleccionar una opción</option>
                            <?php
                            $sel = $row["usuario_institucion"];
                            while ($row4 = mysql_fetch_array($res4)) {
                                if ($sel == $row4["uni_id"]) {
                                    echo"<option value='" . $row4["uni_id"] . "' selected>" . $row4["uni_nombre"] . "</option>";
                                } else {
                                    echo"<option value='" . $row4["uni_id"] . "'>" . $row4["uni_nombre"] . "</option>";
                                }
                            }
                            ?>
                        </select>					
                    </td>
                </tr>
                <tr>
                    <td class="texto1">Direcci&oacute;n de Trabajo:</td>
                    <td>
                        <select name="dir" class="caja" required>
                            <option value="">Seleccionar una opción</option>
                            <?php
                            $sel1 = $row["usuario_direccion"];
                            while ($row3 = mysql_fetch_array($res3)) {
                                if ($sel1 == $row3["bodega_id"]) {
                                    echo"<option value='" . $row3["bodega_id"] . "' selected>" . $row3["bodega_dir"] . "</option>";
                                } else {
                                    echo"<option value='" . $row3["bodega_id"] . "'>" . $row3["bodega_dir"] . "</option>";
                                }
                            }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="texto1">Fecha de Nacimiento:</td>
                    <td><input type="date" class='caja' name="anio" step="1" min="1950-01-01" max="<?= $fecha ?>" value="<?= $row["usuario_fechaNac"] ?>" required></td>
                </tr>
                <tr>
                    <td class="texto1">Perfil Usuario:</td>
                    <td><select name="txtperfil" class="caja" id="perfil" required>
                            <?php
                            $option1 = number_format($row["usuario_perfil"]);
                            $z = 0;
                            while ($row2 = mysql_fetch_array($res2)) {
                                if ($z == $option1) {
                                    echo "<option value='" . $option1 . "' selected>‌" . $row2 ["perfil_nombre"] . "</option>";
                                } else {
                                    echo"<option value=", $z, ">", $row2["perfil_nombre"], "</option>";
                                }
                                $z++;
                            }
                            ?>                            
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="texto1">Estado Usuario:</td>
                    <td>
                        <select name="txtestado" class="caja" id="estado" required>
                            <?php
                            $option = number_format($row["usuario_estado"]);
                            $y = 0;
                            while ($row1 = mysql_fetch_array($res1)) {
                                if ($y == $option) {
                                    echo "<option value='" . $option . "' selected>‌" . $row1 ["estado_nombre"] . "</option>";
                                } else {
                                    echo"<option value=", $y, ">", $row1["estado_nombre"], "</option>";
                                }
                                $y++;
                            }
                            mysql_close($link);
                            ?>                            
                        </select>
                    </td>
                </tr>
                <tr style="text-align: center;">
                    <td colspan="2">
                        <button class="botonnormal" type="button" onclick="javascript:history.back()">Volver&nbsp;&nbsp;<img class='img' alt='volver' title='Volver a pantalla anterior' src='../img/undo.png'></button>&nbsp;&nbsp;&nbsp;
                        <button class="botonnormal" type="submit">Modificar Usuario&nbsp;&nbsp;<img class='img' alt='modificar' title='modificar usuario' src='../img/edit1.png'></button>
                    </td>
                </tr>
            </table>
        </form>
        <div id="resultado"></div>
    </body>
</html>
