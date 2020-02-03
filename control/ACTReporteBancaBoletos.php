<?php
/**
*@package pXP
*@file gen-ACTReporteBancaBoletos.php
*@author  (Ismael Valdivia)
*@date 03-01-2020 10:54:00
*@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
*/

require_once(dirname(__FILE__).'/../reportes/RReporteGeneralBancaBoletosExcel.php');
require_once(dirname(__FILE__).'/../reportes/RReporteEstadoCuentasBancaBoletosExcel.php');
class ACTReporteBancaBoletos extends ACTbase{

function listarDatosBanca(){

    $this->objParam->defecto('ordenacion','nombre');
    $this->objParam->defecto('dir_ordenacion','asc');

    if($this->objParam->getParametro('tipo_agencia') != '' && $this->objParam->getParametro('tipo_agencia') != 'todas'){
        $this->objParam->addFiltro(" ag.tipo_agencia = ''".$this->objParam->getParametro('tipo_agencia')."''");
    }

    if($this->objParam->getParametro('id_lugar') != ''){
        $this->objParam->addFiltro(" ag.id_lugar IN ( ".$this->objParam->getParametro('id_lugar').")");
    }

    if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
        $this->objReporte = new Reporte($this->objParam,$this);
        $this->res = $this->objReporte->generarReporteListado('MODReporteBancaBoletos','listarDatosBanca');

    } else{
        $this->objFunc=$this->create('MODReporteBancaBoletos');
        $this->res=$this->objFunc->listarDatosBanca($this->objParam);
         $temp = Array();
         $temp['monto_boa_general'] = $this->res->extraData['total_boa_general'];
         $temp['monto_agencia_general'] = $this->res->extraData['total_agencia_general'];
         $temp['monto_debito_general'] = $this->res->extraData['total_debito_general'];
         $temp['tipo_reg'] = 'summary';
         $temp['agencia_id'] = 0;
         $this->res->total++;
         $this->res->addLastRecDatos($temp);
    }
    $this->res->imprimirRespuesta($this->res->generarJson());
}


function  ReporteDatosBanca(){
    if($this->objParam->getParametro('id_lugar') != ''){
        $this->objParam->addFiltro(" ag.id_lugar IN ( ".$this->objParam->getParametro('id_lugar').")");
    }
    if($this->objParam->getParametro('tipo_agencia') != '' && $this->objParam->getParametro('tipo_agencia') != 'todas'){
        $this->objParam->addFiltro(" ag.tipo_agencia = ''".$this->objParam->getParametro('tipo_agencia')."''");
    }
    $this->objFunc=$this->create('MODReporteBancaBoletos');
    $this->res=$this->objFunc->ReporteDatosBanca($this->objParam);
    //obtener titulo de reporte
   // var_dump($this->res);exit;
    $titulo ='Reporte General Banca Boletos';
    //Genera el nombre del archivo (aleatorio + titulo)
    $nombreArchivo=uniqid(md5(session_id()).$titulo);
    $nombreArchivo.='.xls';
    $this->objParam->addParametro('nombre_archivo',$nombreArchivo);
    $this->objParam->addParametro('datos',$this->res->datos);
    //Instancia la clase de excel
    $this->objReporteFormato=new RReporteGeneralBancaBoletosExcel($this->objParam);
    $this->objReporteFormato->generarDatos();
    $this->objReporteFormato->generarReporte();

    $this->mensajeExito=new Mensaje();
    $this->mensajeExito->setMensaje('EXITO','Reporte.php','Reporte generado',
        'Se generó con éxito el reporte: '.$nombreArchivo,'control');
    $this->mensajeExito->setArchivoGenerado($nombreArchivo);
    $this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());

}


function  ReporteEstadoCuentas(){

    $this->objFunc=$this->create('MODReporteBancaBoletos');
    $this->res=$this->objFunc->ReporteEstadoCuentas($this->objParam);
    //obtener titulo de reporte
    $titulo ='Reporte Estado de Cuentas';
    //Genera el nombre del archivo (aleatorio + titulo)
    $nombreArchivo=uniqid(md5(session_id()).$titulo);
    $nombreArchivo.='.xls';
    $this->objParam->addParametro('nombre_archivo',$nombreArchivo);
    $this->objParam->addParametro('datos',$this->res->datos);
    //Instancia la clase de excel
    $this->objReporteFormato=new RReporteEstadoCuentasBancaBoletosExcel($this->objParam);
    $this->objReporteFormato->generarDatos();
    $this->objReporteFormato->generarReporte();

    $this->mensajeExito=new Mensaje();
    $this->mensajeExito->setMensaje('EXITO','Reporte.php','Reporte generado',
        'Se generó con éxito el reporte: '.$nombreArchivo,'control');
    $this->mensajeExito->setArchivoGenerado($nombreArchivo);
    $this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());

}

}

?>
