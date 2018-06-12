<?php
/**
*@package pXP
*@file gen-MODContratos.php
*@author  (miguel.mamani)
*@date 24-05-2018 15:10:35
*@description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
*/

class MODContratos extends MODbase{
	
	function __construct(CTParametro $pParam){
		parent::__construct($pParam);
	}
			
	function listarContratos(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='obingresos.ft_contratos_sel';
		$this->transaccion='OBING_CON_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion
				
		//Definicion de la lista del resultado del query
		$this->captura('id_contrato','int4');
		$this->captura('codigo','varchar');
		$this->captura('tipo_agencia','varchar');
		$this->captura('codigo_noiata','varchar');
		$this->captura('numero','varchar');
		$this->captura('fecha_fin','date');
		$this->captura('nombre','varchar');
		$this->captura('nit','varchar');
		$this->captura('desc_funcionario1','text');
		$this->captura('id_agencia','int4');
		$this->captura('tipo','varchar');
		$this->captura('dias_restante','int4');
		$this->captura('estado','varchar');
		$this->captura('email','varchar');
		$this->captura('fecha_inicio','date');
		$this->captura('formas_pago','_varchar');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();
		
		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function insertarContratos(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='obingresos.ft_contratos_ime';
		$this->transaccion='OBING_CON_INS';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('codigo','codigo','varchar');
		$this->setParametro('tipo_agencia','tipo_agencia','varchar');
		$this->setParametro('codigo_noiata','codigo_noiata','varchar');
		$this->setParametro('numero','numero','varchar');
		$this->setParametro('fecha_fin','fecha_fin','date');
		$this->setParametro('nombre','nombre','varchar');
		$this->setParametro('nit','nit','varchar');
		$this->setParametro('desc_funcionario1','desc_funcionario1','text');
		$this->setParametro('id_agencia','id_agencia','int4');
		$this->setParametro('tipo','tipo','varchar');
		$this->setParametro('dias_restante','dias_restante','int4');
		$this->setParametro('estado','estado','varchar');
		$this->setParametro('email','email','varchar');
		$this->setParametro('fecha_inicio','fecha_inicio','date');
		$this->setParametro('formas_pago','formas_pago','_varchar');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function modificarContratos(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='obingresos.ft_contratos_ime';
		$this->transaccion='OBING_CON_MOD';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_contrato','id_contrato','int4');
		$this->setParametro('codigo','codigo','varchar');
		$this->setParametro('tipo_agencia','tipo_agencia','varchar');
		$this->setParametro('codigo_noiata','codigo_noiata','varchar');
		$this->setParametro('numero','numero','varchar');
		$this->setParametro('fecha_fin','fecha_fin','date');
		$this->setParametro('nombre','nombre','varchar');
		$this->setParametro('nit','nit','varchar');
		$this->setParametro('desc_funcionario1','desc_funcionario1','text');
		$this->setParametro('id_agencia','id_agencia','int4');
		$this->setParametro('tipo','tipo','varchar');
		$this->setParametro('dias_restante','dias_restante','int4');
		$this->setParametro('estado','estado','varchar');
		$this->setParametro('email','email','varchar');
		$this->setParametro('fecha_inicio','fecha_inicio','date');
		$this->setParametro('formas_pago','formas_pago','_varchar');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function eliminarContratos(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='obingresos.ft_contratos_ime';
		$this->transaccion='OBING_CON_ELI';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_contrato','id_contrato','int4');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
}
?>