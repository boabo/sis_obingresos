<?php
/**
*@package pXP
*@file gen-ACTDepositoBoleto.php
*@author  (jrivera)
*@date 06-01-2016 22:42:31
*@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
*/

class ACTDepositoBoleto extends ACTbase{    
			
	function listarDepositoBoleto(){
		$this->objParam->defecto('ordenacion','id_deposito_boleto');

		$this->objParam->defecto('dir_ordenacion','asc');
		
		if ($this->objParam->getParametro('id_boleto') != '') {
			$this->objParam->addFiltro("bol.id_boleto = ". $this->objParam->getParametro('id_boleto'));
		}
		
		if ($this->objParam->getParametro('id_deposito') != '') {
			$this->objParam->addFiltro("dep.id_deposito = ". $this->objParam->getParametro('id_deposito'));
		}
		
		if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
			$this->objReporte = new Reporte($this->objParam,$this);
			$this->res = $this->objReporte->generarReporteListado('MODDepositoBoleto','listarDepositoBoleto');
		} else{
			$this->objFunc=$this->create('MODDepositoBoleto');
			
			$this->res=$this->objFunc->listarDepositoBoleto($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
				
	function insertarDepositoBoleto(){
		$this->objFunc=$this->create('MODDepositoBoleto');	
		if($this->objParam->insertar('id_deposito_boleto')){
			$this->res=$this->objFunc->insertarDepositoBoleto($this->objParam);			
		} else{			
			$this->res=$this->objFunc->modificarDepositoBoleto($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
						
	function eliminarDepositoBoleto(){
			$this->objFunc=$this->create('MODDepositoBoleto');	
		$this->res=$this->objFunc->eliminarDepositoBoleto($this->objParam);
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
			
}

?>