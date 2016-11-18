<?php
/**
*@package pXP
*@file gen-ACTBoletoImpuesto.php
*@author  (jrivera)
*@date 13-06-2016 20:42:17
*@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
*/

class ACTBoletoImpuesto extends ACTbase{    
			
	function listarBoletoImpuesto(){
		$this->objParam->defecto('ordenacion','id_boleto_impuesto');

		$this->objParam->defecto('dir_ordenacion','asc');
		
		if ($this->objParam->getParametro('id_boleto') != '') {
			$this->objParam->addFiltro("bit.id_boleto = ". $this->objParam->getParametro('id_boleto'));
		}
		if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
			$this->objReporte = new Reporte($this->objParam,$this);
			$this->res = $this->objReporte->generarReporteListado('MODBoletoImpuesto','listarBoletoImpuesto');
		} else{
			$this->objFunc=$this->create('MODBoletoImpuesto');
			
			$this->res=$this->objFunc->listarBoletoImpuesto($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
				
	function insertarBoletoImpuesto(){
		$this->objFunc=$this->create('MODBoletoImpuesto');	
		if($this->objParam->insertar('id_boleto_impuesto')){
			$this->res=$this->objFunc->insertarBoletoImpuesto($this->objParam);			
		} else{			
			$this->res=$this->objFunc->modificarBoletoImpuesto($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
						
	function eliminarBoletoImpuesto(){
			$this->objFunc=$this->create('MODBoletoImpuesto');	
		$this->res=$this->objFunc->eliminarBoletoImpuesto($this->objParam);
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
			
}

?>