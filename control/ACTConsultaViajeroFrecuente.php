<?php
/**
 *@package pXP
 *@file gen-ACTConsultaViajeroFrecuente.php
 *@author  (miguel.mamani)
 *@date 15-12-2017 14:59:25
 *@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
 */

class ACTConsultaViajeroFrecuente extends ACTbase{

    function listarConsultaViajeroFrecuente(){
        $this->objParam->defecto('ordenacion','id_consulta_viajero_frecuente');

        $this->objParam->defecto('dir_ordenacion','asc');
        if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
            $this->objReporte = new Reporte($this->objParam,$this);
            $this->res = $this->objReporte->generarReporteListado('MODConsultaViajeroFrecuente','listarConsultaViajeroFrecuente');
        } else{
            $this->objFunc=$this->create('MODConsultaViajeroFrecuente');

            $this->res=$this->objFunc->listarConsultaViajeroFrecuente($this->objParam);
        }
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function insertarConsultaViajeroFrecuente(){

        $data = array(  "FFID" => $this->objParam->getParametro('ffid'),
            "PNR" => '',
            "TicketNumber" => $this->objParam->getParametro('nro_boleto'),
            "VoucherCode" => 'OB.FF.VO'.$this->objParam->getParametro('voucher_code'));
        $data_string = json_encode($data);
        $request = 'https://elevate.boa.bo/LoyaltyRestService/Api/LoyaltyRest/ValidateVoucher';
        $user_id = '1';
        $passwd = 'F6C66224B072D38B5C2E3859179CED04';

        $session = curl_init($request);
        curl_setopt($session, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($session, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($session, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($session, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data_string),
                'LoyaltyAuthorization: ' .  strtoupper(md5($user_id . $passwd)),
                'UserId: ' . $user_id)
        );
        $result = curl_exec($session);
        curl_close($session);
        $respuesta = json_decode($result,true);
        if ($respuesta["HasErrors"]== true) {
            throw new Exception('Error en el servicio Voucher.'.$respuesta["Message"]);
        } else {

            $this->objParam->addParametro('message', $respuesta["Message"]);
            $this->objParam->addParametro('status', $respuesta["Status"]);
            $this->objFunc = $this->create('MODConsultaViajeroFrecuente');
            if ($this->objParam->insertar('id_consulta_viajero_frecuente')) {
                $this->res = $this->objFunc->insertarConsultaViajeroFrecuente($this->objParam);
            } else {
                $this->res = $this->objFunc->modificarConsultaViajeroFrecuente($this->objParam);
            }
            $this->res->imprimirRespuesta($this->res->generarJson());
        }
    }

    function eliminarConsultaViajeroFrecuente(){
        $this->objFunc=$this->create('MODConsultaViajeroFrecuente');
        $this->res=$this->objFunc->eliminarConsultaViajeroFrecuente($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

}

?>