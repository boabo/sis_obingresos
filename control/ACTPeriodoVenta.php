<?php
/**
*@package pXP
*@file gen-ACTPeriodoVenta.php
*@author  (jrivera)
*@date 08-04-2016 22:44:37
*@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
*/

class ACTPeriodoVenta extends ACTbase{    
			
	function listarPeriodoVenta(){
		$this->objParam->defecto('ordenacion','id_periodo_venta');

		$this->objParam->defecto('dir_ordenacion','asc');
		
		if($this->objParam->getParametro('id_pais') != '') {
                $this->objParam->addFiltro(" perven.id_pais = " . $this->objParam->getParametro('id_pais'));
        }
		
		if($this->objParam->getParametro('id_gestion') != '') {
                $this->objParam->addFiltro(" perven.id_gestion = " . $this->objParam->getParametro('id_gestion'));
        }
		
		if($this->objParam->getParametro('tipo') != '') {
                $this->objParam->addFiltro(" perven.tipo = ''" . $this->objParam->getParametro('tipo') . "''");
        }
		
		if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
			$this->objReporte = new Reporte($this->objParam,$this);
			$this->res = $this->objReporte->generarReporteListado('MODPeriodoVenta','listarPeriodoVenta');
		} else{
			$this->objFunc=$this->create('MODPeriodoVenta');
			
			$this->res=$this->objFunc->listarPeriodoVenta($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
				
	function insertarPeriodoVenta(){
		$this->objFunc=$this->create('MODPeriodoVenta');	
		if($this->objParam->insertar('id_periodo_venta')){
			$this->res=$this->objFunc->insertarPeriodoVenta($this->objParam);			
		} else{			
			$this->res=$this->objFunc->modificarPeriodoVenta($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
						
	function eliminarPeriodoVenta(){
			$this->objFunc=$this->create('MODPeriodoVenta');	
		$this->res=$this->objFunc->eliminarPeriodoVenta($this->objParam);
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
			
}

?>