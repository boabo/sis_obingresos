<?php
/**
*@package pXP
*@file gen-ACTSkybizArchivoDetalle.php
*@author  (admin)
*@date 15-02-2017 19:08:58
*@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
*/

class ACTSkybizArchivoDetalle extends ACTbase{    
			
	function listarSkybizArchivoDetalle(){
		$this->objParam->defecto('ordenacion','id_skybiz_archivo_detalle');

		$this->objParam->defecto('dir_ordenacion','asc');

		if($this->objParam->getParametro('id_skybiz_archivo') != ''){
			$this->objParam->addFiltro("skydet.id_skybiz_archivo = ".$this->objParam->getParametro('id_skybiz_archivo'));
		}

		
		if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
			$this->objReporte = new Reporte($this->objParam,$this);
			$this->res = $this->objReporte->generarReporteListado('MODSkybizArchivoDetalle','listarSkybizArchivoDetalle');
		} else{
			$this->objFunc=$this->create('MODSkybizArchivoDetalle');
			
			$this->res=$this->objFunc->listarSkybizArchivoDetalle($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
				
	function insertarSkybizArchivoDetalle(){
		$this->objFunc=$this->create('MODSkybizArchivoDetalle');	
		if($this->objParam->insertar('id_skybiz_archivo_detalle')){
			$this->res=$this->objFunc->insertarSkybizArchivoDetalle($this->objParam);			
		} else{			
			$this->res=$this->objFunc->modificarSkybizArchivoDetalle($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
						
	function eliminarSkybizArchivoDetalle(){
			$this->objFunc=$this->create('MODSkybizArchivoDetalle');	
		$this->res=$this->objFunc->eliminarSkybizArchivoDetalle($this->objParam);
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
	function insertarSkybizArchivoDetalleJson(){
			$this->objFunc=$this->create('MODSkybizArchivoDetalle');
		$this->res=$this->objFunc->insertarSkybizArchivoDetalleJson($this->objParam);
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
			
}

?>