<?php
/**
 *@package pXP
 *@file gen-MODEstablecimientoPuntoVenta.php
 *@author  (admin)
 *@date 17-03-2021 11:14:41
 *@description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
 */

class MODEstablecimientoPuntoVenta extends MODbase{

    function __construct(CTParametro $pParam){
        parent::__construct($pParam);
    }

    function listarEstablecimientoPuntoVenta(){
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento='obingresos.ft_establecimiento_punto_venta_sel';
        $this->transaccion='OBING_ESTPVEN_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion

        //Definicion de la lista del resultado del query
        $this->captura('id_establecimiento_punto_venta','int4');
        $this->captura('estado_reg','varchar');
        $this->captura('codigo_estable','varchar');
        $this->captura('nombre_estable','varchar');
        $this->captura('id_punto_venta','int4');
        $this->captura('id_usuario_reg','int4');
        $this->captura('fecha_reg','timestamp');
        $this->captura('id_usuario_ai','int4');
        $this->captura('usuario_ai','varchar');
        $this->captura('id_usuario_mod','int4');
        $this->captura('fecha_mod','timestamp');
        $this->captura('usr_reg','varchar');
        $this->captura('usr_mod','varchar');
        $this->captura('nombre_office','varchar');
        $this->captura('tipo_estable','varchar');
        $this->captura('comercio','varchar');
        $this->captura('nombre_lugar','varchar');
        $this->captura('nombre_iata','varchar');
        $this->captura('iata_code','varchar');
        $this->captura('direccion_estable','text');
        $this->captura('id_lugar','int4');
        $this->captura('id_stage_pv','int4');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function insertarEstablecimientoPuntoVenta(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='obingresos.ft_establecimiento_punto_venta_ime';
        $this->transaccion='OBING_ESTPVEN_INS';
        $this->tipo_procedimiento='IME';

        //Define los parametros para la funcion
        $this->setParametro('estado_reg','estado_reg','varchar');
        $this->setParametro('codigo_estable','codigo_estable','varchar');
        $this->setParametro('nombre_estable','nombre_estable','varchar');
        $this->setParametro('id_punto_venta','id_punto_venta','int4');
        $this->setParametro('tipo_estable','tipo_estable','varchar');
        $this->setParametro('id_stage_pv','id_stage_pv','integer');
        $this->setParametro('id_lugar','id_lugar','integer');
        $this->setParametro('direccion_estable','direccion_estable','text');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function modificarEstablecimientoPuntoVenta(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='obingresos.ft_establecimiento_punto_venta_ime';
        $this->transaccion='OBING_ESTPVEN_MOD';
        $this->tipo_procedimiento='IME';

        //Define los parametros para la funcion
        $this->setParametro('id_establecimiento_punto_venta','id_establecimiento_punto_venta','int4');
        $this->setParametro('estado_reg','estado_reg','varchar');
        $this->setParametro('codigo_estable','codigo_estable','varchar');
        $this->setParametro('nombre_estable','nombre_estable','varchar');
        $this->setParametro('id_punto_venta','id_punto_venta','int4');
        $this->setParametro('tipo_estable','tipo_estable','varchar');
        $this->setParametro('id_stage_pv','id_stage_pv','integer');
        $this->setParametro('id_lugar','id_lugar','integer');
        $this->setParametro('direccion_estable','direccion_estable','text');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function eliminarEstablecimientoPuntoVenta(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='obingresos.ft_establecimiento_punto_venta_ime';
        $this->transaccion='OBING_ESTPVEN_ELI';
        $this->tipo_procedimiento='IME';

        //Define los parametros para la funcion
        $this->setParametro('id_establecimiento_punto_venta','id_establecimiento_punto_venta','int4');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function listarPuntoVentaStage(){
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento='obingresos.ft_establecimiento_punto_venta_sel';
        $this->transaccion='OBING_PSTAGE_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion

        //Definicion de la lista del resultado del query
        $this->captura('id_stage_pv','int4');
        $this->captura('stage_id_pv','int4');
        $this->captura('iata_area','varchar');
        $this->captura('iata_zone','varchar');
        $this->captura('iata_zone_name','varchar');
        $this->captura('country_code','varchar');
        $this->captura('country_name','varchar');
        $this->captura('city_code','varchar');
        $this->captura('city_name','varchar');
        $this->captura('accounting_station','varchar');
        $this->captura('sale_type','varchar');
        $this->captura('sale_channel','varchar');
        $this->captura('tipo_pos','varchar');
        $this->captura('iata_code','varchar');
        $this->captura('iata_status','varchar');
        $this->captura('osd','varchar');
        $this->captura('office_id','varchar');
        $this->captura('gds','varchar');
        $this->captura('nit','varchar');
        $this->captura('name_pv','varchar');
        $this->captura('address','varchar');
        $this->captura('phone_number','varchar');


        //Ejecuta la instruccion
        $this->armarConsulta();//var_dump($this->consulta);exit;
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

}
?>