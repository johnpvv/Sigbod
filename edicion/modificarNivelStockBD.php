<!DOCTYPE html>
<!--
Sigbod John Vaccarella
-->
<html>
    <head>
        <link rel="stylesheet" href="../css/tabla.css" />
        <link rel="stylesheet" href="../css/estilos.css" />
        <link rel="shortcut icon" href="../img/favicon.ico" />           
        <script src="../js/jquery-2.2.4.js" type="text/javascript"></script>
        <title>SigBod - Modificar Niveles de Stock</title>    
    </head>   
    <body class="fondo">
        <div id="resultado"></div>
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
        if ($_SESSION['perfil'] > 1) {
            echo '<script>alert("Su Perfil no permite la visualizacion de esta pantalla, Favor contacte al Administrador.");history.back(-1);</script>';
            echo '<center><button class="boton" onclick="javascript:window.close()">Salir&nbsp;<img class="img" alt="Salir" title="salir de la pantalla de edicion" src="../img/salir.png"></button></center>';
            exit();
        }
        $usuario = $_SESSION["usuario"];
        $date = date("y-m-d H:i:s");
        include("../include/conn.php");
        $link = Conectarse(); //Variable de conexion
        /* ------------------arreglos para obtener datos desde pagina anterior------------------ */
        $i = $_POST["contador"]; //cantidad de elementos totales
        $min = [];
        $cri = [];
        $max = [];
        $codigo = [];
        $opc = $_POST["opc"];
        if ($opc == 1) {
            $btnopc = ' value="Salir" onclick="javascript:window.close()"';
        } else {
            $btnopc = ' value="Volver" onclick="javascript:history.go(-2)"';
        }
        for ($j = 0; $j < $i; $j++) {
            $min[$j] = $_POST["txtmin_" . $j]; //array con las cantidades minimas
            $cri[$j] = $_POST["txtcri_" . $j]; //array con las cantidades criticas para modificar
            $max[$j] = $_POST["txtmax_" . $j]; //array con las cantidades maximas para modificar
            $codigo[$j] = $_POST["txtcod_" . $j]; //array con los codigos            
        }
        /* ------------------actualizar datos de saldo oc en BBDD y mostrar resultado en pantalla------------------ */
        echo '<table class="table2" align="center">';
        echo '<tr><th colspan="7" class="texto4"><h1>NIVELES DE STOCK</h1></th></tr>';
        echo '<tr><th colspan="7"><h3>Fecha: ' . date("d/m/Y") . '</h3></th></tr>            
                <tr><th>Codigo Interno</th><th width="450">Glosa</th>
                <th>Unidad de Medida</th><th>Precio Unitario en Sistema</th><th>Stock Minimo</th>
                <th>Stock Critico</th><th>Stock Maximo</th></tr>';
        for ($k = 0; $k < count($codigo); $k++) {
            $sql3 = "UPDATE `tiponivel` SET `tiponivel_min`='$min[$k]',`tiponivel_critico`='$cri[$k]',`tiponivel_max`='$max[$k]',`tiponivel_fecha`='$date' WHERE '$codigo[$k]'= tiponivel_codigo";
            MySQL_query($sql3, $link) or die("<center><h2 class='texto'>Ha Ocurrido un Error: </h2> " . mysql_error() . "<br/><input type='button' value='Volver Atras' class='boton' onclick='javascript:history.back(-1)'></center>");
            $sql = "SELECT * FROM productos, unimed WHERE (prd_codigo ='$codigo[$k]') AND prd_unimed=unimed_id"; //agrupar en parentesis los campos para que se ejecute primero la accion
            $data = mysql_query($sql);
            while ($row = mysql_fetch_array($data)) {
                echo '<tr><td align="center"><b>' . $codigo[$k] . '<b></td><td align="left">' . $row["prd_glosa"] . '</td><td>' . $row["unimed_nombre"] . '</td><td>$ ' . number_format($row["prd_precio"], 2, ',', '.') . '</td><td align="center"><b>' . $min[$k] . '</b></td><td align="center"><b>' . $cri[$k] . '</b></td><td align="center"><b>' . $max[$k] . '</b></td></tr>';
            }
        }
        echo '</table>';
        echo '<script>alert("Los datos se han actualizado Correctamente");</script>';
        $_SESSION['pedido'] = 0;
        mysql_close($link);
        ?>
        <br/>        
    <center><form><input type="button" class="boton" <?php echo $btnopc ?>>&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" value="Volver al Menu Principal" class="boton" onclick="window.location = '../principal.php'"/>&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" name="imprimir" value="Imprimir pantalla" class="boton" onclick="window.print();"></form></center>
</body>
</html>