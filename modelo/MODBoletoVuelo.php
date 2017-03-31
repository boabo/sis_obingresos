<?php
/**
*@package pXP
*@file gen-MODBoletoVuelo.php
*@author  (jrivera)
*@date 29-03-2017 10:59:33
*@description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
*/

class MODBoletoVuelo extends MODbase{
	
	function __construct(CTParametro $pParam){
		parent::__construct($pParam);
	}
			
	function listarBoletoVuelo(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='obingresos.ft_boleto_vuelo_sel';
		$this->transaccion='OBING_BVU_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion
				
		//Definicion de la lista del resultado del query
		$this->captura('id_boleto_vuelo','int4');
		$this->captura('id_aeropuerto_destino','int4');
		$this->captura('id_aeropuerto_origen','int4');
		$this->captura('fecha_hora_origen','timestamp');
		$this->captura('id_boleto_conjuncion','int4');
		$this->captura('linea','varchar');
		$this->captura('estado_reg','varchar');
		$this->captura('vuelo','varchar');
		$this->captura('fecha','date');
		$this->captura('hora_destino','time');
		$this->captura('status','varchar');
		$this->captura('equipaje','varchar');
		$this->captura('hora_origen','time');
		$this->captura('retorno','varchar');
		$this->captura('fecha_hora_destino','timestamp');
		$this->captura('tiempo_conexion','int4');
		$this->captura('cupon','int2');
		$this->captura('id_boleto','int4');
		$this->captura('aeropuerto_origen','varchar');
		$this->captura('aeropuerto_destino','varchar');
		$this->captura('tarifa','varchar');
		$this->captura('usuario_ai','varchar');
		$this->captura('fecha_reg','timestamp');
		$this->captura('id_usuario_reg','int4');
		$this->captura('id_usuario_ai','int4');
		$this->captura('id_usuario_mod','int4');
		$this->captura('fecha_mod','timestamp');
		$this->captura('usr_reg','varchar');
		$this->captura('usr_mod','varchar');
        $this->captura('boleto_vuelo','varchar');
		
		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();
		
		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function insertarBoletoVuelo(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='obingresos.ft_boleto_vuelo_ime';
		$this->transaccion='OBING_BVU_INS';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_aeropuerto_destino','id_aeropuerto_destino','int4');
		$this->setParametro('id_aeropuerto_origen','id_aeropuerto_origen','int4');
		$this->setParametro('fecha_hora_origen','fecha_hora_origen','timestamp');
		$this->setParametro('id_boleto_conjuncion','id_boleto_conjuncion','int4');
		$this->setParametro('linea','linea','varchar');
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('vuelo','vuelo','varchar');
		$this->setParametro('fecha','fecha','date');
		$this->setParametro('hora_destino','hora_destino','time');
		$this->setParametro('status','status','varchar');
		$this->setParametro('equipaje','equipaje','varchar');
		$this->setParametro('hora_origen','hora_origen','time');
		$this->setParametro('retorno','retorno','varchar');
		$this->setParametro('fecha_hora_destino','fecha_hora_destino','timestamp');
		$this->setParametro('tiempo_conexion','tiempo_conexion','int4');
		$this->setParametro('cupon','cupon','int2');
		$this->setParametro('id_boleto','id_boleto','int4');
		$this->setParametro('aeropuerto_origen','aeropuerto_origen','varchar');
		$this->setParametro('aeropuerto_destino','aeropuerto_destino','varchar');
		$this->setParametro('tarifa','tarifa','varchar');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function modificarBoletoVuelo(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='obingresos.ft_boleto_vuelo_ime';
		$this->transaccion='OBING_BVU_MOD';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_boleto_vuelo','id_boleto_vuelo','int4');
		$this->setParametro('id_aeropuerto_destino','id_aeropuerto_destino','int4');
		$this->setParametro('id_aeropuerto_origen','id_aeropuerto_origen','int4');
		$this->setParametro('fecha_hora_origen','fecha_hora_origen','timestamp');
		$this->setParametro('id_boleto_conjuncion','id_boleto_conjuncion','int4');
		$this->setParametro('linea','linea','varchar');
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('vuelo','vuelo','varchar');
		$this->setParametro('fecha','fecha','date');
		$this->setParametro('hora_destino','hora_destino','time');
		$this->setParametro('status','status','varchar');
		$this->setParametro('equipaje','equipaje','varchar');
		$this->setParametro('hora_origen','hora_origen','time');
		$this->setParametro('retorno','retorno','varchar');
		$this->setParametro('fecha_hora_destino','fecha_hora_destino','timestamp');
		$this->setParametro('tiempo_conexion','tiempo_conexion','int4');
		$this->setParametro('cupon','cupon','int2');
		$this->setParametro('id_boleto','id_boleto','int4');
		$this->setParametro('aeropuerto_origen','aeropuerto_origen','varchar');
		$this->setParametro('aeropuerto_destino','aeropuerto_destino','varchar');
		$this->setParametro('tarifa','tarifa','varchar');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function eliminarBoletoVuelo(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='obingresos.ft_boleto_vuelo_ime';
		$this->transaccion='OBING_BVU_ELI';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_boleto_vuelo','id_boleto_vuelo','int4');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
}
?>