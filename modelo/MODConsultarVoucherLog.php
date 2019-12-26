<?php
/**
*@package pXP
*@file gen-MODConsultarVoucherLog.php
*@author  (ismael.valdivia)
*@date 19-12-2019 17:56:00
*@description Clase que envia los parametros para recuperar datos del voucher
*/

class MODConsultarVoucherLog extends MODbase{

	function __construct(CTParametro $pParam){
		parent::__construct($pParam);
	}

	function consultarVoucher(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='obingresos.ft_recuperar_datos_voucher_sel';
		$this->transaccion='OBING_RECUVOUCH_SEL';
		$this->tipo_procedimiento='SEL';
		$this->setCount(false);


		//Define los parametros para la funcion
		$this->setParametro('voucher_code','voucher_code','varchar');

		$this->captura('responsable','varchar');
		$this->captura('nro_boleto','varchar');
		$this->captura('pnr','varchar');
		$this->captura('status','varchar');
		$this->captura('status_canjeado','varchar');
		$this->captura('message','varchar');
		$this->captura('message_canjeado','varchar');
		$this->captura('ffid','int4');
		$this->captura('voucher_code','varchar');
		$this->captura('nombre_completo2','varchar');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();
		//Devuelve la respuesta
		return $this->respuesta;
	}

}
?>
