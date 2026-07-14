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
        <script type="text/javascript">
            function cerrar() {
                window.opener.document.getElementById('buscar').click();
                this.window.close();
            }
        </script>
        <title>SigBod - Ingreso de Facturas</title>
    </head>
    <body class="fondo">
        <?php
        session_start();
        if (!isset($_SESSION['usuario'])) {
            header('Location: ../index.php');
            exit();
        }
        $op = $_GET["op"];
        include("../include/conn.php");
        $link = Conectarse(); //Variable de coneccion
        $usuario = $_SESSION["usuario"];
        $sql2 = "SELECT * FROM usuarios WHERE usuario_rut='$usuario'";
        $data = MySQL_query($sql2, $link);
        $row = mysql_fetch_array($data);
        $date = date("Y-m-d H:i:s");
        $comilladob = '"';
        $comillasim = "'";

        /* ------------------arreglos para obtener datos desde pagina anterior documentos------------------ */
        $contador = $_POST["contadortotal"];
        $subcontador = $_POST["subcuentatotal"];
        $rutpv = $_POST["rutpv"];
        $dv = $_POST["dv"];
        $nompv = $_POST["nompv"];
        $id = $_POST["nreg"];
        $rutinic = $_POST["rutini"];
        $contmod = 0;
        $sd=0;
        $tipo = [];
        $nfact = [];
        $ffact = [];
        $noc = [];
        $monto = [];
        $origen = [];
        $norig = [];
        $forig = [];
        $obs = [];
        $tabfila = [];
        $url = [];
        /* ------------------arreglos para obtener datos desde pagina anterior Subdocumentos------------------ */
        $subtipo = [];
        $subndoc = [];
        $subfdoc = [];
        $submonto = [];
        $cont = [];
        $tipodato = [];
        $file = [];
        $destino = [];
        $adjunto = [];

        /* ------------------------------------------Llenado de arreglos--------------------------------------- */
        for ($j = 0; $j < $contador; $j++) {
            if ($_POST["tipo_" . $j] != "") {
                $tipo[$j] = $_POST["tipo_" . $j];
                $nfact[$j] = $_POST["nfact_" . $j];
                $ffact[$j] = $_POST["ffact_" . $j];
                $noc[$j] = strtoupper($_POST["noc_" . $j]);
                $monto[$j] = $_POST["monto_" . $j];
                $origen[$j] = $_POST["origen_" . $j];
                $norig[$j] = $_POST["norig_" . $j];
                $forig[$j] = $_POST["forig_" . $j];
                $obs[$j] = mb_strtoupper($_POST["obs_" . $j]);
                $tabfila[$j] = $_POST["tabfila_" . $j];
                $url[$j] = $_POST["url_" . $j];
                if ($tabfila[$j] != "") {
                    for ($k = 1; $k <= $subcontador; $k++) {
                        if ($_POST["subtipo_" . $k . "_" . $j] != "") {
                            $subtipo[$j . $k] = $_POST["subtipo_" . $k . "_" . $j];
                            if($subtipo[$j . $k] == "Recep. de Bodega" || $subtipo[$j . $k] == "Nota de Credito" || $subtipo[$j . $k] == "Devolucion"){
                                $sd++;
                            }
                            $subndoc[$j . $k] = $_POST["subndoc_" . $k . "_" . $j];
                            $subfdoc[$j . $k] = $_POST["subfdoc_" . $k . "_" . $j];
                            $submonto[$j . $k] = $_POST["submonto_" . $k . "_" . $j];
                            $tipodato[$j . $k] = $_POST["tipodato_" . $k . "_" . $j];
                            $file[$j . $k] = $_FILES['file_' . $k . '_' . $j]['name'];
                            if ($file[$j . $k] != "") {
                                $nomarch = $id ."_". $subndoc[$j . $k];//el nombre de archivo se compone de el id del registro principal, guion bajo y el numero del subdocumento.
                                $extension = pathinfo($_FILES['file_' . $k . '_' . $j]['name'], PATHINFO_EXTENSION); //capturar extension del archivo
                                $destino[$j . $k] = '../doc/' . $nomarch . '.' . strtolower($extension);
                                copy($_FILES['file_' . $k . '_' . $j]['tmp_name'], $destino[$j . $k]);
                                $adjunto[$j . $k] = ",`subdoc_adjunto` = '" . $destino[$j . $k] . "'";
                            } else {
                                $adjunto[$j . $k] = "";
                            }
                            $cont[$j] ++;
                        }
                    }
                }
            }
        }
        /* ------------------------------------------Carga en BBDD--------------------------------------- */
        for ($j = 0; $j < $contador; $j++) {
            if ($tipo[$j] != "") {
                $clavedoc = $rutpv . $nfact[$j];
                $sql = "UPDATE `documento` SET `doc_clave`='$clavedoc',`doc_rutpv`='$rutpv',`doc_subdoc_cuenta`= '$cont[$j]',`doc_tipodoc`='$tipo[$j]',`doc_ndoc`='$nfact[$j]',`doc_fechadoc`='$ffact[$j]',"
                        . "`doc_noc`='$noc[$j]',`doc_montototal`='$monto[$j]',`doc_obs`='$obs[$j]',`doc_origen`='$origen[$j]',`doc_norigen`='$norig[$j]',`doc_fechaorigen`='$forig[$j]',`doc_umodif`='$usuario',`doc_fechamod`='$date',`doc_url`='$url[$j]' WHERE doc_id='$id'";
                MySQL_query($sql, $link) or die("<center><h2 class='texto'>Ha Ocurrido un Error: </h2> " . mysql_error() . "<br/><input type='button' value='Volver Atras' class='boton' onclick='javascript:history.back(-1)'></center>");
                $contmod = mysql_affected_rows();
                if ($tabfila[$j] != "") {
                    for ($k = 1; $k <= $subcontador; $k++) {
                        if ($subtipo[$j . $k] != "") {
                            $clavesubdoc = $rutpv . $subndoc[$j . $k];
                            if ($rutinic != $rutpv) {//comprobar si se ha modificado el rut del proveedor
                                $clavesubdoc1 = $rutinic . $subndoc[$j . $k];
                            } else {
                                $clavesubdoc1 = $clavesubdoc;
                            }
                            if ($tipodato[$j . $k] == "act") {
                                $sql1 = "UPDATE IGNORE `subdocumento` SET `subdoc_clave` = '$clavesubdoc', `subdoc_rutpv` = '$rutpv', `subdoc_tipo` = '" . $subtipo[$j . $k] . "', `subdoc_numdoc` = '" . $subndoc[$j . $k] . "', `subdoc_fecha` = '" . $subfdoc[$j . $k] . "', `subdoc_monto` = '" . $submonto[$j . $k] . "'" . $adjunto[$j . $k] . " WHERE `subdoc_clave` = '$clavesubdoc1'";
                            } else {
                                $sql1 = "INSERT INTO `subdocumento`(`subdoc_clave`,`subdoc_id`,`subdoc_rutpv`,`subdoc_tipo`,`subdoc_numdoc`,"
                                        . "`subdoc_fecha`,`subdoc_monto`, `subdoc_adjunto`) VALUES ('$clavesubdoc','$id','$rutpv','" . $subtipo[$j . $k] . "','" . $subndoc[$j . $k] . "','" . $subfdoc[$j . $k] . "','" . $submonto[$j . $k] . "','" . $destino[$j . $k] . "')";
                            }
                            //echo $sql1;
                            MySQL_query($sql1, $link) or die("<center><h2 class='texto'>Ha Ocurrido un Error: </h2> " . mysql_error() . "<br/><input type='button' value='Volver Atras' class='boton' onclick='javascript:history.back(-1)'></center>");
                            $contmod = $contmod + mysql_affected_rows();                            
                            if ($subtipo[$j . $k] == "Recep. de Bodega") {
                                $sql3 = "UPDATE `documento` SET `doc_estado`='Recepcionado' WHERE doc_id='$id'";
                                MySQL_query($sql3, $link) or die("<center><h2 class='texto'>Ha Ocurrido un Error: </h2> " . mysql_error() . "<br/><input type='button' value='Volver Atras' class='boton' onclick='javascript:history.back(-1)'></center>");
                            }
                            if ($subtipo[$j . $k] == "Nota de Credito") {
                                $sql3 = "UPDATE `documento` SET `doc_estado`='Anulado' WHERE doc_id='$id'";
                                MySQL_query($sql3, $link) or die("<center><h2 class='texto'>Ha Ocurrido un Error: </h2> " . mysql_error() . "<br/><input type='button' value='Volver Atras' class='boton' onclick='javascript:history.back(-1)'></center>");
                            }
                            if ($subtipo[$j . $k] == "Devolucion") {
                                $sql3 = "UPDATE `documento` SET `doc_estado`='Devuelto' WHERE doc_id='$id'";
                                MySQL_query($sql3, $link) or die("<center><h2 class='texto'>Ha Ocurrido un Error: </h2> " . mysql_error() . "<br/><input type='button' value='Volver Atras' class='boton' onclick='javascript:history.back(-1)'></center>");
                            }
                            if ($sd==0) {
                                $sql3 = "UPDATE `documento` SET `doc_estado`='' WHERE doc_id='$id'";
                                MySQL_query($sql3, $link) or die("<center><h2 class='texto'>Ha Ocurrido un Error: </h2> " . mysql_error() . "<br/><input type='button' value='Volver Atras' class='boton' onclick='javascript:history.back(-1)'></center>");
                            }
                        }
                    }
                }
            }
        }

        /* ---------------------------------mostrar resultado en pantalla------------------------------------- */
        if ($op != 1) {
            echo "<table class='table2a'>";
            echo "<tr><td class='texto' colspan='9'>MODIFICACION DE DOCUMENTOS</td></tr>";
            echo "<tr><td colspan='9' class='texto5a'>Usuario Digitador: " . $row["usuario_nombre"] . " " . $row["usuario_apellidos"] . ",&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Proveedor: " . number_format($rutpv, 0, '.', '.') . "-" . $dv . "&nbsp;&nbsp;" . $nompv . "</td></tr>";
            for ($j = 0; $j < $contador; $j++) {
                if ($tipo[$j] != "") {
                    echo "<tr><td colspan='9' class='texto8g'>N° de Registro Modificado: &nbsp;<b>" . $id . "</b></td></tr>";
                    echo "<th>Tipo doc. Principal</th>";
                    echo "<th>N° Doc.</th>";
                    echo "<th>Fecha Doc.</th>";
                    echo "<th>Orden de Compra</th>";
                    echo "<th>Monto con IVA</th>";
                    echo "<th>Origen Doc.</th>";
                    echo "<th>N° Origen</th>";
                    echo "<th>Fecha Origen</th>";
                    echo "<th width='250'>Observaciones</th>";
                    echo "<tr>";
                    echo "<td>" . $tipo[$j] . "</td>";
                    echo "<td><b>" . $nfact[$j] . "</b></td>";
                    echo "<td>" . $ffact[$j] . "</td>";
                    echo "<td>" . $noc[$j] . "</td>";
                    echo "<td>$&nbsp;" . $monto[$j] . "</td>";
                    echo "<td>" . $origen[$j] . "</td>";
                    echo "<td>" . $norig[$j] . "</td>";
                    echo "<td>" . $forig[$j] . "</td>";
                    echo "<td>" . $obs[$j] . "</td>";
                    echo "</tr>";
                    if ($tabfila[$j] != "") {
                        echo "<tr>";
                        echo "<td rowspan='" . $cont[$j] . "' class='texto8f'>Subdocumentos (" . $cont[$j] . "):</td>";
                        for ($k = 1; $k <= $subcontador; $k++) {
                            if ($subtipo[$j . $k] != "") {
                                echo "<td colspan='8'><b>Tipo Subdocumento:</b>&nbsp;&nbsp;<input type='text' class='caja2a' size='12' value='" . $subtipo[$j . $k] . "' readonly>&nbsp;&nbsp;&nbsp;&nbsp;";
                                echo "<b>N° Subdocumento:</b>&nbsp;&nbsp;<input type='text' class='caja2a' size='8' value='" . $subndoc[$j . $k] . "' readonly>&nbsp;&nbsp;&nbsp;&nbsp;";
                                echo "<b>Fecha Subdocumento:</b>&nbsp;&nbsp;<input type='text' class='caja2a' size='8' value='" . $subfdoc[$j . $k] . "' readonly>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
                                echo "<b>Monto Subdocumento:</b>&nbsp;&nbsp;$&nbsp;<input type='text' class='caja2a' size='8' value='" . $submonto[$j . $k] . "' readonly></td>";
                                echo "</tr>";
                            }
                        }
                    }
                    echo "<tr><td class='texto7' colspan='9'></td></tr>";
                }
            }
            echo "</table><br>";
            echo '<center><input type="button" value="Salir" class="boton" onclick="cerrar();">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" name="imprimir" value="Imprimir pantalla" class="boton" onclick="window.print();"></center>';
        } else {
            if ($contmod != 0) {
                echo '<script>alert("Los datos se han Actualizado Correctamente...");cerrar();</script>';
            } else {
                echo '<script>alert("Ha Ocurrido Un error con los Datos Ingresados (' . $contmod . '), favor Revisar y completar todos los campos Requeridos...");</script>';
            }
        }
        mysql_close($link);
        ?>
        <a href="javascript:void(0);" id="scroll" title="Ir Arriba" style="display: none;">Arriba<span></span></a>
    </body>
</html>