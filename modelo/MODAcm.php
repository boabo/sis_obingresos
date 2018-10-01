<?php
/**
*@package pXP
*@file gen-MODAcm.php
*@author  (jrivera)
*@date 05-09-2018 20:34:32
*@description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
*/

class MODAcm extends MODbase{

	function __construct(CTParametro $pParam){
		parent::__construct($pParam);
	}

	function listarAcm(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='obingresos.ft_acm_sel';
		$this->transaccion='OBING_acm_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion

		//Definicion de la lista del resultado del query
		$this->captura('id_acm','int4');
		$this->captura('id_moneda','int4');
		$this->captura('id_archivo_acm_det','int4');
		$this->captura('fecha','date');
		$this->captura('numero','varchar');
		$this->captura('ruta','varchar');
		$this->captura('estado_reg','varchar');
		$this->captura('importe','numeric');
		$this->captura('id_usuario_ai','int4');
		$this->captura('id_usuario_reg','int4');
		$this->captura('fecha_reg','timestamp');
		$this->captura('usuario_ai','varchar');
		$this->captura('id_usuario_mod','int4');
		$this->captura('fecha_mod','timestamp');
		$this->captura('usr_reg','varchar');
		$this->captura('usr_mod','varchar');
		$this->captura('desc_moneda','varchar');
		$this->captura('nombre','varchar');
		$this->captura('codigo','varchar');
		$this->captura('id_movimiento_entidad','int4');
		$this->captura('fecha_ini','date');
		$this->captura('fecha_fin','date');
		$this->captura('total_bsp','numeric');
		$this->captura('neto_total_mb','numeric');
		$this->captura('neto_total_mt','numeric');
		$this->captura('office_id','varchar');
		$this->captura('codigo_largo','varchar');




		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}

	function insertarAcm(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='obingresos.ft_acm_ime';
		$this->transaccion='OBING_acm_INS';
		$this->tipo_procedimiento='IME';

		//Define los parametros para la funcion
		$this->setParametro('id_moneda','id_moneda','int4');
		$this->setParametro('id_archivo_acm_det','id_archivo_acm_det','int4');
		$this->setParametro('fecha','fecha','date');
		$this->setParametro('numero','numero','varchar');
		$this->setParametro('ruta','ruta','varchar');
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('importe','importe','numeric');


		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}

	function generarACM(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='obingresos.ft_acm_ime';
		$this->transaccion='OBING_INSERTAR_INS';
		$this->tipo_procedimiento='IME';

		//Define los parametros para la funcion
		$this->setParametro('id_moneda','id_moneda','int4');
		$this->setParametro('id_archivo_acm_det','id_archivo_acm_det','int4');
		$this->setParametro('fecha','fecha','date');
		$this->setParametro('numero','numero','varchar');
		$this->setParametro('ruta','ruta','varchar');
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('importe','importe','numeric');
		$this->setParametro('id_archivo_acm','id_archivo_acm','varchar');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}

	function modificarAcm(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='obingresos.ft_acm_ime';
		$this->transaccion='OBING_acm_MOD';
		$this->tipo_procedimiento='IME';

		//Define los parametros para la funcion
		$this->setParametro('id_acm','id_acm','int4');
		$this->setParametro('id_moneda','id_moneda','int4');
		$this->setParametro('id_archivo_acm_det','id_archivo_acm_det','int4');
		$this->setParametro('fecha','fecha','date');
		$this->setParametro('numero','numero','varchar');
		$this->setParametro('ruta','ruta','varchar');
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('importe','importe','numeric');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}

	function eliminarAcm(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='obingresos.ft_acm_ime';
		$this->transaccion='OBING_acm_ELI';
		$this->tipo_procedimiento='IME';

		//Define los parametros para la funcion
		$this->setParametro('id_acm','id_acm','int4');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
	function eliminarAcmGenerado(){
			//Definicion de variables para ejecucion del procedimiento
			$this->procedimiento='obingresos.ft_acm_ime';
			$this->transaccion='OBING_ACM_LIMPIO_ELI';
			$this->tipo_procedimiento='IME';

			//Define los parametros para la funcion
			//$this->setParametro('id_archivo_acm_det','id_archivo_acm_det','int4');
			$this->setParametro('id_archivo_acm','id_archivo_acm','int4');

			//Ejecuta la instruccion
			$this->armarConsulta();
			$this->ejecutarConsulta();

			//Devuelve la respuesta
			return $this->respuesta;

	}

	function eliminarAcmValidado(){
			//Definicion de variables para ejecucion del procedimiento
			$this->procedimiento='obingresos.ft_movimiento_entidad_ime';
			$this->transaccion='OBING_VALILIMPIO_ELI';
			$this->tipo_procedimiento='IME';

			//Define los parametros para la funcion
			$this->setParametro('id_archivo_acm','id_archivo_acm','int4');

			//Ejecuta la instruccion
			$this->armarConsulta();
			$this->ejecutarConsulta();

			//Devuelve la respuesta
			return $this->respuesta;
	}
	function reporteGenACM(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='obingresos.ft_acm_sel';
		$this->transaccion='OBING_REPOR_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion
		//$this->setCount(false);
		//Define los parametros para la funcion
	  $this->setParametro('id_acm','id_acm','int4');
		$this->setParametro('numero','numero','varchar');
		$this->setParametro('comision','comision','numeric');
		$this->setParametro('neto','neto','numeric');
		$this->setParametro('porcentaje','porcentaje','int4');
		$this->setParametro('office_id','office_id','varchar');
		$this->setParametro('fecha_ini','fecha_ini','date');
		$this->setParametro('fecha_fin','fecha_fin','date');
		$this->setParametro('codigo','codigo','varchar');
		$this->setParametro('nombre','nombre','varchar');
		$this->setParametro('billete','billete','varchar');

		$this->setParametro('com_bsp','com_bsp','numeric');
		$this->setParametro('importe_total_mb','importe_total_mb','numeric');
		$this->setParametro('total_bsp','total_bsp','numeric');


		//Definicion de la lista del resultado del query
		$this->captura('id_acm','int4');
		$this->captura('id_moneda','int4');
		$this->captura('id_archivo_acm_det','int4');
		$this->captura('fecha','date');
		$this->captura('numero','varchar');
		$this->captura('ruta','varchar');
		$this->captura('estado_reg','varchar');
		$this->captura('importe','numeric');
		$this->captura('id_usuario_ai','int4');
		$this->captura('id_usuario_reg','int4');
		$this->captura('fecha_reg','timestamp');
		$this->captura('usuario_ai','varchar');
		$this->captura('id_usuario_mod','int4');
		$this->captura('fecha_mod','timestamp');
		$this->captura('id_movimiento_entidad','int4');

		$this->captura('comision','numeric');
		$this->captura('neto','numeric');
		$this->captura('porcentaje','int4');
		$this->captura('office_id','varchar');
		$this->captura('fecha_fin','date');
		$this->captura('fecha_ini','date');

		$this->captura('codigo','varchar');
		$this->captura('nombre','varchar');
		$this->captura('billete','varchar');

		$this->captura('com_bsp','numeric');
		$this->captura('neto_total_mb','numeric');
		$this->captura('total_bsp','numeric');
		$this->captura('td','varchar');




		//var_dump( $this->respuesta);exit;
		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}

}
?>
