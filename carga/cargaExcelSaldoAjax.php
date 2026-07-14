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
$fechaact = date("Y-m-d");
set_time_limit(900);
$filename = $_FILES['sel_file']['tmp_name'];
if ($filename == "") {
    echo '<script>alert("Error, debe elegir un archivo.");</script>';
} else {
    if (!isset($_POST['submit'])) { //Aquí es donde seleccionamos nuestro csv
        $tipo = $_POST['act'];
        $fname = $_FILES['sel_file']['name'];
        echo '<table class="table2a">';
        echo '<tr><td class="texto7">Cargando Archivo: ' . $fname . '</td></tr>';
        echo '<tr><td class="texto8b">Resultados:</td></tr>';
        $chk_ext = explode(".", $fname);
        if (strtolower(end($chk_ext)) == "csv") {//si es correcto, entonces damos permisos de lectura para subir                    
            $filename = $_FILES['sel_file']['tmp_name'];
            $handle = fopen($filename, "r");
            $cont = 0;
            if ($tipo == "act") {
                while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
                    $valor = str_replace(",", ".", $data[4]); // doy formato al precio para pasarlo a la BBDD
                    if ($cont == 0) {
                        $cont = $cont + 1;                        
                    } else {//si estamos aca es porque se eligio actualizar los datos con los valores...                        
                        $fechaoc = date("Y-m-d", strtotime($data[1])); // doy formato a la fecha para pasarla a la BBDD
						$oc =strtoupper($data[0]);
						$cod=trim($data[3]);
						
                        $sql = "UPDATE saldos SET saldo_oc='" . $oc . "',saldo_fechaoc='$fechaoc',saldo_rutprov='$data[2]',saldo_codigo='$data[3]',saldo_preciounit='$valor',saldo_solicitado='$data[5]',saldo_recibido='$data[6]', saldo_pendiente='$data[7]',saldo_fechacarga='$fechaact' WHERE saldo_clave='$oc$cod' AND saldo_est = 0";
                        mysql_query($sql, $link) or die("<h3 class='texto2'>Ha ocurrido un Error con la Importaci&oacute;n: " . mysql_error() . "</h3><br/><center><input type='button' value='Volver Atras' class='boton' onclick='javascript:history.back(-1)'/></center>");
                        if (mysql_affected_rows() <> 0) {
                            echo "<tr><td align='left'>" . $cont . ".- Se ha actualizado La OC: <b>" . $data[0] . "</b> con el Codigo Actual: <b>" . $data[3] . "</b>, a Cantidad Pendiente: <b>" . $data[7] . "</b>, Precio Neto:<b> $" . $valor . "</b></td></tr>";
                        }
                        $cont = $cont + mysql_affected_rows();
                    }
                }
                $cont = ($cont - 1);
                if ($cont <> 0) {
                    $sqllog = "INSERT INTO`carga_log`(`carga_codigo`,`carga_tipo`,`carga_nombre`,`carga_fecha`,`carga_usuario`,`carga_cuenta`) VALUES(2,'Carga Saldos','Actualizacion','$fecha','$user','$cont')";
                    mysql_query($sqllog, $link)or die("<h3 class='texto2'>Ha ocurrido un Error con la Importaci&oacute;n: " . mysql_error() . "</h3><br/><center><input type='button' value='Volver Atras' class='boton' onclick='javascript:history.back(-1)'/></center>");
                }
            } else {
                while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
                    $valor = str_replace(",", ".", $data[4]); // doy formato al precio para pasarlo a la BBDD
                    if ($cont == 0) {
                        $cont = $cont + 1;
                    } else {//Insertamos los datos con los valores...                                
                        $fechaoc = date("Y-m-d", strtotime($data[1])); // doy formato a la fecha para pasarla a la BBDD
						$oc =strtoupper($data[0]);
						$cod=trim($data[3]);						
                        $res1 = mysql_query("SELECT saldo_clave FROM `saldos` WHERE saldo_clave='$oc$cod'", $link);
						$contar = mysql_num_rows($res1);
						if ($contar == 0) {
                        $sql = "INSERT IGNORE INTO saldos(saldo_clave, saldo_oc, saldo_fechaoc, saldo_rutprov, saldo_codigo, saldo_preciounit, saldo_solicitado, saldo_recibido, saldo_pendiente,saldo_fechacarga) "
                                . "VALUES ('$oc$data[3]','$data[0]','$fechaoc','$data[2]','$data[3]','$valor','$data[5]','$data[6]','$data[7]','$fechaact')";
                        mysql_query($sql, $link) or die("<h3 class='texto2'>Ha ocurrido un Error con la Importaci&oacute;n: " . mysql_error() . "</h3><br/><center><input type='button' value='Volver Atras' class='boton' onclick='javascript:history.back(-1)'/></center>");
                        if (mysql_affected_rows() <> 0) {
                            echo "<tr><td align='left'>" . $cont . ".- Se ha cargado La OC: <b>" . $data[0] . "</b> con el Codigo Actual: <b>" . $data[3] . "</b>, Cantidad Pendiente: <b>" . $data[7] . "</b>, Precio Neto:<b> $" . $valor . "</b></td></tr>";
                        }
                        $cont = $cont + mysql_affected_rows();
						}
                    }
                }
                $cont = $cont - 1;
                if ($cont <> 0) {
                    $sqllog = "INSERT INTO`carga_log`(`carga_codigo`,`carga_tipo`,`carga_nombre`,`carga_fecha`,`carga_usuario`,`carga_cuenta`) VALUES(2,'Carga Saldos','Carga Masiva','$fecha','$user','$cont')";
                    mysql_query($sqllog, $link)or die("<h3 class='texto2'>Ha ocurrido un Error con la Importaci&oacute;n: " . mysql_error() . "</h3><br/><center><input type='button' value='Volver Atras' class='boton' onclick='javascript:history.back(-1)'/></center>");
                }
            }
            fclose($handle); //cerramos la lectura del archivo "abrir archivo" con un "cerrar archivo"
            $timedesp = microtime(true); //calculo tiempo final
            $time = round($timedesp - $timeant, 2); //resto los tiempos y obtengo la demora de la ejecucucion
            if ($cont == 0) {
                echo '<tr><td class="texto2">No se ha Modificado ningun Registro. (La consulta ha tardado: ' . $time . ' Segundos)</td></tr>';
            } else {
                echo '<tr><td class="texto2">Importación exitosa! Se ha(n) cargado: ' . $cont . ' Registro(s). (La consulta ha tardado: ' . $time . ' Segundos) Tasa promedio: ' . round($cont / $time, 2) . ' reg/seg</td></tr>';
                $sqla = "UPDATE proveedores SET proveedores.prov_estado=0";
                $sql1 = "UPDATE proveedores, saldos SET proveedores.prov_estado=1 WHERE saldos.saldo_rutprov=proveedores.prov_rut"; //actualizar los registros de proveedores que tienen saldo
                mysql_query($sqla, $link);
                mysql_query($sql1, $link);
                $filas = mysql_affected_rows();
                echo '<tr><td class="texto5">Adicionalmente, Se han actualizado A: ' . $filas . ' Los Proveedores disponibles para generar Pedidos!</td></tr>';
            }
        } else {
            echo '<tr><td class="texto2"> Archivo invalido!</td></tr>'; //si aparece esto, el archivo no se cargo o el formato no es correcto
        }
    }
    echo "</table>";
}
mysql_close($link);
?>