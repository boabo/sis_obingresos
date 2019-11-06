<?php
/**
 *@package pXP
 *@file gen-MODFormaPagoPw.php
 *@author  (admin)
 *@date 04-06-2019 21:58:00
 *@description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
 */

class MODFormaPagoPw extends MODbase{

    function __construct(CTParametro $pParam){
        parent::__construct($pParam);
    }

    function listarFormaPagoPw(){
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento='obingresos.ft_forma_pago_pw_sel';
        $this->transaccion='OBING_FPPW_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion

        //Definicion de la lista del resultado del query
        $this->captura('id_forma_pago_pw','int4');
        $this->captura('estado_reg','varchar');
        $this->captura('name','varchar');
        $this->captura('country_code','varchar');
        $this->captura('erp_code','varchar');
        $this->captura('fop_code','varchar');
        $this->captura('manage_account','numeric');
        $this->captura('id_usuario_reg','int4');
        $this->captura('fecha_reg','timestamp');
        $this->captura('id_usuario_ai','int4');
        $this->captura('usuario_ai','varchar');
        $this->captura('id_usuario_mod','int4');
        $this->captura('fecha_mod','timestamp');
        $this->captura('usr_reg','varchar');
        $this->captura('usr_mod','varchar');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function insertarFormaPagoPw(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='obingresos.ft_forma_pago_pw_ime';
        $this->transaccion='OBING_FPPW_INS';
        $this->tipo_procedimiento='IME';

        //Define los parametros para la funcion
        $this->setParametro('estado_reg','estado_reg','varchar');
        $this->setParametro('name','name','varchar');
        $this->setParametro('country_code','country_code','varchar');
        $this->setParametro('erp_code','erp_code','varchar');
        $this->setParametro('fop_code','fop_code','varchar');
        $this->setParametro('manage_account','manage_account','numeric');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function modificarFormaPagoPw(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='obingresos.ft_forma_pago_pw_ime';
        $this->transaccion='OBING_FPPW_MOD';
        $this->tipo_procedimiento='IME';

        //Define los parametros para la funcion
        $this->setParametro('id_forma_pago_pw','id_forma_pago_pw','int4');
        $this->setParametro('estado_reg','estado_reg','varchar');
        $this->setParametro('name','name','varchar');
        $this->setParametro('country_code','country_code','varchar');
        $this->setParametro('erp_code','erp_code','varchar');
        $this->setParametro('fop_code','fop_code','varchar');
        $this->setParametro('manage_account','manage_account','numeric');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function eliminarFormaPagoPw(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='obingresos.ft_forma_pago_pw_ime';
        $this->transaccion='OBING_FPPW_ELI';
        $this->tipo_procedimiento='IME';

        //Define los parametros para la funcion
        $this->setParametro('id_forma_pago_pw','id_forma_pago_pw','int4');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

}
?>
