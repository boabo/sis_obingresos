<?php
/**
*@package pXP
*@file gen-MODDepositoBoleto.php
*@author  (jrivera)
*@date 06-01-2016 22:42:31
*@description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
*/

class MODDepositoBoleto extends MODbase{
	
	function __construct(CTParametro $pParam){
		parent::__construct($pParam);
	}
			
	function listarDepositoBoleto(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='obingresos.ft_deposito_boleto_sel';
		$this->transaccion='OBING_DEPBOL_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion
				
		//Definicion de la lista del resultado del query
		$this->captura('id_deposito_boleto','int4');
		$this->captura('id_boleto','int4');
		$this->captura('id_deposito','int4');
		$this->captura('tc','numeric');
		$this->captura('estado_reg','varchar');
		$this->captura('monto_moneda_boleto','numeric');
		$this->captura('monto_moneda_deposito','numeric');
		$this->captura('id_usuario_reg','int4');
		$this->captura('fecha_reg','timestamp');
		$this->captura('usuario_ai','varchar');
		$this->captura('id_usuario_ai','int4');
		$this->captura('fecha_mod','timestamp');
		$this->captura('id_usuario_mod','int4');
		$this->captura('usr_reg','varchar');
		$this->captura('usr_mod','varchar');
		
		$this->captura('boleto','varchar');
		$this->captura('moneda_boleto','varchar');
		$this->captura('deposito','varchar');
		$this->captura('moneda_deposito','varchar');
		
		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();
		
		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function insertarDepositoBoleto(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='obingresos.ft_deposito_boleto_ime';
		$this->transaccion='OBING_DEPBOL_INS';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_boleto','id_boleto','int4');
		$this->setParametro('id_deposito','id_deposito','int4');
		$this->setParametro('tc','tc','numeric');
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('monto_moneda_boleto','monto_moneda_boleto','numeric');
		$this->setParametro('monto_moneda_deposito','monto_moneda_deposito','numeric');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function modificarDepositoBoleto(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='obingresos.ft_deposito_boleto_ime';
		$this->transaccion='OBING_DEPBOL_MOD';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_deposito_boleto','id_deposito_boleto','int4');
		$this->setParametro('id_boleto','id_boleto','int4');
		$this->setParametro('id_deposito','id_deposito','int4');
		$this->setParametro('tc','tc','numeric');
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('monto_moneda_boleto','monto_moneda_boleto','numeric');
		$this->setParametro('monto_moneda_deposito','monto_moneda_deposito','numeric');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function eliminarDepositoBoleto(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='obingresos.ft_deposito_boleto_ime';
		$this->transaccion='OBING_DEPBOL_ELI';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_deposito_boleto','id_deposito_boleto','int4');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
}
?>