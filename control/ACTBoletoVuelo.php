<?php
/**
*@package pXP
*@file gen-ACTBoletoVuelo.php
*@author  (jrivera)
*@date 29-03-2017 10:59:33
*@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
*/

class ACTBoletoVuelo extends ACTbase{    
			
	function listarBoletoVuelo(){
		$this->objParam->defecto('ordenacion','id_boleto_vuelo');

		$this->objParam->defecto('dir_ordenacion','asc');

        if ($this->objParam->getParametro('id_boleto') != '') {
            $this->objParam->addFiltro("(bvu.id_boleto = ". $this->objParam->getParametro('id_boleto') . " or bvu.id_boleto_conjuncion = " . $this->objParam->getParametro('id_boleto') . ")");
        }

		if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
			$this->objReporte = new Reporte($this->objParam,$this);
			$this->res = $this->objReporte->generarReporteListado('MODBoletoVuelo','listarBoletoVuelo');
		} else{
			$this->objFunc=$this->create('MODBoletoVuelo');
			
			$this->res=$this->objFunc->listarBoletoVuelo($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
				
	function insertarBoletoVuelo(){
		$this->objFunc=$this->create('MODBoletoVuelo');	
		if($this->objParam->insertar('id_boleto_vuelo')){
			$this->res=$this->objFunc->insertarBoletoVuelo($this->objParam);			
		} else{			
			$this->res=$this->objFunc->modificarBoletoVuelo($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
						
	function eliminarBoletoVuelo(){
			$this->objFunc=$this->create('MODBoletoVuelo');	
		$this->res=$this->objFunc->eliminarBoletoVuelo($this->objParam);
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
			
}

?>