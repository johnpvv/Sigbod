<?php

$timeant = microtime(true); //calcular el tiempo de ejecucion, capturo tiempo inicial
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: ../noSesion.php');
    exit();
}
include '../include/conn.php';
$link = Conectarse();
$fecha = date("Y-m-d H:i:s");
$user = $_SESSION["usuario"];
$html = "";
set_time_limit(600);
$filename = $_FILES['sel_file']['tmp_name'];
if ($filename == "") {
    echo '<script>alert("Error, debe elegir un archivo.");</script>';
} else {
    if (!isset($_POST['submit'])) { //Aquí es donde seleccionamos nuestro csv
        $fname = $_FILES['sel_file']['name'];
        echo '<form id="export" method="post" name="export" action="">';
        echo '<table class="table2a">';
        echo '<tr><td class="texto7">Cargando Archivo: ' . $fname . '</td></tr>';
        echo '<tr><td class="texto8b">Resultados:</td></tr>';
        $html = $html . '<table border="1" cellpadding="0" cellspacing="0">';
        $html = $html . '<tr><td style="width:1050px;">Archivo Cargado: ' . $fname . '</td></tr>';
        $html = $html . '<tr><td>Resultados de la Importacion:</td></tr>';
        $chk_ext = explode(".", $fname);
        if (strtolower(end($chk_ext)) == "csv") {//si es correcto, entonces damos permisos de lectura para subir                    
            $filename = $_FILES['sel_file']['tmp_name'];
            $handle = fopen($filename, "r");
            $cont1 = 0;
            while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
                if ($cont1 == 0) {
                    $cont1 = $cont1 + 1;
                } else {
					$ndoc = trim($data[0]);//N° Subdocumento
                    $guia = trim($data[5]);//Guia de despacho
                    $fact = trim($data[6]);//documento principal
                    $adj  = trim($data[7]);//url adjunto
                    $rut = str_replace(".", "", $data[1]);					
					
                    if (strpos($rut, "-") !== false) {
                        $rutlim = trim(substr($rut, 0, strpos($rut, "-")));
                    } else {
                        $rutlim = $rut;
                    }
					
					$sql3 = "SELECT `subdoc_rutpv`,`subdoc_numdoc`,`subdoc_ai` FROM `subdocumento` WHERE `subdoc_clave`='$rutlim$ndoc'";
                    $result3 = mysql_query($sql3, $link);
                    $row3 = mysql_fetch_array($result3);
                    $subid = $row3["subdoc_ai"];						
					if($subid==""){					
                    $fechdoc = date("Y-m-d", strtotime($data[2]));
                    $monto = str_replace(",", ".", $data[3]); // doy formato al precio para pasarlo a la BBDD;
                    $tipo = trim(iconv("WINDOWS-1252", "UTF-8", $data[4]));
                    if ($tipo == "Recep. de Bodega" || $tipo == "Recep. De Bodega" || $tipo == "recep. de bodega") {
                        $estado = "Recepcionado";
						$tipo = "Recep. de Bodega";
                    } elseif ($tipo == "Nota de Credito") {
                        $estado = "Anulado";
                    } elseif ($tipo == "Guía de Despacho") {
                        $estado = $estado;
                    } elseif ($tipo == "Devolucion") {
                        $estado = "Devuelto";
                    } else {
                        $estado = "";
                    }
                    if ($fact == "" || $fact == "0") {
                        $sql2 = "SELECT `doc_id`,`doc_subdoc_cuenta` FROM `documento` WHERE `doc_obs` LIKE '%$guia%' AND doc_rutpv = '$rutlim'";
                        $result2 = mysql_query($sql2, $link);
                        $row2 = mysql_fetch_array($result2);
                        $id = $row2["doc_id"];
                        $cuenta = $row2["doc_subdoc_cuenta"];
                        $tipodoc = " (Guia de Despacho) ";
                        $docprinc = $guia;
                    } else {
                        $sql1 = "SELECT `doc_id`,`doc_subdoc_cuenta` FROM `documento` WHERE `doc_clave`='$rutlim$fact'";
                        $result = mysql_query($sql1, $link);
                        $row = mysql_fetch_array($result);
                        $id = $row["doc_id"];
                        $cuenta = $row["doc_subdoc_cuenta"];
                        $tipodoc = " (Factura) ";
                        $docprinc = $fact;
                    }
                    if ($id == "") {
                        $html = $html . "<tr><td align='left' style='color:red;'>" . $cont1 . ".- Error... el documento N°: <b>" . $ndoc . "</b> del Documento Principal: " . $docprinc . "<b>" . $tipodoc . ", No se ha podido guardar, probablemente no exista.</b></td></tr>";
                        echo "<tr><td align='left' style='color:red;'>" . $cont1 . ".- Error... el documento N°: <b>" . $ndoc . "</b> del Documento Principal: " . $docprinc . "<b>" . $tipodoc . ", No se ha podido guardar, probablemente no exista.</b></td></tr>";
                    } else {							
						$sql = "INSERT IGNORE INTO `subdocumento`(`subdoc_clave`,`subdoc_id`,`subdoc_rutpv`,`subdoc_tipo`,`subdoc_numdoc`,`subdoc_fecha`,`subdoc_monto`,`subdoc_adjunto`) VALUES('$rutlim$ndoc','$id','$rutlim','$tipo','$ndoc','$fechdoc','$monto','$adj')";
						mysql_query($sql, $link) or die("<h3 class='texto2'>Ha ocurrido un Error con la Importaci&oacute;n: " . mysql_error() . "</h3><br/><center><input type='button' value='Volver Atras' class='boton' onclick='javascript:history.back(-1)'/></center>");
						if (mysql_affected_rows() <> 0) {
							echo "<tr><td align='left'>" . $cont1 . ".- Se ha Creado el SubDocumento N°: <b>" . $ndoc . "</b> con el ID Principal N°: <b>" . $id . "</b> y el folio N°: <b>" . $docprinc . "</b>" . $tipodoc . "Correctamente</td></tr>";
							$html = $html . "<tr><td align='left'>" . $cont1 . ".- Se ha Creado el SubDocumento N°: <b>" . $ndoc . "</b> con el ID Principal N°: <b>" . $id . "</b> y el folio N°: <b>" . $docprinc . "</b>" . $tipodoc . "Correctamente</td></tr>";
							$cont ++;
							$cuenta = $cuenta + 1;
							mysql_query("UPDATE `documento` SET `doc_subdoc_cuenta` = '$cuenta', doc_umodif='$user', doc_fechamod='$fecha', doc_estado='$estado' WHERE doc_id='$id'", $link);
						} else {
							echo "<tr><td align='left' style='color:blue;'>" . $cont1 . ".- Error... el documento N°: <b>" . $ndoc . "</b> con el Documento Principal: " . $docprinc . "<b>" . $tipodoc . ", No se ha podido guardar, probablemente esté duplicado.</b></td></tr>";
							$html = $html . "<tr><td align='left' style='color:blue;'>" . $cont1 . ".- Error... el documento N°: <b>" . $ndoc . "</b> con el Documento Principal: " . $docprinc . "<b>" . $tipodoc . ", No se ha podido guardar, probablemente esté duplicado.</b></td></tr>";
						}              
					}					
                }else{
					$html = $html . "<tr><td align='left' style='color:red;'>" . $cont1 . ".- Error... el subdocumento N°: <b>" . $ndoc . "</b> del Documento Principal: " . $fact . "<b>, No se ha podido guardar, debe estar duplicado.</b></td></tr>";
					echo "<tr><td align='left' style='color:red;'>" . $cont1 . ".- Error... el subdocumento N°: <b>" . $ndoc . "</b> del Documento Principal: " . $fact . "<b>, No se ha podido guardar, debe estar duplicado.</b></td></tr>";
				}
				$cont1 ++;
			}
			
        }
    }
        if ($cont <> 0) {
            $sqllog = "INSERT INTO`carga_log`(`carga_codigo`,`carga_tipo`,`carga_nombre`,`carga_fecha`,`carga_usuario`,`carga_cuenta`) VALUES(6,'Carga SubDocumentos','Carga Masiva','$fecha','$user','$cont')";
            mysql_query($sqllog, $link)or die("<h3 class='texto2'>Ha ocurrido un Error con la Importaci&oacute;n: " . mysql_error() . "</h3><br/><center><input type='button' value='Volver Atras' class='boton' onclick='javascript:history.back(-1)'/></center>");
        }
        fclose($handle); //cerramos la lectura del archivo "abrir archivo" con un "cerrar archivo"
        $timedesp = microtime(true); //calculo tiempo final
        $time = round($timedesp - $timeant, 2); //resto los tiempos y obtengo la demora de la ejecucion
        if ($cont == 0) {
            echo '<tr><td class="texto2">No se ha Modificado ningun Registro. (La consulta ha tardado: ' . $time . ' Segundos)</td></tr>';
            $html = $html . '<tr><td class="texto2">No se ha Modificado ningun Registro</td></tr>';
        } else {
            echo '<tr><td class="texto2">Importación exitosa! Se ha(n) cargado: ' . $cont . ' Registro(s). (La consulta ha tardado: ' . $time . ' Segundos) Tasa promedio: ' . round($cont / $time, 2) . ' reg/seg</td></tr>';
            $html = $html . '<tr><td class="texto2">Importación exitosa! Se ha(n) cargado: ' . $cont . ' Registro(s).</td></tr>';
        }
    } else {
        echo '<tr><td class="texto2"> Archivo invalido!</td></tr>'; //si aparece esto, el archivo no se cargo o el formato no es correcto
        $html = $html . '<tr><td class="texto2"> Archivo invalido!</td></tr>'; //si aparece esto, el archivo no se cargo o el formato no es correcto
    }
    echo "</table>";
    $html = $html . "</table>";
    echo"<input type='hidden' name='flag' id='flag' value='1'>";
    echo"<textarea name='html' hidden='yes'>" . $html . "</textarea></form>";
}
mysql_close($link);
