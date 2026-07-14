<?php
$header = "OC;Fecha Recepcion;Codigo Articulo;Cant. Recibida;Precio Unitario;";
header('Content-Encoding: UTF-8');
header('Content-Type: application/vnd.ms-excel; charset=utf-8');
header("Content-Disposition: attachment; filename=plantilla_sigbod_act_saldooc.csv");
header("Pragma: no-cache");
header("Expires: 0");
    echo $header . "\n";
   ?>