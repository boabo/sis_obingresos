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
        $this->setCount(false);

				$this->setParametro('fecha_fin','fecha_fin','varchar');
				$this->setParametro('fecha_ini','fecha_ini','varchar');

        $this->setParametro('id_agencia','id_agencia','int4');
        $this->setParametro('id_periodo_venta','id_periodo_venta','int4');
        $this->captura('id_agencia','int4');
		$this->captura('nombre','varchar');
        $this->captura('tipo','varchar');
        $this->captura('pnr','varchar');
        $this->captura('fecha','date');
        $this->captura('autorizacion__nro_deposito','text');
        $this->captura('billete','varchar');
        $this->captura('comision','numeric');
        $this->captura('importe','numeric');
        $this->captura('neto','numeric');
        $this->captura('saldo','numeric');
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
        $this->transaccion='OBING_ENT_RE';
        $this->tipo_procedimiento='SEL';//tipo de transaccion
        $this->setCount(false);
        $this->setParametro('id_agencia','id_agencia','int4');
        $this->setParametro('id_periodo_venta','id_periodo_venta','int4');
        //Definicion de la lista del resultado del query
        $this->captura('id_agencia','int4');
        $this->captura('tipo','varchar');
        $this->captura('moneda','varchar');
        $this->captura('monto','numeric');
        $this->captura('monto_mb','numeric');
        $this->captura('aux','varchar');
        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();
        //Devuelve la respuesta
        return $this->respuesta;
    }
    function reporteSaldoVigente(){
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento='obingresos.ft_reporte_cuenta_sel';
        $this->transaccion='OBING_SALVIG_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion

        $this->capturaCount('total_creditos','numeric');
        $this->capturaCount('total_debitos','numeric');
        $this->capturaCount('total_ajustes','numeric');
				$this->capturaCount('total_saldo_con_boleto','numeric');
        $this->capturaCount('total_saldo_sin_boleto','numeric');

        //Definicion de la lista del resultado del query
				$this->setParametro('fecha_fin','fecha_fin','varchar');
        $this->setParametro('fecha_ini','fecha_ini','varchar');
        $this->captura('id_agencia','int4');
        $this->captura('nombre','varchar');
        $this->captura('codigo_int','varchar');
        $this->captura('tipo_agencia','varchar');
        $this->captura('formas_pago','varchar');
        $this->captura('codigo_ciudad','varchar');
        $this->captura('monto_credito','numeric');
        $this->captura('garantia','numeric');
        $this->captura('monto_debito','numeric');
        $this->captura('monto_ajustes','numeric');
				$this->captura('saldo_con_boleto','numeric');
        $this->captura('saldo_sin_boleto','numeric');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //var_dump( $this->respuesta);exit;
        //Devuelve la respuesta
        return $this->respuesta;
    }
    function  reporteEstadoCuentaIng(){
        $this->procedimiento='obingresos.ft_reporte_cuenta_sel';
        $this->transaccion='OBING_CUT_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion
        $this->setCount(false);

        $this->setParametro('id_agencia','id_agencia','int4');
				$this->setParametro('fecha_ini','fecha_ini','varchar');
				$this->setParametro('fecha_fin','fecha_fin','varchar');

        $this->captura('tipo','varchar');
        $this->captura('id_agencia','int4');
        $this->captura('id_periodo_venta','int4');
        $this->captura('nombre','varchar');
        $this->captura('periodo','text');
        $this->captura('monto_debito','numeric');
        $this->captura('monto_deposito','numeric');
        $this->captura('fecha_pago','date');
        $this->captura('fecha','date');
        $this->captura('nro_deposito','varchar');
				$this->captura('garante','numeric');
				$this->captura('nro_deposito_boa','varchar');
        $this->captura('monto_sin_boleta','numeric');

        $this->armarConsulta();
        $this->ejecutarConsulta();

      //  var_dump( $this->respuesta);exit;
        //Devuelve la respuesta
        return $this->respuesta;
    }
    function reporteEstadoMovimiento(){
        $this->procedimiento='obingresos.ft_reporte_cuenta_sel';
        $this->transaccion='OBING_MOV_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion
        $this->setCount(false);

				$this->setParametro('id_agencia','id_agencia','int4');
				$this->setParametro('fecha_ini','fecha_ini','varchar');
				$this->setParametro('fecha_fin','fecha_fin','varchar');
				$this->setParametro('mes_ini','mes_ini','varchar');
				$this->setParametro('dia_ini','dia_ini','varchar');
        $this->setParametro('año_ini','año_ini','varchar');

        $this->captura('credito','varchar');
        $this->captura('debito','varchar');
        $this->captura('nombre','varchar');
        $this->captura('codigo_int','varchar');
        $this->captura('id_agencia','int4');
        $this->captura('id_periodo_venta','int4');
				$this->captura('periodo','text');
        $this->captura('monto_total','numeric');
        $this->captura('monto_total_debito','numeric');
				$this->captura('saldo','numeric');
        $this->captura('saldo2','numeric');

        $this->armarConsulta();
        $this->ejecutarConsulta();
        //  var_dump( $this->respuesta);exit;
        //Devuelve la respuesta
        return $this->respuesta;
    }
    function listarPeriodo(){
        $this->procedimiento='obingresos.ft_reporte_cuenta_sel';
        $this->transaccion='OBING_PER_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion
        $this->setCount(false);

        $this->captura('id_periodo_venta','int4');
        $this->captura('id_gestion','int4');
        $this->captura('periodo','text');
        $this->captura('mes','varchar');
        $this->captura('fecha_ini','date');
        $this->captura('fecha_fin','date');

        $this->armarConsulta();
        $this->ejecutarConsulta();
        //  var_dump( $this->respuesta);exit;
        //Devuelve la respuesta
        return $this->respuesta;
    }
    function reporteGenrealCuenta(){
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento='obingresos.ft_reporte_cuenta_sel';
        $this->transaccion='OBING_RERE_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion
        $this->setCount(false);
        //Definicion de la lista del resultado del query
				$this->setParametro('fecha_fin','fecha_fin','varchar');
        $this->setParametro('fecha_ini','fecha_ini','varchar');
        $this->setParametro('id_lugar','id_lugar','int4');
        $this->setParametro('tipo_agencia','tipo_agencia','varchar');
        $this->setParametro('forma_pago','forma_pago','varchar');
        //Definicion de la lista del resultado del query
        $this->captura('id_agencia','int4');
        $this->captura('nombre','varchar');
        $this->captura('codigo_int','varchar');
        $this->captura('tipo_agencia','varchar');
        $this->captura('formas_pago','varchar');
        $this->captura('codigo_ciudad','varchar');
        $this->captura('monto_creditos','numeric');
        $this->captura('garantia','numeric');
        $this->captura('monto_debitos','numeric');
        $this->captura('monto_ajustes','numeric');
				$this->captura('saldo_con_boleto','numeric');
        $this->captura('saldo_sin_boleto','numeric');
        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //var_dump( $this->respuesta);exit;
        //Devuelve la respuesta
        return $this->respuesta;
    }
}
?>
