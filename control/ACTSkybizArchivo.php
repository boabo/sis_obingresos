<?php
/**
*@package pXP
*@file gen-ACTSkybizArchivo.php
*@author  (admin)
*@date 15-02-2017 15:18:39
*@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
*/

class ACTSkybizArchivo extends ACTbase{    
			
	function listarSkybizArchivo(){
		$this->objParam->defecto('ordenacion','id_skybiz_archivo');

		$this->objParam->defecto('dir_ordenacion','asc');
		if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
			$this->objReporte = new Reporte($this->objParam,$this);
			$this->res = $this->objReporte->generarReporteListado('MODSkybizArchivo','listarSkybizArchivo');
		} else{
			$this->objFunc=$this->create('MODSkybizArchivo');
			
			$this->res=$this->objFunc->listarSkybizArchivo($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
				
	function insertarSkybizArchivo(){
		$this->objFunc=$this->create('MODSkybizArchivo');	
		if($this->objParam->insertar('id_skybiz_archivo')){
			$this->res=$this->objFunc->insertarSkybizArchivo($this->objParam);			
		} else{			
			$this->res=$this->objFunc->modificarSkybizArchivo($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
						
	function eliminarSkybizArchivo(){
			$this->objFunc=$this->create('MODSkybizArchivo');	
		$this->res=$this->objFunc->eliminarSkybizArchivo($this->objParam);
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
	function insertarSkybizArchivoJson(){
			$this->objFunc=$this->create('MODSkybizArchivo');
		$this->res=$this->objFunc->insertarSkybizArchivoJson($this->objParam);
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
			
}

?>