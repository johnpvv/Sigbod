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
if ($_SESSION['perfil'] > 1) {
    echo '<script>alert("Su Perfil no permite la visualizacion de esta pantalla, Favor contacte al Administrador.");history.back(-1);</script>';
    echo '<center><button class="boton" onclick="javascript:window.close()">Salir&nbsp;<img class="img" alt="Salir" title="salir de la pantalla de edicion" src="../img/salir.png"></button></center>';
    exit();
}
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
                var id = "txtcod_" + (cuenta + 1);
                $("#contador").val(cuenta + 1);
                document.getElementById("tabla").insertRow(cont - 1).innerHTML =
                        '<tr><td class="ancho"><input type="text" class="textoredondo0" size="1" value="' + (cuenta + 1) + '" readonly>&nbsp;<input type="text" class="caja2c" size="7" id="txtcod_' + (cuenta + 1) + '" name="txtcod_' + (cuenta + 1) + '" required onChange="buscarDuplicado(this.value,this.id)" onkeypress="if (event.which == 13) event.returnValue = false, insertar(this.value);" maxlength="8">&nbsp;<img class="imgpeq" id="img_' + (cuenta + 1) + '" alt="buscar" title="buscar" src="../img/lupan.png" onclick="buscar(this.id)"></td>' +
                        '<td align="left"><input type="text" id="txtglosa_' + (cuenta + 1) + '" name="txtglosa_' + (cuenta + 1) + '" value="" size="70" class="caja2d" readonly></td>' +
                        '<td><button type="button" class="boton1" id="eliminar_' + (cuenta + 1) + '" title="Eliminar Fila" onclick="eliminarFila(this.parentNode.parentNode.rowIndex)"><b>&nbsp;-&nbsp;</b></button></td></tr>';
                document.getElementById(id).focus();
            }
        </script>
        <script type="text/javascript"> //consultar datos y autollenar campos del codigo
            $(document).ready(function () {
                $("#agregar").click(function () {
                    $("#mostrar").css("display", "none");
                    var i = ($("#contador").val());//determinar posicion en la fila de la tabla
                    $("#txtcod_" + i).on("blur", function () {//activar funcion al sacar el foco del input del codigo
                        var valor = $(this).val(); //sacar el valor del input del codigo
                        if (valor !== "") {
                            $.ajax({
                                url: "../herramientas/buscaCod.php",
                                type: "POST",
                                dataType: "json",
                                data: {val: valor},
                                success: function (res) {//objeto que trae los parametros json elegidos
                                    if (res.glosa === null) {
                                        $("#txtcod_" + i).val("");
                                        $("#txtglosa_" + i).val("");
                                        $("#txtcod_" + i).focus();
                                        alert("Error...\nEl codigo ingresado: " + valor + " , NO Existe");
                                    } else {
                                        if(res.estado === "0"){
                                            alert("Error...\nEl codigo ingresado: " + valor + " , NO esta Vigente, favor revisar");
                                            $("#txtcod_" + i).val("");
                                            $("#txtglosa_" + i).val("");
                                            $("#txtcod_" + i).focus();
                                        }else{
                                            $("#txtglosa_" + i).val(res.glosa);//asignar los valores a los input elegidos dinamicos
                                            $("#txtcod_" + i).attr("readonly", true);
                                        }
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
                var idx = id.replace("img", "txtcod");
                document.getElementById("id").value = idx;
                window.open('../reportes/listarBuscaArticulo.php', '_blank', 'scrollbars=yes,statusbar=no,resizable=no,width=720,height=480 left=300,top=50');
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
                        alert("El Codigo Ingresado: " + x + " Ya esta En la Lista de Productos, favor revisar.");
                        document.getElementById(i).value = "";
                        document.getElementById(i).focus();
                    }
                });

            }
        </script>
        <script type="text/javascript">
            function borrar() {
                if (confirm("¿Esta Seguro que quiere borrar este Producto del Grupo Actual?"))
                    return true;
                else
                    return false;
            }
        </script>
		<script type="text/javascript">
            function insertar(val) {
                if (val !== "") {
                    agregar.click();
                }
            }
        </script>
        <?php
        $link = Conectarse();
        $grp = $_GET['id'];
        $tipo = $_GET["tipo"];
        $boton = "";
        $tipobtn = '<button class="boton" type="button" onclick="javascript:history.go(-1)"/>Volver&nbsp;<img class="img" alt="volver" title="Volver atras" src="../img/undo.png"></button> ';
        if ($grp === NULL) {
            $boton = "Crear Grupo";
            $title = "CREAR NUEVO GRUPO";
            $pagina = "crea";
            $focus = "autofocus";
            $contador = 0;
        } else {
            $boton = "Modificar Grupo";
            $title = "MODIFICACIÓN DE GRUPOS";
            $pagina = "modifica";
            $sql = "SELECT * FROM grupo_productos WHERE (grupo_prd_id='$grp')";
            $sql1 = "SELECT * FROM grupo_productos_detalle WHERE grupo_prd_id_id='$grp' ORDER BY grupo_prd_det_codigo";
            $res = MySQL_query($sql, $link)or die(mysql_error());
            $contar = mysql_num_rows($res);
            if ($contar == 0) {
                echo '<script>alert("Error...\nEl codigo ingresado es invalido, o se encuentra Inactivo");
				history.back(-1);</script>';
            }
            $row = MySQL_Fetch_array($res);
            $contador = $row["grupo_prd_cuenta"];
            $res1 = MySQL_query($sql1, $link)or die(mysql_error());
            $contar1 = mysql_num_rows($res1);
        }
        ?>        
        <title>SigBod - <?php echo $boton; ?></title>
        <script type="text/javascript">
            function goUpdate() {
                var glosa = $("#txtnombre").val();
                var descrip = $("#txtdescrip").val();
                if (glosa === "") {
                    alert("Debe Introducir Un Nombre de Grupo");
                    document.frm.txtnombre.focus();
                    return 0;
                }
                if (descrip === "") {
                    alert("Debe Introducir Una Descripcion del Grupo");
                    document.frm.txtdescrip.focus();
                    return 0;
                } else {
                    frm.action = "../edicion/crearGrupoAjax.php";
                    frm.submit();
                }
            }
        </script>
        <script type="text/javascript">
            function desbloquea() {
                location.reload();
                $('#grupo').unblock();
            }
        </script> 
    </head>
    <body class="fondo" id="grupo">
        <form id="frm" name="frm" action="" method="post" onKeypress="if (event.keyCode == 13)
                    event.returnValue = false;" onsubmit="goUpdate()">
            <table class="table3">
                <tr>
                    <td class="texto" colspan="2"><?php echo $title; ?></td>
                </tr>
                <tr>
                    <td class="texto1">ID Grupo:</td>
                    <td class="texto5"><input type="text" name="txtid" id="txtid" class="caja2e" value="<?= $row["grupo_prd_id"] ?>" size="3" readonly="readonly" maxlength="8"/>
                    </td>
                </tr>
                <tr>
                    <td class="texto1">Nombre Grupo:</td>
                    <td><textarea name="txtnombre" id="txtnombre" class="caja_negrita" cols="80" rows="1" required <?= $focus ?>><?= $row["grupo_prd_nombre"] ?></textarea></td>
                </tr>
                <tr>
                    <td class="texto1">Descripci&oacute;n Ampliada:</td>
                    <td><textarea name="txtdescrip" id="txtdescrip" class="caja_color" cols="80" rows="3" required><?= $row["grupo_prd_descrip"] ?></textarea></td>
                </tr>
                <tr>
                    <td class="texto1">Articulos Asociados al Grupo:</td>
                    <td class="texto5">
                        <?php
                        if ($contar1 != 0) {
                            $cuenta = 1;
                            echo "<table class='table' id='tabla'>";
                            while ($row1 = mysql_fetch_array($res1)) {
                                $codigo = $row1["grupo_prd_det_codigo"];
                                $sql2 = "SELECT * FROM productos WHERE prd_codigo='$codigo'";
                                $res2 = MySQL_query($sql2, $link)or die(mysql_error());
                                $row2 = MySQL_Fetch_array($res2);
                                echo "<tr><td class='ancho'><input type='text' class='textoredondo0' size='1' value='" . $cuenta . "'readonly>&nbsp;&nbsp;<input type='text' id='txtcod_" . $cuenta . "' class='caja2c' size='8' value='" . $codigo . "' readonly></td>";
                                echo "<td>" . $row2["prd_glosa"] . "</td>";
                                echo '<td><a href="borrarArticuloGrupo.php?id=' . $row1["grupo_prd_det_id"] . '&grp=' . $grp . '&cont=' . $row["grupo_prd_cuenta"] . '&cod=' . $codigo . '" class="boton1" title="Eliminar Registro" id="eliminar_' . $cuenta . '" onclick="return borrar();"><b>&nbsp;-&nbsp;</b></a></td></tr>';
                                $cuenta++;
                            }
                            echo'<tr><td colspan="3" class="texto1b"><button type="button" class="boton1a" id="agregar" title="Añadir Articulo" onclick="agregarFila()"><b>&nbsp;+&nbsp;</b></button></td></tr></table>';
                        } else {
                            echo "<table class='table' id='tabla'>";
                            echo'<tr><td colspan="3" class="texto1b"><button type="button" class="boton1a" id="agregar" title="Añadir Articulo" onclick="agregarFila()"><b>&nbsp;+&nbsp;</b></button><br><span id="mostrar">No hay Articulos Asociados Aún</span></td></tr></table>';
                        }
                        ?>
                    </td>
                <input type='hidden' name='txtpag' value='<?php echo $pagina; //envia si la pagina es de creacion de articulo o modificacion          ?>' />
                <input type='hidden' name='txttipo' value='<?php echo $tipo; //envia si la pagina es de creacion de articulo o modificacion          ?>' />
                <input type='hidden' name='contador' id='contador' value='<?php echo $contador ?>'/>
                <input type='hidden' name='id' id='id' value=''/>
                </tr>
                <tr>
                    <td colspan="2"><center><button class="boton" type="submit"><?php echo $boton; ?>&nbsp;<img class="img" alt="Grabar" title="Grabar" src="../img/edit1.png"></button> &nbsp;&nbsp;&nbsp;&nbsp;<?php echo $tipobtn ?></center>
                </td>
                <?php
                mysql_close($link);
                ?>
                </tr>
            </table>
        </form>
    </body>
</html>