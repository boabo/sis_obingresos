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
        $this->captura('id_deposito','int4');
        $this->captura('nombre','varchar');

       $this->captura('estado_reg','varchar');
        $this->captura('nro_deposito','varchar');
        $this->captura('nro_deposito_boa','varchar');
        $this->captura('monto_deposito','numeric');
        $this->captura('id_agencia','int4');
        $this->captura('fecha','date');
        $this->captura('estado','varchar');
        $this->captura('id_apertura_cierre_caja','int4');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();
       // var_dump($this->respuesta);exit;
        //Devuelve la respuesta
        return $this->respuesta;
    }




}
?>
