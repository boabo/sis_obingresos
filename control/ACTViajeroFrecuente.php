<?php
/**
*@package pXP
*@file gen-ACTViajeroFrecuente.php
*@author  (miguel.mamani)
*@date 12-12-2017 19:32:55
*@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
*/

class ACTViajeroFrecuente extends ACTbase{    
			
	function listarViajeroFrecuente(){
		$this->objParam->defecto('ordenacion','id_viajero_frecuente');

        if($this->objParam->getParametro('id_boleto_amadeus') != '') {
            $this->objParam->addFiltro("vfb.id_boleto_amadeus = " . $this->objParam->getParametro('id_boleto_amadeus'));
        }

		$this->objParam->defecto('dir_ordenacion','asc');
		if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
			$this->objReporte = new Reporte($this->objParam,$this);
			$this->res = $this->objReporte->generarReporteListado('MODViajeroFrecuente','listarViajeroFrecuente');
		} else{
			$this->objFunc=$this->create('MODViajeroFrecuente');
			
			$this->res=$this->objFunc->listarViajeroFrecuente($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
				
	function insertarViajeroFrecuente(){
        $data = array("FFID" => $this->objParam->getParametro('ffid'),
            "PNR" => $this->objParam->getParametro('pnr'),
            "TicketNumber" => $this->objParam->getParametro('ticketNumber'),
            "VoucherCode" => $this->objParam->getParametro('voucherCode'));
        $data_string = json_encode($data);
        $request = 'http://172.17.59.75/LoyaltyServer/wsBoA.ServiceLibrary.Amadeus.LTY.Services.LoyaltyRestService.svc/Loyalty/ValidateVoucher/';
        $session = curl_init($request);
        curl_setopt($session, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($session, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($session, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data_string))
        );
        $result = curl_exec($session);
        curl_close($session);
        $respuesta = json_decode($result,true);
        if ($respuesta["HasErrors"]== true) {

            throw new Exception('Error en el servicio Voucher.'.$respuesta["Message"]);

        } else {
            if ($respuesta["FFId"] == null and $respuesta["FullName"] == null) {
                throw new Exception('No tiene codigo de Vouche .');
            }
            if ($respuesta["Message"] == null) {
                $mjs = 'no';
            } else {
                $mjs = $respuesta["Message"];
            }

            $this->objParam->addParametro('id_pasajero_frecuente', $respuesta["FFID"]);
            $this->objParam->addParametro('nombre_completo', $respuesta["FullName"]);
            $this->objParam->addParametro('mensaje', $mjs);
            $this->objParam->addParametro('status', $respuesta["Status"]);
            $this->objFunc = $this->create('MODViajeroFrecuente');
            if ($this->objParam->insertar('id_viajero_frecuente')) {
                $this->res = $this->objFunc->insertarViajeroFrecuente($this->objParam);
            } else {
                $this->res = $this->objFunc->modificarViajeroFrecuente($this->objParam);
            }
            $this->res->imprimirRespuesta($this->res->generarJson());
        }
	}
						
	function eliminarViajeroFrecuente(){
			$this->objFunc=$this->create('MODViajeroFrecuente');	
		$this->res=$this->objFunc->eliminarViajeroFrecuente($this->objParam);
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
			
}

?>