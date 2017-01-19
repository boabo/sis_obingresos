<?php
/**
*@package pXP
*@file gen-ACTVentaWebModificaciones.php
*@author  (jrivera)
*@date 11-01-2017 19:44:28
*@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
*/

class ACTVentaWebModificaciones extends ACTbase{    
			
	function listarVentaWebModificaciones(){
		$this->objParam->defecto('ordenacion','id_venta_web_modificaciones');
        if ($this->objParam->getParametro('pes_estado') == 'pendientes') {
            $this->objParam->addFiltro("((b.voided is null or b.voided = ''no'') or (vwebmod.tipo=''reemision'' and vwebmod.procesado=''no''))");
        }

        if ($this->objParam->getParametro('pes_estado') == 'finalizados') {
            $this->objParam->addFiltro("((b.voided is not null and b.voided = ''si'') and (vwebmod.tipo=''anulado'' or vwebmod.procesado=''si''))");
        }



        $this->objParam->defecto('dir_ordenacion','asc');
		if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
			$this->objReporte = new Reporte($this->objParam,$this);
			$this->res = $this->objReporte->generarReporteListado('MODVentaWebModificaciones','listarVentaWebModificaciones');
		} else{
			$this->objFunc=$this->create('MODVentaWebModificaciones');
			
			$this->res=$this->objFunc->listarVentaWebModificaciones($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
				
	function insertarVentaWebModificaciones(){
		$this->objFunc=$this->create('MODVentaWebModificaciones');	
		if($this->objParam->insertar('id_venta_web_modificaciones')){
			$this->res=$this->objFunc->insertarVentaWebModificaciones($this->objParam);			
		} else{			
			$this->res=$this->objFunc->modificarVentaWebModificaciones($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
						
	function eliminarVentaWebModificaciones(){
			$this->objFunc=$this->create('MODVentaWebModificaciones');	
		$this->res=$this->objFunc->eliminarVentaWebModificaciones($this->objParam);
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
			
}

?>