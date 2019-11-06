<?php
/**
 *@package pXP
 *@file gen-ACTFormaPagoPw.php
 *@author  (admin)
 *@date 04-06-2019 21:58:00
 *@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
 */

class ACTFormaPagoPw extends ACTbase{

    function listarFormaPagoPw(){
        $this->objParam->defecto('ordenacion','id_forma_pago_pw');

        $this->objParam->defecto('dir_ordenacion','asc');
        if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
            $this->objReporte = new Reporte($this->objParam,$this);
            $this->res = $this->objReporte->generarReporteListado('MODFormaPagoPw','listarFormaPagoPw');
        } else{
            $this->objFunc=$this->create('MODFormaPagoPw');

            $this->res=$this->objFunc->listarFormaPagoPw($this->objParam);
        }
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function insertarFormaPagoPw(){
        $this->objFunc=$this->create('MODFormaPagoPw');
        if($this->objParam->insertar('id_forma_pago_pw')){
            $this->res=$this->objFunc->insertarFormaPagoPw($this->objParam);
        } else{
            $this->res=$this->objFunc->modificarFormaPagoPw($this->objParam);
        }
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function eliminarFormaPagoPw(){
        $this->objFunc=$this->create('MODFormaPagoPw');
        $this->res=$this->objFunc->eliminarFormaPagoPw($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

}

?>
