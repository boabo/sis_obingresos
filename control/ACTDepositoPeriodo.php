<?php

class ACTDepositoPeriodo extends ACTbase{

    function listarDepositosPeriodo(){
        $this->objParam->defecto('ordenacion','id_movimiento_entidad');
        $this->objParam->defecto('dir_ordenacion','asc');
        if($this->objParam->getParametro('id_agencia') != '') {
            $this->objParam->addFiltro("mo.id_agencia = " . $this->objParam->getParametro('id_agencia')."
            and mo.id_periodo_venta between 	     /*INICIO PERIODO*/
                              								   (select mo.id_periodo_venta
                                                               from obingresos.vdepositos_periodo mo
                                                               where mo.id_agencia = " . $this->objParam->getParametro('id_agencia')." and  mo.fecha_fin >= ''".$this->objParam->getParametro('fecha_ini')."''
                                                               order by mo.id_periodo_venta
                                                               FETCH FIRST 1 ROW ONLY  )

                                                               and

                                                               /*Fin del periodo*/
                                                               (select max (mo.id_periodo_venta)
                                                               from obingresos.vdepositos_periodo mo
                                                               where mo.id_agencia=" . $this->objParam->getParametro('id_agencia')."  and mo.fecha_ini <= ''".$this->objParam->getParametro('fecha_fin')."'')
                                                           ");
            //var_dump($this->objParam->getParametro('fecha_fin'));exit;
        }
        if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
            $this->objReporte = new Reporte($this->objParam,$this);
            $this->res = $this->objReporte->generarReporteListado('MODDepositoPeriodo','listarDepositosPeriodo');
        } else{
            $this->objFunc=$this->create('MODDepositoPeriodo');
            $this->res=$this->objFunc->listarDepositosPeriodo($this->objParam);
            $temp = Array();
            $temp['monto_total'] = $this->res->extraData['suma_total'];
            $temp['autorizacion__nro_deposito'] = 'summary';
            $temp['id_movimiento_entidad'] = 0;
            $this->res->total++;
            $this->res->addLastRecDatos($temp);
        }
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

}
?>
