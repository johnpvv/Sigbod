<?php
$header = "Rut;DV;Nombre Proveedor;Proveedor Giro;Proveedor Direccion;Nombre Representante;Email;Telefono;estado:(0=no vigente,1=vigente)";
header('Content-Encoding: UTF-8');
header('Content-Type: application/vnd.ms-excel; charset=utf-8');
header("Content-Disposition: attachment; filename=plantilla_sigbod_proveedores.csv");
header("Pragma: no-cache");
header("Expires: 0");
    echo $header . "\n";
   ?>