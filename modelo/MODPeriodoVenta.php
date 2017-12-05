<?php
/**
*@package pXP
*@file gen-MODPeriodoVenta.php
*@author  (jrivera)
*@date 08-04-2016 22:44:37
*@description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
*/

class MODPeriodoVenta extends MODbase{
	
	function __construct(CTParametro $pParam){
		parent::__construct($pParam);
	}
			
	function listarPeriodoVenta(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='obingresos.ft_periodo_venta_sel';
		$this->transaccion='OBING_PERVEN_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion
				
		//Definicion de la lista del resultado del query
		$this->captura('id_periodo_venta','int4');
		$this->captura('id_gestion','int4');
		$this->captura('mes','varchar');
		$this->captura('estado','varchar');
		$this->captura('nro_periodo_mes','int4');
		$this->captura('fecha_fin','date');
		$this->captura('fecha_ini','date');
		$this->captura('tipo_periodo','varchar');
        $this->captura('medio_pago','varchar');
        $this->captura('tipo_cc','varchar');
		$this->captura('estado_reg','varchar');
		$this->captura('id_usuario_ai','int4');
		$this->captura('id_usuario_reg','int4');
		$this->captura('usuario_ai','varchar');
		$this->captura('fecha_reg','timestamp');
		$this->captura('fecha_mod','timestamp');
		$this->captura('id_usuario_mod','int4');
		$this->captura('usr_reg','varchar');
		$this->captura('usr_mod','varchar');
        $this->captura('desc_periodo','text');
        $this->captura('fecha_pago','date');
		
		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();
		
		//Devuelve la respuesta
		return $this->respuesta;
	}

    function listarDetallePeriodoAgencia(){
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento='obingresos.ft_periodo_venta_sel';
        $this->transaccion='OBING_PERDETAG_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion

        $this->setCount(false);

        $this->setParametro('id_periodo_venta','id_periodo_venta','int4');
        $this->setParametro('id_agencia','id_agencia','int4');
        $this->setParametro('tipo','tipo','varchar');


        //Definicion de la lista del resultado del query
        $this->captura('id_periodo_venta','int4');
        $this->captura('tipo','varchar');
        $this->captura('fecha','text');
        $this->captura('pnr','varchar');
        $this->captura('apellido','varchar');
        $this->captura('moneda','varchar');
        $this->captura('monto_boleto','numeric');
        $this->captura('comision','numeric');
        $this->captura('monto_credito_debito','numeric');
        $this->captura('ajuste','varchar');
		$this->captura('garantia','varchar');
		$this->captura('autorizacion_deposito','varchar');
        $this->captura('monto_credito_debito_mb','numeric');
        $this->captura('tipo_cambio','numeric');
        $this->captura('cierre_periodo','varchar');

        //Ejecuta la instruccion
        $this->armarConsulta();

        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function ResumenEstadoCC(){
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento='obingresos.ft_periodo_venta_sel';
        $this->transaccion='OBING_RESESTCC_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion

        $this->setCount(false);


        $this->setParametro('id_agencia','id_agencia','int4');



        //Definicion de la lista del resultado del query
        $this->captura('id_agencia','int4');
        $this->captura('tipo','varchar');
        $this->captura('moneda','varchar');
        $this->captura('monto','numeric');
        $this->captura('monto_mb','numeric');


        //Ejecuta la instruccion
        $this->armarConsulta();
        

        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }


    function listarTotalesPeriodoAgencia(){
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento='obingresos.ft_periodo_venta_sel';
        $this->transaccion='OBING_PERAGTOT_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion


        $this->setParametro('id_periodo_venta','id_periodo_venta','int4');

        $this->capturaCount('total_credito_mb','numeric');
        $this->capturaCount('total_credito_me','numeric');
        $this->capturaCount('total_boletos_mb','numeric');
        $this->capturaCount('total_boletos_usd','numeric');
        $this->capturaCount('total_comision_mb','numeric');
        $this->capturaCount('total_comision_usd','numeric');
        $this->capturaCount('total_debito_mb','numeric');
        $this->capturaCount('total_debito_usd','numeric');
        $this->capturaCount('total_neto_mb','numeric');
        $this->capturaCount('total_neto_usd','numeric');
        

        //Definicion de la lista del resultado del query
        $this->captura('id_periodo_venta_agencia','int4');
        $this->captura('codigo_periodo','varchar');
        $this->captura('id_agencia','int4');
        $this->captura('medio_pago','varchar');
        $this->captura('mes','varchar');
        $this->captura('gestion','varchar');
        $this->captura('id_periodo_venta','int4');
        $this->captura('fecha_ini','varchar');
        $this->captura('fecha_fin','varchar');
		$this->captura('moneda_restrictiva','varchar');
		$this->captura('codigo_int','varchar');
		$this->captura('nombre','varchar');
        $this->captura('fecha_ini2','varchar');
        $this->captura('fecha_fin2','varchar');
        $this->captura('estado','varchar');
        $this->captura('total_credito_mb','numeric');
        $this->captura('total_credito_me','numeric');
		$this->captura('total_boletos_mb','numeric');
        $this->captura('total_boletos_usd','numeric');
		$this->captura('total_comision_mb','numeric');
        $this->captura('total_comision_usd','numeric');
		$this->captura('total_debito_mb','numeric');
        $this->captura('total_debito_usd','numeric');
        $this->captura('total_neto_mb','numeric');
        $this->captura('total_neto_usd','numeric');
		$this->captura('monto_mb','numeric');
        $this->captura('monto_usd','numeric');
        $this->captura('billetes','text');
		


        //Ejecuta la instruccion
        $this->armarConsulta();

        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }
			
	function insertarPeriodoVenta(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='obingresos.ft_periodo_venta_ime';
		$this->transaccion='OBING_PERVEN_INS';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion

		$this->setParametro('id_gestion','id_gestion','int4');
		$this->setParametro('id_tipo_periodo','id_tipo_periodo','integer');


		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function modificarPeriodoVenta(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='obingresos.ft_periodo_venta_ime';
		$this->transaccion='OBING_PERVEN_MOD';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_periodo_venta','id_periodo_venta','int4');
		$this->setParametro('fecha_pago','fecha_pago','date');


		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function eliminarPeriodoVenta(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='obingresos.ft_periodo_venta_ime';
		$this->transaccion='OBING_PERVEN_ELI';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_periodo_venta','id_periodo_venta','int4');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
}
?>