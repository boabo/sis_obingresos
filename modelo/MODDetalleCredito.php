<?php
/**
 *@package pXP
 *@file gen-MODDetalleCredito.php
 *@author  (miguel.mamani)
 *@date 18-07-2018 16:53:28
 *@description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
 */

class MODDetalleCredito extends MODbase{

    function __construct(CTParametro $pParam){
        parent::__construct($pParam);
    }

    function listarDetalleCredito(){
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento='obingresos.ft_detalle_credito_sel';
        $this->transaccion='OBING_RDC_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion
        $this->capturaCount('suma_total','numeric');
        //Definicion de la lista del resultado del query
        $this->captura('id_movimiento_entidad','int4');
        $this->captura('id_agencia','int4');
        $this->captura('id_periodo_venta','int4');
        $this->captura('nro_deposito','varchar');
        $this->captura('monto_total','numeric');
        $this->captura('fecha','date');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }



}
?>