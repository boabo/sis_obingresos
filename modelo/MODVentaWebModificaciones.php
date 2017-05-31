<?php
/**
*@package pXP
*@file gen-MODVentaWebModificaciones.php
*@author  (jrivera)
*@date 11-01-2017 19:44:28
*@description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
*/

class MODVentaWebModificaciones extends MODbase{
	
	function __construct(CTParametro $pParam){
		parent::__construct($pParam);
	}
			
	function listarVentaWebModificaciones(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='obingresos.ft_venta_web_modificaciones_sel';
		$this->transaccion='OBING_VWEBMOD_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion
				
		//Definicion de la lista del resultado del query
		$this->captura('id_venta_web_modificaciones','int4');
		$this->captura('nro_boleto','varchar');
		$this->captura('tipo','varchar');
		$this->captura('motivo','text');
		$this->captura('nro_boleto_reemision','varchar');
		$this->captura('used','varchar');
		$this->captura('estado_reg','varchar');
		$this->captura('id_usuario_ai','int4');
		$this->captura('usuario_ai','varchar');
		$this->captura('fecha_reg','timestamp');
		$this->captura('id_usuario_reg','int4');
		$this->captura('fecha_mod','timestamp');
		$this->captura('id_usuario_mod','int4');
		$this->captura('usr_reg','varchar');
		$this->captura('usr_mod','varchar');
        $this->captura('procesado','varchar');
        $this->captura('anulado','varchar');
        $this->captura('pnr_antiguo','varchar');
        $this->captura('fecha_reserva_antigua','date');
        $this->captura('banco','varchar');
		
		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();
		
		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function insertarVentaWebModificaciones(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='obingresos.ft_venta_web_modificaciones_ime';
		$this->transaccion='OBING_VWEBMOD_INS';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('nro_boleto','nro_boleto','varchar');
		$this->setParametro('tipo','tipo','varchar');
		$this->setParametro('motivo','motivo','text');
		$this->setParametro('nro_boleto_reemision','nro_boleto_reemision','varchar');
		$this->setParametro('used','used','varchar');
		$this->setParametro('estado_reg','estado_reg','varchar');
        $this->setParametro('pnr_antiguo','pnr_antiguo','varchar');
        $this->setParametro('fecha_reserva_antigua','fecha_reserva_antigua','date');
        $this->setParametro('banco','banco','varchar');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function modificarVentaWebModificaciones(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='obingresos.ft_venta_web_modificaciones_ime';
		$this->transaccion='OBING_VWEBMOD_MOD';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_venta_web_modificaciones','id_venta_web_modificaciones','int4');
		$this->setParametro('nro_boleto','nro_boleto','varchar');
		$this->setParametro('tipo','tipo','varchar');
		$this->setParametro('motivo','motivo','text');
		$this->setParametro('nro_boleto_reemision','nro_boleto_reemision','varchar');
		$this->setParametro('used','used','varchar');
		$this->setParametro('estado_reg','estado_reg','varchar');
        $this->setParametro('pnr_antiguo','pnr_antiguo','varchar');
        $this->setParametro('fecha_reserva_antigua','fecha_reserva_antigua','date');
        $this->setParametro('banco','banco','varchar');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function eliminarVentaWebModificaciones(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='obingresos.ft_venta_web_modificaciones_ime';
		$this->transaccion='OBING_VWEBMOD_ELI';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_venta_web_modificaciones','id_venta_web_modificaciones','int4');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
}
?>