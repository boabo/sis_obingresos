<?php
/**
*@package pXP
*@file gen-ACTContratos.php
*@author  (miguel.mamani)
*@date 24-05-2018 15:10:35
*@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
*/

class ACTContratos extends ACTbase{    
			
	function listarContratos(){
		$this->objParam->defecto('ordenacion','id_contrato');

		$this->objParam->defecto('dir_ordenacion','asc');
		if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
			$this->objReporte = new Reporte($this->objParam,$this);
			$this->res = $this->objReporte->generarReporteListado('MODContratos','listarContratos');
		} else{
			$this->objFunc=$this->create('MODContratos');
			
			$this->res=$this->objFunc->listarContratos($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
				
	function insertarContratos(){
		$this->objFunc=$this->create('MODContratos');	
		if($this->objParam->insertar('id_contrato')){
			$this->res=$this->objFunc->insertarContratos($this->objParam);			
		} else{			
			$this->res=$this->objFunc->modificarContratos($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
						
	function eliminarContratos(){
			$this->objFunc=$this->create('MODContratos');	
		$this->res=$this->objFunc->eliminarContratos($this->objParam);
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
			
}

?>