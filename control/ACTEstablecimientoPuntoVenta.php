<?php
/**
 *@package pXP
 *@file gen-ACTEstablecimientoPuntoVenta.php
 *@author  (admin)
 *@date 17-03-2021 11:14:41
 *@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
 */

class ACTEstablecimientoPuntoVenta extends ACTbase{

    function listarEstablecimientoPuntoVenta(){
        $this->objParam->defecto('ordenacion','id_establecimiento_punto_venta');
        $this->objParam->defecto('dir_ordenacion','asc');

        $this->objParam->addFiltro(" estpven.estado_reg in (''activo'')");

        if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
            $this->objReporte = new Reporte($this->objParam,$this);
            $this->res = $this->objReporte->generarReporteListado('MODEstablecimientoPuntoVenta','listarEstablecimientoPuntoVenta');
        } else{
            $this->objFunc=$this->create('MODEstablecimientoPuntoVenta');

            $this->res=$this->objFunc->listarEstablecimientoPuntoVenta($this->objParam);
        }
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function insertarEstablecimientoPuntoVenta(){
        $this->objFunc=$this->create('MODEstablecimientoPuntoVenta');
        if($this->objParam->insertar('id_establecimiento_punto_venta')){
            $this->res=$this->objFunc->insertarEstablecimientoPuntoVenta($this->objParam);
        } else{
            $this->res=$this->objFunc->modificarEstablecimientoPuntoVenta($this->objParam);
        }
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function eliminarEstablecimientoPuntoVenta(){
        $this->objFunc=$this->create('MODEstablecimientoPuntoVenta');
        $this->res=$this->objFunc->eliminarEstablecimientoPuntoVenta($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function listarPuntoVentaStage(){
        $this->objParam->defecto('ordenacion','id_stage_pv');

        $this->objParam->defecto('dir_ordenacion','asc');
        if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
            $this->objReporte = new Reporte($this->objParam,$this);
            $this->res = $this->objReporte->generarReporteListado('MODEstablecimientoPuntoVenta','listarPuntoVentaStage');
        } else{
            $this->objFunc=$this->create('MODEstablecimientoPuntoVenta');

            $this->res=$this->objFunc->listarPuntoVentaStage($this->objParam);
        }
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

}

?>