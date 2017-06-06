<?php
/**
*@package pXP
*@file gen-MODObservacionesConciliacion.php
*@author  (jrivera)
*@date 01-06-2017 21:16:45
*@description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
*/

class MODObservacionesConciliacion extends MODbase{
	
	function __construct(CTParametro $pParam){
		parent::__construct($pParam);
	}
			
	function listarObservacionesConciliacion(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='obingresos.ft_observaciones_conciliacion_sel';
		$this->transaccion='OBING_OBC_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion
				
		//Definicion de la lista del resultado del query
		$this->captura('id_observaciones_conciliacion','int4');
		$this->captura('tipo_observacion','varchar');
		$this->captura('estado_reg','varchar');
		$this->captura('fecha_observacion','date');
		$this->captura('banco','varchar');
		$this->captura('observacion','text');
		$this->captura('id_usuario_reg','int4');
		$this->captura('fecha_reg','timestamp');
		$this->captura('usuario_ai','varchar');
		$this->captura('id_usuario_ai','int4');
		$this->captura('fecha_mod','timestamp');
		$this->captura('id_usuario_mod','int4');
		$this->captura('usr_reg','varchar');
		$this->captura('usr_mod','varchar');
		
		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();
		
		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function insertarObservacionesConciliacion(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='obingresos.ft_observaciones_conciliacion_ime';
		$this->transaccion='OBING_OBC_INS';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('tipo_observacion','tipo_observacion','varchar');
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('fecha_observacion','fecha_observacion','date');
		$this->setParametro('banco','banco','varchar');
		$this->setParametro('observacion','observacion','text');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function modificarObservacionesConciliacion(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='obingresos.ft_observaciones_conciliacion_ime';
		$this->transaccion='OBING_OBC_MOD';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_observaciones_conciliacion','id_observaciones_conciliacion','int4');
		$this->setParametro('tipo_observacion','tipo_observacion','varchar');
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('fecha_observacion','fecha_observacion','date');
		$this->setParametro('banco','banco','varchar');
		$this->setParametro('observacion','observacion','text');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function eliminarObservacionesConciliacion(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='obingresos.ft_observaciones_conciliacion_ime';
		$this->transaccion='OBING_OBC_ELI';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_observaciones_conciliacion','id_observaciones_conciliacion','int4');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
}
?>