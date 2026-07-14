<!DOCTYPE html>
<!--
Sigbod John Vaccarella
-->
<html>
    <head>
        <link rel="stylesheet" href="../css/tabla.css" />
        <link rel="stylesheet" href="../css/estilos.css" />
        <link rel="shortcut icon" href="../img/favicon.ico" />   
        <script src="../js/jquery-2.2.4.js" type="text/javascript"></script>
        <script src="../js/jquery.blockUI.js" type="text/javascript"></script>		
        <?php
        include_once("../include/conn.php");
        session_start();
        if (!isset($_SESSION['usuario'])) {
            header('Location: ../index.php');
            exit();
        }
        if ($_SESSION['perfil'] > 1) {
            echo '<script>alert("Su Perfil no permite la visualizacion de esta pantalla, Favor contacte al Administrador.");history.back(-1);</script>';
            echo '<center><button class="boton" onclick="javascript:window.close()">Salir&nbsp;<img class="img" alt="Salir" title="salir de la pantalla de edicion" src="../img/salir.png"></button></center>';
            exit();
        }
        $link = Conectarse();
        $cod = $_GET['id'];
        $tipo = $_GET["tipo"];
        $lectura = "";
        $boton = "";
        $option = "";
        $comilladob = '"';
        $comillasim = "'";
        if ($tipo == "1") {
            $tipobtn = '<button class="boton" type="button" onclick="javascript:window.close()">Salir&nbsp;<img class="img" alt="Salir" title="salir de la pantalla de edicion" src="../img/salir.png"></button>';
        } else if ($tipo == "2") {
            $tipobtn = '<button class="boton" type="button" onclick="javascript:history.go(-2)">Volver&nbsp;<img class="img" alt="volver" title="Volver a la pantalla anterior" src="../img/undo.png"></button>';
        } else {
            $tipobtn = '<button class="boton" type="button" onclick="javascript:history.back(-1)">Volver&nbsp;<img class="img" alt="volver" title="Volver a la pantalla anterior" src="../img/undo.png"></button>';
        }
        if ($cod === NULL) {
            $lectura = "";
            $boton = "Crear Art&iacute;culo";
            $title = "Creación de Art&iacute;culos";
            $pagina = "crea";
            $option = "1";
            $option1 = "1";
            $oculto = "hidden";
            $sql1 = "SELECT * FROM estado";
            $res1 = MySQL_query($sql1, $link)or die(mysql_error());
            $sql2 = "SELECT * FROM unimed ORDER BY unimed_nombre";
            $res2 = MySQL_query($sql2, $link)or die(mysql_error());
            $res3 = MySQL_query($sql2, $link)or die(mysql_error());
        } else {
            $lectura = "readonly";
            $boton = "Modificar Art&iacute;culo";
            $title = "Modificación de Art&iacute;culos";
            $pagina = "modifica";
            $oculto = "";
            $sql = "SELECT * FROM productos, unimed WHERE (prd_codigo='$cod') AND prd_unimed=unimed_id";
            $sql1 = "SELECT * FROM estado";
            $res = MySQL_query($sql, $link)or die(mysql_error());
            $contar = mysql_num_rows($res);
            if ($contar == 0) {
                echo '<script>alert("Error...\nEl codigo ingresado es invalido, o se encuentra Inactivo");
                window.close();</script>';
            }
            $row = MySQL_Fetch_array($res);
            $res1 = MySQL_query($sql1, $link)or die(mysql_error());
            $sql2 = "SELECT * FROM unimed ORDER BY unimed_nombre";
            $res2 = MySQL_query($sql2, $link)or die(mysql_error());
            $res3 = MySQL_query($sql2, $link)or die(mysql_error());
            $diarepo = $row["prd_diarepo"];
        }
        ?>        
        <title>SigBod - <?php echo $boton; ?></title>
        <script type="text/javascript">
            function goUpdatecod() {
                var precio = $("#precio").val();
                var cod = $("#cod").val();
                var glosa = $("#glosa").val();
                var unimed = $("#unimed").val();
                var precioNum = parseInt(precio);
                if (cod === "" || cod.length <= 7) {
                    alert("Debe Introducir Un Codigo Valido Ej. 123-4567");
                    document.frm.txtcod.focus();
                    return 0;
                }
                if (glosa === "") {
                    alert("Debe Introducir Una Glosa");
                    document.frm.txtglosa.focus();
                    return 0;
                }
                if (unimed === "0") {
                    alert("Debe introducir la unidad de Medida");
                    document.frm.txtunimed.focus();
                    document.getElementById("unimed").style.background = "red";
                    return 0;
                }
                if (isNaN(precioNum) || precioNum === "") {
                    alert("Debe Introducir Un Numero Valido");
                    document.frm.txtprecio.focus();
                    return 0;
                } else {
                    frm.action = "../edicion/ModificarArticuloBD.php";
                    frm.submit();
                }
            }
        </script>
        <script>
            function confirmar() {
                if (document.getElementById('destacado').checked === false) {//obtener si el check nombre destacado esta marcado o no
                    if (confirm("Si desactiva esta casilla, no se verificarán los niveles de stock del articulo")) {
                        $("#destacado").prop('checked', false);
                    } else {
                        $("#destacado").prop('checked', true);
                    }
                }
            }
        </script>
        <script type="text/javascript"> //consultar datos y autollenar campos 
            $(document).ready(function () {
                $('#ticket').hide();
                $("#cod").change(function () {//activar funcion al sacar el foco del input del codigo
                    var valor = $(this).val(); //sacar el valor del input del codigo
                    $.ajax({
                        url: "../herramientas/buscaCod.php",
                        type: "POST",
                        dataType: "json",
                        data: {val: valor},
                        success: function (res) {//objeto que trae los parametros json elegidos
                            if (res.glosa !== null) {
                                if (confirm("El codigo ingresado: " + valor + " ya existe en el Sistema, ¿Desea Editarlo?")) {
                                    window.location = "modificarArticulo.php?id=" + valor + "&tipo=2";
                                } else {
                                    $("#cod").val("");
                                    $('#ticket').hide();
                                }
                            } else {
                                $('#ticket').show();
                            }
                        }
                    });
                });
            });
        </script>
        <script type="text/javascript"> //consultar codigo de barras
            $(document).ready(function () {
                var codigo = $("#cod").val();
                $("#" + codigo).click(function () {
                    $('#pantallaprd').block({message: null});
                    window.open("../herramientas/codigoBarra.php?id=" + codigo, "Sigbod - Codigo de Barras", "width=415,height=130 left=500 top=300,scrollbars=NO");
                });
            });
        </script>
        <script type="text/javascript">
            function desbloquea() {
                location.reload();
                $('#pantallaprd').unblock();
            }
        </script> 
        <script type="text/javascript">
            function validaEstado() {
                var cod = document.getElementById("cod").value;
                $.ajax({
                    url: "../herramientas/buscaStock.php",
                    type: "POST",
                    dataType: "json",
                    data: {cod: cod},
                    success: function (res) {//objeto que trae los parametros json elegidos
                        if (res.stock !== "0") {
                            alert("Error...\nEl Codigo: '" + cod + "' , Tiene: " + res.stock + " Unidades en stock, No se puede Desactivar");
                            document.getElementById("estado").value = "1";
                        }
                    }
                });
            }
        </script>
    </head>
    <body class="fondo" id="pantallaprd">
        <form id="frm" name="frm" action="" method="post" enctype="multipart/form-data" onKeypress="if (event.keyCode == 13)
                    event.returnValue = false;">
            <table class="table">
                <tr>
                    <td class="texto" colspan="2"><img class="imgico" alt='Productos' title='Productos' src='../img/art.png'>&nbsp;&nbsp;&nbsp;<?php echo $title; ?></td>
                </tr>
                <tr>
                    <td class="texto1">C&oacute;digo Interno:</td>
                    <td class="texto5"><input type="text" name="txtcod" id="cod" class="caja_color" value="<?= $row["prd_codigo"] ?>" autofocus size="10" <?php echo $lectura; ?> maxlength="8"/>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="destacado" id="destacado" onchange="confirmar()" 
                        <?php
                        if ($row["prd_destacado"] == 1) {
                            echo " checked";
                        }
                        ?>/>
                        Producto Destacado&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <img class="imgnormal" src="../img/check.png" id="ticket" title="El codigo ingresado esta disponible"> 
                        <?php
                        if ($row["prd_imagen"] == "") {
                            $imagen = "";
                        } else {
                            $imagen = "<a href=../herramientas/visorImagen.php?id=" . $row["prd_codigo"] . "&tipo=1 target='_blank' title='Ver Imagen' onclick=" . $comilladob . "window.open(this.href, this.target, " . $comillasim . "height=600, width=720 left=300 top=30" . $comillasim . "); return false;" . $comilladob . "><img class='imgico' alt='imagen' title='ver imagen' src='../img/cam.png'></a>.
                            &nbsp;&nbsp;<a href=../herramientas/visorImagen.php?id=" . $row["prd_codigo"] . "&tipo=2><img class='imgborra' title='Borrar imagen' src='../img/trash.png'><a>";
                        }
                        ?>
                        <?= $imagen ?>                        
                    </td>
                </tr>
                <tr>
                    <td class="texto1">Glosa:</td>
                    <td><textarea name="txtglosa" id="glosa" class="caja_negrita" cols="60" rows="4"><?= $row["prd_glosa"] ?></textarea></td>
                </tr>
                <tr>
                    <td class="texto1">Descripci&oacute;n Ampliada:</td>
                    <td><textarea name="txtglosaam" id="glosaam" class="caja_color" cols="60" rows="3"><?= $row["prd_glosaamp"] ?></textarea></td>
                </tr>
                <tr>
                    <td class="texto1">Unidad de Medida:</td>
                    <td><select name="txtunimed" class="caja" id="unimed">
                            <?php
                            $option1 = number_format($row["unimed_id"]);
                            while ($row2 = mysql_fetch_array($res2)) {
                                if ($row2["unimed_id"] == $option1) {
                                    $sel = "selected";
                                } else {
                                    $sel = "";
                                }
                                echo "<option value='" . $row2["unimed_id"] . "' " . $sel . ">‌" . $row2 ["unimed_nombre"] . "</option>";
                            }
                            ?>                            
                        </select>     
                        <span  class="texto1">&nbsp;Unidad Presentación&nbsp;
                            <select name="txtunimedprov" class="caja" id="unimedprov">
                                <?php
                                $option2 = number_format($row["prd_prov_unimed"]);
                                while ($row3 = mysql_fetch_array($res3)) {
                                    if ($row3["unimed_id"] == $option2) {
                                        $sel = "selected";
                                    } else {
                                        $sel = "";
                                    }
                                    echo "<option value='" . $row3["unimed_id"] . "' " . $sel . ">‌" . $row3["unimed_nombre"] . "</option>";
                                }
                                ?>                            
                            </select>
                        </span>
                </tr>
                <tr>
                    <td class="texto1">Referencia Proveedor:</td>
                    <td><input type="text" name="txtref" class="caja" id="ref" value="<?= $row["prd_ref"] ?>" size="50"/></td>
                </tr>
                <tr>
                    <td class="texto1">ID Convenio Marco:</td>
                    <td><input type="text" name="txtcodcm" class="caja" id="cm" value="<?php
                        $cm = $row["prd_codcm"];
                        if ($cm == "") {
                            echo '" size="10"/>';
                        } else {
                            echo $row["prd_codcm"];
                            ?>" size='10'/>&nbsp;<?php
                                   echo "<a class='boton1' href='http://www.mercadopublico.cl/TiendaFicha/Ficha?idProducto=" . $row['prd_codcm'] . "' target='_blank'>Ver en CM.</a>";
                               }
                               ?>
                        <span  class="texto1">&nbsp;Imagen:&nbsp;<input type="file" name="imagen" id="imagen" class="boton1b" accept=".jpg"/></span></td>

                </tr>
                <tr>                
                    <td class="texto1">C&oacute;digo CENABAST:</td>
                    <td><input type="text" name="txtcen" class="caja" id="cenabast" value="<?= $row["prd_cenabast"] ?>" size="15"/>
                        &nbsp;&nbsp;<b class="texto1">Código de Barras:</b>
                        <?php
                        if ($row["prd_barcode"] == "") {
                            echo '<img class="imgnormal" src="../img/error.png" id="' . $cod . '" title="codigo de barras NO cargado">';
                        } else {
                            echo '<input type="text" name="barcode" class="caja" id="barcode" value="' . $row["prd_barcode"] . '" size="15" readonly />&nbsp;&nbsp;&nbsp;<img class="imgnormal" src="../img/check.png" id="' . $row["prd_codigo"] . '" title="codigo de barras cargado OK">';
                        }
                        ?>
                    </td>
                </tr>
                <tr>
                    <td class="texto1">Precio Neto:&nbsp;$</td>
                    <td>
                        <input type="text" id="precio" name="txtprecio" class="caja" value="<?= $row["prd_precio"] ?>" size="15"/>
                        &nbsp;&nbsp;&nbsp;&nbsp;
                        <span class="texto1">Día de Reposición:&nbsp;
                            <select name="diarepo" class="caja">
                                <?php
                                $w = 0;
                                if ($diarepo == 0) {
                                    $diarepo = "Elija dia...";
                                }
                                while ($w <= 30) {
                                    if ($diarepo == $w) {
                                        echo "<option value='" . $diarepo . "' selected>‌" . $diarepo . "</option>";
                                    } else {
                                        if ($w == 0) {
                                            echo"<option value=" . $w . ">Borrar dia...</option>";
                                        } else {
                                            echo"<option value=" . $w . ">" . $w . "</option>";
                                        }
                                    }
                                    $w++;
                                }
                                ?>    
                            </select>
                        </span>
                    </td>
                </tr>
                <tr>
                    <td class="texto1">Estado Art&iacute;culo:</td>
                    <td>
                        <select name="txtestado" class="caja" id="estado" onchange="validaEstado()">
                            <?php
                            if ($option == "1") {
                                $option = 1;
                            } else {
                                $option = number_format($row["prd_estado"]);
                            }
                            $y = 0;
                            while ($row1 = mysql_fetch_array($res1)) {
                                if ($y == $option) {
                                    echo "<option value='" . $option . "' selected>‌" . $row1 ["estado_nombre"] . "</option>";
                                } else {
                                    echo"<option value=", $y, ">", $row1["estado_nombre"], "</option>";
                                }
                                $y++;
                            }
                            ?>                            
                        </select>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <span class="texto1" <?php echo $oculto; ?>>Fecha Ultima Modificación: <input type="text" id="fecha" name="txtfecha" class="caja" readonly value="<?= date("d/m/Y, H:i", strtotime($row["prd_fecha"])) ?>" size="14"/></span>
                        <input type='hidden' name='txtpagina' value='<?php echo $pagina; //envia si la pagina es de creacion de articulo o modificacion         ?>' />
                        <input type='hidden' name='txttipo' value='<?php echo $tipo; //envia si la pagina es de creacion de articulo o modificacion         ?>' />
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="text-align: center;">
                        <button type="button" class="boton" onclick="goUpdatecod()"><?php echo $boton; ?>&nbsp;<img class="img" alt="Salir" title="Grabar" src="../img/edit1.png"></button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;                        
                        <button type="button" class="boton" onclick="window.open('../reportes/fichaProdPDF.php?id=<?= $row["prd_codigo"] ?>', '_blank', 'scrollbars=yes,statusbar=no,resizable=no,width=960,height=640 left=150,top=20')">Generar Ficha en PDF&nbsp;&nbsp;<img class="img" alt="pdf" title="Genera Ficha en formato PDF" src="../img/pdf1.png"></button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <?php echo $tipobtn ?>
                    </td>
                    <?php
                    mysql_close($link);
                    ?>
                </tr>
            </table>
        </form>
    </body>
</html>