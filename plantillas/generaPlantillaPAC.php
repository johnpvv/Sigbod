<?php
$header = "Codigo_Producto;Cantidad_total;Ano_PAC";
header('Content-Encoding: UTF-8');
header('Content-Type: application/vnd.ms-excel; charset=utf-8');
header("Content-Disposition: attachment; filename=plantilla_sigbod_PAC.csv");
header("Pragma: no-cache");
header("Expires: 0");
    echo $header . "\n";
   ?>