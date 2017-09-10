<?php
/**
*@package pXP
*@file gen-MODTipoPeriodo.php
*@author  (jrivera)
*@date 08-05-2017 20:02:14
*@description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
*/

class MODTipoPeriodo extends MODbase{
	
	function __construct(CTParametro $pParam){
		parent::__construct($pParam);
	}
			
	function listarTipoPeriodo(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='obingresos.ft_tipo_periodo_sel';
		$this->transaccion='OBING_TIPER_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion
				
		//Definicion de la lista del resultado del query
		$this->captura('id_tipo_periodo','int4');
		$this->captura('pago_comision','varchar');
		$this->captura('tipo','varchar');
		$this->captura('estado','varchar');
		$this->captura('estado_reg','varchar');
		$this->captura('medio_pago','varchar');
		$this->captura('tiempo','varchar');
		$this->captura('tipo_cc','varchar');
		$this->captura('id_usuario_reg','int4');
		$this->captura('fecha_reg','timestamp');
		$this->captura('usuario_ai','varchar');
		$this->captura('id_usuario_ai','int4');
		$this->captura('fecha_mod','timestamp');
		$this->captura('id_usuario_mod','int4');
		$this->captura('usr_reg','varchar');
		$this->captura('usr_mod','varchar');
        $this->captura('fecha_ini_primer_periodo','date');
		
		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();
		
		//Devuelve la respuesta
		return $this->respuesta;
	}

	function obtenerTipoPeriodoXFP(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='obingresos.ft_tipo_periodo_sel';
		$this->transaccion='OBING_TIPERXFP_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion
		$this->setCount(false);
		
		$this->setParametro('formas_pago','formas_pago','varchar');
				
		//Definicion de la lista del resultado del query
		$this->captura('id_tipo_periodo','int4');		
		$this->captura('tiempo','varchar');
		$this->captura('forma_pago','varchar');		
		
		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();
		
		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function insertarTipoPeriodo(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='obingresos.ft_tipo_periodo_ime';
		$this->transaccion='OBING_TIPER_INS';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('pago_comision','pago_comision','varchar');
		$this->setParametro('tipo','tipo','varchar');
		$this->setParametro('estado','estado','varchar');
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('medio_pago','medio_pago','varchar');
		$this->setParametro('tiempo','tiempo','varchar');
		$this->setParametro('tipo_cc','tipo_cc','varchar');
        $this->setParametro('fecha_ini_primer_periodo','fecha_ini_primer_periodo','date');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function modificarTipoPeriodo(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='obingresos.ft_tipo_periodo_ime';
		$this->transaccion='OBING_TIPER_MOD';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_tipo_periodo','id_tipo_periodo','int4');
		$this->setParametro('pago_comision','pago_comision','varchar');
		$this->setParametro('tipo','tipo','varchar');
		$this->setParametro('estado','estado','varchar');
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('medio_pago','medio_pago','varchar');
		$this->setParametro('tiempo','tiempo','varchar');
		$this->setParametro('tipo_cc','tipo_cc','varchar');
        $this->setParametro('fecha_ini_primer_periodo','fecha_ini_primer_periodo','date');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function eliminarTipoPeriodo(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='obingresos.ft_tipo_periodo_ime';
		$this->transaccion='OBING_TIPER_ELI';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_tipo_periodo','id_tipo_periodo','int4');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
}
?>