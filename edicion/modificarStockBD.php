<!DOCTYPE html>
<!--
Sigbod John Vaccarella
-->
<html>
    <head>
        <link rel="stylesheet" href="../css/estilos.css" />
        <link rel="shortcut icon" href="../img/favicon.ico" />
        <title>SigBod - Modificar Stock</title>       
        <script src="../js/jquery-2.2.4.js" type="text/javascript"></script>
    </head>   
    <body>
        <?php
        session_start();
        if (!isset($_SESSION['usuario'])) {
            header('Location: ../index.php');
            exit();
        }
        if ($_SESSION['pedido'] == 0) {
            echo"<script>javascript:history.go(-2)</script>";
            exit();
        }
        include("../include/conn.php");
        $link = Conectarse(); //Variable de coneccion
        /* ------------------arreglos para obtener datos desde pagina anterior------------------ */
        $i = $_POST["contador"]; //cantidad de elementos totales
        $codigo = [];
        $stock = [];
		$ant=[];
        $opc = $_POST["opc"];
		$user=$_SESSION['usuario'];
		$date = date("Y-m-d H:i:s");
        if ($opc == 1) {
            $btnopc = ' value="Salir" onclick="javascript:window.close()"';
        } else {
            $btnopc = ' value="Volver" onclick="javascript:history.go(-2)"';
        }
        for ($j = 0; $j < $i; $j++) {
            $stock[$j] = $_POST["txtstock_" . $j]; //array con los stock
			$ant[$j] = $_POST["txtant_" . $j]; //array con los stock anterior
            $codigo[$j] = $_POST["txtcod_" . $j]; //array con los codigos            
        }
        /* ------------------actualizar datos en BBDD y mostrar resultado en pantalla------------------ */
        $cont = 0;
        for ($k = 0; $k < count($codigo); $k++) {
            if($stock[$k]<0){
                echo '<script>alert("Error:\nNo se Puede Actualizar el stock, favor revise los datos ingresados.");window.close();</script>';
                exit();                
            }
            $sql3 = "UPDATE `stock` SET `stock_cantidad`='$stock[$k]', `stock_fecha`='$date', `stock_obs`='modificado por: $user, stock anterior: $ant[$k]' WHERE '$codigo[$k]'= stock_codigo";
            MySQL_query($sql3, $link) or die("<center><h2 class='texto'>Ha Ocurrido un Error: </h2> " . mysql_error() . "<br/><input type='button' value='Volver Atras' class='boton' onclick='javascript:history.back(-1)'></center>");
            $cont++;
        }
        if ($cont > 0) {
            echo '<script>alert("Los Datos han sido Actualizados Correctamente.");window.close();</script>';
        }
        $_SESSION['pedido'] = 0;
        mysql_close($link);
        ?>
    <center><input type="button" class="boton" <?php echo $btnopc ?>>&nbsp;&nbsp;&nbsp;&nbsp;</center>
</body>
</html>