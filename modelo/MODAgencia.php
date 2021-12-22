<?php
/**
 *@package pXP
 *@file gen-MODAgencia.php
 *@author  (jrivera)
 *@date 06-01-2016 21:30:12
 *@description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
 */

class MODAgencia extends MODbase{

    function __construct(CTParametro $pParam){
        parent::__construct($pParam);
    }

    function listarAgencia(){
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento='obingresos.ft_agencia_sel';
        $this->transaccion='OBING_AGE_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion

        //Definicion de la lista del resultado del query
        $this->captura('id_agencia','int4');
        $this->captura('id_moneda_control','int4');
        $this->captura('depositos_moneda_boleto','varchar');
        $this->captura('tipo_pago','varchar');
        $this->captura('nombre','varchar');
        $this->captura('monto_maximo_deuda','numeric');
        $this->captura('tipo_cambio','varchar');
        $this->captura('codigo_int','varchar');
        $this->captura('codigo','varchar');
        $this->captura('codigo_noiata','varchar');
        $this->captura('tipo_agencia','varchar');
        $this->captura('usr_reg','varchar');
        $this->captura('usr_mod','varchar');
        $this->captura('desc_moneda','varchar');

        $this->captura('bloquear_emision','varchar');
        $this->captura('validar_boleta','varchar');
        $this->captura('controlar_periodos_pago','varchar');
        $this->captura('estado_reg','varchar');


        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function obtenerOfficeIDsAgencias(){
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento='obingresos.ft_agencia_sel';
        $this->transaccion='OBING_OFFIDAGE_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion

        //Definicion de la lista del resultado del query
        $this->captura('officeID','varchar');
        $this->captura('codigo_iata','varchar');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function getDocumentosContrato(){
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento='obingresos.ft_agencia_sel';
        $this->transaccion='OBING_AGEDOCON_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion

        $this->setCount(false);

        $this->setParametro('id_contrato','id_contrato','int4');

        //Definicion de la lista del resultado del query
        $this->captura('id_documento_wf','int4');
        $this->captura('codigo_tipo_documento','varchar');
        $this->captura('nombre_tipo_doc','varchar');


        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function listarAgenciaContrato(){
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento='obingresos.ft_agencia_sel';
        $this->transaccion='OBING_AGE_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion

        //Definicion de la lista del resultado del query
        $this->captura('id_agencia','int4');
        $this->captura('id_moneda_control','int4');
        $this->captura('depositos_moneda_boleto','varchar');
        $this->captura('tipo_pago','varchar');
        $this->captura('nombre','varchar');
        $this->captura('monto_maximo_deuda','numeric');
        $this->captura('tipo_cambio','varchar');
        $this->captura('codigo_int','varchar');
        $this->captura('codigo','varchar');
        $this->captura('codigo_noiata','varchar');
        $this->captura('tipo_agencia','varchar');
        $this->captura('usr_reg','varchar');
        $this->captura('usr_mod','varchar');
        $this->captura('desc_moneda','varchar');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function insertarAgencia(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='obingresos.ft_agencia_ime';
        $this->transaccion='OBING_AGE_INS';
        $this->tipo_procedimiento='IME';

        //Define los parametros para la funcion
        $this->setParametro('id_moneda_control','id_moneda_control','int4');
        $this->setParametro('depositos_moneda_boleto','depositos_moneda_boleto','varchar');
        $this->setParametro('tipo_pago','tipo_pago','varchar');
        $this->setParametro('nombre','nombre','varchar');
        $this->setParametro('monto_maximo_deuda','monto_maximo_deuda','numeric');
        $this->setParametro('tipo_cambio','tipo_cambio','varchar');
        $this->setParametro('codigo_int','codigo_int','varchar');
        $this->setParametro('codigo','codigo','varchar');
        $this->setParametro('tipo_agencia','tipo_agencia','varchar');
        $this->setParametro('id_lugar','id_lugar','integer');
        $this->setParametro('boaagt','boaagt','varchar');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function insertarAgenciaPortal(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='obingresos.ft_agencia_ime';
        $this->transaccion='OBING_AGEPOR_INS';
        $this->tipo_procedimiento='IME';

        //Define los parametros para la funcion
        //agencia
        $this->setParametro('nombre','nombre','varchar');
        $this->setParametro('ciudad','ciudad','varchar');
        $this->setParametro('tipo_agencia','tipo_agencia','varchar');
        $this->setParametro('tipo_persona','tipo_persona','varchar');
        /*Aumentnado este parametro para diferenciar corporativo publica o privada (Ismael Valdivia 04/08/2021)*/
        //$this->setParametro('tipo_institucion','tipo_institucion','varchar');


        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function insertarContratoPortal(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='obingresos.ft_agencia_ime';
        $this->transaccion='OBING_CONPOR_INS';
        $this->tipo_procedimiento='IME';

        $this->setParametro('id_agencia','id_agencia','integer');
        $this->setParametro('id_funcionario','id_funcionario','integer');
        $this->setParametro('numero_contrato','numero_contrato','varchar');
        $this->setParametro('objeto','objeto','varchar');
        $this->setParametro('fecha_firma','fecha_firma','date');
        $this->setParametro('fecha_inicio','fecha_inicio','date');
        $this->setParametro('fecha_fin','fecha_fin','date');
        $this->setParametro('tipo_agencia','tipo_agencia','varchar');
        $this->setParametro('formas_pago','formas_pago','varchar');
        $this->setParametro('moneda_restrictiva','moneda_restrictiva','varchar');

        $this->setParametro('cuenta_bancaria1','cuenta_bancaria1','varchar');
        $this->setParametro('entidad_bancaria1','entidad_bancaria1','varchar');
        $this->setParametro('nombre_cuenta_bancaria1','nombre_cuenta_bancaria1','varchar');

        $this->setParametro('cuenta_bancaria2','cuenta_bancaria2','varchar');
        $this->setParametro('entidad_bancaria2','entidad_bancaria2','varchar');
        $this->setParametro('nombre_cuenta_bancaria2','nombre_cuenta_bancaria2','varchar');


        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function insertarBoletaAgencia(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='obingresos.ft_agencia_ime';
        $this->transaccion='OBING_BOLAGE_INS';
        $this->tipo_procedimiento='IME';

        $this->setParametro('id_contrato','id_contrato','integer');
        $this->setParametro('banco','banco','varchar');
        $this->setParametro('tipo_boleta','tipo_boleta','varchar');
        $this->setParametro('fecha_inicio','fecha_inicio','date');
        $this->setParametro('fecha_fin','fecha_fin','date');
        $this->setParametro('moneda','moneda','varchar');
        $this->setParametro('monto','monto','numeric');


        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function verificarSaldo(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='obingresos.ft_agencia_ime';
        $this->transaccion='OBING_VERSALAGE_MOD';
        $this->tipo_procedimiento='IME';

        $this->setParametro('pnr','pnr','varchar');
        $this->setParametro('apellido','apellido','varchar');
        $this->setParametro('id_agencia','id_agencia','integer');
        $this->setParametro('forma_pago','forma_pago','varchar');
        $this->setParametro('moneda','moneda','varchar');
        $this->setParametro('monto','monto','numeric');
        $this->setParametro('usuario','usuario','varchar');
        $this->setParametro('tipo_usuario','tipo_usuario','varchar');
        $this->setParametro('monto_total','monto_total','numeric');
        $this->setParametro('fecha','fecha','date');


        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function getSaldoAgencia(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='obingresos.ft_agencia_ime';
        $this->transaccion='OBING_GETSALAGE_MOD';
        $this->tipo_procedimiento='IME';

        $this->setParametro('id_agencia','id_agencia','integer');
        $this->setParametro('moneda','moneda','varchar');


        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function insertarDepositoAgencia(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='obingresos.ft_agencia_ime';
        $this->transaccion='OBING_DEPAGE_INS';
        $this->tipo_procedimiento='IME';

        $this->setParametro('id_agencia','id_agencia','integer');
        $this->setParametro('monto','monto','numeric');
        $this->setParametro('moneda','moneda','varchar');
        $this->setParametro('fecha','fecha','date');
        $this->setParametro('banco','banco','varchar');
        $this->setParametro('numero','numero','varchar');
        $this->setParametro('cuenta_bancaria','cuenta_bancaria','varchar');
        $this->setParametro('depositante','depositante','varchar');


        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function insertarComisionAgencia(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='obingresos.ft_agencia_ime';
        $this->transaccion='OBING_COMAGE_INS';
        $this->tipo_procedimiento='IME';

        $this->setParametro('id_contrato','id_contrato','integer');
        $this->setParametro('descripcion','descripcion','varchar');
        $this->setParametro('tipo_comision','tipo_comision','varchar');
        $this->setParametro('mercado','mercado','varchar');
        $this->setParametro('porcentaje','porcentaje','numeric');
        $this->setParametro('moneda','moneda','varchar');
        $this->setParametro('limite_superior','limite_superior','numeric');
        $this->setParametro('limite_inferior','limite_inferior','numeric');


        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function modificarAgencia(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='obingresos.ft_agencia_ime';
        $this->transaccion='OBING_AGE_MOD';
        $this->tipo_procedimiento='IME';

        //Define los parametros para la funcion
        $this->setParametro('id_agencia','id_agencia','int4');
        $this->setParametro('id_moneda_control','id_moneda_control','int4');
        $this->setParametro('depositos_moneda_boleto','depositos_moneda_boleto','varchar');
        $this->setParametro('tipo_pago','tipo_pago','varchar');
        $this->setParametro('nombre','nombre','varchar');
        $this->setParametro('monto_maximo_deuda','monto_maximo_deuda','numeric');
        $this->setParametro('tipo_cambio','tipo_cambio','varchar');
        $this->setParametro('codigo_int','codigo_int','varchar');
        $this->setParametro('codigo','codigo','varchar');
        $this->setParametro('tipo_agencia','tipo_agencia','varchar');

        $this->setParametro('bloquear_emision','bloquear_emision','varchar');
        $this->setParametro('validar_boleta','validar_boleta','varchar');
        $this->setParametro('controlar_periodos_pago','controlar_periodos_pago','varchar');
        $this->setParametro('estado_reg','estado_reg','varchar');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function finalizarContratoPortal(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='obingresos.ft_agencia_ime';
        $this->transaccion='OBING_FINCONPOR_MOD';
        $this->tipo_procedimiento='IME';

        //Define los parametros para la funcion
        $this->setParametro('id_contrato','id_contrato','int4');


        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function eliminarAgencia(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='obingresos.ft_agencia_ime';
        $this->transaccion='OBING_AGE_ELI';
        $this->tipo_procedimiento='IME';

        //Define los parametros para la funcion
        $this->setParametro('id_agencia','id_agencia','int4');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function updateAgencia(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='obingresos.ft_agencia_ime';
        $this->transaccion='OBING_AGE_UPD';
        $this->tipo_procedimiento='IME';

        //Define los parametros para la funcion
        $this->setParametro('id_agencia','id_agencia','int4');
        $this->setParametro('codigo','codigo','varchar');
        $this->setParametro('codigo_noiata','codigo_noiata','varchar');
        //$this->setParametro('codigo_int','codigo_int','varchar');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function insertarAgenciaCompletaPortal(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='obingresos.ft_agencia_ime';
        $this->transaccion='OBING_INS_AGEYCONT';
        $this->tipo_procedimiento='IME';

        //Define los parametros para la funcion

        /*Parametros de la Agencia para su registro*/
          $this->setParametro('nombre','nombre','varchar');
          $this->setParametro('ciudad','ciudad','varchar');
          $this->setParametro('tipo_agencia','tipo_agencia','varchar');
          $this->setParametro('tipo_persona','tipo_persona','varchar');
          $this->setParametro('codigo','codigo','varchar');
          $this->setParametro('codigo_noiata','codigo_noiata','varchar');
          /*Incluir el Campo OfficeID*/
          $this->setParametro('office_id','office_id','varchar');
          /**************************/
          /*Aumentnado este parametro para diferenciar corporativo publica o privada (Ismael Valdivia 04/08/2021)*/
          $this->setParametro('tipo_institucion','tipo_institucion','varchar');
          /**********************************************/
          /*Parametro para validar si se controlara la boleta de Garantia y el monnto maximo de deuda*/
          $this->setParametro('validar_boleta','validar_boleta','varchar');
          $this->setParametro('monto_maximo_deuda','monto_maximo_deuda','numeric');
          /***********************************************************************************************************/
          /*Nuevos Campos que registra el Portal de Agencias*/
          $this->setParametro('iata_status','iata_status','varchar');
          $this->setParametro('osd','osd','varchar');
          $this->setParametro('terciariza','terciariza','varchar');
        /*********************************************************************************************************************/
         /*Nuevos Campos para registrar datos de la Agencia Ismael Valdivia 28/09/2021*/
         $this->setParametro('business_name','business_name','varchar');
         $this->setParametro('representante_legal','representante_legal','varchar');
         $this->setParametro('ci','ci','varchar');
         $this->setParametro('expedido','expedido','varchar');
         /****************************************************************************/

        /*Parametros para Registrar el Contrato*/
          $this->setParametro('id_funcionario','id_funcionario','integer');
          $this->setParametro('numero_contrato','numero_contrato','varchar');
          $this->setParametro('objeto','objeto','varchar');
          $this->setParametro('fecha_firma','fecha_firma','date');
          $this->setParametro('fecha_inicio','fecha_inicio','date');
          $this->setParametro('fecha_fin','fecha_fin','date');
          //$this->setParametro('tipo_agencia','tipo_agencia','varchar');
          $this->setParametro('formas_pago','formas_pago','varchar');
          $this->setParametro('moneda_restrictiva','moneda_restrictiva','varchar');

          // $this->setParametro('cuenta_bancaria1','cuenta_bancaria1','varchar');
          // $this->setParametro('entidad_bancaria1','entidad_bancaria1','varchar');
          // $this->setParametro('nombre_cuenta_bancaria1','nombre_cuenta_bancaria1','varchar');
          //
          // $this->setParametro('cuenta_bancaria2','cuenta_bancaria2','varchar');
          // $this->setParametro('entidad_bancaria2','entidad_bancaria2','varchar');
          // $this->setParametro('nombre_cuenta_bancaria2','nombre_cuenta_bancaria2','varchar');
        /***************************************/

        /*Parametros para insertar en la tabla leg.tanexo y obingresos.tmovimiento_entidad*/
          $this->setParametro('fecha_inicio_boleta','fecha_inicio_boleta','date');
          $this->setParametro('fecha_fin_boleta','fecha_fin_boleta','date');
          $this->setParametro('moneda_boleta','moneda_boleta','varchar');
          $this->setParametro('monto_boleta_garantia','monto_boleta_garantia','varchar');
          $this->setParametro('banco_boleta','banco_boleta','varchar');
          $this->setParametro('nro_documento','nro_documento','varchar');
          $this->setParametro('fecha_fin_uso','fecha_fin_uso','date');
        /**********************************************************************************/


        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    /*Servicios para update de las Agencias (Ismael Valdivia 22/09/2021)*/
    /*Servicio para actualiza la entidad*/
    function updateEntidadPortal(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='obingresos.ft_agencia_ime';
        $this->transaccion='OBING_ENTIDAD_UPD';
        $this->tipo_procedimiento='IME';

        //Define los parametros para la funcion
        $this->setParametro('id_agencia','id_agencia','int4');
        $this->setParametro('nombre','nombre','varchar');
        $this->setParametro('ciudad','ciudad','varchar');
        $this->setParametro('terciariza','terciariza','varchar');
        $this->setParametro('tipo_agencia','tipo_agencia','varchar');
        $this->setParametro('tipo_institucion','tipo_institucion','varchar');
        //$this->setParametro('codigo_int','codigo_int','varchar');

        //Ejecuta la instrucciona
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }
    /***********************************/

    /*Servicio para actualizar el contrato*/
    function updateContratoPortal(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='obingresos.ft_agencia_ime';
        $this->transaccion='OBING_CONTRA_UPD';
        $this->tipo_procedimiento='IME';

        //Data para el contrato
        $this->setParametro('id_contrato','id_contrato','int4');
        $this->setParametro('numero','numero','varchar');
        $this->setParametro('tipo','tipo','varchar');
        $this->setParametro('fecha_inicio','fecha_inicio','varchar');
        $this->setParametro('fecha_fin','fecha_fin','varchar');
        $this->setParametro('fecha_firma','fecha_firma','varchar');
        //Data para la agencia
        $this->setParametro('business_name','business_name','varchar');
        $this->setParametro('nit','nit','varchar');
        $this->setParametro('representante_legal','representante_legal','varchar');
        $this->setParametro('ci','ci','varchar');
        $this->setParametro('expedido','expedido','varchar');
        $this->setParametro('tipo_persona','tipo_persona','varchar');


        //Ejecuta la instrucciona
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }
    /*********************************/

    /*Servicio para actualizar el contrato*/
    function updateOficinaPortal(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='obingresos.ft_agencia_ime';
        $this->transaccion='OBING_OFICINA_UPD';
        $this->tipo_procedimiento='IME';

        $this->setParametro('id_agencia','id_agencia','int4');
        $this->setParametro('codigo_int','codigo_int','varchar');
        $this->setParametro('codigo','codigo','varchar');
        $this->setParametro('iata_status','iata_status','varchar');
        $this->setParametro('osd','osd','varchar');

        //Ejecuta la instrucciona
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }
    /*********************************/

    /*Servicio para actualizar el contrato*/
    function registrarAmpliacionContrato(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='obingresos.ft_ampliacion_contrato';
        $this->transaccion='OBING_AMPLI_CONT_INS';
        $this->tipo_procedimiento='IME';

        $this->setParametro('id_contrato_anterior','id_contrato_anterior','int4');
        $this->setParametro('observaciones','observaciones','varchar');
        $this->setParametro('fecha_inicio','fecha_inicio','varchar');
        $this->setParametro('fecha_fin','fecha_fin','varchar');
        $this->setParametro('id_funcionario','id_funcionario','integer');

        //Ejecuta la instrucciona
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }
    /*********************************/

    function actualizarAmpliacionContrato(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='obingresos.ft_ampliacion_contrato';
        $this->transaccion='OBING_UPDT_AMPL_INS';
        $this->tipo_procedimiento='IME';

        $this->setParametro('id_contrato_ampliacion','id_contrato_ampliacion','int4');
        $this->setParametro('observaciones','observaciones','varchar');
        $this->setParametro('fecha_inicio','fecha_inicio','varchar');
        $this->setParametro('fecha_fin','fecha_fin','varchar');
        $this->setParametro('id_funcionario','id_funcionario','integer');

        //Ejecuta la instrucciona
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

}
?>    
