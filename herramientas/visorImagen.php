<!DOCTYPE html>
<!--
Sigbod John Vaccarella creado: 28/08/2019
-->
<html>
    <head>
        <link rel="stylesheet" href="../css/tabla.css" />
        <link rel="stylesheet" href="../css/estilos.css" />
        <link rel="shortcut icon" href="../img/favicon.ico" />
        <script src="../js/jquery-2.2.4.js" type="text/javascript"></script>
        <script type="text/javascript">
          $(document).ready(function () {
				zoomLvl = 1;
            $('#zoom-in').click(function () {
                updtZoom(0.1);
            });
            $('#zoom-out').click(function () {
                updtZoom(-0.1);
            });
			$('#sinzoom').click(function () {
                $('img').css({zoom:1});/*volver zoom a valor 1*/
				zoomLvl = 1;
            });            
            var updtZoom = function (zoom) {
                zoomLvl += zoom;
                $('img').css({zoom: zoomLvl, '-moz-transform': 'scale(' + zoomLvl + ')'});
            }
        });
		
        </script>
		<script>
		window.addEventListener("load", function() {//hacer que la imagen quede dentro de la ventana
			var img = document.getElementById("img");
			var windowWidth = window.innerWidth *0.95;
			var windowHeight = window.innerHeight *0.95;
			var imgWidth = img.naturalWidth;
			var imgHeight = img.naturalHeight;
			var widthRatio = windowWidth / imgWidth;
			var heightRatio = windowHeight / imgHeight;
			var minRatio = Math.min(widthRatio, heightRatio);
			img.style.width = imgWidth * minRatio + "px";
			img.style.height = imgHeight * minRatio + "px";
		});

		</script>
        <title>SigBod - Visor Imágenes</title>
        <?php
        include_once("../include/conn.php");
        session_start();
        if (!isset($_SESSION['usuario'])) {
            header('Location: ../noSesion.php');
            exit();
        }
        $link = Conectarse();
        $cod = $_GET['id'];
        $tipo = $_GET["tipo"];
        if ($tipo == 2) {
            $sql = "UPDATE productos SET prd_imagen='' WHERE prd_codigo='$cod'";
            mysql_query($sql, $link)or die("<script>alert('Ha ocurrido un error en la base de datos, favor intente nuevamente la operacion...');javascript:history.go(-1)</script>");
            $contar = mysql_affected_rows();
            if ($contar > 0) {
                echo '<script>alert("Se Ha borrado la imagen correctamente");	history.back(-1);</script>';
            }
        } else {
            $sql = "SELECT * FROM productos WHERE prd_codigo='$cod'";
            $res = MySQL_query($sql, $link)or die(mysql_error());
            $contar = mysql_num_rows($res);
            if ($contar == 0) {
                echo '<script>alert("Error...\nEl codigo ingresado es invalido, o se encuentra Inactivo");
				history.back(-1);</script>';
            }
            $row = MySQL_Fetch_array($res);
			$imagen=$row["prd_imagen"];
			if (!file_exists($imagen)||$imagen==""){
                $imagen="../img/img-error.jpg";
			}
        }	
        mysql_close($link);
        ?>
    </head>
    <body>
        <center><img src="<?= $imagen ?>" alt="Imagen Producto" class="imgvista" id="img"></center>
        <div class="abajo">        
		<button type="button" id="zoom-in" class="botonredondo" title="Aumentar Imagen">+</button>		
        <button type="button" id="zoom-out" class="botonredondo" title="Reducir Imagen">-</button>
		<button type="button" id="sinzoom" class="botonredondo" title="Restablecer Imagen">x</button>&nbsp;&nbsp;&nbsp;
        <button class="boton1" onclick="javascript:window.close()">Cerrar</button>        
    </div>
    </div>
    </body>
</html>