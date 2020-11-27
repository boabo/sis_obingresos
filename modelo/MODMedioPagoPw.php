<?php
/**
 *@package pXP
 *@file gen-MODMedioPagoPw.php
 *@author  (admin)
 *@date 04-06-2019 22:47:38
 *@description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
 */

class MODMedioPagoPw extends MODbase{

    function __construct(CTParametro $pParam){
        parent::__construct($pParam);
    }

    function listarMedioPagoPw(){
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento='obingresos.ft_medio_pago_pw_sel';
        $this->transaccion='OBING_MPPW_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion

        $this->setParametro('emision','emision','varchar');

        //Definicion de la lista del resultado del query
        $this->captura('id_medio_pago_pw','int4');
        $this->captura('estado_reg','varchar');
        //$this->captura('medio_pago_id','int4');
        $this->captura('forma_pago_id','int4');
        $this->captura('name','varchar');
        $this->captura('mop_code','varchar');
        $this->captura('code','varchar');
        $this->captura('id_usuario_reg','int4');
        $this->captura('fecha_reg','timestamp');
        $this->captura('id_usuario_ai','int4');
        $this->captura('usuario_ai','varchar');
        $this->captura('id_usuario_mod','int4');
        $this->captura('fecha_mod','timestamp');
        $this->captura('usr_reg','varchar');
        $this->captura('usr_mod','varchar');

        $this->captura('regionales','varchar');
        $this->captura('sw_autorizacion','varchar');
        $this->captura('nombre_fp','varchar');
        $this->captura('fop_code','varchar');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();
        //Devuelve la respuesta
        return $this->respuesta;
    }

    function insertarMedioPagoPw(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='obingresos.ft_medio_pago_pw_ime';
        $this->transaccion='OBING_MPPW_INS';
        $this->tipo_procedimiento='IME';

        //Define los parametros para la funcion
        $this->setParametro('estado_reg','estado_reg','varchar');
        //$this->setParametro('medio_pago_id','medio_pago_id','int4');
        $this->setParametro('forma_pago_id','forma_pago_id','int4');
        $this->setParametro('name','name','varchar');
        $this->setParametro('mop_code','mop_code','varchar');
        $this->setParametro('code','code','varchar');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function modificarMedioPagoPw(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='obingresos.ft_medio_pago_pw_ime';
        $this->transaccion='OBING_MPPW_MOD';
        $this->tipo_procedimiento='IME';

        //Define los parametros para la funcion
        $this->setParametro('id_medio_pago_pw','id_medio_pago_pw','int4');
        $this->setParametro('estado_reg','estado_reg','varchar');
        //$this->setParametro('medio_pago_id','medio_pago_id','int4');
        $this->setParametro('forma_pago_id','forma_pago_id','int4');
        $this->setParametro('name','name','varchar');
        $this->setParametro('mop_code','mop_code','varchar');
        $this->setParametro('code','code','varchar');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function eliminarMedioPagoPw(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='obingresos.ft_medio_pago_pw_ime';
        $this->transaccion='OBING_MPPW_ELI';
        $this->tipo_procedimiento='IME';

        //Define los parametros para la funcion
        $this->setParametro('id_medio_pago_pw','id_medio_pago_pw','int4');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function editarAutorizaciones(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='obingresos.ft_medio_pago_pw_ime';
        $this->transaccion='OBING_AUTORIZA_UDT';
        $this->tipo_procedimiento='IME';

        //Define los parametros para la funcion
        $this->setParametro('id_medio_pago_pw','id_medio_pago_pw','int4');
        $this->setParametro('sw_autorizacion','sw_autorizacion','varchar');
        $this->setParametro('regionales','regionales','varchar');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

}
?>
