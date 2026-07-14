<!DOCTYPE html>
<!--
Sigbod John Vaccarella
-->
<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: index.php');
    exit();
}
if ($_SESSION['perfil'] > 1) {
    $cargar = "hidden='yes'";
} else {
    $cargar = "";
}
$usuario = $_SESSION["usuario"];
$_SESSION['query'] = "";
$_SESSION["cambiar"] = 1;
$_SESSION['seg'] = -1; //seguimiento
$fecha_ingreso = date_create($_SESSION["fecha"]);
$date = date_format($fecha_ingreso, 'd/m/Y h:i A');
$anio = date('Y');
include("include/conn.php");
$link = Conectarse();
?>
<html lang="es">
    <head>       
        <link rel="stylesheet" href="css/estilos.css" />
        <link rel="stylesheet" href="css/tabla.css" />
        <link rel="shortcut icon" href="img/favicon.ico" />
        <link href="css/SpryAssets/SpryMenuBarHorizontal.css" rel="stylesheet" type="text/css" />     
        <script src="js/SpryMenuBar.js" type="text/javascript"></script>
        <script src="js/jquery-2.2.4.js" type="text/javascript"></script>
        <script src="js/Chart.min.js" type="text/javascript"></script>
        <meta name="keywords" content="proveedores,solicitud despachos, sistema, sigbod, sigbod technology, sistema sigbod, abastecimiento, pedidos" />
        <meta name="author" content="John Vaccarella" />
        <script type="text/javascript">
            $(document).ready(function () {
                $(window).scroll(function () {
                    if ($(this).scrollTop() > 100) {
                        $('#scroll').fadeIn();
                    } else {
                        $('#scroll').fadeOut();
                    }
                });
                $('#scroll').click(function () {
                    $("html, body").animate({scrollTop: 0}, 600);
                    return false;
                });
            });
        </script>
        <script type="text/javascript">
            $(document).ready(function () {
                $("#graficos").hide();
                $("#h2p").hide();
                $("#h2p").fadeIn();
                $.ajax({//hace la busqueda
                    type: "POST",
                    url: "principalAjax.php",
                    beforeSend: function () {
                        $("#resultado").html("<p class='centrar_alerta'>Preparando Alertas, por favor espere...<img src='img/loader1.gif' width='400' height='100' /></p>"); //imagen de carga
                    },
                    error: function () {
                        alert("error peticion ajax");
                    },
                    success: function (data) {
                        $("#resultado").empty();
                        $("#resultado").append(data);
                        $("#resultado").hide();
                        $("#resultado").fadeIn();
                        dibujaGrafico();
                        $("#graficos").fadeIn(1000);
                    }
                });
            });
        </script>
        <script>
            function dibujaGrafico() {
                $.ajax({
                    url: "herramientas/buscaDocChart.php",
                    dataType: 'json',
                    contentType: "application/json; charset=utf-8",
                    method: "POST",
                    success: function (data) {
                        var nombre = [];
                        var valor = [];
                        var total = 0;
                        var color = ['rgba(255, 99, 132, 0.2)', 'rgba(54, 162, 235, 0.2)', 'rgba(255, 206, 86, 0.2)', 'rgba(75, 192, 192, 0.2)', 'rgba(255, 159, 64, 0.2)', 'rgba(25, 159, 64, 0.2)'];
                        var bordercolor = ['rgba(255,99,132,1)', 'rgba(54, 162, 235, 1)', 'rgba(255, 206, 86, 1)', 'rgba(75, 192, 192, 1)', 'rgba(153, 102, 255, 1)', 'rgba(25, 102, 255, 1)'];
                        $.each(data, function (i, item) {
                            valor.push(item.valor);
                            nombre.push(item.nombre);
                            total = total + parseInt(item.valor);
                        });
                        var chartdata = {
                            labels: nombre,
                            datasets: [{
                                    label: "Documentos en Curso Año: <?=$anio?>  (Total: " + total + ")",
                                    backgroundColor: color,
                                    borderColor: bordercolor,
                                    borderWidth: 1,
                                    hoverBackgroundColor: bordercolor,
                                    hoverCursor: 'pointer',
                                    hoverBorderColor: color,
                                    data: valor
                                }]
                        };
                        var options = {
                            responsive: true,
                            scales: {
                                yAxes: [
                                    {
                                        ticks: {
                                            beginAtZero: true
                                        }
                                    }
                                ]
                            },
                            onHover: function (evt, chartElement) {
                                // Cambiar el tipo de cursor al pasar el mouse sobre las barras
                                evt.target.style.cursor = chartElement[0] ? "pointer" : "default";
                            },
                            onClick: function (evt, activeElements) {
                                // Verificar si se ha hecho clic en una barra
                                if (activeElements.length > 0) {
                                    var index = activeElements[0]._index;
                                    var label = nombre[index];
                                    window.location.href = "herramientas/buscarDocumento.php?rec=2&op=1&est=" + label;
                                }
                            }
                        };
                        // Crear el gráfico
                        var ctx = document.getElementById("grafica").getContext("2d");
                        var myChart = new Chart(ctx, {
                            type: "horizontalBar",
                            data: chartdata,
                            options: options
                        });
                    },
                    error: function (data) {
                        console.log(data);
                    }
                });
            }
        </script>
        <script type="text/javascript">
            function actualizar() {
                location.reload(true);
            }
            setInterval("actualizar()", 600000); //Función para actualizar cada 90 segundos(90000 milisegundos)
        </script>
        <?php
        $sql2 = "SELECT version_num FROM version WHERE version_estado=1";
        $res2 = MySQL_query($sql2, $link) or die("<center><h2 class='texto'>Ha Ocurrido un Error en la version del sistema: </h2> " . mysql_error() . "<br/></center>");
        $row2 = MySQL_Fetch_array($res2);
        ?>
        <title>SigBod - Menú Principal V.<?= $row2["version_num"] ?></title>       
    </head>
    <body class="fondo">

        <?php
        $sql = "SELECT usuario_nombre, usuario_apellidos, perfil_nombre, uni_nombre FROM usuarios, perfiles, unid_oper WHERE usuario_rut='$usuario' AND usuario_perfil=perfil_id AND usuario_institucion=uni_id";
        $res = MySQL_query($sql, $link) or die("<center><h2 class='texto'>Ha Ocurrido un Error: </h2> " . mysql_error() . "<br/></center>");
        $row = MySQL_Fetch_array($res);
        echo '<h2 class="texto4a" align="center" id="h2p"><p><img  align="center" src="img/info.png" width="25" height="25" title="Elija una opcion del menu principal"/>&nbsp;&nbsp;BIENVENIDO A SIGBOD - INFORMACIÓN GENERAL:</p>Usuario: ' . $row["usuario_nombre"] . ' ' . $row["usuario_apellidos"] . ' , Perfil: <i>' . $row["perfil_nombre"] . '</i>, Unidad Operativa: <i>' . $row["uni_nombre"] . '</i><p>ULTIMO ACCESO: ' . $date . '</p></h2>';
        mysql_close($link);
        ?>
        <div id="menu">
            <ul id="MenuBar1" class="MenuBarHorizontal">
                <li <?= $cargar ?>><a class="MenuBarItemSubmenu" href="#"><img src="img/sub.png" alt="upload" class="imgmenu"/>&nbsp;&nbsp;&nbsp;&nbsp;Cargar Archivos</a>
                    <ul>
                        <li><a class="MenuBarItemSubmenu" href="#">Carga Archivos Sistema</a>
                            <ul>
                                <li><a href="carga/cargaExcelProv.php">Cargar CSV de Proveedores</a></li>
                                <li><a href="carga/cargaExcelProd.php">Cargar CSV de Productos</a></li>
                                <li><a href="carga/cargaExcelProdPrec.php">Cargar CSV de Glosas y Precios de Productos</a></li>
                                <li><a href="carga/cargaExcelStock.php">Cargar CSV de Stock</a></li>
                            </ul>
                        </li>
                        <li><a class="MenuBarItemSubmenu" href="#">Carga Archivos Documentos</a>
                            <ul>
                                <li><a href="carga/cargaExcelDoc.php">Cargar CSV de Documentos</a></li>
                                <li><a href="carga/cargaExcelSubdoc.php">Cargar CSV de SubDocumentos</a></li>
                                <li><a href="carga/cargaExcelDocObs.php">Cargar CSV de Observaciones de Documentos</a></li>
                            </ul>
                        </li>
                        <li><a class="MenuBarItemSubmenu" href="#">Carga Archivos Orden de Compra</a>
                            <ul>
                                <li><a href="carga/cargaExcelSaldo.php">Cargar CSV de Saldos OC</a></li>
                                <li><a href="carga/cargaExcelActSaldo.php">Cargar CSV de Recibidos OC</a></li>  
                            </ul>
                        </li>
                        <li><a class="MenuBarItemSubmenu" href="#">Carga Archivos PAC</a>
                            <ul>
                                <li><a href="carga/cargaExcelPAC.php">Cargar CSV de PAC</a></li>                      
                            </ul>
                        </li>
                    </ul>
                </li>                
                <li><a class="MenuBarItemSubmenu" href="#"><img src="img/report.png" alt="reportes" class="imgmenu"/>&nbsp;&nbsp;&nbsp;&nbsp;Reportes</a>
                    <ul>
                        <li><a class="MenuBarItemSubmenu" href="#">Art&iacute;culos</a>
                            <ul>
                                <li><a href="reportes/buscarCartolaArticulo.php">Cartola de Art&iacute;culos</a></li>
                                <li><a href="reportes/nivelStock.php">Niveles de Stock</a></li>								
                            </ul>
                        </li>
                        <li><a class="MenuBarItemSubmenu" href="#">Orden de Compra</a>
                            <ul>
                                <li><a href="herramientas/buscarOcSaldo.php">Estado Orden de Compra</a></li>
                                <li><a href="herramientas/saldoOc.php">Saldos de Orden de Compra</a></li>
                                <li><a href="exportar/exportaExcelSaldoNoFact.php">Ordenes Sin Pendiente y Sin Factura (Excel)</a></li>
                                <li><a href="herramientas/buscarFactOC.php">Facturación Mayor a Orden de Compra</a></li>
                            </ul>
                        </li>
                        <li><a class="MenuBarItemSubmenu" href="#">Proveedores</a>
                            <ul>
                                <li><a href="reportes/montoProv.php">Montos transados por Proveedor</a></li>							
                            </ul>
                        </li>                                                
                        <li><a class="MenuBarItemSubmenu" href="#">Pedidos</a>
                            <ul>
                                <li><a href="pedidos/pedidoEnviado.php">Pedidos Generados</a></li>
                                <li><a href="reportes/buscarCartolaProveedor.php">Cartola de Pedidos por Proveedor</a></li>
                                <li><a href="reportes/solicitudesPendientes.php">Solicitudes pendientes Sin Stock</a></li>
                                <li><a href="reportes/solicitudes.php">Productos Solicitados</a></li>
                            </ul>
                        </li>
                        <li><a class="MenuBarItemSubmenu" href="#">Sistema</a>
                            <ul>
                                <li <?= $cargar ?>><a href="reportes/accesos.php">Accesos al Sistema</a></li>                                
                                <li <?= $cargar ?>><a href="reportes/cargaLog.php">Registro de Carga Archivos</a></li>
                                <li <?= $cargar ?>><a href="reportes/contador.php">Estadisticas del Sistema</a></li>
                                <li><a onclick="window.open('herramientas/versionInfo.php', '_blank', 'scrollbars=yes,statusbar=no,resizable=no,width=340,height=360 left=400 top=200')">Versión</a></li>
                            </ul>
                        </li>
                    </ul>
                </li>
                <li><a class="MenuBarItemSubmenu" href="#"><img src="img/ped2.png" alt="pedidos" class="imgmenu"/>&nbsp;&nbsp;&nbsp;&nbsp;Pedidos</a>
                    <ul>
                        <li><a href="pedidos/pedidoProveedor.php">Generar Pedido por Proveedor</a></li>
                        <li><a href="pedidos/PedidoArticulo.php">Generar Pedido por Art&iacute;culo</a></li>
                        <li><a href="pedidos/PedidoArticuloPAC.php">Generar Pedido por Art&iacute;culo, Basado en PAC</a></li>
                        <li><a href="reportes/crearCotizacion.php">Generar Cotizacion</a></li>
                    </ul>
                </li>
                <li><a class="MenuBarItemSubmenu" href="#"><img src="img/exist.png" alt="existencias" class="imgmenu"/>&nbsp;&nbsp;&nbsp;&nbsp;Existencias</a>
                    <ul>
                        <li><a class="MenuBarItemSubmenu" href="#">Recepcion</a>
                            <ul>
                                <li><a href="recepcion/recepHistorico.php">Recepciones Historicas</a></li>
                                <li><a href="recepcion/recepHistoricoDet.php">Detalle Recepciones Historicas</a></li>
                            </ul>
                        </li>
                        <li <?= $cargar ?>><a class="MenuBarItemSubmenu" href="#">Despacho</a>
                            <ul>
                                <li><a href="">en desarrollo</a></li>
                            </ul>
                        </li>
                    </ul>
                </li>
                <li><a class="MenuBarItemSubmenu" href="#"><img src="img/gest.png" alt="gestion" class="imgmenu"/>&nbsp;&nbsp;&nbsp;&nbsp;Gesti&oacute;n</a>
                    <ul>
                        <li><a class="MenuBarItemSubmenu" href="#">Documentos</a>
                            <ul>
                                <li><a href="herramientas/cargaDocumento.php">Crear Nuevo Documento</a></li>
                                <li><a href="herramientas/buscarDocumento.php">Editar/Consultar Documentos</a></li>                                
                                <li><a href="herramientas/buscarDocDetalleOC.php">Consultar Documentos V/S OC</a></li>
                                <li <?= $cargar ?>><a href="edicion/crearSeguimiento.php">Crear Seguimiento de Documentos</a></li>
                                <li><a href="herramientas/buscarSeguimiento.php">Buscar Seguimiento de Documentos</a></li>                                
                            </ul>
                        </li>
                        <li><a class="MenuBarItemSubmenu" href="#">Orden de Compra</a>
                            <ul>
                                <li><a href="herramientas/buscarOc.php">Editar/Consultar Ordenes de Compra</a></li>
								<li><a href="herramientas/buscarOcObs.php">Consultar Observaciones de Ordenes de Compra</a></li>
                                <li><a href="herramientas/crearOc.php">Crear Orden de Compra</a></li>   
                                <li><a href="herramientas/listarOcMP.php">Listar Orden de Compra desde Mercado Público</a></li>   
                                <li><a href="herramientas/buscarInfoOC.php">Información Avanzada de Orden de Compra</a></li>                                
                            </ul>
                        </li>
						<li><a class="MenuBarItemSubmenu" href="#">Licitaciones</a>
                            <ul>
                                <li><a href="herramientas/buscarLic.php">Consultar Licitación</a></li>
                                <li><a href="herramientas/listarLicMP.php">Listar Licitaciones desde Mercado Público</a></li>   
                            </ul>
                        </li>
                        <li><a class="MenuBarItemSubmenu" href="#">PAC</a>
                            <ul>
                                <li><a href="herramientas/buscarPAC.php">Editar/Consultar PAC</a></li>
                            </ul>
                        </li>
                        <li><a class="MenuBarItemSubmenu" href="#">Stocks</a>
                            <ul>
                                <li><a href="reportes/analisisStock.php">Análisis de Stock</a></li>
                                <li><a href="herramientas/agruparArticulo.php">Articulos Agrupados</a></li>
                                <li><a href="herramientas/buscarNivelStock.php">Niveles de Stock</a></li>
                                <li><a href="reportes/listarRepo.php">Productos a Reponer</a></li> 
                                <li><a href="herramientas/buscarStock.php">Stock Sistema</a></li>
                            </ul>
                        </li>
                    </ul>
                </li>
                <li><a class="MenuBarItemSubmenu" href="#"><img src="img/eng.png" alt="administracion" class="imgmenu"/>&nbsp;&nbsp;&nbsp;&nbsp;Administraci&oacute;n</a>
                    <ul>
                        <li><a class="MenuBarItemSubmenu" href="#">Productos</a>
                            <ul>
                                <li><a href="herramientas/buscarProductos.php">Editar/Consultar Productos</a></li>
                                <li <?= $cargar ?>><a href="edicion/ModificarArticulo.php">Crear Producto</a></li>
                                <li><a href="edicion/crearGrupo.php">Crear Grupo de Productos</a></li>
                                <li><a onclick="window.open('herramientas/leeCodigoBarra.php', '_blank', 'scrollbars=0,statusbar=0,resizable=0,width=800,height=500,left=300,top=100')">Lector de Código de Barras</a></li>
                            </ul>
                        </li>
                        <li><a class="MenuBarItemSubmenu" href="#">Usuarios</a>
                            <ul>
                                <li><a href="herramientas/buscarUsuarios.php">Editar/Consultar Usuarios</a></li>
                                <li <?= $cargar ?>><a href="herramientas/registroUsuario.php">Crear Usuario</a></li>
                                <li><a onclick="window.open('herramientas/cambiopassword.php', '_blank', 'scrollbars=0,statusbar=0,resizable=0,width=440,height=200,left=400,top=250')">Cambiar Password</a></li>                       
                            </ul>
                        </li>						
                        <li><a class="MenuBarItemSubmenu" href="#">Proveedores</a>
                            <ul>                       
                                <li><a href="herramientas/buscarProveedor.php">Editar/Consultar Proveedores</a></li>
                                <li <?= $cargar ?>><a href="edicion/ModificarProveedor.php">Crear Proveedor</a></li>
                            </ul>                            
                        </li>
                        <li <?= $cargar ?>><a class="MenuBarItemSubmenu" href="#">Sistema</a>
                            <ul>                       
                                <li><a href="carga/cargaManuales.php">Editar/Cargar Manuales</a></li>
                                <li><a href="herramientas/editarFondo.php">Cambiar Fondo del Sitio</a></li>  
                                <li><a href="edicion/editarConst.php">Cambiar Constantes de Configuración</a></li> 								
                            </ul>                            
                        </li>                        
                    </ul>
                </li>
                <li><a class="MenuBarItemSubmenu" href="#"><img src="img/help.png" alt="ayuda" class="imgmenu"/>&nbsp;&nbsp;&nbsp;&nbsp;Ayuda</a>
                    <ul>
                        <li><a href="Ayuda.php">Ver Ayuda de Sigbod</a></li>
                    </ul>
                </li>
                <li><a href="salir.php" class="color_menu"><img src="img/salir.png" alt="salir" class="imgmenu"/>&nbsp;&nbsp;&nbsp;&nbsp;Salir</a></li>
            </ul>            
        </div>
        <script type="text/javascript">
            var MenuBar1 = new Spry.Widget.MenuBar("MenuBar1", {imgDown: "css/SpryAssets/SpryMenuBarDownHover.gif", imgRight: "css/SpryAssets/SpryMenuBarRightHover.gif"});
        </script>
        <br/>
        <div id="resultado"></div>
        <br>
        <div id="graficos" class="chart">
            <canvas id="grafica" style="width: 784px; height: 220px;"></canvas>
        </div>
        <br>
        <a href="javascript:void(0);" id="scroll" title="Ir Arriba" style="display: none;">Arriba<span></span></a>
    </body>
</html>