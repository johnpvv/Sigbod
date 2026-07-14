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
            while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
                if ($cont == 0) {
                    $cont = $cont + 1;
                } else {
                    $glosa = strtoupper(iconv("WINDOWS-1252", "UTF-8", $data[1]));
                    $glosalim= str_replace("'", '"', $glosa);
                    $valor = str_replace(",", ".", $data[2]); // doy formato al precio para pasarlo a la BBDD
                    if ($valor == "" || $glosalim == "") {
                        echo "<tr><td align='left'>" . $cont . ".- Error, el Codigo: <b>" . $data[0] . "</b> " . $glosalim . ", Tiene Precio $0, o Glosa Vacia no se puede Actualizar.</td></tr>";
                    } else {
                        $sql = "UPDATE productos SET prd_glosa='$glosalim', prd_precio='$valor', prd_fecha='$fecha' WHERE prd_codigo='$data[0]'";
                        mysql_query($sql, $link) or die("<tr><td class='texto5' align='left'>Ha ocurrido un Error con la Importaci&oacute;n: " . mysql_error() . "</td></tr>");
                        if (mysql_affected_rows() <> 0) {
                            echo "<tr><td align='left'>" . $cont . ".- Se ha Actualizado el Codigo: <b>" . $data[0] . "</b> " . $glosalim . ", A Precio Neto: $" . $valor . "</td></tr>";
                        }
                        $cont = $cont + mysql_affected_rows();
                    }
                }
            }
            $cont = ($cont - 1);
            if ($cont <> 0) {
                $sqllog = "INSERT INTO`carga_log`(`carga_codigo`,`carga_tipo`,`carga_nombre`,`carga_fecha`,`carga_usuario`,`carga_cuenta`) VALUES(8,'Actualiza Productos','Actualizacion','$fecha','$user','$cont')";
                mysql_query($sqllog, $link)or die("<tr><td class='texto5' align='left'>Ha ocurrido un Error con la Importaci&oacute;n: " . mysql_error() . "</td></tr>");
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