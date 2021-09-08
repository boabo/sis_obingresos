<?php
/**
 *@package  BoA
 *@file     ACTExcluirAgencia.php
 *@author  (franklin.espinoza)
 *@date     11-08-2021 09:16:06
 *@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
 */

class ACTExcluirAgencia extends ACTbase{

    function listarExcluirAgencia(){
        $gestion =  $this->objParam->getParametro('gestion');
        $periodo =  $this->objParam->getParametro('periodo');

        $data = array(
            "gestion" => $gestion,
            "periodo" => $periodo
        );

        $json_data = json_encode($data);
        $s = curl_init();
        curl_setopt($s, CURLOPT_URL, 'http://sms.obairlines.bo/CommissionServices/ServiceComision.svc/GetExcludeAgencies');
        curl_setopt($s, CURLOPT_POST, true);
        curl_setopt($s, CURLOPT_POSTFIELDS, $json_data);
        curl_setopt($s, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($s, CURLOPT_CONNECTTIMEOUT, 20);
        curl_setopt($s, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($json_data))
        );
        /*$s = curl_init();
        curl_setopt($s, CURLOPT_URL, 'http://sms.obairlines.bo/CommissionServices/ServiceComision.svc/GetExcludeAgencies');
        curl_setopt($s, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($s, CURLOPT_CONNECTTIMEOUT, 20);
        curl_setopt($s, CURLOPT_HEADER, 0);*/
        $_out = curl_exec($s);
        $status = curl_getinfo($s, CURLINFO_HTTP_CODE);

        if (!$status) {
            throw new Exception("No se pudo conectar con el Servicio");
        }
        curl_close($s);

        $res = json_decode($_out);
        $res = json_decode($res->GetExcludeAgenciesResult);//var_dump('$res', $res);exit;


        /*if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
            $this->objReporte = new Reporte($this->objParam,$this);
            $this->res = $this->objReporte->generarReporteListado('MODFormaPago','listarFormaPago');
        } else{
            $this->objFunc=$this->create('MODFormaPago');

            $this->res=$this->objFunc->listarFormaPago($this->objParam);
        }*/

        $this->res = new Mensaje();
        $this->res->setMensaje(
            'EXITO',
            'driver.php',
            'Get Exclude Agencies',
            'Service Get Exclude Agencies',
            'control',
            'obingresos.ft_reportes_sel',
            'VEF_OVER_COM_SEL',
            'SEL'
        );

        $this->res->setTotal(count($res->Data));
        $this->res->datos = $res->Data;

        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function insertarExcluirAgencia(){


        if($this->objParam->insertar('id_exclude_agencie')){

            $iataCode =  $this->objParam->getParametro('iataCode');
            $officeId =  $this->objParam->getParametro('officeId');
            $f_ini =  implode('-',array_reverse(explode('/',$this->objParam->getParametro('f_ini'))));
            $f_fin =  implode('-',array_reverse(explode('/',$this->objParam->getParametro('f_fin'))));
            $obs =  $this->objParam->getParametro('obs');

            $data = array(
                "transaction" => 'INS',
                "id_exclude_agencie" => 0,
                "iataCode" => $iataCode,
                "officeId" => $officeId,
                "f_ini" => $f_ini,
                "f_fin" => $f_fin,
                "obs" => $obs
            );

            $json_data = json_encode($data);
            $s = curl_init();
            curl_setopt($s, CURLOPT_URL, 'http://sms.obairlines.bo/CommissionServices/ServiceComision.svc/ImeExcludeAgencies');
            curl_setopt($s, CURLOPT_POST, true);
            curl_setopt($s, CURLOPT_POSTFIELDS, $json_data);
            curl_setopt($s, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($s, CURLOPT_CONNECTTIMEOUT, 20);
            curl_setopt($s, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen($json_data))
            );
            $_out = curl_exec($s);
            $status = curl_getinfo($s, CURLINFO_HTTP_CODE);
            if (!$status) {
                throw new Exception("No se pudo conectar con el Servicio");
            }
            curl_close($s);

            $res = json_decode($_out);
            $res = json_decode($res->ImeExcludeAgenciesResult);

            $this->res = new Mensaje();
            $this->res->setMensaje(
                'EXITO',
                'driver.php',
                'Insert Exclude Agencies',
                'Service Insert Exclude Agencies',
                'control',
                'obingresos.ft_reporte_ime',
                'VEF_OVER_COM_IME',
                'IME'
            );
            $this->res->datos = $res->Data;

        } else{

            $iataCode =  $this->objParam->getParametro('iataCode');
            $officeId =  $this->objParam->getParametro('officeId');
            $f_ini =  implode('-',array_reverse(explode('/',$this->objParam->getParametro('f_ini'))));
            $f_fin =  implode('-',array_reverse(explode('/',$this->objParam->getParametro('f_fin'))));
            $obs =  $this->objParam->getParametro('obs');
            $id_exclude_agencie =  $this->objParam->getParametro('id_exclude_agencie');

            $data = array(
                "transaction" => 'MOD',
                "id_exclude_agencie" => $id_exclude_agencie,
                "iataCode" => $iataCode,
                "officeId" => $officeId,
                "f_ini" => $f_ini,
                "f_fin" => $f_fin,
                "obs" => $obs
            );

            $json_data = json_encode($data);
            $s = curl_init();
            curl_setopt($s, CURLOPT_URL, 'http://sms.obairlines.bo/CommissionServices/ServiceComision.svc/ImeExcludeAgencies');
            curl_setopt($s, CURLOPT_POST, true);
            curl_setopt($s, CURLOPT_POSTFIELDS, $json_data);
            curl_setopt($s, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($s, CURLOPT_CONNECTTIMEOUT, 20);
            curl_setopt($s, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen($json_data))
            );
            $_out = curl_exec($s);//var_dump('$response', $_out);exit;
            $status = curl_getinfo($s, CURLINFO_HTTP_CODE);
            if (!$status) {
                throw new Exception("No se pudo conectar con el Servicio");
            }
            curl_close($s);

            $res = json_decode($_out);
            $res = json_decode($res->ImeExcludeAgenciesResult);

            $this->res = new Mensaje();
            $this->res->setMensaje(
                'EXITO',
                'driver.php',
                'Update Exclude Agencies',
                'Service Update Exclude Agencies',
                'control',
                'obingresos.ft_reporte_ime',
                'VEF_OVER_COM_IME',
                'IME'
            );
            $this->res->datos = $res->Data;
            //var_dump('$this->res->datos', $res->Data[0]->Result == 1,$res->Data[0]->Result=='1');exit;
        }
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function eliminarExcluirAgencia(){

        $id_exclude_agencie =  $this->objParam->arreglo_parametros[0]['id_exclude_agencie'];
        $data = array(
            "transaction" => 'DEL',
            "id_exclude_agencie" => $id_exclude_agencie,
            "iataCode" => '',
            "officeId" => '',
            "f_ini" => '',
            "f_fin" => '',
            "obs" => ''
        );
        $json_data = json_encode($data);
        $s = curl_init();
        curl_setopt($s, CURLOPT_URL, 'http://sms.obairlines.bo/CommissionServices/ServiceComision.svc/ImeExcludeAgencies');
        curl_setopt($s, CURLOPT_POST, true);
        curl_setopt($s, CURLOPT_POSTFIELDS, $json_data);
        curl_setopt($s, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($s, CURLOPT_CONNECTTIMEOUT, 20);
        curl_setopt($s, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($json_data))
        );
        $_out = curl_exec($s);//var_dump('$response', $_out);exit;
        $status = curl_getinfo($s, CURLINFO_HTTP_CODE);
        if (!$status) {
            throw new Exception("No se pudo conectar con el Servicio");
        }
        curl_close($s);

        $res = json_decode($_out);
        $res = json_decode($res->ImeExcludeAgenciesResult);

        $this->res = new Mensaje();
        $this->res->setMensaje(
            'EXITO',
            'driver.php',
            'Delete Exclude Agencies',
            'Service Delete Exclude Agencies',
            'control',
            'obingresos.ft_reporte_ime',
            'VEF_OVER_COM_IME',
            'IME'
        );
        $this->res->datos = $res->Data;

        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function registrarExcluirAgencia() {
        $this->funciones = $this->create('MODExcluirAgencia');
        $this->res=$this->funciones->registrarExcluirAgencia();
        //Se imprime el json del arbol
        $this->res->imprimirRespuesta($this->res->generarJson());
    }
}
?>