<?php
/**
*@package pXP
*@file gen-MODPeriodoVenta.php
*@author  (jrivera)
*@date 08-04-2016 22:44:37
*@description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
*/

class MODPeriodoVenta extends MODbase{
	
	function __construct(CTParametro $pParam){
		parent::__construct($pParam);
	}
			
	function listarPeriodoVenta(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='obingresos.ft_periodo_venta_sel';
		$this->transaccion='OBING_PERVEN_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion
				
		//Definicion de la lista del resultado del query
		$this->captura('id_periodo_venta','int4');
		$this->captura('id_pais','int4');
		$this->captura('id_gestion','int4');
		$this->captura('mes','varchar');
		$this->captura('estado','varchar');
		$this->captura('nro_periodo_mes','int4');
		$this->captura('fecha_fin','date');
		$this->captura('fecha_ini','date');
		$this->captura('tipo','varchar');
		$this->captura('estado_reg','varchar');
		$this->captura('id_usuario_ai','int4');
		$this->captura('id_usuario_reg','int4');
		$this->captura('usuario_ai','varchar');
		$this->captura('fecha_reg','timestamp');
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
			
	function insertarPeriodoVenta(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='obingresos.ft_periodo_venta_ime';
		$this->transaccion='OBING_PERVEN_INS';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_pais','id_pais','int4');
		$this->setParametro('id_gestion','id_gestion','int4');
		$this->setParametro('mes','mes','varchar');
		$this->setParametro('estado','estado','varchar');
		$this->setParametro('nro_periodo_mes','nro_periodo_mes','int4');
		$this->setParametro('fecha_fin','fecha_fin','date');
		$this->setParametro('fecha_ini','fecha_ini','date');
		$this->setParametro('tipo','tipo','varchar');
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('tipo_periodo','tipo_periodo','varchar');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function modificarPeriodoVenta(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='obingresos.ft_periodo_venta_ime';
		$this->transaccion='OBING_PERVEN_MOD';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_periodo_venta','id_periodo_venta','int4');
		$this->setParametro('id_pais','id_pais','int4');
		$this->setParametro('id_gestion','id_gestion','int4');
		$this->setParametro('mes','mes','varchar');
		$this->setParametro('estado','estado','varchar');
		$this->setParametro('nro_periodo_mes','nro_periodo_mes','int4');
		$this->setParametro('fecha_fin','fecha_fin','date');
		$this->setParametro('fecha_ini','fecha_ini','date');
		$this->setParametro('tipo','tipo','varchar');
		$this->setParametro('estado_reg','estado_reg','varchar');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function eliminarPeriodoVenta(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='obingresos.ft_periodo_venta_ime';
		$this->transaccion='OBING_PERVEN_ELI';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_periodo_venta','id_periodo_venta','int4');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
}
?>