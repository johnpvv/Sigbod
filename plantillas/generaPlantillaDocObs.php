<?php
$header = "N_doc;RUT;Observaciones;URL_doc";
header('Content-Encoding: UTF-8');
header('Content-Type: application/vnd.ms-excel; charset=utf-8');
header("Content-Disposition: attachment; filename=plantilla_sigbod_Documento.csv");
header("Pragma: no-cache");
header("Expires: 0");
    echo $header . "\n";
   ?>