<?php
/**
*@package pXP
*@file gen-MODSkybizArchivo.php
*@author  (admin)
*@date 15-02-2017 15:18:39
*@description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
*/

class MODSkybizArchivo extends MODbase{
	
	function __construct(CTParametro $pParam){
		parent::__construct($pParam);
	}
			
	function listarSkybizArchivo(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='obingresos.ft_skybiz_archivo_sel';
		$this->transaccion='OBING_SKYBIZ_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion
				
		//Definicion de la lista del resultado del query
		$this->captura('id_skybiz_archivo','int4');
		$this->captura('fecha','date');
		$this->captura('subido','varchar');
		$this->captura('comentario','varchar');
		$this->captura('estado_reg','varchar');
		$this->captura('nombre_archivo','varchar');
		$this->captura('id_usuario_ai','int4');
		$this->captura('usuario_ai','varchar');
		$this->captura('fecha_reg','timestamp');
		$this->captura('id_usuario_reg','int4');
		$this->captura('id_usuario_mod','int4');
		$this->captura('fecha_mod','timestamp');
		$this->captura('usr_reg','varchar');
		$this->captura('usr_mod','varchar');
		$this->captura('moneda','varchar');
		$this->captura('banco','varchar');
		$this->captura('total','numeric');

		//Ejecuta la instruccion
		$this->armarConsulta();

		$this->ejecutarConsulta();

		
		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function insertarSkybizArchivo(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='obingresos.ft_skybiz_archivo_ime';
		$this->transaccion='OBING_SKYBIZ_INS';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('fecha','fecha','date');
		$this->setParametro('subido','subido','varchar');
		$this->setParametro('comentario','comentario','varchar');
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('nombre_archivo','nombre_archivo','varchar');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function modificarSkybizArchivo(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='obingresos.ft_skybiz_archivo_ime';
		$this->transaccion='OBING_SKYBIZ_MOD';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_skybiz_archivo','id_skybiz_archivo','int4');
		$this->setParametro('fecha','fecha','date');
		$this->setParametro('subido','subido','varchar');
		$this->setParametro('comentario','comentario','varchar');
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('nombre_archivo','nombre_archivo','varchar');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function eliminarSkybizArchivo(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='obingresos.ft_skybiz_archivo_ime';
		$this->transaccion='OBING_SKYBIZ_ELI';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_skybiz_archivo','id_skybiz_archivo','int4');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
	function insertarSkybizArchivoJson(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='obingresos.ft_skybiz_archivo_ime';
		$this->transaccion='OBING_SKYBIZ_JSON';
		$this->tipo_procedimiento='IME';

		//Define los parametros para la funcion
		$this->setParametro('arra_json','arra_json','text');
		$this->setParametro('fecha','fecha','date');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
}
?>