<?php
class  MODDepositoPeriodo extends MODbase{
    function __construct(CTParametro $pParam){
        parent::__construct($pParam);
    }

    function listarDepositosPeriodo(){
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento='obingresos.ft_depositos_periodo_sel';
        $this->transaccion='OB_DE_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion
        //$this->setCount(false);
        $this->capturaCount('suma_total','numeric');

        //Definicion de la lista del resultado del query
        $this->captura('id_movimiento_entidad','int4');
        $this->captura('id_agencia','int4');
        $this->captura('id_periodo_venta','int4');
        $this->captura('gestion','varchar');
        $this->captura('mes','varchar');
        $this->captura('fecha_ini','date');
        $this->captura('fecha_fin','date');
        $this->captura('fecha','date');
        $this->captura('autorizacion__nro_deposito','varchar');
        $this->captura('monto_total','numeric');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();
       // var_dump($this->respuesta);exit;
        //Devuelve la respuesta
        return $this->respuesta;
    }




}
?>