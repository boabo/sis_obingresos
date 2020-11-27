<?php
/**
 *@package pXP
 *@file gen-ACTFacturaNoUtilizada.php
 *@author  Maylee Perez Pastor
 *@date 05-05-2020 20:37:45
 *@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
 */

class ACTFacturaNoUtilizada extends ACTbase{

    function listarFacturaNoUtilizada(){
        $this->objParam->defecto('ordenacion','id_factura_manual');
        $this->objParam->defecto('dir_ordenacion','asc');

        if ($this->objParam->getParametro('id_punto_venta') != '') {
            $this->objParam->addFiltro(" fam.id_punto_venta = ". $this->objParam->getParametro('id_punto_venta')  );
        }

        if ($this->objParam->getParametro('id_sucursal') != '') {
            $this->objParam->addFiltro(" dos.id_sucursal = ". $this->objParam->getParametro('id_sucursal')  );
        }


        if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
            $this->objReporte = new Reporte($this->objParam,$this);
            $this->res = $this->objReporte->generarReporteListado('MODFacturaNoUtilizada','listarFacturaNoUtilizada');
        } else{
            $this->objFunc=$this->create('MODFacturaNoUtilizada');

            $this->res=$this->objFunc->listarFacturaNoUtilizada($this->objParam);
        }
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function insertarFacturaNoUtilizada(){
        $this->objFunc=$this->create('MODFacturaNoUtilizada');
        if($this->objParam->insertar('id_factura_manual')){
            $this->res=$this->objFunc->insertarFacturaNoUtilizada($this->objParam);
        } else{
            $this->res=$this->objFunc->modificarFacturaNoUtilizada($this->objParam);
        }
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function eliminarFacturaNoUtilizada(){
        $this->objFunc=$this->create('MODFacturaNoUtilizada');
        $this->res=$this->objFunc->eliminarFacturaNoUtilizada($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function obtenerNroFacInicial(){
        $this->objFunc=$this->create('MODFacturaNoUtilizada');
        $this->res=$this->objFunc->obtenerNroFacInicial($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

}

?>