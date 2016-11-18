<?php
/**
*@package pXP
*@file gen-MODFormaPago.php
*@author  (jrivera)
*@date 10-06-2016 20:37:45
*@description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
*/

class MODFormaPago extends MODbase{
	
	function __construct(CTParametro $pParam){
		parent::__construct($pParam);
	}
			
	function listarFormaPago(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='obingresos.ft_forma_pago_sel';
		$this->transaccion='OBING_FOP_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion
				
		//Definicion de la lista del resultado del query
		$this->captura('id_forma_pago','int4');		
		$this->captura('codigo','varchar');		
		$this->captura('nombre','varchar');
		$this->captura('moneda','varchar');
		$this->captura('pais','varchar');
		$this->captura('forma_pago','varchar');
				
		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();
		
		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function insertarFormaPago(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='obingresos.ft_forma_pago_ime';
		$this->transaccion='OBING_FOP_INS';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_entidad','id_entidad','int4');
		$this->setParametro('id_moneda','id_moneda','int4');
		$this->setParametro('codigo','codigo','varchar');
		$this->setParametro('registrar_tarjeta','registrar_tarjeta','varchar');
		$this->setParametro('defecto','defecto','varchar');
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('registrar_cc','registrar_cc','varchar');
		$this->setParametro('nombre','nombre','varchar');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function modificarFormaPago(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='obingresos.ft_forma_pago_ime';
		$this->transaccion='OBING_FOP_MOD';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_forma_pago','id_forma_pago','int4');
		$this->setParametro('id_entidad','id_entidad','int4');
		$this->setParametro('id_moneda','id_moneda','int4');
		$this->setParametro('codigo','codigo','varchar');
		$this->setParametro('registrar_tarjeta','registrar_tarjeta','varchar');
		$this->setParametro('defecto','defecto','varchar');
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('registrar_cc','registrar_cc','varchar');
		$this->setParametro('nombre','nombre','varchar');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function eliminarFormaPago(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='obingresos.ft_forma_pago_ime';
		$this->transaccion='OBING_FOP_ELI';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_forma_pago','id_forma_pago','int4');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
}
?>