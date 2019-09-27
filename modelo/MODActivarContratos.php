<?php
/**
*@package pXP
*@file gen-MODAcreditacionPorVoideo.php
*@author  (ismael.valdivia)
*@date 08-03-2019 15:50:00
*@description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
*/

class MODActivarContratos extends MODbase{

	function __construct(CTParametro $pParam){
		parent::__construct($pParam);
	}

	function actualizarContratos(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='obingresos.ft_activar_contrato_agencias_ime';
		$this->transaccion='OBING_AC_CONT_MOD';
		$this->tipo_procedimiento='IME';

		//Define los parametros para la funcion
		$this->setParametro('id_agencia','id_agencia','int4');
		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();
		//Devuelve la respuesta
		return $this->respuesta;
	}

}
?>
