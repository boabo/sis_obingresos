<?php
/**
*@package pXP
*@file gen-ACTObservacionesConciliacion.php
*@author  (jrivera)
*@date 01-06-2017 21:16:45
*@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
*/

class ACTObservacionesConciliacion extends ACTbase{    
			
	function listarObservacionesConciliacion(){
		$this->objParam->defecto('ordenacion','id_observaciones_conciliacion');

		$this->objParam->defecto('dir_ordenacion','asc');
		if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
			$this->objReporte = new Reporte($this->objParam,$this);
			$this->res = $this->objReporte->generarReporteListado('MODObservacionesConciliacion','listarObservacionesConciliacion');
		} else{
			$this->objFunc=$this->create('MODObservacionesConciliacion');
			
			$this->res=$this->objFunc->listarObservacionesConciliacion($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
				
	function insertarObservacionesConciliacion(){
		$this->objFunc=$this->create('MODObservacionesConciliacion');	
		if($this->objParam->insertar('id_observaciones_conciliacion')){
			$this->res=$this->objFunc->insertarObservacionesConciliacion($this->objParam);			
		} else{			
			$this->res=$this->objFunc->modificarObservacionesConciliacion($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
						
	function eliminarObservacionesConciliacion(){
			$this->objFunc=$this->create('MODObservacionesConciliacion');	
		$this->res=$this->objFunc->eliminarObservacionesConciliacion($this->objParam);
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
			
}

?>