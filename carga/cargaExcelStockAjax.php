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
set_time_limit(600);
$filename = $_FILES['sel_file']['tmp_name'];
if ($filename == "") {
    echo '<script>alert("Error, debe elegir un archivo.");</script>';
} else {
    if (isset($_POST['resp'])) {
        $resp = $_POST['resp'];
        if ($resp == "si") {
            $sqlb = "TRUNCATE TABLE `stock`";
            $sqllog = "INSERT INTO`carga_log`(`carga_codigo`,`carga_tipo`,`carga_nombre`,`carga_fecha`,`carga_usuario`,`carga_cuenta`) VALUES('1','0','Borrado','$fecha','$user','$cuenta')";
            mysql_query($sqlb, $link) or die('<h3 class="texto2">Ha ocurrido un Error: ' . mysql_error() . '</h3>');
            $cuenta = mysql_affected_rows();
            mysql_query($sqllog, $link) or die('<h3 class="texto2">Ha ocurrido un Error: ' . mysql_error() . '</h3>');
            echo '<h3 class="texto2">Los Registros se han borrado exitosamente</h3><br/>';
            echo "<center>";
        } else {
            echo '<h3 class="texto2">No se ha Podido Borrar</h3>';
        }
    } else {
        if (!isset($_POST['submit'])) {        //Aquí es donde seleccionamos nuestro csv
            $tipo = $_POST['act'];
            $fname = $_FILES['sel_file']['name'];
            echo '<table class="table2a">';
            echo '<tr><td class="texto7">Cargando Archivo: ' . $fname . '</td></tr>';
            echo '<tr><td class="texto8b">RESUMEN:</td></tr>';
            $chk_ext = explode(".", $fname);
            if (strtolower(end($chk_ext)) == "csv") {//si es correcto, entonces damos permisos de lectura para subir            
                $filename = $_FILES['sel_file']['tmp_name'];
                $handle = fopen($filename, "r");
                $cont = 0;
                if ($tipo == "act") {
                    while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
                        if ($cont == 0) {
                            $cont = $cont + 1;
                        } else {//si estamos aca es porque se eligio actualizar los datos con los valores... 
                            $codigo = $data[0];
                            $sql1 = "SELECT stock_cantidad FROM `stock` WHERE stock_codigo='$codigo'";
                            $res = mysql_query($sql1, $link);
                            $row = MySQL_Fetch_array($res);
                            $stock = $row["stock_cantidad"];
                            $stockcsv=$data[1];
                            if(!is_numeric($stockcsv)){
                                echo '<script>alert("Error, los datos de stock no son numéricos. Se ha Cancelado la Operación...");</script>';
                                exit();
                            }
                            if ($stock <> $stockcsv) {
                                $sql = "UPDATE stock SET stock_cantidad='$stockcsv',stock_precio='$data[2]', stock_fecha='$fecha' WHERE stock_codigo='$data[0]'";
                                mysql_query($sql, $link) or die("<h3 class='texto2'>Ha ocurrido un Error con la Importaci&oacute;n: " . mysql_error() . "</h3><br/><center><input type='button' value='Volver Atras' class='boton' onclick='javascript:history.back(-1)'/></center>");
                                echo "<tr><td align='left'>" . $cont . ".- Se ha actualizado el Codigo: <b>" . $data[0] . "</b> con stock actual: " . $stock . ",<b> a nuevo Stock: " . $stockcsv . "</b></td></tr>";
                                $cont = $cont + mysql_affected_rows();
                            }
                        }
                    }
                    $cont = $cont - 1;
                    if ($cont <> 0) {
                        $sqllog = "INSERT INTO`carga_log`(`carga_codigo`,`carga_tipo`,`carga_nombre`,`carga_fecha`,`carga_usuario`,`carga_cuenta`) VALUES('1','Carga Stock','Actualizacion','$fecha','$user','$cont')";
                        mysql_query($sqllog, $link)or die("<h3 class='texto2'>Ha ocurrido un Error con la Importaci&oacute;n: " . mysql_error() . "</h3><br/><center><input type='button' value='Volver Atras' class='boton' onclick='javascript:history.back(-1)'/></center>");
                    }
                } else {
                    while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
                        if ($cont == 0) {
                            $cont = $cont + 1;
                        } else {
							$stockcsv=$data[1];
							if(!is_numeric($stockcsv)){
                                echo '<script>alert("Error, los datos de stock no son numéricos. Se ha Cancelado la Operación...");</script>';
                                exit();
                            }else{
                            $sql = "INSERT IGNORE INTO stock (stock_codigo, stock_cantidad, stock_precio, stock_fecha) VALUES('$data[0]','$stockcsv','$data[2]','$fecha')";
                            mysql_query($sql, $link) or die("<h3 class='texto2'>Ha ocurrido un Error con la Importaci&oacute;n: " . mysql_error() . "</h3><br/><center><input type='button' value='Volver Atras' class='boton' onclick='javascript:history.back(-1)'/></center>");                            
							if(mysql_affected_rows()==1){
								echo "<tr><td align='left'>" . $cont . ".- Se ha creado el Codigo: <b>" . $data[0] . "</b>, a nuevo Stock: <b>" . $data[1] . "</b></td></tr>";
							}
							$cont = $cont + mysql_affected_rows();
							}
                        }
                    }
                    $cont = ($cont - 1);
                    if ($cont <> 0) {
                        $sqllog = "INSERT INTO`carga_log`(`carga_codigo`,`carga_tipo`,`carga_nombre`,`carga_fecha`,`carga_usuario`,`carga_cuenta`) VALUES(1,'Carga Stock','Carga Masiva','$fecha','$user','$cont')";
                        mysql_query($sqllog, $link)or die("<h3 class='texto2'>Ha ocurrido un Error con la Importaci&oacute;n: " . mysql_error() . "</h3><br/><center><input type='button' value='Volver Atras' class='boton' onclick='javascript:history.back(-1)'/></center>");
                    }
                }
                fclose($handle); //cerramos la lectura del archivo "abrir archivo" con un "cerrar archivo"            
                $timedesp = microtime(true); //calculo tiempo final
                $time = round($timedesp - $timeant, 2); //resto los tiempos y obtengo la demora de la ejecucion
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
    }
}
mysql_close($link);
?>