<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: ../index.php');
    exit();
}
$usuario = $_SESSION["usuario"];
ini_set('memory_limit', '3000M');
set_time_limit(3000);
include("../include/conn.php");
$link = Conectarse();
$fini =$_GET["fini"];
$ffin = $_GET["ffin"];
$sql = "SELECT saldo_codigo, prd_glosa, prd_precio, unimed_nombre, SUM(saldo_pendiente) AS suma FROM saldos, productos, unimed WHERE saldo_codigo=prd_codigo AND prd_unimed=unimed_id AND saldo_fechaoc BETWEEN '$fini' AND '$ffin' GROUP BY saldo_codigo";
$result = mysql_query($sql, $link);
$contar = mysql_num_rows($result);
if ($contar <= 0) {
    echo '<script>alert("Error, no hay datos para exportar, favor revise los filtros...");				
            history.go(-1);
            </script>';
    exit();
}
header("Content-Type: application/vnd.ms-excel");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("content-disposition: attachment;filename=sigbod_Saldos_Consolidado.xls");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <title>Sigbod Saldos</title>
    </head>
    <body>
        <table border="1" cellpadding="0" cellspacing="0">
            <tr align="center">
                <td colspan ="8" align="center"><b>INFORME CONSOLIDADO DE SALDOS</b></td>
            </tr>
            <tr>
                <td colspan ="8" align="center">Fecha consulta desde: <b><?= $fini . " Hasta: " . $ffin  ?></b></td>				
            </tr>			
			<tr>
                <td colspan ="8" align="center"></td>				
            </tr>			
            <tr style="text-align:center;">
                <td>Codigo</td><td>Glosa</td><td>U.M.</td><td>Precio Unitario en sistema</td><td>Precio neto En ultima OC</td><td>Fecha Ultima OC</td><td>Folio Ultima OC</td><td><b>Total Saldo Pendiente</b></td>
            </tr>
<?php
while ($row = mysql_fetch_array($result)) {
    $cod = $row["saldo_codigo"];
    $sql1 = "SELECT saldo_fechaoc, saldo_preciounit, saldo_oc FROM saldos WHERE saldo_codigo='$cod' ORDER BY saldo_fechaoc DESC LIMIT 1";
    $result1 = mysql_query($sql1, $link);
    $row1 = mysql_fetch_array($result1);
    echo "<tr><td><b>" . $cod . '</b></td><td width="500">' . iconv("UTF-8", "WINDOWS-1252", $row["prd_glosa"]) . '</td><td>' . $row["unimed_nombre"] . '</td><td width="100">$' . number_format($row["prd_precio"], 2, ',', '.') . '</td><td width="100">$' . number_format($row1["saldo_preciounit"], 0, ',', '.') . '</td><td>' . $row1["saldo_fechaoc"] . '</td><td>' . $row1["saldo_oc"] . '</td><td width="100"><b>' . $row["suma"] . "</b></td></tr>";
}
mysql_free_result($result);
mysql_close($link);
?>
        </table>
    </body>
</html>
