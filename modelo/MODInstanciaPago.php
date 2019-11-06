<?php
/**
 *@package pXP
 *@file gen-MODInstanciaPago.php
 *@author  (admin)
 *@date 04-06-2019 19:31:28
 *@description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
 */

class MODInstanciaPago extends MODbase{

    function __construct(CTParametro $pParam){
        parent::__construct($pParam);
    }

    function listarInstanciaPago(){
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento='obingresos.ft_instancia_pago_sel';
        $this->transaccion='OBING_INSP_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion

        //Definicion de la lista del resultado del query
        $this->captura('id_instancia_pago','int4');
        $this->captura('estado_reg','varchar');
        $this->captura('id_medio_pago','int4');
        $this->captura('instancia_pago_id','int4');
        $this->captura('nombre','varchar');
        $this->captura('codigo','varchar');
        $this->captura('codigo_forma_pago','varchar');
        $this->captura('codigo_medio_pago','varchar');
        $this->captura('id_usuario_reg','int4');
        $this->captura('fecha_reg','timestamp');
        $this->captura('id_usuario_ai','int4');
        $this->captura('usuario_ai','varchar');
        $this->captura('id_usuario_mod','int4');
        $this->captura('fecha_mod','timestamp');
        $this->captura('usr_reg','varchar');
        $this->captura('usr_mod','varchar');
        $this->captura('fp_code','varchar');
        $this->captura('ins_code','varchar');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function insertarInstanciaPago(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='obingresos.ft_instancia_pago_ime';
        $this->transaccion='OBING_INSP_INS';
        $this->tipo_procedimiento='IME';

        //Define los parametros para la funcion
        $this->setParametro('estado_reg','estado_reg','varchar');
        $this->setParametro('id_medio_pago','id_medio_pago','int4');
        $this->setParametro('instancia_pago_id','instancia_pago_id','int4');
        $this->setParametro('nombre','nombre','varchar');
        $this->setParametro('codigo','codigo','varchar');
        $this->setParametro('codigo_forma_pago','codigo_forma_pago','varchar');
        $this->setParametro('codigo_medio_pago','codigo_medio_pago','varchar');
        $this->setParametro('fp_code','fp_code','varchar');
        $this->setParametro('ins_code','ins_code','varchar');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function modificarInstanciaPago(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='obingresos.ft_instancia_pago_ime';
        $this->transaccion='OBING_INSP_MOD';
        $this->tipo_procedimiento='IME';

        //Define los parametros para la funcion
        $this->setParametro('id_instancia_pago','id_instancia_pago','int4');
        $this->setParametro('estado_reg','estado_reg','varchar');
        $this->setParametro('id_medio_pago','id_medio_pago','int4');
        $this->setParametro('instancia_pago_id','instancia_pago_id','int4');
        $this->setParametro('nombre','nombre','varchar');
        $this->setParametro('codigo','codigo','varchar');
        $this->setParametro('codigo_forma_pago','codigo_forma_pago','varchar');
        $this->setParametro('codigo_medio_pago','codigo_medio_pago','varchar');
        $this->setParametro('fp_code','fp_code','varchar');
        $this->setParametro('ins_code','ins_code','varchar');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function eliminarInstanciaPago(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='obingresos.ft_instancia_pago_ime';
        $this->transaccion='OBING_INSP_ELI';
        $this->tipo_procedimiento='IME';

        //Define los parametros para la funcion
        $this->setParametro('id_instancia_pago','id_instancia_pago','int4');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

}
?>
