<?php
require_once(dirname(__FILE__).'/../reportes/RReporteNitRazonXLS.php');
require_once(dirname(__FILE__).'/../reportes/RConciliacionBancaInter.php');
require_once(dirname(__FILE__).'/../reportes/RConciliacionBancaInterRes.php');
class ACTDetalleBoletosWeb extends ACTbase{
	
	function listarDetalleBoletosWeb(){
		$this->objParam->defecto('ordenacion','id_detalle_boletos_web');

		$this->objParam->defecto('dir_ordenacion','asc');
		
		if($this->objParam->getParametro('id_agencia') != ''){
			$this->objParam->addFiltro(" detbol.id_agencia = ".$this->objParam->getParametro('id_agencia'));			
		}
		
		if($this->objParam->getParametro('numero_autorizacion') != ''){
			$this->objParam->addFiltro(" detbol.numero_autorizacion = ''".$this->objParam->getParametro('numero_autorizacion')."''");			
		}
		$this->objParam->addFiltro(" detbol.origen = ''portal'' and detbol.numero_autorizacion is not null");
		
		if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
			$this->objReporte = new Reporte($this->objParam,$this);
			$this->res = $this->objReporte->generarReporteListado('MODDetalleBoletosWeb','listarDetalleBoletosWeb');
		} else{
			$this->objFunc=$this->create('MODDetalleBoletosWeb');
			
			$this->res=$this->objFunc->listarDetalleBoletosWeb($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}

    function ReporteNitRazon(){
        $this->objFunc = $this->create('MODDetalleBoletosWeb');
        $this->res = $this->objFunc->listarReporteNitRazon($this->objParam);
        //var_dump( $this->res);exit;
        //obtener titulo de reporte
        $titulo = 'Reporte Nit Razon';
        //Genera el nombre del archivo (aleatorio + titulo)
        $nombreArchivo = uniqid(md5(session_id()) . $titulo);

        $nombreArchivo .= '.xls';
        $this->objParam->addParametro('nombre_archivo', $nombreArchivo);
        $this->objParam->addParametro('datos', $this->res->datos);
        //Instancia la clase de excel
        $this->objReporteFormato = new RReporteNitRazonXLS($this->objParam);
        $this->objReporteFormato->generarDatos();
        $this->objReporteFormato->generarReporte();

        $this->mensajeExito = new Mensaje();
        $this->mensajeExito->setMensaje('EXITO', 'Reporte.php', 'Reporte generado','Se generó con éxito el reporte: ' . $nombreArchivo, 'control');
        $this->mensajeExito->setArchivoGenerado($nombreArchivo);
        $this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());
    }

    function insertarBilletePortal(){
        $this->objFunc=$this->create('MODDetalleBoletosWeb');

        $this->res=$this->objFunc->insertarBilletePortal($this->objParam);

        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function conciliacionBancaInter(){
        $this->objFunc = $this->create('MODDetalleBoletosWeb');
        $this->res = $this->objFunc->listarConciliacionTotales($this->objParam);
        //var_dump( $this->res);exit;
        //obtener titulo de reporte
        $titulo = 'Reporte Conciliacion';
        //Genera el nombre del archivo (aleatorio + titulo)
        $nombreArchivo = uniqid(md5(session_id()) . $titulo);

        $nombreArchivo .= '.xls';
        $this->objParam->addParametro('nombre_archivo', $nombreArchivo);
        $this->objParam->addParametro('datos_total', $this->res->datos);
        $this->objFunc = $this->create('MODDetalleBoletosWeb');
        $this->res = $this->objFunc->listarConciliacionDetalle($this->objParam);
        $this->objParam->addParametro('datos_detalle', $this->res->datos);
        //Instancia la clase de excel
        $this->objReporteFormato = new RConciliacionBancaInter($this->objParam);
        $this->objReporteFormato->imprimeCabecera();
        $this->objReporteFormato->generarDatos();
        $this->objReporteFormato->generarReporte();

        $this->mensajeExito = new Mensaje();
        $this->mensajeExito->setMensaje('EXITO', 'Reporte.php', 'Reporte generado','Se generó con éxito el reporte: ' . $nombreArchivo, 'control');
        $this->mensajeExito->setArchivoGenerado($nombreArchivo);
        $this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());
    }

    function conciliacionBancaInterRes(){
        $this->objFunc = $this->create('MODDetalleBoletosWeb');
        $this->res = $this->objFunc->listarConciliacionResumen($this->objParam);
        //var_dump( $this->res);exit;
        //obtener titulo de reporte
        $titulo = 'Reporte Conciliacion Resumen';
        //Genera el nombre del archivo (aleatorio + titulo)
        $nombreArchivo = uniqid(md5(session_id()) . $titulo);

        $nombreArchivo .= '.xls';
        $this->objParam->addParametro('nombre_archivo', $nombreArchivo);
        $this->objParam->addParametro('tipo', 'skybiz');
        $this->objParam->addParametro('datos_conciliacion', $this->res->datos);
        $this->objFunc = $this->create('MODDetalleBoletosWeb');
        $this->res = $this->objFunc->listarConciliacionObservaciones($this->objParam);
        $this->objParam->addParametro('datos_observaciones', $this->res->datos);
        //Instancia la clase de excel
        $this->objReporteFormato = new RConciliacionBancaInterRes($this->objParam);
        $this->objReporteFormato->imprimeCabecera();
        $this->objReporteFormato->generarDatos();
        $this->objReporteFormato->generarReporte();

        $this->mensajeExito = new Mensaje();
        $this->mensajeExito->setMensaje('EXITO', 'Reporte.php', 'Reporte generado','Se generó con éxito el reporte: ' . $nombreArchivo, 'control');
        $this->mensajeExito->setArchivoGenerado($nombreArchivo);
        $this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());
    }


}
?>