<?php
/**
*@package pXP
*@file gen-ACTAcmDet.php
*@author  (jrivera)
*@date 05-09-2018 20:52:05
*@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
*/

class ACTAcmDet extends ACTbase{

	function listarAcmDet(){
		$this->objParam->defecto('ordenacion','id_acm_det');

		$this->objParam->defecto('dir_ordenacion','asc');

		if($this->objParam->getParametro('id_acm')!=''){
			$this->objParam->addFiltro("acmdet.id_acm = ".$this->objParam->getParametro('id_acm'));
		}
		if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
			$this->objReporte = new Reporte($this->objParam,$this);
			$this->res = $this->objReporte->generarReporteListado('MODAcmDet','listarAcmDet');
		} else{
			$this->objFunc=$this->create('MODAcmDet');
			if ($this->objParam->getParametro('id_acm') != '') {
					$this->res=$this->objFunc->listarAcmDet($this->objParam);
					$temp = Array();
					$temp['total_over_comision'] = $this->res->extraData['total_over_comision'];
					$temp['total_neto'] = $this->res->extraData['total_neto'];
					$temp['total_bsp'] = $this->res->extraData['total_bsp'];
					$temp['tipo_reg'] = 'summary';
					//$temp['id_deposito'] = 0;

					$this->res->total++;
					$this->res->addLastRecDatos($temp);

			}else{
						$this->res=$this->objFunc->listarAcmDet($this->objParam);

			}


		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}

	function insertarAcmDet(){
		$this->objFunc=$this->create('MODAcmDet');
		if($this->objParam->insertar('id_acm_det')){
			$this->res=$this->objFunc->insertarAcmDet($this->objParam);
		} else{
			$this->res=$this->objFunc->modificarAcmDet($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}

	function eliminarAcmDet(){
			$this->objFunc=$this->create('MODAcmDet');
		$this->res=$this->objFunc->eliminarAcmDet($this->objParam);
		$this->res->imprimirRespuesta($this->res->generarJson());
	}

}

?>
