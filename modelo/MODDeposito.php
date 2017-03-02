<?php
/**
*@package pXP
*@file gen-MODDeposito.php
*@author  (jrivera)
*@date 06-01-2016 22:42:28
*@description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
*/

class MODDeposito extends MODbase{
	
	function __construct(CTParametro $pParam){
		parent::__construct($pParam);
	}
			
	function listarDeposito(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='obingresos.ft_deposito_sel';
		$this->transaccion='OBING_DEP_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion
				
		//Definicion de la lista del resultado del query
		$this->captura('id_deposito','int4');
		$this->captura('estado_reg','varchar');
		$this->captura('nro_deposito','varchar');
		$this->captura('monto_deposito','numeric');
		$this->captura('id_moneda_deposito','int4');
		$this->captura('id_agencia','int4');
		$this->captura('fecha','date');
		$this->captura('saldo','numeric');
		$this->captura('id_usuario_reg','int4');
		$this->captura('fecha_reg','timestamp');
		$this->captura('id_usuario_ai','int4');
		$this->captura('usuario_ai','varchar');
		$this->captura('id_usuario_mod','int4');
		$this->captura('fecha_mod','timestamp');
		$this->captura('usr_reg','varchar');
		$this->captura('usr_mod','varchar');
		$this->captura('desc_moneda','varchar');
        $this->captura('agt','varchar');
        $this->captura('fecha_venta','date');
        $this->captura('monto_total','numeric');
		
		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();
		
		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function insertarDeposito(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='obingresos.ft_deposito_ime';
		$this->transaccion='OBING_DEP_INS';
		$this->tipo_procedimiento='IME';

		
		//Define los parametros para la funcion
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('nro_deposito','nro_deposito','varchar');
        $this->setParametro('agt','agt','varchar');
		$this->setParametro('monto_deposito','monto_deposito','numeric');
		$this->setParametro('id_moneda_deposito','id_moneda_deposito','int4');
		$this->setParametro('id_agencia','id_agencia','int4');
		$this->setParametro('fecha','fecha','date');
		$this->setParametro('saldo','saldo','numeric');
        $this->setParametro('descripcion','descripcion','varchar');
        $this->setParametro('pnr','pnr','varchar');
        $this->setParametro('moneda','moneda','varchar');
        $this->setParametro('tipo','tipo','varchar');
        $this->setParametro('fecha_venta','fecha_venta','date');
        $this->setParametro('monto_total','monto_total','numeric');


		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function modificarDeposito(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='obingresos.ft_deposito_ime';
		$this->transaccion='OBING_DEP_MOD';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_deposito','id_deposito','int4');
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('nro_deposito','nro_deposito','varchar');
        $this->setParametro('agt','agt','varchar');
		$this->setParametro('monto_deposito','monto_deposito','numeric');
		$this->setParametro('id_moneda_deposito','id_moneda_deposito','int4');
		$this->setParametro('id_agencia','id_agencia','int4');
		$this->setParametro('fecha','fecha','date');
		$this->setParametro('saldo','saldo','numeric');
        $this->setParametro('tipo','tipo','varchar');
        $this->setParametro('fecha_venta','fecha_venta','date');
        $this->setParametro('monto_total','monto_total','numeric');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function eliminarDeposito(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='obingresos.ft_deposito_ime';
		$this->transaccion='OBING_DEP_ELI';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_deposito','id_deposito','int4');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
    function subirDatos(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='obingresos.ft_deposito_ime';
        $this->transaccion='OBING_DEP_SUB';
        $this->tipo_procedimiento='IME';

        //Define los parametros para la funcion
        $this->setParametro('nro_deposito','nro_deposito','varchar');
        //$this->setParametro('id_agencia','id_agencia','int4');
        $this->setParametro('monto_deposito','monto_deposito','numeric');
        $this->setParametro('moneda','moneda','varchar');
        $this->setParametro('descripcion','descripcion','varchar');
        $this->setParametro('pnr','pnr','varchar');
        $this->setParametro('estado','estado','varchar');
        $this->setParametro('fecha','fecha','varchar');
        $this->setParametro('tipo','tipo','varchar');
        $this->setParametro('observaciones','observaciones','varchar');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }
    function  listarDepositoReporte()
    {
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento='obingresos.ft_deposito_sel';
        $this->transaccion='OBING_DEREP_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion


        $this->setParametro('fecha_ini','fecha_ini','date');
        $this->setParametro('fecha_fin','fecha_fin','date');
        $this->setParametro('por','por','varchar');
        $this->setParametro('tipo_deposito','tipo_deposito','varchar');
        $this->setCount(false);


        $this->captura('nro_deposito','varchar');
        $this->captura('fecha_deposito','varchar');
        $this->captura('pnr','varchar');
        $this->captura('monto_deposito','numeric');
        $this->captura('moneda','varchar');
        $this->captura('numero_tarjeta_deposito','varchar');

        $this->captura('total_boletos','numeric');
        $this->captura('nro_boletos','text');
        $this->captura('fecha_boletos','text');
        $this->captura('numero_tarjeta','text');
        $this->captura('detalle_boletos','text');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();



        //Devuelve la respuesta
        return $this->respuesta;
    }

    function  reporteDepositoBancaInternet()
    {
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento='obingresos.ft_deposito_sel';
        $this->transaccion='OBING_DEPBIN_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion


        $this->setParametro('fecha_ini','fecha_ini','date');
        $this->setParametro('fecha_fin','fecha_fin','date');
        $this->setParametro('id_moneda','id_moneda','integer');

        $this->setCount(false);


        $this->captura('fecha','varchar');
        $this->captura('banco','varchar');
        $this->captura('monto','numeric');


        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();



        //Devuelve la respuesta
        return $this->respuesta;
    }

    function  reporteDepositoBancaInternetArchivo()
    {
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento='obingresos.ft_deposito_sel';
        $this->transaccion='OBING_DEPBINARC_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion


        $this->setParametro('fecha_ini','fecha_ini','date');
        $this->setParametro('fecha_fin','fecha_fin','date');
        $this->setParametro('id_moneda','id_moneda','integer');

        $this->setCount(false);


        $this->captura('fecha','varchar');
        $this->captura('banco','varchar');
        $this->captura('monto','numeric');


        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        

        //Devuelve la respuesta
        return $this->respuesta;
    }


}
?>