<!DOCTYPE html>
<!--
Sigbod John Vaccarella
-->
<html>
    <head>
        <link rel="stylesheet" href="../css/tabla.css" />
        <link rel="stylesheet" href="../css/estilos.css" />
        <link rel="shortcut icon" href="../img/favicon.ico" />
        <title>SigBod - Pedido a Proveedor</title>       
        <script src="../js/jquery-2.2.4.js" type="text/javascript"></script>
		<script type="text/javascript">
            function cerrar() {
                window.opener.document.getElementById('buscar').click();
                this.window.close();
            }
        </script>
    </head>   
    <body class="fondo">
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
        $usuario = $_SESSION["usuario"];
        $date = date("y-m-d H:i:s");
        include("../include/conn.php");
        $link = Conectarse(); //Variable de coneccion
        /* ------------------arreglos para obtener datos desde pagina anterior------------------ */
        $i = $_POST["contador"]; //cantidad de elementos totales
        $rut = $_POST["rutpv"];
        $nompv = $_POST["nompv"];
        $obs = $_POST["obsped"]; //observacion del despacho        
        $tipomov = 1; //definir el movimiento de la pagina--- 1=pedido a proveedor
        $cantidad = [];
        $codigo = [];
        $check = [];
        $oc = [];
        $preciooc=[];
        $opc=$_POST["opc"];//si valor es 1, muestra boton salir, de lo contrario esta oculto
        
        if($opc=="1"){
            $boton= '&nbsp;&nbsp;&nbsp;&nbsp;<button class="boton" onclick="cerrar()">Salir&nbsp;<img class="img" alt="principal" title="Cerrar" src="../img/salir.png"></button>';
        }else{
            $boton="";
        }
        for ($j = 0; $j < $i; $j++) {
            $cantidad[$j] = $_POST["caja_" . $j]; //array con las cantidades escritas
            $check[$j] = $_POST["check_" . $j]; //array con los check marcados(on)
            $codigo[$j] = $_POST["txtcod_" . $j]; //array con los codigos elegidos
            $oc[$j] = $_POST["txtoc_" . $j]; //array con las ocs marcadas
            $preciooc[$j] = $_POST["txtprec_" . $j];
        }
        for ($n = 0; $n < count($cantidad); $n++) {
            if ($check[$n] == "on") {
                $contador++;
            }
        }
        /* ------------------insertar datos del movimiento en BBDD------------------ */
        $sql1 = "INSERT INTO `movimiento` (`mov_tipo`,`mov_rutprv`, `mov_usuario`, `mov_fecha`,`mov_observacion`,`mov_cuenta`,`mov_estado`) VALUES('$tipomov','$rut','$usuario','$date','$obs','$contador','1')";
        MySQL_query($sql1, $link) or die("<center><h2 class='texto'>Ha Ocurrido un Error: </h2> " . mysql_error() . "<br/><input type='button' value='Volver Atras' class='boton' onclick='javascript:history.back(-1)'></center>");
        $id = mysql_insert_id(); //obtener el ultimo id en la tabla movimiento
        for ($n = 0; $n < count($cantidad); $n++) {
            if ($check[$n] == "on") {//obtener solo los valores de los check marcados en pagina anterior
                $sql2 = "SELECT * FROM productos, unimed WHERE (prd_codigo ='$codigo[$n]') AND prd_unimed=unimed_id"; //agrupar en parentesis los campos para que se ejecute primero la accion
                $data = mysql_query($sql2);
                while ($row = mysql_fetch_array($data)) {
                    $precio = $row['prd_precio'];
                    $sql3 = "INSERT INTO `detmovimiento` (`detmov_id`,`detmov_clave`,`detmov_codigoprd`,`detmov_precio`,`detmov_cantidad`,`detmov_oc`)"
                            . "VALUES ('$id','$oc[$n]$codigo[$n]','$codigo[$n]','$preciooc[$n]','$cantidad[$n]','$oc[$n]')";
                    MySQL_query($sql3, $link) or die("<center><h2 class='texto'>Ha Ocurrido un Error: </h2> " . mysql_error() . "<br/><input type='button' value='Volver Atras' class='boton' onclick='javascript:history.back(-1)'></center>");
                    mysql_query("UPDATE nivelstock SET nivelstock_reciente='1' WHERE nivelstock_cod='$codigo[$n]'", $link)or die(mysql_error());//actualizar el nivelstock reciente a1 si la fecha es valida a menos de 6 dias
                    
                }
            }
        }
        /* ------------------mostrar resultado en pantalla------------------ */
        echo '<table class="table3" align="center">
                <tr><th colspan="7" class="texto4"><h1>SOLICITUD DE PEDIDO A PROVEEDOR N° : ' . $id . ' / ' . date("Y") . '</h1></th></tr>
                <tr><th colspan="7" class="texto7">(copia uso interno)</th></tr>
                <tr><th colspan="7"><h3>Proveedor: ' . $rut . ' &nbsp;&nbsp;  ' . $nompv . ' ,&nbsp;&nbsp;  Fecha: ' . date("d/m/Y") . '</h3></th></tr>            
                <tr><th width="70">Codigo Interno</th><th width="90">Codigo CM</th><th width="400">Glosa</th>
                <th width="70">Empaque</th><th width="100">Precio Unitario Neto</th><th width="30">Cantidad Requerida</th>
                <th width="150">OC MercadoPublico</th></tr>';
        for ($k = 0; $k < count($cantidad); $k++) {
            if ($check[$k] == "on") {
                $sql = "SELECT * FROM productos, unimed WHERE (prd_codigo ='$codigo[$k]') AND prd_unimed=unimed_id"; //agrupar en parentesis los campos para que se ejecute primero la accion
                $data = mysql_query($sql);
                while ($row = mysql_fetch_array($data)) {
                    echo '<tr><td align="center"><b>' . $codigo[$k] . '<b></td><td align="center"><b>' . $row["prd_codcm"] . '<b></td><td align="left">' . $row["prd_glosa"] . '</td><td>' . $row["unimed_nombre"] . '</td><td>$ ' . number_format($preciooc[$k], 2, ',', '.') . '</td><td align="center"><b>' . $cantidad[$k] . '</b></td><td align="center"><b>' . $oc[$k] . '</b></td></tr>';
                    $sum = $sum + ((float) $preciooc[$k] * (float) $cantidad[$k]);
                }
            }
        }
        $sql4 = "UPDATE `movimiento` SET `mov_valorneto`='$sum' WHERE `mov_id`='$id'";
        mysql_query($sql4);
        echo '<tr><td colspan="7" class="texto2"></td></tr>';
        echo '<tr><td colspan="6" align="right">Total NETO:</td><td align="right"><b> $ ' . number_format($sum, 0, ',', '.') . '&nbsp;&nbsp;</b></td></tr>';
        echo'<tr><td colspan="6" align="right">IVA 19%:</td><td align="right"><b> $ ' . number_format(($sum * 0.19), 0, ',', '.') . '&nbsp;&nbsp;</b></td></tr>';
        echo'<tr><td colspan="6" align="right"><b>Total Solicitado (IVA Incluido):</b></td><td align="right"><b class="texto6"> $ ' . number_format(($sum * 1.19), 0, ',', '.') . '&nbsp;&nbsp;</b></td></tr>';
        echo '<tr><td colspan="2" align="right"><b>Observaciones:</b></td><td colspan="5" style="width:150px; height:50px; text-align:left;" >' . str_replace("\n","<br>",$obs) . '</td></tr>';
        echo '</table>';
        $_SESSION['pedido'] = 0;
        mysql_close($link);
        ?>
        <br/>
    <center><form><button class="boton" type="button" onclick="javascript:history.go(-2)">Volver&nbsp;&nbsp;<img class='img' alt='volver' title='Volver a pantalla anterior' src='../img/undo.png'></button>&nbsp;&nbsp;&nbsp;&nbsp;<button class="boton" type="button" onclick="window.location = '../principal.php'">Volver al Menu Principal&nbsp;<img class="img" alt="principal" title="volver al menu principal" src="../img/casa.png"></button>&nbsp;&nbsp;&nbsp;&nbsp;<button class="boton" type="button" onclick="window.print();">Imprimir&nbsp;<img class="img" alt="imprimir pantalla" title="imprimir pantalla" src="../img/print.png"></button>&nbsp;&nbsp;&nbsp;&nbsp;<button class="boton" type="button" onclick="window.location = 'editarPedido.php?id=<?=$id?>'">Modificar Solicitud&nbsp;<img class="img" alt="Modificar" title="modificar" src="../img/edit1.png"></button>&nbsp;&nbsp;&nbsp;&nbsp;<button class="boton" type="button" id="capturar" onclick="window.open('../reportes/pedidoPDF.php<?php echo'?id=' . $id ?>', '_blank', 'scrollbars=yes,statusbar=no,resizable=no,width=860,height=620 left=200,top=30')">Generar PDF&nbsp;<img class="img" alt="generar PDF" title="generar PDF" src="../img/pdf1.png"></button><?=$boton?></form></center>
</body>
</html>