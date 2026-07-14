<?php

$timeant = microtime(true); //calcular el tiempo de ejecucion, capturo tiempo inicial
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: ../noSesion.php');
    exit();
}
include '../include/conn.php';
$link = Conectarse();
$fechaact = date("Y-m-d H:i:s");
$user = $_SESSION["usuario"];
set_time_limit(600);
$filename = $_FILES['sel_file']['tmp_name'];
if ($filename == "") {
    echo '<script>alert("Error, debe elegir un archivo.");</script>';
} else {
    if (!isset($_POST['submit'])) {
        $tipo = $_POST['act'];
        $fname = $_FILES['sel_file']['name']; //Aquí es donde seleccionamos nuestro csv
        echo '<table class="table2a">';
        echo '<tr><td class="texto7">Cargando Archivo: ' . $fname . '</td></tr>';
        echo '<tr><td class="texto8b">Resultados:</td></tr>';
        $chk_ext = explode(".", $fname);
        if (strtolower(end($chk_ext)) == "csv") { //si es correcto, entonces damos permisos de lectura para subir
            $filename = $_FILES['sel_file']['tmp_name'];
            $handle = fopen($filename, "r");
            $cont = 0;
            if ($tipo == "act") {
                while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
                    if ($cont == 0) {
                        $cont = $cont + 1;
                    } else {//si estamos aca es porque se eligio actualizar los datos con los valores...                        
                        $sql = "UPDATE proveedores SET prov_nombre='" . strtoupper($data[2]) . "',prov_giro='" . strtoupper($data[3]) . "', prov_direccion='" . strtoupper($data[4]) . "', prov_nomcontacto='".ucwords($data[5])."', prov_email='$data[6]',prov_telefono='$data[7]',prov_estado='$data[8]', prov_fechacreacion='$fechaact' WHERE prov_rut='$data[0]' AND prov_dv='$data[1]'";
                        mysql_query($sql, $link) or die("<tr><td class='texto5' align='left'>Ha ocurrido un Error con la Importaci&oacute;n: " . mysql_error() . "</td></tr>");
                        if (mysql_affected_rows() <> 0) {
                            echo "<tr><td align='left'>" . $cont . ".- Se ha Actualizado el Proveedor RUT: <b>" . $data[0] . "-" . $data[1] . "&nbsp;&nbsp;" . strtoupper($data[2]) . "</b></td></tr>";
                        }
                        $cont = $cont + mysql_affected_rows();
                    }
                }
                $cont = ($cont - 1);
                if ($cont <> 0) {
                    $sqllog = "INSERT INTO`carga_log`(`carga_codigo`,`carga_tipo`,`carga_nombre`,`carga_fecha`,`carga_usuario`,`carga_cuenta`) VALUES(4,'Carga Proveedores','Actualizacion','$fechaact','$user','$cont')";
                    mysql_query($sqllog, $link)or die("<tr><td class='texto5' align='left'>Ha ocurrido un Error con la Importaci&oacute;n: " . mysql_error() . "</td></tr>");
                }
            }else {
                while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
                    if ($cont == 0) {
                        $cont = $cont + 1;
                    } else {//Insertamos los datos con los valores...                        
                        $sql = "INSERT IGNORE INTO proveedores(prov_rut,prov_dv,prov_nombre,prov_giro,prov_direccion,prov_nomcontacto,prov_email,prov_telefono,prov_estado, prov_fechacreacion) "
                                . "VALUES ('$data[0]','$data[1]','" . strtoupper($data[2]) . "','" . strtoupper($data[3]) . "','" . strtoupper($data[4]) . "','".ucwords($data[5])."','$data[6] ','$data[7]','$data[8]','$fechaact')";
                        mysql_query($sql, $link) or die("<h3 class='texto2'>Ha ocurrido un Error con la Importaci&oacute;n: " . mysql_error() . "</h3><br/><center><input type='button' value='Volver Atras' class='boton' onclick='javascript:history.back(-1)'/></center>");
                        if (mysql_affected_rows() <> 0) {
                            echo "<tr><td align='left'>" . $cont . ".- Se ha Creado el Proveedor RUT: <b>" . $data[0] . "-" . $data[1] . "&nbsp;&nbsp;" . strtoupper($data[2]) . "</b></td></tr>";
                        }
                        $cont = $cont + mysql_affected_rows();
                    }
                }
                $cont = $cont - 1;
                if ($cont <> 0) {
                    $sqllog = "INSERT INTO`carga_log`(`carga_codigo`,`carga_tipo`,`carga_nombre`,`carga_fecha`,`carga_usuario`,`carga_cuenta`) VALUES('4','Carga Proveedores','Carga Masiva','$fechaact','$user','$cont')";
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
        }else {
            echo '<tr><td class="texto2"> Archivo invalido!</td></tr>'; //si aparece esto, el archivo no se cargo o el formato no es correcto
        }
        echo "</table>";
    }
}
mysql_close($link);
?>