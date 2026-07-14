<?php
$header = "Codigo Producto;Nombre Producto;Glosa Ampliada;Unidad Medida;Referencia Proveedor;ID Conv. Marco;Codigo CENABAST;Precio Unitario";
header('Content-Encoding: UTF-8');
header('Content-Type: application/vnd.ms-excel; charset=utf-8');
header("Content-Disposition: attachment; filename=plantilla_sigbod_productos.csv");
header("Pragma: no-cache");
header("Expires: 0");
    echo $header . "\n";
   ?>