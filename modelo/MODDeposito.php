<?php
/**
*@package pXP
*@file gen-MODDeposito.php
*@author  (jrivera)
*@date 06-01-2016 22:42:28
*@description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
*/

class MODDeposito extends MODbase{
	
	function __construct(CTParametro $pParam){
		parent::__construct($pParam);
	}
			
	function listarDeposito(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='obingresos.ft_deposito_sel';
		$this->transaccion='OBING_DEP_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion
				
		//Definicion de la lista del resultado del query
		$this->captura('id_deposito','int4');
		$this->captura('estado_reg','varchar');
		$this->captura('nro_deposito','varchar');
		$this->captura('monto_deposito','numeric');
		$this->captura('id_moneda_deposito','int4');
		$this->captura('id_agencia','int4');
		$this->captura('fecha','date');
		$this->captura('saldo','numeric');
		$this->captura('id_usuario_reg','int4');
		$this->captura('fecha_reg','timestamp');
		$this->captura('id_usuario_ai','int4');
		$this->captura('usuario_ai','varchar');
		$this->captura('id_usuario_mod','int4');
		$this->captura('fecha_mod','timestamp');
		$this->captura('usr_reg','varchar');
		$this->captura('usr_mod','varchar');
		$this->captura('desc_moneda','varchar');
		
		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();
		
		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function insertarDeposito(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='obingresos.ft_deposito_ime';
		$this->transaccion='OBING_DEP_INS';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('nro_deposito','nro_deposito','varchar');
		$this->setParametro('monto_deposito','monto_deposito','numeric');
		$this->setParametro('id_moneda_deposito','id_moneda_deposito','int4');
		$this->setParametro('id_agencia','id_agencia','int4');
		$this->setParametro('fecha','fecha','date');
		$this->setParametro('saldo','saldo','numeric');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function modificarDeposito(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='obingresos.ft_deposito_ime';
		$this->transaccion='OBING_DEP_MOD';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_deposito','id_deposito','int4');
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('nro_deposito','nro_deposito','varchar');
		$this->setParametro('monto_deposito','monto_deposito','numeric');
		$this->setParametro('id_moneda_deposito','id_moneda_deposito','int4');
		$this->setParametro('id_agencia','id_agencia','int4');
		$this->setParametro('fecha','fecha','date');
		$this->setParametro('saldo','saldo','numeric');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function eliminarDeposito(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='obingresos.ft_deposito_ime';
		$this->transaccion='OBING_DEP_ELI';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_deposito','id_deposito','int4');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
}
?>