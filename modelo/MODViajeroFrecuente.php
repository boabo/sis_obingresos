<?php
/**
*@package pXP
*@file gen-MODViajeroFrecuente.php
*@author  (miguel.mamani)
*@date 12-12-2017 19:32:55
*@description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
*/

class MODViajeroFrecuente extends MODbase{
	
	function __construct(CTParametro $pParam){
		parent::__construct($pParam);
	}
			
	function listarViajeroFrecuente(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='obingresos.ft_viajero_frecuente_sel';
		$this->transaccion='OBING_VFB_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion
				
		//Definicion de la lista del resultado del query
		$this->captura('id_viajero_frecuente','int4');
		$this->captura('nombre_completo','varchar');
		$this->captura('voucher_code','varchar');
		$this->captura('estado_reg','varchar');
		$this->captura('pnr','varchar');
		$this->captura('status','varchar');
		$this->captura('ffid','varchar');
		$this->captura('ticket_number','varchar');
		$this->captura('mensaje','varchar');
		$this->captura('id_pasajero_frecuente','int4');
		$this->captura('id_boleto_amadeus','int4');
		$this->captura('id_usuario_reg','int4');
		$this->captura('fecha_reg','timestamp');
		$this->captura('usuario_ai','varchar');
		$this->captura('id_usuario_ai','int4');
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
			
	function insertarViajeroFrecuente(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='obingresos.ft_viajero_frecuente_ime';
		$this->transaccion='OBING_VFB_INS';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('nombre_completo','nombre_completo','varchar');
		$this->setParametro('voucher_code','voucher_code','varchar');
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('pnr','pnr','varchar');
		$this->setParametro('status','status','varchar');
		$this->setParametro('ffid','ffid','varchar');
		$this->setParametro('ticket_number','ticket_number','varchar');
		$this->setParametro('mensaje','mensaje','varchar');
		$this->setParametro('id_pasajero_frecuente','id_pasajero_frecuente','int4');
		$this->setParametro('id_boleto_amadeus','id_boleto_amadeus','int4');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function modificarViajeroFrecuente(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='obingresos.ft_viajero_frecuente_ime';
		$this->transaccion='OBING_VFB_MOD';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_viajero_frecuente','id_viajero_frecuente','int4');
		$this->setParametro('nombre_completo','nombre_completo','varchar');
		$this->setParametro('voucher_code','voucher_code','varchar');
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('pnr','pnr','varchar');
		$this->setParametro('status','status','varchar');
		$this->setParametro('ffid','ffid','varchar');
		$this->setParametro('ticket_number','ticket_number','varchar');
		$this->setParametro('mensaje','mensaje','varchar');
		$this->setParametro('id_pasajero_frecuente','id_pasajero_frecuente','int4');
		$this->setParametro('id_boleto_amadeus','id_boleto_amadeus','int4');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function eliminarViajeroFrecuente(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='obingresos.ft_viajero_frecuente_ime';
		$this->transaccion='OBING_VFB_ELI';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_viajero_frecuente','id_viajero_frecuente','int4');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
}
?>