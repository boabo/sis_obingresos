<?php
/**
*@package pXP
*@file gen-ACTFormaPago.php
*@author  (jrivera)
*@date 10-06-2016 20:37:45
*@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
*/

class ACTFormaPago extends ACTbase{    
			
	function listarFormaPago(){
		$this->objParam->defecto('ordenacion','id_forma_pago');

		$this->objParam->defecto('dir_ordenacion','asc');
		
		if ($this->objParam->getParametro('id_punto_venta') != '') {
			$this->objParam->addFiltro("fop.id_lugar = (select param.f_get_id_lugar_pais(id_lugar) 
														from vef.tpunto_venta pv
														inner join vef.tsucursal s on s.id_sucursal = pv.id_sucursal
														and id_punto_venta = ". $this->objParam->getParametro('id_punto_venta').")");
		}

        if ($this->objParam->getParametro('fp_ventas') == 'si') {
            $this->objParam->addFiltro("(fop.codigo not in (''CM'',''CHQV'') and fop.codigo not like ''RF%'' or fop.codigo not like ''TC%'')");
        }
		
		if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
			$this->objReporte = new Reporte($this->objParam,$this);
			$this->res = $this->objReporte->generarReporteListado('MODFormaPago','listarFormaPago');
		} else{
			$this->objFunc=$this->create('MODFormaPago');
			
			$this->res=$this->objFunc->listarFormaPago($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
				
	function insertarFormaPago(){
		$this->objFunc=$this->create('MODFormaPago');	
		if($this->objParam->insertar('id_forma_pago')){
			$this->res=$this->objFunc->insertarFormaPago($this->objParam);			
		} else{			
			$this->res=$this->objFunc->modificarFormaPago($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
						
	function eliminarFormaPago(){
			$this->objFunc=$this->create('MODFormaPago');	
		$this->res=$this->objFunc->eliminarFormaPago($this->objParam);
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
			
}

?>