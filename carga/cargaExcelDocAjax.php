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
$fechaact = date("Y-m-d H:i:s");
set_time_limit(600);
$filename = $_FILES['sel_file']['tmp_name'];
if ($filename == "") {
    echo '<script>alert("Error, debe elegir un archivo.");</script>';
} else {
    if (!isset($_POST['submit'])) { //Aquí es donde seleccionamos nuestro csv
        $fname = $_FILES['sel_file']['name'];
        echo '<table class="table2a">';
        echo '<tr><td class="texto7">Cargando Archivo: ' . $fname . '</td></tr>';
        echo '<tr><td class="texto8b">Resultados:</td></tr>';
        $chk_ext = explode(".", $fname);
        if (strtolower(end($chk_ext)) == "csv") {//si es correcto, entonces damos permisos de lectura para subir                    
            $filename = $_FILES['sel_file']['tmp_name'];
            $handle = fopen($filename, "r");
            $cont1 = 0;
            while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
                if ($cont1 == 0) {
                    $cont1 = $cont1 + 1;
                } else {
                    $ndoc = trim($data[0]);
                    $rut = str_replace(".", "", $data[1]);
                    if (strpos($rut, "-") !== false) {
                        $rutlim = trim(substr($rut, 0, strpos($rut, "-")));
                    } else {
                        $rutlim = $rut;
                    }
					$oc = trim(strtoupper($data[3]));
					$sql2="SELECT doc_id, doc_obs FROM documento WHERE doc_clave='$rutlim$ndoc'";//buscar si ya existe el registo, para no duplicar en bbdd 
					$rs = mysql_query($sql2, $link);
					$contar = mysql_num_rows($rs);					
					if($contar!==0){
						$row = mysql_fetch_array($rs);
						$idd=$row["doc_id"];
						$obss=$row["doc_obs"];
						if($obss==""){
							$obs1 = strtoupper(trim(iconv("WINDOWS-1252", "UTF-8", $data[9])));
							if($obs1!=""){
								$sql3 = "UPDATE `documento` SET doc_obs='$obs1', doc_fechamod='$fecha', doc_umodif='$user' WHERE `doc_clave`='$rutlim$ndoc' ";
								mysql_query($sql3, $link);
								if (mysql_affected_rows() <> 0) {
									$rt=", Se actualiza Observación";
								}else{
									$rt="";
								}		
							}				
						}
						echo "<tr><td align='left'>" . $cont1 . ".- Error... el documento N°: <b>" . $ndoc . "</b> con OC: " . $oc . "<b>, No se ha podido guardar, ya que esta duplicado, ingresado al sistema con ID:".$idd.".</b>".$rt."</td></tr>";
					}else{
						$fechdoc = date("Y-m-d", strtotime($data[2]));						
						$monto = str_replace(",", ".", $data[4]); // doy formato al precio para pasarlo a la BBDD;
						$tipo = trim($data[5]);
						$orig = trim($data[6]);
						$norig = trim($data[7]);
						$forig = date("Y-m-d", strtotime($data[8]));
						$obs = strtoupper(trim(iconv("WINDOWS-1252", "UTF-8", $data[9])));
						$url = trim($data[10]);
						$sql = "INSERT IGNORE INTO `documento`(`doc_carga_id`,`doc_clave`,`doc_rutpv`,`doc_usuario`,`doc_fechacarga`,`doc_subdoc_cuenta`,`doc_tipodoc`,"
								. "`doc_ndoc`,`doc_fechadoc`,`doc_noc`,`doc_montototal`,`doc_origen`,`doc_norigen`,`doc_fechaorigen`, `doc_obs`, `doc_url`) "
								. "VALUES('-1','$rutlim$ndoc','$rutlim','$user','$fechaact','0','$tipo','$ndoc','$fechdoc','$oc','$monto','$orig','$norig','$forig','$obs','$url')";
						mysql_query($sql, $link) or die("<h3 class='texto2'>Ha ocurrido un Error con la Importaci&oacute;n: " . mysql_error() . "</h3><br/><center><input type='button' value='Volver Atras' class='boton' onclick='javascript:history.back(-1)'/></center>");
						if (mysql_affected_rows() <> 0) {
							echo "<tr><td align='left'>" . $cont1 . ".- Se ha Creado el Documento N°: <b>" . $ndoc . "</b> con la OC: <b>" . $oc . "</b> Correctamente</td></tr>";
							$cont ++;
						} else {
							echo "<tr><td align='left'>" . $cont1 . ".- Error... el documento N°: <b>" . $ndoc . "</b> con OC: " . $oc . "<b>, No se ha podido guardar, probablemente esté duplicado.</b></td></tr>";
						}
					}
					$cont1 ++;
                }
				
            }
        }
        if ($cont <> 0) {
            $sqllog = "INSERT INTO`carga_log`(`carga_codigo`,`carga_tipo`,`carga_nombre`,`carga_fecha`,`carga_usuario`,`carga_cuenta`) VALUES(5,'Carga Documentos','Carga Masiva','$fecha','$user','$cont')";
            mysql_query($sqllog, $link)or die("<h3 class='texto2'>Ha ocurrido un Error con la Importaci&oacute;n: " . mysql_error() . "</h3><br/><center><input type='button' value='Volver Atras' class='boton' onclick='javascript:history.back(-1)'/></center>");
        }

        fclose($handle); //cerramos la lectura del archivo "abrir archivo" con un "cerrar archivo"
        $timedesp = microtime(true); //calculo tiempo final
        $time = round($timedesp - $timeant, 2); //resto los tiempos y obtengo la demora de la ejecucucion
        if ($cont == 0) {
            echo '<tr><td class="texto2">No se ha Modificado ningun Registro. (La consulta ha tardado: ' . $time . ' Segundos)</td></tr>';
        } else {
            echo '<tr><td class="texto2">Importación exitosa! Se ha(n) cargado: ' . $cont . ' Registro(s). (La consulta ha tardado: ' . $time . ' Segundos) Tasa promedio: ' . round($cont / $time, 2) . ' reg/seg</td></tr>';
        }
    } else {
        echo '<tr><td class="texto2"> Archivo invalido!</td></tr>'; //si aparece esto, el archivo no se cargo o el formato no es correcto
    }

    echo "</table>";
}
mysql_close($link);
