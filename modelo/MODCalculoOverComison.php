<?php
/**
 *@package pXP
 *@file MODCalculoOverComison.php
 *@author  (franklin.espinoza)
 *@date     31-05-2021 22:42:25
 *@description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
 */
class MODCalculoOverComison extends MODbase{

    function __construct(CTParametro $pParam){
        parent::__construct($pParam);
    }

    function generarCreditoNoIata(){
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento='obingresos.ft_reporte_ime';
        $this->transaccion='OBING_CRED_NO_IATA';
        $this->tipo_procedimiento='IME';//tipo de transaccion
        //Define los parametros para la funcion

        $this->setParametro('fecha_desde','fecha_desde','date');
        $this->setParametro('fecha_hasta','fecha_hasta','date');
        $this->setParametro('tipo','tipo','varchar');
        $this->setParametro('dataJson','dataJson','jsonb');
        $this->setParametro('accion','accion','varchar');

        //Ejecuta la instruccion
        $this->armarConsulta();
        //echo($this->consulta);exit;
        $this->ejecutarConsulta();
        //Devuelve la respuesta
        return $this->respuesta;
    }

    function verificarPeriodoGenerado(){
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento='obingresos.ft_reporte_ime';
        $this->transaccion='OBING_VER_PER_GENERA';
        $this->tipo_procedimiento='IME';//tipo de transaccion
        //Define los parametros para la funcion

        $this->setCount(false);

        $this->setParametro('fecha_ini','fecha_ini','date');
        $this->setParametro('fecha_fin','fecha_fin','date');
        $this->setParametro('tipo','tipo','varchar');

        //Ejecuta la instruccion
        $this->armarConsulta();
        //echo($this->consulta);exit;
        $this->ejecutarConsulta();
        //Devuelve la respuesta
        return $this->respuesta;
    }

    function revertirMovimientoEntidad(){
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento='obingresos.ft_reporte_ime';
        $this->transaccion='OBING_REVERTIR_ABONO';
        $this->tipo_procedimiento='IME';//tipo de transaccion
        //Define los parametros para la funcion


        $this->setParametro('AcmKey','AcmKey','integer');
        $this->setParametro('DocumentNumber','DocumentNumber','varchar');

        //Ejecuta la instruccion
        $this->armarConsulta();
        //echo($this->consulta);exit;
        $this->ejecutarConsulta();
        //Devuelve la respuesta
        return $this->respuesta;
    }

    function listarAgenciaExcluida(){
        $this->procedimiento='obingresos.ft_calculo_over_comison_sel';
        $this->transaccion='OBING_AGE_EXC_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion

        $this->setCount(false);

        $this->setParametro('fecha_desde','fecha_desde','date');
        $this->setParametro('fecha_hasta','fecha_hasta','date');

        //Definicion de la lista del resultado del query
        $this->captura('id_acm_key','integer');
        $this->captura('iata_code','varchar');
        $this->captura('office_id','varchar');
        $this->captura('fecha_desde','date');
        $this->captura('fecha_hasta','date');
        $this->captura('observacion','text');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        return $this->respuesta;
    }
}
?>
