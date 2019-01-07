<?php
/**
*@package pXP
*@file gen-ACTReporteCuenta.php
*@author  (Ismael.Valdivia)
*@date 11-06-2018 15:14:58
*@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo*/


require_once(dirname(__FILE__).'/../reportes/RReporteControl.php');
class ACTControlAgencias extends ACTbase{

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
            $this->res = $this->objReporte->generarReporteListado('MODControlAgencias','reporteSaldoVigente');

        } else{
            $this->objFunc=$this->create('MODControlAgencias');
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

    function  reporteGeneralEstadoCuenta(){
      $this->objFunc=$this->create('MODControlAgencias');
      $this->res=$this->objFunc->reporteGenrealCuenta($this->objParam);
      //obtener titulo de reporte
       //var_dump($this->res);exit;
      $titulo ='Control Agencia';
      //Genera el nombre del archivo (aleatorio + titulo)
      $nombreArchivo=uniqid(md5(session_id()).$titulo);
      $nombreArchivo.='.xls';
      $this->objParam->addParametro('nombre_archivo',$nombreArchivo);
      $this->objParam->addParametro('datos',$this->res->datos);
      //Instancia la clase de excel
      $this->objReporteFormato=new RReporteControl($this->objParam);
      $this->objReporteFormato->generarDatos();
      $this->objReporteFormato->generarReporte();

      $this->mensajeExito=new Mensaje();
      $this->mensajeExito->setMensaje('EXITO','Reporte.php','Reporte generado',
          'Se generó con éxito el reporte: '.$nombreArchivo,'control');
      $this->mensajeExito->setArchivoGenerado($nombreArchivo);
      $this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());

    }

    function listarverificarMoneda(){
        $this->objParam->defecto('ordenacion','id_agencia');
        $this->objParam->defecto('dir_ordenacion','asc');
        if($this->objParam->getParametro('id_agencia') != '') {
            $this->objParam->addFiltro("mo.id_agencia = " . $this->objParam->getParametro('id_agencia'));
            //var_dump($this->objParam->getParametro('fecha_fin'));exit;
        }
        if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
          $this->objReporte = new Reporte($this->objParam,$this);
          $this->res = $this->objReporte->generarReporteListado('MODControlAgencias','listarverificarMoneda');
        } else{
          $this->objFunc=$this->create('MODControlAgencias');

          $this->res=$this->objFunc->listarverificarMoneda($this->objParam);
        }
        $this->res->imprimirRespuesta($this->res->generarJson());
    }


}
?>
