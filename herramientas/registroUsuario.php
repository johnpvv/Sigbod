<?php
session_start();
if (!isset($_SESSION['usuario']) && isset($_SESSION['cambiar'])) {//cambiar para saber si viene de la creacion de usuario desde el login
    header('Location: ../index.php');
    exit();
}
if ($_SESSION['perfil'] > 1) {
    echo '<script>alert("Su Perfil no permite la visualizacion de esta pantalla, Favor contacte al Administrador.");history.back(-1);</script>';
    echo '<center><button class="boton" onclick="javascript:window.close()">Salir&nbsp;<img class="img" alt="Salir" title="salir de la pantalla de edicion" src="../img/salir.png"></button></center>';
    exit();
}

if (!isset($_SESSION["cambiar"])) {
    $perf = "0";
    $hid = 'hidden="yes"';
} else {
    $perf = "1";
    $hid = "";
}
include("../include/conn.php");

$link = Conectarse();
$fecha = $date = date("Y-m-d");
$sql = "SELECT perfil_nombre, perfil_id FROM perfiles";
$res = mysql_query($sql) or die(mysql_error());

$sql1 = "SELECT bodega_dir, bodega_id FROM bodegas GROUP BY bodega_dir ORDER BY bodega_dir";
$res1 = mysql_query($sql1) or die(mysql_error());

