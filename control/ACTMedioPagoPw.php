<?php
/**
 *@package pXP
 *@file gen-ACTMedioPagoPw.php
 *@author  (admin)
 *@date 04-06-2019 22:47:38
 *@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
 */

class ACTMedioPagoPw extends ACTbase{

    function listarMedioPagoPw(){
        $this->objParam->defecto('ordenacion','id_medio_pago_pw');
        $this->objParam->defecto('dir_ordenacion','asc');

        /*Aumentando para filtrar solo los conceptos que seran para Recibos Oficiales (Ismael Valdivia 14/07/2020)*/
        if($this->objParam->getParametro('emision')!=''){
            $this->objParam->addFiltro("''".$this->objParam->getParametro('emision')."''=ANY (mppw.sw_autorizacion) AND ''".$this->objParam->getParametro('regional')."''=ANY (mppw.regionales)");
            // if ($this->objParam->getParametro('regional')!='') {
            //   var_dump("aqui llega el dato",$this->objParam->getParametro('regional'));
            //   $this->objParam->addFiltro("''".$this->objParam->getParametro('regional')."''=ANY (mppw.regionales)");
            // }
        }

        // if($this->objParam->getParametro('regional')!=''){
        //     $this->objParam->addFiltro("''".$this->objParam->getParametro('regional')."''=ANY (mppw.regionales)");
        // }

        /*Aumentando filtro para obtener efectivo (CASH)*/
        // if($this->objParam->getParametro('defecto')=='si'){
        //     $this->objParam->addFiltro("fp.fop_code = ''CA''");
        // }
        /************************************************/

        /**************************************************************************************************************/

        if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
            $this->objReporte = new Reporte($this->objParam,$this);
            $this->res = $this->objReporte->generarReporteListado('MODMedioPagoPw','listarMedioPagoPw');
        } else{
            $this->objFunc=$this->create('MODMedioPagoPw');

            $this->res=$this->objFunc->listarMedioPagoPw($this->objParam);
        }
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function insertarMedioPagoPw(){
        $this->objFunc=$this->create('MODMedioPagoPw');
        if($this->objParam->insertar('id_medio_pago_pw')){
            $this->res=$this->objFunc->insertarMedioPagoPw($this->objParam);
        } else{
            $this->res=$this->objFunc->modificarMedioPagoPw($this->objParam);
        }
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function eliminarMedioPagoPw(){
        $this->objFunc=$this->create('MODMedioPagoPw');
        $this->res=$this->objFunc->eliminarMedioPagoPw($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function editarAutorizaciones(){
      $this->objFunc=$this->create('MODMedioPagoPw');
      $this->res=$this->objFunc->editarAutorizaciones($this->objParam);
      $this->res->imprimirRespuesta($this->res->generarJson());
    }


}

?>
