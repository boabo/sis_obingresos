<?php
/**
*@package pXP
*@file gen-ACTAcm.php
*@author  (jrivera)
*@date 05-09-2018 20:34:32
*@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
*/
//require_once(dirname(__FILE__).'/../reportes/RCotizacion.php');
require_once(dirname(__FILE__).'/../reportes/RReporteAcm.php');
class ACTAcm extends ACTbase{

	function listarAcm(){
		$this->objParam->defecto('ordenacion','id_acm');

		if ($this->objParam->getParametro('acm') == 'especifico') {
		 $this->objParam->addFiltro("acm.id_archivo_acm_det = ". $this->objParam->getParametro('id_archivo_acm_det'));
	 }

		$this->objParam->defecto('dir_ordenacion','asc');
		if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
			$this->objReporte = new Reporte($this->objParam,$this);
			$this->res = $this->objReporte->generarReporteListado('MODAcm','listarAcm');
		} else{
			$this->objFunc=$this->create('MODAcm');

			$this->res=$this->objFunc->listarAcm($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}

	function insertarAcm(){
		$this->objFunc=$this->create('MODAcm');
		if($this->objParam->insertar('id_acm')){
			$this->res=$this->objFunc->insertarAcm($this->objParam);
		} else{
			$this->res=$this->objFunc->modificarAcm($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}

	function generarACM(){
		$this->objFunc=$this->create('MODAcm');
		if($this->objParam->insertar('id_acm')){
			$this->res=$this->objFunc->generarACM($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}

	function eliminarAcm(){
			$this->objFunc=$this->create('MODAcm');
		$this->res=$this->objFunc->eliminarAcm($this->objParam);
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
	function eliminarAcmGenerado(){
		//var_dump($this->objParam->getParametro('id_archivo_acm'));
			$this->objFunc=$this->create('sis_obingresos/MODAcm');
			$this->res=$this->objFunc->eliminarAcmGenerado($this->objParam);
			$this->res->imprimirRespuesta($this->res->generarJson());
	}
    function validarAcm(){
        $this->objFunc=$this->create('sis_obingresos/MODArchivoAcm');
        if($this->objParam->insertar('id_acm')){
            $this->res=$this->objFunc->validarAcm($this->objParam);
        }
        $this->res->imprimirRespuesta($this->res->generarJson());
    }
		function eliminarAcmValidado(){
			//var_dump($this->objParam->getParametro('id_archivo_acm'));
				$this->objFunc=$this->create('MODAcm');
				$this->res=$this->objFunc->eliminarAcmValidado($this->objParam);
				$this->res->imprimirRespuesta($this->res->generarJson());
		}
		function reporteGeneralACM(){
			if($this->objParam->getParametro('id_acm') != ''){
				$this->objParam->addFiltro("acm.id_acm = ". $this->objParam->getParametro('id_acm'));
	 	 }
			//var_dump($this->objParam->getParametro('id_acm'));exit;
			$this->objFunc=$this->create('MODAcm');
			$this->res=$this->objFunc->reporteGenACM($this->objParam);
			//obtener titulo de reporte
		  //var_dump($this->res);exit;
			$titulo ='ACM DOMESTICO';
			//Genera el nombre del archivo (aleatorio + titulo)
			$nombreArchivo=uniqid(md5(session_id()).$titulo);
			$nombreArchivo.='.xls';
			$this->objParam->addParametro('nombre_archivo',$nombreArchivo);
			$this->objParam->addParametro('datos',$this->res->datos);
			//Instancia la clase de excel
			$this->objReporteFormato=new RReporteACM($this->objParam);
			$this->objReporteFormato->generarDatos();
			$this->objReporteFormato->generarReporte();

			$this->mensajeExito=new Mensaje();
			$this->mensajeExito->setMensaje('EXITO','Reporte.php','Reporte generado',
					'Se generó con éxito el reporte: '.$nombreArchivo,'control');
			$this->mensajeExito->setArchivoGenerado($nombreArchivo);
			$this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());
		}

}

?>
