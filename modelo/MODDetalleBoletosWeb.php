<?php
class  MODDetalleBoletosWeb extends MODbase{
    function __construct(CTParametro $pParam){
        parent::__construct($pParam);
    }
    function listarReporteNitRazon(){
        //Definicion de variables para ejecucion del procedimientp
            $this->procedimiento='obingresos.ft_detalle_boletos_web_sel';
            $this->transaccion='OBING_DETBOWEB_SEL';
            $this->tipo_procedimiento='SEL';//tipo de transaccion

            $this->setParametro('fecha_ini','fecha_ini','date');
            $this->setParametro('fecha_fin','fecha_fin','date');
            $this->setCount(false);

            $this->captura('fecha_emision', 'date');
            $this->captura('billete', 'varchar');
            $this->captura('entidad_pago', 'varchar');
            $this->captura('nit', 'varchar');
            $this->captura('razon_social', 'varchar');
            $this->captura('importe', 'numeric');
            $this->captura('nit_ingresos', 'varchar');
            $this->captura('razon_ingresos', 'varchar');

            //Ejecuta la instruccion
            $this->armarConsulta();
            $this->ejecutarConsulta();
            //var_dump($this->respuesta);exit;
            //Devuelve la respuesta
            return $this->respuesta;
    }

    function listarConciliacionTotales(){
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento='obingresos.ft_detalle_boletos_web_sel';
        $this->transaccion='OBING_CONBINTOT_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion

        $this->setParametro('fecha_ini','fecha_ini','date');
        $this->setParametro('fecha_fin','fecha_fin','date');
        $this->setParametro('moneda','moneda','varchar');
        $this->setParametro('id_moneda','id_moneda','integer');
        $this->setParametro('banco','banco','varchar');
        $this->setCount(false);

        $this->captura('fecha', 'varchar');
        $this->captura('monto', 'numeric');
        $this->captura('tipo', 'varchar');

        //Ejecuta la instruccion
        $this->armarConsulta();

        
        $this->ejecutarConsulta();

        return $this->respuesta;
    }

    function listarConciliacionDetalle(){
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento='obingresos.ft_detalle_boletos_web_sel';
        $this->transaccion='OBING_CONBINDET_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion

        $this->setParametro('fecha_ini','fecha_ini','date');
        $this->setParametro('fecha_fin','fecha_fin','date');
        $this->setParametro('moneda','moneda','varchar');
        $this->setParametro('id_moneda','id_moneda','integer');
        $this->setParametro('banco','banco','varchar');
        $this->setCount(false);

        $this->captura('fecha_hora', 'varchar');
        $this->captura('fecha', 'varchar');
        $this->captura('pnr', 'varchar');
        $this->captura('monto_ingresos', 'numeric');
        $this->captura('monto_archivos', 'numeric');
        $this->captura('fecha_pago', 'varchar');
        $this->captura('observaciones', 'varchar');

        //Ejecuta la instruccion
        $this->armarConsulta();


        $this->ejecutarConsulta();

        return $this->respuesta;
    }

}
?>