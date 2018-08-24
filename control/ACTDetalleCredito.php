<?php
/**
 *@package pXP
 *@file gen-ACTDetalleCredito.php
 *@author  (miguel.mamani)
 *@date 18-07-2018 16:53:28
 *@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
 */

class ACTDetalleCredito extends ACTbase{

    function listarDetalleCredito(){
        $this->objParam->defecto('ordenacion','id_agencia');
        $this->objParam->defecto('dir_ordenacion','asc');
        if($this->objParam->getParametro('id_agencia') != '') {
            $this->objParam->addFiltro(" mo.id_movimiento_entidad in (" .$this->objParam->getParametro('id_creditos').") and  mo.id_agencia = ".$this->objParam->getParametro('id_agencia'));
        }
        if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
            $this->objReporte = new Reporte($this->objParam,$this);
            $this->res = $this->objReporte->generarReporteListado('MODDetalleCredito','listarDetalleCredito');
        } else{
            $this->objFunc=$this->create('MODDetalleCredito');

            $this->res=$this->objFunc->listarDetalleCredito($this->objParam);
            $temp = Array();
            $temp['monto_total'] = $this->res->extraData['suma_total'];
            $temp['nro_deposito'] = 'summary';
            $temp['id_movimiento_entidad'] = 0;
            $this->res->total++;
            $this->res->addLastRecDatos($temp);
        }
        $this->res->imprimirRespuesta($this->res->generarJson());
    }


}

?>