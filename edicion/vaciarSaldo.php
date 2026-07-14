<?php

session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: ../index.php');
    exit();
}
include '../include/conn.php';
$link = Conectarse();
$fecha = date("Y-m-d H:i:s");
$user = $_SESSION["usuario"];
$fechaact = date("Y-m-d");
set_time_limit(600);
if (isset($_POST['resp'])) {
    $resp = $_POST['resp'];
    if ($resp == "si") {
        echo '<table class="table2a">';
        echo '<tr><td class="texto7">Vaciar Datos de tabla: Saldos de Orden de Compra</td></tr>';
        echo '<tr><td class="texto8b">Resultados:</td></tr>';
        $sqlb = "TRUNCATE TABLE `saldos`";
        $sqllog = "INSERT INTO`carga_log`(`carga_codigo`,`carga_tipo`,`carga_nombre`,`carga_fecha`,`carga_usuario`,`carga_cuenta`) VALUES(2,'Carga Saldos','Borrado','$fecha','$user','$cuenta')";
        mysql_query($sqlb, $link) or die('<tr><td class="texto5">Ha ocurrido un Error: ' . mysql_error() . '</td></tr>');
        $cuenta = mysql_affected_rows();
        mysql_query($sqllog, $link) or die('<tr><td class="texto5">Ha ocurrido un Error: ' . mysql_error() . '</td></tr>');
        
        echo '<tr><td class="texto5">Los Registros se han borrado exitosamente</td></tr>';
        //echo "<center>";
    } else {
        echo '<tr><td class="texto7">No se ha Podido Borrar</td></tr>';
    }
}
$sqla = "UPDATE proveedores SET proveedores.prov_estado=0";
$sql1 = "UPDATE proveedores, saldos SET proveedores.prov_estado=1 WHERE saldos.saldo_rutprov=proveedores.prov_rut"; //actualizar los registros de proveedores que tienen saldo
mysql_query($sqla, $link);
mysql_query($sql1, $link);
$filas = mysql_affected_rows();
echo '<tr><td class="texto5">Adicionalmente, Se han actualizado A: ' . $filas . ' Los Proveedores disponibles para generar Pedidos!</td></tr></table>';
mysql_close($link);
?>
