<?php
/**
*@package pXP
*@file gen-MODAcmDet.php
*@author  (jrivera)
*@date 05-09-2018 20:52:05
*@description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
*/

class MODAcmDet extends MODbase{

	function __construct(CTParametro $pParam){
		parent::__construct($pParam);
	}

	function listarAcmDet(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='obingresos.ft_acm_det_sel';
		$this->transaccion='OBING_ACMDET_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion

		$this->capturaCount('total_over_comision','numeric');
		$this->capturaCount('total_neto','numeric');
		$this->capturaCount('total_bsp','numeric');
		//Definicion de la lista del resultado del query
		$this->captura('id_acm_det','int4');
		$this->captura('id_acm','int4');
		$this->captura('id_detalle_boletos_web','int4');
		$this->captura('neto','numeric');
		$this->captura('over_comision','numeric');
		$this->captura('estado_reg','varchar');
		$this->captura('id_usuario_ai','int4');
		$this->captura('usuario_ai','varchar');
		$this->captura('fecha_reg','timestamp');
		$this->captura('id_usuario_reg','int4');
		$this->captura('id_usuario_mod','int4');
		$this->captura('fecha_mod','timestamp');
		$this->captura('com_bsp','numeric');
		$this->captura('moneda','varchar');
		$this->captura('td','varchar');
		$this->captura('porcentaje_over','int4');
		$this->captura('usr_reg','varchar');
		$this->captura('usr_mod','varchar');
		$this->captura('billete','varchar');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}

	function insertarAcmDet(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='obingresos.ft_acm_det_ime';
		$this->transaccion='OBING_ACMDET_INS';
		$this->tipo_procedimiento='IME';

		//Define los parametros para la funcion
		$this->setParametro('id_acm','id_acm','int4');
		$this->setParametro('id_detalle_boletos_web','id_detalle_boletos_web','int4');
		$this->setParametro('neto','neto','numeric');
		$this->setParametro('over_comision','over_comision','numeric');
		$this->setParametro('estado_reg','estado_reg','varchar');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}

	function modificarAcmDet(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='obingresos.ft_acm_det_ime';
		$this->transaccion='OBING_ACMDET_MOD';
		$this->tipo_procedimiento='IME';

		//Define los parametros para la funcion
		$this->setParametro('id_acm_det','id_acm_det','int4');
		$this->setParametro('id_acm','id_acm','int4');
		$this->setParametro('id_detalle_boletos_web','id_detalle_boletos_web','int4');
		$this->setParametro('neto','neto','numeric');
		$this->setParametro('over_comision','over_comision','numeric');
		$this->setParametro('estado_reg','estado_reg','varchar');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}

	function eliminarAcmDet(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='obingresos.ft_acm_det_ime';
		$this->transaccion='OBING_ACMDET_ELI';
		$this->tipo_procedimiento='IME';

		//Define los parametros para la funcion
		$this->setParametro('id_acm_det','id_acm_det','int4');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}

}
?>