$sql2 = "SELECT uni_nombre, uni_id FROM unid_oper ORDER BY uni_nombre";
$res2 = mysql_query($sql2) or die(mysql_error());
?>
<!DOCTYPE html>
<!--
Sigbod John Vaccarella
-->
<html>
    <head>
        <title>SigBod - Agregar Usuarios</title>
        <script type="text/javascript" src="../js/validarut.js"></script>
        <script src="../js/jquery-2.2.4.js" type="text/javascript"></script>
        <script src="../js/jquery.blockUI.js" type="text/javascript"></script>
        <link rel="shortcut icon" href="../img/favicon.ico" />
        <link rel="stylesheet" href="../css/estilos.css" />
        <link rel="stylesheet" href="../css/tabla.css" />
        <script type="text/javascript">
            $(document).ready(function () {
                $('#ticket').hide();
            });
        </script>
        <script type="text/javascript">
            function validar() {
                if (document.entrar.pass0.value !== document.entrar.pass1.value) {
                    alert('Las contraseñas no son iguales');
                    document.entrar.pass0.focus();
                    return false;
                } else {
                    if (document.entrar.pass0.value.length === 0 & document.entrar.pass1.value.length === 0) {
                        alert('Digite las contraseñas');
                        document.entrar.pass0.focus();
                        return false;
                    }
                    if (document.entrar.pass0.value.length <= 3 & document.entrar.pass1.value.length <= 3) {
                        alert('La contraseña debe tener como minimo 4 caracteres');
                        document.entrar.pass0.focus();
                        return false;
                    }
                    if (document.entrar.vrut.value == 0) {
                        alert('El RUT ingresado esta erroneo...');
                        document.entrar.rut.focus();
                        document.entrar.rut.select();
                        return false;
                    }
                }
                return true;
            }
        </script>
        <script type="text/javascript">
            function cargaRut(r) {  //digito verificador         
                if (Rut(r)) {
                    $('#ticket').show();
                    $('#vrut').val(1);
                    $("#rut").css("background", "#FFFFFF");
                } else {
                    $('#ticket').hide();
                    $("#rut").css("background", "#FF0040");
                    $('#vrut').val(0);
                }
            }
        </script>
        <script type="text/javascript">
            $(document).ready(function (e) {
                $("#entrar").on('submit', (function (e) {
                    e.preventDefault();
                    if (validar()) {
                        $.ajax({
                            url: 'registroUsuarioAjax.php',
                            data: $("#entrar").serialize(),
                            type: 'POST',
                            beforeSend: function () {
                                $.blockUI({
                                    message: "<p class='centrar_cuadro_alertas'>Creando usuario, por favor espere...<img src='../img/loading.gif' width='200' height='140' /></p>"
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
        <script type="text/javascript">
            function buscar(x) {
                $.ajax({//hace la busqueda
                    type: "POST",
                    url: "buscaUser.php",
                    dataType: "json",
                    data: {dato: x},
                    error: function () {
                        alert("error peticion ajax");
                    },
                    success: function (data1) {
                        if (data1.nombre !== " ") {
                            alert("Error...\nEl RUT ingresado: '" + x + "' , Ya existe, para el Usuario: \n" + data1.nombre);
                            $("#rut").val("");
                            $('#ticket').hide();
                            $("#rut").css("background", "#FF0040");
                            $('#vrut').val(0);
                        }
                    }
                });
            }
        </script>
    </head>
    <body class="fondo">
        <form name="entrar" id="entrar" method="post" autocomplete="off">
            <table class="table">
                <tr>
                    <td colspan="2" class="texto"><img class="imgico" alt='Usuarios' title='Usuarios' src='../img/add-user.png'>&nbsp;&nbsp;&nbsp;Creación de Usuarios</td>
                </tr>
                <tr>
                    <td class="texto1">RUT:</td> 
                    <td class="texto5a"><input type="text" name="rut" id="rut" size="12" maxlength="9" class="caja_color" onchange="cargaRut(this.value), buscar(this.value)" required autofocus />&nbsp;&nbsp;Sin Guion y con Dígito Verificador&nbsp;&nbsp;&nbsp;<img class="imgnormal" src="../img/check.png" id="ticket" title="El RUT ingresado esta correcto"></td>
                </tr>
                <tr>
                    <td class="texto1">Nombres: </td>
                    <td><input name="nom" type="text" size="50" maxlength="50" class="caja" required></td>
                </tr>
                <tr>
                    <td class="texto1">Apellidos: </td>
                    <td><input name="ap" type="text" size="50" maxlength="50" class="caja" required></td>
                </tr>
                <tr>
                    <td class="texto1">Fecha de Nacimiento:</td>
                    <td><input type="date" class='caja_color' name="fnac" step="1" min="1950-01-01" max="<?= $fecha ?>" required>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <span class="texto1">Sexo:&nbsp;&nbsp;<input type="radio" name="sex" value="M" required>Masculino&nbsp;&nbsp;<input type="radio" name="sex" value="F" required>Femenino</span></td>
                </tr>
                <tr>
                    <td class="texto1">Correo Electrónico:</td> 
                    <td><input name="email" type="email" size="50" maxlength="50" class="caja" required></td>
                </tr>
                <tr>
                    <td class="texto1">Tel&eacute;fono:</td> 
                    <td><input name="txtfono" type="text" size="20" maxlength="12" class="caja" required></td>
                </tr>
                <tr>	
                    <td class="texto1">Unidad de Operativa:</td>
                    <td><select name="instituc" class="caja" required>
                            <option value="" selected>Seleccionar una opción</option>
                            <?php
                            while ($row2 = mysql_fetch_array($res2)) {
                                echo"<option value='" . $row2["uni_id"] . "'>" . $row2["uni_nombre"] . "</option>";
                            }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr>	
                    <td class="texto1">Direcci&oacute;n de Trabajo:</td>
                    <td>
                        <select name="dir" class="caja" required>
                            <option value="" selected>Seleccionar una opción</option>
                            <?php
                            while ($row1 = mysql_fetch_array($res1)) {
                                echo"<option value='" . $row1["bodega_id"] . "'>" . $row1["bodega_dir"] . "</option>";
                            }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr <?= $hid ?>>	
                    <td class="texto1">Perfil:</td>
                    <td>                                                    
                        <?php
                        if ($perf == 1) {
                            $y = 0;
                            echo '<select name="perf" class="caja" required>';
                            echo '<option value="" selected>Seleccionar una opción</option>';
                            while ($row = mysql_fetch_array($res)) {
                                echo"<option value=", $y, ">", $row["perfil_nombre"], "</option>";
                                $y++;
                            }
                            echo '</select>';
                        }
                        ?>                        
                    </td>
                </tr>
                <tr>	
                    <td class="texto1">Clave:</td>
                    <td><input type="password" name="pass0" class="caja_color" autocomplete="new-password"></td><!--para qe no complete los campos con datos de otros usuarios el navegador-->
                </tr>
                <tr>	
                    <td class="texto1">Repetir Clave:</td>
                    <td><input type="password" name="pass1" class="caja_color" autocomplete="off">
                        <input type="hidden" name="vrut" id="vrut" value="0">
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="text-align: center;"><button class="boton" type="button" onclick="window.location = '../principal.php'">Volver&nbsp;&nbsp;<img class='img' alt='volver' title='Volver a pantalla anterior' src='../img/undo.png'></button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <button class="boton" type="submit">Crear Usuario&nbsp;&nbsp;<img class='img' alt='modificar' title='crear usuario' src='../img/edit1.png'></button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button class="boton" type="button" onclick="window.location = 'registroUsuario.php'">Limpiar&nbsp;&nbsp;<img class='img' alt='modificar' title='limpiar formulario' src='../img/clean.png'></button>
                    </td>
                </tr>
            </table>
        </form>
        <div id="resultado"></div>
        <?php
        mysql_close($link);
        ?>
    </body>
</html>