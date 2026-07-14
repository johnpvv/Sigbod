<?php
include_once("../include/conn.php");
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: ../index.php');
    exit();
}
$link = Conectarse();
$cod = $_GET['id'];
$rut = $_GET['rut'];
$optpag = $_GET['option'];
$optpag1 = $_GET['option'];
$lectura = "";
$boton = "";
$option = "";
$oculto = "hidden";
$fechaact = date("Y-m-d");
$fechaprim = date("Y-01-01");
$comilladob = '"';
$comillasim = "'";

if ($cod === NULL || $rut != NULL) {
    $lectura = "";
    $boton = "Crear Proveedor";
    $pagina = "crea";
    $option = "1";
    $sql1 = "SELECT * FROM estado";
    $res1 = MySQL_query($sql1, $link)or die(mysql_error());
    $flg = "hidden='yes'";
    if ($optpag == 3) {
        $nom = trim(str_replace("_", " ", $_GET['nombre']));
        $cod = $rut;
        $dv = $_GET['dv'];
        $dir = trim(str_replace("_", " ", $_GET['dir']));
        $giro = trim(str_replace("_", " ", $_GET['giro']));
        $cont = trim(str_replace("_", " ", $_GET['cont']));
        $mail = $_GET['mail'];
        $fono = "+" . preg_replace('([^A-Za-z0-9])', "", trim($_GET['fono']));
        if (strlen($fono) < 5) {
            $fono = "";
        }
    }
} else {
    $lectura = "readonly";
    $boton = "Modificar Proveedor";
    $pagina = "modifica";
    $oculto = "";
    $sql = "SELECT * FROM proveedores WHERE prov_rut='$cod'";
    $sql1 = "SELECT * FROM estado";
    $res = MySQL_query($sql, $link)or die(mysql_error());
    $contar = mysql_num_rows($res);
    if ($contar == 0 && $optpag != 3) {
        echo '<script>alert("El RUT del proveedor ingresado no existe en sistema, favor revisar los datos.");window.close();</script>';
    } else {
        $res1 = MySQL_query($sql1, $link)or die(mysql_error());
        $row = MySQL_Fetch_array($res);
    }
}
if ($optpag == 1) {
    $optpag = '<button type="button" class="boton" onclick="javascript:window.close()">Salir';
    $boton1 = "Ficha Del Proveedor";
    $btnoc= '&nbsp;&nbsp;&nbsp;&nbsp;<button type="button" class="boton" onclick="window.location ='.$comillasim.'../herramientas/buscarOcSaldo.php?op=1&rut=' . $cod . '&op=1&fini=' . $fechaprim . '&ffin='.$fechaact.$comillasim.', window.moveTo(0,0);window.resizeTo(screen.availWidth, screen.availHeight);">Ver OCs Asociadas&nbsp;<img class="img" alt="Grabar" title="Grabar" src="../img/oc.png"></button>';
} else if ($optpag == 2) {
    $optpag = '<button type="button" class="boton" onclick="javascript:history.back(-1)">Volver';
    $boton1 = "Modificar Proveedor";
    $btnoc= '&nbsp;&nbsp;&nbsp;&nbsp;<button type="button" class="boton" onclick="window.location ='.$comillasim.'../herramientas/buscarOcSaldo.php?op=1&rut=' . $cod . '&op=1&fini=' . $fechaprim . '&ffin='.$fechaact.$comillasim.', window.moveTo(0,0);window.resizeTo(screen.availWidth, screen.availHeight);">Ver OCs Asociadas&nbsp;<img class="img" alt="Grabar" title="Grabar" src="../img/oc.png"></button>';
} else {
    if ($optpag == 3) {
        $optpag = '<button type="button" class="boton" onclick="cerrar();">Salir';
        $boton1 = "Crear Nuevo Proveedor";
    } else {
        $optpag = '<button type="button" class="boton" onclick="javascript:history.back(-1)">Volver';
        $boton1 = "Crear Nuevo Proveedor";
    }
}
?>
<!DOCTYPE html>
<!--
Sigbod John Vaccarella
-->
<html>
    <head>
        <script type="text/javascript" src="../js/validarut.js"></script>
        <link rel="stylesheet" href="../css/tabla.css" />
        <link rel="stylesheet" href="../css/estilos.css" />
        <link rel="shortcut icon" href="../img/favicon.ico" />
        <script src="../js/jquery-2.2.4.js" type="text/javascript"></script>
        <script src="../js/jquery.blockUI.js" type="text/javascript"></script>        
        <title>SigBod - <?php echo $boton; ?></title>
        <script type="text/javascript">
            function dgv(T) {  //digito verificador         
                var M = 0, S = 1;
                for (; T; T = Math.floor(T / 10))
                    S = (S + T % 10 * (9 - M++ % 6)) % 11;
                var dv = S ? S - 1 : 'K';
                $(function () {
                    $("#dv").val(dv);
                });
            }
        </script>
        <script type="text/javascript">
            function validar() {
                var rut = $("#rut").val();
                var nom = $("#nombre").val();
                if (rut === "" || rut.length < 6) {
                    alert("Debe Introducir Un RUT Valido Ej. 12345678");
                    document.frm.txtrut.focus();
                    document.frm.txtrut.select();
                    return false;
                }
                if (nom === "") {
                    alert("Debe Introducir Un Nombre");
                    document.frm.txtnombre.focus();
                    return false;
                }
                if (confirm("¿Esta Seguro que los datos estan correctos?")) {
                    return true;
                } else {
                    return false;
                }
            }
        </script>
        <script type="text/javascript">
            function buscarProv(x) {
                $.ajax({//hace la busqueda
                    type: "POST",
                    url: "../herramientas/buscaprov.php",
                    dataType: "json",
                    data: {val: x},
                    error: function () {
                        alert("error peticion ajax");
                    },
                    success: function (data1) {
                        if (data1.nombre !== null) {
                            alert("Error...\nEl RUT ingresado: '" + x + "' , Ya existe, para el Proveedor: \n" + data1.nombre);
                            $("#rut").val("");
                            $("#rut").css("background", "#FF0040");
                            $('#dv').val("");
                        } else {
                            $('#ticket').show();
                        }
                    }
                });
            }
        </script>
        <script type="text/javascript">
            $(document).ready(function (e) {
                $("#frm").on('submit', (function (e) {
                    e.preventDefault();
                    if (validar()) {
                        $.ajax({
                            url: 'modificarProveedorAjax.php',
                            data: $("#frm").serialize(),
                            type: 'POST',
                            beforeSend: function () {
                                $.blockUI({
                                    message: "<p class='centrar_cuadro_alertas'>Grabando, por favor espere...<img src='../img/loading.gif' width='200' height='140' /></p>"
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
            function cerrar() {
                var prov = document.cookie.replace(/(?:(?:^|.*;\s*)prov\s*\=\s*([^;]*).*$)|^.*$/, "$1");//obtener si se creó proveedor desde cookie
                if (prov == "1") {
                    var rutpadre = window.opener.document.getElementById('rutpv2').value;
                    var dvpadre = window.opener.document.getElementById('dvpv2').value;
                    window.opener.document.getElementById("rutpv").value = rutpadre;
                    window.opener.document.getElementById("dv").value = dvpadre;
                    window.opener.document.getElementById("provfic").style.display = "none";
                    window.opener.document.getElementById("rutpv").focus();
                    this.window.close();
                } else {
                    this.window.close();
                }
            }
        </script>
    </head>
    <body class="fondo">
        <form name="frm" id="frm" method="post" autocomplete="off">
            <table class="table">
                <tr>
                    <td class="texto" colspan="2"><img class="imgico" alt='Proveedor' title='Proveedor' src='../img/suply.png'>&nbsp;&nbsp;&nbsp;<?php echo $boton1; ?></td>
                </tr>
                <tr>
                    <td class="texto1" width="340">RUT:</td>
                    <td class="texto6"><input type="text" name="txtrut" id="rut" onkeyup="javascript:dgv(window.document.frm.rut.value)" onchange="buscarProv(this.value);" class="caja_color" value="<?= $cod ?>" autofocus size="8" maxlength="8" <?php echo $lectura; ?> required>&nbsp;&nbsp;-&nbsp;&nbsp;<input type="text" name="txtdv" id="dv" class="caja_color" value="<?= $row["prov_dv"] ?><?= $dv ?>" autofocus size="1" maxlength="1" readonly="readonly"/>* Sin puntos, guión, ni Dígito Verificador&nbsp;&nbsp;&nbsp;<img class="imgnormal" src="../img/check.png" id="ticket" hidden="yes" title="El RUT ingresado esta correcto"></td>
                </tr>
                <tr>
                    <td class="texto1">Raz&oacute;n Social:</td>
                    <td><textarea name="txtnombre" id="nombre" class="caja_negrita" cols="65" rows="3" required><?= $row["prov_nombre"] ?><?= $nom ?></textarea></td>
                </tr>
                <tr>
                    <td class="texto1">Giro:</td>
                    <td><textarea name="txtgiro" id="giro" class="caja" cols="65" rows="2" ><?= $row["prov_giro"] ?><?= $giro ?></textarea></td>
                </tr>
                <tr>
                    <td class="texto1">Direcci&oacute;n:</td>
                    <td><input type="text" name="txtdir" class="caja" id="dir" value="<?= $row["prov_direccion"] ?><?= $dir ?>" size="50"/></td>
                </tr>
                <tr>
                    <td class="texto1">Nombre Representante:</td>
                    <td><input type="text" id="nomrep" name="txtnomrep" class="caja" value="<?= $row["prov_nomcontacto"] ?><?= $cont ?>" size="50"/></td>
                </tr>
                <tr>
                    <td class="texto1">E-Mail:</td>
                    <td><input type="text" name="txtmail" class="caja_color" id="mail" value="<?= $row["prov_email"] ?><?= $mail ?>" size="50"/></td>
                </tr>
                <tr>
                    <td class="texto1">Tel&eacute;fono:</td>
                    <td><input type="text" name="txtfono" class="caja" id="fono" value="<?php
                        if ($row["prov_telefono"] == "") {
                            $flg = "hidden='yes'";
                        } else {
                            echo $row["prov_telefono"];
                        }
                        ?><?= $fono ?>" size="12" maxlength="12"/>&nbsp;&nbsp;&nbsp;<a href="tel:<?= $row["prov_telefono"] ?>" <?= $flg ?>><img class='imgnormal' alt='tel' title='Llamar' src='../img/tel1.png'></a></td>
                </tr>
                <tr>
                    <td class="texto1">Estado Proveedor:</td>
                    <td><select name="txtestado" class="caja">
                            <?php
                            if ($option == "1") {
                                $option = 1;
                            } else {
                                $option = number_format($row["prov_estado"]);
                            }
                            $y = 0;
                            while ($row1 = mysql_fetch_array($res1)) {
                                if ($y == $option) {
                                    echo "<option value='" . $option . "'selected>‌" . $row1["estado_nombre"] . "</option>";
                                } else {
                                    echo"<option value=", $y, ">", $row1["estado_nombre"], "</option>";
                                }
                                $y++;
                            }
                            ?>                            
                        </select>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <span class="texto1" <?php echo $oculto; ?>>Fecha Ultima Modificación: &nbsp;<?= date("d/m/Y", strtotime($row["prov_fechacreacion"])) ?></span>
                        <input type='hidden' name='txtpagina' value='<?php echo $pagina; ?>' />
                        <input type='hidden' name='optionpgn' value='<?php echo $optpag1; ?>' />
                    </td>
                </tr>
                <tr>
                    <td colspan="2" class="texto5">
                <center>
                    <button type="submit" class="boton"><?php echo $boton; ?>&nbsp;<img class="img" alt="Grabar" title="Grabar" src="../img/edit2.png"></button>
                    <?php echo $btnoc; ?>
                    &nbsp;&nbsp;&nbsp;&nbsp;<?php echo $optpag; ?>&nbsp;<img class="img" alt="Salir" title="salir de la pantalla de edicion" src="../img/salir.png"></button> 
                    
                </center>
                </td>
                </tr>
            </table>
        </form>
        <div id="resultado"></div>
    </body>
    <?php
    mysql_close($link);
    ?>
</html>