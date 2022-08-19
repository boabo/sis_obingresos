<?php
/**
 *@package pXP
 *@file gen-MODConsultaViajeroFrecuente.php
 *@author  (miguel.mamani)
 *@date 15-12-2017 14:59:25
 *@description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
 */
class MODConsultaViajeroFrecuente extends MODbase{
    function __construct(CTParametro $pParam){
        parent::__construct($pParam);
    }
    function listarConsultaViajeroFrecuente(){
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento='obingresos.ft_consulta_viajero_frecuente_sel';
        $this->transaccion='OBING_VIF_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion
        //Definicion de la lista del resultado del query
        $this->captura('id_consulta_viajero_frecuente','int4');
        $this->captura('ffid','varchar');
        $this->captura('estado_reg','varchar');
        $this->captura('message','varchar');
        $this->captura('message_canjeado','varchar');
        $this->captura('voucher_code','varchar');
        $this->captura('status','varchar');
        $this->captura('status_canjeado','varchar');
        $this->captura('nro_boleto','varchar');
        $this->captura('pnr','varchar');
        $this->captura('estado','varchar');
        $this->captura('id_usuario_reg','int4');
        $this->captura('fecha_reg','timestamp');
        $this->captura('usuario_ai','varchar');
        $this->captura('id_usuario_ai','int4');
        $this->captura('fecha_mod','timestamp');
        $this->captura('id_usuario_mod','int4');
        $this->captura('usr_reg','varchar');
        $this->captura('usr_mod','varchar');

        $this->captura('desc_persona','text');
        //Ejecuta la instruccion
        $this->armarConsulta();
        //echo $this->consulta; exit;
        $this->ejecutarConsulta();
        //Devuelve la respuesta
        return $this->respuesta;
    }
    function insertarConsultaViajeroFrecuente(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='obingresos.ft_consulta_viajero_frecuente_ime';
        $this->transaccion='OBING_VIF_INS';
        $this->tipo_procedimiento='IME';
        //Define los parametros para la funcion
        $this->setParametro('ffid','ffid','varchar');
        $this->setParametro('estado_reg','estado_reg','varchar');
        $this->setParametro('message','message','varchar');
        $this->setParametro('voucher_code','voucher_code','varchar');
        $this->setParametro('status','status','varchar');
        //$this->setParametro('nro_boleto','nro_boleto','varchar');
        //Ejecuta la instruccion
        $this->armarConsulta();
        //echo $this->consulta;exit;
        $this->ejecutarConsulta();
        //Devuelve la respuesta
        return $this->respuesta;
    }
    function modificarConsultaViajeroFrecuente(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='obingresos.ft_consulta_viajero_frecuente_ime';
        $this->transaccion='OBING_VIF_MOD';
        $this->tipo_procedimiento='IME';

        //Define los parametros para la funcion
        $this->setParametro('id_consulta_viajero_frecuente','id_consulta_viajero_frecuente','int4');
        //$this->setParametro('ffid','ffid','int4');
        //$this->setParametro('estado_reg','estado_reg','varchar');
        //$this->setParametro('message','message','varchar');
        //$this->setParametro('voucher_code','voucher_code','varchar');
        $this->setParametro('status','status','varchar');
        $this->setParametro('nro_boleto','nro_boleto','varchar');
        $this->setParametro('pnr','pnr','varchar');
        $this->setParametro('message','message','varchar');

        //Ejecuta la instruccion
        $this->armarConsulta();
        //echo $this->consulta;exit;
        $this->ejecutarConsulta();
        //Devuelve la respuesta
        return $this->respuesta;
    }
    function eliminarConsultaViajeroFrecuente(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='obingresos.ft_consulta_viajero_frecuente_ime';
        $this->transaccion='OBING_VIF_ELI';
        $this->tipo_procedimiento='IME';
        //Define los parametros para la funcion
        $this->setParametro('id_consulta_viajero_frecuente','id_consulta_viajero_frecuente','int4');
        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();
        //Devuelve la respuesta
        return $this->respuesta;
    }
}
?>
