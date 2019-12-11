<?php
/**
 *@package pXP
 *@file gen-MODBoleto.php
 *@author  (jrivera)
 *@date 06-01-2016 22:42:25
 *@description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
 */
class MODBoleto extends MODbase{

    function __construct(CTParametro $pParam){
        parent::__construct($pParam);
    }

    function listarBoleto(){
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento='obingresos.ft_boleto_sel';
        $this->transaccion='OBING_BOL_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion

        //Definicion de la lista del resultado del query
        $this->captura('id_boleto','int4');
        $this->captura('fecha_emision','date');
        $this->captura('codigo_noiata','varchar');
        $this->captura('cupones','int4');
        $this->captura('ruta','varchar');
        $this->captura('estado','varchar');
        $this->captura('id_agencia','int4');
        $this->captura('moneda','varchar');
        $this->captura('total','numeric');
        $this->captura('pasajero','varchar');
        $this->captura('id_moneda_boleto','int4');
        $this->captura('estado_reg','varchar');
        $this->captura('gds','varchar');
        $this->captura('comision','numeric');
        $this->captura('codigo_agencia','varchar');
        $this->captura('neto','numeric');
        $this->captura('tipopax','varchar');
        $this->captura('origen','varchar');
        $this->captura('destino','varchar');
        $this->captura('retbsp','varchar');
        $this->captura('monto_pagado_moneda_boleto','numeric');
        $this->captura('tipdoc','varchar');
        $this->captura('liquido','numeric');
        $this->captura('nro_boleto','varchar');
        $this->captura('id_usuario_ai','int4');
        $this->captura('id_usuario_reg','int4');
        $this->captura('fecha_reg','timestamp');
        $this->captura('usuario_ai','varchar');
        $this->captura('id_usuario_mod','int4');
        $this->captura('fecha_mod','timestamp');
        $this->captura('usr_reg','varchar');
        $this->captura('usr_mod','varchar');
        $this->captura('nombre_agencia','varchar');

        $this->captura('id_forma_pago','integer');
        $this->captura('forma_pago','varchar');
        $this->captura('monto_forma_pago','numeric');
        $this->captura('codigo_forma_pago','varchar');
        $this->captura('numero_tarjeta','varchar');
        $this->captura('codigo_tarjeta','varchar');
        $this->captura('ctacte','varchar');
        $this->captura('moneda_fp1','varchar');

        $this->captura('id_forma_pago2','integer');
        $this->captura('forma_pago2','varchar');
        $this->captura('monto_forma_pago2','numeric');
        $this->captura('codigo_forma_pago2','varchar');
        $this->captura('numero_tarjeta2','varchar');
        $this->captura('codigo_tarjeta2','varchar');
        $this->captura('ctacte2','varchar');
        $this->captura('moneda_fp2','varchar');


        $this->captura('tc','numeric');
        $this->captura('moneda_sucursal','varchar');
        $this->captura('ruta_completa','varchar');
        $this->captura('voided','varchar');
        $this->captura('monto_total_fp','numeric');
        $this->captura('mensaje_error','text');
        $this->captura('id_boleto_vuelo','integer');
        $this->captura('vuelo_retorno','varchar');
        $this->captura('localizador','varchar');


        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();
        //var_dump($this->consulta); exit;
        //Devuelve la respuesta
        return $this->respuesta;
    }
    function listarPNRBoleto(){
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento='obingresos.ft_boleto_sel';
        $this->transaccion='OBING_PNRBOL_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion
        //Definicion de la lista del resultado del query
        $this->captura('localizador','varchar');
        $this->captura('total','numeric');
        $this->captura('comision','numeric');
        $this->captura('liquido','numeric');
        $this->captura('id_moneda_boleto','int4');
        $this->captura('moneda','varchar');
        $this->captura('neto','numeric');
        $this->captura('origen','varchar');
        $this->captura('destino','varchar');
        $this->captura('fecha_emision','date');
        $this->captura('boletos','text');
        $this->captura('pasajeros','text');
        $this->captura('id_forma_pago','int4');
        $this->captura('forma_pago','varchar');
        $this->captura('monto_forma_pago','numeric');
        $this->captura('id_forma_pago2','int4');
        $this->captura('forma_pago2','varchar');
        $this->captura('monto_forma_pago2','numeric');
        /*
        $this->captura('moneda_fp1','varchar');
        $this->captura('id_forma_pago2','integer');
        $this->captura('forma_pago2','varchar');
        $this->captura('monto_forma_pago2','numeric');
        $this->captura('codigo_forma_pago2','varchar');
        $this->captura('numero_tarjeta2','varchar');
        $this->captura('codigo_tarjeta2','varchar');
        $this->captura('ctacte2','varchar');
        $this->captura('moneda_fp2','varchar');
        $this->captura('tc','numeric');
        $this->captura('moneda_sucursal','varchar');
        $this->captura('ruta_completa','varchar');
        $this->captura('monto_total_fp','numeric');
        $this->captura('mensaje_error','text');
        $this->captura('id_boleto_vuelo','integer');
        $this->captura('vuelo_retorno','varchar');
        */
        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();
        //var_dump($this->consulta); exit;
        //Devuelve la respuesta
        return $this->respuesta;
    }
    function listarBoletosEmitidosAmadeus(){
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento='obingresos.ft_boleto_sel';
        $this->transaccion='OBING_BOLEMI_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion
        //Definicion de la lista del resultado del query
        $this->captura('id_boleto_amadeus','integer');
        $this->captura('localizador','varchar');
        $this->captura('total','numeric');
        $this->captura('total_moneda_extranjera','numeric');
        $this->captura('liquido','numeric');
        $this->captura('id_moneda_boleto','int4');
        $this->captura('moneda','varchar');
        $this->captura('moneda_sucursal','varchar');
        $this->captura('tc','numeric');
        $this->captura('neto','numeric');
        $this->captura('comision','numeric');
        $this->captura('fecha_emision','date');
        $this->captura('tipo_comision','varchar');
        $this->captura('nro_boleto','varchar');
        $this->captura('pasajero','varchar');
        $this->captura('voided','varchar');
        $this->captura('estado','varchar');
        $this->captura('agente_venta','varchar');
        $this->captura('codigo_agente','varchar');
        $this->captura('forma_pago_amadeus','varchar');
        $this->captura('id_forma_pago','int4');
        $this->captura('moneda_fp1','varchar');
        $this->captura('forma_pago','varchar');
        $this->captura('codigo_forma_pago','varchar');
        $this->captura('numero_tarjeta','varchar');
        $this->captura('codigo_tarjeta','varchar');
        $this->captura('mco','varchar');
        $this->captura('id_auxiliar','int4');
        $this->captura('nombre_auxiliar','varchar');
        $this->captura('monto_forma_pago','numeric');
        $this->captura('id_forma_pago2','int4');
        $this->captura('moneda_fp2','varchar');
        $this->captura('forma_pago2','varchar');
        $this->captura('codigo_forma_pago2','varchar');
        $this->captura('numero_tarjeta2','varchar');
        $this->captura('codigo_tarjeta2','varchar');
        $this->captura('mco2','varchar');
        $this->captura('id_auxiliar2','int4');
        $this->captura('nombre_auxiliar2','varchar');
        $this->captura('monto_forma_pago2','numeric');
        $this->captura('ffid_consul','varchar');
        $this->captura('voucher_consu','varchar');

        $this->captura('trans_code','varchar');
        $this->captura('trans_issue_indicator','varchar');
        $this->captura('punto_venta','varchar');
        $this->captura('trans_code_exch','varchar');
        $this->captura('impreso','varchar');

        //Ejecuta la instruccion
        $this->armarConsulta();//echo($this->consulta); exit;
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }
    function listarBoletoAmadeus(){
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento='obingresos.ft_boleto_amadeus_sel';
        $this->transaccion='OBING_BOLREPAMA_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion
        //Definicion de la lista del resultado del query
        $this->captura('id_boleto_amadeus','int4');
        $this->captura('fecha_emision','date');
        $this->captura('estado','varchar');
        $this->captura('moneda','varchar');
        $this->captura('total','numeric');
        $this->captura('pasajero','varchar');
        $this->captura('id_moneda_boleto','int4');
        $this->captura('estado_reg','varchar');
        $this->captura('neto','numeric');
        $this->captura('liquido','numeric');
        $this->captura('nro_boleto','varchar');
        $this->captura('id_usuario_ai','int4');
        $this->captura('id_usuario_reg','int4');
        $this->captura('fecha_reg','timestamp');
        $this->captura('usuario_ai','varchar');
        $this->captura('id_usuario_mod','int4');
        $this->captura('fecha_mod','timestamp');
        $this->captura('usr_reg','varchar');
        $this->captura('usr_mod','varchar');
        $this->captura('id_forma_pago','integer');
        $this->captura('forma_pago','varchar');
        $this->captura('forma_pago_amadeus','varchar');
        $this->captura('monto_forma_pago','numeric');
        $this->captura('codigo_forma_pago','varchar');
        $this->captura('numero_tarjeta','varchar');
        $this->captura('codigo_tarjeta','varchar');
        $this->captura('ctacte','varchar');
        $this->captura('moneda_fp1','varchar');
        $this->captura('id_forma_pago2','integer');
        $this->captura('forma_pago2','varchar');
        $this->captura('forma_pago_amadeus2','varchar');
        $this->captura('monto_forma_pago2','numeric');
        $this->captura('codigo_forma_pago2','varchar');
        $this->captura('numero_tarjeta2','varchar');
        $this->captura('codigo_tarjeta2','varchar');
        $this->captura('ctacte2','varchar');
        $this->captura('moneda_fp2','varchar');
        $this->captura('voided','varchar');
        $this->captura('monto_total_fp','numeric');
        $this->captura('localizador','varchar');
        $this->captura('forma_pag_amadeus','varchar');
        $this->captura('officeid','varchar');
        $this->captura('codigo_iata','varchar');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();
        //var_dump($this->consulta); exit;
        //Devuelve la respuesta
        return $this->respuesta;
    }
    function obtenerOfficeID(){
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento='obingresos.ft_boleto_sel';
        $this->transaccion='OBING_PNRBOL_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion
        //Define los parametros para la funcion
        $this->setParametro('id_punto_venta','id_punto_venta','int4');
        //Definicion de la lista del resultado del query
        $this->captura('officeID','varchar');
        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();
        //Devuelve la respuesta
        return $this->respuesta;
    }
    function insertarBoleto(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='obingresos.ft_boleto_ime';
        $this->transaccion='OBING_BOL_INS';
        $this->tipo_procedimiento='IME';

        //Define los parametros para la funcion
        $this->setParametro('fecha_emision','fecha_emision','date');
        $this->setParametro('agtnoiata','agtnoiata','varchar');
        $this->setParametro('cupones','cupones','int4');
        $this->setParametro('ruta','ruta','varchar');
        $this->setParametro('estado','estado','varchar');
        $this->setParametro('id_agencia','id_agencia','int4');
        $this->setParametro('moneda','moneda','varchar');
        $this->setParametro('total','total','numeric');
        $this->setParametro('pasajero','pasajero','varchar');
        $this->setParametro('id_moneda_boleto','id_moneda_boleto','int4');
        $this->setParametro('estado_reg','estado_reg','varchar');
        $this->setParametro('gds','gds','varchar');
        $this->setParametro('comision','comision','numeric');
        $this->setParametro('agt','agt','varchar');
        $this->setParametro('neto','neto','numeric');
        $this->setParametro('tipopax','tipopax','varchar');
        $this->setParametro('origen','origen','varchar');
        $this->setParametro('destino','destino','varchar');
        $this->setParametro('retbsp','retbsp','varchar');
        $this->setParametro('monto_pagado_moneda_boleto','monto_pagado_moneda_boleto','numeric');
        $this->setParametro('tipdoc','tipdoc','varchar');
        $this->setParametro('liquido','liquido','numeric');
        $this->setParametro('nro_boleto','nro_boleto','varchar');
        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();
        //Devuelve la respuesta
        return $this->respuesta;
    }
    function anularBoleto(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='obingresos.ft_boleto_ime';
        $this->transaccion='OBING_ANUBOL_UPD';
        $this->tipo_procedimiento='IME';
        //Define los parametros para la funcion
        $this->setParametro('id_boleto_amadeus','id_boleto_amadeus','varchar');
        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();
        //Devuelve la respuesta
        return $this->respuesta;
    }
    function modificarBoletoVenta(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='obingresos.ft_boleto_ime';
        $this->transaccion='OBING_BOLVEN_UPD';
        $this->tipo_procedimiento='IME';
        //Define los parametros para la funcion
        $this->setParametro('id_boleto','id_boleto','integer');
        $this->setParametro('id_forma_pago','id_forma_pago','integer');
        $this->setParametro('monto_forma_pago','monto_forma_pago','numeric');
        $this->setParametro('numero_tarjeta','numero_tarjeta','varchar');
        $this->setParametro('codigo_tarjeta','codigo_tarjeta','varchar');
        $this->setParametro('ctacte','ctacte','varchar');
        $this->setParametro('id_forma_pago2','id_forma_pago2','integer');
        $this->setParametro('monto_forma_pago2','monto_forma_pago2','numeric');
        $this->setParametro('numero_tarjeta2','numero_tarjeta2','varchar');
        $this->setParametro('codigo_tarjeta2','codigo_tarjeta2','varchar');
        $this->setParametro('ctacte2','ctacte2','varchar');
        $this->setParametro('comision','comision','numeric');
        $this->setParametro('tipo_comision','tipo_comision','varchar');
        $this->setParametro('estado','estado','varchar');
        $this->setParametro('id_boleto_vuelo','id_boleto_vuelo','integer');
        $this->setParametro('id_punto_venta','id_punto_venta','integer');
        $this->setParametro('id_auxiliar','id_auxiliar','integer');
        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();
        //Devuelve la respuesta
        return $this->respuesta;
    }
    function modificarBoletoAmadeusVenta(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='obingresos.ft_boleto_ime';
        $this->transaccion='OBING_BOLAMAVEN_UPD';
        $this->tipo_procedimiento='IME';
        //Define los parametros para la funcion
        $this->setParametro('id_boleto_amadeus','id_boleto_amadeus','integer');
        $this->setParametro('id_forma_pago','id_forma_pago','integer');
        $this->setParametro('monto_forma_pago','monto_forma_pago','numeric');
        $this->setParametro('numero_tarjeta','numero_tarjeta','varchar');
        $this->setParametro('codigo_tarjeta','codigo_tarjeta','varchar');

        /*-------------Aumentando el nro_cupon y nro_cuota---------------*/
        $this->setParametro('nro_cupon','nro_cupon','varchar');
        $this->setParametro('nro_cuota','nro_cuota','varchar');
        $this->setParametro('nro_cupon_2','nro_cupon_2','varchar');
        $this->setParametro('nro_cuota_2','nro_cuota_2','varchar');
        /*--------------------------------------------------------------*/
        $this->setParametro('ctacte','ctacte','varchar');
        $this->setParametro('id_forma_pago2','id_forma_pago2','integer');
        $this->setParametro('monto_forma_pago2','monto_forma_pago2','numeric');
        $this->setParametro('numero_tarjeta2','numero_tarjeta2','varchar');
        $this->setParametro('codigo_tarjeta2','codigo_tarjeta2','varchar');
        $this->setParametro('ctacte2','ctacte2','varchar');
        $this->setParametro('comision','comision','numeric');
        $this->setParametro('tipo_comision','tipo_comision','varchar');
        $this->setParametro('estado','estado','varchar');
        $this->setParametro('id_punto_venta','id_punto_venta','integer');
        $this->setParametro('id_auxiliar','id_auxiliar','integer');
        $this->setParametro('id_auxiliar2','id_auxiliar2','integer');
        $this->setParametro('mco','mco','varchar');
        $this->setParametro('mco2','mco2','varchar');
        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();
        //Devuelve la respuesta
        return $this->respuesta;
    }
    function modificarFpPNRBoleto(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='obingresos.ft_boleto_ime';
        $this->transaccion='OBING_MODFPPNR_UPD';
        $this->tipo_procedimiento='IME';
        //Define los parametros para la funcion
        $this->setParametro('localizador','localizador','varchar');
        $this->setParametro('total','total','numeric');
        $this->setParametro('moneda','moneda','varchar');
        $this->setParametro('id_forma_pago','id_forma_pago','integer');
        $this->setParametro('monto_forma_pago','monto_forma_pago','varchar');
        $this->setParametro('numero_tarjeta','numero_tarjeta','varchar');
        $this->setParametro('codigo_tarjeta','codigo_tarjeta','varchar');
        $this->setParametro('ctacte','ctacte','varchar');
        $this->setParametro('id_forma_pago2','id_forma_pago2','integer');
        $this->setParametro('monto_forma_pago2','monto_forma_pago2','numeric');
        $this->setParametro('numero_tarjeta2','numero_tarjeta2','varchar');
        $this->setParametro('codigo_tarjeta2','codigo_tarjeta2','varchar');
        $this->setParametro('ctacte2','ctacte2','varchar');
        $this->setParametro('id_punto_venta','id_punto_venta','integer');
        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();
        //Devuelve la respuesta
        return $this->respuesta;
    }
    function modificarFpGrupo(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='obingresos.ft_boleto_ime';
        $this->transaccion='OBING_MODFPGRUPO_UPD';
        $this->tipo_procedimiento='IME';
        //Define los parametros para la funcion
        $this->setParametro('ids_seleccionados','ids_seleccionados','varchar');
        $this->setParametro('id_forma_pago','id_forma_pago','integer');
        $this->setParametro('monto_forma_pago','monto_forma_pago','numeric');
        $this->setParametro('numero_tarjeta','numero_tarjeta','varchar');
        $this->setParametro('ctacte','ctacte','varchar');
        $this->setParametro('id_forma_pago2','id_forma_pago2','integer');
        $this->setParametro('monto_forma_pago2','monto_forma_pago2','numeric');
        $this->setParametro('numero_tarjeta2','numero_tarjeta2','varchar');
        $this->setParametro('ctacte2','ctacte2','varchar');
        $this->setParametro('tipo_comision','tipo_comision','varchar');
        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();
        //Devuelve la respuesta
        return $this->respuesta;
    }
    function modificarAmadeusFpGrupo(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='obingresos.ft_boleto_ime';
        $this->transaccion='OBING_MODAMAFPGR_UPD';
        $this->tipo_procedimiento='IME';
        //Define los parametros para la funcion
        $this->setParametro('ids_seleccionados','ids_seleccionados','varchar');
        $this->setParametro('id_forma_pago','id_forma_pago','integer');
        $this->setParametro('monto_forma_pago','monto_forma_pago','numeric');
        $this->setParametro('numero_tarjeta','numero_tarjeta','varchar');
        $this->setParametro('codigo_tarjeta','codigo_tarjeta','varchar');
        $this->setParametro('ctacte','ctacte','varchar');
        $this->setParametro('id_auxiliar','id_auxiliar','integer');
        $this->setParametro('id_forma_pago2','id_forma_pago2','integer');
        $this->setParametro('monto_forma_pago2','monto_forma_pago2','numeric');
        $this->setParametro('numero_tarjeta2','numero_tarjeta2','varchar');
        $this->setParametro('codigo_tarjeta2','codigo_tarjeta2','varchar');
        $this->setParametro('ctacte2','ctacte2','varchar');
        $this->setParametro('id_auxiliar2','id_auxiliar2','integer');
        $this->setParametro('tipo_comision','tipo_comision','varchar');
        $this->setParametro('mco','mco','varchar');
        $this->setParametro('mco2','mco2','varchar');
        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();
        //Devuelve la respuesta
        return $this->respuesta;
    }
    function insertarBoletoServicio(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='obingresos.ft_boleto_ime';
        $this->transaccion='OBING_BOLSERV_INS';
        $this->tipo_procedimiento='IME';

        //Define los parametros para la funcion
        $this->setParametro('id_boleto','id_boleto','integer');//ok
        $this->setParametro('id_punto_venta','id_punto_venta','integer');//ok
        $this->setParametro('nro_boleto','nro_boleto','varchar');//ok
        $this->setParametro('fecha_emision','fecha_emision','varchar');//ok
        $this->setParametro('pasajero','pasajero','varchar');//ok
        $this->setParametro('total','total','numeric');//ok
        $this->setParametro('moneda','moneda','varchar');//ok
        $this->setParametro('neto','neto','numeric');//ok
        $this->setParametro('endoso','endoso','varchar');//ok
        $this->setParametro('origen','origen','varchar');//ok
        $this->setParametro('destino','destino','varchar');//ok
        $this->setParametro('cupones','cupones','integer');//ok
        $this->setParametro('impuestos','impuestos','varchar');//ok
        $this->setParametro('tasas','tasas','varchar');//ok

        $this->setParametro('fp','fp','varchar');
        $this->setParametro('moneda_fp','moneda_fp','varchar');
        $this->setParametro('valor_fp','valor_fp','varchar');
        $this->setParametro('tarjeta_fp','tarjeta_fp','varchar');
        $this->setParametro('autorizacion_fp','autorizacion_fp','varchar');
        $this->setParametro('rutas','rutas','varchar');

        $this->setParametro('ruta_completa','ruta_completa','varchar');
        $this->setParametro('vuelos','vuelos','text');
        $this->setParametro('localizador','localizador','varchar');
        $this->setParametro('identificacion','identificacion','varchar');
        $this->setParametro('fare_calc','fare_calc','text');
        $this->setParametro('vuelos2','vuelos2','text');
        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();
        //Devuelve la respuesta
        return $this->respuesta;
    }
    function insertarBoletoServicioAmadeus(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='obingresos.ft_boleto_ime';
        $this->transaccion='OBING_BOLSERVAMA_INS';
        $this->tipo_procedimiento='IME';
        //Define los parametros para la funcion
        $this->setParametro('id_punto_venta','id_punto_venta','integer');//ok
        $this->setParametro('identificador_reporte','identificador_reporte','varchar');//ok
        $this->setParametro('id_agencia','id_agencia','integer');//ok
        $this->setParametro('nro_boleto','nro_boleto','varchar');//ok
        $this->setParametro('fecha_emision','fecha_emision','varchar');//ok
        $this->setParametro('pasajero','pasajero','varchar');//ok
        $this->setParametro('total','total','numeric');//ok
        $this->setParametro('liquido','liquido','numeric');//ok
        $this->setParametro('neto','neto','numeric');//ok
        $this->setParametro('tasas','tasas','varchar');//ok
        $this->setParametro('comision','comision','numeric');//ok
        $this->setParametro('carrier_fees','carrier_fees','varchar');//ok
        $this->setParametro('moneda','moneda','varchar');//ok
        $this->setParametro('forma_pago_amadeus','forma_pago_amadeus','varchar');//ok
        $this->setParametro('voided','voided','varchar');//ok
        $this->setParametro('agente_venta','agente_venta','varchar');
        $this->setParametro('fp','fp','varchar');
        $this->setParametro('valor_fp','valor_fp','numeric');
        $this->setParametro('localizador','localizador','varchar');
        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();
        //Devuelve la respuesta
        return $this->respuesta;
    }
    function insertarBoletoServicioAmadeusJSon(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='obingresos.ft_boleto_ime';
        $this->transaccion='OBING_SERVAMAJS_INS';
        $this->tipo_procedimiento='IME';
        //Define los parametros para la funcion
        $this->setParametro('id_punto_venta','id_punto_venta','integer');//ok
        $this->setParametro('fecha_emision','fecha_emision','date');//ok
        $this->setParametro('id_agencia','id_agencia','integer');//ok
        $this->setParametro('boletos','boletos','text');//ok
        //Ejecuta la instruccion
        $this->armarConsulta();
        //var_dump($this->consulta); exit;
        $this->ejecutarConsulta();
        //Devuelve la respuesta
        return $this->respuesta;
    }
    function insertarBoletoReporteServicioAmadeus(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='obingresos.ft_boleto_amadeus_ime';
        $this->transaccion='OBING_BOLREPSERV_INS';
        $this->tipo_procedimiento='IME';
        //Define los parametros para la funcion
        $this->setParametro('id_punto_venta','id_punto_venta','integer');//ok
        $this->setParametro('nro_boleto','nro_boleto','varchar');//ok
        $this->setParametro('fecha_emision','fecha_emision','varchar');//ok
        $this->setParametro('pasajero','pasajero','varchar');//ok
        $this->setParametro('total','total','numeric');//ok
        $this->setParametro('liquido','liquido','numeric');//ok
        $this->setParametro('neto','neto','numeric');//ok
        $this->setParametro('tasas','tasas','varchar');//ok
        //$this->setParametro('comision','comision','numeric');//ok
        //$this->setParametro('carrier_fees','carrier_fees','varchar');//ok
        $this->setParametro('moneda','moneda','varchar');//ok
        $this->setParametro('voided','voided','varchar');//ok
        $this->setParametro('fp','fp','varchar');
        $this->setParametro('forma_pago_amadeus','forma_pago_amadeus','varchar');
        $this->setParametro('valor_fp','valor_fp','numeric');
        $this->setParametro('localizador','localizador','varchar');
        //Ejecuta la instruccion
        $this->armarConsulta(); //echo $this->consulta;exit;
        $this->ejecutarConsulta();
        //Devuelve la respuesta
        return $this->respuesta;
    }
    function insertarBoletoAgenciaReporteServicioAmadeus(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='obingresos.ft_boleto_amadeus_ime';
        $this->transaccion='OBING_BOLREPAG_INS';
        $this->tipo_procedimiento='IME';
        //Define los parametros para la funcion
        $this->setParametro('nro_boleto','nro_boleto','varchar');//ok
        $this->setParametro('fecha_emision','fecha_emision','varchar');//ok
        $this->setParametro('pasajero','pasajero','varchar');//ok
        $this->setParametro('total','total','numeric');//ok
        $this->setParametro('liquido','liquido','numeric');//ok
        $this->setParametro('neto','neto','numeric');//ok
        $this->setParametro('moneda','moneda','varchar');//ok
        $this->setParametro('voided','voided','varchar');//ok
        $this->setParametro('forma_pago_amadeus','forma_pago_amadeus','varchar');
        $this->setParametro('localizador','localizador','varchar');
        $this->setParametro('officeid','officeid','varchar');
        $this->setParametro('codigo_iata','codigo_iata','varchar');
        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();
        //Devuelve la respuesta
        return $this->respuesta;
    }
    function compararBoletosServicioAmadeusERP(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='obingresos.ft_boleto_ime';
        $this->transaccion='OBING_BOLAMAERP_INS';
        $this->tipo_procedimiento='IME';
        //Define los parametros para la funcion
        $this->setParametro('id_punto_venta','id_punto_venta','integer');//ok
        $this->setParametro('id_usuario_cajero','id_usuario_cajero','integer');//ok
        $this->setParametro('boletos','boletos','varchar');//ok
        $this->setParametro('fecha_emision','fecha_emision','varchar');//ok
        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();
        //Devuelve la respuesta
        return $this->respuesta;
    }
    function insertarBoletosRET(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='obingresos.ft_boleto_retweb_ime';
        $this->transaccion='OBING_BOLRW_INS';
        $this->tipo_procedimiento='IME';
        $this->setParametro('fecha_emision','fecha_emision','date');
        $this->setParametro('detalle','detalle','text');
        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();
        //Devuelve la respuesta
        return $this->respuesta;
    }
    function listarBoletoReporte(){
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento='obingresos.ft_boleto_sel';
        $this->transaccion='OBING_BOLFAC_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion
        $this->setCount(false);

        $this->setParametro('id_boleto','id_boleto','integer');//ok

        //Definicion de la lista del resultado del query
        $this->captura('nit','varchar');
        $this->captura('fecha_emision','varchar');
        $this->captura('codigo_punto_venta','varchar');
        $this->captura('nombre_punto_venta','varchar');
        $this->captura('nro_boleto','varchar');
        $this->captura('localizador','varchar');
        $this->captura('endoso','varchar');
        $this->captura('neto','varchar');
        $this->captura('sujeto_credito','varchar');
        $this->captura('tasas_impuestos','varchar');
        $this->captura('total','varchar');
        $this->captura('forma_pago','varchar');
        $this->captura('pasajero','varchar');
        $this->captura('tipo_identificacion','varchar');
        $this->captura('identificacion','varchar');
        $this->captura('pais','varchar');
        $this->captura('origen','varchar');
        $this->captura('direccion','varchar');
        $this->captura('telefono','varchar');
        $this->captura('fare_calc','text');
        $this->captura('detalle_tasas','varchar');
        $this->captura('conexion','varchar');


        //Ejecuta la instruccion
        $this->armarConsulta();
        //var_dump($this->consulta);exit;
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function listarBoletoDetalleReporte(){
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento='obingresos.ft_boleto_sel';
        $this->transaccion='OBING_BOLFACDET_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion
        $this->setCount(false);

        $this->setParametro('id_boleto','id_boleto','integer');//ok

        //Definicion de la lista del resultado del query
        $this->captura('fecha_origen','varchar');
        $this->captura('fecha_destino','varchar');
        $this->captura('vuelo','varchar');
        $this->captura('desde','varchar');
        $this->captura('hacia','varchar');
        $this->captura('hora_origen','varchar');
        $this->captura('hora_destino','varchar');
        $this->captura('tarifa','varchar');
        $this->captura('equipaje','varchar');
        $this->captura('clase','varchar');
        $this->captura('cupon','smallint');
        $this->captura('flight_status','varchar');
        $this->captura('conexion','varchar');
        $this->captura('retorno','varchar');
        $this->captura('validez_tarifaria','integer');
        $this->captura('pais_origen','varchar');
        $this->captura('pais_destino','varchar');

        //Ejecuta la instruccion
        $this->armarConsulta();

        $this->ejecutarConsulta();
        //Devuelve la respuesta
        return $this->respuesta;
    }

    function modificarBoleto(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='obingresos.ft_boleto_ime';
        $this->transaccion='OBING_BOL_MOD';
        $this->tipo_procedimiento='IME';

        //Define los parametros para la funcion
        $this->setParametro('id_boleto','id_boleto','int4');
        $this->setParametro('fecha_emision','fecha_emision','date');
        $this->setParametro('agtnoiata','agtnoiata','varchar');
        $this->setParametro('cupones','cupones','int4');
        $this->setParametro('ruta','ruta','varchar');
        $this->setParametro('estado','estado','varchar');
        $this->setParametro('id_agencia','id_agencia','int4');
        $this->setParametro('moneda','moneda','varchar');
        $this->setParametro('total','total','numeric');
        $this->setParametro('pasajero','pasajero','varchar');
        $this->setParametro('id_moneda_boleto','id_moneda_boleto','int4');
        $this->setParametro('estado_reg','estado_reg','varchar');
        $this->setParametro('gds','gds','varchar');
        $this->setParametro('comision','comision','numeric');
        $this->setParametro('agt','agt','varchar');
        $this->setParametro('neto','neto','numeric');
        $this->setParametro('tipopax','tipopax','varchar');
        $this->setParametro('origen','origen','varchar');
        $this->setParametro('destino','destino','varchar');
        $this->setParametro('retbsp','retbsp','varchar');
        $this->setParametro('monto_pagado_moneda_boleto','monto_pagado_moneda_boleto','numeric');
        $this->setParametro('tipdoc','tipdoc','varchar');
        $this->setParametro('liquido','liquido','numeric');
        $this->setParametro('nro_boleto','nro_boleto','varchar');
        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();
        //Devuelve la respuesta
        return $this->respuesta;
    }

    function eliminarBoleto(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='obingresos.ft_boleto_ime';
        $this->transaccion='OBING_BOL_ELI';
        $this->tipo_procedimiento='IME';

        //Define los parametros para la funcion
        $this->setParametro('id_boleto','id_boleto','int4');
        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();
        //Devuelve la respuesta
        return $this->respuesta;
    }
    function cambiarRevisionBoleto(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='obingresos.ft_boleto_ime';
        $this->transaccion='OBING_REVBOL_MOD';
        $this->tipo_procedimiento='IME';
        //Define los parametros para la funcion
        $this->setParametro('id_boleto_amadeus','id_boleto_amadeus','int4');
        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();
        //Devuelve la respuesta
        return $this->respuesta;
    }
    function eliminarBoletosAmadeus(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='obingresos.ft_boleto_amadeus_ime';
        $this->transaccion='OBING_BOLAMA_ELI';
        $this->tipo_procedimiento='IME';
        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();
        //Devuelve la respuesta
        return $this->respuesta;
    }
    function cambiaEstadoBoleto(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='obingresos.ft_boleto_ime';
        $this->transaccion='OBING_BOLEST_MOD';
        $this->tipo_procedimiento='IME';

        //Define los parametros para la funcion
        $this->setParametro('id_boleto','id_boleto','int4');
        $this->setParametro('accion','accion','varchar');
        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();
        //Devuelve la respuesta
        return $this->respuesta;
    }
    function ultimaFechaMigracion(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='obingresos.ft_detalle_boletos_web_ime';
        $this->transaccion='OBING_BOWEBFEC_MOD';
        $this->tipo_procedimiento='IME';
        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();
        //Devuelve la respuesta
        return $this->respuesta;
    }
    function detalleDiarioBoletosWeb(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='obingresos.ft_detalle_boletos_web_ime';
        $this->transaccion='OBING_DETBOWEB_INS';
        $this->tipo_procedimiento='IME';
        $this->setParametro('fecha','fecha','varchar');
        $this->setParametro('detalle_boletos','detalle_boletos','json_text');
        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();
        //Devuelve la respuesta
        return $this->respuesta;
    }
    function procesarDetalleBoletos(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='obingresos.ft_detalle_boletos_web_ime';
        $this->transaccion='OBING_BOWEBPROC_MOD';
        $this->tipo_procedimiento='IME';
        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }
    function  listarDepositoTmp(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='obingresos.ft_detalle_boletos_web_ime';
        $this->transaccion='OBING_BOWEBPROC_MOD';
        $this->tipo_procedimiento='IME';
        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();
        //Devuelve la respuesta
        return $this->respuesta;
    }
    function getBoletoServicio(){
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento='obingresos.ft_boleto_sel';
        $this->transaccion='OBING_BOLSERV_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion
        $this->setCount(false);
        $this->setParametro('nro_boleto','nro_boleto','varchar');//ok
        //Definicion de la lista del resultado del query
        $this->captura('boleto','json');
        $this->captura('detalle','json');
        $this->captura('pagos','json');
        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();
        //Devuelve la respuesta
        return $this->respuesta;
    }
    function  listarReporteResiberVentasWeb()
    {
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento='obingresos.ft_boleto_sel';
        $this->transaccion='OBING_REPRESVEW_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion
        $this->setParametro('fecha_ini','fecha_ini','date');
        $this->setParametro('fecha_fin','fecha_fin','date');
        $this->setParametro('tipo','tipo','varchar');
        $this->setCount(false);
        $this->captura('boleto_resiber','varchar');
        $this->captura('boleto_ventas_web','varchar');
        $this->captura('numero_tarjeta','varchar');
        $this->captura('fecha','date');
        $this->captura('monto_resiber','numeric');
        $this->captura('monto_ventas_web','numeric');
        $this->captura('moneda','varchar');
        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();
        //Devuelve la respuesta
        return $this->respuesta;
    }
    function viajeroFrecuente(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='obingresos.ft_boleto_ime';
        $this->transaccion='OBING_BOWEBFEC_VEF';
        $this->tipo_procedimiento='IME';
        $this->setParametro('id_boleto_amadeus','id_boleto_amadeus','int4');
        $this->setParametro('ffid','ffid','varchar');
        $this->setParametro('bandera','bandera','varchar');
        $this->setParametro('pnr','pnr','varchar');
        $this->setParametro('ticketNumber','ticketNumber','varchar');
        $this->setParametro('voucherCode','voucherCode','varchar');
        $this->setParametro('id_pasajero_frecuente','id_pasajero_frecuente','int4');
        $this->setParametro('nombre_completo','nombre_completo','varchar');
        $this->setParametro('mensaje','mensaje','varchar');
        $this->setParametro('status','status','varchar');
        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();
        //Devuelve la respuesta
        return $this->respuesta;
    }
    function logViajeroFrecuente () {
        $this->procedimiento='obingresos.ft_boleto_ime';
        $this->transaccion='OBING_LOG_VEF';
        $this->tipo_procedimiento='IME';
        $this->setParametro('id_boleto_amadeus','id_boleto_amadeus','int4');
        $this->setParametro('tickert_number','tickert_number','varchar');
        $this->setParametro('pnr','pnr','varchar');
        $this->setParametro('moneda','moneda','varchar');
        $this->setParametro('importe','importe','numeric');
        $this->armarConsulta();
        $this->ejecutarConsulta();
        //Devuelve la respuesta
        return $this->respuesta;
    }


    function traerReservaBoletoExch(){
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento='obingresos.ft_boleto_sel';
        $this->transaccion='OBING_BOL_EXCH_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion
        //Define los parametros para la funcion

        $this->setParametro('localizador','localizador','jsonb');
        $this->setParametro('ct','ct','jsonb');
        $this->setParametro('fc','fc','jsonb');
        $this->setParametro('pasajeros','pasajeros','jsonb');
        $this->setParametro('tasa','tasa','jsonb');
        $this->setParametro('importes','importes','jsonb');
        $this->setParametro('fn_V2','fn_V2','jsonb');
        $this->setParametro('ssrs','ssrs','jsonb');
        $this->setParametro('tl','tl','jsonb');
        $this->setParametro('responsable','responsable','jsonb');
        $this->setParametro('tipo_pv','tipo_pv','jsonb');
        $this->setParametro('update','update','jsonb');
        $this->setParametro('vuelo','vuelo','jsonb');
        $this->setParametro('tipo','tipo','varchar');
        $this->setParametro('id_boletos_amadeus','id_boletos_amadeus','varchar');
        $this->setParametro('pnr','pnr','varchar');
        $this->setParametro('nro_boleto','nro_boleto','varchar');

        //Definicion de la lista del resultado del query
        $this->captura('id_vuelo','integer');
        $this->captura('clase','varchar');
        $this->captura('linea','varchar');
        $this->captura('estado','varchar');
        $this->captura('origen','varchar');
        $this->captura('destino','varchar');
        $this->captura('num_vuelo','varchar');
        $this->captura('hora_salida','varchar');
        $this->captura('fecha_salida','varchar');
        $this->captura('hora_llegada','varchar');

        $this->captura('codigo_tarifa','varchar');
        $this->captura('calculo_tarifa','varchar');
        $this->captura('tasa','varchar');
        $this->captura('rc_iva','numeric');
        $this->captura('forma_identificacion','varchar');
        $this->captura('importe_total','varchar');
        $this->captura('importe_tarifa','varchar');
        $this->captura('agente','varchar');
        $this->captura('nombre_ofi','varchar');
        $this->captura('codigo_iata','varchar');
        $this->captura('telefono_ofi','varchar');
        $this->captura('direccion_ofi','varchar');
        $this->captura('tipo_cambio','numeric');
        $this->captura('endoso','varchar');
        $this->captura('fecha_create','varchar');
        $this->captura('moneda_iva','varchar');
        $this->captura('tipo_emision','varchar');
        $this->captura('moneda_tarifa','varchar');
        $this->captura('pasajero','varchar');
        $this->captura('numero_billete','varchar');


        //Ejecuta la instruccion
        $this->armarConsulta();
        //echo($this->consulta);exit;
        $this->ejecutarConsulta();
        //Devuelve la respuesta
        return $this->respuesta;
    }

    function generarBilleteElectronico()
    {
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento = 'obingresos.ft_boleto_sel';
        $this->transaccion = 'OBING_BOL_EXCH_SEL';
        $this->tipo_procedimiento = 'SEL';//tipo de transaccion
        //Define los parametros para la funcion


        $this->setParametro('pnr', 'pnr', 'varchar');
        $this->setParametro('tipo', 'tipo', 'varchar');


        //Definicion de la lista del resultado del query
        $this->captura('id_vuelo', 'integer');
        $this->captura('clase', 'varchar');
        $this->captura('linea', 'varchar');
        $this->captura('estado', 'varchar');
        $this->captura('origen', 'varchar');
        $this->captura('destino', 'varchar');
        $this->captura('num_vuelo', 'varchar');
        $this->captura('hora_salida', 'varchar');
        $this->captura('fecha_salida', 'varchar');
        $this->captura('hora_llegada', 'varchar');

        $this->captura('codigo_tarifa', 'varchar');
        $this->captura('calculo_tarifa', 'varchar');
        $this->captura('tasa', 'varchar');
        $this->captura('rc_iva', 'numeric');
        $this->captura('forma_identificacion', 'varchar');

        /*$this->captura('lugar_agencia','varchar');
        $this->captura('lugar_cod_agencia','varchar');*/


        //Ejecuta la instruccion
        $this->armarConsulta();
        //echo($this->consulta);exit;
        $this->ejecutarConsulta();
    }

    function verificarBoletoExch(){

        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento='obingresos.ft_boleto_ime';
        $this->transaccion='OBING_VER_EXCH_IME';
        $this->tipo_procedimiento='IME';//tipo de transaccion


        $this->setParametro('pnr', 'pnr', 'varchar');
        $this->setParametro('id_boletos_amadeus', 'id_boletos_amadeus', 'varchar');
        $this->setParametro('fecha_emision', 'fecha_emision', 'date');
        $this->setParametro('exchange', 'exchange', 'boolean');
        $this->setParametro('tipo_emision', 'tipo_emision', 'jsonb');
        $this->setParametro('data_field', 'data_field', 'varchar');

        $this->captura('exchange','boolean');
        $this->captura('tipo_emision','varchar');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function disparaCorreoVentasWeb(){

        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento='obingresos.ft_boleto_ime';
        $this->transaccion='OBING_MAIL_DET_VW';
        $this->tipo_procedimiento='IME';//tipo de transaccion
        $this->setCount(false);

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function listarVentasCounter(){

        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento='obingresos.ft_boleto_sel';
        $this->transaccion='OBING_SAL_BOL_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion

        $this->setParametro('fecha', 'fecha', 'varchar');
        //captura parametros adicionales para el count
        $this->capturaCount('precio_total','numeric');

        $this->captura('id_boleto_amadeus','integer');
        $this->captura('pasajero','varchar');
        $this->captura('localizador','varchar');
        $this->captura('nro_boleto','varchar');
        $this->captura('forma_pago_amadeus','varchar');
        $this->captura('moneda','varchar');
        $this->captura('precio_total','numeric');
        $this->captura('codigo_agente','varchar');
        $this->captura('id_forma_pago','int4');
        $this->captura('monto_forma_pago','numeric');
        $this->captura('forma_pago','varchar');
        $this->captura('fecha_emision','date');
        $this->captura('trans_code','varchar');
        $this->captura('trans_issue_indicator','varchar');
        $this->captura('punto_venta','varchar');
        $this->captura('trans_code_exch','varchar');
        $this->captura('impreso','varchar');

        //Ejecuta la instruccion
        $this->armarConsulta();//echo $this->consulta;exit;
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }
}
?>
