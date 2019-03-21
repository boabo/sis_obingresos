<?php

class ACTDepositoPeriodo extends ACTbase{

    function listarDepositosPeriodo(){
        $this->objParam->defecto('ordenacion','id_deposito');
        $this->objParam->defecto('dir_ordenacion','asc');
        if($this->objParam->getParametro('id_agencia') != '') {
            $this->objParam->addFiltro("dep.id_agencia = " . $this->objParam->getParametro('id_agencia')."
            and dep.fecha between ''".$this->objParam->getParametro('fecha_ini')."'' and ''".$this->objParam->getParametro('fecha_fin')."''
            and dep.estado = ''validado''");





            //var_dump($this->objParam->getParametro('fecha_fin'));exit;
        }
        if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
            $this->objReporte = new Reporte($this->objParam,$this);
            $this->res = $this->objReporte->generarReporteListado('MODDepositoPeriodo','listarDepositosPeriodo');
        } else{
            $this->objFunc=$this->create('MODDepositoPeriodo');
            $this->res=$this->objFunc->listarDepositosPeriodo($this->objParam);
            $temp = Array();
            $temp['monto_deposito'] = $this->res->extraData['suma_total'];
            $temp['nro_deposito'] = 'summary';
            $temp['id_deposito'] = 0;
            $this->res->total++;
            $this->res->addLastRecDatos($temp);
        }
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

}
?>
