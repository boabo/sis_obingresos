<?php
/**
 *@package BoA
 *@file    ACTReportes.php
 *@author  franklin.espinoza
 *@date    11-04-2020 15:14:58
 *@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
 */


require_once(dirname(__FILE__).'/../reportes/RReporteCruceAtcXLS.php');
require_once(dirname(__FILE__).'/../reportes/RReporteCruceLinkserXLS.php');
require_once(dirname(__FILE__).'/../reportes/RReporteCruceTigoXLS.php');
require_once(dirname(__FILE__).'/../reportes/RReporteCalculoA7XLS.php');

include_once(dirname(__FILE__).'/../../lib/lib_modelo/ConexionSqlServer.php');

class ACTReportes extends ACTbase{


    function  generarCruceTarjetasBoletos(){


        $tipo_rep = $this->objParam->getParametro('tipo_reporte');

        if($tipo_rep != 'pago_tigo'){
            $this->objParam->defecto('ordenacion','nombre');
            $this->objParam->defecto('dir_ordenacion','asc');
            $this->objParam->defecto('cantidad','5000');
            $this->objParam->defecto('puntero','0');

            $this->objParam->addFiltro("(1 in (select id_rol from segu.tusuario_rol ur where ur.id_usuario = 1 ) or 
            ( 1 in (select id_usuario from vef.tsucursal_usuario sucusu where puve.id_punto_venta = sucusu.id_punto_venta )))");
            $this->objFunc=$this->create('MODReportes');
            $this->res=$this->objFunc->generarCruceTarjetasBoletos($this->objParam);

            //obtener titulo de reporte
            $titulo ='Cruce Tarjetas Boletos';
        }else{
            $this->objFunc=$this->create('MODReportes');
            $this->res=$this->objFunc->generarCruceTigoBoletos($this->objParam);
            //obtener titulo de reporte
            $titulo ='Cruce Tigo Boletos';
        }

        $this->datos = $this->res->getDatos();

        //Genera el nombre del archivo (aleatorio + titulo)
        $nombreArchivo=uniqid(md5(session_id()).$titulo);
        $nombreArchivo.='.xls';

        $this->objParam->addParametro('nombre_archivo',$nombreArchivo);
        $this->objParam->addParametro('datos',$this->datos);
        $this->objParam->addParametro('tipo',$tipo_rep);
        $this->objParam->addParametro('fecha_desde',$this->objParam->getParametro('fecha_desde'));
        $this->objParam->addParametro('fecha_hasta',$this->objParam->getParametro('fecha_hasta'));

        //Instancia la clase de excel
        if($tipo_rep == 'pago_atc'){
            $this->objReporteFormato = new RReporteCruceAtcXLS($this->objParam);
        }else if($tipo_rep == 'pago_linkser'){
            $this->objReporteFormato = new RReporteCruceLinkserXLS($this->objParam);
        }else if($tipo_rep == 'pago_tigo'){
            $this->objParam->addParametro('depositos',$this->res->depositos);
            $this->objReporteFormato = new RReporteCruceTigoXLS($this->objParam);
        }

        $this->objReporteFormato->imprimeDatos();
        $this->objReporteFormato->generarReporte();

        $this->mensajeExito=new Mensaje();
        $this->mensajeExito->setMensaje('EXITO','Reporte.php','Reporte generado',
            'Se generó con éxito el reporte: '.$nombreArchivo,'control');
        $this->mensajeExito->setArchivoGenerado($nombreArchivo);

        $this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());
    }

    function listarAgencias(){
        $this->objParam->defecto('ordenacion','nombre');
        $this->objParam->defecto('dir_ordenacion','asc');
        $this->objParam->defecto('cantidad','15');
        $this->objParam->defecto('puntero','0');

        $this->objParam->addFiltro("(1 in (select id_rol from segu.tusuario_rol ur where ur.id_usuario = 1 ) or 
            ( 1 in (select id_usuario from vef.tsucursal_usuario sucusu where puve.id_punto_venta = sucusu.id_punto_venta )))");

        $this->objFunc=$this->create('MODReportes');
        $this->res=$this->objFunc->listarAgencias($this->objParam);

        if($this->objParam->getParametro('_adicionar')!=''){

            $respuesta = $this->res->getDatos();

            array_unshift ( $respuesta, array(  'id_punto_venta'=>'0',
                'nombre'=>'Todos',
                'codigo'=>'Todos',
                'office_id'=>'todos'));

            $this->res->setDatos($respuesta);
        }

        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    /**{developer:franklin.espinoza, date:22/12/2020, description: Reporte Calculo A7}**/
    function generarReporteCalculoA7(){


        if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
            $this->objReporte = new Reporte($this->objParam,$this);
            $this->res = $this->objReporte->generarReporteListado('MODReportes','generarReporteCalculoA7');
        }else {
            $this->objFunc=$this->create('MODReportes');
            $this->res=$this->objFunc->generarReporteCalculoA7($this->objParam);
        }

        $this->res->imprimirRespuesta($this->res->generarJson());
    }
    /*function  generarReporteCalculoA7(){

        $this->objFunc=$this->create('MODReportes');
        $this->res=$this->objFunc->generarReporteCalculoA7($this->objParam);
        //obtener titulo de reporte
        $titulo ='Calculo A7';

        $this->datos = $this->res->getDatos();

        //Genera el nombre del archivo (aleatorio + titulo)
        $nombreArchivo=uniqid(md5(session_id()).$titulo);
        $nombreArchivo.='.xls';

        $this->objParam->addParametro('nombre_archivo',$nombreArchivo);
        $this->objParam->addParametro('datos',$this->datos);
        $this->objParam->addParametro('fecha_desde',$this->objParam->getParametro('fecha_desde'));
        $this->objParam->addParametro('fecha_hasta',$this->objParam->getParametro('fecha_hasta'));

        //Instancia la clase de excel
        $this->objReporteFormato = new RReporteCalculoA7XLS($this->objParam);

        $this->objReporteFormato->imprimeDatos();
        $this->objReporteFormato->generarReporte();

        $this->mensajeExito=new Mensaje();
        $this->mensajeExito->setMensaje('EXITO', 'Reporte.php', 'Reporte generado', 'Se generó con éxito el reporte: '.$nombreArchivo, 'control');
        $this->mensajeExito->setArchivoGenerado($nombreArchivo);

        $this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());
    }*/
    /**{developer:franklin.espinoza, date:22/12/2020, description: Reporte Calculo A7}**/

    /**{developer:franklin.espinoza, date:22/12/2020, description: Detalle Vuelo Calculo A7}**/
    function detalleVueloCalculoA7(){

        $idFlight = $this->objParam->getParametro('vuelo_id');

        $data = array(
            "user"=>"usuarioSABSA2018",
            "pwd"=>"bOaSabS4.2018",
            "idFlight"=>$idFlight
        );

        $json_data = json_encode($data);
        $s = curl_init();
        curl_setopt($s, CURLOPT_URL, 'http://172.17.59.75/soaMigracion/Sabsa/servDataSabsa.svc/SabsaDetalleManifiesto');
        curl_setopt($s, CURLOPT_POST, true);
        curl_setopt($s, CURLOPT_POSTFIELDS, $json_data);
        curl_setopt($s, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($s, CURLOPT_CONNECTTIMEOUT, 20);
        curl_setopt($s, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($json_data))
        );
        $_out = curl_exec($s);//var_dump(json_decode($_out)->SabsaDetalleManifiestoResult);exit;
        $status = curl_getinfo($s, CURLINFO_HTTP_CODE);
        if (!$status) {
            throw new Exception("No se pudo conectar con Resiber");
        }
        curl_close($s);
        $res = json_decode($_out);
        $this->objParam->addParametro('detalle_vuelo', $res->SabsaDetalleManifiestoResult);


        if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
            $this->objReporte = new Reporte($this->objParam,$this);
            $this->res = $this->objReporte->generarReporteListado('MODReportes','detalleVueloCalculoA7');
        }else {
            $this->objFunc=$this->create('MODReportes');
            $this->res=$this->objFunc->detalleVueloCalculoA7($this->objParam);
        }
        $this->res->imprimirRespuesta($this->res->generarJson());
    }
    /**{developer:franklin.espinoza, date:22/12/2020, description: Detalle Vuelo Calculo A7}**/

    /**{developer:franklin.espinoza, date:22/12/2020, description: Detalle Vuelo Calculo A7}**/
    function detallePasajeroCalculoA7(){

        $pax_id = $this->objParam->getParametro('pax_id');
        $std_date = $this->objParam->getParametro('std_date');

        $data = array(
            "user"=>"usuarioSABSA2018",
            "pwd"=>"bOaSabS4.2018",
            "passengerId"=>$pax_id,
            "stdCurrentFlight"=>$std_date,
        );

        $json_data = json_encode($data);
        $s = curl_init();
        curl_setopt($s, CURLOPT_URL, 'http://172.17.59.75/SoaMigracion/Sabsa/servDataSabsa.svc/GetRoutingPassengerA7');
        curl_setopt($s, CURLOPT_POST, true);
        curl_setopt($s, CURLOPT_POSTFIELDS, $json_data);
        curl_setopt($s, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($s, CURLOPT_CONNECTTIMEOUT, 20);
        curl_setopt($s, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($json_data))
        );
        $_out = curl_exec($s);//var_dump(json_decode($_out)->SabsaDetalleManifiestoResult);exit;
        $status = curl_getinfo($s, CURLINFO_HTTP_CODE);
        if (!$status) {
            throw new Exception("No se pudo conectar con Resiber");
        }
        curl_close($s);
        $res = json_decode($_out); //var_dump('$res', $res);exit;
        $this->objParam->addParametro('detalle_pasajero', $res->GetRoutingPassengerA7Result);


        if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
            $this->objReporte = new Reporte($this->objParam,$this);
            $this->res = $this->objReporte->generarReporteListado('MODReportes','detallePasajeroCalculoA7');
        }else {
            $this->objFunc=$this->create('MODReportes');
            $this->res=$this->objFunc->detallePasajeroCalculoA7($this->objParam);
        }
        $this->res->imprimirRespuesta($this->res->generarJson());
    }
    /**{developer:franklin.espinoza, date:22/12/2020, description: Detalle Vuelo Calculo A7}**/


    /**{developer:franklin.espinoza, date:12/03/2021, description: Detalle Pagos realizados por la Administradora}**/
    function  getDetallePagosAdministradora(){

        //$tipo_administrador = $this->objParam->getParametro('tipo_administrador'); //var_dump('$tipo_administrador', $tipo_administrador);exit;

        if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
            $this->objReporte = new Reporte($this->objParam,$this);
            $this->res = $this->objReporte->generarReporteListado('MODReportes','getDetallePagosAdministradora');
        }else {
            $this->objFunc=$this->create('MODReportes');
            $this->res=$this->objFunc->getDetallePagosAdministradora($this->objParam);
        }

        $this->res->imprimirRespuesta($this->res->generarJson());
    }
    /**{developer:franklin.espinoza, date:12/03/2021, description: Detalle Pagos realizados por la Administradora}**/

    function getTicketInformationRecursive() {
        /*$nro_ticket = $this->objParam->getParametro('nro_ticket');
        $array = array();


        $conexion = new ConexionSqlServer('172.17.110.6', 'SPConnection', 'Passw0rd', 'DBStage');
        $conn = $conexion->conectarSQL();

        $query_string = "Select DBStage.dbo.fn_getTicketInformation('$nro_ticket') "; // boleto miami 9303852215072

        //$query_string = "select * from AuxBSPVersion";
        //$query_string = utf8_decode("select FlightItinerary from FactTicket where TicketNumber = '9302400056027'");
        @mssql_query('SET CONCAT_NULL_YIELDS_NULL ON');
        @mssql_query('SET ANSI_WARNINGS ON');
        @mssql_query('SET ANSI_PADDING ON');

        $query = @mssql_query($query_string, $conn);
        $row = mssql_fetch_array($query, MSSQL_ASSOC);

        $data_json_string = $row['computed'];
        $data_json = json_decode(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $data_json_string), true);

        $dataJson = new Mensaje();
        if($data_json != null) {
            $send = array(
                "nro_ticket" =>  $nro_ticket,
                "data" =>  $data_json,
            );echo json_encode($send);
            $temp = Array();
            $temp['data_json'] = json_encode($data_json);
            //$dataJson->setDatos($temp);
            $dataJson->addLastRecDatos($temp);
            $dataJson->setMensaje('EXITO', 'Reportes.php', 'Request Success', 'Se pudo encontrar el ticket solicitado desde AMADEUS.', 'control');
        } else {
            $send = array(
                "error" => false,
                "errorTicket" => true,
                "message" =>  "No se pudo encontrar el ticket solicitado, el mismo puede estar en un estado VOID o no haber sido emitido por AMADEUS",
            );echo json_encode($send);
            $dataJson->setMensaje('ERROR', 'Reportes.php', 'Request Error', 'No se pudo encontrar el ticket solicitado, el mismo puede estar en un estado VOID o no haber sido emitido por AMADEUS.', 'control');
        }

        $dataJson->imprimirRespuesta($dataJson->generarJson());*/
        $this->objFunc=$this->create('MODReportes');
        $this->res=$this->objFunc->getTicketInformationRecursive($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function getDetalleConciliacionAdministradora() {
        $this->objFunc=$this->create('MODReportes');
        $this->res=$this->objFunc->getDetalleConciliacionAdministradora($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }
}
?>
