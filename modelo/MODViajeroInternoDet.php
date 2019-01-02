<?php
/**
*@package pXP
*@file gen-MODViajeroInternoDet.php
*@author  (rzabala)
*@date 21-12-2018 14:21:07
*@description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
*/

class MODViajeroInternoDet extends MODbase{
	
	function __construct(CTParametro $pParam){
		parent::__construct($pParam);
	}
			
	function listarViajeroInternoDet(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='obingresos.ft_viajero_interno_det_sel';
		$this->transaccion='OBING_DVI_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion
				
		//Definicion de la lista del resultado del query
		$this->captura('id_viajero_interno_det','int4');
		$this->captura('estado_reg','varchar');
		$this->captura('nombre','varchar');
		$this->captura('pnr','varchar');
		$this->captura('num_boleto','varchar');
		$this->captura('id_usuario_reg','int4');
		$this->captura('fecha_reg','timestamp');
		$this->captura('id_usuario_ai','int4');
		$this->captura('usuario_ai','varchar');
		$this->captura('id_usuario_mod','int4');
		$this->captura('fecha_mod','timestamp');
		$this->captura('usr_reg','varchar');
		$this->captura('usr_mod','varchar');
        $this->captura('id_viajero_interno','int4');
        $this->captura('solicitud','varchar');
        $this->captura('num_documento','int');
        $this->captura('estado_voucher','varchar');
        $this->captura('tarifa','varchar');
		
		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();
		
		//Devuelve la respuesta
		return $this->respuesta;
		var_dump($this->respuesta);exit;
	}
			
	function insertarViajeroInternoDet(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='obingresos.ft_viajero_interno_det_ime';
		$this->transaccion='OBING_DVI_INS';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('nombre','nombre','varchar');
		$this->setParametro('pnr','pnr','varchar');
		$this->setParametro('num_boleto','num_boleto','varchar');
        $this->setParametro('id_viajero_interno','id_viajero_interno','int4');
        $this->setParametro('solicitud','solicitud','varchar');
        $this->setParametro('num_documento','num_documento','int');
        $this->setParametro('estado_voucher','estado_voucher','varchar');
        $this->setParametro('tarifa','tarifa','varchar');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function modificarViajeroInternoDet(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='obingresos.ft_viajero_interno_det_ime';
		$this->transaccion='OBING_DVI_MOD';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_viajero_interno_det','id_viajero_interno_det','int4');
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('nombre','nombre','varchar');
		$this->setParametro('pnr','pnr','varchar');
		$this->setParametro('num_boleto','num_boleto','varchar');
        $this->setParametro('id_viajero_interno','id_viajero_interno','int4');
        $this->setParametro('solicitud','solicitud','varchar');
        $this->setParametro('num_documento','num_documento','int');
        $this->setParametro('estado_voucher','estado_voucher','varchar');
        $this->setParametro('tarifa','tarifa','varchar');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
    function actualizarViajeroInternoDet(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='obingresos.ft_viajero_interno_det_ime';
        $this->transaccion='OBING_DVI_ACT';
        $this->tipo_procedimiento='IME';

        //Define los parametros para la funcion
        $this->setParametro('request','request','jsonb');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //var_dump($res);exit;
        //Devuelve la respuesta
        return $this->respuesta;
        //var_dump($this->respuesta);exit;
    }
			
	function eliminarViajeroInternoDet(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='obingresos.ft_viajero_interno_det_ime';
		$this->transaccion='OBING_DVI_ELI';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_viajero_interno_det','id_viajero_interno_det','int4');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
}
?>