<?php

$timeant = microtime(true); //calcular el tiempo de ejecucion, capturo tiempo inicial
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: ../noSesion.php');
    exit();
}
include '../include/conn.php';
$link = Conectarse();
$user = $_SESSION["usuario"];
$fecha = date("Y-m-d H:i:s");
set_time_limit(600);
$filename = $_FILES['sel_file']['tmp_name'];
if ($filename == "") {
    echo '<script>alert("Error, debe elegir un archivo.");</script>';
} else {
    if (!isset($_POST['submit'])) {        //Aquí es donde seleccionamos nuestro csv
        $fname = $_FILES['sel_file']['name'];
        $tipo = $_POST['act'];
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
                    if ($cont == 0) {
                        $cont = $cont + 1;
                    } else {//si estamos aca es porque se eligio actualizar los datos con los valores...                        
                        $valor=str_replace(",",".",$data[7]); // doy formato al precio para pasarlo a la BBDD
                        $sql = "UPDATE productos SET prd_glosa='" . strtoupper(iconv("WINDOWS-1252", "UTF-8", $data[1])) . "', prd_glosaamp='".strtoupper($data[2])."', prd_unimed='$data[3]', prd_ref='".strtoupper($data[4])."', prd_codcm='$data[5]', prd_cenabast='$data[6]', prd_precio='$valor',  prd_estado='1', prd_fecha='$fecha' WHERE prd_codigo='$data[0]'";
                        //echo $sql."<br>";
                        mysql_query($sql, $link) or die("<tr><td class='texto5' align='left'>Ha ocurrido un Error con la Importaci&oacute;n: " . mysql_error() . "</td></tr>");
                        if (mysql_affected_rows() <> 0) {
                            echo "<tr><td align='left'>" . $cont . ".- Se ha Actualizado el Codigo: <b>" . $data[0] . "&nbsp;&nbsp;" . strtoupper(iconv("WINDOWS-1252", "UTF-8", $data[1])) . "</b>, Precio Neto: $".$valor."</td></tr>";
                        }
                        $cont = $cont + mysql_affected_rows();
                    }
                }
                $cont = ($cont - 1);
                if ($cont <> 0) {
                    $sqllog = "INSERT INTO`carga_log`(`carga_codigo`,`carga_tipo`,`carga_nombre`,`carga_fecha`,`carga_usuario`,`carga_cuenta`) VALUES(3,'Carga Productos','Actualizacion','$fecha','$user','$cont')";
                    mysql_query($sqllog, $link)or die("<tr><td class='texto5' align='left'>Ha ocurrido un Error con la Importaci&oacute;n: " . mysql_error() . "</td></tr>");
                }
            } else {
                while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
                    if ($cont == 0) {
                        $cont = $cont + 1;
                    } else {//Insertamos los datos con los valores...
                        $fam = substr($data[0], 0, 3); //capturo la familia del art 
                        $valor=str_replace(",",".",$data[7]);// doy formato al precio para pasarlo a la BBDD
                        $sql = "INSERT IGNORE INTO productos (prd_fam, prd_codigo, prd_glosa, prd_glosaamp, prd_unimed, prd_ref, prd_codcm,prd_cenabast, prd_precio, prd_estado, prd_fecha) values('$fam','$data[0]','".strtoupper(iconv("WINDOWS-1252", "UTF-8", $data[1]))."','".strtoupper($data[2])."','$data[3]','".strtoupper($data[4])."','$data[5]','$data[6]','$valor','1','$fecha')";
                        //echo $sql."<br>";
                        mysql_query($sql, $link) or die("<tr><td class='texto5' align='left'>Ha ocurrido un Error con la Importaci&oacute;n: " . mysql_error() . "</td></tr>");
                        if (mysql_affected_rows() <> 0) {
                            echo "<tr><td align='left'>" . $cont . ".- Se ha Creado el Codigo: <b>" . $data[0] . "&nbsp;&nbsp;" . strtoupper(iconv("WINDOWS-1252", "UTF-8", $data[1])) . "</b></td></tr>";
                        }
                        $cont = $cont + mysql_affected_rows();
                    }
                }
                $cont = $cont - 1;
                if ($cont <> 0) {
                    $sqllog = "INSERT INTO`carga_log`(`carga_codigo`,`carga_tipo`,`carga_nombre`,`carga_fecha`,`carga_usuario`,`carga_cuenta`) VALUES('3','Carga Productos','Carga Masiva','$fecha','$user','$cont')";
                    mysql_query($sqllog, $link)or die("<tr><td class='texto5'>Ha ocurrido un Error con la Importaci&oacute;n: " . mysql_error() . "</td></tr>");
                }
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
}
mysql_close($link);
?>