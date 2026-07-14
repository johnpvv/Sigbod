<?php
$header = "OC;Fecha OC;Rut Proveedor;Codigo Articulo;Precio Unitario;Cant. Comprada;Cant. Recibida;Cant. Pendiente";
header('Content-Encoding: UTF-8');
header('Content-Type: application/vnd.ms-excel; charset=utf-8');
header("Content-Disposition: attachment; filename=plantilla_sigbod_saldooc.csv");
header("Pragma: no-cache");
header("Expires: 0");
    echo $header . "\n";
   ?>