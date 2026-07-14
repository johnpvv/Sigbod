<!DOCTYPE html>
<!--
Sigbod John Vaccarella
-->
<?php
include_once("../include/conn.php");
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: ../index.php');
    exit();
}
?>
<html>
    <head>
        <script type="text/javascript" src="js/validarut.js"></script>
        <link rel="stylesheet" href="../css/tabla.css" />
        <link rel="stylesheet" href="../css/estilos.css" />
        <link rel="shortcut icon" href="../img/favicon.ico" />
		<script type="text/javascript">
            function cerrar() {
                window.opener.document.getElementById('buscar').click();
                this.window.close();
            }
        </script>		
        <title>SigBod - Modificar productos</title>  
    </head>
    <body class="fondo">
        <?php
        $link = Conectarse();
        $pagina = $_POST["txtpagina"];
        $cod = $_POST['txtcod'];
        $nombre = mb_strtoupper($_POST["txtglosa"]);
        $glosaamp = $_POST ["txtglosaam"];
        $giro = mb_strtoupper($_POST["txtunimed"]);
        $ref = mb_strtoupper($_POST["txtref"]);
        $cm = $_POST["txtcodcm"];
        $opt = $_POST["txttipo"];
        $cenabast = $_POST["txtcen"];
	$unimedprov = $_POST["txtunimedprov"];	
        $precio = $_POST["txtprecio"];
        $estado = number_format($_POST["txtestado"]);
        $dest = $_POST["destacado"];
        $date = date("Y-m-d H:i:s");
        $diarepo = $_POST["diarepo"];
        $fam = substr($cod, 0, 3); //extrae los 3 primeros caracteres del codigo
        /* cargar imagenes */
        $nombre1 = $_FILES['imagen']['name'];
        if ($nombre1 != "") {
            $nomimg = $cod;
            list($nombref, $extension) = split("\.", $_FILES['imagen']['name']);
            $destino = '../img/articulos/' . $nomimg . '.' . strtolower($extension);
            $imgbase = ",prd_imagen='" . $destino . "'";
            copy($_FILES['imagen']['tmp_name'], $destino);
            /* fin carga */
        } else {
            $destino = "";
            $imgbase = "";
        }
        if ($dest == "on") {
            $tipo = 1;
            $sql2 = "INSERT IGNORE INTO `tiponivel` (`tiponivel_codigo`,`tiponivel_fam`,`tiponivel_critico`,`tiponivel_min`,`tiponivel_max`,`tiponivel_fecha`)VALUES('$cod','$fam',1,2,3,'$date')";
            mysql_query($sql2, $link)
                    or die("<script>alert('Ha ocurrido un error en la base de datos, favor intente nuevamente la operacion(1)...');javascript:history.back(-1)</script>");
            $msj = "\\nRecuerde Actualizar los valores en la Página de Niveles de Stock";
            $sql3= "INSERT IGNORE INTO nivelstock (`nivelstock_cod`,`nivelstock_cant`,`nivelstock_contador`,`nivelstock_cuentatotal`,`nivelstock_reciente`,`nivelstock_fecha`) VALUES ('$cod','0','0','0','0','$date')";
            mysql_query($sql3, $link) or die(mysql_error()); //Insertar el nivelstock reciente si no existe
        } else {
            $tipo = 0;
            $msj = "";
        }
        if ($pagina === "modifica") {
            $sql = "UPDATE productos SET prd_glosa ='$nombre', prd_glosaamp='$glosaamp', prd_unimed='$giro',prd_prov_unimed='$unimedprov',prd_ref='$ref',prd_codcm='$cm',prd_cenabast='$cenabast',prd_precio='$precio',prd_estado='$estado',prd_fecha='$date',prd_diarepo='$diarepo', prd_destacado='$tipo'" . $imgbase . " WHERE prd_codigo='$cod'";
        } else {
            $sql = "INSERT INTO productos (prd_codigo,prd_glosa,prd_glosaamp,prd_unimed,prd_prov_unimed,prd_ref,prd_codcm,prd_cenabast,prd_precio,prd_estado,prd_fam, prd_fecha, prd_destacado, prd_imagen, prd_diarepo) VALUES('$cod','$nombre','$glosaamp','$giro','$unimedprov','$ref','$cm','$cenabast','$precio','$estado','$fam','$date','$tipo','$destino','$diarepo')";
        }
        mysql_query($sql, $link)
                or die("<script>alert('Ha ocurrido un error en la base de datos, favor intente nuevamente la operacion(2)...');javascript:history.back(-1)</script>");
        
        if ($opt == "1") {
            echo"<script>alert('Felicidades, los datos se han actualizado correctamente..." . $msj . "');javascript:window.close();cerrar();</script>";
        } else if ($opt === "2") {
            echo'<script>alert("Felicidades, los datos se han actualizado correctamente...' . $msj . '");cerrar();javascript:history.go(-3);</script>';
        } else {
            echo'<script>alert("Felicidades, los datos se han actualizado correctamente...' . $msj . '");javascript:history.go(-2);</script>';
        }
		$sql4= "SELECT stock_codigo FROM stock WHERE stock_codigo ='$cod'";
		$result4= mysql_query($sql4, $link);
		$cont4 = mysql_num_rows($result4);
		if($cont==0){
			$sql5= "INSERT INTO stock (stock_codigo,stock_cantidad,stock_precio,stock_fecha,stock_obs) VALUES('$cod','0','$precio','$date','Creacion Articulo Automatico')";
			mysql_query($sql5, $link);
		}
		mysql_close($link);
        ?> 
    </body>
</html>