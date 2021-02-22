<?php
/**
 *@package pXP
 *@file gen-MODFacturaNoUtilizada.php
 *@author  Maylee Perez Pastor
 *@date 05-05-2020 20:37:45
 *@description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
 */

class MODFacturaNoUtilizada extends MODbase{

    function __construct(CTParametro $pParam){
        parent::__construct($pParam);
    }

    function listarFacturaNoUtilizada(){
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento='obingresos.ft_factura_no_utilizada_sel';
        $this->transaccion='OBING_FACMAN_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion

        //Definicion de la lista del resultado del query
        //$this->captura('id_lugar_pais','int4');
        //$this->captura('id_lugar_depto','int4');
        $this->captura('id_factura_no_utilizada','int4');
        $this->captura('id_punto_venta','int4');
        $this->captura('id_estado_factura','int4');
        $this->captura('tipo_cambio','numeric');
        $this->captura('id_moneda','int4');
        $this->captura('nombre','varchar');
        $this->captura('nit','varchar');
        $this->captura('observaciones','varchar');
        $this->captura('id_concepto_ingas','int4');

        $this->captura('estado_reg','varchar');
        $this->captura('id_usuario_reg','int4');
        $this->captura('fecha_reg','timestamp');
        $this->captura('id_usuario_mod','int4');
        $this->captura('fecha_mod','timestamp');
        $this->captura('usr_reg','varchar');
        $this->captura('usr_mod','varchar');
        $this->captura('nom_punto_venta','varchar');

        $this->captura('id_dosificacion','int4');
        $this->captura('estacion','varchar');
        $this->captura('tipo','varchar');
        $this->captura('nro_tramite','varchar');
        $this->captura('id_sucursal','int4');
        $this->captura('nom_sucursal','text');
        $this->captura('nombre_sucursal','varchar');
        $this->captura('fecha','date');
        $this->captura('fecha_dosificacion','date');
        $this->captura('nroaut','varchar');
        $this->captura('inicial','integer');
        $this->captura('final','integer');

        $this->captura('desc_moneda','varchar');


        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();
//var_dump('llegabd', $this->respuesta);
        //Devuelve la respuesta
        return $this->respuesta;
    }

    function insertarFacturaNoUtilizada(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='obingresos.ft_factura_no_utilizada_ime';
        $this->transaccion='OBING_FACMAN_INS';
        $this->tipo_procedimiento='IME';

        //Define los parametros para la funcion
        //$this->setParametro('id_lugar_pais','id_lugar_pais','int4');
        //$this->setParametro('id_lugar_depto','id_lugar_depto','int4');
        $this->setParametro('id_punto_venta','id_punto_venta','int4');
        $this->setParametro('id_sucursal','id_sucursal','int4');
        $this->setParametro('id_dosificacion','id_dosificacion','int4');
        $this->setParametro('inicial','inicial','numeric');
        $this->setParametro('final','final','numeric');

        $this->setParametro('id_estado_factura','id_estado_factura','int4');
        $this->setParametro('fecha','fecha','date');
        $this->setParametro('id_moneda','id_moneda','int4');

        $this->setParametro('tipo_cambio','tipo_cambio','numeric');
        $this->setParametro('nombre','nombre','varchar');
        $this->setParametro('nit','nit','varchar');
        $this->setParametro('observaciones','observaciones','varchar');
        //$this->setParametro('id_concepto_ingas','id_concepto_ingas','int4');

        $this->setParametro('estado_reg','estado_reg','varchar');

        $this->setParametro('nroaut','nroaut','varchar');

        //$this->setParametro('estacion','estacion','varchar');



        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function modificarFacturaNoUtilizada(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='obingresos.ft_factura_no_utilizada_ime';
        $this->transaccion='OBING_FACMAN_MOD';
        $this->tipo_procedimiento='IME';

        //Define los parametros para la funcion
        $this->setParametro('id_factura_no_utilizada','id_factura_no_utilizada','int4');
        /*$this->setParametro('id_lugar_pais','id_lugar_pais','int4');
        $this->setParametro('id_lugar_depto','id_lugar_depto','int4');*/
        $this->setParametro('id_punto_venta','id_punto_venta','int4');
        $this->setParametro('id_sucursal','id_sucursal','int4');
        $this->setParametro('id_dosificacion','id_dosificacion','int4');
        $this->setParametro('inicial','inicial','numeric');
        $this->setParametro('final','final','numeric');
        $this->setParametro('fecha','fecha','date');
        $this->setParametro('id_moneda','id_moneda','int4');
        $this->setParametro('tipo_cambio','tipo_cambio','numeric');
        $this->setParametro('nombre','nombre','varchar');
        $this->setParametro('nit','nit','varchar');
        $this->setParametro('observaciones','observaciones','varchar');
        //$this->setParametro('id_concepto_ingas','id_concepto_ingas','int4');

        $this->setParametro('estado_reg','estado_reg','varchar');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function eliminarFacturaNoUtilizada(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='obingresos.ft_factura_no_utilizada_ime';
        $this->transaccion='OBING_FACMAN_ELI';
        $this->tipo_procedimiento='IME';

        //Define los parametros para la funcion
        $this->setParametro('id_factura_no_utilizada','id_factura_no_utilizada','int4');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function obtenerNroFacInicial(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='obingresos.ft_factura_no_utilizada_ime';
        $this->transaccion='OBING_NFACINI_GET';
        $this->tipo_procedimiento='IME';

        //Define los parametros para la funcion
        $this->setParametro('id_dosificacion','id_dosificacion','int4');
        $this->setParametro('final','final','int4');

        //Ejecuta la instruccion
        $this->armarConsulta();
        //echo $this->consulta;exit;
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

}
?>