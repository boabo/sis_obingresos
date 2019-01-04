<?php
/**
*@package pXP
*@file gen-MODViajeroInterno.php
*@author  (rzabala)
*@date 21-12-2018 14:21:03
*@description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
*/

class MODViajeroInterno extends MODbase{
	
	function __construct(CTParametro $pParam){
		parent::__construct($pParam);
	}
			
	function listarViajeroInterno(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='obingresos.ft_viajero_interno_sel';
		$this->transaccion='OBING_CVI_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion
				
		//Definicion de la lista del resultado del query
		$this->captura('id_viajero_interno','int4');
		$this->captura('estado_reg','varchar');
		$this->captura('codigo_voucher','varchar');
		$this->captura('mensaje','varchar');
		$this->captura('estado','varchar');
		$this->captura('id_usuario_reg','int4');
		$this->captura('fecha_reg','timestamp');
		$this->captura('id_usuario_ai','int4');
		$this->captura('usuario_ai','varchar');
		$this->captura('id_usuario_mod','int4');
		$this->captura('fecha_mod','timestamp');
		$this->captura('usr_reg','varchar');
		$this->captura('usr_mod','varchar');
		
		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();
		
		//Devuelve la respuesta
		return $this->respuesta;
	}

    /*function insertarViajeroInternoM(){//die('insertar');
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='obingresos.ft_viajero_interno_ime';
        $this->transaccion='OBING_CVI_INS';
        $this->tipo_procedimiento='IME';

        //Define los parametros para la funcion
        $this->setParametro('estado_reg','estado_reg','varchar');
        $this->setParametro('codigo_voucher','codigo_voucher','varchar');
        $this->setParametro('mensaje','mensaje','varchar');
        $this->setParametro('estado','estado','varchar');
        $this->setParametro('detalles','detalles','jsonb');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }*/
			
	function insertarViajeroInterno(){//die('insertar');
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='obingresos.ft_viajero_interno_ime';
		$this->transaccion='OBING_CVI_INS';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('codigo_voucher','codigo_voucher','varchar');
		$this->setParametro('mensaje','mensaje','varchar');
		$this->setParametro('estado','estado','varchar');
        $this->setParametro('detalles','detalles','jsonb');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
		//var_dump($this->respuesta);exit;
	}
			
	function modificarViajeroInterno(){die('modificar');
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='obingresos.ft_viajero_interno_ime';
		$this->transaccion='OBING_CVI_MOD';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_viajero_interno','id_viajero_interno','int4');
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('codigo_voucher','codigo_voucher','varchar');
		$this->setParametro('mensaje','mensaje','varchar');
		$this->setParametro('estado','estado','varchar');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function eliminarViajeroInterno(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='obingresos.ft_viajero_interno_ime';
		$this->transaccion='OBING_CVI_ELI';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_viajero_interno','id_viajero_interno','int4');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
}
?>