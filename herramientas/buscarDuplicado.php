<!DOCTYPE html>
<html lang="es">
    <head>
        <link rel="stylesheet" href="../css/tabla.css" />
        <link rel="stylesheet" href="../css/estilos.css" />
        <link rel="shortcut icon" href="../img/favicon.ico" />          
        <script src="../js/jquery-2.2.4.js" type="text/javascript"></script>
        <script src="../js/jquery.blockUI.js" type="text/javascript"></script>
        <script src="../js/tablesorter.min.js" type="text/javascript"></script>
        <script src="../js/jquery.tablesorter.pager.js"></script> 
        <title>SigBod - Buscar duplicado</title>
    </head>
    <body class="fondo">   
        <?php
        session_start();
        if (!isset($_SESSION['usuario'])) {
            header('Location: ../noSesion.php');
            exit();
        }
        include("../include/conn.php");
        $link = Conectarse(); //Variable de coneccion
        set_time_limit(3000);
        /* $buscar = $_POST['b'];
          $chk = $_POST['chk'];
          $vertodo = $_POST["chkall"];
          $chimg = $_POST['chimg'];
          $chnv = $_POST['chnv'];
          $comilladob = '"';
          $comillasim = "'";
          $_SESSION['query'] = "";
          if ($chk == "true") {
          $checktipo = " prd_destacado=1 AND ";
          } else {
          $checktipo = "";
          }
          if ($chnv == "true") {
          $chknv = " prd_estado=0 AND ";
          } else {
          $chknv = "";
          }
          if ($chimg == "true") {
          $img = " prd_imagen != '' AND ";
          } else {
          $img = "";
          }
          if (empty($buscar) and $vertodo == "true") {
          buscar($buscar, $checktipo, $img, $chknv);
          }
          if (!empty($buscar) and strlen($buscar) >= 3) {
          buscar($buscar, $checktipo, $img, $chknv);
          }

          function buscar($b, $checktipo, $img, $chknv) {
          global $comilladob;
          global $comillasim;

          $ark = explode(" ", $b); //inicio filtro anidado, llena arreglo con los caracteres a buscar, considera espacio como separador
          if (count($ark) <= 1) {
          $sql = "SELECT * FROM productos, unimed, estado  WHERE " . $checktipo . $img . $chknv . " prd_unimed = unimed_id AND prd_estado=estado_id"
          . " AND (prd_codigo LIKE '%" . $ark[0] . "%' or prd_glosa LIKE '%" . $ark[0] . "%'or prd_ref LIKE '%" . $ark[0] . "%'or prd_codcm LIKE '%" . $ark[0] . "%' or prd_cenabast LIKE '%" . $ark[0] . "%' or prd_barcode LIKE '%" . $ark[0] . "%' or prd_fecha LIKE '%" . $ark[0] . "%')";
          } else {
          $sql = "SELECT * FROM productos, unimed, estado  WHERE " . $checktipo . $img . $chknv . "  prd_unimed = unimed_id AND prd_estado=estado_id";
          for ($i = 0; $i <= count($ark); $i++) {
          if (!empty($ark[$i])) {
          $sql .= " AND (prd_codigo LIKE '%" . $ark[$i] . "%' or prd_glosa LIKE '%" . $ark[$i] . "%'or prd_ref LIKE '%" . $ark[$i] . "%'or prd_codcm LIKE '%" . $ark[$i] . "%' or prd_cenabast LIKE '%" . $ark[$i] . "%' or prd_barcode LIKE '%" . $ark[$i] . "%' or prd_fecha LIKE '%" . $ark[$i] . "%')";
          }
          }
          }
          $data = mysql_query($sql);
          $_SESSION['query'] = $sql;
          $contar = mysql_num_rows($data);
          if ($contar === 0) {
          echo "<p class='texto2'><img class='imginfo' src='../img/info.png' title='se muestran los productos que cumplen con los filtros.'/>&nbsp;&nbsp;No hay datos para Mostrar...Revise si hay algun error en la escritura, o revise bien los filtros aplicados.</p><img class='imgnoenc' src='../img/nores.png' title='sin Resultados.'/>";
          $_SESSION['query'] = "";
          } else {
          if ($b == "") {
          echo "<p class='texto2'><img src='../img/info.png' class='imginfo' title='se muestran todos los productos que cumplen con los filtros.'/>&nbsp;&nbsp;Mostrando todos los Registros. (se han encontrado: <b>$contar</b> Resultados)</p>";
          } else {
          echo "<p class='texto2'><img src='../img/info.png' class='imginfo' title='se muestran todos los productos que cumplen con los filtros.'/>&nbsp;&nbsp;Se han encontrado <b>$contar</b> resultados para: *<b>" . $b . "</b>*</p>";
          }
          echo '<table class="table2" id="tabladoc">
          <thead><tr>
          <th>Fam.</th><th width="80">C&oacute;digo Producto</th><th>Nombre Producto</th><th>Unidad Medida</th><th>Referencia Proveedor</th><th>ID Conv. Marco</th><th>C&oacute;digo Cenabast</th><th width="90">Precio Neto</th><th>Estado</th><th width="40">Tipo</th><th width="40">Imagen</th><th width="120" data-sorter="false" data-filter="false">Acciones:</th>
          </tr></thead>';
          while ($row = mysql_fetch_array($data)) {
          $dest = $row["prd_destacado"];
          if ($dest == "1") {
          $estrella = "<img border='0' alt='destacado' title='Producto Destacado' src='../img/estrella.png' width='25' height='25'>";
          } else {
          $estrella = "";
          }
          if ($row["prd_imagen"] == "") {
          $imagen = "";
          } else {
          $imagen = "<a href=../herramientas/visorImagen.php?id=" . $row["prd_codigo"] . "&tipo=1 target='_blank' title='Ver Imagen' onclick=" . $comilladob . "window.open(this.href, this.target, " . $comillasim . "height=600, width=720 left=300 top=30" . $comillasim . "); return false;" . $comilladob . "><img class='imgmenu' alt='imagen' title='ver imagen' src='../img/cam.png'></a>";
          }
          printf("<tr><td>%s</td><td><b>%s</b></td><td align='left'>%s</td><td>%s</td><td>%s</td><td>%s</td><td><b>%s</b></td><td align='left'>%s</td><td><b>%s</b></td><td><b>%s</b></td><td><b>%s</b></td><td align='center' width='80'>%s</td></tr>", $row["prd_fam"], $row["prd_codigo"], $row["prd_glosa"], $row["unimed_nombre"], $row["prd_ref"], '<a href="http://www.mercadopublico.cl/TiendaFicha/Ficha?idProducto=' . $row["prd_codcm"] . '" target="_blank">' . $row["prd_codcm"] . '</a>', $row["prd_cenabast"], "$ " . number_format($row["prd_precio"], 2, ',', '.'), $row["estado_nombre"], $estrella, $imagen, "<a href=../edicion/ModificarArticulo.php?id=" . $row["prd_codigo"] . "&tipo=1 target='_blank' title='Editar Articulo' onclick=" . $comilladob . "window.open(this.href, this.target, " . $comillasim . "height=620, width=820 left=250 top=20" . $comillasim . "); return false;" . $comilladob . "><img border='0' alt='editar articulo' title='Editar articulo en ventana externa' src='../img/edit1.png' width='25' height='25'></a>&nbsp;|&nbsp;
          <a href=../reportes/CartolaArticulo.php?id=" . $row["prd_codigo"] . "><img border='0' alt='movimiento articulo' title='ver movimientos articulo: " . $row["prd_codigo"] . "' src='../img/lupa.png' width='25' height='25'></a>&nbsp;|&nbsp;
          <a href=../edicion/modificarNivelStock.php?fam=" . substr($row["prd_codigo"], 0, 3) . "&cod=" . $row["prd_codigo"] . " target='_blank'><img border='0' alt='editar Nivel Stock articulo' title='Editar Nivel Stock articulo: " . $row["prd_codigo"] . "' src='../img/edit.png' width='25' height='25'></a>");
          }
          }
          echo '<tr><td colspan="12" class="texto8c"></td></tr>';
          echo '</table>';
          echo '<script type="text/javascript">$(document).ready(function () {$("#tabladoc").tablesorter();});</script>';
          } */
        /* $ark = explode(" ", $b); //inicio filtro anidado, llena arreglo con los caracteres a buscar, considera espacio como separador
          if (count($ark) <= 1) {
          $sql = "SELECT * FROM productos, unimed, estado  WHERE " . $checktipo . $img . $chknv . " prd_unimed = unimed_id AND prd_estado=estado_id"
          . " AND (prd_codigo LIKE '%" . $ark[0] . "%' or prd_glosa LIKE '%" . $ark[0] . "%'or prd_ref LIKE '%" . $ark[0] . "%'or prd_codcm LIKE '%" . $ark[0] . "%' or prd_cenabast LIKE '%" . $ark[0] . "%' or prd_barcode LIKE '%" . $ark[0] . "%' or prd_fecha LIKE '%" . $ark[0] . "%')";
          } else {
          $sql = "SELECT * FROM productos, unimed, estado  WHERE " . $checktipo . $img . $chknv . "  prd_unimed = unimed_id AND prd_estado=estado_id";
          for ($i = 0; $i <= count($ark); $i++) {
          if (!empty($ark[$i])) {
          $sql .= " AND (prd_codigo LIKE '%" . $ark[$i] . "%' or prd_glosa LIKE '%" . $ark[$i] . "%'or prd_ref LIKE '%" . $ark[$i] . "%'or prd_codcm LIKE '%" . $ark[$i] . "%' or prd_cenabast LIKE '%" . $ark[$i] . "%' or prd_barcode LIKE '%" . $ark[$i] . "%' or prd_fecha LIKE '%" . $ark[$i] . "%')";
          }
          }
          } */
        // $sql = "SELECT prd_codigo, prd_glosa FROM productos ORDER BY prd_glosa";
        $sql = "SELECT prd_codigo, prd_glosa, count(prd_glosa) AS LARGO FROM productos GROUP BY prd_glosa HAVING COUNT(*)>1";
        //echo $sql;
        $data = mysql_query($sql);
        $contar = mysql_num_rows($data);
        echo "<p class='texto2'>Mostrando todos los Registros. (se han encontrado: <b>$contar</b> Resultados)</p>";
        echo '<table class="table2" id="tabladoc">
         <thead><tr>
             <th width="80">C&oacute;digo Producto</th><th>Nombre Producto</th><th>Veces repetidas</th><th>Codigo Repetido:</th>
         </tr></thead>';

