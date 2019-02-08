<?php
/**
*@package pXP
*@file gen-MODReporteCuenta.php
*@author  (Ismael.Valdivia)
*@date 11-06-2018 15:14:58
*@description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
*/

class MODControlAgencias extends MODbase{

	function __construct(CTParametro $pParam){
		parent::__construct($pParam);
	}

  function reporteSaldoVigente(){
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento='obingresos.ft_control_agencia_sel';
        $this->transaccion='OBING_LISTA_SEL';
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

    function reporteGenrealCuenta(){
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento='obingresos.ft_control_agencia_sel';
        $this->transaccion='OBING_REPORIS_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion
        $this->setCount(false);
        //Definicion de la lista del resultado del query
				$this->setParametro('fecha_fin','fecha_fin','varchar');
				$this->setParametro('nombre','nombre','varchar');
				$this->setParametro('id_agencia','id_agencia','int4');
        //$this->setParametro('fecha_ini','fecha_ini','varchar');
        //Definicion de la lista del resultado del query
        $this->captura('id_agencia','int4');
				$this->captura('id_periodo_venta','int4');
				$this->captura('tipo','varchar');

        $this->captura('depositos_con_saldos','numeric');
        $this->captura('depositos','numeric');
        $this->captura('debitos','numeric');
				$this->captura('saldo_arrastrado','numeric');
        $this->captura('periodo','varchar'); 
        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //var_dump( $this->respuesta);exit;
        //Devuelve la respuesta
        return $this->respuesta;
    }
		function listarverificarMoneda(){
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento='obingresos.ft_control_agencia_sel';
        $this->transaccion='OBING_MONE_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion
        //$this->setCount(false);

        //Definicion de la lista del resultado del query
				//$this->captura('id_movimiento_entidad','int4');

        $this->captura('id_agencia','int4');
        $this->captura('id_moneda','int4');
        $this->captura('codigo','varchar');
				$this->captura('moneda','varchar');
				$this->captura('nombre','varchar');


        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();
       // var_dump($this->respuesta);exit;
        //Devuelve la respuesta
        return $this->respuesta;
    }
}
?>
