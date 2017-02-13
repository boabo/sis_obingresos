<?php
/**
*@package pXP
*@file gen-MODAgencia.php
*@author  (jrivera)
*@date 06-01-2016 21:30:12
*@description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
*/

class MODAgencia extends MODbase{
	
	function __construct(CTParametro $pParam){
		parent::__construct($pParam);
	}
			
	function listarAgencia(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='obingresos.ft_agencia_sel';
		$this->transaccion='OBING_AGE_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion
				
		//Definicion de la lista del resultado del query
		$this->captura('id_agencia','int4');
		$this->captura('id_moneda_control','int4');
		$this->captura('depositos_moneda_boleto','varchar');
		$this->captura('tipo_pago','varchar');
		$this->captura('nombre','varchar');
		$this->captura('monto_maximo_deuda','numeric');
		$this->captura('tipo_cambio','varchar');
		$this->captura('codigo_int','varchar');
		$this->captura('codigo','varchar');
		$this->captura('codigo_noiata','varchar');
		$this->captura('tipo_agencia','varchar');
		$this->captura('usr_reg','varchar');
		$this->captura('usr_mod','varchar');
		$this->captura('desc_moneda','varchar');
		
		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function insertarAgencia(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='obingresos.ft_agencia_ime';
		$this->transaccion='OBING_AGE_INS';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_moneda_control','id_moneda_control','int4');
		$this->setParametro('depositos_moneda_boleto','depositos_moneda_boleto','varchar');
		$this->setParametro('tipo_pago','tipo_pago','varchar');
		$this->setParametro('nombre','nombre','varchar');
		$this->setParametro('monto_maximo_deuda','monto_maximo_deuda','numeric');
		$this->setParametro('tipo_cambio','tipo_cambio','varchar');
		$this->setParametro('codigo_int','codigo_int','varchar');
		$this->setParametro('codigo','codigo','varchar');
		$this->setParametro('tipo_agencia','tipo_agencia','varchar');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function modificarAgencia(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='obingresos.ft_agencia_ime';
		$this->transaccion='OBING_AGE_MOD';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_agencia','id_agencia','int4');
		$this->setParametro('id_moneda_control','id_moneda_control','int4');
		$this->setParametro('depositos_moneda_boleto','depositos_moneda_boleto','varchar');
		$this->setParametro('tipo_pago','tipo_pago','varchar');
		$this->setParametro('nombre','nombre','varchar');
		$this->setParametro('monto_maximo_deuda','monto_maximo_deuda','numeric');
		$this->setParametro('tipo_cambio','tipo_cambio','varchar');
		$this->setParametro('codigo_int','codigo_int','varchar');
		$this->setParametro('codigo','codigo','varchar');
		$this->setParametro('tipo_agencia','tipo_agencia','varchar');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function eliminarAgencia(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='obingresos.ft_agencia_ime';
		$this->transaccion='OBING_AGE_ELI';
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