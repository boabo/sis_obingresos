<?php
/**
*@package pXP
*@file gen-ACTTotalComisionMes.php
*@author  (jrivera)
*@date 17-08-2017 21:28:24
*@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
*/

class ACTTotalComisionMes extends ACTbase{    
			
	function listarTotalComisionMes(){
		$this->objParam->defecto('ordenacion','id_total_comision_mes');

        if($this->objParam->getParametro('gestion') != '') {
            $this->objParam->addFiltro(" totfac.gestion = " . $this->objParam->getParametro('gestion'));
        }

        if($this->objParam->getParametro('periodo') != '') {
            $this->objParam->addFiltro(" totfac.periodo = " . $this->objParam->getParametro('periodo'));
        }

        if($this->objParam->getParametro('estado') != '') {
            $this->objParam->addFiltro(" totfac.estado = ''" . $this->objParam->getParametro('estado')."''");
        }

		$this->objParam->defecto('dir_ordenacion','asc');
		if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
			$this->objReporte = new Reporte($this->objParam,$this);
			$this->res = $this->objReporte->generarReporteListado('MODTotalComisionMes','listarTotalComisionMes');
		} else{
			$this->objFunc=$this->create('MODTotalComisionMes');
			
			$this->res=$this->objFunc->listarTotalComisionMes($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
				
	function insertarTotalComisionMes(){
		$this->objFunc=$this->create('MODTotalComisionMes');	
		if($this->objParam->insertar('id_total_comision_mes')){
			$this->res=$this->objFunc->insertarTotalComisionMes($this->objParam);			
		} else{			
			$this->res=$this->objFunc->modificarTotalComisionMes($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
						
	function eliminarTotalComisionMes(){
			$this->objFunc=$this->create('MODTotalComisionMes');	
		$this->res=$this->objFunc->eliminarTotalComisionMes($this->objParam);
		$this->res->imprimirRespuesta($this->res->generarJson());
	}

    function validarComisionMes(){
        $this->objFunc=$this->create('MODTotalComisionMes');
        $this->res=$this->objFunc->validarComisionMes($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }
			
}

?>