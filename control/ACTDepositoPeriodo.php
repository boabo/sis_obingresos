<?php

class ACTDepositoPeriodo extends ACTbase{

    function listarDepositosPeriodo(){
        $this->objParam->defecto('ordenacion','id_movimiento_entidad');
        $this->objParam->defecto('dir_ordenacion','asc');
        if($this->objParam->getParametro('id_agencia') != '') {
            $this->objParam->addFiltro("mo.id_agencia = " . $this->objParam->getParametro('id_agencia'));
        }
        if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
            $this->objReporte = new Reporte($this->objParam,$this);
            $this->res = $this->objReporte->generarReporteListado('MODDepositoPeriodo','listarDepositosPeriodo');
        } else{
            $this->objFunc=$this->create('MODDepositoPeriodo');
            $this->res=$this->objFunc->listarDepositosPeriodo($this->objParam);
            $temp = Array();
            $temp['monto_total'] = $this->res->extraData['suma_total'];
            $temp['autorizacion__nro_deposito'] = 'summary';
            $temp['id_movimiento_entidad'] = 0;
            $this->res->total++;
            $this->res->addLastRecDatos($temp);
        }
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

}
?>