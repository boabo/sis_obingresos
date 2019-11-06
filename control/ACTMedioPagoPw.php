<?php
/**
 *@package pXP
 *@file gen-ACTMedioPagoPw.php
 *@author  (admin)
 *@date 04-06-2019 22:47:38
 *@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
 */

class ACTMedioPagoPw extends ACTbase{

    function listarMedioPagoPw(){
        $this->objParam->defecto('ordenacion','id_medio_pago_pw');

        $this->objParam->defecto('dir_ordenacion','asc');
        if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
            $this->objReporte = new Reporte($this->objParam,$this);
            $this->res = $this->objReporte->generarReporteListado('MODMedioPagoPw','listarMedioPagoPw');
        } else{
            $this->objFunc=$this->create('MODMedioPagoPw');

            $this->res=$this->objFunc->listarMedioPagoPw($this->objParam);
        }
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function insertarMedioPagoPw(){
        $this->objFunc=$this->create('MODMedioPagoPw');
        if($this->objParam->insertar('id_medio_pago_pw')){
            $this->res=$this->objFunc->insertarMedioPagoPw($this->objParam);
        } else{
            $this->res=$this->objFunc->modificarMedioPagoPw($this->objParam);
        }
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function eliminarMedioPagoPw(){
        $this->objFunc=$this->create('MODMedioPagoPw');
        $this->res=$this->objFunc->eliminarMedioPagoPw($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

}

?>
