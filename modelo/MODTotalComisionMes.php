<?php
/**
*@package pXP
*@file gen-MODTotalComisionMes.php
*@author  (jrivera)
*@date 17-08-2017 21:28:24
*@description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
*/

class MODTotalComisionMes extends MODbase{
	
	function __construct(CTParametro $pParam){
		parent::__construct($pParam);
	}
			
	function listarTotalComisionMes(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='obingresos.ft_total_comision_mes_sel';
		$this->transaccion='OBING_TOTFAC_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion
				
		//Definicion de la lista del resultado del query
		$this->captura('id_total_comision_mes','int4');
		$this->captura('gestion','numeric');
		$this->captura('estado','varchar');

		$this->captura('periodo','numeric');
		$this->captura('total_comision','numeric');
		$this->captura('id_periodos','_int4');
		$this->captura('estado_reg','varchar');
		$this->captura('id_tipo_periodo','int4');
		$this->captura('id_usuario_ai','int4');
		$this->captura('id_usuario_reg','int4');
		$this->captura('fecha_reg','timestamp');
		$this->captura('usuario_ai','varchar');
		$this->captura('fecha_mod','timestamp');
		$this->captura('id_usuario_mod','int4');
		$this->captura('usr_reg','varchar');
		$this->captura('usr_mod','varchar');
        $this->captura('codigo_int','varchar');
        $this->captura('agencia','varchar');
        $this->captura('medio_pago','varchar');
		
		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();
		
		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function insertarTotalComisionMes(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='obingresos.ft_total_comision_mes_ime';
		$this->transaccion='OBING_TOTFAC_INS';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('gestion','gestion','numeric');
		$this->setParametro('estado','estado','varchar');
		$this->setParametro('max_fecha_fin_periodo','max_fecha_fin_periodo','date');
		$this->setParametro('periodo','periodo','numeric');
		$this->setParametro('total_comision','total_comision','numeric');
		$this->setParametro('id_periodos','id_periodos','_int4');
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('id_tipo_periodo','id_tipo_periodo','int4');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function modificarTotalComisionMes(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='obingresos.ft_total_comision_mes_ime';
		$this->transaccion='OBING_TOTFAC_MOD';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_total_comision_mes','id_total_comision_mes','int4');
		$this->setParametro('gestion','gestion','numeric');
		$this->setParametro('estado','estado','varchar');
		$this->setParametro('max_fecha_fin_periodo','max_fecha_fin_periodo','date');
		$this->setParametro('periodo','periodo','numeric');
		$this->setParametro('total_comision','total_comision','numeric');
		$this->setParametro('id_periodos','id_periodos','_int4');
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('id_tipo_periodo','id_tipo_periodo','int4');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function eliminarTotalComisionMes(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='obingresos.ft_total_comision_mes_ime';
		$this->transaccion='OBING_TOTFAC_ELI';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_total_comision_mes','id_total_comision_mes','int4');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}

    function validarComisionMes(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='obingresos.ft_total_comision_mes_ime';
        $this->transaccion='OBING_VALCOMMES_MOD';
        $this->tipo_procedimiento='IME';

        //Define los parametros para la funcion
        $this->setParametro('id_total_comision_mes','id_total_comision_mes','int4');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }
			
}
?>