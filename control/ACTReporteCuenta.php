<?php
/**
*@package pXP
*@file gen-ACTReporteCuenta.php
*@author  (miguel.mamani)
*@date 11-06-2018 15:14:58
*@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
*/

require_once(dirname(__FILE__).'/../reportes/REstadoCuentaCorriente.php');
require_once(dirname(__FILE__).'/../reportes/RReporteEstCuentaIng.php');
require_once(dirname(__FILE__).'/../reportes/RMovimientos.php');
require_once(dirname(__FILE__).'/../reportes/REstadoCuentaGeneral.php');
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
    function reporteSaldoVigente(){
        $this->objParam->defecto('ordenacion','nombre');
        $this->objParam->defecto('dir_ordenacion','asc');

        if($this->objParam->getParametro('id_lugar') != ''){
            $this->objParam->addFiltro(" ag.id_lugar IN ( ".$this->objParam->getParametro('id_lugar').")");
        }
        if($this->objParam->getParametro('tipo_agencia') != '' && $this->objParam->getParametro('tipo_agencia') != 'todas'){
            $this->objParam->addFiltro(" ag.tipo_agencia = ''".$this->objParam->getParametro('tipo_agencia')."''");
        }
        if($this->objParam->getParametro('forma_pago') != '' && $this->objParam->getParametro('forma_pago') != 'todas'){
            $this->objParam->addFiltro("''" . $this->objParam->getParametro('forma_pago') . "''  = ANY(con.formas_pago)");
        }

        if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
            $this->objReporte = new Reporte($this->objParam,$this);
            $this->res = $this->objReporte->generarReporteListado('MODReporteCuenta','reporteSaldoVigente');

        } else{
            $this->objFunc=$this->create('MODReporteCuenta');
            $this->res=$this->objFunc->reporteSaldoVigente($this->objParam);
             $temp = Array();
             $temp['monto_credito'] = $this->res->extraData['total_creditos'];
             $temp['monto_debito'] = $this->res->extraData['total_debitos'];
             $temp['monto_ajustes'] = $this->res->extraData['total_ajustes'];
             $temp['saldo_con_boleto'] = $this->res->extraData['total_saldo_con_boleto'];
             $temp['saldo_sin_boleto'] = $this->res->extraData['total_saldo_sin_boleto'];
             $temp['tipo_reg'] = 'summary';
             $temp['id_agencia'] = 0;
             $this->res->total++;
             $this->res->addLastRecDatos($temp);
        }
        $this->res->imprimirRespuesta($this->res->generarJson());
    }
    function EstadoCuentaIng(){
        $this->objFunc=$this->create('MODReporteCuenta');
        $cbteHeader = $this->objFunc->reporteEstadoCuentaIng($this->objParam);
        if($cbteHeader->getTipo() == 'EXITO'){
            return $cbteHeader;
        }
        else{
            $cbteHeader->imprimirRespuesta($cbteHeader->generarJson());
            exit;
        }

    }
    function listarReporteCuentaIng(){
        $dataSource = $this->EstadoCuentaIng();
        // var_dump($resumen);exit;
        $nombreArchivo = uniqid(md5(session_id()).'Estado Cuentas').'.xls';
        $this->objParam->addParametro('nombre_archivo',$nombreArchivo);
        $reporte =new RReporteEstCuentaIng($this->objParam);
        $reporte->datosHeader($dataSource->getDatos());
        $reporte->generarReporte();
        $this->mensajeExito=new Mensaje();
        $this->mensajeExito->setMensaje('EXITO','Reporte.php','Reporte generado','Se generó con éxito el reporte: '.$nombreArchivo,'control');
        $this->mensajeExito->setArchivoGenerado($nombreArchivo);
        $this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());
    }

    function Movimientos(){
        $this->objFunc=$this->create('MODReporteCuenta');
        $cbteHeader = $this->objFunc->reporteEstadoMovimiento($this->objParam);
        if($cbteHeader->getTipo() == 'EXITO'){
            return $cbteHeader;
        }
        else{
            $cbteHeader->imprimirRespuesta($cbteHeader->generarJson());
            exit;
        }

    }
    function listarReporteMovimientos(){
        $dataSource = $this->Movimientos();
        // var_dump($resumen);exit;
        $nombreArchivo = uniqid(md5(session_id()).'Estado Cuentas').'.xls';
        $this->objParam->addParametro('nombre_archivo',$nombreArchivo);
        $reporte =new RMovimientos($this->objParam);
        $reporte->datosHeader($dataSource->getDatos());
        $reporte->generarReporte();
        $this->mensajeExito=new Mensaje();
        $this->mensajeExito->setMensaje('EXITO','Reporte.php','Reporte generado','Se generó con éxito el reporte: '.$nombreArchivo,'control');
        $this->mensajeExito->setArchivoGenerado($nombreArchivo);
        $this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());
    }
    function listarPeriodo(){
        $this->objParam->defecto('ordenacion','id_periodo_venta');
        $this->objParam->defecto('dir_ordenacion','asc');

        $this->objFunc=$this->create('MODReporteCuenta');
        $this->res=$this->objFunc->listarPeriodo($this->objParam);
        $respuesta = $this->res->getDatos();
        array_unshift ( $respuesta, array(  'id_periodo_venta'=>null,
            'periodo'=>'Periodo Vigente'));
        $this->res->setDatos($respuesta);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }
    function  reporteGeneralEstadoCuenta(){
        if($this->objParam->getParametro('id_lugar') != ''){
            $this->objParam->addFiltro(" ag.id_lugar IN ( ".$this->objParam->getParametro('id_lugar').")");
        }
        if($this->objParam->getParametro('tipo_agencia') != '' && $this->objParam->getParametro('tipo_agencia') != 'todas'){
            $this->objParam->addFiltro(" ag.tipo_agencia = ''".$this->objParam->getParametro('tipo_agencia')."''");
        }
        if($this->objParam->getParametro('forma_pago') != '' && $this->objParam->getParametro('forma_pago') != 'todas'){
            $this->objParam->addFiltro("''" . $this->objParam->getParametro('forma_pago') . "''  = ANY(con.formas_pago)");
        }
        $this->objFunc=$this->create('MODReporteCuenta');
        $this->res=$this->objFunc->reporteGenrealCuenta($this->objParam);
        //obtener titulo de reporte
       // var_dump($this->res);exit;
        $titulo ='Cuenta Corriente';
        //Genera el nombre del archivo (aleatorio + titulo)
        $nombreArchivo=uniqid(md5(session_id()).$titulo);
        $nombreArchivo.='.xls';
        $this->objParam->addParametro('nombre_archivo',$nombreArchivo);
        $this->objParam->addParametro('datos',$this->res->datos);
        //Instancia la clase de excel
        $this->objReporteFormato=new REstadoCuentaGeneral($this->objParam);
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
