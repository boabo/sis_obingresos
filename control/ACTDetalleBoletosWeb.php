<?php
require_once(dirname(__FILE__).'/../reportes/RReporteNitRazonXLS.php');
class ACTDetalleBoletosWeb extends ACTbase{

    function ReporteNitRazon(){
        $this->objFunc = $this->create('MODDetalleBoletosWeb');
        $this->res = $this->objFunc->listarReporteNitRazon($this->objParam);
        //var_dump( $this->res);exit;
        //obtener titulo de reporte
        $titulo = 'Reporte Nit Razon';
        //Genera el nombre del archivo (aleatorio + titulo)
        $nombreArchivo = uniqid(md5(session_id()) . $titulo);

        $nombreArchivo .= '.xls';
        $this->objParam->addParametro('nombre_archivo', $nombreArchivo);
        $this->objParam->addParametro('datos', $this->res->datos);
        //Instancia la clase de excel
        $this->objReporteFormato = new RReporteNitRazonXLS($this->objParam);
        $this->objReporteFormato->generarDatos();
        $this->objReporteFormato->generarReporte();

        $this->mensajeExito = new Mensaje();
        $this->mensajeExito->setMensaje('EXITO', 'Reporte.php', 'Reporte generado','Se generó con éxito el reporte: ' . $nombreArchivo, 'control');
        $this->mensajeExito->setArchivoGenerado($nombreArchivo);
        $this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());
    }


}
?>