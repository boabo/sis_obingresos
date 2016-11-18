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

}
?>