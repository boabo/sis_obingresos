<?php
/**
 *@package pXP
 *@file gen-ACTBoleto.php
 *@author  (jrivera)
 *@date 06-01-2016 22:42:25
 *@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
 */
include(dirname(__FILE__).'/../reportes/RBoleto.php');
include(dirname(__FILE__).'/../reportes/RBoletoBOPDF.php');
include(dirname(__FILE__).'/../reportes/RBoletoBRPDF.php');
include(dirname(__FILE__).'/../reportes/RReporteBoletoResiberVentasWeb.php');

class ACTBoleto extends ACTbase{
    var $objParamAux;

    function listarBoleto(){
        $this->objParam->defecto('ordenacion','id_boleto');

        $this->objParam->defecto('dir_ordenacion','desc');

        if ($this->objParam->getParametro('id_agencia') != '') {
            $this->objParam->addFiltro("bol.id_agencia = ". $this->objParam->getParametro('id_agencia'));
        }

        if ($this->objParam->getParametro('id_punto_venta') != '') {
            $this->objParam->addFiltro("bol.id_punto_venta = ". $this->objParam->getParametro('id_punto_venta'));
        }

        if ($this->objParam->getParametro('id_usuario_cajero') != '') {
            $this->objParam->addFiltro("bol.id_usuario_cajero = ". $this->objParam->getParametro('id_usuario_cajero'));
        }

        if ($this->objParam->getParametro('localizador') != '') {
            $this->objParam->addFiltro("bol.localizador = ''". $this->objParam->getParametro('localizador')."''");
        }

        if ($this->objParam->getParametro('fecha_emision') != '') {
            $this->objParam->addFiltro("bol.fecha_emision = ''". $this->objParam->getParametro('fecha_emision')."''");
        }

        if ($this->objParam->getParametro('estado') != '') {
            if ($this->objParam->getParametro('estado') == 'borrador') {
                $this->objParam->addFiltro("(bol.id_usuario_reg = ". $_SESSION["ss_id_usuario"] . " or exists(	select 1
																												from segu.tusuario_rol
																												where id_rol = 1 and estado_reg = ''activo'' and
																												id_usuario = ". $_SESSION["ss_id_usuario"] . " ))");
            }

            $this->objParam->addFiltro("bol.estado = ''". $this->objParam->getParametro('estado')."''");
        }

        /*if ($this->objParam->getParametro('estado') != '') {
            if ($this->objParam->getParametro('estado') == 'pagado') {
                $this->objParam->addFiltro("bol.liquido = bol.monto_pagado_moneda_boleto ");
            } else {
                $this->objParam->addFiltro("bol.liquido > bol.monto_pagado_moneda_boleto ");
            }
        }*/

