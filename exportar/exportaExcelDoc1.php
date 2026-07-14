<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: ../index.php');
    exit();
}
$usuario = $_SESSION["usuario"];
$query = $_SESSION['query'];
$query= str_replace("ORDER BY doc_id DESC", "ORDER BY prov_nombre", $query);
ini_set('memory_limit', '3000M');
set_time_limit(3000);
include("../include/conn.php");
$link = Conectarse();

if ($query == "") {
    echo '<script>alert("No Hay Datos para Exportar");				
			history.back(-1);
	</script>';
} else {
    $result = mysql_query($query, $link);
    header("Content-Type: application/vnd.ms-excel");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("content-disposition: attachment;filename=sigbod_documentos.xls");
    ?>
    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml">
        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
            <title>Sigbod Documentos</title>
        </head>
        <body>
            <table border="1" cellpadding="0" cellspacing="0">
                <tr style="text-align:center;font-weight:bold;">
                    <td width="80">N_Registro</td><td width="100">N_Documento Principal</td><td  width="100">Tipo Documento Principal</td><td>Rut Proveedor</td><td width="390">Nombre Proveedor</td><td width="90">Fecha Doc. Principal</td><td width="110">Fecha creacion Sigbod</td><td  width="110">Fecha MODIFICACION</td><td>Orden de Compra</td><td  width="90">Monto Doc. Principal</td><td  width="90">Origen Doc.</td><td width="90">N_Origen</td><td width="100">Fecha Origen</td><td width="90">N_Subdocumento</td><td width="100">Tipo SubDocumento</td><td width="90">Fecha Subdocumento</td><td  width="90">Monto Subdocumento</td><td  width="100">Estado doc. Principal</td><td>Observaciones</td><td  width="550">URL</td>
                </tr>
                <?php
                while ($row = mysql_fetch_array($result)) {
                    $sum = $sum + $row["doc_montototal"];
                    $fmod = $row["doc_fechamod"];
                    if ($fmod == "0000-00-00 00:00:00") {
                        $fmod = "";
                    } else {
                        $fmod = date("d/m/Y H:i:s", strtotime($row["doc_fechamod"]));
                    }
                    $sql1 = "SELECT * FROM subdocumento WHERE subdoc_id='" . $row["doc_id"] . "'";
                    $result1 = mysql_query($sql1, $link);
                    $cont = mysql_num_rows($result1);
                    if ($cont != 0) {
                        while ($row1 = mysql_fetch_array($result1)) {
                            $fechasub = date("d/m/Y", strtotime($row1["subdoc_fecha"]));
                            $montosub = number_format($row1["subdoc_monto"], 0, ',', '.');
                            echo "<tr><td>" . $row["doc_id"] . '</td><td><b>' . $row["doc_ndoc"] . '</b></td><td>' . iconv("UTF-8", "WINDOWS-1252", $row["doc_tipodoc"]) . '</td><td>' . $row["prov_rut"] . "-" . $row["prov_dv"] . '</td><td>' . iconv("UTF-8", "WINDOWS-1252", $row["prov_nombre"]) . '</td><td><b>' . date("d/m/Y", strtotime($row["doc_fechadoc"])) . '</b></td><td>' . date("d/m/Y H:i:s", strtotime($row["doc_fechacarga"])) . '</td><td>' . $fmod . '</td><td><b>' . $row["doc_noc"] . '</b></td><td>' . number_format($row["doc_montototal"], 0, ',', '.') . '</td><td>' . $row["doc_origen"] . '</td><td>' . $row["doc_norigen"] . '</td><td>' . $row["doc_fechaorigen"] . '</td><td>' . $row1["subdoc_numdoc"] . '</td><td>' . iconv("UTF-8", "WINDOWS-1252", $row1["subdoc_tipo"]) . '</td><td>' . $fechasub . '</td><td>' . $montosub . '</td><td>' . $row["doc_estado"] . '</td><td>' . str_replace("\r\n", "", iconv("UTF-8", "WINDOWS-1252", $row["doc_obs"])) . '</td><td>' . $row["doc_url"] . "</td></tr>";
                        }
                    } else {
                        $fechasub = "";
                        $montosub = "";
                        echo '<tr><td>' . $row["doc_id"] . '</td><td><b>' . $row["doc_ndoc"] . '</b></td><td>' . iconv("UTF-8", "WINDOWS-1252", $row["doc_tipodoc"]) . '</td><td>' . $row["prov_rut"] . "-" . $row["prov_dv"] . '</td><td>' . iconv("UTF-8", "WINDOWS-1252", $row["prov_nombre"]) . '</td><td><b>' . date("d/m/Y", strtotime($row["doc_fechadoc"])) . '</b></td><td>' . date("d/m/Y H:i:s", strtotime($row["doc_fechacarga"])) . '</td><td>' . $fmod . '</td><td><b>' . $row["doc_noc"] . '</b></td><td>$' . number_format($row["doc_montototal"], 0, ',', '.') . '</td><td>' . $row["doc_origen"] . '</td><td>' . $row["doc_norigen"] . '</td><td>' . $row["doc_fechaorigen"] . '</td><td>' . $row1["subdoc_numdoc"] . '</td><td>' . iconv("UTF-8", "WINDOWS-1252", $row1["subdoc_tipo"]) . '</td><td>' . $fechasub . '</td><td>' . $montosub . '</td><td>' . $row["doc_estado"] . '</td><td>' . str_replace("\r\n", "", iconv("UTF-8", "WINDOWS-1252", $row["doc_obs"])) . '</td><td>' . $row["doc_url"] . '</td></tr>';
                    }
                }
                echo "<tr><td colspan='9' style='text-align:right;'><b>Total Valorizado:</b></td><td><b>$" . number_format($sum, 0, ',', '.') . "</b></td></tr>";
                mysql_free_result($result);
                mysql_close($link);
            }
            ?>
        </table>
    </body>
</html>