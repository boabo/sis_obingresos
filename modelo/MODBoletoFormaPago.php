<?php
/**
*@package pXP
*@file gen-MODBoletoFormaPago.php
*@author  (jrivera)
*@date 13-06-2016 20:42:15
*@description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
*/

class MODBoletoFormaPago extends MODbase{
	
	function __construct(CTParametro $pParam){
		parent::__construct($pParam);
	}
			
	function listarBoletoFormaPago(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='obingresos.ft_boleto_forma_pago_sel';
		$this->transaccion='OBING_BFP_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion
				
		//Definicion de la lista del resultado del query
		$this->captura('id_boleto_forma_pago','int4');
		$this->captura('tipo','varchar');
		$this->captura('id_forma_pago','int4');
		$this->captura('id_boleto','int4');
		$this->captura('estado_reg','varchar');
		$this->captura('tarjeta','varchar');
		$this->captura('ctacte','varchar');
		$this->captura('importe','numeric');
		$this->captura('numero_tarjeta','varchar');
		$this->captura('id_usuario_ai','int4');
		$this->captura('id_usuario_reg','int4');
		$this->captura('usuario_ai','varchar');
		$this->captura('fecha_reg','timestamp');
		$this->captura('id_usuario_mod','int4');
		$this->captura('fecha_mod','timestamp');
		$this->captura('usr_reg','varchar');
		$this->captura('usr_mod','varchar');
		$this->captura('forma_pago','varchar');
		$this->captura('codigo_forma_pago','varchar');
		$this->captura('moneda','varchar');
		
		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();
		
		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function insertarBoletoFormaPago(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='obingresos.ft_boleto_forma_pago_ime';
		$this->transaccion='OBING_BFP_INS';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('tipo','tipo','varchar');
		$this->setParametro('id_forma_pago','id_forma_pago','int4');
		$this->setParametro('id_boleto','id_boleto','int4');
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('tarjeta','tarjeta','varchar');
		$this->setParametro('ctacte','ctacte','varchar');
		$this->setParametro('importe','importe','numeric');
		$this->setParametro('numero_tarjeta','numero_tarjeta','varchar');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function modificarBoletoFormaPago(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='obingresos.ft_boleto_forma_pago_ime';
		$this->transaccion='OBING_BFP_MOD';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_boleto_forma_pago','id_boleto_forma_pago','int4');
		$this->setParametro('tipo','tipo','varchar');
		$this->setParametro('id_forma_pago','id_forma_pago','int4');
		$this->setParametro('id_boleto','id_boleto','int4');
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('tarjeta','tarjeta','varchar');
		$this->setParametro('ctacte','ctacte','varchar');
		$this->setParametro('importe','importe','numeric');
		$this->setParametro('numero_tarjeta','numero_tarjeta','varchar');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function eliminarBoletoFormaPago(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='obingresos.ft_boleto_forma_pago_ime';
		$this->transaccion='OBING_BFP_ELI';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_boleto_forma_pago','id_boleto_forma_pago','int4');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
}
?>