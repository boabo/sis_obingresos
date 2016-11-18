<?php
/**
*@package pXP
*@file gen-ACTAgencia.php
*@author  (jrivera)
*@date 06-01-2016 21:30:12
*@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
*/

class ACTAgencia extends ACTbase{    
			
	function listarAgencia(){
		$this->objParam->defecto('ordenacion','id_agencia');

		$this->objParam->defecto('dir_ordenacion','asc');
		if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
			$this->objReporte = new Reporte($this->objParam,$this);
			$this->res = $this->objReporte->generarReporteListado('MODAgencia','listarAgencia');
		} else{
			$this->objFunc=$this->create('MODAgencia');
			
			$this->res=$this->objFunc->listarAgencia($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
				
	function insertarAgencia(){
		$this->objFunc=$this->create('MODAgencia');	
		if($this->objParam->insertar('id_agencia')){
			$this->res=$this->objFunc->insertarAgencia($this->objParam);			
		} else{			
			$this->res=$this->objFunc->modificarAgencia($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
						
	function eliminarAgencia(){
			$this->objFunc=$this->create('MODAgencia');	
		$this->res=$this->objFunc->eliminarAgencia($this->objParam);
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
			
}

?>