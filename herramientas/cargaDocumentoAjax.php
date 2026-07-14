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
        <title>SigBod - Ingreso de Documentos</title>
    </head>
    <body class="fondo">
        <?php
        session_start();
        if (!isset($_SESSION['usuario'])) {
            header('Location: ../noSesion.php');
            exit();
        }
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
        $dv= $_POST["dv"];
        $nompv=$_POST["nompv"];
	$max=$_POST["max"];
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
        $id = [];
        $cont = [];

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
                            $subndoc[$j . $k] = $_POST["subndoc_" . $k . "_" . $j];
                            $subfdoc[$j . $k] = $_POST["subfdoc_" . $k . "_" . $j];
                            $submonto[$j . $k] = $_POST["submonto_" . $k . "_" . $j];
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
                $sql = "INSERT INTO `documento`(`doc_carga_id`,`doc_clave`,`doc_rutpv`,`doc_usuario`,`doc_fechacarga`,`doc_subdoc_cuenta`,`doc_tipodoc`,"
                        . "`doc_ndoc`,`doc_fechadoc`,`doc_noc`,`doc_montototal`,`doc_origen`,`doc_norigen`,`doc_fechaorigen`, `doc_obs`, `doc_url`) "
                        . "VALUES('$max','$clavedoc','$rutpv','$usuario','$date','$cont[$j]','$tipo[$j]','$nfact[$j]','$ffact[$j]','$noc[$j]','$monto[$j]','$origen[$j]','$norig[$j]','$forig[$j]','$obs[$j]','$url[$j]')";
                //echo $rutpv.$nfact[$j];
                MySQL_query($sql, $link) or die("<center><h2 class='texto'>Ha Ocurrido un Error: </h2> " . mysql_error() . "<br/><input type='button' value='Volver Atras' class='boton' onclick='javascript:history.back(-1)'></center>");
                $id[$j] = mysql_insert_id();
                if ($tabfila[$j] != "") {
                    for ($k = 1; $k <= $subcontador; $k++) {
                        if ($subtipo[$j . $k] != "") {
                            $clavesubdoc = $rutpv . $subndoc[$j . $k];
                            $sql1 = "INSERT INTO `subdocumento`(`subdoc_clave`,`subdoc_id`,`subdoc_rutpv`,`subdoc_tipo`,`subdoc_numdoc`,"
                                    . "`subdoc_fecha`,`subdoc_monto`) VALUES ('$clavesubdoc','$id[$j]','$rutpv','" . $subtipo[$j . $k] . "','" . $subndoc[$j . $k] . "','" . $subfdoc[$j . $k] . "','" . $submonto[$j . $k] . "')";
                            MySQL_query($sql1, $link) or die("<center><h2 class='texto'>Ha Ocurrido un Error: </h2> " . mysql_error() . "<br/><input type='button' value='Volver Atras' class='boton' onclick='javascript:history.back(-1)'></center>");
                        }
				if ($subtipo[$j . $k] == "Recep. de Bodega") {
                                $sql3 = "UPDATE `documento` SET `doc_estado`='Recepcionado' WHERE doc_id='$id[$j]'";
                                MySQL_query($sql3, $link) or die("<center><h2 class='texto'>Ha Ocurrido un Error: </h2> " . mysql_error() . "<br/><input type='button' value='Volver Atras' class='boton' onclick='javascript:history.back(-1)'></center>");
                            }
                            if ($subtipo[$j . $k] == "Nota de Credito") {
                                $sql3 = "UPDATE `documento` SET `doc_estado`='Anulado' WHERE doc_id='$id[$j]'";
                                MySQL_query($sql3, $link) or die("<center><h2 class='texto'>Ha Ocurrido un Error: </h2> " . mysql_error() . "<br/><input type='button' value='Volver Atras' class='boton' onclick='javascript:history.back(-1)'></center>");
                            }
				if ($subtipo[$j . $k] == "Devolucion") {
                                $sql3 = "UPDATE `documento` SET `doc_estado`='Devuelto' WHERE doc_id='$id[$j]'";
                                MySQL_query($sql3, $link) or die("<center><h2 class='texto'>Ha Ocurrido un Error: </h2> " . mysql_error() . "<br/><input type='button' value='Volver Atras' class='boton' onclick='javascript:history.back(-1)'></center>");
                            }
                    }
                }
            }
        }

        /* ---------------------------------mostrar resultado en pantalla------------------------------------- */

        echo "<table class='table2a'>";
        echo "<tr><td class='texto' colspan='9'>CARGA DE DOCUMENTOS</td></tr>";
        echo "<tr><td colspan='9' class='texto5a'>Usuario Digitador: " . $row["usuario_nombre"] . " " . $row["usuario_apellidos"] . ",&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Proveedor: ".number_format($rutpv, 0, '.', '.')."-".$dv."&nbsp;&nbsp;".$nompv."</td></tr>";
        for ($j = 0; $j < $contador; $j++) {
            if ($tipo[$j] != "") {
                echo "<tr><td colspan='9' class='texto8g'>N° de Registro: &nbsp;<b>" . $id[$j] . "</b></td></tr>";
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
        echo '<center><button type="button" class="boton" onclick="window.location = ' . $comillasim . 'cargaDocumento.php' . $comillasim . '">Nuevo Documento&nbsp;<img class="img" alt="nuevo" title="Crear Documento" src="../img/doc3.png"></button>&nbsp;&nbsp;&nbsp;&nbsp;<button type="button" class="boton" onclick="window.location = ' . $comillasim . '../principal.php' . $comillasim . '">Volver al Menu Principal&nbsp;<img class="img" alt="principal" title="volver al menu principal" src="../img/casa.png"></button>&nbsp;&nbsp;&nbsp;&nbsp;<button type="button" name="imprimir" class="boton" onclick="window.print();">Imprimir&nbsp;<img class="img" alt="imprimir" title="imprimir pantalla" src="../img/print.png"></button></center>';
        mysql_close($link);
	?>
        <a href="javascript:void(0);" id="scroll" title="Ir Arriba" style="display: none;">Arriba<span></span></a>
    </body>
</html>