<?php
/**
 *@package BoA
 *@file    ACTVueloPendiente.php
 *@author  franklin.espinoza
 *@date    29-08-2022 15:14:58
 *@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
 */

class ACTVueloPendiente extends ACTbase{

    /**{developer:franklin.espinoza, date:29/08/2022, description: Listar Vuelos Pendientes SICNO TRAFICO}**/
    function generarVueloPendiente(){

        if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
            $this->objReporte = new Reporte($this->objParam,$this);
            $this->res = $this->objReporte->generarReporteListado('MODVueloPendiente','generarVueloPendiente');
        }else {
            $this->objFunc=$this->create('MODVueloPendiente');
            $this->res=$this->objFunc->generarVueloPendiente($this->objParam);
        }

        $this->res->imprimirRespuesta($this->res->generarJson());
    }
    /**{developer:franklin.espinoza, date:29/08/2022, description: Listar Vuelos Pendientes SICNO TRAFICO}**/

}
?>
