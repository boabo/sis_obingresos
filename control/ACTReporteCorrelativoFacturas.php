<?php
/**
 *@package pXP
 *@file ACTReporteCorrelativoFacturas.php
 *@author  Maylee Perez Pastor
 *@date 24-07-2020 05:58:00
 *@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
 */

include_once(dirname(__FILE__).'/../../lib/lib_general/funciones.inc.php');
require_once(dirname(__FILE__).'/../../pxp/pxpReport/ReportWriter.php');
require_once(dirname(__FILE__).'/../../pxp/pxpReport/DataSource.php');

include_once(dirname(__FILE__).'/../../lib/PHPMailer/class.phpmailer.php');
include_once(dirname(__FILE__).'/../../lib/PHPMailer/class.smtp.php');
include_once(dirname(__FILE__).'/../../lib/lib_general/cls_correo_externo.php');

require_once(dirname(__FILE__).'/../reportes/RCorrelativoFac.php');
//require_once(dirname(__FILE__).'/../../sis_obingresos/reportes/RCorrelativoFac.php');
//require_once(dirname(__FILE__).'/../reportes/RCorrelativoFacConsolidado.php');

class ACTReporteCorrelativoFacturas extends ACTbase{


    function reporteCorrelativoFacturas(){

        $dataSource = new DataSource();


        $id_lugar = $this->objParam->getParametro('id_lugar');
        $id_sucursal = $this->objParam->getParametro('id_sucursal');
        //$tipo_reporte = $this->objParam->getParametro('tipo_reporte');
        $id_punto_venta = $this->objParam->getParametro('id_punto_venta');
        $tipo_generacion = $this->objParam->getParametro('tipo_generacion');
        $fecha_desde = $this->objParam->getParametro('fecha_desde');
        $fecha_hasta = $this->objParam->getParametro('fecha_hasta');

        $this->objParam->addParametroConsulta('ordenacion','id_punto_venta');
        $this->objParam->addParametroConsulta('dir_ordenacion','ASC');
        $this->objParam->addParametroConsulta('cantidad',1000);
        $this->objParam->addParametroConsulta('puntero',0);

        $dataSource->putParameter('id_lugar', $id_lugar);
        $dataSource->putParameter('id_sucursal', $id_sucursal);
        //$dataSource->putParameter('tipo_reporte', $tipo_reporte);
        $dataSource->putParameter('id_punto_venta', $id_punto_venta);
        $dataSource->putParameter('tipo_generacion', $tipo_generacion);
        $dataSource->putParameter('fecha_desde', $fecha_desde);
        $dataSource->putParameter('fecha_hasta', $fecha_hasta);

        $this->objFunc = $this->create('MODReporteCorrelativoFacturas');
        $resultCorrelativo = $this->objFunc->reporteCorrelativoFacturas($this->objParam);

        if($resultCorrelativo->getTipo()=='EXITO'){

            $datosCorrelativo = $resultCorrelativo->getDatos();
            $dataSource->setDataSet($datosCorrelativo);

            $nombreArchivo = 'CorrelativoFac.pdf';

            //if($tipo_reporte == 'consolidado'){
                $reporte = new RCorrelativoFac();
            /*}else{
                $reporte = new RCorrelativoFac();
            }*/




            $reporte->setDataSource($dataSource);
            $reportWriter = new ReportWriter($reporte, dirname(__FILE__).'/../../reportes_generados/'.$nombreArchivo);
            $reportWriter->writeReport(ReportWriter::PDF);

            $mensajeExito = new Mensaje();
            $mensajeExito->setMensaje('EXITO','Reporte.php','Reporte generado',
                'Se generó con éxito el reporte: '.$nombreArchivo,'control');
            $mensajeExito->setArchivoGenerado($nombreArchivo);
            $this->res = $mensajeExito;
            $this->res->imprimirRespuesta($this->res->generarJson());
        }
        else{
            $resultCorrelativo->imprimirRespuesta($resultCorrelativo->generarJson());
        }



    }

}

?>