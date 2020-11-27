<?php
/**
 *@package pXP
 *@file gen-ACTInstanciaPago.php
 *@author  (admin)
 *@date 04-06-2019 19:31:28
 *@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
 */

class ACTInstanciaPago extends ACTbase{

    function listarInstanciaPago(){
        $this->objParam->defecto('ordenacion','id_instancia_pago');

        $this->objParam->defecto('dir_ordenacion','asc');
              
        if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
            $this->objReporte = new Reporte($this->objParam,$this);
            $this->res = $this->objReporte->generarReporteListado('MODInstanciaPago','listarInstanciaPago');
        } else{
            $this->objFunc=$this->create('MODInstanciaPago');

            $this->res=$this->objFunc->listarInstanciaPago($this->objParam);
        }
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function insertarInstanciaPago(){
        $this->objFunc=$this->create('MODInstanciaPago');
        if($this->objParam->insertar('id_instancia_pago')){
            $this->res=$this->objFunc->insertarInstanciaPago($this->objParam);
        } else{
            $this->res=$this->objFunc->modificarInstanciaPago($this->objParam);
        }
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function eliminarInstanciaPago(){
        $this->objFunc=$this->create('MODInstanciaPago');
        $this->res=$this->objFunc->eliminarInstanciaPago($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

}

?>
