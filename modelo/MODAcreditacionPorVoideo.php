<?php
/**
*@package pXP
*@file gen-MODAcreditacionPorVoideo.php
*@author  (ismael.valdivia)
*@date 08-03-2019 15:50:00
*@description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
*/

class MODAcreditacionPorVoideo extends MODbase{

	function __construct(CTParametro $pParam){
		parent::__construct($pParam);
	}

	function insertarAcreditacion(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='obingresos.ft_acreditacion_por_voideo_ime';
		$this->transaccion='OBING_ACREVOID_INS';
		$this->tipo_procedimiento='IME';

		//Define los parametros para la funcion
    //$this->setParametro('id_movimiento_entidad','id_movimiento_entidad','int4');
		$this->setParametro('pnr','pnr','varchar');
		//$this->setParametro('fecha','fecha','date');
		$this->setParametro('id_boleto','id_boleto','varchar');
		$this->setParametro('monto','monto','numeric');
		$this->setParametro('codigo_moneda','codigo_moneda','varchar');
		$this->setParametro('autorizacion__nro_deposito','autorizacion__nro_deposito','varchar');
		$this->setParametro('id_agencia','id_agencia','int4');
		$this->setParametro('billete','billete','varchar');
		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}

	// function modificarContratos(){
	// 	//Definicion de variables para ejecucion del procedimiento
	// 	$this->procedimiento='obingresos.ft_contratos_ime';
	// 	$this->transaccion='OBING_CON_MOD';
	// 	$this->tipo_procedimiento='IME';
  //
	// 	//Define los parametros para la funcion
	// 	$this->setParametro('id_contrato','id_contrato','int4');
	// 	$this->setParametro('codigo','codigo','varchar');
	// 	$this->setParametro('tipo_agencia','tipo_agencia','varchar');
	// 	$this->setParametro('codigo_noiata','codigo_noiata','varchar');
	// 	$this->setParametro('numero','numero','varchar');
	// 	$this->setParametro('fecha_fin','fecha_fin','date');
	// 	$this->setParametro('nombre','nombre','varchar');
	// 	$this->setParametro('nit','nit','varchar');
	// 	$this->setParametro('desc_funcionario1','desc_funcionario1','text');
	// 	$this->setParametro('id_agencia','id_agencia','int4');
	// 	$this->setParametro('tipo','tipo','varchar');
	// 	$this->setParametro('dias_restante','dias_restante','int4');
	// 	$this->setParametro('estado','estado','varchar');
	// 	$this->setParametro('email','email','varchar');
	// 	$this->setParametro('fecha_inicio','fecha_inicio','date');
	// 	$this->setParametro('formas_pago','formas_pago','_varchar');
  //
	// 	//Ejecuta la instruccion
	// 	$this->armarConsulta();
	// 	$this->ejecutarConsulta();
  //
	// 	//Devuelve la respuesta
	// 	return $this->respuesta;
	// }
  //
	// function eliminarContratos(){
	// 	//Definicion de variables para ejecucion del procedimiento
	// 	$this->procedimiento='obingresos.ft_contratos_ime';
	// 	$this->transaccion='OBING_CON_ELI';
	// 	$this->tipo_procedimiento='IME';
  //
	// 	//Define los parametros para la funcion
	// 	$this->setParametro('id_contrato','id_contrato','int4');
  //
	// 	//Ejecuta la instruccion
	// 	$this->armarConsulta();
	// 	$this->ejecutarConsulta();
  //
	// 	//Devuelve la respuesta
	// 	return $this->respuesta;
	// }

}
?>
