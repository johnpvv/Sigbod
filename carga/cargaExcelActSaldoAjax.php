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
//$fechaact = date("Y-m-d");
set_time_limit(600);
$filename = $_FILES['sel_file']['tmp_name'];
if ($filename == "") {
    echo '<script>alert("Error, debe elegir un archivo.");</script>';
} else {
    if (!isset($_POST['submit'])) { //Aquí es donde seleccionamos nuestro csv
        //$tipo = $_POST['act'];
        $fname = $_FILES['sel_file']['name'];
        echo '<table class="table2a">';
        echo '<tr><td class="texto7">Cargando Archivo: ' . $fname . '</td></tr>';
        echo '<tr><td class="texto8b">Resultados:</td></tr>';
        $chk_ext = explode(".", $fname);
        if (strtolower(end($chk_ext)) == "csv") {//si es correcto, entonces damos permisos de lectura para subir                    
            $filename = $_FILES['sel_file']['tmp_name'];
            $handle = fopen($filename, "r");
            $cont1 = 0;
            //if ($tipo == "act") {
            while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
                if ($cont1 == 0) {
                    $cont1 = $cont1 + 1;
                } else {//si estamos aca es porque se eligio actualizar los datos con los valores...                        
                    $oc = strtoupper($data[0]);
                    $fechaact = date("Y-m-d", strtotime($data[1])); // doy formato a la fecha para pasarla a la BBDD
                    $cod = $data[2];
                    $recib = $data[3];
                    $precio = str_replace(",",".",$data[4]); // doy formato al precio para pasarlo a la BBDD;
                    $sql1 = "SELECT `saldo_solicitado`,`saldo_recibido`,`saldo_pendiente` FROM saldos WHERE `saldo_clave`='$oc$cod'";
                    $res = mysql_query($sql1, $link) or die(mysql_error());
                    $row = mysql_fetch_array($res);
                    $rec = $row["saldo_recibido"];
                    $solic = $row["saldo_solicitado"];
                    $recibfinal = ($recib + $rec);
                    $pend = ($solic - $recibfinal);
                    if ($pend < 0) {
                        echo "<tr><td align='left'>" . $cont1 . ".- La OC: <b>" . $oc . "</b> con el Codigo Actual: " . $cod . ",<b> No ha Sufrido cambios, ya que los saldos no coinciden.</b></td></tr>";
                    } else {
                        $sql = "UPDATE saldos SET saldo_preciounit='$precio', saldo_recibido='$recibfinal', saldo_pendiente='$pend', saldo_fechacarga='$fechaact' WHERE saldo_clave='$oc$cod' AND saldo_est = 0";
                        //echo $sql;
                        mysql_query($sql, $link) or die("<h3 class='texto2'>Ha ocurrido un Error con la Importaci&oacute;n: " . mysql_error() . "</h3><br/><center><input type='button' value='Volver Atras' class='boton' onclick='javascript:history.back(-1)'/></center>");
                        if (mysql_affected_rows() <> 0) {
                            echo "<tr><td align='left'>" . $cont1 . ".- Se ha actualizado La OC: <b>" . $oc . "</b> con el Codigo Actual: " . $cod . ",<b> a Cantidad Pendiente: " . $pend . "<b></td></tr>";
                            $cont ++;
                        }
                    }
                    $cont1 ++;
                }
            }
            //$cont = ($cont - 1);
            if ($cont <> 0) {
                $sqllog = "INSERT INTO`carga_log`(`carga_codigo`,`carga_tipo`,`carga_nombre`,`carga_fecha`,`carga_usuario`,`carga_cuenta`) VALUES(4,'Actualizacion Saldos','Actualizacion','$fecha','$user','$cont')";
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
    }
    echo "</table>";
}
mysql_close($link);
