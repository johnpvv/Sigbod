<!DOCTYPE html>
<!--
Sigbod John Vaccarella
-->
<html>
    <head>
        <link rel="stylesheet" href="../css/tabla.css" />
        <link rel="stylesheet" href="../css/estilos.css" />
        <link rel="shortcut icon" href="../img/favicon.ico" />
        <title>SigBod - Crear Grupo de Articulos</title>       
        <script src="../js/jquery-2.2.4.js" type="text/javascript"></script>
    </head>   
    <body class="fondo">
        <?php
        session_start();
        if (!isset($_SESSION['usuario'])) {
            header('Location: ../index.php');
            exit();
        }
        $usuario = $_SESSION["usuario"];
        $date = date("Y-m-d H:i:s");
        include("../include/conn.php");
        $link = Conectarse(); //Variable de coneccion
        
        /* ------------------arreglos para obtener datos desde pagina anterior------------------ */
        $i = $_POST["contador"]; //cantidad de elementos totales
        $id = $_POST["txtid"];
        $nombre = strtoupper($_POST["txtnombre"]);
        $descrip = strtoupper($_POST["txtdescrip"]);
        $pag = $_POST["txtpag"];
        $codigo = [];
        
        /* ---------------------------------Llenar Arreglos------------------------------------- */
        for ($j = 0; $j <= $i; $j++) {
            $codigo[$j] = $_POST["txtcod_" . $j]; //array con los codigos 
        }
        
        /* -------------------------actualizar datos de grupos en BBDD-------------------------- */
        if ($pag == "modifica") {
            $sql1 = "UPDATE grupo_productos SET grupo_prd_nombre='$nombre', grupo_prd_descrip='$descrip', grupo_prd_cuenta='$cuenta' WHERE grupo_prd_id='$id' ";
            MySQL_query($sql1, $link) or die("<center><h2 class='texto'>Ha Ocurrido un Error: </h2> " . mysql_error() . "<br/><input type='button' value='Volver Atras' class='boton' onclick='javascript:history.back(-1)'></center>");
            $tipo="MODIFICADO";            
        }
        if ($pag == "crea") {
            $sql1 = "INSERT INTO grupo_productos (grupo_prd_nombre, grupo_prd_descrip, grupo_prd_cuenta, grupo_prd_fechacrea, grupo_prd_rev) VALUES ('$nombre','$descrip','$cuenta','$date','0')";
            MySQL_query($sql1, $link) or die("<center><h2 class='texto'>Ha Ocurrido un Error: </h2> " . mysql_error() . "<br/><input type='button' value='Volver Atras' class='boton' onclick='javascript:history.back(-1)'></center>");
            $id = mysql_insert_id(); //obtener el ultimo id en la tabla movimiento
            $tipo="CREADO"; 
        }
        $cuenta = 1;
        for ($k = 0; $k < count($codigo); $k++) {
            if ($codigo[$k] != "") {
                $sql = "INSERT IGNORE INTO grupo_productos_detalle(grupo_prd_id_id,grupo_prd_det_codigo,grupo_prd_det_fecha) VALUES ('$id','$codigo[$k]','$date')";
                MySQL_query($sql, $link) or die("<center><h2 class='texto'>Ha Ocurrido un Error: </h2> " . mysql_error() . "<br/><input type='button' value='Volver Atras' class='boton' onclick='javascript:history.back(-1)'></center>");
            } else {
                $cuenta++;
            }
        }
        $_SESSION['pedido'] = 0;
        
        /* -----------------------------mostrar resultado en pantalla--------------------------- */
        echo '<table class="table3" align="center">';
        echo '<tr><th colspan="3" class="texto4"><h1>EL GRUPO N°: ' . $id . ', SE HA '.$tipo.' CORRECTAMENTE.</h1></th></tr>';
        echo '<tr><th colspan="3"><h3> Nombre del Grupo: ' . $nombre . '</h3></th></tr>
                <tr><th colspan="9"><h3> Descripcion del Grupo: ' . $descrip . '</h3></th></tr>              
                <tr><th width="70">Codigo Interno</th><th>Glosa Artículo</th><th>Precio Unitario</th></tr>';
        $sql3 = "SELECT * FROM grupo_productos_detalle WHERE grupo_prd_id_id='$id' ORDER BY grupo_prd_det_codigo";
        $res3 = MySQL_query($sql3, $link)or die(mysql_error());
        while ($row3 = mysql_fetch_array($res3)) {
            $codigo = $row3["grupo_prd_det_codigo"];
            $sql2 = "SELECT * FROM productos WHERE prd_codigo='$codigo'";
            $res2 = MySQL_query($sql2, $link)or die(mysql_error());
            $row2 = MySQL_Fetch_array($res2);
            echo "<tr><td>" . $codigo . "</td><td>" . $row2["prd_glosa"] . "<td>$ " . $row2["prd_precio"] . "</td></tr>";
        }
        echo '</table>';
        /* -------------------actualizar datos de cuenta articulos en BBDD----------------------- */
        $sql4 = "SELECT * FROM grupo_productos_detalle WHERE grupo_prd_id_id='$id'";
        $res4 = MySQL_query($sql4, $link)or die(mysql_error());
        $contar4 = mysql_num_rows($res4);
        $sql5 = "UPDATE grupo_productos SET grupo_prd_cuenta='$contar4' WHERE grupo_prd_id='$id' ";
        MySQL_query($sql5, $link) or die("<center><h2 class='texto'>Ha Ocurrido un Error: </h2> " . mysql_error() . "<br/><input type='button' value='Volver Atras' class='boton' onclick='javascript:history.back(-1)'></center>");
        mysql_close($link);
        ?>
        <br/>
    <center><form><input type="button" value="Volver atras" class="boton" onclick="javascript:history.go(-2)">&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" value="Volver al Menu Principal" class="boton" onclick="window.location = '../principal.php'"/>&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" name="imprimir" value="Imprimir pantalla" class="boton" onclick="window.print();"></form></center>
</body>
</html>