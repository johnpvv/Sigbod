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
                    $obs = trim(strtoupper(iconv("WINDOWS-1252", "UTF-8", $data[2])));
                    if ($obs == "") {
                        $varobs = "";
                    } else {
                        $varobs = "`doc_obs`='$obs', ";
                    }
                    $url = trim($data[3]);
                    if ($url == "" OR (strpos($url, "http") === false)) {
                        $varurl = "";
                    } else {
                        $varurl = "`doc_url`='$url', ";
                    }
                    if ($url == "" && $obs == "") {
                        echo "<tr><td align='left'>" . $cont1 . ".- Error... el Documento N°: <b>" . $ndoc . "</b>, No se ha podido Actualizar, ya que los campos Observacion y URL estan Vacios!!</td></tr>";
                    } else {
                        $sql = "UPDATE `documento` SET ".$varobs." ".$varurl." doc_umodif='$user', doc_fechamod='$fecha' WHERE `doc_clave`='$rutlim$ndoc' ";
                        mysql_query($sql, $link) or die("<h3 class='texto2'>Ha ocurrido un Error con la Importaci&oacute;n: " . mysql_error() . "</h3><br/><center><input type='button' value='Volver Atras' class='boton' onclick='javascript:history.back(-1)'/></center>");
                        if (mysql_affected_rows() <> 0) {
                            echo "<tr><td align='left'>" . $cont1 . ".- Se ha Actualizado el Documento N°: <b>" . $ndoc . "</b> Correctamente</td></tr>";
                            $cont ++;
                        } else {
                            echo "<tr><td align='left'>" . $cont1 . ".- Error... el documento N°: <b>" . $ndoc . "</b>, No se ha podido Actualizar, probablemente no exista en el Sistema.</td></tr>";
                        }
                    }                    
                }
				$cont1 ++;
            }
        }
        if ($cont <> 0) {
            $sqllog = "INSERT INTO`carga_log`(`carga_codigo`,`carga_tipo`,`carga_nombre`,`carga_fecha`,`carga_usuario`,`carga_cuenta`) VALUES(7,'Carga Obs. Documentos','Actualizacion','$fecha','$user','$cont')";
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
