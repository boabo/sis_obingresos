<?php
/**
*@package pXP
*@file gen-ACTBoletoFormaPago.php
*@author  (jrivera)
*@date 13-06-2016 20:42:15
*@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
*/

class ACTBoletoFormaPago extends ACTbase{    
			
	function listarBoletoFormaPago(){
		$this->objParam->defecto('ordenacion','id_boleto_forma_pago');

		$this->objParam->defecto('dir_ordenacion','asc');
		if ($this->objParam->getParametro('id_boleto') != '') {
			$this->objParam->addFiltro("bfp.id_boleto = ". $this->objParam->getParametro('id_boleto'));
		}
		if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
			$this->objReporte = new Reporte($this->objParam,$this);
			$this->res = $this->objReporte->generarReporteListado('MODBoletoFormaPago','listarBoletoFormaPago');
		} else{
			$this->objFunc=$this->create('MODBoletoFormaPago');
			
			$this->res=$this->objFunc->listarBoletoFormaPago($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}

	function listarBoletoAmadeusFormaPago(){
		$this->objParam->defecto('ordenacion','id_boleto_amadeus_forma_pago');

		$this->objParam->defecto('dir_ordenacion','asc');
		if ($this->objParam->getParametro('id_boleto_amadeus') != '') {
			$this->objParam->addFiltro("bfp.id_boleto_amadeus = ". $this->objParam->getParametro('id_boleto_amadeus'));
		}
		if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
			$this->objReporte = new Reporte($this->objParam,$this);
			$this->res = $this->objReporte->generarReporteListado('MODBoletoFormaPago','listarBoletoAmadeusFormaPago');
		} else{
			$this->objFunc=$this->create('MODBoletoFormaPago');

			$this->res=$this->objFunc->listarBoletoAmadeusFormaPago($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}

	function insertarBoletoFormaPago(){
		$this->objFunc=$this->create('MODBoletoFormaPago');	
		if($this->objParam->insertar('id_boleto_forma_pago')){
			$this->res=$this->objFunc->insertarBoletoFormaPago($this->objParam);			
		} else{			
			$this->res=$this->objFunc->modificarBoletoFormaPago($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}

	function insertarBoletoAmadeusFormaPago(){
		$this->objFunc=$this->create('MODBoletoFormaPago');
		if($this->objParam->insertar('id_boleto_amadeus_forma_pago')){
			$this->res=$this->objFunc->insertarBoletoAmadeusFormaPago($this->objParam);
		} else{
			$this->res=$this->objFunc->modificarBoletoAmadeusFormaPago($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}

	function eliminarBoletoFormaPago(){
			$this->objFunc=$this->create('MODBoletoFormaPago');
		$this->res=$this->objFunc->eliminarBoletoFormaPago($this->objParam);
		$this->res->imprimirRespuesta($this->res->generarJson());
	}

	function eliminarBoletoAmadeusFormaPago(){
		$this->objFunc=$this->create('MODBoletoFormaPago');
		$this->res=$this->objFunc->eliminarBoletoAmadeusFormaPago($this->objParam);
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
			
}

?>