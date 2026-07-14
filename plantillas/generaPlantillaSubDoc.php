<?php
$header = "N_doc;RUT;Fecha_DOC;Monto_total;Tipo_doc;Folio_Guia;Folio_Factura_Principal;URL_adjunto";
header('Content-Encoding: UTF-8');
header('Content-Type: application/vnd.ms-excel; charset=utf-8');
header("Content-Disposition: attachment; filename=plantilla_sigbod_SUBDocumento.csv");
header("Pragma: no-cache");
header("Expires: 0");
    echo $header . "\n";
   ?>