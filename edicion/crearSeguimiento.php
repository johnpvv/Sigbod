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

$link = Conectarse(); //Variable de coneccion
$res2 = mysql_query("SELECT * FROM usuarios where usuario_estado='1' ORDER BY usuario_nombre", $link);
$fecha = $date = date("Y-m-d");
$fecha1 = $date = date("d/m/Y");
$comilladob = '"';
$comillasim = "'";
?>
<html>
    <head>
        <link rel="stylesheet" href="../css/tabla.css" />
        <link rel="stylesheet" href="../css/estilos.css" />
        <link rel="shortcut icon" href="../img/favicon.ico" />   
        <script src="../js/jquery-2.2.4.js" type="text/javascript"></script>
        <script src="../js/jquery.blockUI.js" type="text/javascript"></script>
        <script type="text/javascript">
            function agregarFila() {
                var table = document.getElementById("tabla");
                var cont = table.rows.length;
                var cuenta = parseInt($("#contador").val());
                var id = "txtdoc_" + (cuenta + 1);
                $("#contador").val(cuenta + 1);
                document.getElementById("tabla").insertRow(cont - 1).innerHTML =
                        '<tr><td class="ancho"><input type="text" class="textoredondo0" size="1" value="' + (cuenta + 1) + '" readonly>&nbsp;<input type="text" class="caja2c" size="7" id="txtdoc_' + (cuenta + 1) + '" name="txtdoc_' + (cuenta + 1) + '" onChange="buscarDuplicado(this.value,this.id)" onSelect="blur();" onkeypress="if (event.which == 13) event.returnValue = false, insertar(this.value);"  maxlength="8" placeholder="Digite ID.." required>&nbsp;<img class="imgpeq" id="img_' + (cuenta + 1) + '" alt="buscar" title="buscar" src="../img/lupan.png" onclick="buscar(this.id)"></td>' +
                        '<td align="left"><span id="txtglosa_' + (cuenta + 1) + '"></span></td>' +
                        '<td style="width:10px;"><button type="button" class="boton1" id="eliminar_' + (cuenta + 1) + '" title="Eliminar Fila" onclick="eliminarFila(this.parentNode.parentNode.rowIndex)"><b>-</b></button></td></tr>';
                document.getElementById(id).focus();
            }
        </script>
        <script type="text/javascript"> //consultar datos y autollenar campos del codigo
            $(document).ready(function () {
                $("#agregar").click(function () {
                    $("#mostrar").css("display", "none");
                    var i = ($("#contador").val());//determinar posicion en la fila de la tabla
                    $("#txtdoc_" + i).on("blur", function blur() {//activar funcion al sacar el foco del input del id y nombre de funcion                        
                        var valor = $(this).val(); //sacar el valor del input del codigo                        
                        if (valor != "") {
                            $.ajax({
                                url: "../herramientas/buscarDocID.php",
                                type: "POST",
                                dataType: "json",
                                data: {val: valor},
                                success: function (res) {//objeto que trae los parametros json elegidos
                                    if (res.numdoc == null) {
                                        $("#txtdoc_" + i).val("");
                                        $("#txtglosa_" + i).val("");
                                        $("#txtdoc_" + i).focus();
                                        alert("Error...\nEl ID ingresado: " + valor + " , NO Existe");
                                    } else {
                                        $("#txtglosa_" + i).text(res.tipo + " N° " + res.numdoc + ", De: " + res.prov + ", OC: " + res.oc + ", Estado: " + res.est);//asignar los valores a los input elegidos dinamicos
                                        $("#txtdoc_" + i).attr("readonly", true);
                                    }
                                }
                            });
                        }
                    });
                });
            });
        </script>
        <script type="text/javascript">
            function buscar(id) {
                var idx = id.replace("img", "txtdoc");
                document.getElementById("id").value = idx;
                var win = window.open('../reportes/listarBuscaDoc.php', '_blank', 'scrollbars=yes,statusbar=no,resizable=no,width=720,height=480 left=300,top=50');

                var winClosed = setInterval(function () {//pára llamar una funcion cuando se cierra ventana hija
                    if (win.closed) {
                        clearInterval(winClosed);
                        actualiza();
                    }
                }, 1000);
            }
        </script>
        <script type="text/javascript">
            function eliminarFila(i) {
                var valor = document.getElementById("contador").value;
                var table = document.getElementById("tabla");
                if (valor === 1) {
                    alert("ERROR, NO PUEDE BORRAR LA ULTIMA FILA");
                } else {
                    table.deleteRow(i);
                    var x = 0;
                    $(".caja2c").each(function () {
                        x++;
                    });
                    if (x == 0) {
                        alert("Se Han Borrado todos los Elementos...");
                        $("#mostrar").css("display", "block");
                        ;
                    }
                }
            }
        </script>
        <script type="text/javascript"> //consultar datos y autollenar campos del rutprov
            function buscarDuplicado(x, i) {
                $(document).ready(function () {
                    var cont = 0;
                    $(".caja2c").each(function () {
                        if ($(this).val() == x) {
                            cont++;
                        }
                    });
                    if (cont > 1) {
                        alert("El documento ingresado, con el ID: " + x + " Ya esta en la Lista, favor revisar.");
                        document.getElementById(i).value = "";
                        document.getElementById(i).focus();
                    }
                });
            }
        </script>
        <script type="text/javascript">
            function borrar() {
                if (confirm("¿Esta Seguro que quiere borrar este Documento del seguimiento Actual?"))
                    return true;
                else
                    return false;
            }
        </script>
        <script type="text/javascript">
            function actualiza() {
                $('#carga').attr("src", "../img/load.gif");
                $(".caja2c").each(function () {
                    if ($(this).val() !== "") {
                        $(this).select();
                    }
                });
                setInterval(ocultar, 2000);
            }
            function ocultar() {
                $('#carga').attr("src", "../img/reload.png");
            }
        </script>
        <script type="text/javascript">
            function cambiaEst() {
                var estado = $("#txtest").prop("checked");
                if (estado) {
                    $("#txtest").val(0);
                } else {
                    $("#txtest").val(1);
                }
            }
        </script>
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
            $(document).ready(function (e) {
                $("#frm").on('submit', (function (e) {
                    e.preventDefault();
                    if ($("#contador").val() == 0) {
                        alert("No se Puede Grabar...Debe existir mínimo un documento asociado.");
                    } else {
                        var data = new FormData(this); // <-- 'this' is your form element
                        $.ajax({
                            url: 'crearSeguimientoAjax.php',
                            data: data,
                            cache: false,
                            contentType: false,
                            processData: false,
                            type: 'POST',
                            beforeSend: function () {
                                $.blockUI({
                                    message: "<p class='centrar_cuadro_alertas'>Grabando, por favor espere...<img src='../img/ajax-loading.gif' width='280' height='180' /></p>"
                                });
                            },
                            error: function () {
                                alert("error peticion ajax");
                            },
                            success: function (data) {
                                $("#resultado").empty();
                                $("#resultado").append(data);
                                $("html, body").animate({scrollTop: 0}, 600);
                                $.unblockUI();
                            }
                        });
                    }
                }));

            });
        </script>
        <script type="text/javascript">
            function check() {
                $("#check").show(1000);
                $('.check').show("slow");
            }
        </script>
        <script type="text/javascript">
            function insertar(val) {
                if (val !== "") {
                    agregar.click();
                }
            }
        </script>
        <script type="text/javascript">
            //window.history.forward();
        </script>
        <script type="text/javascript">
            function llenarFecha() {
                var id = "txtdescrip";
                var lleno = document.getElementById("llena").value;
                if (lleno == 0) {
                    var texto = document.getElementById(id).value;
                    texto = texto + "\n<?= $fecha1 ?>" + ": ";
                    document.getElementById(id).value = texto.trim() + " ";
                    $("#" + id).animate({scrollTop: $("#" + id)[0].scrollHeight - $("#" + id).height()}, 1000);
                    document.getElementById("llena").value = 1;
                }
            }
        </script>
        <?php
        $idseg = $_GET['idseg'];
        $tipo = $_GET["tipo"];
        $boton = "";
        if ($idseg === NULL) {
            $boton = "Crear Seguimiento";
            $title = "CREAR NUEVO SEGUIMIENTO";
            $pagina = "crea";
            $focus = "autofocus";
            $contador = 0;
            $texto = "";
            $check = "hidden";
            $pdfbtn = "hidden";
            $est = "1";
        } else {
            $boton = "Modificar Seguimiento";
            $title = "MODIFICACIÓN SEGUIMIENTO";
            $pagina = "modifica";
            $texto = "Cerrar Seguimiento";
            $pdfbtn = "";
            $sql = "SELECT * FROM seguimiento WHERE (seg_id='$idseg')";
            $sql1 = "SELECT * FROM seguimiento_detalle WHERE seg_id='$idseg' ORDER BY seg_id_doc";
            $res = MySQL_query($sql, $link)or die(mysql_error());
            $contar = mysql_num_rows($res);
            if ($contar == 0) {
                echo '<script>alert("Error...\nLos Datos Ingresados son invalidos, favor intente Nuevamente.");	history.back(-1);</script>';
            }
            $row = MySQL_Fetch_array($res);
            $contador = $row["seg_cant"];
            $est = $row["seg_estado"];
            if ($est == 0) {
                $check = "checked";
                $valfec = "disabled";
            } else {
                $check = "";
                $valfec = "";
            }
            $res1 = MySQL_query($sql1, $link)or die(mysql_error());
            $contar1 = mysql_num_rows($res1);
            $_SESSION['query'] = $sql;
        }
        ?>        
        <title>SigBod - <?php echo $boton; ?></title>
    </head>
    <body class="fondo" id="grupo">
        <form id="frm" name="frm" method='post' enctype="multipart/form-data">
            <table class="table3a">
                <tr>
                    <td class="texto" colspan="2"><?php echo $title; ?></td>
                </tr>
                <tr>
                    <td class="texto1">ID Seguimiento:</td>
                    <td class="texto5"><input type="text" name="txtid" id="txtid" class="caja2e" value="<?= $row["seg_id"] ?>" size="3" readonly="readonly" maxlength="8" onKeypress="if (event.which === 13)
                                event.returnValue = false;"/>
                        <img class="imgnormal" id="check" src="../img/check.png" alt="ok" hidden>&nbsp;&nbsp;&nbsp;
                        <input type="checkbox" value="<?= $est ?>" id="txtest" name="txtest" onchange="cambiaEst()" <?= $check ?>> <?= $texto ?>
                    </td>
                </tr>
                <tr>
                    <td class="texto1">Nombre Seguimiento:</td>
                    <td><textarea name="txtnombre" id="txtnombre" class="caja_negrita" cols="110" rows="1" required <?= $focus ?>><?= $row["seg_nombre"] ?></textarea></td>
                </tr>
                <tr>
                    <td class="texto1">Descripci&oacute;n Ampliada:</td>
                    <td><textarea name="txtdescrip" id="txtdescrip" class="caja_color" cols="110" rows="6" style="overflow: auto;" required onclick="llenarFecha()"><?= $row["seg_descrip"] ?></textarea></td>
                </tr>
                <tr>
                    <td class="texto1">Fecha de Vencimiento:</td>
                    <td class="texto5"><input type="date" name="txtven" id="txtven" class="texto5" value="<?= $row["seg_fven"] ?>" min="<?= $fecha ?>" required <?= $valfec ?>/>
                </tr>
                <tr>
                    <td class="texto1">Responsable:</td>
                    <td class="texto5">
                        <select name="txtresp" id="txtresp" class="texto5" required>
                            <?php
                            $option = number_format($row["seg_resp"]);
                            if ($option == 0) {
                                echo'<option value="" selected>-- Elija Usuario --</option>';
                            }
                            while ($row2 = mysql_fetch_array($res2)) {
                                if ($row2["usuario_id"] == $option) {
                                    $sel = "selected";
                                } else {
                                    $sel = "";
                                }
                                echo "<option value='" . $row2["usuario_id"] . "' " . $sel . ">‌" . $row2 ["usuario_nombre"] . ' ' . $row2["usuario_apellidos"] . "</option>";
                            }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="texto1">Documentos Asociados al Seguimiento:</td>
                    <td class="texto5">
                        <?php
                        $seg = $_SESSION['seg'];
                        if ($seg == $idseg) {
                            echo "<script>check();</script>";
                            $disab = "disabled style='background:black;cursor:no-drop;'";
                        }
                        if ($contar1 != 0) {
                            $cuenta = 1;
                            echo "<table class='table0a' id='tabla'>";
                            while ($row1 = mysql_fetch_array($res1)) {
                                $id_doc = $row1["seg_id_doc"];
                                $sql3 = "SELECT * FROM documento, proveedores WHERE doc_id='$id_doc' AND doc_rutpv=prov_rut";
                                $res3 = MySQL_query($sql3, $link)or die(mysql_error());
                                $row3 = MySQL_Fetch_array($res3);
                                $estado = $row3["doc_estado"];
                                if ($estado == "Anulado") {
                                    $data = "'A.'";
                                    $bgcolor = "green";
                                } else if ($estado == "Recepcionado") {
                                    $data = "'R.'";
                                    $bgcolor = "blue";
                                } else if ($estado == "Devuelto") {
                                    $data = "'D.'";
                                    $bgcolor = "brown";
                                } else {
                                    $data = "'P.'";
                                    $bgcolor = "red";
                                }
                                echo "<tr><td class='ancho'><input type='text' class='textoredondo0' size='1' value='" . $cuenta . "'readonly>&nbsp;<input type='text' id='txtdoc_" . $cuenta . "' class='caja2c' size='7' value='" . $id_doc . "' readonly>";
                                echo "<a href='../herramientas/buscarSubdocumento.php?id=" . $id_doc . "&tipo=1' target='_blank' title='Detalle documentos' onclick=" . $comilladob . "window.open(this.href, this.target, " . $comillasim . "height=620, width=840 left=250 top=20" . $comillasim . "); return false;" . $comilladob . "><img border='0' alt='subdoc' title='Detalle documento' src='../img/eye.png' class='img'></a>";
                                echo "<td>" . $row3["doc_tipodoc"] . " N° " . $row3["doc_ndoc"] . ", De: " . $row3["prov_nombre"] . ", OC: " . $row3["doc_noc"] . ", <b style='color:" . $bgcolor . ";'>Estado: " . $data . "</b></td>";
                                echo '<td style="width:10px;"><a href="borrarDocSeg.php?id=' . $row1["seg_id_det"] . '&cont=' . $row["seg_cant"] . '&doc=' . $row1["seg_id_doc"] . '&idseg=' . $row["seg_id"] . '" class="boton1" title="Eliminar Registro" id="eliminar_' . $cuenta . '" onclick="return borrar();"><b>-</b></a></td></tr>';
                                $cuenta++;
                            }
                            echo'<tr><td colspan="3" class="texto1b"><button type="button" class="boton1a" id="agregar" title="Añadir Documento" onclick="agregarFila()"><b>&nbsp;&nbsp;+&nbsp;&nbsp;</b></button></td></tr></table>';
                        } else {
                            echo "<table class='table0a' id='tabla'>";
                            echo'<tr><td colspan="3" class="texto1b"><button type="button" class="boton1a" id="agregar" title="Añadir Documento" onclick="agregarFila()"><b>&nbsp;&nbsp;+&nbsp;&nbsp;</b></button><br><span id="mostrar">No hay Documentos Asociados Aún</span></td></tr></table>';
                        }
                        ?>
                    </td>                               
                </tr>
                <tr>
                    <td colspan="2" style="text-align:center;"><button class="boton" type="button" id="act" onclick="actualiza();">Actualizar&nbsp;<img class="img" alt="recargar" id="carga" title="recargar" src="../img/reload.png"></button>&nbsp;&nbsp;&nbsp;&nbsp;<button class="boton" type="submit" <?php echo $disab ?>><?php echo $boton; ?>&nbsp;<img class="img" alt="Grabar" title="Grabar" src="../img/edit1.png"></button>&nbsp;&nbsp;&nbsp;&nbsp;<button class="boton" type="button" onclick="location.href = '../herramientas/buscarSeguimiento.php'">Ir a Seguimientos&nbsp;<img class="img" alt="volver" title="Volver atras" src="../img/undo.png"></button>&nbsp;&nbsp;&nbsp;&nbsp;<button class="boton" type="button" onclick="window.location = '../principal.php'">Menu Principal&nbsp;&nbsp;<img class='img' alt='volver' title='Volver a pantalla principal' src='../img/casa.png'></button>&nbsp;&nbsp;&nbsp;&nbsp;<button id="capturar" type="button" class="boton" onclick="window.open('../reportes/SeguimientoPDF.php?id=<?= $idseg ?>', '_blank', 'scrollbars=yes,statusbar=no,resizable=no,width=860,height=620 left=200,top=30')" <?= $pdfbtn ?>>Generar PDF&nbsp;<img class="img" alt="pdf" title="Generar reporte PDF" src="../img/pdf1.png"></button>&nbsp;&nbsp;&nbsp;&nbsp;<button type="button" onclick="window.location = '../exportar/exportaExcelSeg.php'" class="boton" <?= $pdfbtn ?>>Exportar a Excel&nbsp;&nbsp;<img class='img' alt='exportar' title='Exportar a Excel los datos mostrados en pantalla' src='../img/excel3.png'></button>
                    </td>
                    <?php
                    mysql_close($link);
                    ?>
                </tr>
            </table>
            <input type='hidden' name='txtpag' value='<?php echo $pagina; ?>'>
            <input type='hidden' name='txttipo' value='<?php echo $tipo; ?>'>
            <input type='hidden' name='contador' id='contador' value='<?php echo $contador ?>'>
            <input type='hidden' name='id' id='id' value=''> 
            <input type='hidden' name='llena' id='llena' value='0'> 
        </form>
        <div id="resultado"></div>
        <a href="javascript:void(0);" id="scroll" title="Ir Arriba" style="display: none;">Arriba<span></span></a>
    </body>
</html>