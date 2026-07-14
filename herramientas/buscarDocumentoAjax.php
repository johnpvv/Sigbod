<?php

session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: ../noSesion.php');
    exit();
}
include("../include/conn.php");
$link1 = Conectarse(); //Variable de coneccion
$buscar = $_POST['saldo'];
$vertodo = $_POST["chkall"];
$subdoc = $_POST["chksub"];
$fact = $_POST["chkfac"];
$anio = $_POST["anio"];
$anio2 = $_POST["anio2"] . " 23:59:59";
$fmod = $_POST["fmod"];
$fmod1 = $_POST["fmod1"] . " 23:59:59";
$fdoc = $_POST["fdoc"];
$fdoc1 = $_POST["fdoc1"]. " 23:59:59";
$chkst = $_POST["chkst"];
$rec = $_POST["rec"];
$comilladob = '"';
$comillasim = "'";
if ($chkst == "true") {
    $st = " AND doc_obs <> '' ";
} else {
    $st = "";
}
if ($subdoc != "") {
    if ($subdoc == "1") {
        $subdoc = " AND doc_estado='' ";
        $orden = " sortReset   : true "; //, sortList: [[3,1]] ";
    } else if ($subdoc == "0") {
        $subdoc = " AND doc_estado<>'' ";
    } else if ($subdoc == "2" || $subdoc == "Pendiente") {
        $subdoc = " AND doc_estado ='' AND doc_obs NOT LIKE '%reclam%' AND doc_tipodoc= 'Factura' ";
    } else if ($subdoc == "Reclamadas") {
        $subdoc = " AND doc_estado ='' AND doc_obs LIKE '%reclam%' AND doc_tipodoc= 'Factura' ";
    } else {
        $subdoc = " AND doc_estado='$subdoc' ";
    }
} else {
    $subdoc = "";
}
if ($fact != "") {
    $fact = "AND doc_tipodoc='$fact' "; //tipo de documento a buscar
} else {
    $fact = "";
}
if ($anio != "") {
    $anio1 = "AND doc_fechacarga BETWEEN '$anio' AND '$anio2' ";
}
if ($fmod != "") {
    $fmod2 = "AND doc_fechamod BETWEEN '$fmod' AND '$fmod1' ";
}
if ($fdoc != "") {
    $fdoc2 = "AND doc_fechadoc BETWEEN '$fdoc' AND '$fdoc1' ";
}
if ($rec == 1) {
    $fdoc2 .= " AND doc_obs NOT LIKE '%reclam%' AND doc_tipodoc= 'Factura' ";
}
if (empty($buscar) and $vertodo == "true") {
    buscar($buscar, $subdoc, $fact, $anio1, $fmod2, $fdoc2, $orden, $st);
} else if (!empty($buscar)) {
    buscar($buscar, $subdoc, $fact, $anio1, $fmod2, $fdoc2, $orden, $st);
} else {
    echo "<script>alert('Error, ingrese algun dato para Buscar');</script>";
}

