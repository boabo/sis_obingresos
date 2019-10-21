<?php
/**
 *@package pXP
 *@file gen-MODMovimientoEntidad.php
 *@author  (jrivera)
 *@date 17-05-2017 15:53:35
 *@description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
 */

class MODMovimientoEntidad extends MODbase{
    function __construct(CTParametro $pParam){
        parent::__construct($pParam);
    }

    function listarMovimientoEntidad(){
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento='obingresos.ft_movimiento_entidad_sel';
        $this->transaccion='OBING_MOE_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion

        $this->setParametro('id_agencia','id_entidad','int4');

        $this->capturaCount('total_credito','numeric');
        $this->capturaCount('total_debito','numeric');
        $this->capturaCount('total_credito_moneda','numeric');
        $this->capturaCount('total_debito_moneda','numeric');
        $this->capturaCount('monto_total','numeric');
        $this->capturaCount('saldo_actual','numeric');
        $this->capturaCount('tipo','varchar');
        $this->capturaCount('deudas','numeric');


        //Definicion de la lista del resultado del query
        $this->captura('id_movimiento_entidad','int4');
        $this->captura('id_moneda','int4');
        $this->captura('id_periodo_venta','int4');
        $this->captura('id_agencia','int4');
        $this->captura('garantia','varchar');
        $this->captura('monto_total','numeric');
        $this->captura('tipo','varchar');
        $this->captura('autorizacion__nro_deposito','varchar');
        $this->captura('estado_reg','varchar');
        $this->captura('credito','numeric');
        $this->captura('debito','numeric');
        $this->captura('ajuste','varchar');
        $this->captura('fecha','date');
        $this->captura('pnr','varchar');
        $this->captura('apellido','varchar');
        $this->captura('id_usuario_reg','int4');
        $this->captura('fecha_reg','timestamp');
        $this->captura('usuario_ai','varchar');
        $this->captura('id_usuario_ai','int4');
        $this->captura('fecha_mod','timestamp');
        $this->captura('id_usuario_mod','int4');
        $this->captura('usr_reg','varchar');
        $this->captura('usr_mod','varchar');
        $this->captura('moneda','varchar');
        $this->captura('credito_mb','numeric');
        $this->captura('debito_mb','numeric');
        $this->captura('tipo_cambio','numeric');
        $this->captura('monto','numeric');
        $this->captura('nro_deposito', 'varchar');
        $this->captura('id_deposito', 'int4');
        $this->captura('fk_id_movimiento_entidad', 'int4');
        $this->captura('desc_asociar', 'varchar');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function listarMovimientoEntidadAsociar(){
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento='obingresos.ft_movimiento_entidad_sel';
        $this->transaccion='OBING_MOVE_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion
        //$this->setCount(false);
        //$this->setParametro('id_agencia','id_entidad','int4');
        $this->setParametro('id_agencia','id_entidad','int4');
        //Definicion de la lista del resultado del query
        $this->captura('id_movimiento_entidad','int4');
        $this->captura('autorizacion__nro_deposito','varchar');
        $this->captura('estado_reg','varchar');
        $this->captura('monto','numeric');
        $this->captura('tipo','varchar');
        $this->captura('nro_deposito_boa', 'varchar');
        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function insertarMovimientoEntidad(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='obingresos.ft_movimiento_entidad_ime';
        $this->transaccion='OBING_MOE_INS';
        $this->tipo_procedimiento='IME';

        //Define los parametros para la funcion
        $this->setParametro('id_moneda','id_moneda','int4');
        $this->setParametro('id_periodo_venta','id_periodo_venta','int4');
        $this->setParametro('id_agencia','id_agencia','int4');
        $this->setParametro('garantia','garantia','varchar');
        $this->setParametro('monto_total','monto_total','numeric');
        $this->setParametro('tipo','tipo','varchar');
        $this->setParametro('autorizacion__nro_deposito','autorizacion__nro_deposito','varchar');
        $this->setParametro('estado_reg','estado_reg','varchar');
        $this->setParametro('monto','monto','numeric');
        $this->setParametro('ajuste','ajuste','varchar');
        $this->setParametro('fecha','fecha','date');
        $this->setParametro('pnr','pnr','varchar');
        $this->setParametro('apellido','apellido','varchar');
        $this->setParametro('fk_id_movimiento_entidad','fk_id_movimiento_entidad','int4');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function modificarMovimientoEntidad(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='obingresos.ft_movimiento_entidad_ime';
        $this->transaccion='OBING_MOE_MOD';
        $this->tipo_procedimiento='IME';

        //Define los parametros para la funcion
        $this->setParametro('id_movimiento_entidad','id_movimiento_entidad','int4');
        $this->setParametro('id_moneda','id_moneda','int4');
        $this->setParametro('id_periodo_venta','id_periodo_venta','int4');
        $this->setParametro('id_agencia','id_agencia','int4');
        $this->setParametro('garantia','garantia','varchar');
        $this->setParametro('monto_total','monto_total','numeric');
        $this->setParametro('tipo','tipo','varchar');
        $this->setParametro('autorizacion__nro_deposito','autorizacion__nro_deposito','varchar');
        $this->setParametro('estado_reg','estado_reg','varchar');
        $this->setParametro('monto','monto','numeric');
        $this->setParametro('ajuste','ajuste','varchar');
        $this->setParametro('fecha','fecha','date');
        $this->setParametro('pnr','pnr','varchar');
        $this->setParametro('apellido','apellido','varchar');
        $this->setParametro('fk_id_movimiento_entidad','fk_id_movimiento_entidad','int4');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function eliminarMovimientoEntidad(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='obingresos.ft_movimiento_entidad_ime';
        $this->transaccion='OBING_MOE_ELI';
        $this->tipo_procedimiento='IME';

        //Define los parametros para la funcion
        $this->setParametro('id_movimiento_entidad','id_movimiento_entidad','int4');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function anularAutorizacion(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='obingresos.ft_movimiento_entidad_ime';
        $this->transaccion='OBING_ANUAUTO_MOD';
        $this->tipo_procedimiento='IME';

        //Define los parametros para la funcion
        $this->setParametro('autorizacion','autorizacion','varchar');
        $this->setParametro('billete','billete','varchar');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    /*Aumentando para Arrastrar el saldo*/
    function arrastrarSaldo(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='obingresos.ft_movimiento_entidad_ime';
        $this->transaccion='OBING_ARRASTRAR_IME';
        $this->tipo_procedimiento='IME';

        //Define los parametros para la funcion
        $this->setParametro('id_agencia','id_agencia','int4');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function verificarSaldoAgencia(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='obingresos.ft_movimiento_entidad_ime';
        $this->transaccion='OBING_VERISALDO_IME';
        $this->tipo_procedimiento='IME';

        //Define los parametros para la funcion
        $this->setParametro('id_agencia','id_agencia','int4');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }
    /************************************/

}
?>
