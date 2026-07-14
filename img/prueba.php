<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: ../index.php');
    exit();
}
?>

<html lang="es">
    <head>       
        <link rel="stylesheet" href="css/estilos.css" />
        <link rel="stylesheet" href="css/tabla.css" />
        <link rel="shortcut icon" href="img/favicon.ico" />
		<script src="js/jquery-2.2.4.js" type="text/javascript"></script>
        <title>SigBod - Muestra de Estilos</title>       
					<script>
	var miWindows=0;
	$(document).ready(function()
	{
		miWindows=window.location("http://10.6.24.11/SOFYA/contenido/Index1.aspx?user=4&serv=1","target");
	});
	</script>
    </head>
    <body class="fondo">
        <table class="table1">     
            <tr><td>Estilos de Texto:</td><td>Nombre Clase</td></tr>
            <tr><td class="texto">Prueba de texto1234567890-*/?!AaEeIiOoUu</td><td>Clase: texto</td></tr>
            <tr><td class="texto0">Prueba de texto1234567890-*/?!AaEeIiOoUu</td><td>Clase: texto0</td></tr>
            <tr><td class="texto0a">Prueba de texto1234567890-*/?!AaEeIiOoUu</td><td>Clase: texto0a</td></tr>
            <tr><td class="texto0b">Prueba de texto1234567890-*/?!AaEeIiOoUu</td><td>Clase: texto0b</td></tr>
            <tr><td class="texto1">Prueba de texto1234567890-*/?!AaEeIiOoUu</td><td>Clase: texto1</td></tr>
            <tr><td class="texto1a">Prueba de texto1234567890-*/?!AaEeIiOoUu</td><td>Clase: texto1a</td></tr>
            <tr><td class="texto1b">Prueba de texto1234567890-*/?!AaEeIiOoUu</td><td>Clase: texto1b</td></tr>
            <tr><td class="texto2">Prueba de texto1234567890-*/?!AaEeIiOoUu</td><td>Clase: texto2</td></tr>
            <tr><td class="texto3">Prueba de texto1234567890-*/?!AaEeIiOoUu</td><td>Clase: texto3</td></tr>
            <tr><td class="texto4">Prueba de texto1234567890-*/?!AaEeIiOoUu</td><td>Clase: texto4</td></tr>
            <tr><td class="texto4a">Prueba de texto1234567890-*/?!AaEeIiOoUu</td><td>Clase: texto4a</td></tr>
            <tr><td class="texto4b">Prueba de texto1234567890-*/?!AaEeIiOoUu</td><td>Clase: texto4b</td></tr>
            <tr><td class="texto4c">Prueba de texto1234567890-*/?!AaEeIiOoUu</td><td>Clase: texto4c</td></tr>
            <tr><td class="texto4d">Prueba de texto1234567890-*/?!AaEeIiOoUu</td><td>Clase: texto4d</td></tr>
            <tr><td class="texto5">Prueba de texto1234567890-*/?!AaEeIiOoUu</td><td>Clase: texto5</td></tr>
            <tr><td class="texto5a">Prueba de texto1234567890-*/?!AaEeIiOoUu</td><td>Clase: texto5a</td></tr>
            <tr><td class="texto5b">Prueba de texto1234567890-*/?!AaEeIiOoUu</td><td>Clase: texto5b</td></tr>
            <tr><td class="texto5c">Prueba de texto1234567890-*/?!AaEeIiOoUu</td><td>Clase: texto5c</td></tr>
            <tr><td class="texto6">Prueba de texto1234567890-*/?!AaEeIiOoUu</td><td>Clase: texto6</td></tr>
            <tr><td class="texto6a">Prueba de texto1234567890-*/?!AaEeIiOoUu</td><td>Clase: texto6a</td></tr>
            <tr><td class="texto7">Prueba de texto1234567890-*/?!AaEeIiOoUu</td><td>Clase: texto7</td></tr>
            <tr><td class="texto7a">Prueba de texto1234567890-*/?!AaEeIiOoUu</td><td>Clase: texto7a</td></tr>
            <tr><td class="texto7b">Prueba de texto1234567890-*/?!AaEeIiOoUu</td><td>Clase: texto7b</td></tr>
            <tr><td class="texto8">Prueba de texto1234567890-*/?!AaEeIiOoUu</td><td>Clase: texto8</td></tr>
            <tr><td class="texto8a">Prueba de texto1234567890-*/?!AaEeIiOoUu</td><td>Clase: texto8a</td></tr>
            <tr><td class="texto8b">Prueba de texto1234567890-*/?!AaEeIiOoUu</td><td>Clase: texto8b</td></tr>
            <tr><td class="texto8c">Prueba de texto1234567890-*/?!AaEeIiOoUu</td><td>Clase: texto8c</td></tr>
            <tr><td class="texto8d">Prueba de texto1234567890-*/?!AaEeIiOoUu</td><td>Clase: texto8d</td></tr>
            <tr><td class="texto8e">Prueba de texto1234567890-*/?!AaEeIiOoUu</td><td>Clase: texto8e</td></tr>
            <tr><td class="texto8f">Prueba de texto1234567890-*/?!AaEeIiOoUu</td><td>Clase: texto8f</td></tr>
            <tr><td class="texto8g">Prueba de texto1234567890-*/?!AaEeIiOoUu</td><td>Clase: texto8g</td></tr>
            <tr><td class="texto8h">Prueba de texto1234567890-*/?!AaEeIiOoUu</td><td>Clase: texto8h</td></tr>
            <tr><td class="texto8i">Prueba de texto1234567890-*/?!AaEeIiOoUu</td><td>Clase: texto8i</td></tr>
			<tr><td class="texto8j">Prueba de texto1234567890-*/?!AaEeIiOoUu</td><td style="width:200px;">Clase: texto8j</td></tr>
            <tr><td class="texto9">Prueba de texto1234567890-*/?!AaEeIiOoUu</td><td>Clase: texto9</td></tr>
            <tr><td class="texto9a">Prueba de texto1234567890-*/?!AaEeIiOoUu</td><td>Clase: texto9a</td></tr>
            <tr><td class="texto10">Prueba de texto1234567890-*/?!AaEeIiOoUu</td><td>Clase: texto10</td></tr>
            <tr><td class="texto11">Prueba de texto1234567890-*/?!AaEeIiOoUu</td><td>Clase: texto11</td></tr>
            <tr><td class="texto12">Prueba de texto1234567890-*/?!AaEeIiOoUu</td><td>Clase: texto12</td></tr>
            <tr><td class="texto_ancho">Prueba de texto1234567890-*/?!AaEeIiOoUu</td><td>Clase: textoancho</td></tr>
            <tr><td class="textograd">Prueba de texto1234567890-*/?!AaEeIiOoUu</td><td>Clase: textograd</td></tr>
            <tr><td class="textoredondo">Prueba de texto1234567890-*/?!AaEeIiOoUu</td><td>Clase: textoredondo</td></tr>
        </table>

 
<body>
 
<div onclick="alert(miWindows.dgData);">Capturar la variable</div>
 
</body>
    </body>
</html>
