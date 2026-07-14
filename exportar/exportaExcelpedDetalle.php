<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: ../index.php');
    exit();
}
$usuario = $_SESSION["usuario"];
$query = $_SESSION['query'];
ini_set('memory_limit', '3000M');
set_time_limit(3000);
include("../include/conn.php");
$link = Conectarse();
$result = mysql_query($query, $link);
if ($query == "") {
    echo '<script>alert("Error, no hay datos para exportar, favor revise los filtros...");				
            history.go(-1);
            </script>';
    exit();
}
header("Content-Type: application/vnd.ms-excel");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("content-disposition: attachment;filename=sigbod_Saldos_OC.xls");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <title>Sigbod Saldos</title>
    </head>
    <body>
        <table border="1" style="font-size: 12px;">
            <tr align="center">
                <td colspan ="14" align="center"><b>Saldos de Orden de Compra</b></td>
            </tr>
            <tr style="text-align:center;font-weight:bold;">
                <td width="60">Codigo</td><td width="350">Glosa</td><td width="55">U.M.</td><td width="60">Rut Proveedor</td><td width="20">DV</td><td width="350">Nombre Proveedor</td><td width="120">OC Mercadopublico</td><td width="70">Saldo Solicitado</td><td width="70">Saldo Recibido</td><td width="70"><b>Saldo Pendiente</b></td><td width="80">Precio Unitario Articulo</td><td width="80">Precio Neto En OC</td><td>Fecha OC</td><td>Estado Item</td>
            </tr>
            <?php
            while ($row = mysql_fetch_array($result)) {
                $estado = $row["saldo_est"];
                if ($estado == "0") {
                    $es = "<b style='color:blue;'>Vigente</b>";
                } else {
                    $es = "<b style='color:red;'>Nulo</b>";
                }
                echo '<tr><td><b>'.$row["saldo_codigo"] . '</b></td><td>' . iconv("UTF-8", "WINDOWS-1252", $row["prd_glosa"]) . '</td><td>' . $row["unimed_nombre"] . '</td><td>' . $row["saldo_rutprov"] . '</td><td>' .$row["prov_dv"].'</td><td>'. iconv("UTF-8", "WINDOWS-1252", $row["prov_nombre"]) . '</td><td><b>' . $row["saldo_oc"] . '</b></td><td>' . $row["saldo_solicitado"] . '</td><td>' . $row["saldo_recibido"] . '</td><td><b>' . $row["saldo_pendiente"] . '</b></td><td>' . "$ " . number_format($row["prd_precio"], 2, ',', '.') . '</td><td>' . "$ " . number_format($row["saldo_preciounit"], 2, ',', '.') . '</td><td>' . $row["saldo_fechaoc"] . '</td><td>' . $es . '</td></tr>';
            }
            mysql_free_result($result);
            mysql_close($link);
            ?>
        </table>
    </body>
</html>
