<?php

set_time_limit(600);
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: ../noSesion.php');
    exit();
}
ini_set('display_errors', false);

include("include/conn.php");

set_time_limit(300);

$link = Conectarse();
$fecha = date("Y-m-d");

$sql = "SELECT * FROM indicador WHERE ind_fecha ='$fecha'";
$res = MySQL_query($sql, $link) or die(mysql_error());
$cuenta = mysql_num_rows($res);

if ($cuenta == 0) {
    obtenerIndicador();
} else {
    enviarIndicador();
}
/*
  function obtenerIndicador() {
  global $link;
  $fecha1 = date("Y-m-d");
  $data = json_decode(file_get_contents('http://api.cmfchile.cl/api-sbifv3/recursos_api/dolar?apikey=ebd31b8abbff75581357b9a612eaf62a60ea4996&formato=json'), true);
  $dolar = $data["Dolares"][0]["Valor"];
  $data1 = json_decode(file_get_contents('http://api.cmfchile.cl/api-sbifv3/recursos_api/uf?apikey=ebd31b8abbff75581357b9a612eaf62a60ea4996&formato=json'), true);
  $uf = $data1["UFs"][0]["Valor"];
  $data2 = json_decode(file_get_contents('http://api.cmfchile.cl/api-sbifv3/recursos_api/utm?apikey=ebd31b8abbff75581357b9a612eaf62a60ea4996&formato=json'), true);
  $utm = $data2["UTMs"][0]["Valor"];
  $data3 = json_decode(file_get_contents('http://api.cmfchile.cl/api-sbifv3/recursos_api/euro?apikey=ebd31b8abbff75581357b9a612eaf62a60ea4996&formato=json'), true);
  $euro = $data3["Euros"][0]["Valor"];
  $sql1 = "INSERT INTO indicador (ind_fecha,ind_dolar,ind_uf,ind_utm,ind_euro) VALUES ('$fecha1','$dolar','$uf','$utm','$euro')";
  MySQL_query($sql1, $link) or die(mysql_error());
  }
 
*/
function obtenerIndicador() {
    global $link;
    $fecha1 = date("Y-m-d");
    $apiUrl = 'https://mindicador.cl/api';
    if (ini_get('allow_url_fopen')) {//Es necesario tener habilitada la directiva allow_url_fopen para usar file_get_contents
        $json = file_get_contents($apiUrl);
    } else {//De otra forma utilizamos cURL        
        $curl = curl_init($apiUrl);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $json = curl_exec($curl);
        curl_close($curl);
    }
    $dailyIndicators = json_decode($json);
    $uf = number_format($dailyIndicators->uf->valor, 2, ',', '.');
    $dolar = number_format($dailyIndicators->dolar->valor, 2, ',', '.');
    //echo 'El valor actual del Dólar acuerdo es $' . $dailyIndicators->dolar_intercambio->valor;
    $euro = number_format($dailyIndicators->euro->valor, 2, ',', '.');
    $utm = number_format($dailyIndicators->utm->valor, 2, ',', '.');
    //echo 'El valor actual del IVP es $' . $dailyIndicators->ivp->valor;
    //echo 'El valor actual del Imacec es ' . $dailyIndicators->imacec->valor;
    $sql1 = "INSERT INTO indicador (ind_fecha,ind_dolar,ind_uf,ind_utm,ind_euro) VALUES ('$fecha1','$dolar','$uf','$utm','$euro')";
    MySQL_query($sql1, $link) or die(mysql_error());
}

function enviarIndicador() {
    global $link;
    $fecha2 = date("Y-m-d");
    $sql2 = "SELECT * FROM indicador WHERE ind_fecha ='$fecha2'";
    $res2 = MySQL_query($sql2, $link) or die(mysql_error());
    $row2 = mysql_fetch_array($res2);
    $dato["dolar"] = $row2["ind_dolar"];
    $dato["uf"] = $row2["ind_uf"];
    $dato["utm"] = $row2["ind_utm"];
    $dato["euro"] = $row2["ind_euro"];
    return $dato;
}

?>