<?php
/**
*@package pXP
*@file gen-ACTArchivoAcmDet.php
*@author  (admin)
*@date 05-09-2018 20:36:49
*@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
*/
require_once(dirname(__FILE__).'/../reportes/RReporteArchivoAcm.php');

class ACTArchivoAcmDet extends ACTbase{

	function listarArchivoAcmDet(){
		$this->objParam->defecto('ordenacion','id_archivo_acm_det');

		$this->objParam->defecto('dir_ordenacion','asc');

		if ($this->objParam->getParametro('id_archivo_acm') != '') {
            $this->objParam->addFiltro("aad.id_archivo_acm = ".$this->objParam->getParametro('id_archivo_acm'));
        }

		if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
			$this->objReporte = new Reporte($this->objParam,$this);
			$this->res = $this->objReporte->generarReporteListado('MODArchivoAcmDet','listarArchivoAcmDet');
		} else{
			$this->objFunc=$this->create('MODArchivoAcmDet');
            if ($this->objParam->getParametro('id_archivo_acm') != '') {
                $this->res=$this->objFunc->listarArchivoAcmDet($this->objParam);
                $temp = Array();
                $temp['total_importe_b'] = $this->res->extraData['total_importe_b'];
                $temp['total_importe'] = $this->res->extraData['total_importe'];
                $temp['sum_neto_b'] = $this->res->extraData['sum_neto_b'];
                $temp['cantidad_boletosmb'] = $this->res->extraData['cantidad_boletosmb'];
				$temp['sum_neto'] = $this->res->extraData['sum_neto'];
				$temp['cantidad_boletosmt'] = $this->res->extraData['cantidad_boletosmt'];
                $temp['tipo_reg'] = 'summary';
                //$temp['id_deposito'] = 0;

                $this->res->total++;
                $this->res->addLastRecDatos($temp);

            }else{
                $this->res=$this->objFunc->listarArchivoAcmDet($this->objParam);

            }


            //$this->res=$this->objFunc->listarArchivoAcmDet($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}

	function insertarArchivoAcmDet(){
		$this->objFunc=$this->create('MODArchivoAcmDet');
		if($this->objParam->insertar('id_archivo_acm_det')){
			$this->res=$this->objFunc->insertarArchivoAcmDet($this->objParam);
		} else{
			$this->res=$this->objFunc->modificarArchivoAcmDet($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}

	function eliminarArchivoAcmDet(){
			$this->objFunc=$this->create('MODArchivoAcmDet');
		$this->res=$this->objFunc->eliminarArchivoAcmDet($this->objParam);
		$this->res->imprimirRespuesta($this->res->generarJson());
	}

}

?>
