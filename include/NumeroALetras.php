<?php

function basico($numero) {
    $valor = array('UN', 'DOS', 'TRES', 'CUATRO', 'CINCO', 'SEIS', 'SIETE', 'OCHO', 'NUEVE', 'DIEZ', 'ONCE', 'DOCE', 'TRECE', 'CATORCE', 'QUINCE', 'DIECISÉIS', 'DIECISIETE', 'DIECIOCHO', 'DIECINUEVE', 'VEINTE', 'VEINTIUNO', 'VEINTIDÓS', 'VEINTITRÉS', 'VEINTICUATRO', 'VEINTICINCO', 'VEINTISÉIS', 'VEINTISIETE', 'VEINTIOCHO', 'VEINTINUEVE');
    return $valor[$numero - 1];
}

function decenas($n) {
    $decenas = array(30 => 'TREINTA', 40 => 'CUARENTA', 50 => 'CINCUENTA', 60 => 'SESENTA', 70 => 'SETENTA', 80 => 'OCHENTA', 90 => 'NOVENTA');
    if ($n <= 29)
        return basico($n);
    $x = $n % 10;
    if ($x == 0) {
        return $decenas[$n];
    } else
        return $decenas[$n - $x] . ' Y ' . basico($x);
}

function centenas($n) {
    $cientos = array(100 => 'CIEN', 200 => 'DOSCIENTOS', 300 => 'TRESCIENTOS',400 => 'CUATROCIENTOS', 500 => 'QUINIENTOS', 600 => 'SEISCIENTOS',700 => 'SETECIENTOS', 800 => 'OCHOCIENTOS', 900 => 'NOVECIENTOS');
    if ($n >= 100) {
        if ($n % 100 == 0) {
            return $cientos[$n];
        } else {
            $u = (int) substr($n, 0, 1);
            $d = (int) substr($n, 1, 2);
            return (($u == 1) ? 'CIENTO' : $cientos[$u * 100]) . ' ' . decenas($d);
        }
    } else
        return decenas($n);
}

function miles($n) {
    if ($n > 999) {
        if ($n == 1000) {
            return 'MIL';
        } else {
            $l = strlen($n);
            $c = (int) substr($n, 0, $l - 3);
            $x = (int) substr($n, -3);
            if ($c == 1) {
                $cadena = 'MIL ' . centenas($x);
            } else if ($x != 0) {
                $cadena = centenas($c) . ' MIL ' . centenas($x);
            } else
                $cadena = centenas($c) . ' MIL';
            return $cadena;
        }
    } else
        return centenas($n);
}

function millones($n) {
    if ($n == 1000000) {
        return 'UN MILLÓN';
    } else {
        $l = strlen($n);
        $c = (int) substr($n, 0, $l - 6);
        $x = (int) substr($n, -6);
        if ($c == 1) {
            $cadena = ' MILLÓN ';
        } else {
            $cadena = ' MILLONES ';
        }
        return miles($c) . $cadena . (($x > 0) ? miles($x) : '');
    }
}

function convertir($n) {
    switch (true) {
        case ( $n >= 1 && $n <= 29) : return basico($n);
            break;
        case ( $n >= 30 && $n < 100) : return decenas($n);
            break;
        case ( $n >= 100 && $n < 1000) : return centenas($n);
            break;
        case ($n >= 1000 && $n <= 999999): return miles($n);
            break;
        case ($n >= 1000000): return millones($n);
    }
}