function buscar($saldo, $subdoc, $fact, $anio1, $fmod2, $fdoc2, $orden, $st) {
    global $comilladob;
    global $comillasim;
    if (strlen($saldo) > 30) {//si se escriben menos de 20 caracteres buscara
    } else {
        $ark = explode(" ", $saldo); //inicio filtro anidado, llena arreglo con los caracteres a buscar, considera espacio como separador
        if (count($ark) <= 1) {
            $sql1 = "SELECT * "
                    . "FROM proveedores, documento "
                    . "WHERE doc_rutpv=prov_rut " . $subdoc . " " . $fact . " " . $anio1 . " " . $fmod2 . " " . $fdoc2 . " " . $st . " "
                    . "AND (prov_nombre LIKE '%" . $ark[0] . "%' or doc_id LIKE '%" . $ark[0] . "%' or prov_rut LIKE '%" . $ark[0] . "%' or doc_ndoc LIKE '%" . $ark[0] . "%' or doc_noc LIKE '%" . $ark[0] . "%' or doc_obs LIKE '%" . $ark[0] . "%' or doc_origen LIKE '%" . $ark[0] . "%' or doc_estado LIKE '%" . $ark[0] . "%' or concat_ws(' ', doc_origen, doc_norigen) LIKE '%" . $ark[0] . "%')"
                    . "ORDER BY prov_nombre, doc_id DESC";
        } else {
            $sql1 = "SELECT * "
                    . "FROM proveedores, documento "
                    . "WHERE doc_rutpv=prov_rut " . $subdoc . " " . $fact . " " . $anio1 . " " . $fmod2 . " " . $fdoc2 . " " . $st . " ";
            for ($i = 0; $i <= count($ark); $i++) {
                if (!empty($ark[$i])) {
                    $sql1 .= " AND (prov_nombre LIKE '%" . $ark[$i] . "%' or doc_id LIKE '%" . $ark[$i] . "%' or prov_rut LIKE '%" . $ark[$i] . "%' or doc_ndoc LIKE '%" . $ark[$i] . "%' or doc_noc LIKE '%" . $ark[$i] . "%' or doc_obs LIKE '%" . $ark[$i] . "%' or doc_origen LIKE '%" . $ark[$i] . "%' or doc_estado LIKE '%" . $ark[$i] . "%' or concat_ws(' ', doc_origen, doc_norigen) LIKE '%" . $ark[$i] . "%')";
                }
            }
            $sql1 .= "ORDER BY prov_nombre, doc_id DESC";
        }
    }
    //echo $sql1;
    $data = mysql_query($sql1);
    $_SESSION['query'] = $sql1;
    $contar = mysql_num_rows($data);
    if ($contar === 0) {
        echo "<p class='texto2'><img class='imginfo' src='../img/info.png' title='se muestran los documentos que cumplen con los filtros.'/>&nbsp;&nbsp;No hay datos para Mostrar...Revise si hay algun error en la escritura, o revise bien los filtros aplicados.</p><img class='imgnoenc' src='../img/nores.png' title='sin Resultados.'/>";
        $_SESSION['query'] = "";
    } else {
        if ($saldo == "") {
            echo "<p class='texto2'><img class='imginfo' src='../img/info.png' title='se muestran todos los documentos que cumplen con los filtros.'/>&nbsp;&nbsp;Mostrando todos los Registros. (se han encontrado: <b>$contar</b> Resultados)</p>";
        } else {
            echo "<p class='texto2'><img class='imginfo' src='../img/info.png' title='se muestran todos los documentos que cumplen con los filtros.'/>&nbsp;&nbsp;Se han encontrado <b>$contar</b> resultados para: *<b>" . $saldo . "</b>*</p>";
        }
        echo '<table id="tabladoc" class="table2e">';
        echo '<thead><th>N° Reg.</th><th>Num. Doc.</th><th width="50">Tipo Doc.</th><th width="130" data-sortinitialorder="desc">Proveedor</th><th>Fecha Doc.</th><th>Fecha creación</th><th>Fecha Modif.</th><th>O.C.</th><th>Monto Total</th><th>Cant. Subdoc.</th><th width="40">Origen Doc.</th><th width="90">Observaciones</th><th>Estado</th><th style="width:155px; word-wrap: break-word;" data-sorter="false" data-filter="false">Acciones:</th></thead>';
        while ($row = mysql_fetch_array($data)) {
            $link = $row["doc_url"];
            $dococ = "<a href=../edicion/modificarSaldo.php?id=" . $row["prov_rut"] . "&oc=" . $row["doc_noc"] . "&op=1 target='_blank' title='Ver detalle OC' onclick=" . $comilladob . "window.open(this.href, this.target, " . $comillasim . "height=640, width=1280 left=20 top=10" . $comillasim . "); return false;" . $comilladob . ">" . $row["doc_noc"] . "</a><br><a href='#' id='".$row["doc_noc"]."' onclick='buscaCod(this.id);' title='Buscar por esta OC'>▲</a>";
            $id = $row["doc_id"];
            if ($row["doc_subdoc_cuenta"] > 0) {
                $subdoc = "<a href=../herramientas/buscarSubdocumento.php?id=" . $row["doc_id"] . "&tipo=1 target='_blank' title='Visualizar Subdocumentos' onclick=" . $comilladob . "window.open(this.href, this.target, " . $comillasim . "height=620, width=840 left=250 top=20" . $comillasim . "); return false;" . $comilladob . "><img border='0' alt='subdoc' title='Visualizar Subdocumentos' src='../img/lupa.png' width='20' height='20'></a>|";
            } else {
                $subdoc = "<img border='0' alt='Documento no Encontrado' title='Subdocumento no Encontrado' src='../img/noenc.png' width='20' height='20'>|";
            }
            if ($link <> "") {
                $url = "|&nbsp;<a href=$link target='_blank' title='Ver DTE' id='link_" . $row["doc_id"] . "'  onfocus='cambiaImg(this.id);' onclick=" . $comilladob . "window.open(this.href, this.target, " . $comillasim . "height=560, width=840 left=250 top=30" . $comillasim . "); return false;" . $comilladob . "><img border='0' alt='doc' title='Ver DTE' src='../img/fact.png' width='19' height='20' id='img_" . $row["doc_id"] . "'></a>&nbsp;";
                $traza = "|<a href=https://escritorio.acepta.com/traza/?url=" . $link . " target='_blank' title='Ver Traza' id='linkt_" . $row["doc_id"] . "' onfocus='cambiaImgTraza(this.id);' onclick=" . $comilladob . "window.open(this.href, this.target, " . $comillasim . "height=640, width=840 left=250 top=10" . $comillasim . "); return false;" . $comilladob . "><img border='0' alt='doc' title='Ver Traza en Acepta' src='../img/track.png' width='20' height='20' id='imgt_" . $row["doc_id"] . "'></a>";
            } else {
                $url = "";
                $traza = "";
            }
            if ($row["doc_estado"] == "") {
                $mail = "&nbsp;|<a href=../herramientas/enviarMailFact.php?id=" . $id . "&rt=" . $row["prov_rut"] . " target='_blank' title='Enviar Mail' id='linkm_" . $row["doc_id"] . "' onfocus='cambiaImgMail(this.id);' onclick=" . $comilladob . "window.open(this.href, this.target, " . $comillasim . "height=200, width=480 left=450 top=20" . $comillasim . "); return false;" . $comilladob . "><img border='0' alt='Enviar mail' id='imgm_" . $row["doc_id"] . "' title='Enviar mail para solicitar respaldos de recepcion' src='../img/mail.png' width='20' height='20'></a>";
            } else {
                $mail = "";
            }
            if ($row["doc_fechadoc"] != "0000-00-00") {
                $fechadoc = date("d/m/Y", strtotime($row["doc_fechadoc"]));
            } else {
                $fechadoc = "";
            }
            if ($row["doc_fechamod"] != "0000-00-00 00:00:00") {
                $fechamod = date("d/m/Y, H:i", strtotime($row["doc_fechamod"]));
            } else {
                $fechamod = "";
            }
            if ($row["doc_origen"] != "") {
                $origen = $row["doc_origen"];
            } else {
                $origen = "";
            }if ($row["doc_norigen"] != 0) {
                $origen = $origen . " N°" . $row["doc_norigen"];
            } else {
                $origen = $origen . "";
            } if ($row["doc_fechaorigen"] != "0000-00-00") {
                $origen = $origen . ", Fecha: " . date("d/m/Y", strtotime($row["doc_fechaorigen"]));
            } else {
                $origen = $origen . "";
            }
            $rs = mysql_query("SELECT mail_doc,mail_fecha,mail_id FROM mail WHERE mail_doc ='$id' ORDER BY mail_id DESC LIMIT 1"); //saber si se ha enviado mail al proveedor por respaldos
            $row2 = mysql_fetch_array($rs);
            $mailen = $row2["mail_doc"];
            if ($mailen != "") {
                $mailen = "<a href=../reportes/envioMail.php?id=" . $id . " target='_blank' onclick=" . $comilladob . "window.open(this.href, this.target, " . $comillasim . "height=400, width=840 left=250 top=30" . $comillasim . "); return false;" . $comilladob . "><img width='20' height='18' src='../img/mailenv.png' title='Correo ya enviado a proveedor, el: " . date("d/m/Y, H:i", strtotime($row2["mail_fecha"])) . "'></a>";
            } else {
                $mailen = "";
            }
            printf("<tr><td><i>%s</i></td><td><b>%s</b></td><td>%s</td><td align='left'><b>%s</b></td><td>%s</td><td><b>%s</b></td><td>%s</td><td>%s</td><td><b>$</b>%s</td><td>%s</td><td>%s</td><td>%s</td><td><b>%s</b></td><td>%s</td></tr>", $id, $row["doc_ndoc"] . "<br>" . $mailen, $row["doc_tipodoc"], "<a href=../edicion/modificarProveedor.php?id=" . $row["prov_rut"] . "&option=1 target='_blank' title='Ver detalle proveedor' onclick=" . $comilladob . "window.open(this.href, this.target, " . $comillasim . "height=540, width=800 left=300 top=100" . $comillasim . "); return false;" . $comilladob . ">" . number_format($row["prov_rut"], 0, '.', '.') . "-" . $row["prov_dv"] . "</a>&nbsp;" . " " . $row["prov_nombre"], $fechadoc, date("d/m/Y, H:i", strtotime($row["doc_fechacarga"])), $fechamod, $dococ, number_format($row["doc_montototal"], 0, ',', '.'), $row["doc_subdoc_cuenta"], $origen, $row["doc_obs"], $row["doc_estado"], $subdoc . "<a href=../edicion/modificarDocumento.php?id=" . $row["doc_id"] . "&tipo=1 target='_blank' title='Editar Documento' onclick=" . $comilladob . "window.open(this.href, this.target, " . $comillasim . "height=380, width=1320 left=10 top=20" . $comillasim . "); return false;" . $comilladob . "><img border='0' alt='editar documento' title='editar Documento: " . $row["doc_ndoc"] . "' src='../img/edit1.png' width='20' height='20'></a>|<a href=../reportes/verRegistroPDF.php?id=" . $row["doc_id"] . " target='_blank'><img border='0' alt='reporte' title='Generar Reporte : " . $row["doc_id"] . "' src='../img/pdf.png' width='21' height='20'></a>" . $url . $traza . $mail);
        }
    }
    echo '</table>';
    echo "<br>";
    if ($contar > 10) {
        echo '<div id="pager" class="pagerform">
        <form>
            <img src="../js/images/first.png" class="first" style="cursor:pointer;" title="Ir a Página Inicial"/>
            <img src="../js/images/prev.png" class="prev" style="cursor:pointer;" title="Ir a Página Anterior"/>
            &nbsp;<input type="text" class="pagedisplay" size="45" readonly style="border:1px solid #808080;font-weight: bold;text-align: center;"/>
            <img src="../js/images/next.png" class="next" style="cursor:pointer;" title="Ir a Página Siguiente"/>
            <img src="../js/images/last.png" class="last" style="cursor:pointer;" title="Ir a Página Final"/>&nbsp;
            <select class="pagesize" style="border:1px solid #808080;" title="Cantidad de elementos Mostrados por Página">
		<option value="5">5</option>
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="30">30</option>
                <option value="40">40</option>
                <option value="50">50</option>
                <option value="100">100</option>
				<option value="300" selected="selected">300</option>
				<option value="500">500</option>
            </select>
	<select class="gotoPage" title="Ir a Pagina"></select>
        </form>
    </div>';
    }
    echo '<script type="text/javascript">
        $(document).ready(function (){
            $("#tabladoc").tablesorter({' . $orden . '}).
            tablesorterPager({container: $("#pager"),
		//showProcessing: true,
		size: 300,
		output: "Mostrando Registros del {startRow} al {endRow} (Total: {totalRows})"
            });
        //para crear animacion mientras se ordena la tabla
            $("#tabladoc").on("sortStart", function() {
		$(this).addClass("load");
		}).on("sortEnd", function() {
		$(this).removeClass("load");
            });
	});
        </script>';
}

mysql_close($link1);
?>