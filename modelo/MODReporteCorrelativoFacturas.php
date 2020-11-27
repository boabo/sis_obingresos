<?php
/**
 *@package pXP
 *@file MODReporteCorrelativoFacturas.php
 *@author  Maylee Perez Pastor
 *@date 24-07-2020 05:58:00
 *@description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
 */

class MODReporteCorrelativoFacturas extends MODbase{

    function __construct(CTParametro $pParam){
        parent::__construct($pParam);
    }

    function reporteCorrelativoFacturas(){
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento='obingresos.ft_reporte_correlativo_facturas_sel';
        $this->transaccion='OBING_RCORREFAC_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion
        $this->setCount(false);
       // $this->setTipoRetorno('record');

        $this->setParametro('id_lugar','id_lugar','int4');
        $this->setParametro('id_sucursal','id_sucursal','int4');
        $this->setParametro('id_punto_venta','id_punto_venta','int4');
        $this->setParametro('tipo_generacion','tipo_generacion','varchar');
        $this->setParametro('fecha_desde','fecha_desde','date');
        $this->setParametro('fecha_hasta','fecha_hasta','date');

        //Definicion de la lista del resultado del query
        $this->captura('estacion','varchar');
        $this->captura('sucursal','varchar');
        $this->captura('punto_venta','varchar');
        $this->captura('nroaut','varchar');
        $this->captura('nro_desde','int4');
        $this->captura('nro_hasta','int4');
        $this->captura('cantidad','int4');

        //Ejecuta la instruccion
        $this->armarConsulta();
        //echo $this->getConsulta();
        //exit;
        $this->ejecutarConsulta();

        return $this->respuesta;
    }

}
?>
