<?php
/**
*@package pXP
*@file gen-MODReporteBancaBoletos.php
*@author  (Ismael Valdivia)
*@date 03-01-2020 10:54:00
*@description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
*/

class MODReporteBancaBoletos extends MODbase{

    function listarDatosBanca(){
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento='obingresos.ft_reporte_banca_boletos_sel';
        $this->transaccion='OBING_BOLEBANC_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion

        $this->capturaCount('total_boa_general','numeric');
        $this->capturaCount('total_agencia_general','numeric');
        $this->capturaCount('total_debito_general','numeric');

        //Definicion de la lista del resultado del query
				$this->setParametro('fecha_fin','fecha_fin','varchar');
        $this->setParametro('fecha_ini','fecha_ini','varchar');

        $this->captura('agencia_id','int4');
        $this->captura('nombre','varchar');
        $this->captura('codigo_int','varchar');
        $this->captura('tipo_agencia','varchar');
        $this->captura('nombre_lugar','varchar');
        $this->captura('codigo_internacional','varchar');
        //$this->captura('fecha_pago_banco','date');
        $this->captura('monto_boa','numeric');
        $this->captura('monto_agencia','numeric');
        $this->captura('total_debito','numeric');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //var_dump( $this->respuesta);exit;
        //Devuelve la respuesta
        return $this->respuesta;
    }

    function ReporteDatosBanca(){
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento='obingresos.ft_reporte_banca_boletos_sel';
        $this->transaccion='OBING_BOLEBANC_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion
        $this->setCount(false);
        $this->capturaCount('total_boa_general','numeric');
        $this->capturaCount('total_agencia_general','numeric');
        $this->capturaCount('total_debito_general','numeric');

        //Definicion de la lista del resultado del query
				$this->setParametro('fecha_fin','fecha_fin','varchar');
        $this->setParametro('fecha_ini','fecha_ini','varchar');

        $this->captura('agencia_id','int4');
        $this->captura('nombre','varchar');
        $this->captura('codigo_int','varchar');
        $this->captura('tipo_agencia','varchar');
        $this->captura('nombre_lugar','varchar');
        $this->captura('codigo_internacional','varchar');
        //$this->captura('fecha_pago_banco','date');
        $this->captura('monto_boa','numeric');
        $this->captura('monto_agencia','numeric');
        $this->captura('total_debito','numeric');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //var_dump( $this->respuesta);exit;
        //Devuelve la respuesta
        return $this->respuesta;
    }

    function ReporteEstadoCuentas(){
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento='obingresos.ft_reporte_banca_boletos_sel';
        $this->transaccion='OBING_BOLEDETBAN_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion
        $this->setCount(false);

        //Definicion de la lista del resultado del query
        $this->setParametro('fecha_fin','fecha_fin','varchar');
        $this->setParametro('fecha_ini','fecha_ini','varchar');
        $this->setParametro('id_agencia','id_agencia','int4');

        $this->captura('id_agencia','int4');
        $this->captura('nombre','varchar');
        $this->captura('codigo_int','varchar');
        $this->captura('transaccion_id','int4');
        $this->captura('pnr','varchar');
        $this->captura('tkt','varchar');
        $this->captura('neto','numeric');
        $this->captura('tasas','numeric');
        $this->captura('monto_total','numeric');
        $this->captura('comision','numeric');
        $this->captura('moneda','varchar');
        $this->captura('fecha_emision','date');
        $this->captura('fecha_transaccion','timestamp');
        $this->captura('fecha_pago_banco','date');
        $this->captura('forma_pago','varchar');
        $this->captura('entidad_pago','varchar');
        $this->captura('estado','varchar');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //var_dump( $this->respuesta);exit;
        //Devuelve la respuesta
        return $this->respuesta;
    }

}
?>
