<?php
/**
 *@package pXP
 *@file gen-ACTDetalleDebito.php
 *@author  (miguel.mamani)
 *@date 18-07-2018 16:54:10
 *@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
 */

class ACTDetalleDebito extends ACTbase{

    function listarDetalleDebito(){
        $this->objParam->defecto('ordenacion','id_agencia');
        $this->objParam->defecto('dir_ordenacion','asc');
        if($this->objParam->getParametro('id_agencia') != '') {
            $this->objParam->addFiltro(" mo.id_movimiento_entidad in (" .$this->objParam->getParametro('id_debitos').") and  mo.id_agencia = ".$this->objParam->getParametro('id_agencia'));
        }
        if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
            $this->objReporte = new Reporte($this->objParam,$this);
            $this->res = $this->objReporte->generarReporteListado('MODDetalleDebito','listarDetalleDebito');
        } else{
            $this->objFunc=$this->create('MODDetalleDebito');
            $this->res=$this->objFunc->listarDetalleDebito($this->objParam);

            $temp = Array();
            $temp['monto'] = $this->res->extraData['monto_total'];
            $temp['neto'] = $this->res->extraData['neto_total'];
            $temp['comision'] = $this->res->extraData['comision_total'];
            $temp['total_monto'] = $this->res->extraData['total_monto'];
            $temp['nro_boleto'] = 'summary';
            $temp['id_detalle_boletos_web'] = 0;
            $this->res->total++;
            $this->res->addLastRecDatos($temp);

        }
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function insertarDetalleDebito(){
        $this->objFunc=$this->create('MODDetalleDebito');
        if($this->objParam->insertar('id_agencia')){
            $this->res=$this->objFunc->insertarDetalleDebito($this->objParam);
        } else{
            $this->res=$this->objFunc->modificarDetalleDebito($this->objParam);
        }
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function eliminarDetalleDebito(){
        $this->objFunc=$this->create('MODDetalleDebito');
        $this->res=$this->objFunc->eliminarDetalleDebito($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

}

?>