<?php
/**
*@package pXP
*@file gen-ACTMovimientoEntidad.php
*@author  (jrivera)
*@date 17-05-2017 15:53:35
*@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
*/

class ACTMovimientoEntidad extends ACTbase{    
			
	function listarMovimientoEntidad(){
		$this->objParam->defecto('ordenacion','fecha');

		$this->objParam->defecto('dir_ordenacion','asc');

        if ($this->objParam->getParametro('id_entidad') != '') {
            $this->objParam->addFiltro("moe.id_agencia = ". $this->objParam->getParametro('id_entidad'));
        }
		
		if ($this->objParam->getParametro('fecha_inicio') != '' && $this->objParam->getParametro('fecha_fin') != '') {
            $this->objParam->addFiltro("moe.fecha >= ''" . $this->objParam->getParametro('fecha_inicio') ."'' and 
            							moe.fecha <= ''" . $this->objParam->getParametro('fecha_fin') . "''");
			$this->objParam->addFiltro("moe.cierre_periodo = ''no'' and moe.garantia = ''no'' ");
        } else if ($this->objParam->getParametro('id_periodo_venta') == '') {
        	$this->objParam->addFiltro("moe.id_periodo_venta is null ");
        }
		
		if ($this->objParam->getParametro('id_periodo_venta') != '') {
            $this->objParam->addFiltro("moe.id_periodo_venta = ". $this->objParam->getParametro('id_periodo_venta') . " and moe.garantia = ''no'' ");
        }

		if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
			$this->objReporte = new Reporte($this->objParam,$this);
			$this->res = $this->objReporte->generarReporteListado('MODMovimientoEntidad','listarMovimientoEntidad');
		} else{
			$this->objFunc=$this->create('MODMovimientoEntidad');
			
			$this->res=$this->objFunc->listarMovimientoEntidad($this->objParam);
			$temp = Array();
			$temp['credito_mb'] = $this->res->extraData['total_credito'];
			$temp['debito_mb'] = $this->res->extraData['total_debito'];	
			$temp['monto_total'] = $this->res->extraData['monto_total'];
			$temp['debito'] = $this->res->extraData['saldo_actual'];
			$temp['pnr'] = $this->res->extraData['tipo'];
			$temp['deudas'] = $this->res->extraData['deudas'];

			$temp['tipo_reg'] = 'summary';
			$temp['id_movimiento_entidad'] = 0;
			
			$this->res->total++;
			
			$this->res->addLastRecDatos($temp);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
				
	function insertarMovimientoEntidad(){
		$this->objFunc=$this->create('MODMovimientoEntidad');	
		if($this->objParam->insertar('id_movimiento_entidad')){
			$this->res=$this->objFunc->insertarMovimientoEntidad($this->objParam);			
		} else{			
			$this->res=$this->objFunc->modificarMovimientoEntidad($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
						
	function eliminarMovimientoEntidad(){
			$this->objFunc=$this->create('MODMovimientoEntidad');	
		$this->res=$this->objFunc->eliminarMovimientoEntidad($this->objParam);
		$this->res->imprimirRespuesta($this->res->generarJson());
	}

    function anularAutorizacion(){
        $this->objFunc=$this->create('MODMovimientoEntidad');
        $this->res=$this->objFunc->anularAutorizacion($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }
			
}

?>