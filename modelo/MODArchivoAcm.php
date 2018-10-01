<?php
/**
*@package pXP
*@file gen-MODArchivoAcm.php
*@author  (admin)
*@date 05-09-2018 20:09:45
*@description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
*/

class MODArchivoAcm extends MODbase{

	function __construct(CTParametro $pParam){
		parent::__construct($pParam);
	}

	function listarArchivoAcm(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='obingresos.ft_archivo_acm_sel';
		$this->transaccion='OBING_taa_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion

		//Definicion de la lista del resultado del query
		$this->captura('id_archivo_acm','int4');
		$this->captura('estado_reg','varchar');
		$this->captura('fecha_fin','date');
		$this->captura('nombre','varchar');
		$this->captura('fecha_ini','date');
		$this->captura('usuario_ai','varchar');
		$this->captura('fecha_reg','timestamp');
		$this->captura('id_usuario_reg','int4');
		$this->captura('id_usuario_ai','int4');
		$this->captura('id_usuario_mod','int4');
		$this->captura('fecha_mod','timestamp');
        $this->captura('estado','varchar');
		$this->captura('usr_reg','varchar');
		$this->captura('usr_mod','varchar');
		//$this->captura('ultimo_numero','int4');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}

	function insertarArchivoAcm(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='obingresos.ft_archivo_acm_ime';
		$this->transaccion='OBING_taa_INS';
		$this->tipo_procedimiento='IME';

		//Define los parametros para la funcion
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('fecha_fin','fecha_fin','date');
		$this->setParametro('nombre','nombre','varchar');
		$this->setParametro('fecha_ini','fecha_ini','date');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}

	function modificarArchivoAcm(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='obingresos.ft_archivo_acm_ime';
		$this->transaccion='OBING_taa_MOD';
		$this->tipo_procedimiento='IME';

		//Define los parametros para la funcion
		$this->setParametro('id_archivo_acm','id_archivo_acm','int4');
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('fecha_fin','fecha_fin','date');
		$this->setParametro('nombre','nombre','varchar');
		$this->setParametro('fecha_ini','fecha_ini','date');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}

	function eliminarArchivoAcm(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='obingresos.ft_archivo_acm_ime';
		$this->transaccion='OBING_taa_ELI';
		$this->tipo_procedimiento='IME';

		//Define los parametros para la funcion
		$this->setParametro('id_archivo_acm','id_archivo_acm','int4');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
    function reporteGenArchivoACM(){
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento='obingresos.ft_archivo_acm_sel';
        $this->transaccion='OBING_REPORT_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion
        //$this->setCount(false);
        //Define los parametros para la funcion
        $this->setParametro('id_archivo_acm','id_archivo_acm','int4');
        $this->setParametro('fecha_ini','fecha_ini','date');
        $this->setParametro('fecha_fin','fecha_fin','date');
        /*$this->setParametro('numero','numero','varchar');
        $this->setParametro('comision','comision','numeric');
        $this->setParametro('neto','neto','numeric');
        $this->setParametro('porcentaje','porcentaje','int4');
        $this->setParametro('office_id','office_id','varchar');

        $this->setParametro('codigo','codigo','varchar');
        $this->setParametro('nombre','nombre','varchar');
        $this->setParametro('billete','billete','varchar');

        $this->setParametro('com_bsp','com_bsp','numeric');
        $this->setParametro('importe_total_mb','importe_total_mb','numeric');
        $this->setParametro('total_bsp','total_bsp','numeric');*/


        //Definicion de la lista del resultado del query
        $this->captura('id_archivo_acm','int4');
        $this->captura('estado_reg','varchar');
        $this->captura('fecha_fin','date');
        $this->captura('nombre','varchar');
        $this->captura('fecha_ini','date');
        $this->captura('usuario_ai','varchar');
        $this->captura('fecha_reg','timestamp');
        $this->captura('id_usuario_reg','int4');
        $this->captura('id_usuario_ai','int4');
        $this->captura('id_usuario_mod','int4');
        $this->captura('fecha_mod','timestamp');
        $this->captura('estado','varchar');
//        $this->captura('usr_reg','varchar');
//        $this->captura('usr_mod','varchar');

        $this->captura('porcentaje','int4');
        $this->captura('neto_total_mb','numeric');
        $this->captura('neto_total_mt','numeric');
        $this->captura('cant_bol_mb','int4');
        $this->captura('cant_bol_mt','int4');
        $this->captura('importe_total_mb','numeric');
        $this->captura('importe_total_mt','numeric');
        $this->captura('id_archivo_acm_det','int4');


        $this->captura('agencia','varchar');
        $this->captura('office_id','varchar');

        $this->captura('cod_ciudad','varchar');
        $this->captura('estacion','varchar');

        $this->captura('numero_acm','varchar');




        //var_dump( $this->respuesta);exit;
        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }
    function habilitarValidacion(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='obingresos.ft_archivo_acm_ime';
        $this->transaccion='OBING_taa_habilitar';
        $this->tipo_procedimiento='IME';

        //Define los parametros para la funcion
        $this->setParametro('id_archivo_acm','id_archivo_acm','int4');
        $this->setParametro('estado_reg','estado_reg','varchar');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

}
?>
