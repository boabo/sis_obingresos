<?php
/**
*@package pXP
*@file gen-MODBoleto.php
*@author  (jrivera)
*@date 06-01-2016 22:42:25
*@description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
*/

class MODBoleto extends MODbase{
	
	function __construct(CTParametro $pParam){
		parent::__construct($pParam);
	}
			
	function listarBoleto(){ 
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='obingresos.ft_boleto_sel';
		$this->transaccion='OBING_BOL_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion
				
		//Definicion de la lista del resultado del query
		$this->captura('id_boleto','int4');
		$this->captura('fecha_emision','date');
		$this->captura('codigo_noiata','varchar');
		$this->captura('cupones','int4');
		$this->captura('ruta','varchar');
		$this->captura('estado','varchar');
		$this->captura('id_agencia','int4');
		$this->captura('moneda','varchar');
		$this->captura('total','numeric');
		$this->captura('pasajero','varchar');
		$this->captura('id_moneda_boleto','int4');
		$this->captura('estado_reg','varchar');
		$this->captura('gds','varchar');
		$this->captura('comision','numeric');
		$this->captura('codigo_agencia','varchar');
		$this->captura('neto','numeric');
		$this->captura('tipopax','varchar');
		$this->captura('origen','varchar');
		$this->captura('destino','varchar');
		$this->captura('retbsp','varchar');
		$this->captura('monto_pagado_moneda_boleto','numeric');
		$this->captura('tipdoc','varchar');
		$this->captura('liquido','numeric');
		$this->captura('nro_boleto','varchar');
		$this->captura('id_usuario_ai','int4');
		$this->captura('id_usuario_reg','int4');
		$this->captura('fecha_reg','timestamp');
		$this->captura('usuario_ai','varchar');
		$this->captura('id_usuario_mod','int4');
		$this->captura('fecha_mod','timestamp');
		$this->captura('usr_reg','varchar');
		$this->captura('usr_mod','varchar');
		$this->captura('nombre_agencia','varchar');
		
		$this->captura('id_forma_pago','integer');
		$this->captura('forma_pago','varchar');
		$this->captura('monto_forma_pago','numeric');
		$this->captura('codigo_forma_pago','varchar');
		$this->captura('numero_tarjeta','varchar');
		$this->captura('ctacte','varchar');
		$this->captura('moneda_fp1','varchar');
		
		$this->captura('id_forma_pago2','integer');
		$this->captura('forma_pago2','varchar');
		$this->captura('monto_forma_pago2','numeric');
		$this->captura('codigo_forma_pago2','varchar');
		$this->captura('numero_tarjeta2','varchar');
		$this->captura('ctacte2','varchar');
		$this->captura('moneda_fp2','varchar');
		
		
		$this->captura('tc','numeric');
		$this->captura('moneda_sucursal','varchar');
		$this->captura('ruta_completa','varchar');
		
		
		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();
		
		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function insertarBoleto(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='obingresos.ft_boleto_ime';
		$this->transaccion='OBING_BOL_INS';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('fecha_emision','fecha_emision','date');
		$this->setParametro('agtnoiata','agtnoiata','varchar');
		$this->setParametro('cupones','cupones','int4');
		$this->setParametro('ruta','ruta','varchar');
		$this->setParametro('estado','estado','varchar');
		$this->setParametro('id_agencia','id_agencia','int4');
		$this->setParametro('moneda','moneda','varchar');
		$this->setParametro('total','total','numeric');
		$this->setParametro('pasajero','pasajero','varchar');
		$this->setParametro('id_moneda_boleto','id_moneda_boleto','int4');
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('gds','gds','varchar');
		$this->setParametro('comision','comision','numeric');
		$this->setParametro('agt','agt','varchar');
		$this->setParametro('neto','neto','numeric');
		$this->setParametro('tipopax','tipopax','varchar');
		$this->setParametro('origen','origen','varchar');
		$this->setParametro('destino','destino','varchar');
		$this->setParametro('retbsp','retbsp','varchar');
		$this->setParametro('monto_pagado_moneda_boleto','monto_pagado_moneda_boleto','numeric');
		$this->setParametro('tipdoc','tipdoc','varchar');
		$this->setParametro('liquido','liquido','numeric');
		$this->setParametro('nro_boleto','nro_boleto','varchar');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}

	function modificarBoletoVenta(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='obingresos.ft_boleto_ime';
		$this->transaccion='OBING_BOLVEN_UPD';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_boleto','id_boleto','integer');
		$this->setParametro('id_forma_pago','id_forma_pago','integer');
		$this->setParametro('monto_forma_pago','monto_forma_pago','numeric');
		$this->setParametro('numero_tarjeta','numero_tarjeta','varchar');
		$this->setParametro('ctacte','ctacte','varchar');
		$this->setParametro('id_forma_pago2','id_forma_pago2','integer');
		$this->setParametro('monto_forma_pago2','monto_forma_pago2','numeric');
		$this->setParametro('numero_tarjeta2','numero_tarjeta2','varchar');
		$this->setParametro('ctacte2','ctacte2','varchar');
		$this->setParametro('comision','comision','numeric');	
		$this->setParametro('estado','estado','varchar');		

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}

	function modificarFpGrupo(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='obingresos.ft_boleto_ime';
		$this->transaccion='OBING_MODFPGRUPO_UPD';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('ids_seleccionados','ids_seleccionados','varchar');
		$this->setParametro('id_forma_pago','id_forma_pago','integer');
		$this->setParametro('monto_forma_pago','monto_forma_pago','numeric');
		$this->setParametro('numero_tarjeta','numero_tarjeta','varchar');
		$this->setParametro('ctacte','ctacte','varchar');
		$this->setParametro('id_forma_pago2','id_forma_pago2','integer');
		$this->setParametro('monto_forma_pago2','monto_forma_pago2','numeric');
		$this->setParametro('numero_tarjeta2','numero_tarjeta2','varchar');
		$this->setParametro('ctacte2','ctacte2','varchar');
				

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}

	function insertarBoletoServicio(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='obingresos.ft_boleto_ime';
		$this->transaccion='OBING_BOLSERV_INS';
		$this->tipo_procedimiento='IME'; 
				
		//Define los parametros para la funcion
		$this->setParametro('id_punto_venta','id_punto_venta','integer');//ok
		$this->setParametro('nro_boleto','nro_boleto','varchar');//ok
		$this->setParametro('fecha_emision','fecha_emision','varchar');//ok
		$this->setParametro('pasajero','pasajero','varchar');//ok
		$this->setParametro('total','total','numeric');//ok
		$this->setParametro('moneda','moneda','varchar');//ok
		$this->setParametro('neto','neto','numeric');//ok
		$this->setParametro('endoso','endoso','varchar');//ok
		$this->setParametro('origen','origen','varchar');//ok
		$this->setParametro('destino','destino','varchar');//ok
		$this->setParametro('cupones','cupones','integer');//ok
		$this->setParametro('impuestos','impuestos','varchar');//ok
		
		$this->setParametro('fp','fp','varchar');
		$this->setParametro('moneda_fp','moneda_fp','varchar');
		$this->setParametro('valor_fp','valor_fp','varchar');
		$this->setParametro('rutas','rutas','varchar');	
		
		$this->setParametro('ruta_completa','ruta_completa','varchar');		

		//Ejecuta la instruccion
		$this->armarConsulta();
		
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function modificarBoleto(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='obingresos.ft_boleto_ime';
		$this->transaccion='OBING_BOL_MOD';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_boleto','id_boleto','int4');
		$this->setParametro('fecha_emision','fecha_emision','date');
		$this->setParametro('agtnoiata','agtnoiata','varchar');
		$this->setParametro('cupones','cupones','int4');
		$this->setParametro('ruta','ruta','varchar');
		$this->setParametro('estado','estado','varchar');
		$this->setParametro('id_agencia','id_agencia','int4');
		$this->setParametro('moneda','moneda','varchar');
		$this->setParametro('total','total','numeric');
		$this->setParametro('pasajero','pasajero','varchar');
		$this->setParametro('id_moneda_boleto','id_moneda_boleto','int4');
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('gds','gds','varchar');
		$this->setParametro('comision','comision','numeric');
		$this->setParametro('agt','agt','varchar');
		$this->setParametro('neto','neto','numeric');
		$this->setParametro('tipopax','tipopax','varchar');
		$this->setParametro('origen','origen','varchar');
		$this->setParametro('destino','destino','varchar');
		$this->setParametro('retbsp','retbsp','varchar');
		$this->setParametro('monto_pagado_moneda_boleto','monto_pagado_moneda_boleto','numeric');
		$this->setParametro('tipdoc','tipdoc','varchar');
		$this->setParametro('liquido','liquido','numeric');
		$this->setParametro('nro_boleto','nro_boleto','varchar');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function eliminarBoleto(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='obingresos.ft_boleto_ime';
		$this->transaccion='OBING_BOL_ELI';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_boleto','id_boleto','int4');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
	
	function cambiaEstadoBoleto(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='obingresos.ft_boleto_ime';
		$this->transaccion='OBING_BOLEST_MOD';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_boleto','id_boleto','int4');
		$this->setParametro('accion','accion','varchar');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
}
?>