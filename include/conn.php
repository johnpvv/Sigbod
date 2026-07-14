<?php
ini_set('display_errors', false);
function Conectarse() {
    $dbhost = "localhost";  // localhost or IP
    $dbuser = "root";   // database username
    $dbpass = "john6462";   // database password
    $dbname = "sigbod"; // database name

    if (!($link = mysql_connect($dbhost, $dbuser, $dbpass))) {
        echo"<h2 class='texto'>Ha Ocurrido un Error: </h2><center><b class='texto1'>Error de Conexión con el Servidor de Base de Datos...</b></center>";
        exit();
    }
    if (!mysql_select_db($dbname, $link)) {
        echo"<h2 class='texto'>Ha Ocurrido un Error: </h2><center><b class='texto1'>Error de seleccion de la base de datos...</b></center>";
        exit();
    }
    return $link;	
}
function is_connected(){
	if(!$sock = @fsockopen('www.google.com', 80)){
		echo "<br>";
		echo '<table class="table2b">';
		echo '<tr>';
		echo '<td class="texto8g"><img class="imgiz" alt="Campana Alerta" title="Alertas" src="img/alert.png">&nbsp;No hay conexión a Internet, algunas funciones del sistema no se ejecutarán correctamente.</td>';
		echo '</tr>';
		echo '</table>';
		fclose($sock);
	}
}	
?>
