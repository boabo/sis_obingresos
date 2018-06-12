<?php
/**
*@package pXP
*@file gen-MODReporteCuenta.php
*@author  (miguel.mamani)
*@date 11-06-2018 15:14:58
*@description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
*/

class MODReporteCuenta extends MODbase{
	
	function __construct(CTParametro $pParam){
		parent::__construct($pParam);
	}
			
	function listarReporteCuenta(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='obingresos.ft_reporte_cuenta_sel';
		$this->transaccion='OBING_ENT_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion
        $this->setParametro('id_agencia','id_agencia','int4');
        $this->setCount(false);
				
		//Definicion de la lista del resultado del query
		$this->captura('id_reporte','int4');
		$this->captura('nombre','varchar');
		$this->captura('neto','numeric');
		$this->captura('fecha','date');
		$this->captura('comision','numeric');
		$this->captura('autorizacion__nro_deposito','text');
		$this->captura('id_agencia','int4');
		$this->captura('pnr','varchar');
		$this->captura('billete','varchar');
		$this->captura('importe','numeric');
		$this->captura('tipo','varchar');
		
		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();
		//var_dump($this->respuesta);exit;
		//Devuelve la respuesta
		return $this->respuesta;
	}
    function ResumenEstadoCC(){
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento='obingresos.ft_reporte_cuenta_sel';
        $this->transaccion='OBING_ENT_SEL';
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
			

			
}
?>