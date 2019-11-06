<?php
/**
 *@package pXP
 *@file gen-MODBoletosObservados.php
 *@author  (admin)
 *@date 04-06-2019 19:39:16
 *@description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
 */

class MODBoletosObservados extends MODbase{

    function __construct(CTParametro $pParam){
        parent::__construct($pParam);
    }

    function listarBoletosObservados(){
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento='obingresos.ft_boletos_observados_sel';
        $this->transaccion='OBING_BOBS_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion

        //Definicion de la lista del resultado del query
        $this->captura('id_boletos_observados','int4');
        $this->captura('estado_reg','varchar');
        $this->captura('pnr','varchar');
        $this->captura('nro_autorizacion','varchar');
        $this->captura('moneda','varchar');
        $this->captura('importe_total','numeric');
        $this->captura('fecha_emision','date');
        $this->captura('estado_p','varchar');
        $this->captura('forma_pago','varchar');
        $this->captura('medio_pago','varchar');
        $this->captura('instancia_pago','varchar');
        $this->captura('office_id_emisor','varchar');
        $this->captura('pnr_prov','varchar');
        $this->captura('nro_autorizacion_prov','varchar');
        $this->captura('office_id_emisor_prov','varchar');
        $this->captura('importe_prov','numeric');
        $this->captura('moneda_prov','varchar');
        $this->captura('estado_prov','varchar');
        $this->captura('fecha_autorizacion_prov','date');
        $this->captura('tipo_error','varchar');
        $this->captura('tipo_validacion','varchar');
        $this->captura('prov_informacion','varchar');
        //$this->captura('id_instancia_pago','int4');
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

    function insertarBoletosObservados(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='obingresos.ft_boletos_observados_ime';
        $this->transaccion='OBING_BOBS_INS';
        $this->tipo_procedimiento='IME';

        //Define los parametros para la funcion
        $this->setParametro('estado_reg','estado_reg','varchar');
        $this->setParametro('pnr','pnr','varchar');
        $this->setParametro('nro_autorizacion','nro_autorizacion','varchar');
        $this->setParametro('moneda','moneda','varchar');
        $this->setParametro('importe_total','importe_total','numeric');
        $this->setParametro('fecha_emision','fecha_emision','date');
        $this->setParametro('estado_p','estado_p','varchar');
        $this->setParametro('forma_pago','forma_pago','varchar');
        $this->setParametro('medio_pago','medio_pago','varchar');
        $this->setParametro('instancia_pago','instancia_pago','varchar');
        $this->setParametro('office_id_emisor','office_id_emisor','varchar');
        $this->setParametro('pnr_prov','pnr_prov','varchar');
        $this->setParametro('nro_autorizacion_prov','nro_autorizacion_prov','varchar');
        $this->setParametro('office_id_emisor_prov','office_id_emisor_prov','varchar');
        $this->setParametro('importe_prov','importe_prov','numeric');
        $this->setParametro('moneda_prov','moneda_prov','varchar');
        $this->setParametro('estado_prov','estado_prov','varchar');
        $this->setParametro('fecha_autorizacion_prov','fecha_autorizacion_prov','date');
        $this->setParametro('tipo_error','tipo_error','varchar');
        $this->setParametro('tipo_validacion','tipo_validacion','varchar');
        $this->setParametro('prov_informacion','prov_informacion','varchar');
        //$this->setParametro('id_instancia_pago','id_instancia_pago','int4');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function modificarBoletosObservados(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='obingresos.ft_boletos_observados_ime';
        $this->transaccion='OBING_BOBS_MOD';
        $this->tipo_procedimiento='IME';

        //Define los parametros para la funcion
        $this->setParametro('id_boletos_observados','id_boletos_observados','int4');
        $this->setParametro('estado_reg','estado_reg','varchar');
        $this->setParametro('pnr','pnr','varchar');
        $this->setParametro('nro_autorizacion','nro_autorizacion','varchar');
        $this->setParametro('moneda','moneda','varchar');
        $this->setParametro('importe_total','importe_total','numeric');
        $this->setParametro('fecha_emision','fecha_emision','date');
        $this->setParametro('estado_p','estado_p','varchar');
        $this->setParametro('forma_pago','forma_pago','varchar');
        $this->setParametro('medio_pago','medio_pago','varchar');
        $this->setParametro('instancia_pago','instancia_pago','varchar');
        $this->setParametro('office_id_emisor','office_id_emisor','varchar');
        $this->setParametro('pnr_prov','pnr_prov','varchar');
        $this->setParametro('nro_autorizacion_prov','nro_autorizacion_prov','varchar');
        $this->setParametro('office_id_emisor_prov','office_id_emisor_prov','varchar');
        $this->setParametro('importe_prov','importe_prov','numeric');
        $this->setParametro('moneda_prov','moneda_prov','varchar');
        $this->setParametro('estado_prov','estado_prov','varchar');
        $this->setParametro('fecha_autorizacion_prov','fecha_autorizacion_prov','date');
        $this->setParametro('tipo_error','tipo_error','varchar');
        $this->setParametro('tipo_validacion','tipo_validacion','varchar');
        $this->setParametro('prov_informacion','prov_informacion','varchar');
        //$this->setParametro('id_instancia_pago','id_instancia_pago','int4');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function eliminarBoletosObservados(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='obingresos.ft_boletos_observados_ime';
        $this->transaccion='OBING_BOBS_ELI';
        $this->tipo_procedimiento='IME';

        //Define los parametros para la funcion
        $this->setParametro('id_boletos_observados','id_boletos_observados','int4');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

}
?>
