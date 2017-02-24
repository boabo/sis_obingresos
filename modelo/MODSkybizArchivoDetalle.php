<?php
/**
*@package pXP
*@file gen-MODSkybizArchivoDetalle.php
*@author  (admin)
*@date 15-02-2017 19:08:58
*@description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
*/

class MODSkybizArchivoDetalle extends MODbase{
	
	function __construct(CTParametro $pParam){
		parent::__construct($pParam);
	}
			
	function listarSkybizArchivoDetalle(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='obingresos.ft_skybiz_archivo_detalle_sel';
		$this->transaccion='OBING_SKYDET_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion
				
		//Definicion de la lista del resultado del query
		$this->captura('id_skybiz_archivo_detalle','int4');
		$this->captura('entity','varchar');
		$this->captura('request_date_time','varchar');
		$this->captura('currency','varchar');
		$this->captura('total_amount','numeric');
		$this->captura('ip','varchar');
		$this->captura('status','varchar');
		$this->captura('estado_reg','varchar');
		$this->captura('issue_date_time','varchar');
		$this->captura('identifier_pnr','varchar');
		$this->captura('id_skybiz_archivo','int4');
		$this->captura('pnr','varchar');
		$this->captura('authorization_','varchar');
		$this->captura('id_usuario_ai','int4');
		$this->captura('usuario_ai','varchar');
		$this->captura('fecha_reg','timestamp');
		$this->captura('id_usuario_reg','int4');
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
			
	function insertarSkybizArchivoDetalle(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='obingresos.ft_skybiz_archivo_detalle_ime';
		$this->transaccion='OBING_SKYDET_INS';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('entity','entity','varchar');
		$this->setParametro('request_date_time','request_date_time','varchar');
		$this->setParametro('currency','currency','varchar');
		$this->setParametro('total_amount','total_amount','numeric');
		$this->setParametro('ip','ip','varchar');
		$this->setParametro('status','status','varchar');
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('issue_date_time','issue_date_time','varchar');
		$this->setParametro('identifier_pnr','identifier_pnr','varchar');
		$this->setParametro('id_skybiz_archivo','id_skybiz_archivo','int4');
		$this->setParametro('pnr','pnr','varchar');
		$this->setParametro('authorization_','authorization_','varchar');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function modificarSkybizArchivoDetalle(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='obingresos.ft_skybiz_archivo_detalle_ime';
		$this->transaccion='OBING_SKYDET_MOD';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_skybiz_archivo_detalle','id_skybiz_archivo_detalle','int4');
		$this->setParametro('entity','entity','varchar');
		$this->setParametro('request_date_time','request_date_time','varchar');
		$this->setParametro('currency','currency','varchar');
		$this->setParametro('total_amount','total_amount','numeric');
		$this->setParametro('ip','ip','varchar');
		$this->setParametro('status','status','varchar');
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('issue_date_time','issue_date_time','varchar');
		$this->setParametro('identifier_pnr','identifier_pnr','varchar');
		$this->setParametro('id_skybiz_archivo','id_skybiz_archivo','int4');
		$this->setParametro('pnr','pnr','varchar');
		$this->setParametro('authorization_','authorization_','varchar');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function eliminarSkybizArchivoDetalle(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='obingresos.ft_skybiz_archivo_detalle_ime';
		$this->transaccion='OBING_SKYDET_ELI';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_skybiz_archivo_detalle','id_skybiz_archivo_detalle','int4');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
	function insertarSkybizArchivoDetalleJson(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='obingresos.ft_skybiz_archivo_detalle_ime';
		$this->transaccion='OBING_SKYDET_JSON';
		$this->tipo_procedimiento='IME';

		//Define los parametros para la funcion
		$this->setParametro('arra_json','arra_json','text');
		$this->setParametro('nombre_archivo','nombre_archivo','varchar');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
}
?>