        if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
            $this->objReporte = new Reporte($this->objParam,$this);
            $this->res = $this->objReporte->generarReporteListado('MODBoleto','listarBoleto');
        } else{
            $this->objFunc=$this->create('MODBoleto');

            $this->res=$this->objFunc->listarBoleto($this->objParam);
        }
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function listarPNRBoleto(){
        $this->objParam->defecto('ordenacion','fecha_emision');

        $this->objParam->defecto('dir_ordenacion','desc');

        /*if ($this->objParam->getParametro('id_agencia') != '') {
            $this->objParam->addFiltro("bol.id_agencia = ". $this->objParam->getParametro('id_agencia'));
        }*/

        if ($this->objParam->getParametro('id_punto_venta') != '') {
            $this->objParam->addFiltro("nr.id_punto_venta = ". $this->objParam->getParametro('id_punto_venta'));
        }

        if ($this->objParam->getParametro('estado') != '') {
            if ($this->objParam->getParametro('estado') == 'borrador') {
                $this->objParam->addFiltro("(nr.id_usuario_reg = ". $_SESSION["ss_id_usuario"] . " or exists(	select 1
																												from segu.tusuario_rol
																												where id_rol = 1 and estado_reg = ''activo'' and
																												id_usuario = ". $_SESSION["ss_id_usuario"] . " ))");
            }

            //$this->objParam->addFiltro("bol.estado = ''". $this->objParam->getParametro('estado')."''");
        }

        if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
            $this->objReporte = new Reporte($this->objParam,$this);
            $this->res = $this->objReporte->generarReporteListado('MODBoleto','listarPNRBoleto');
        } else{
            $this->objFunc=$this->create('MODBoleto');

            $this->res=$this->objFunc->listarPNRBoleto($this->objParam);
        }
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function modificarFpPNRBoleto(){
        $this->objFunc=$this->create('MODBoleto');
        $this->res=$this->objFunc->modificarFpPNRBoleto($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function getBoleto(){




        $this->objFunc=$this->create('MODBoleto');

        $this->res=$this->objFunc->getBoletoServicio($this->objParam);

        $boleto = $this->res->datos[0]['boleto'];

        $detalle = $this->res->datos[0]['detalle'];
        $pagos = $this->res->datos[0]['pagos'];
        $this->res->datos = json_decode($boleto,true);
        $this->res->datos['vuelos'] = json_decode($detalle,true);
        $this->res->datos['formas_pago'] = json_decode($pagos,true);


        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function insertarBoleto(){
        $this->objFunc=$this->create('MODBoleto');
        if($this->objParam->insertar('id_boleto')){
            $this->res=$this->objFunc->insertarBoleto($this->objParam);
        } else{
            $this->res=$this->objFunc->modificarBoleto($this->objParam);
        }
        $this->res->imprimirRespuesta($this->res->generarJson());
    }



    function modificarBoletoVenta(){
        $this->objParam->defecto('ordenacion','id_boleto');
        $this->objParam->defecto('dir_ordenacion','desc');
        $this->objParam->defecto('puntero','0');
        $this->objParam->defecto('cantidad','1');

        if($this->objParam->insertar('ids_seleccionados')) {
            $this->objParamAux = $this->objParam;
            //si el boleto tiene conjuncion registramos el boleto de conjuncion
            if ($this->objParam->getParametro('tiene_conjuncion') == 'true'){

                //si no existe nro de boleto lanzamos error si existe aplicamos filtro al listado
                if ($this->objParam->getParametro('nro_boleto_conjuncion') != '') {
                    $this->objParam->addParametro('nro_boleto',"930".$this->objParam->getParametro('nro_boleto_conjuncion'));
                    $this->objParam->addFiltro("bol.nro_boleto = ''". $this->objParam->getParametro('nro_boleto')."''");
                } else {
                    throw new Exception('Debe ingresar el numero de boleto de la conjuncion.');
                }
                //listamos el boleto
                $this->objFunc=$this->create('MODBoleto');
                $this->res=$this->objFunc->listarBoleto($this->objParam);
                if ($this->res->getTipo()=='ERROR') {
                    $this->res->imprimirRespuesta($this->res->generarJson());
                    //si el boleto no esta registrado se registra
                } else {
                    if ($this->res->getTotal() == 0) {
                        $cantidad_vuelos=$this->obtenerBoletoFromServicio();
                    }
                }
            }
            //Se realiza la modificacion normal despues de registra la conjuncion
            $this->objParam = $this->objParamAux;
            $this->objFunc=$this->create('MODBoleto');
            $this->res=$this->objFunc->modificarBoletoVenta($this->objParam);
        } else {
            $this->objFunc=$this->create('MODBoleto');
            $this->res=$this->objFunc->modificarFpGrupo($this->objParam);
        }

        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function modificarBoletoAmadeusVenta(){
        $this->objParam->defecto('ordenacion','id_boleto_amadeus');
        $this->objParam->defecto('dir_ordenacion','desc');
        $this->objParam->defecto('puntero','0');
        $this->objParam->defecto('cantidad','1');

        if($this->objParam->insertar('ids_seleccionados')) {
            //$this->objParamAux = $this->objParam;
            //Se realiza la modificacion normal despues de registra la conjuncion
            //$this->objParam = $this->objParamAux;
            $this->objFunc=$this->create('MODBoleto');
            $this->res=$this->objFunc->modificarBoletoAmadeusVenta($this->objParam);
        } else {
            $this->objFunc=$this->create('MODBoleto');
            $this->res=$this->objFunc->modificarAmadeusFpGrupo($this->objParam);
        }

        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function getBoletoServicio(){
        $this->objParam->defecto('ordenacion','id_boleto');
        $this->objParam->defecto('dir_ordenacion','desc');
        $this->objParam->defecto('puntero','0');
        $this->objParam->defecto('cantidad','1');

        if ($this->objParam->getParametro('nro_boleto') != '') {
            $this->objParam->addParametro('nro_boleto',"930".$this->objParam->getParametro('nro_boleto'));
            $this->objParam->addFiltro("bol.nro_boleto = ''". $this->objParam->getParametro('nro_boleto')."''");
        } else {
            throw new Exception('Debe ingresar el numero de boleto para cargar los datos.');
        }
        $this->objFunc=$this->create('MODBoleto');
        $this->res=$this->objFunc->listarBoleto($this->objParam);
        if ($this->res->getTipo()=='ERROR') {
            $this->res->imprimirRespuesta($this->res->generarJson());
        } else {
            if ($this->res->getTotal() == 1) {
                $this->res->imprimirRespuesta($this->res->generarJson());
            } else {
                $cantidad_vuelos = $this->obtenerBoletoFromServicio();

                $this->objFunc=$this->create('MODBoleto');
                $this->res=$this->objFunc->listarBoleto($this->objParam);
                $this->res->setExtraData(array(	"cantidad_vuelos"=>$cantidad_vuelos));
                $this->res->imprimirRespuesta($this->res->generarJson());
            }
        }
    }

    function eliminarBoleto(){
        $this->objFunc=$this->create('MODBoleto');
        $this->res=$this->objFunc->eliminarBoleto($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function cambiarRevisionBoleto(){
        $this->objFunc=$this->create('MODBoleto');
        $this->res=$this->objFunc->cambiarRevisionBoleto($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function cambiaEstadoBoleto(){
        $this->objFunc=$this->create('MODBoleto');
        $this->res=$this->objFunc->cambiaEstadoBoleto($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function traerBoletos(){

        if ($this->objParam->getParametro('id_punto_venta') != '') {
            $this->objParam->addFiltro("bol.id_punto_venta = ". $this->objParam->getParametro('id_punto_venta'));
        }

        if ($this->objParam->getParametro('fecha') != '') {
            $fecha = $this->objParam->getParametro('fecha');
        }else{
            $fecha = date("Ymd");
        }

        $this->objParam->addFiltro("bol.fecha_emision = ''". date("d-m-Y")."''");

        if ($this->objParam->getParametro('reporte') == 'reporte') {
            $this->objFunc = $this->create('MODBoleto');
            $this->res = $this->objFunc->eliminarBoletosAmadeus($this->objParam);
        }
        if ($this->objParam->getParametro('moneda_base') != '') {
            $mone_base = $this->objParam->getParametro('moneda_base');
        }
        if ($this->objParam->getParametro('officeId_agencia') != '') {
            $officeid = $this->objParam->getParametro('officeId_agencia');
        }else{
            $this->objParam->addParametro('fecha', $fecha);
            $this->objParam->addParametro('moneda', $mone_base);
            $this->objFunc=$this->create('sis_ventas_facturacion/MODPuntoVenta');
            $this->res=$this->objFunc->obtenerOfficeID($this->objParam);

            $datos = $this->res->getDatos();

            $officeid = $datos[0]['officeid'];
            $id_agencia = $datos[0]['id_agencia'];
            $identificador_reporte = $datos[0]['identificador_reporte'];
        }
        //boletos en bolivianos
        $data = array("numberItems"=>"5","lastItemNumber"=>$identificador_reporte,"officeID"=>$officeid, "dateFrom"=>$fecha,"dateTo"=>$fecha,"monetary"=>$mone_base);
        $data_string = json_encode($data);
        $request =  'http://172.17.58.45/esb/RITISERP.svc/Boa_RITRetrieveSales_JS';
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

        $respuesta = json_decode($result);
        //var_dump($respuesta); exit;

        //$xmlRespuesta = new SimpleXMLElement(str_replace("utf-16", "utf-8",$respuesta->Boa_RITRetrieveSalesResult));
        $xmlRespuesta = json_decode($respuesta->Boa_RITRetrieveSales_JSResult);

        if(isset($xmlRespuesta->queryReportDataDetails)) {
            $idReporte = $xmlRespuesta->queryReportDataDetails->actionDetails->lastItemsDetails->lastItemIdentifier;
            $moneda = $xmlRespuesta->queryReportDataDetails->currencyInfo->currencyDetails->currencyIsoCode;
            //var_dump($idReporte);
            foreach ($xmlRespuesta->queryReportDataDetails->queryReportDataOfficeGroup[0]->documentData as $boleto) {

                $this->objParam->addParametro('id_punto_venta', $this->objParam->getParametro('id_punto_venta'));
                $this->objParam->addParametro('identificador_reporte', $idReporte);
                $this->objParam->addParametro('nro_boleto', $boleto->documentNumber->documentDetails->number);

                if(!isset($boleto->bookingAgent->originIdentification->originatorId)) {
                    $this->objParam->addParametro('agente_venta', '');
                }else{
                    $this->objParam->addParametro('agente_venta', $boleto->bookingAgent->originIdentification->originatorId);
                }
                $this->objParam->addParametro('fecha_emision', $fecha);
                $this->objParam->addParametro('id_agencia', $id_agencia);

                if ($boleto->transactionDataDetails->transactionDetails->code == 'CANX') {
                    $this->objParam->addParametro('voided', 'si');
                }
                if ($boleto->transactionDataDetails->transactionDetails->code == 'CANN') {
                    $this->objParam->addParametro('voided', 'si');
                }
                if ($boleto->transactionDataDetails->transactionDetails->code == 'TKTT') {
                    $this->objParam->addParametro('voided', 'no');
                }

                $this->objParam->addParametro('pasajero', $boleto->passengerName->paxDetails->surname);

                foreach ($boleto->monetaryInformation->otherMonetaryDetails as $montoBoleto) {
                    $total=0;

                    if ($montoBoleto->typeQualifier == 'T') {

                        if($montoBoleto->amount!=' '){
                            $total = $montoBoleto->amount;
                        }

                        $this->objParam->addParametro('total', $total);
                        $this->objParam->addParametro('liquido', $total);
                        $this->objParam->addParametro('neto', $total);

                    } else {
                        if ($montoBoleto->typeQualifier == 'TTX') {
                            if($montoBoleto->amount!=' '){
                                $total = $montoBoleto->amount;
                            }
                            $this->objParam->addParametro('tasas', $total);
                        } else {
                            if ($montoBoleto->typeQualifier == 'F') {
                                if($montoBoleto->amount!=' '){
                                    $total = $montoBoleto->amount;
                                }
                                $this->objParam->addParametro('comision', $total);
                            } else {
                                if ($montoBoleto->typeQualifier == 'OB') {
                                    if($montoBoleto->amount!=' '){
                                        $total = $montoBoleto->amount;
                                    }
                                    $this->objParam->addParametro('carrier_fees', $total);
                                }
                            }
                        }
                    }
                }
                $this->objParam->addParametro('moneda', $moneda);

                $this->objParam->addParametro('forma_pago_amadeus', $boleto->fopDetails->fopDescription->formOfPayment->type);
                /* inicio forma de pago */
                if ($boleto->fopDetails->fopDescription->formOfPayment->type == 'CA') {
                    //forma de pago cash
                    $this->objParam->addParametro('fp', 'CA');
                    $this->objParam->addParametro('valor_fp', $boleto->fopDetails->monetaryInfo->monetaryDetails->amount);
                }

                if ($boleto->fopDetails->fopDescription->formOfPayment->type == 'MX') {
                    //exception valor forma de pago no definida
                    //throw new Exception(__METHOD__.'FORMA DE PAGO MX NO DEFINIDO');
                    $this->objParam->addParametro('fp', 'CA');
                    $this->objParam->addParametro('valor_fp', 0);
                }

                //if ($boleto->fopDetails->monetaryInfo->monetaryDetails->amount->__toString() == '' || $boleto->fopDetails->monetaryInfo->monetaryDetails->amount->__toString() == ' ') {
                if (!isset( $boleto->fopDetails->fopDescription->formOfPayment->type)) {
                    //exception valor forma de pago no definida
                    //throw new Exception(__METHOD__ . 'VALOR FORMA DE PAGO NO DEFINIDO');
                    $this->objParam->addParametro('fp', '');
                    $this->objParam->addParametro('valor_fp', 0);
                }else {

                    if ($boleto->fopDetails->fopDescription->formOfPayment->type == 'CC') {
                        if ($boleto->fopDetails->fopDescription->formOfPayment->vendorCode == 'VI') {
                            $this->objParam->addParametro('fp', 'CCVI');
                        }else {
                            if ($boleto->fopDetails->fopDescription->formOfPayment->vendorCode == 'CA') {
                                $this->objParam->addParametro('fp', 'CCCA');
                            } else {
                                if ($boleto->fopDetails->fopDescription->formOfPayment->vendorCode == 'AX') {
                                    $this->objParam->addParametro('fp', 'CCAX');
                                }else{
                                    $this->objParam->addParametro('fp', '');
                                }
                            }
                        }
                        $this->objParam->addParametro('valor_fp', $boleto->fopDetails->monetaryInfo->monetaryDetails->amount);
                    }else{
                        if($boleto->fopDetails->fopDescription->formOfPayment->type=='' || $boleto->fopDetails->fopDescription->formOfPayment->type==' '){
                            $this->objParam->addParametro('fp', '');
                            $this->objParam->addParametro('valor_fp', 0);
                        }
                    }
                }

                /* fin forma de pago */
                if ($boleto->transactionDataDetails->transactionDetails->code != 'CANN') {
                    $this->objParam->addParametro('localizador', $boleto->reservationInformation[0]->controlNumber);
                }else{
                    $this->objParam->addParametro('localizador', ' ');
                }

                if ($this->objParam->getParametro('id_usuario_cajero') != '') {
                    $this->objParam->addParametro('id_usuario_cajero', $this->objParam->getParametro('id_usuario_cajero'));
                    $this->objFunc = $this->create('MODBoleto');
                    $this->res = $this->objFunc->actualizaBoletoServicioAmadeus($this->objParam);
                } else {
                    if ($this->objParam->getParametro('reporte') == 'reporte') {
                        $this->objFunc = $this->create('MODBoleto');
                        $this->res = $this->objFunc->insertarBoletoReporteServicioAmadeus($this->objParam);
                    }else{
                        $this->objFunc = $this->create('MODBoleto');
                        $this->res = $this->objFunc->insertarBoletoServicioAmadeus($this->objParam);
                    }
                }
                if ($this->res->getTipo() == 'ERROR') {
                    $this->res->imprimirRespuesta($this->res->generarJson());
                    exit;
                }
            }
        }

        $this->objParam->addParametro('fecha', $fecha);
        $this->objParam->addParametro('moneda', "USD");
        $this->objFunc=$this->create('sis_ventas_facturacion/MODPuntoVenta');
        $this->res=$this->objFunc->obtenerOfficeID($this->objParam);

        $datos = $this->res->getDatos();
        //var_dump($datos); exit;
        $officeid = $datos[0]['officeid'];
        $id_agencia = $datos[0]['id_agencia'];
        $identificador_reporte = $datos[0]['identificador_reporte'];
        ////boletos en dolares
        //$data = array("numberItems"=>"0","lastItemNumber"=>"0","officeID"=>"SRZOB0104","dateFrom"=>"20170808","dateTo"=>"20170808","monetary"=>"USD");
        $data = array("numberItems"=>"5","lastItemNumber"=>$identificador_reporte,"officeID"=>$officeid, "dateFrom"=>$fecha,"dateTo"=>$fecha,"monetary"=>"USD");
        $data_string = json_encode($data);
        $request =  'http://172.17.58.45/esb/RITISERP.svc/Boa_RITRetrieveSales';
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

        $respuesta = json_decode($result);

        $xmlRespuesta = json_decode($respuesta->Boa_RITRetrieveSales_JSResult);

        if(isset($xmlRespuesta->queryReportDataDetails)) {
            $idReporte = $xmlRespuesta->queryReportDataDetails->actionDetails->lastItemsDetails->lastItemIdentifier;
            $moneda = $xmlRespuesta->queryReportDataDetails->currencyInfo->currencyDetails->currencyIsoCode;

            foreach ($xmlRespuesta->queryReportDataDetails->queryReportDataOfficeGroup[0]->documentData as $boleto) {

                $this->objParam->addParametro('id_punto_venta', $this->objParam->getParametro('id_punto_venta'));
                $this->objParam->addParametro('identificador_reporte', $idReporte);
                $this->objParam->addParametro('nro_boleto', $boleto->documentNumber->documentDetails->number);

                if(!isset($boleto->bookingAgent->originIdentification->originatorId)) {
                    $this->objParam->addParametro('agente_venta', '');
                }else{
                    $this->objParam->addParametro('agente_venta', $boleto->bookingAgent->originIdentification->originatorId);
                }

                $this->objParam->addParametro('fecha_emision', $fecha);
                $this->objParam->addParametro('id_agencia', $id_agencia);

                if ($boleto->transactionDataDetails->transactionDetails->code == 'CANX') {
                    $this->objParam->addParametro('voided', 'si');
                }

                if ($boleto->transactionDataDetails->transactionDetails->code == 'CANN') {
                    $this->objParam->addParametro('voided', 'si');
                }

                if ($boleto->transactionDataDetails->transactionDetails->code == 'TKTT') {
                    $this->objParam->addParametro('voided', 'no');
                }
                $this->objParam->addParametro('pasajero', $boleto->passengerName->paxDetails->surname);
                foreach ($boleto->monetaryInformation->otherMonetaryDetails as $montoBoleto) {
                    $total=0;
                    if ($montoBoleto->typeQualifier == 'T') {
                        $this->objParam->addParametro('total', $montoBoleto->amount);
                        $this->objParam->addParametro('liquido', $montoBoleto->amount);
                        $this->objParam->addParametro('neto', $montoBoleto->amount);
                    } else {
                        if ($montoBoleto->typeQualifier == 'TTX') {
                            $this->objParam->addParametro('tasas', $montoBoleto->amount);
                        } else {
                            if ($montoBoleto->typeQualifier == 'F') {
                                $this->objParam->addParametro('comision', $montoBoleto->amount);
                            } else {
                                if ($montoBoleto->typeQualifier == 'OB') {
                                    $this->objParam->addParametro('carrier_fees', $montoBoleto->amount);
                                }
                            }
                        }
                    }
                }
                $this->objParam->addParametro('moneda', $moneda);
                $this->objParam->addParametro('forma_pago_amadeus', $boleto->fopDetails->fopDescription->formOfPayment->type);
                /* inicio forma de pago */
                if ($boleto->fopDetails->fopDescription->formOfPayment->type == 'CA') {
                    //forma de pago cash
                    $this->objParam->addParametro('fp', 'CA');
                    $this->objParam->addParametro('valor_fp', $boleto->fopDetails->monetaryInfo->monetaryDetails->amount);
                }

                if ($boleto->fopDetails->fopDescription->formOfPayment->type == 'MX') {
                    //exception valor forma de pago no definida
                    //throw new Exception(__METHOD__.'FORMA DE PAGO MX NO DEFINIDO');
                    $this->objParam->addParametro('fp', 'CA');
                    $this->objParam->addParametro('valor_fp', 0);
                }

                if (!isset( $boleto->fopDetails->fopDescription->formOfPayment->type)) {
                    //exception valor forma de pago no definida
                    $this->objParam->addParametro('fp', '');
                    $this->objParam->addParametro('valor_fp', 0);
                }else {
                    if ($boleto->fopDetails->fopDescription->formOfPayment->type == 'CC') {
                        if ($boleto->fopDetails->fopDescription->formOfPayment->vendorCode == 'VI') {
                            $this->objParam->addParametro('fp', 'CCVI');
                        } else {
                            if ($boleto->fopDetails->fopDescription->formOfPayment->vendorCode == 'CA') {
                                $this->objParam->addParametro('fp', 'CCCA');
                            } else {
                                if ($boleto->fopDetails->fopDescription->formOfPayment->vendorCode == 'AX') {
                                    $this->objParam->addParametro('fp', 'CCAX');
                                } else {
                                    $this->objParam->addParametro('fp', '');
                                }
                            }
                        }
                        $this->objParam->addParametro('valor_fp', $boleto->fopDetails->monetaryInfo->monetaryDetails->amount);
                    } else {
                        if ($boleto->fopDetails->fopDescription->formOfPayment->type == '' || $boleto->fopDetails->fopDescription->formOfPayment->type == ' ') {
                            $this->objParam->addParametro('fp', '');
                            $this->objParam->addParametro('valor_fp', 0);
                        }
                    }
                }

                /* fin forma de pago */
                if ($boleto->transactionDataDetails->transactionDetails->code != 'CANN') {
                    $this->objParam->addParametro('localizador', $boleto->reservationInformation[0]->controlNumber);
                }else{
                    $this->objParam->addParametro('localizador', ' ');
                }

                if ($this->objParam->getParametro('id_usuario_cajero') != '') {
                    $this->objParam->addParametro('id_usuario_cajero', $this->objParam->getParametro('id_usuario_cajero'));
                    $this->objFunc = $this->create('MODBoleto');
                    $this->res = $this->objFunc->actualizaBoletoServicioAmadeus($this->objParam);
                } else {
                    if ($this->objParam->getParametro('reporte') == 'reporte') {
                        $this->objFunc = $this->create('MODBoleto');
                        $this->res = $this->objFunc->insertarBoletoReporteServicioAmadeus($this->objParam);
                    }else {
                        $this->objFunc = $this->create('MODBoleto');
                        $this->res = $this->objFunc->insertarBoletoServicioAmadeus($this->objParam);
                    }
                }

                if ($this->res->getTipo() == 'ERROR') {
                    $this->res->imprimirRespuesta($this->res->generarJson());
                    exit;
                }
            }
        }
        if ($this->objParam->getParametro('reporte') == 'reporte') {
            if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
                $this->objReporte = new Reporte($this->objParam,$this);
                $this->res = $this->objReporte->generarReporteListado('MODBoleto','listarBoletoAmadeus');
                $this->res->imprimirRespuesta($this->res->generarJson());
            }else {
                $this->objFunc = $this->create('MODBoleto');
                $this->res = $this->objFunc->listarBoletoAmadeus($this->objParam);
                $this->res->imprimirRespuesta($this->res->generarJson());
            }
        }else {
            $this->objFunc=$this->create('MODBoleto');
            $this->res=$this->objFunc->listarBoletosEmitidosAmadeus($this->objParam);
            $this->res->imprimirRespuesta($this->res->generarJson());
        }

    }

    function listarBoletosEmitidosAmadeus(){

        if ($this->objParam->getParametro('pes_estado') != '') {
            if ($this->objParam->getParametro('pes_estado') == 'revisados') {
                $this->objParam->addFiltro(" bol.estado = ''revisado'' ");
            }else{
                $this->objParam->addFiltro(" bol.estado = ''borrador'' ");
            }
        }

        if ($this->objParam->getParametro('id_punto_venta') != '') {
            //$this->objParam->addParametro('id_punto_venta', $this->objParam->getParametro('id_punto_venta'));
            $this->objParam->addFiltro("bol.id_punto_venta = ". $this->objParam->getParametro('id_punto_venta'));
        }

        if ($this->objParam->getParametro('id_usuario_cajero') != '') {
            $this->objParam->addFiltro("bol.id_usuario_cajero = ". $this->objParam->getParametro('id_usuario_cajero'));
        }
        //var_dump($this->objParam->getParametro('fecha')); exit;
        if ($this->objParam->getParametro('fecha') != '') {
            $fecha = $this->objParam->getParametro('fecha');
            $this->objParam->addFiltro("bol.fecha_emision = ''". $fecha."''");
        }/*else{
			$fecha = date("Ymd");
		}*/

        //$this->objParam->addFiltro("bol.fecha_emision = ''". $fecha."''");

        /*if ($this->objParam->getParametro('reporte') == 'reporte') {
            if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
                $this->objReporte = new Reporte($this->objParam,$this);
                $this->res = $this->objReporte->generarReporteListado('MODBoleto','listarBoletoAmadeus');
                $this->res->imprimirRespuesta($this->res->generarJson());
            }else {
                $this->objFunc = $this->create('MODBoleto');
                $this->res = $this->objFunc->listarBoletoAmadeus($this->objParam);
                $this->res->imprimirRespuesta($this->res->generarJson());
            }
        }else {*/
        if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
            $this->objReporte = new Reporte($this->objParam,$this);
            $this->res = $this->objReporte->generarReporteListado('MODBoleto','listarBoletosEmitidosAmadeus');
            $this->res->imprimirRespuesta($this->res->generarJson());
        }else {
            $this->objFunc = $this->create('MODBoleto');
            $this->res = $this->objFunc->listarBoletosEmitidosAmadeus($this->objParam);
            $this->res->imprimirRespuesta($this->res->generarJson());
        }
        //}

    }

    function traerBoletosAgenciaAmadeus(){

        if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
            $this->objReporte = new Reporte($this->objParam,$this);
            $this->res = $this->objReporte->generarReporteListado('MODBoleto','listarBoletoAmadeus');
            $this->res->imprimirRespuesta($this->res->generarJson());
        }else {
            if ($this->objParam->getParametro('fecha') != '') {
                $fecha = $this->objParam->getParametro('fecha');
            }else{
                $fecha = date("Ymd");
            }

            if ($this->objParam->getParametro('reporte') == 'reporte') {
                $this->objFunc = $this->create('MODBoleto');
                $this->res = $this->objFunc->eliminarBoletosAmadeus($this->objParam);
            }

            if ($this->objParam->getParametro('moneda_base') != '') {
                $mone_base = $this->objParam->getParametro('moneda_base');
            }

            $this->objFunc=$this->create('MODAgencia');
            $this->res=$this->objFunc->obtenerOfficeIDsAgencias($this->objParam);
            $datos = $this->res->getDatos();

            foreach($datos as $agencia) {
                $data = array("numberItems" => "0", "lastItemNumber" => "0", "officeID" => $agencia['officeid'], "dateFrom" => $fecha, "dateTo" => $fecha, "monetary" => $mone_base);
                $data_string = json_encode($data);
                $request = 'http://172.17.58.45/esb/RITISERP.svc/Boa_RITRetrieveSales';
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

                $respuesta = json_decode($result);
                //var_dump($respuesta); exit;
                $xmlRespuesta = new SimpleXMLElement(str_replace("utf-16", "utf-8", $respuesta->Boa_RITRetrieveSalesResult));
                //var_dump($xmlRespuesta); exit;
                if (isset($xmlRespuesta->queryReportDataDetails)) {
                    $moneda = $xmlRespuesta->queryReportDataDetails->currencyInfo->currencyDetails->currencyIsoCode->__toString();
                    //var_dump($moneda); exit;
                    foreach ($xmlRespuesta->queryReportDataDetails->queryReportDataOfficeGroup->documentData as $boleto) {

                        $this->objParam->addParametro('id_punto_venta', $this->objParam->getParametro('id_punto_venta'));
                        $this->objParam->addParametro('nro_boleto', $boleto->documentNumber->documentDetails->number->__toString());
                        $this->objParam->addParametro('fecha_emision', $fecha);
                        $this->objParam->addParametro('codigo_iata', $agencia['codigo_iata']);
                        $this->objParam->addParametro('officeid', $agencia['officeid']);

                        if ($boleto->transactionDataDetails->transactionDetails->code->__toString() == 'CANX') {
                            $this->objParam->addParametro('voided', 'si');
                        }
                        if ($boleto->transactionDataDetails->transactionDetails->code->__toString() == 'CANN') {
                            $this->objParam->addParametro('voided', 'si');
                        }
                        if ($boleto->transactionDataDetails->transactionDetails->code->__toString() == 'TKTT') {
                            $this->objParam->addParametro('voided', 'no');
                        }
                        $this->objParam->addParametro('pasajero', $boleto->passengerName->paxDetails->surname->__toString());
                        foreach ($boleto->monetaryInformation->otherMonetaryDetails as $montoBoleto) {
                            $total = 0;
                            if ($montoBoleto->typeQualifier->__toString() == 'T') {

                                //if ($boleto->documentNumber->documentDetails->number->__toString() == '9302400026571'){
                                if ($montoBoleto->amount->__toString() != ' ') {
                                    $total = $montoBoleto->amount->__toString();
                                }
                                //var_dump($total); exit;
                                //}
                                $this->objParam->addParametro('total', $total);
                                $this->objParam->addParametro('liquido', $total);
                                $this->objParam->addParametro('neto', $total);
                                /*if ($boleto->documentNumber->documentDetails->number->__toString() == '9302400026571'){
                                    var_dump($montoBoleto->amount->__toString()=='');
                                    exit;
                                }*/
                            } else {
                                if ($montoBoleto->typeQualifier->__toString() == 'TTX') {
                                    if ($montoBoleto->amount->__toString() != ' ') {
                                        $total = $montoBoleto->amount->__toString();
                                    }
                                    $this->objParam->addParametro('tasas', $total);
                                } else {
                                    if ($montoBoleto->typeQualifier->__toString() == 'F') {
                                        if ($montoBoleto->amount->__toString() != ' ') {
                                            $total = $montoBoleto->amount->__toString();
                                        }
                                        $this->objParam->addParametro('comision', $total);
                                    } else {
                                        if ($montoBoleto->typeQualifier->__toString() == 'OB') {
                                            if ($montoBoleto->amount->__toString() != ' ') {
                                                $total = $montoBoleto->amount->__toString();
                                            }
                                            $this->objParam->addParametro('carrier_fees', $total);
                                        }
                                    }
                                }
                            }
                        }
                        $this->objParam->addParametro('moneda', $moneda);
                        $this->objParam->addParametro('forma_pago_amadeus', $boleto->fopDetails->fopDescription->formOfPayment->type->__toString());
                        /* inicio forma de pago */
                        if ($boleto->fopDetails->fopDescription->formOfPayment->type->__toString() == 'CA') {
                            //forma de pago cash
                            $this->objParam->addParametro('fp', 'CA');
                            $this->objParam->addParametro('valor_fp', $boleto->fopDetails->monetaryInfo->monetaryDetails->amount->__toString());
                        }

                        if ($boleto->fopDetails->fopDescription->formOfPayment->type->__toString() == 'MX') {
                            //exception valor forma de pago no definida
                            //throw new Exception(__METHOD__.'FORMA DE PAGO MX NO DEFINIDO');
                            $this->objParam->addParametro('fp', 'CA');
                            $this->objParam->addParametro('valor_fp', 0);
                        }

                        if ($boleto->fopDetails->monetaryInfo->monetaryDetails->amount->__toString() == '' || $boleto->fopDetails->monetaryInfo->monetaryDetails->amount->__toString() == ' ') {
                            //exception valor forma de pago no definida
                            //throw new Exception(__METHOD__ . 'VALOR FORMA DE PAGO NO DEFINIDO');
                            $this->objParam->addParametro('fp', 'CA');
                            $this->objParam->addParametro('valor_fp', 0);
                        } else {

                            if ($boleto->fopDetails->fopDescription->formOfPayment->type->__toString() == 'CC') {
                                if ($boleto->fopDetails->fopDescription->formOfPayment->vendorCode->__toString() == 'VI') {
                                    $this->objParam->addParametro('fp', 'CCVI');
                                } else {
                                    if ($boleto->fopDetails->fopDescription->formOfPayment->vendorCode->__toString() == 'CA') {
                                        $this->objParam->addParametro('fp', 'CCCA');
                                    } else {
                                        if ($boleto->fopDetails->fopDescription->formOfPayment->vendorCode->__toString() == 'AX') {
                                            $this->objParam->addParametro('fp', 'CCAX');
                                        } else {
                                            $this->objParam->addParametro('fp', '');
                                        }
                                    }
                                }
                                $this->objParam->addParametro('valor_fp', $boleto->fopDetails->monetaryInfo->monetaryDetails->amount->__toString());
                            }
                        }

                        /* fin forma de pago */

                        if ($boleto->transactionDataDetails->transactionDetails->code->__toString() != 'CANN') {
                            $this->objParam->addParametro('localizador', $boleto->reservationInformation->reservation->controlNumber->__toString());
                        } else {
                            $this->objParam->addParametro('localizador', ' ');
                        }

                        $this->objFunc = $this->create('MODBoleto');
                        $this->res = $this->objFunc->insertarBoletoAgenciaReporteServicioAmadeus($this->objParam);

                        if ($this->res->getTipo() == 'ERROR') {
                            $this->res->imprimirRespuesta($this->res->generarJson());
                            exit;
                        }
                    }
                }

                ////boletos en dolares
                $data = array("numberItems"=>"0","lastItemNumber"=>"0","officeID"=>$agencia['officeid'], "dateFrom"=>$fecha,"dateTo"=>$fecha,"monetary"=>"USD");
                $data_string = json_encode($data);
                $request =  'http://172.17.58.45/esb/RITISERP.svc/Boa_RITRetrieveSales';
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

                $respuesta = json_decode($result);

                $xmlRespuesta = new SimpleXMLElement(str_replace("utf-16", "utf-8",$respuesta->Boa_RITRetrieveSalesResult));
                //var_dump($xmlRespuesta); exit;
                if(isset($xmlRespuesta->queryReportDataDetails)) {
                    $moneda = $xmlRespuesta->queryReportDataDetails->currencyInfo->currencyDetails->currencyIsoCode->__toString();
                    //var_dump($moneda); exit;
                    foreach ($xmlRespuesta->queryReportDataDetails->queryReportDataOfficeGroup->documentData as $boleto) {

                        $this->objParam->addParametro('id_punto_venta', $this->objParam->getParametro('id_punto_venta'));
                        $this->objParam->addParametro('nro_boleto', $boleto->documentNumber->documentDetails->number->__toString());
                        $this->objParam->addParametro('fecha_emision', $fecha);
                        $this->objParam->addParametro('codigo_iata', $agencia['codigo_iata']);
                        $this->objParam->addParametro('officeid', $agencia['officeid']);

                        if ($boleto->transactionDataDetails->transactionDetails->code->__toString() == 'CANX') {
                            $this->objParam->addParametro('voided', 'si');
                        }
                        if ($boleto->transactionDataDetails->transactionDetails->code->__toString() == 'TKTT') {
                            $this->objParam->addParametro('voided', 'no');
                        }
                        $this->objParam->addParametro('pasajero', $boleto->passengerName->paxDetails->surname->__toString());
                        foreach ($boleto->monetaryInformation->otherMonetaryDetails as $montoBoleto) {
                            if ($montoBoleto->typeQualifier->__toString() == 'T') {
                                $this->objParam->addParametro('total', $montoBoleto->amount->__toString());
                                $this->objParam->addParametro('liquido', $montoBoleto->amount->__toString());
                                $this->objParam->addParametro('neto', $montoBoleto->amount->__toString());
                            } else {
                                if ($montoBoleto->typeQualifier->__toString() == 'TTX') {
                                    $this->objParam->addParametro('tasas', $montoBoleto->amount->__toString());
                                } else {
                                    if ($montoBoleto->typeQualifier->__toString() == 'F') {
                                        $this->objParam->addParametro('comision', $montoBoleto->amount->__toString());
                                    } else {
                                        if ($montoBoleto->typeQualifier->__toString() == 'OB') {
                                            $this->objParam->addParametro('carrier_fees', $montoBoleto->amount->__toString());
                                        }
                                    }
                                }
                            }
                        }
                        $this->objParam->addParametro('moneda', $moneda);
                        $this->objParam->addParametro('forma_pago_amadeus', $boleto->fopDetails->fopDescription->formOfPayment->type->__toString());
                        /* inicio forma de pago */
                        if ($boleto->fopDetails->fopDescription->formOfPayment->type->__toString() == 'CA') {
                            //forma de pago cash
                            $this->objParam->addParametro('fp', 'CA');
                            $this->objParam->addParametro('valor_fp', $boleto->fopDetails->monetaryInfo->monetaryDetails->amount->__toString());
                        }

                        if ($boleto->fopDetails->fopDescription->formOfPayment->type->__toString() == 'MX') {
                            //exception valor forma de pago no definida
                            //throw new Exception(__METHOD__.'FORMA DE PAGO MX NO DEFINIDO');
                            $this->objParam->addParametro('fp', 'CA');
                            $this->objParam->addParametro('valor_fp', 0);
                        }

                        if ($boleto->fopDetails->monetaryInfo->monetaryDetails->amount->__toString() == '' || $boleto->fopDetails->monetaryInfo->monetaryDetails->amount->__toString() == ' ') {
                            //exception valor forma de pago no definida
                            $this->objParam->addParametro('fp', 'CA');
                            $this->objParam->addParametro('valor_fp', 0);
                        }

                        if ($boleto->fopDetails->fopDescription->formOfPayment->type->__toString() == 'CC') {
                            if ($boleto->fopDetails->fopDescription->formOfPayment->vendorCode->__toString() == 'VI') {
                                $this->objParam->addParametro('fp', 'CCVI');
                            } else {
                                if ($boleto->fopDetails->fopDescription->formOfPayment->vendorCode->__toString() == 'CA') {
                                    $this->objParam->addParametro('fp', 'CCCA');
                                } else {
                                    if ($boleto->fopDetails->fopDescription->formOfPayment->vendorCode->__toString() == 'AX') {
                                        $this->objParam->addParametro('fp', 'CCAX');
                                    } else {
                                        $this->objParam->addParametro('fp', '');
                                    }
                                }
                            }
                            $this->objParam->addParametro('valor_fp', $boleto->fopDetails->monetaryInfo->monetaryDetails->amount->__toString());
                        }

                        /* fin forma de pago */

                        $this->objParam->addParametro('localizador', $boleto->reservationInformation->reservation->controlNumber->__toString());
                        $this->objFunc = $this->create('MODBoleto');
                        $this->res = $this->objFunc->insertarBoletoAgenciaReporteServicioAmadeus($this->objParam);

                        if ($this->res->getTipo() == 'ERROR') {
                            $this->res->imprimirRespuesta($this->res->generarJson());
                            exit;
                        }
                    }
                }
            }

            $this->objFunc = $this->create('MODBoleto');
            $this->res = $this->objFunc->listarBoletoAmadeus($this->objParam);
            $this->res->imprimirRespuesta($this->res->generarJson());
        }

    }

    function obtenerBoletoFromServicio(){

        if (!isset($_SESSION['_CREDENCIALES_RESIBER']) || $_SESSION['_CREDENCIALES_RESIBER'] == ''){
            throw new Exception('No se definieron las credenciales para conectarse al servicio de Resiber.');
        }
        $data = array(	"credenciales"=>$_SESSION['_CREDENCIALES_RESIBER'],
            "idioma"=>"ES",
            "tkt"=>$this->objParam->getParametro('nro_boleto'),
            "pnr"=>$this->objParam->getParametro('pnr'),
            "ip"=>"127.0.0.1",
            "xmlJson"=>false);

        $json_data = json_encode($data);

        $s = curl_init();
        curl_setopt($s, CURLOPT_URL, 'http://skbproduccion.cloudapp.net/ServicioINT/ServicioInterno.svc/TraerTkt');
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
            throw new Exception("No se pudo conectar con Resiber");
        }
        curl_close($s);
        $res = json_decode($_out);
        $cadena = str_replace('"terminal_salida":{,},', '', $res->TraerTktResult);

        if(strpos($cadena, 'Error') !== false) {
            throw new Exception('No se encontro el numero de billete indicado.');

        } else {
            $res = json_decode($cadena, true);


            $vuelos2 = $this->obtenerVuelos($res['billete']['pnrs']['pnr'], $res['billete']['nom_apdos']['#text']);

            $cantidad_vuelos = substr_count($vuelos2,'$$$') + 1;

            $this->objParam->addParametro('vuelos2',$vuelos2);



            $this->objParam->addParametro('pasajero',$res['billete']['nom_apdos']['#text']);
            $this->objParam->addParametro('fecha_emision',$res['billete']['fecha_emision']);

            $this->objParam->addParametro('total',$res['billete']['total']);
            $this->objParam->addParametro('moneda',$res['billete']['moneda']);
            $this->objParam->addParametro('neto',$res['billete']['tarifa']);

            if (is_array($res['billete']['pnrs']['pnr'])) {
                $pnr = explode(' ',$res['billete']['pnrs']['pnr'][0]);
                $pnr = $res['billete']['pnrs']['pnr'][0];
                $this->objParam->addParametro('localizador', $pnr);
            } else {
                $this->objParam->addParametro('localizador',$res['billete']['pnrs']['pnr']);
            }


            if (isset($res['billete']['identificacion'])) {
                $this->objParam->addParametro('identificacion',$res['billete']['identificacion']['#text']);
            } else {

                $this->objParam->addParametro('identificacion',$res['billete']['foids']['string']);
            }




            if (isset($res['billete']['endosoFields'])) {
                $this->objParam->addParametro('endoso', $res['billete']['endosoFields'][7]['Value'] . " " . $res['billete']['endosoFields'][8]['Value'] . " " . ($res['billete']['endosoFields'][1]['Value']!=''?$res['billete']['endosoFields'][1]['Value']:$res['billete']['endosoFields'][0]['Value']));
            } else {
                $this->objParam->addParametro('endoso','');
            }
            $ruta_completa = '';
            $vuelos = '';

            if (isset($res['billete']['vuelos']['vueloDB'][0])) {
                $this->objParam->addParametro('origen',$res['billete']['vuelos']['vueloDB'][0]['origen']);
                $cupones = count($res['billete']['vuelos']['vueloDB']);
                $this->objParam->addParametro('destino',$res['billete']['vuelos']['vueloDB'][$cupones-1]['destino']);
                //ingresar vuelo
                $vuelos = $res['billete']['vuelos']['vueloDB'][0]['fecha_salida'];
                $vuelos = $vuelos . "|" . $res['billete']['vuelos']['vueloDB'][0]['num_vuelo'];
                $vuelos = $vuelos . "|" . $res['billete']['vuelos']['vueloDB'][0]['hora_salida'];
                $vuelos = $vuelos . "|" . $res['billete']['vuelos']['vueloDB'][0]['origen'];
                $vuelos = $vuelos . "|" . $res['billete']['vuelos']['vueloDB'][0]['destino'];
                $vuelos = $vuelos . "|" . $res['billete']['vuelos']['vueloDB'][0]['fare_basis'];
                $vuelos = $vuelos . "|" . $res['billete']['vuelos']['vueloDB'][0]['kgs'];
                $vuelos = $vuelos . "|" . $res['billete']['vuelos']['vueloDB'][0]['status'];
                $vuelos = $vuelos . "|" . $res['billete']['vuelos']['vueloDB'][0]['clase'];
                $vuelos = $vuelos . "|" . $res['billete']['vuelos']['vueloDB'][0]['flight_status'];
                $vuelos = $vuelos . "|" . $res['billete']['vuelos']['vueloDB'][0]['linea'];

                $ruta_completa = $res['billete']['vuelos']['vueloDB'][0]['origen'] . '-' . $res['billete']['vuelos']['vueloDB'][0]['destino'];
                for ($i = 1;$i < $cupones;$i++) {
                    $ruta_completa .= "-" . $res['billete']['vuelos']['vueloDB'][$i]['destino'];
                    $vuelos = $vuelos . "$$$" . $res['billete']['vuelos']['vueloDB'][$i]['fecha_salida'];
                    $vuelos = $vuelos . "|" . $res['billete']['vuelos']['vueloDB'][$i]['num_vuelo'];
                    $vuelos = $vuelos . "|" . $res['billete']['vuelos']['vueloDB'][$i]['hora_salida'];
                    $vuelos = $vuelos . "|" . $res['billete']['vuelos']['vueloDB'][$i]['origen'];
                    $vuelos = $vuelos . "|" . $res['billete']['vuelos']['vueloDB'][$i]['destino'];
                    $vuelos = $vuelos . "|" . $res['billete']['vuelos']['vueloDB'][$i]['fare_basis'];
                    $vuelos = $vuelos . "|" . $res['billete']['vuelos']['vueloDB'][$i]['kgs'];
                    $vuelos = $vuelos . "|" . $res['billete']['vuelos']['vueloDB'][$i]['status'];
                    $vuelos = $vuelos . "|" . $res['billete']['vuelos']['vueloDB'][$i]['clase'];
                    $vuelos = $vuelos . "|" . $res['billete']['vuelos']['vueloDB'][$i]['flight_status'];
                    $vuelos = $vuelos . "|" . $res['billete']['vuelos']['vueloDB'][$i]['linea'];
                }
            } else {
                $ruta_completa = $res['billete']['vuelos']['vueloDB']['origen'] . '-' . $res['billete']['vuelos']['vueloDB']['destino'];
                $this->objParam->addParametro('origen',$res['billete']['vuelos']['vueloDB']['origen']);
                $cupones = 1;
                $vuelos = $res['billete']['vuelos']['vueloDB']['fecha_salida'];
                $vuelos = $vuelos . "|" . $res['billete']['vuelos']['vueloDB']['num_vuelo'];
                $vuelos = $vuelos . "|" . $res['billete']['vuelos']['vueloDB']['hora_salida'];
                $vuelos = $vuelos . "|" . $res['billete']['vuelos']['vueloDB']['origen'];
                $vuelos = $vuelos . "|" . $res['billete']['vuelos']['vueloDB']['destino'];
                $vuelos = $vuelos . "|" . $res['billete']['vuelos']['vueloDB']['fare_basis'];
                $vuelos = $vuelos . "|" . $res['billete']['vuelos']['vueloDB']['kgs'];
                $vuelos = $vuelos . "|" . $res['billete']['vuelos']['vueloDB']['status'];
                $vuelos = $vuelos . "|" . $res['billete']['vuelos']['vueloDB']['clase'];
                $vuelos = $vuelos . "|" . $res['billete']['vuelos']['vueloDB']['flight_status'];
                $vuelos = $vuelos . "|" . $res['billete']['vuelos']['vueloDB']['linea'];
                $this->objParam->addParametro('destino',$res['billete']['vuelos']['vueloDB']['destino']);

            }
            $this->objParam->addParametro('vuelos',$vuelos);
            $this->objParam->addParametro('ruta_completa',$ruta_completa);
            $this->objParam->addParametro('cupones',$cupones);
            $posicion = strpos($res['billete']['fare_calc'], '*XT');
            if ($posicion) {
                $impuesto = substr ( $res['billete']['fare_calc'] , $posicion + 3 );
            } else {
                $impuesto = "";
            }
            $this->objParam->addParametro('impuestos',$impuesto);
            $this->objParam->addParametro('fare_calc',$res['billete']['fare_calc']);

            $tasa = "";
            if (isset($res['billete']['tasas']['tasaDB'])) {
                foreach ($res['billete']['tasas']['tasaDB'] as $dato) {
                    if (!strpos($dato['valor'],'EXEMPT')) {
                        $temporal = substr($dato['valor'], 3);
                        $temporal = trim($temporal);
                        $tasa .= $temporal;
                    }
                }
            }

            $this->objParam->addParametro('tasas',$tasa);
            $fps = explode('+', $res['billete']['forma_pago']);
            $fp = '';
            $moneda_fp = '';
            $valor_fp = '';
            $tarjeta_fp = '';
            $autorizacion_fp = '';
            foreach ($fps as $dato) {

                $temp_array = explode('/', $dato);
                if (strpos($temp_array[0], 'CASH')!== FALSE) { //Cash y MCO
                    if (strpos($temp_array[0], 'CASH,MCO')!== FALSE) {
                        $fp .= '#MCO';
                        $moneda_fp .= '#' . substr($temp_array[1], 0,3);
                        $temp_array[1] = substr($temp_array[1], 3);
                        $temp_array[1] = trim($temp_array[1]);
                        $valor_fp .= '#' . $temp_array[1];
                        $tarjeta_fp .= '#';
                        $autorizacion_fp .= '#';
                    } else if(strpos($temp_array[0], 'TKT,CASH')!== FALSE)	{
                        $fp .= '#EX';
                        $moneda_fp .= '#' . $res['billete']['moneda'];
                        $valor_fp .= '#0';
                        $tarjeta_fp .= '#';
                        $autorizacion_fp .= '#';
                    }else {
                        $fp .= '#CA';
                        $moneda_fp .= '#' . substr($temp_array[1], 0,3);
                        $temp_array[1] = substr($temp_array[1], 3);
                        $temp_array[1] = trim($temp_array[1]);
                        $valor_fp .= '#' . $temp_array[1];
                        $tarjeta_fp .= '#';
                        $autorizacion_fp .= '#';
                    }


                } else if (strpos($temp_array[0], 'DEPU')!== FALSE) { //El boleto tiene un valor de 0
                    $fp .= '#CA';
                    $moneda_fp .= '#' . $res['billete']['moneda'];
                    $valor_fp .= '#' . '0';
                    $tarjeta_fp .= '#';
                    $autorizacion_fp .= '#';

                }else if (strpos($temp_array[0], 'SF') !== FALSE) { //forma de pago SF
                    if (strpos($temp_array[0], 'SFCA')!== FALSE) {
                        $fp .= '#SFCA';
                        $moneda_fp .= '#' . substr($temp_array[1], 0,3);
                        $tarjeta_fp .= '#';
                        $autorizacion_fp .= '#';
                        $temp_array[1] = substr($temp_array[1], 3);
                        $temp_array[1] = trim($temp_array[1]);
                        $valor_fp .= '#' . $temp_array[1];
                    } else if (strpos($temp_array[0], 'SFCC')!== FALSE){
                        $temp_array[0] = str_replace('SFCC', 'SF', $temp_array[0]);
                        $fp .= '#' . substr($temp_array[0], 0,4);
                        $tarjeta_fp .= '#';
                        $autorizacion_fp .= '#';
                        $moneda_fp .= '#' . substr($temp_array[3], 0,3);
                        $temp_array[3] = substr($temp_array[3], 3);
                        $temp_array[3] = trim($temp_array[3]);
                        $valor_fp .= '#' . $temp_array[3];
                    } else {

                        throw new Exception('El billete tiene una forma de pago de tipo SF no reconocida.');
                    }
                } else if (substr( $temp_array[0], 0, 2 ) == 'CC'){ //tarjeta de credito
                    $fp .= '#' . substr($temp_array[0], 0,4);
                    $moneda_fp .= '#' . substr($temp_array[2], 0,3);
                    $tarjeta_fp .= '#' . substr($temp_array[0], 4);
                    $autorizacion_fp .= '#' .substr($temp_array[1], 0,4);
                    $temp_array[2] = substr($temp_array[2], 3);
                    $temp_array[2] = trim($temp_array[2]);
                    $valor_fp .= '#' . $temp_array[2];

                } else {
                    throw new Exception('El billete tiene una forma de pago no reconocida.');
                }
            }

            $this->objParam->addParametro('fp',$fp);
            $this->objParam->addParametro('moneda_fp',$moneda_fp);
            $this->objParam->addParametro('valor_fp',$valor_fp);
            $this->objParam->addParametro('tarjeta_fp',$tarjeta_fp);
            $this->objParam->addParametro('autorizacion_fp',$autorizacion_fp);
            $rutas = '';
            if (isset($res['billete']['vuelos']['vueloDB'][0])) {
                foreach ($res['billete']['vuelos']['vueloDB'] as $dato) {

                    $rutas .= '#' . $dato['origen'];
                    $rutas .= '#' . $dato['destino'];
                }
            } else {
                $rutas .= '#' . $res['billete']['vuelos']['vueloDB']['origen'];
                $rutas .= '#' . $res['billete']['vuelos']['vueloDB']['destino'];
            }

            $this->objParam->addParametro('rutas',$rutas);

            $this->objFunc=$this->create('MODBoleto');
            $this->res=$this->objFunc->insertarBoletoServicio($this->objParam);

            if ($this->res->getTipo()=='ERROR') {
                $this->res->imprimirRespuesta($this->res->generarJson());
                exit;
            }
        }
        return $cantidad_vuelos;

    }

    function obtenerVuelos ($pnr, $nombres) {
        $respuesta = '';
        if (!isset($_SESSION['_CREDENCIALES_RESIBER']) || $_SESSION['_CREDENCIALES_RESIBER'] == ''){
            throw new Exception('No se definieron las credenciales para conectarse al servicio de Resiber.');
        }
        $arreglo = explode('/',$nombres);

        if (is_array($pnr)) {
            $pnr = explode(' ',$pnr[0]);
            $pnr = $pnr[0];
        }


        $data = array("credenciales"=>"{ae7419a1-dbd2-4ea9-9335-2baa08ba78b4}{59331f3e-a518-4e1e-85ca-8df59d14a420}",
            //"credenciales"=>$_SESSION['_CREDENCIALES_RESIBER'],
            "idioma"=>"ES",
            "pnr"=>$pnr,
            "apellido"=>$arreglo[0],
            "ip"=>"127.0.0.1",
            "xmlJson"=>false);

        $json_data = json_encode($data);

        $s = curl_init();
        curl_setopt($s, CURLOPT_URL, 'http://skbproduccion.cloudapp.net/ServicioINT/ServicioInterno.svc/TraerReserva');
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
            throw new Exception("No se pudo conectar con Resiber");
        }
        curl_close($s);


        //$cadena = str_replace('"terminal_salida":{,},', '', $res->TraerReservaResult);
        $_out = str_replace('\\','',$_out);
        $_out = substr($_out,23);
        $_out = substr($_out,0,-2);

        $res = json_decode($_out);

        if(strpos($_out, 'Error') !== false) {
            throw new Exception('No se encontro el pnr indicado.');

        } else {
            if (is_array($res->reserva->vuelos->vuelo)) {

                foreach ($res->reserva->vuelos->vuelo as $value) {

                    $respuesta .= $value->origen . '|' . $value->destino . '|' . $value->fecha_salida . '|' . $value->hora_salida . '|' . $value->hora_llegada . '$$$';
                }
            } else {
                $respuesta .= $res->reserva->vuelos->vuelo->origen . '|' . $res->reserva->vuelos->vuelo->destino . '|' . $res->reserva->vuelos->vuelo->fecha_salida . '|' . $res->reserva->vuelos->vuelo->hora_salida . '|' . $res->reserva->vuelos->vuelo->hora_llegada . '$$$';
            }
            $respuesta = substr($respuesta, 0, -3);

        }

        return $respuesta;
    }

    function reporteBoleto(){

        if (isset($_SESSION['_OBINGRESOS_TIPO_BOLETO']) && $_SESSION['_OBINGRESOS_TIPO_BOLETO'] == 'PDFBR') {
            $this->reporteBoletoBRPDF();
        } else {

            $this->objFunc = $this->create('MODBoleto');
            $datos = array();
            $this->res = $this->objFunc->listarBoletoReporte($this->objParam);
            $datos = $this->res->getDatos();
            $datos = $datos[0];


            $this->objFunc = $this->create('MODBoleto');
            $this->res = $this->objFunc->listarBoletoDetalleReporte($this->objParam);
            $datos['detalle'] = $this->res->getDatos();

            $reporte = new RBoleto();
            $temp = array();

            $temp['html'] = $reporte->generarHtml($datos);
            $this->res->setDatos($temp);
            $this->res->imprimirRespuesta($this->res->generarJson());
        }


    }

    function anularBoleto(){
        $this->objFunc=$this->create('MODBoleto');
        $this->res=$this->objFunc->anularBoleto($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function traerBoletosJson(){

        if ($this->objParam->getParametro('id_punto_venta') != '') {
            $this->objParam->addFiltro("bol.id_punto_venta = ". $this->objParam->getParametro('id_punto_venta'));
        }

        if ($this->objParam->getParametro('fecha') != '') {
            $fecha = $this->objParam->getParametro('fecha');
            $this->objParam->addFiltro("bol.fecha_emision = ''". date($fecha)."''");
            //$this->objParam->addFiltro("bol.fecha_emision = ''". date("d-m-Y")."''");
        }

        if ($this->objParam->getParametro('reporte') == 'reporte') {
            $this->objFunc = $this->create('MODBoleto');
            //$this->res = $this->objFunc->eliminarBoletosAmadeus($this->objParam);
        }

        if ($this->objParam->getParametro('moneda_base') != '') {
            $mone_base = $this->objParam->getParametro('moneda_base');
        }
        //var_dump("llega:",$this->objParam->getParametro('moneda_base'));
        if ($this->objParam->getParametro('officeId_agencia') != '') {
            $officeid = $this->objParam->getParametro('officeId_agencia');
        }else{
            $this->objParam->addParametro('fecha', $fecha);
            $this->objParam->addParametro('moneda', $mone_base);

            $this->objFunc=$this->create('sis_ventas_facturacion/MODPuntoVenta');
            $this->res=$this->objFunc->obtenerOfficeID($this->objParam);

            $datos = $this->res->getDatos();

            $officeid = $datos[0]['officeid'];
            $id_agencia = $datos[0]['id_agencia'];

            if($this->objParam->getParametro('todos')=='no') {
                $numberItems = 5;
                $identificador_reporte = $datos[0]['identificador_reporte'];
            }else{
                $numberItems = 0;
                $identificador_reporte = 0;
            }
        }

        //var_dump($mone_base);exit;
        //boletos en bolivianos
        $data = array("numberItems"=>$numberItems, "lastItemNumber"=>$identificador_reporte,"officeID"=>$officeid, "dateFrom"=>$fecha,"dateTo"=>$fecha,"monetary"=>$mone_base,"statusVoid"=>"");

        $data_string = json_encode($data);
        $request =  'http://172.17.58.45/esbFIN/RITISERP.svc/Boa_RITRetrieveSales_JS';
        $session = curl_init($request);
        curl_setopt($session, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($session, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($session, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data_string))
        );

        $result = curl_exec($session);//var_dump($result);exit;
        curl_close($session);

        $respuesta = json_decode($result);
        //var_dump($respuesta);exit;
        if(isset($respuesta->Boa_RITRetrieveSales_JSResult)) {

            $this->objParam->addParametro('id_punto_venta', $this->objParam->getParametro('id_punto_venta'));
            $this->objParam->addParametro('boletos', $respuesta->Boa_RITRetrieveSales_JSResult);
            $this->objParam->addParametro('fecha_emision', $fecha);
            $this->objParam->addParametro('id_agencia', $id_agencia);

            if ($this->objParam->getParametro('id_usuario_cajero') != '') {
                $this->objParam->addParametro('id_usuario_cajero', $this->objParam->getParametro('id_usuario_cajero'));
                $this->objFunc = $this->create('MODBoleto');
                //$this->res = $this->objFunc->actualizaBoletoServicioAmadeus($this->objParam);
            } else {
                if ($this->objParam->getParametro('reporte') == 'reporte') {
                    $this->objFunc = $this->create('MODBoleto');
                    //$this->res = $this->objFunc->insertarBoletoReporteServicioAmadeus($this->objParam);
                } else {
                    $this->objFunc = $this->create('MODBoleto');
                    $this->res = $this->objFunc->insertarBoletoServicioAmadeusJSon($this->objParam);
                }
            }
            if ($this->res->getTipo() == 'ERROR') {
                $this->res->imprimirRespuesta($this->res->generarJson());
                exit;
            }
        }


        $this->objParam->addParametro('fecha', $fecha);
        $this->objParam->addParametro('moneda', "USD");
        $this->objFunc=$this->create('sis_ventas_facturacion/MODPuntoVenta');
        $this->res=$this->objFunc->obtenerOfficeID($this->objParam);

        $datos = $this->res->getDatos();
        //var_dump($datos); exit;
        $officeid = $datos[0]['officeid'];
        $id_agencia = $datos[0]['id_agencia'];
        $identificador_reporte = $datos[0]['identificador_reporte'];

        if($this->objParam->getParametro('todos')=='no') {
            $numberItems = 5;
            $identificador_reporte = $datos[0]['identificador_reporte'];
        }else{
            $numberItems = 0;
            $identificador_reporte = 0;
        }
        ////boletos en dolares
        //$data = array("numberItems"=>"0","lastItemNumber"=>"0","officeID"=>"SRZOB0104","dateFrom"=>"20170808","dateTo"=>"20170808","monetary"=>"USD");
        $data = array("numberItems"=>$numberItems, "lastItemNumber"=>$identificador_reporte,"officeID"=>$officeid, "dateFrom"=>$fecha,"dateTo"=>$fecha,"monetary"=>"USD","statusVoid"=>"");
        $data_string = json_encode($data);
        $request =  'http://172.17.58.45/esbFIN/RITISERP.svc/Boa_RITRetrieveSales_JS';
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

        $respuesta = json_decode($result);

        if(isset($respuesta->Boa_RITRetrieveSales_JSResult)) {

            $this->objParam->addParametro('id_punto_venta', $this->objParam->getParametro('id_punto_venta'));
            $this->objParam->addParametro('boletos', $respuesta->Boa_RITRetrieveSales_JSResult);
            $this->objParam->addParametro('fecha_emision', $fecha);
            $this->objParam->addParametro('id_agencia', $id_agencia);

            if ($this->objParam->getParametro('id_usuario_cajero') != '') {
                $this->objParam->addParametro('id_usuario_cajero', $this->objParam->getParametro('id_usuario_cajero'));
                $this->objFunc = $this->create('MODBoleto');
                $this->res = $this->objFunc->actualizaBoletoServicioAmadeus($this->objParam);
            } else {
                if ($this->objParam->getParametro('reporte') == 'reporte') {
                    $this->objFunc = $this->create('MODBoleto');
                    $this->res = $this->objFunc->insertarBoletoReporteServicioAmadeus($this->objParam);
                } else {
                    $this->objFunc = $this->create('MODBoleto');
                    $this->res = $this->objFunc->insertarBoletoServicioAmadeusJSon($this->objParam);
                }
            }
            if ($this->res->getTipo() == 'ERROR') {
                $this->res->imprimirRespuesta($this->res->generarJson());
                exit;
            }
        }
        //var_dump($this->objParam->getParametro('tipoReporte'));exit;
        if ($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid') {

            if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){

                $this->objReporte = new Reporte($this->objParam,$this);
                $this->res = $this->objReporte->generarReporteListado('MODBoleto','listarBoletoAmadeus');
                $this->res->imprimirRespuesta($this->res->generarJson());
            }else {
                $this->objFunc = $this->create('MODBoleto');
                $this->res = $this->objFunc->listarBoletoAmadeus($this->objParam);
                $this->res->imprimirRespuesta($this->res->generarJson());
            }
        }else {
            if ($this->objParam->getParametro('pes_estado') != '') {
                if ($this->objParam->getParametro('pes_estado') == 'revisados') {
                    $this->objParam->addFiltro(" bol.estado = ''revisado'' ");
                    $this->objParam->addFiltro("(bol.id_usuario_cajero = ". $_SESSION["ss_id_usuario"] . " or exists(	select 1
																												from segu.tusuario_rol
																												where id_rol = 1 and estado_reg = ''activo'' and
																												id_usuario = ". $_SESSION["ss_id_usuario"] . " )
																											or exists (
																													 select 1
																													 from vef.tsucursal_usuario
																													 where id_punto_venta=". $this->objParam->getParametro('id_punto_venta') . "
																													 and id_usuario=". $_SESSION["ss_id_usuario"] . "
																													 and tipo_usuario=''administrador''
																											))");
                }else{
                    $this->objParam->addFiltro(" bol.estado = ''borrador'' ");
                }
            }
            $this->objFunc=$this->create('MODBoleto');
            $this->res=$this->objFunc->listarBoletosEmitidosAmadeus($this->objParam);
            $this->res->imprimirRespuesta($this->res->generarJson());
        }

    }

    function traerBoletosJsonAnulados(){

        if ($this->objParam->getParametro('fecha') != '') {
            $fecha = $this->objParam->getParametro('fecha');
        }

        if ($this->objParam->getParametro('moneda_base') != '') {
            $mone_base = $this->objParam->getParametro('moneda_base');
        }

        $this->objParam->addParametro('fecha', $fecha);
        $this->objParam->addParametro('moneda', $mone_base);
        $this->objFunc=$this->create('sis_ventas_facturacion/MODPuntoVenta');
        $this->res=$this->objFunc->obtenerOfficeID($this->objParam);

        $datos = $this->res->getDatos();

        $officeid = $datos[0]['officeid'];

        $numberItems = 0;
        $identificador_reporte = 0;

        //boletos en bolivianos
        $data = array("numberItems"=>$numberItems, "lastItemNumber"=>$identificador_reporte,"officeID"=>$officeid, "dateFrom"=>$fecha,"dateTo"=>$fecha,"monetary"=>$mone_base,"statusVoid"=>"V");
        $data_string = json_encode($data);
        $request =  'http://172.17.58.45/esbFIN/RITISERP.svc/Boa_RITRetrieveSales_JS';
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

        $respuesta = json_decode($result);

        $boletos_anulados = array();

        if(isset($respuesta->Boa_RITRetrieveSales_JSResult)) {
            if(json_decode($respuesta->Boa_RITRetrieveSales_JSResult)->queryReportDataDetails != NULL) {
                $boletos = json_decode($respuesta->Boa_RITRetrieveSales_JSResult)->queryReportDataDetails->queryReportDataOfficeGroup[0]->documentData;

                foreach ($boletos as $boleto) {
                    array_push($boletos_anulados, $boleto->documentNumber->documentDetails->number);
                }
            }
        }

        $this->objParam->addParametro('fecha', $fecha);
        $this->objParam->addParametro('moneda', "USD");
        $this->objFunc=$this->create('sis_ventas_facturacion/MODPuntoVenta');
        $this->res=$this->objFunc->obtenerOfficeID($this->objParam);

        $datos = $this->res->getDatos();

        $officeid = $datos[0]['officeid'];
        $numberItems = 0;
        $identificador_reporte = 0;

        ////boletos en dolares
        $data = array("numberItems"=>$numberItems, "lastItemNumber"=>$identificador_reporte,"officeID"=>$officeid, "dateFrom"=>$fecha,"dateTo"=>$fecha,"monetary"=>"USD","statusVoid"=>"V");
        $data_string = json_encode($data);
        $request =  'http://172.17.58.45/esbFIN/RITISERP.svc/Boa_RITRetrieveSales_JS';
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

        $respuesta = json_decode($result);

        if(isset($respuesta->Boa_RITRetrieveSales_JSResult)) {

            if(json_decode($respuesta->Boa_RITRetrieveSales_JSResult)->queryReportDataDetails != NULL) {
                $boletos = json_decode($respuesta->Boa_RITRetrieveSales_JSResult)->queryReportDataDetails->queryReportDataOfficeGroup[0]->documentData;

                foreach ($boletos as $boleto) {
                    array_push($boletos_anulados, $boleto->documentNumber->documentDetails->number);
                }
            }
        }

        asort($boletos_anulados);
        $this->objParam->addParametro('id_punto_venta', $this->objParam->getParametro('id_punto_venta'));
        $this->objParam->addParametro('boletos', implode($boletos_anulados,','));
        $this->objParam->addParametro('fecha_emision', $fecha);

        if ($this->objParam->getParametro('id_usuario_cajero') != '') {
            $this->objParam->addParametro('id_usuario_cajero', $this->objParam->getParametro('id_usuario_cajero'));
            $this->objFunc = $this->create('MODBoleto');
            $this->res = $this->objFunc->compararBoletosServicioAmadeusERP($this->objParam);
        }

        if ($this->res->getTipo() == 'ERROR') {
            $this->res->imprimirRespuesta($this->res->generarJson());
            exit;
        }

        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function reporteBoletoBRPDF() {


        $this->objFunc = $this->create('MODBoleto');
        $this->res = $this->objFunc->listarBoletoReporte($this->objParam);
        $this->objParam->addParametro('datos_maestro',$this->res->getDatos());



        $this->objFunc = $this->create('MODBoleto');
        $this->res = $this->objFunc->listarBoletoDetalleReporte($this->objParam);
        $this->objParam->addParametro('datos_detalle',$this->res->getDatos());

        $nombreArchivo=uniqid(md5(session_id()).'Boleto');

        $this->objParam->addParametro('titulo_archivo','Boleto');
        $nombreArchivo.='.pdf';
        $this->objParam->addParametro('nombre_archivo',$nombreArchivo);
        $this->objParam->addParametro('orientacion','P');
        $this->objParam->addParametro('tamano','A4');

        //Instancia la clase de pdf
        $this->objReporteFormato=new RBoletoBRPDF($this->objParam);
        $this->objReporteFormato->generarReporte();
        $this->objReporteFormato->output($this->objReporteFormato->url_archivo,'F');

        $this->mensajeExito=new Mensaje();
        $this->mensajeExito->setMensaje('EXITO','Reporte.php','Reporte generado',
            'Se generó con éxito el reporte: '.$nombreArchivo,'control');
        $this->mensajeExito->setArchivoGenerado($nombreArchivo);
        $this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());


    }

    function detalleDiarioBoletosWeb(){
        $this->objFunc=$this->create('MODBoleto');

        $this->res=$this->objFunc->ultimaFechaMigracion($this->objParam);
        if ($this->res->getTipo()=='ERROR') {
            $this->res->imprimirRespuesta($this->res->generarJson());
            exit;
        } else {
            $datos = $this->res->getDatos();
            $array_fechas = explode(',',$datos['fecha']);
        }

        if (!isset($_SESSION['_CREDENCIALES_RESIBER']) || $_SESSION['_CREDENCIALES_RESIBER'] == ''){
            throw new Exception('No se definieron las credenciales para conectarse al servicio de Resiber.');
        }
        foreach($array_fechas as $fecha) {
            $data = array("credenciales" => $_SESSION['_CREDENCIALES_RESIBER'],
                "idioma" => "ES",
                "fecha" => $fecha,
                "ip" => "127.0.0.1",
                "xmlJson" => false);

            $json_data = json_encode($data);

            $s = curl_init();
            curl_setopt($s, CURLOPT_URL, 'https://ef.boa.bo/Servicios/ServicioInterno.svc/DetalleDiario');
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
                throw new Exception("No se pudo conectar con Resiber");
            }
            curl_close($s);
            if (strpos($_out,'spAppConciliacionDiariaBoA_Result')) {
                $_out = substr($_out, 109);
                $_out = substr($_out, 0, -4);
                $_out = str_replace('\\', '', $_out);

                $this->objParam->addParametro('fecha', $fecha);
                $this->objParam->addParametro('detalle_boletos', $_out);

                $this->objFunc = $this->create('MODBoleto');

                $this->res = $this->objFunc->detalleDiarioBoletosWeb($this->objParam);

                if ($this->res->getTipo()=='ERROR') {
                    $this->res->imprimirRespuesta($this->res->generarJson());
                }
            }
        }//fin for

        $this->res->imprimirRespuesta($this->res->generarJson());
        exit;

    }

    function procesarDetalleBoletos(){

        $this->objFunc=$this->create('MODBoleto');
        $this->res=$this->objFunc->procesarDetalleBoletos($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
        exit;

    }

    function reporteBoletoResiberVentasWeb(){
        $this->objParam->addParametro('tipo', 'sin_boletos_web');
        $this->objFunc = $this->create('MODBoleto');
        $this->res = $this->objFunc->listarReporteResiberVentasWeb($this->objParam);
        $this->objParam->addParametro('resiber', $this->res->datos);

        $this->objParam->addParametro('tipo', 'sin_boletos_resiber');
        $this->objFunc = $this->create('MODBoleto');
        $this->res = $this->objFunc->listarReporteResiberVentasWeb($this->objParam);
        $this->objParam->addParametro('ventas_web', $this->res->datos);

        $this->objParam->addParametro('tipo', 'montos_diferentes');
        $this->objFunc = $this->create('MODBoleto');
        $this->res = $this->objFunc->listarReporteResiberVentasWeb($this->objParam);
        $this->objParam->addParametro('montos_diferentes', $this->res->datos);

        //obtener titulo de reporte
        $titulo = 'Reporte Depositos';
        //Genera el nombre del archivo (aleatorio + titulo)
        $nombreArchivo = uniqid(md5(session_id()) . $titulo);

        $nombreArchivo .= '.xls';
        $this->objParam->addParametro('nombre_archivo', $nombreArchivo);
        //$this->objParam->addParametro('datos', $this->res->datos);
        //Instancia la clase de excel
        $this->objReporteFormato = new RReporteBoletoResiberVentasWeb($this->objParam);
        $this->objReporteFormato->generarBoletosSinVentasWeb();
        $this->objReporteFormato->generarVentasWebSinBoletos();
        $this->objReporteFormato->generarDiferenciaMonto();

        $this->objReporteFormato->generarReporte();

        $this->mensajeExito = new Mensaje();
        $this->mensajeExito->setMensaje('EXITO', 'Reporte.php', 'Reporte generado','Se generó con éxito el reporte: ' . $nombreArchivo, 'control');
        $this->mensajeExito->setArchivoGenerado($nombreArchivo);
        $this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());

    }

    function insertarBoletosRET ()
    {

        $this->objFunc = $this->create('MODBoleto');
        //1.insertar boletos enviados de la RET
        $this->res = $this->objFunc->insertarBoletosRET($this->objParam);
        //1.1 si hay error devolver el error
        //1.2 si no hay error llamar a servicio de generacion de tickets emitidos Portal, insertar en la tabla y generar observaciones localmente
        // y devolver la observaciones en un arreglo

        //1.2.1 si hay error devolver el error

        //1.2.2 si no hay error llamar al servicio de registro de observaciones Portal enviando las observaciones
        //del punto 1.2

        //1.2.2.1 si hay error devolver el error

        //1.2.2.2 si no hay error se inserta el periodo de venta banca
        //inserta totales de preiodo venta
        //1.2.2.3 si no hay error se inserta el periodo de venta cuenta corriente
        //inserta periodo de venta cuenta corriente
        $this->res->imprimirRespuesta($this->res->generarJson());

    }
    function viajeroFrecuente()
    {

      /*Aumentando control para que si se tiene el boleto y el pnr asociado no llamar al servicio*/

      if ($this->objParam->getParametro('dato_llenado') == 'vacio') {

        $data = array("FFID" => $this->objParam->getParametro('ffid'),
            "PNR" => $this->objParam->getParametro('pnr'),
            "TicketNumber" => '930'.$this->objParam->getParametro('ticketNumber'),
            "VoucherCode" => 'OB.FF.VO'.$this->objParam->getParametro('voucherCode'));
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
        //  var_dump($respuesta["Status"]);exit;
        if ($respuesta["HasErrors"]== true) {

            throw new Exception('Error en el servicio Voucher.'.$respuesta["Message"]);

        } else {

            if ($respuesta["Status"] == 'NOK') {
                throw new Exception($respuesta["Message"]);
            }
            $this->objParam->addParametro('id_pasajero_frecuente', $respuesta["FFId"]);
            $this->objParam->addParametro('nombre_completo', $respuesta["FullName"]);
            $this->objParam->addParametro('mensaje', 'yes');
            $this->objParam->addParametro('status', $respuesta["Status"]);
            $this->objFunc = $this->create('MODBoleto');

            if ($this->objParam->insertar('id_viajero_frecuente')) {
                $this->res = $this->objFunc->viajeroFrecuente($this->objParam);
            }
            $this->res->imprimirRespuesta($this->res->generarJson());
        }

      } /*Fin if si los datos no son vacios mandamos a la consulta e insertamos localmente*/
      else {

        $ffid = $this->objParam->getParametro('ffid');
        $this->objParam->addParametro('id_pasajero_frecuente', $ffid);
        $this->objParam->addParametro('nombre_completo', '');
        $this->objParam->addParametro('mensaje', 'yes');
        $this->objParam->addParametro('status', 'OK');
        $this->objFunc = $this->create('MODBoleto');

        if ($this->objParam->insertar('id_viajero_frecuente')) {
            $this->res = $this->objFunc->viajeroFrecuente($this->objParam);
        }
        $this->res->imprimirRespuesta($this->res->generarJson());

      }
      /*************************************************************************************/
    }

    function logViajeroFrecuente (){
        $this->objFunc=$this->create('MODBoleto');
        $this->res=$this->objFunc->logViajeroFrecuente($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }


    function buscarBoletoAmadeus(){

        $this->objParam->defecto('ordenacion','nro_boleto');
        $this->objParam->defecto('dir_ordenacion','desc');

        $this->objParam->addFiltro("/*bol.fecha_emision = ''". $this->objParam->getParametro('fecha_actual') ."''::date and*/ (bol.nro_boleto like ''%". $this->objParam->getParametro('nro_boleto') . "%''::varchar or bol.localizador = ''".$this->objParam->getParametro('nro_boleto')."''::varchar) and bol.estado in (''borrador'', ''caja'', ''revisado'', ''finalizado'')");

        $this->objFunc=$this->create('MODBoleto');
        $this->res=$this->objFunc->listarBoletosEmitidosAmadeus($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function traerReservaBoletoExch(){
        $respuesta = '';
        if (!isset($_SESSION['_CREDENCIALES_RESIBER']) || $_SESSION['_CREDENCIALES_RESIBER'] == ''){
            throw new Exception('No se definieron las credenciales para conectarse al servicio de Resiber.');
        }

        $pnr = $this->objParam->getParametro('pnr');
        //"credenciales"=>"{ae7419a1-dbd2-4ea9-9335-2baa08ba78b4}{59331f3e-a518-4e1e-85ca-8df59d14a420}"
        $data = array("credenciales"=>"{ae7419a1-dbd2-4ea9-9335-2baa08ba78b4}{59331f3e-a518-4e1e-85ca-8df59d14a420}",
            "idioma"=>"ES",
            "pnr"=>$pnr,//VDBWIF, VHGDZP, LXUQMP --- LKJK27  MSB9Z8-----N5W923, N5ZRKF, N634RP, N6554Y, N654X2------OUU6PY
            "apellido"=>"PRUEBAS",
            "ip"=>"127.0.0.1",
            "xmlJson"=>false);

        $json_data = json_encode($data);
        $s = curl_init();
        curl_setopt($s, CURLOPT_URL, 'https://ef.boa.bo/ServicioINT/ServicioInterno.svc/TraerReservaExch');//skbproduccion, skbpruebas
        curl_setopt($s, CURLOPT_POST, true);
        curl_setopt($s, CURLOPT_POSTFIELDS, $json_data);
        curl_setopt($s, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($s, CURLOPT_CONNECTTIMEOUT, 20);
        curl_setopt($s, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($json_data))
        );
        $_out = curl_exec($s);//var_dump(json_decode($_out)->TraerReservaExchResult);exit;
        $status = curl_getinfo($s, CURLINFO_HTTP_CODE);
        if (!$status) {
            throw new Exception("No se pudo conectar con Resiber");
        }
        curl_close($s);

        $out_origin = $_out;

        $_out = str_replace('\\','',$_out);
        $_out = substr($_out,27);//23
        $_out = substr($_out,0,-2);

        $res = json_decode($_out);
        //var_dump($res->reserva_V2);exit;
        if ($res->reserva_V2 != null) {

            $localizador = array(
                'endosos' => $res->reserva_V2->endosos,
                'fecha_creacion' => $res->reserva_V2->fecha_creacion,
                'hora_creacion' => $res->reserva_V2->hora_creacion,
                'localizador_resiber' => $res->reserva_V2->localizador_resiber,
                'osis' => $res->reserva_V2->osis,
                'pv' => $res->reserva_V2->pv,
                'nit_cliente' => $res->reserva_V2->endosos->endoso->texto,
            );

            $localizador = json_decode(json_encode($localizador));
            $ct = $res->reserva_V2->cts->ct;
            $fc = $res->reserva_V2->elementosTkt->fcs->fc;
            $pasajeros = $res->reserva_V2->pasajeros;
            $tasa = $res->reserva_V2->elementosTkt->fns->fn_V2->Fntaxs->tasa;
            $fn_V2 = $res->reserva_V2->elementosTkt->fns->fn_V2;
            $ssrs = $res->reserva_V2->ssrs;
            $tl = $res->reserva_V2->tl;
            $responsable = $res->reserva_V2->responsable;
            $tipo_pv = $res->reserva_V2->tipo_pv;
            $update = $res->reserva_V2->update;
            $vuelo = $res->reserva_V2->vuelos->vuelo;

            $importes = array(
                'inf' => $res->reserva_V2->elementosTkt->fns->fn_V2->inf,
                'num_pax' => $res->reserva_V2->elementosTkt->fns->fn_V2->num_pax,
                'codigo_tarifa' => $res->reserva_V2->elementosTkt->fns->fn_V2->codigo_tarifa,
                'importe_tarifa' => $res->reserva_V2->elementosTkt->fns->fn_V2->importe_tarifa,
                'importe_total' => $res->reserva_V2->elementosTkt->fns->fn_V2->importe_total,
                'moneda_tarifa' => $res->reserva_V2->elementosTkt->fns->fn_V2->moneda_tarifa,
                'moneda_total' => $res->reserva_V2->elementosTkt->fns->fn_V2->moneda_total,
                'tipo_emision' => $res->reserva_V2->elementosTkt->fns->fn_V2->tipo_emision,
                'tipo_tarifa' => $res->reserva_V2->elementosTkt->fns->fn_V2->tipo_tarifa,
                'tipo_total' => $res->reserva_V2->elementosTkt->fns->fn_V2->tipo_total
            );
            $importes = json_decode(json_encode($importes));

            $this->objParam->addParametro('localizador', json_encode($localizador));
            $this->objParam->addParametro('ct', json_encode($ct));
            $this->objParam->addParametro('fc', json_encode($fc));
            $this->objParam->addParametro('pasajeros', json_encode($pasajeros));
            $this->objParam->addParametro('tasa', json_encode($tasa));
            $this->objParam->addParametro('importes', json_encode($importes));
            $this->objParam->addParametro('fn_V2', json_encode($fn_V2));
            $this->objParam->addParametro('ssrs', json_encode($ssrs));
            $this->objParam->addParametro('tl', json_encode($tl));
            $this->objParam->addParametro('responsable', json_encode($responsable));
            $this->objParam->addParametro('tipo_pv', json_encode($tipo_pv));
            $this->objParam->addParametro('update', json_encode($update));
            $this->objParam->addParametro('vuelo', json_encode($vuelo));
            $this->objParam->addParametro('tipo', 'exchange');


            /*echo('----------------------------------------LOCALIZADOR-----------------------------------------------');
            var_dump($localizador);
            echo('----------------------------------------CTS -> CT-----------------------------------------------');
            var_dump($res->reserva_V2->cts->ct);
            echo('----------------------------------------FCS-----------------------------------------------');
            var_dump($res->reserva_V2->elementosTkt->fcs->fc);
            echo('-----------------------------------------PASAJERO----------------------------------------------');
            var_dump($res->reserva_V2->pasajeros);
            echo('-------------------------------------------TASAS--------------------------------------------');
            var_dump($res->reserva_V2->elementosTkt->fns->fn_V2->Fntaxs->tasa);
            echo('-------------------------------------------IMPORTES--------------------------------------------');
            var_dump($importes);
            echo('-------------------------------------------FNS TASAS--------------------------------------------');
            var_dump($res->reserva_V2->elementosTkt->fns->fn_V2);
            echo('-----------------------------------------SSRS----------------------------------------------');
            var_dump($res->reserva_V2->ssrs);
            echo('-----------------------------------------TL----------------------------------------------');
            var_dump($res->reserva_V2->tl);
            echo('-----------------------------------------RESPONSABLE----------------------------------------------');
            var_dump($res->reserva_V2->responsable);
            echo('-----------------------------------------TIPO PV----------------------------------------------');
            var_dump($res->reserva_V2->tipo_pv);
            echo('-----------------------------------------UPDATE----------------------------------------------');
            var_dump($res->reserva_V2->update);
            echo('-----------------------------------------VUELOS----------------------------------------------');
            var_dump($res->reserva_V2->vuelos);

             exit;*/

            $this->objFunc = $this->create('MODBoleto');
            $this->res = $this->objFunc->traerReservaBoletoExch($this->objParam);
            $datos = $this->res->getDatos();

            $this->objParam->addParametro('datos_detalle', $res->reserva_V2);
            $this->objParam->addParametro('datos', $datos);

            $nombreArchivo = uniqid(md5(session_id()) . 'Boleto BO');


            $this->objParam->addParametro('titulo_archivo', 'Boleto');
            $nombreArchivo .= '.pdf';
            $this->objParam->addParametro('nombre_archivo', $nombreArchivo);
            $this->objParam->addParametro('orientacion', 'P');
            $this->objParam->addParametro('tamano', 'A4');

            //Instancia la clase de pdf
            $this->objReporteFormato = new RBoletoBOPDF($this->objParam);
            $this->objReporteFormato->generarReporte();
            $this->objReporteFormato->output($this->objReporteFormato->url_archivo, 'F');


            //$this->extraData['tipo_emision'] = $datos[0]['tipo_emision'];

            if($datos == null){
                $tipo_emision = 'consulta';
            }else{
                $tipo_emision = $datos[0]['tipo_emision'];
            }

            $this->mensajeExito = new Mensaje();

            $this->mensajeExito->setMensaje('EXITO', 'Reporte.php', 'Reporte generado',
                'Se generó con éxito el reporte: ' . $nombreArchivo, 'control');
            $this->mensajeExito->setArchivoGenerado($nombreArchivo);
            $this->mensajeExito->setDatos(array("tipo_emision"=>$tipo_emision));
            $this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());
        }else{

            /*$this->objParam->addParametro('tipo', 'normal');
            $this->objFunc = $this->create('MODBoleto');

            $this->res = $this->objFunc->generarBilleteElectronico($this->objParam);

            $datos = $this->res->getDatos();

            $this->objParam->addParametro('datos', $datos);

            $nombreArchivo = uniqid(md5(session_id()) . 'Boleto BO');

            $this->objParam->addParametro('titulo_archivo', 'Boleto');
            $nombreArchivo .= '.pdf';
            $this->objParam->addParametro('nombre_archivo', $nombreArchivo);
            $this->objParam->addParametro('orientacion', 'P');
            $this->objParam->addParametro('tamano', 'A4');

            //Instancia la clase de pdf
            $this->objReporteFormato = new RBoletoBOPDF($this->objParam);
            $this->objReporteFormato->generarReporte();
            $this->objReporteFormato->output($this->objReporteFormato->url_archivo, 'F');*/

            $this->mensajeExito = new Mensaje();
            /*$this->mensajeExito->setMensaje('EXITO', 'Reporte.php', 'Reporte generado',
                'Se generó con éxito el reporte: ' . $nombreArchivo, 'control');
            $this->mensajeExito->setArchivoGenerado($nombreArchivo);*/
            //$this->mensajeExito->setDatos(array());
            $res = json_decode($out_origin);
            if($res == null){
                $tipo_emision = 'normal';
            }else{
                $tipo_emision = 'estructura';
            }
            $this->mensajeExito->setDatos(array("tipo_emision"=>$tipo_emision, "error"=>$res->TraerReservaExchResult));
            $this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());
        }
        //$this->res->imprimirRespuesta($this->res->generarJson());
    }

    function verificarBoletoExch()
    {
        $respuesta = '';
        if (!isset($_SESSION['_CREDENCIALES_RESIBER']) || $_SESSION['_CREDENCIALES_RESIBER'] == '') {
            throw new Exception('No se definieron las credenciales para conectarse al servicio de Resiber.');
        }

        $pnr = $this->objParam->getParametro('pnr');
        $data = array("credenciales" => "{ae7419a1-dbd2-4ea9-9335-2baa08ba78b4}{59331f3e-a518-4e1e-85ca-8df59d14a420}",
            "idioma" => "ES",
            "pnr" => $pnr,//'MSB9Z8'
            "apellido" => "prueba",
            "ip" => "127.0.0.1",
            "xmlJson" => false);

        $json_data = json_encode($data);
        $s = curl_init();
        curl_setopt($s, CURLOPT_URL, 'https://ef.boa.bo/ServicioINT/ServicioInterno.svc/TraerReservaExch');//skbproduccion, skbpruebas
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
            throw new Exception("No se pudo conectar con Resiber");
        }

        curl_close($s);

        $_out = str_replace('\\', '', $_out);
        $_out = substr($_out, 27);//23
        $_out = substr($_out, 0, -2);

        $res = json_decode($_out);

        if ($res->reserva_V2 != null) {
            $this->objParam->addParametro('exchange', true);

            $tipo_emision = $res->reserva_V2->elementosTkt->fns->fn_V2->tipo_emision;
            if ($tipo_emision != null) {
                $tipo_emision = $res->reserva_V2->elementosTkt->fns->fn_V2->tipo_emision;
            }else{
                $tipo_emision = $res->reserva_V2->elementosTkt->fns->fn_V2;
            }
            $this->objParam->addParametro('tipo_emision', json_encode($tipo_emision));
        } else {
            $this->objParam->addParametro('exchange', false);
            $this->objParam->addParametro('tipo_emision', json_encode(array('tipo_emision'=>'F')));
        }

        $this->objFunc=$this->create('MODBoleto');
        $this->res=$this->objFunc->verificarBoletoExch($this->objParam);

        /*$respuesta = [];
        $this->res = new Mensaje();

        if ($res->reserva_V2 != null) {
            array_unshift($respuesta, array('exchange' => true, 'tipo_emision' => $tipo_emision));
            $this->res->setDatos($respuesta);
            //return $respuesta;
        } else {
            array_unshift($respuesta, array('exchange' => false, 'tipo_emision' => $tipo_emision));
            $this->res->setDatos($respuesta);
            //return $respuesta;
        }*/
        $this->res->imprimirRespuesta($this->res->generarJson());
    }
    //correo de incidentes detalle venta web
    function disparaCorreoVentasWeb(){
        $this->objFunc=$this->create('MODBoleto');
        $this->res=$this->objFunc->disparaCorreoVentasWeb($this->objParam);

        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    //(f.e.a)ventas por dia counter
    function listarVentasCounter(){

        $this->objParam->defecto('ordenacion','nro_boleto');
        $this->objParam->defecto('dir_ordenacion','desc');

        //$this->objParam->addFiltro(" bol.fecha_emision = ''".$this->objParam->getParametro('fecha')."''::date");

        //$this->objParam->addParametro('id_usuario', $_SESSION["ss_id_usuario"]);


        $this->objFunc=$this->create('MODBoleto');
        $this->res=$this->objFunc->listarVentasCounter($this->objParam);

        //adicionar una fila al resultado con el summario
		$temp = Array();
        $temp['id_boleto_amadeus'] = 0;
		$temp['precio_total'] ='<b style="font-size: 15px; color: green">'.$this->res->extraData['precio_total'].'</b>';
		$temp['monto_forma_pago'] ='<b style="font-size: 15px; color: green">'.$this->res->extraData['precio_total'].'</b>';
        $temp['nro_boleto'] = '<b style="font-size: 20px; color: green">Total</b>';
		//$temp['tipo_reg'] = 'summary';


		$this->res->total++;

		$this->res->addLastRecDatos($temp);

        $this->res->imprimirRespuesta($this->res->generarJson());
    }


}

?>