/*
        $res2 = mysql_query("SELECT prd_codigo AS C, prd_glosa AS T FROM productos", $link);
       $i=0;
        while ($row2 = mysql_fetch_array($res2)) {//llenar arreglo con los saldos y dejarlo preparado para consultar las oc con saldo
            $cod[$i] = $row2["T"];
            $i++;
        }
        $j=0;
        $res3 = mysql_query("SELECT prd_codigo AS C, prd_glosa AS T FROM productos", $link);
        while ($row3 = mysql_fetch_array($res3)) {//llenar arreglo con los saldos y dejarlo preparado para consultar las oc con saldo
            $cod1[$j] = $row3["T"];
            $j++;
        }

        for ($i = 0; $i < count($cod); $i++) { 
            


            for ($j = 0; $j < count($cod1); $j++) {         // Bucle interior
               if($cod[$i]== $cod1[$j]){
                   echo "dupicado(".$cod[$i]."<br>";
               }
                
//                if(){
                    //echo $cod[$i];
//                }
                //
            }
        }
        
        
        
        
        
        
        */
        
        
        
       while ($row = mysql_fetch_array($data)) {
           $glosa=$row["prd_glosa"];
           //$glosaarr= $cod[$row["prd_codigo"]];
            //if ($glosa == $glosaarr ) {
            $sql1 = "SELECT prd_codigo, prd_glosa FROM productos where prd_glosa='$glosa' ORDER BY prd_codigo DESC Limit 1";
            //echo $sql1;
        $data1 = mysql_query($sql1);
            
            $row1 = mysql_fetch_array($data1);
                printf("<tr><td><b>%s</b></td><td align='left'>%s</td><td>%s</td><td>%s</td></tr>", $row["prd_codigo"], $row["prd_glosa"],$row["LARGO"],$row1["prd_codigo"]);
           //}
        }

        echo '<tr><td colspan="12" class="texto8c"></td></tr>';
        echo '</table>';
        mysql_close($link);
        ?>
    </body>
</html>