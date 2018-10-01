<?php
/**
*@package pXP
*@file gen-MODArchivoAcmDet.php
*@author  (admin)
*@date 05-09-2018 20:36:49
*@description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
*/

class MODArchivoAcmDet extends MODbase{

	function __construct(CTParametro $pParam){
		parent::__construct($pParam);
	}

	function listarArchivoAcmDet(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='obingresos.ft_archivo_acm_det_sel';
		$this->transaccion='OBING_AAD_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion

        $this->capturaCount('total_importe_b','numeric');
        $this->capturaCount('total_importe','numeric');
		$this->capturaCount('sum_neto_b','numeric');
        $this->capturaCount('cantidad_boletosmb','numeric');
        $this->capturaCount('sum_neto','numeric');
        $this->capturaCount('cantidad_boletosmt','numeric');

		//Definicion de la lista del resultado del query
		$this->captura('id_archivo_acm_det','int4');
		$this->captura('id_archivo_acm','int4');
		$this->captura('importe_total_mb','numeric');
		$this->captura('estado_reg','varchar');
		$this->captura('porcentaje','int4');
		$this->captura('importe_total_mt','numeric');
		$this->captura('id_agencia','int4');
		$this->captura('officce_id','varchar');
		$this->captura('usuario_ai','varchar');
		$this->captura('fecha_reg','timestamp');
		$this->captura('id_usuario_reg','int4');
		$this->captura('id_usuario_ai','int4');
		$this->captura('id_usuario_mod','int4');
		$this->captura('fecha_mod','timestamp');
    	$this->captura('neto_total_mb','numeric');
    	$this->captura('cant_bol_mb','int4');
		$this->captura('neto_total_mt','numeric');
    	$this->captura('cant_bol_mt','int4');
		$this->captura('usr_reg','varchar');
		$this->captura('usr_mod','varchar');
		$this->captura('agencia','varchar');
    	$this->captura('tipo_agencia','varchar');
        $this->captura('estado','varchar');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}

	function insertarArchivoAcmDet(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='obingresos.ft_archivo_acm_det_ime';
		$this->transaccion='OBING_AAD_INS';
		$this->tipo_procedimiento='IME';



		//Define los parametros para la funcion
		$this->setParametro('id_archivo_acm','id_archivo_acm','int4');
		$this->setParametro('importe_total_mt','importe_total_mt','numeric');
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('porcentaje','porcentaje','int4');
		$this->setParametro('importe_total_mb','importe_total_mb','numeric');
		$this->setParametro('id_agencia','id_agencia','int4');
		$this->setParametro('officce_id','officce_id','varchar');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}

	function modificarArchivoAcmDet(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='obingresos.ft_archivo_acm_det_ime';
		$this->transaccion='OBING_AAD_MOD';
		$this->tipo_procedimiento='IME';

		//Define los parametros para la funcion
		$this->setParametro('id_archivo_acm_det','id_archivo_acm_det','int4');
		$this->setParametro('id_archivo_acm','id_archivo_acm','int4');
		$this->setParametro('importe_total_mt','importe_total_mt','numeric');
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('porcentaje','porcentaje','int4');
		$this->setParametro('importe_total_mb','importe_total_mb','numeric');
		$this->setParametro('id_agencia','id_agencia','int4');
		$this->setParametro('officce_id','officce_id','varchar');
        $this->setParametro('neto_total_mt','neto_total_mt','numeric');
        $this->setParametro('cant_bol_mt','cant_bol_mt','numeric');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}

	function eliminarArchivoAcmDet(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='obingresos.ft_archivo_acm_det_ime';
		$this->transaccion='OBING_AAD_ELI';
		$this->tipo_procedimiento='IME';

		//Define los parametros para la funcion
		$this->setParametro('id_archivo_acm_det','id_archivo_acm_det','int4');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
    function eliminarArchivoAcm(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='obingresos.ft_archivo_acm_det_ime';
        $this->transaccion='OBING_AA_ELI';
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

}
?>
