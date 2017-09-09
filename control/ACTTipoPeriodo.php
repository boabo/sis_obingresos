<?php
/**
*@package pXP
*@file gen-ACTTipoPeriodo.php
*@author  (jrivera)
*@date 08-05-2017 20:02:14
*@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
*/

class ACTTipoPeriodo extends ACTbase{    
			
	function listarTipoPeriodo(){
		$this->objParam->defecto('ordenacion','id_tipo_periodo');

		$this->objParam->defecto('dir_ordenacion','asc');

        if ($this->objParam->getParametro('estado') != '') {
            $this->objParam->addFiltro("tiper.estado = ''". $this->objParam->getParametro('estado')."''");
        }

		if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
			$this->objReporte = new Reporte($this->objParam,$this);
			$this->res = $this->objReporte->generarReporteListado('MODTipoPeriodo','listarTipoPeriodo');
		} else{
			$this->objFunc=$this->create('MODTipoPeriodo');
			
			$this->res=$this->objFunc->listarTipoPeriodo($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
	function obtenerTipoPeriodoXFP(){
		$this->objParam->defecto('ordenacion','id_tipo_periodo');

		$this->objParam->defecto('dir_ordenacion','asc');
        
		$this->objFunc=$this->create('MODTipoPeriodo');
			
		$this->res=$this->objFunc->obtenerTipoPeriodoXFP($this->objParam);
		
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
				
	function insertarTipoPeriodo(){
		$this->objFunc=$this->create('MODTipoPeriodo');	
		if($this->objParam->insertar('id_tipo_periodo')){
			$this->res=$this->objFunc->insertarTipoPeriodo($this->objParam);			
		} else{			
			$this->res=$this->objFunc->modificarTipoPeriodo($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
						
	function eliminarTipoPeriodo(){
        $this->objFunc=$this->create('MODTipoPeriodo');
		$this->res=$this->objFunc->eliminarTipoPeriodo($this->objParam);
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
			
}

?>