<!DOCTYPE html>
<html>
<head>
	<title>Zoom en Imagen</title>
	<style type="text/css">
		#contenedor {
			position: relative;
			width: 500px;
			height: 500px;
			margin: 0 auto;
			overflow: hidden;
		}
		#imagen {
			position: absolute;
			left: 0;
			top: 0;
			width: 100%;
			height: 100%;
			background-position: center;
			background-size: cover;
			cursor: zoom-in;
			transition: transform 0.5s;
		}
		#imagen:hover {
			transform-origin: 50% 50%;
			cursor: zoom-out;
		}
		.area-zoom {
			position: absolute;
			border: 2px solid #f00;
			cursor: pointer;
			opacity: 0.3;
			transition: opacity 0.3s;
		}
		.area-zoom:hover {
			opacity: 0.5;
		}
		.area-zoom.active {
			opacity: 1;
		}
	</style>
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
	<div id="contenedor">
		<div id="imagen" style="background-image: url('<?= $imagen ?>')">
			<div class="area-zoom" style="top: 100px; left: 100px; width: 100px; height: 100px;"></div>
			<div class="area-zoom" style="top: 200px; left: 200px; width: 150px; height: 150px;"></div>
		</div>
	</div>
	<script type="text/javascript">
		var areasZoom = document.querySelectorAll('.area-zoom');
		var imagen = document.getElementById('imagen');

		for (var i = 0; i < areasZoom.length; i++) {
			areasZoom[i].addEventListener('mouseenter', function(e) {
				var area = e.target;
				var areaRect = area.getBoundingClientRect();
				var imagenRect = imagen.getBoundingClientRect();
				var scaleX = areaRect.width / imagenRect.width;
				var scaleY = areaRect.height / imagenRect.height;
				var translateX = (areaRect.left - imagenRect.left);
				var translateY = (areaRect.top - imagenRect.top);

				imagen.style.transform = 'scale(' + scaleX + ',' + scaleY + ') translate(' + -translateX + 'px,' + -translateY + 'px)';
				imagen.classList.add('area-zoom');
				area.classList.add('active');
			});

			areasZoom[i].addEventListener('mouseleave', function(e) {
				imagen.style.transform = 'none';
				imagen.classList.remove('area-zoom');
				for (var j = 0; j < areasZoom.length; j++) {
					areasZoom[j].classList.remove('active');
				}
			});
		}
	</script>
</body>
</html>
