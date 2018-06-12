<?php
/**
*@package pXP
*@file gen-ACTReporteCuenta.php
*@author  (miguel.mamani)
*@date 11-06-2018 15:14:58
*@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
*/

require_once(dirname(__FILE__).'/../reportes/REstadoCuentaCorriente.php');
class ACTReporteCuenta extends ACTbase{


    function EstadoCuenta(){
        $this->objFunc=$this->create('MODReporteCuenta');
        $cbteHeader = $this->objFunc->listarReporteCuenta($this->objParam);
        if($cbteHeader->getTipo() == 'EXITO'){
            return $cbteHeader;
        }
        else{
            $cbteHeader->imprimirRespuesta($cbteHeader->generarJson());
            exit;
        }

    }

    function ResumenEstadoCC(){
        $this->objFunc=$this->create('MODReporteCuenta');
        $cbteHeader = $this->objFunc->ResumenEstadoCC($this->objParam);
        if($cbteHeader->getTipo() == 'EXITO'){
            return $cbteHeader;
        }
        else{
            $cbteHeader->imprimirRespuesta($cbteHeader->generarJson());
            exit;
        }

    }
    function listarReporteCuenta(){

        $dataSource = $this->EstadoCuenta();
        $resumen = $this->ResumenEstadoCC();
       // var_dump($resumen);exit;
        $nombreArchivo = uniqid(md5(session_id()).'Estado Cuentas').'.xls';
        $this->objParam->addParametro('nombre_archivo',$nombreArchivo);
        $reporte =new REstadoCuentaCorriente($this->objParam);
        $reporte->datosHeader($dataSource->getDatos(),$resumen->getDatos());
        $reporte->generarReporte();
        $this->mensajeExito=new Mensaje();
        $this->mensajeExito->setMensaje('EXITO','Reporte.php','Reporte generado','Se generó con éxito el reporte: '.$nombreArchivo,'control');
        $this->mensajeExito->setArchivoGenerado($nombreArchivo);
        $this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());
    }

}

?>