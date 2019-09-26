<?php
/**
*@package pXP
*@file gen-MODAcreditacionPorVoideo.php
*@author  (ismael.valdivia)
*@date 08-03-2019 15:50:00
*@description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
*/

class MODAcreditacionPortal extends MODbase{

	function __construct(CTParametro $pParam){
		parent::__construct($pParam);
	}

	function insertarAcreditacion(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='obingresos.ft_acreditacion_portal_ime';
		$this->transaccion='OBING_ACREPOR_INS';
		$this->tipo_procedimiento='IME';

		//Define los parametros para la funcion
		$this->setParametro('pnr','pnr','varchar');
		$this->setParametro('id_void','id_void','int4');
		$this->setParametro('monto','monto','numeric');
		$this->setParametro('codigo_moneda','codigo_moneda','varchar');
		//$this->setParametro('autorizacion__nro_deposito','autorizacion__nro_deposito','varchar');
		$this->setParametro('id_agencia','id_agencia','int4');
		$this->setParametro('billete','billete','varchar');
		$this->setParametro('monto_comision','monto_comision','numeric');
		$this->setParametro('tipo_void','tipo_void','varchar');
		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();
		//Devuelve la respuesta
		return $this->respuesta;
	}

}
?>
