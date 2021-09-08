<?php
/**
 *@package  BoA
 *@file     MODExcluirAgencia.php
 *@author  (franklin.espinoza)
 *@date     11-08-2021 09:16:06
 *@description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
 */

class MODExcluirAgencia extends MODbase{

    function __construct(CTParametro $pParam)
    {
        parent::__construct($pParam);
    }

    function registrarExcluirAgencia(){
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento='obingresos.ft_calculo_over_comison_ime';
        $this->transaccion='OBING_REG_EXC_AGE';
        $this->tipo_procedimiento='IME';//tipo de transaccion
        //Define los parametros para la funcion

        $this->setParametro('id_acm_key','id_acm_key','integer');
        $this->setParametro('iata_code','iata_code','varchar');
        $this->setParametro('office_id','office_id','varchar');
        $this->setParametro('fecha_desde','fecha_desde','date');
        $this->setParametro('fecha_hasta','fecha_hasta','date');
        $this->setParametro('observacion','observacion','text');

        //Ejecuta la instruccion
        $this->armarConsulta();
        //echo($this->consulta);exit;
        $this->ejecutarConsulta();
        //Devuelve la respuesta
        return $this->respuesta;
    }

}