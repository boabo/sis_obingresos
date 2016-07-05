<?php
/**
*@package pXP
*@file gen-MODBoletoImpuesto.php
*@author  (jrivera)
*@date 13-06-2016 20:42:17
*@description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
*/

class MODBoletoImpuesto extends MODbase{
	
	function __construct(CTParametro $pParam){
		parent::__construct($pParam);
	}
			
	function listarBoletoImpuesto(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='obingresos.ft_boleto_impuesto_sel';
		$this->transaccion='OBING_BIT_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion
				
		//Definicion de la lista del resultado del query
		$this->captura('id_boleto_impuesto','int4');
		$this->captura('importe','numeric');
		$this->captura('id_impuesto','int4');
		$this->captura('id_boleto','int4');
		$this->captura('estado_reg','varchar');
		$this->captura('id_usuario_ai','int4');
		$this->captura('usuario_ai','varchar');
		$this->captura('fecha_reg','timestamp');
		$this->captura('id_usuario_reg','int4');
		$this->captura('fecha_mod','timestamp');
		$this->captura('id_usuario_mod','int4');
		$this->captura('usr_reg','varchar');
		$this->captura('usr_mod','varchar');
		$this->captura('codigo_impuesto','varchar');
		$this->captura('nombre_impuesto','varchar');
		
		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();
		
		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function insertarBoletoImpuesto(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='obingresos.ft_boleto_impuesto_ime';
		$this->transaccion='OBING_BIT_INS';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('importe','importe','numeric');
		$this->setParametro('id_impuesto','id_impuesto','int4');
		$this->setParametro('id_boleto','id_boleto','int4');
		$this->setParametro('estado_reg','estado_reg','varchar');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function modificarBoletoImpuesto(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='obingresos.ft_boleto_impuesto_ime';
		$this->transaccion='OBING_BIT_MOD';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_boleto_impuesto','id_boleto_impuesto','int4');
		$this->setParametro('importe','importe','numeric');
		$this->setParametro('id_impuesto','id_impuesto','int4');
		$this->setParametro('id_boleto','id_boleto','int4');
		$this->setParametro('estado_reg','estado_reg','varchar');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function eliminarBoletoImpuesto(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='obingresos.ft_boleto_impuesto_ime';
		$this->transaccion='OBING_BIT_ELI';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_boleto_impuesto','id_boleto_impuesto','int4');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
}
?>