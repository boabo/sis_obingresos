<?php
/**
 *@package pXP
 *@file gen-ACTBoletosObservados.php
 *@author  (admin)
 *@date 04-06-2019 19:39:16
 *@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
 */

class ACTBoletosObservados extends ACTbase{

    function listarBoletosObservados(){
        $this->objParam->defecto('ordenacion','id_boletos_observados');

        $this->objParam->defecto('dir_ordenacion','asc');

        if ($this->objParam->getParametro('codigo_instancia_pago') != '' || $this->objParam->getParametro('codigo_forma_pago') != ''|| $this->objParam->getParametro('codigo_medio_pago') != '') {
            $this->objParam->addFiltro(" (bobs.instancia_pago = ''" . $this->objParam->getParametro('codigo_instancia_pago')."'' or
            bobs.forma_pago = ''" . $this->objParam->getParametro('codigo_forma_pago')."'' or
            bobs.medio_pago = ''" . $this->objParam->getParametro('codigo_medio_pago')."'' )");
        }

        if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
            $this->objReporte = new Reporte($this->objParam,$this);
            $this->res = $this->objReporte->generarReporteListado('MODBoletosObservados','listarBoletosObservados');
        } else{
            $this->objFunc=$this->create('MODBoletosObservados');

            $this->res=$this->objFunc->listarBoletosObservados($this->objParam);
        }
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function insertarBoletosObservados(){
        $this->objFunc=$this->create('MODBoletosObservados');
        if($this->objParam->insertar('id_boletos_observados')){
            $this->res=$this->objFunc->insertarBoletosObservados($this->objParam);
        } else{
            $this->res=$this->objFunc->modificarBoletosObservados($this->objParam);
        }
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function eliminarBoletosObservados(){
        $this->objFunc=$this->create('MODBoletosObservados');
        $this->res=$this->objFunc->eliminarBoletosObservados($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

}

?>
