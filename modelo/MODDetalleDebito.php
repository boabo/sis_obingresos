<?php
/**
 *@package pXP
 *@file gen-MODDetalleDebito.php
 *@author  (miguel.mamani)
 *@date 18-07-2018 16:54:10
 *@description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
 */

class MODDetalleDebito extends MODbase{

    function __construct(CTParametro $pParam){
        parent::__construct($pParam);
    }

    function listarDetalleDebito(){
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento='obingresos.ft_detalle_debito_sel';
        $this->transaccion='OBING_DBR_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion

        $this->capturaCount('monto_total','numeric');
        $this->capturaCount('neto_total','numeric');
        $this->capturaCount('comision_total','numeric');
        $this->capturaCount('total_monto','numeric');
        //Definicion de la lista del resultado del query
        $this->captura('id_detalle_boletos_web','int4');
        $this->captura('id_agencia','int4');
        $this->captura('id_periodo_venta','int4');
        $this->captura('id_movimiento_entidad','int4');
        $this->captura('numero_autorizacion','varchar');
        $this->captura('nro_boleto','text');
        $this->captura('fecha','date');
        $this->captura('monto','numeric');
        $this->captura('neto','numeric');
        $this->captura('comision','numeric');
        $this->captura('total_monto','numeric');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function insertarDetalleDebito(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='obingresos.ft_detalle_debito_ime';
        $this->transaccion='OBING_DBR_INS';
        $this->tipo_procedimiento='IME';

        //Define los parametros para la funcion
        $this->setParametro('saldo','saldo','numeric');
        $this->setParametro('fecha','fecha','date');
        $this->setParametro('comision','comision','numeric');
        $this->setParametro('neto','neto','numeric');
        $this->setParametro('importe','importe','numeric');
        $this->setParametro('billeta_pnr','billeta_pnr','text');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function modificarDetalleDebito(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='obingresos.ft_detalle_debito_ime';
        $this->transaccion='OBING_DBR_MOD';
        $this->tipo_procedimiento='IME';

        //Define los parametros para la funcion
        $this->setParametro('id_agencia','id_agencia','int4');
        $this->setParametro('saldo','saldo','numeric');
        $this->setParametro('fecha','fecha','date');
        $this->setParametro('comision','comision','numeric');
        $this->setParametro('neto','neto','numeric');
        $this->setParametro('importe','importe','numeric');
        $this->setParametro('billeta_pnr','billeta_pnr','text');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function eliminarDetalleDebito(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='obingresos.ft_detalle_debito_ime';
        $this->transaccion='OBING_DBR_ELI';
        $this->tipo_procedimiento='IME';

        //Define los parametros para la funcion
        $this->setParametro('id_agencia','id_agencia','int4');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

}
?>