<?php
/**
*@package pXP
*@file gen-MODBoleto.php
*@author  (jrivera)
*@date 06-01-2016 22:42:25
*@description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
*/

class MODBoleto extends MODbase{
	
	function __construct(CTParametro $pParam){
		parent::__construct($pParam);
	}
			
	function listarBoleto(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='obingresos.ft_boleto_sel';
		$this->transaccion='OBING_BOL_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion
				
		//Definicion de la lista del resultado del query
		$this->captura('id_boleto','int4');
		$this->captura('id_agencia','int4');
		$this->captura('id_moneda_boleto','int4');
		$this->captura('estado_reg','varchar');
		$this->captura('comision','numeric');
		$this->captura('fecha_emision','date');
		$this->captura('total','numeric');
		$this->captura('pasajero','varchar');
		$this->captura('monto_pagado_moneda_boleto','numeric');
		$this->captura('liquido','numeric');
		$this->captura('nro_boleto','varchar');
		$this->captura('id_usuario_reg','int4');
		$this->captura('fecha_reg','timestamp');
		$this->captura('usuario_ai','varchar');
		$this->captura('id_usuario_ai','int4');
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
			
	function insertarBoleto(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='obingresos.ft_boleto_ime';
		$this->transaccion='OBING_BOL_INS';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_agencia','id_agencia','int4');
		$this->setParametro('id_moneda_boleto','id_moneda_boleto','int4');
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('comision','comision','numeric');
		$this->setParametro('fecha_emision','fecha_emision','date');
		$this->setParametro('total','total','numeric');
		$this->setParametro('pasajero','pasajero','varchar');
		$this->setParametro('monto_pagado_moneda_boleto','monto_pagado_moneda_boleto','numeric');
		$this->setParametro('liquido','liquido','numeric');
		$this->setParametro('nro_boleto','nro_boleto','varchar');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function modificarBoleto(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='obingresos.ft_boleto_ime';
		$this->transaccion='OBING_BOL_MOD';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_boleto','id_boleto','int4');
		$this->setParametro('id_agencia','id_agencia','int4');
		$this->setParametro('id_moneda_boleto','id_moneda_boleto','int4');
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('comision','comision','numeric');
		$this->setParametro('fecha_emision','fecha_emision','date');
		$this->setParametro('total','total','numeric');
		$this->setParametro('pasajero','pasajero','varchar');
		$this->setParametro('monto_pagado_moneda_boleto','monto_pagado_moneda_boleto','numeric');
		$this->setParametro('liquido','liquido','numeric');
		$this->setParametro('nro_boleto','nro_boleto','varchar');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function eliminarBoleto(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='obingresos.ft_boleto_ime';
		$this->transaccion='OBING_BOL_ELI';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_boleto','id_boleto','int4');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
}
?>