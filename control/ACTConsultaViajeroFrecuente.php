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
        /*Recuperamos la fecha actual*/
         $fecha_actual = date("d/m/Y");
        /*****************************/
        if ($this->objParam->getParametro('fecha_reg') == '') {
            /*Validar voucherCode Ismael Valdivia(28/11/2019)*/
            if ($this->objParam->getParametro('nro_boleto') == '' && $this->objParam->getParametro('pnr') == '') {
              $voucher_code = 'OB.FF.VO'.$this->objParam->getParametro('voucher_code');
            } else {
              $voucher_code = $this->objParam->getParametro('voucher_code');
            }
            /*************************************************/
            $data = array(  "FFID" => $this->objParam->getParametro('ffid'),
                "PNR" => $this->objParam->getParametro('pnr'),
                "TicketNumber" => $this->objParam->getParametro('nro_boleto'),
                "VoucherCode" => $voucher_code);
                //var_dump($data);
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
            /*****************Control para verificar si el servicio esta activo 28/11/2019 (Ismael Valdivia)***********************/
            if ($respuesta == '') {
              throw new Exception('Error en el servicio Elevate. favor consultar con el departamento de sistemas.');
            }
            /*********************************************************************************************************************/

            if ($respuesta["HasErrors"]== true) {
                throw new Exception('Error en el servicio Voucher.'.$respuesta["Message"]);
            } else {

            /*Modificacion de Validacion Voucher 28/11/2019 (Ismael Valdivia)*/
                $this->objParam->addParametro('message', $respuesta["Message"]);
                $this->objParam->addParametro('status', $respuesta["Status"]);
            /*******************************************************************************************************/

                $this->objFunc = $this->create('MODConsultaViajeroFrecuente');
                if ($this->objParam->insertar('id_consulta_viajero_frecuente')) {
                    $this->res = $this->objFunc->insertarConsultaViajeroFrecuente($this->objParam);
                } //else {
                    //$this->res = $this->objFunc->modificarConsultaViajeroFrecuente($this->objParam);
                //}
                $this->res->imprimirRespuesta($this->res->generarJson());
            }
        }
         else { //Condicion si existe la fecha reg se esta haciendo una modificacion y validamos si la modificacion es el mismo dia que se realizo el registro
                if ($this->objParam->getParametro('fecha_reg') == $fecha_actual) {
                 /*Validar voucherCode Ismael Valdivia(28/11/2019)*/
                 if ($this->objParam->getParametro('nro_boleto') == '' && $this->objParam->getParametro('pnr') == '') {
                  $voucher_code = 'OB.FF.VO'.$this->objParam->getParametro('voucher_code');
                } else {
                  $voucher_code = $this->objParam->getParametro('voucher_code');
                }
                 /*************************************************/
                 //var_dump("el voucher es ",$voucher_code);
                 $data = array(  "FFID" => $this->objParam->getParametro('ffid'),
                     "PNR" => strtoupper($this->objParam->getParametro('pnr')),
                     "TicketNumber" =>'930'.$this->objParam->getParametro('nro_boleto'),
                     "VoucherCode" => $voucher_code);
                     //var_dump($data);
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
                 //var_dump("el pass es:",strlen($data_string));exit;
                 $result = curl_exec($session);
                 curl_close($session);
                 $respuesta = json_decode($result,true);

                 /*****************Control para verificar si el servicio esta activo 28/11/2019 (Ismael Valdivia)***********************/
                 if ($respuesta == '') {
                   throw new Exception('Error en el servicio Elevate. favor consultar con el departamento de sistemas.');
                 }
                 /*********************************************************************************************************************/

                 if ($respuesta["HasErrors"]== true) {
                     throw new Exception('Error en el servicio Voucher.'.$respuesta["Message"]);
                 } else {

                 /*Modificacion de Validacion Voucher 28/11/2019 (Ismael Valdivia)*/
                     $this->objParam->addParametro('message', $respuesta["Message"]);
                     $this->objParam->addParametro('status', $respuesta["Status"]);
                 /*******************************************************************************************************/

                     $this->objFunc = $this->create('MODConsultaViajeroFrecuente');
                     /*if ($this->objParam->insertar('id_consulta_viajero_frecuente')) {
                         $this->res = $this->objFunc->insertarConsultaViajeroFrecuente($this->objParam);
                     } else {*/
                         $this->res = $this->objFunc->modificarConsultaViajeroFrecuente($this->objParam);
                     //}
                     $this->res->imprimirRespuesta($this->res->generarJson());
                 }
             }
              else {
                  throw new Exception('Solo se puede realizar la modificación el mismo dia que se registro el Voucher. la fecha de registro para el voucher es '.$this->objParam->getParametro('fecha_reg').' y la fecha de modificación es: '.$fecha_actual);
              }
         }
    }

    function eliminarConsultaViajeroFrecuente(){
        $this->objFunc=$this->create('MODConsultaViajeroFrecuente');
        $this->res=$this->objFunc->eliminarConsultaViajeroFrecuente($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

}

?>
