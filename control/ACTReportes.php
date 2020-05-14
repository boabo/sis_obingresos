<?php
/**
 *@package BoA
 *@file    ACTReportes.php
 *@author  franklin.espinoza
 *@date    11-04-2020 15:14:58
 *@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
 */


require_once(dirname(__FILE__).'/../reportes/RReporteCruceAtcXLS.php');
require_once(dirname(__FILE__).'/../reportes/RReporteCruceLinkserXLS.php');

class ACTReportes extends ACTbase{


    function  generarCruceTarjetasBoletos(){

        $this->objParam->defecto('ordenacion','nombre');
        $this->objParam->defecto('dir_ordenacion','asc');
        $this->objParam->defecto('cantidad','5000');
        $this->objParam->defecto('puntero','0');

        $this->objParam->addFiltro("(1 in (select id_rol from segu.tusuario_rol ur where ur.id_usuario = 1 ) or 
        ( 1 in (select id_usuario from vef.tsucursal_usuario sucusu where puve.id_punto_venta = sucusu.id_punto_venta )))");

        $this->objFunc=$this->create('MODReportes');
        $this->res=$this->objFunc->generarCruceTarjetasBoletos($this->objParam);
        $this->datos = $this->res->getDatos();

        //obtener titulo de reporte
        $titulo ='Cruce Tarjetas Boletos';
        //Genera el nombre del archivo (aleatorio + titulo)
        $nombreArchivo=uniqid(md5(session_id()).$titulo);
        $nombreArchivo.='.xls';
        $tipo_rep = $this->objParam->getParametro('tipo_reporte');
        $this->objParam->addParametro('nombre_archivo',$nombreArchivo);
        $this->objParam->addParametro('datos',$this->datos);
        $this->objParam->addParametro('tipo',$tipo_rep);
        $this->objParam->addParametro('fecha_desde',$this->objParam->getParametro('fecha_desde'));
        $this->objParam->addParametro('fecha_hasta',$this->objParam->getParametro('fecha_hasta'));

        //Instancia la clase de excel
        if($tipo_rep == 'pago_atc'){
            $this->objReporteFormato = new RReporteCruceAtcXLS($this->objParam);
        }else if($tipo_rep == 'pago_linkser'){
            $this->objReporteFormato = new RReporteCruceLinkserXLS($this->objParam);
        }

        $this->objReporteFormato->imprimeDatos();
        $this->objReporteFormato->generarReporte();

        $this->mensajeExito=new Mensaje();
        $this->mensajeExito->setMensaje('EXITO','Reporte.php','Reporte generado',
            'Se generó con éxito el reporte: '.$nombreArchivo,'control');
        $this->mensajeExito->setArchivoGenerado($nombreArchivo);
        $this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());

    }

    function listarAgencias(){
        $this->objParam->defecto('ordenacion','nombre');
        $this->objParam->defecto('dir_ordenacion','asc');
        $this->objParam->defecto('cantidad','15');
        $this->objParam->defecto('puntero','0');

        $this->objParam->addFiltro("(1 in (select id_rol from segu.tusuario_rol ur where ur.id_usuario = 1 ) or 
            ( 1 in (select id_usuario from vef.tsucursal_usuario sucusu where puve.id_punto_venta = sucusu.id_punto_venta )))");

        $this->objFunc=$this->create('MODReportes');
        $this->res=$this->objFunc->listarAgencias($this->objParam);

        if($this->objParam->getParametro('_adicionar')!=''){

            $respuesta = $this->res->getDatos();

            array_unshift ( $respuesta, array(  'id_punto_venta'=>'0',
                'nombre'=>'Todos',
                'codigo'=>'Todos',
                'office_id'=>'todos'));

            $this->res->setDatos($respuesta);
        }

        $this->res->imprimirRespuesta($this->res->generarJson());
    }
}
?>
