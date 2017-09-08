<?php
/**
*@package pXP
*@file gen-ACTBoleto.php
*@author  (jrivera)
*@date 06-01-2016 22:42:25
*@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
*/
include(dirname(__FILE__).'/../reportes/RBoleto.php');
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

		if ($this->objParam->getParametro('officeID') != '') {
			$this->objParam->addFiltro("bol.id_punto_venta = ". $this->objParam->getParametro('id_punto_venta'));
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
		/*$this->objParam->defecto('ordenacion','id_boleto');
		$this->objParam->defecto('dir_ordenacion','desc');
		$this->objParam->defecto('puntero','0');
		$this->objParam->defecto('cantidad','1');
		*/
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
	
	function cambiaEstadoBoleto(){
		$this->objFunc=$this->create('MODBoleto');	
		$this->res=$this->objFunc->cambiaEstadoBoleto($this->objParam);
		$this->res->imprimirRespuesta($this->res->generarJson());
	}

	function traerBoletos(){

		if ($this->objParam->getParametro('id_punto_venta') != '') {
			$this->objParam->addParametro('id_punto_venta', $this->objParam->getParametro('id_punto_venta'));
		}

		if ($this->objParam->getParametro('fecha') != '') {
			$fecha = $this->objParam->getParametro('fecha');
		}else{
			$fecha = date("Ymd");
		}
		$this->objFunc=$this->create('sis_ventas_facturacion/MODPuntoVenta');
		$this->res=$this->objFunc->obtenerOfficeID($this->objParam);
		$datos = $this->res->getDatos();
		$officeid = $datos[0]['officeid'];

		//boletos en bolivianos
		//$data = array("numberItems"=>"0","lastItemNumber"=>"0","officeID"=>"CBBOB0900","dateFrom"=>$fecha,"dateTo"=>$fecha);
		$data = array("numberItems"=>"0","lastItemNumber"=>"0","officeID"=>"SRZOB0104","dateFrom"=>"20170808","dateTo"=>"20170808","monetary"=>"BOB");
		$data_string = json_encode($data);
		//$request =  'http://wservices.obairlines.bo/Dotacion.AppService/SvcDotacion.svc/RevertirDotacionAlmacenes';
		$request =  'http://wservices.obairlines.bo/esb/RITISERP.svc/Boa_RITRetrieveSales';
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

				//var_dump($boleto->documentNumber->documentDetails->number->__toString());

				$this->objParam->addParametro('id_punto_venta', $this->objParam->getParametro('id_punto_venta'));
				$this->objParam->addParametro('nro_boleto', $boleto->documentNumber->documentDetails->number->__toString());
				//$this->objParam->addParametro('fecha_emision', $fecha);
				$this->objParam->addParametro('fecha_emision',"20170808");

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
				/*$this->objParam->addParametro('endoso',"");
				$this->objParam->addParametro('origen',"");
				$this->objParam->addParametro('destino',"");
				$this->objParam->addParametro('cupones',"");
				$this->objParam->addParametro('impuestos',"");*/
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

				//$this->objParam->addParametro('mandar_fp',"");
				if ($boleto->fopDetails->monetaryInfo->monetaryDetails->amount->__toString() == '') {
					//exception valor forma de pago no definida
					throw new Exception(__METHOD__ . 'VALOR FORMA DE PAGO NO DEFINIDO');
				}

				if ($boleto->fopDetails->fopDescription->formOfPayment->type->__toString() == 'CC') {
					if ($boleto->fopDetails->fopDescription->formOfPayment->vendorCode->__toString() == 'VI') {
						$this->objParam->addParametro('fp', 'CCVI');
					}
					if ($boleto->fopDetails->fopDescription->formOfPayment->vendorCode->__toString() == 'CA') {
						$this->objParam->addParametro('fp', 'CCCA');
					}
					if ($boleto->fopDetails->fopDescription->formOfPayment->vendorCode->__toString() == 'AX') {
						$this->objParam->addParametro('fp', 'CCAX');
					}
				}

				/*$this->objParam->addParametro('rutas',"");
				$this->objParam->addParametro('ruta_completa',"");
				$this->objParam->addParametro('vuelos',"");*/
				$this->objParam->addParametro('localizador', $boleto->reservationInformation->reservation->controlNumber->__toString());
				/*$this->objParam->addParametro('identificacion',"");
				$this->objParam->addParametro('fare_calc',"");
				$this->objParam->addParametro('vuelos2',"");*/

				if ($this->objParam->getParametro('id_usuario_cajero') != '') {
					$this->objParam->addParametro('id_usuario_cajero', $this->objParam->getParametro('id_usuario_cajero'));
					$this->objFunc = $this->create('MODBoleto');
					$this->res = $this->objFunc->actualizaBoletoServicioAmadeus($this->objParam);
				} else {
					$this->objFunc = $this->create('MODBoleto');
					$this->res = $this->objFunc->insertarBoletoServicioAmadeus($this->objParam);
				}

				if ($this->res->getTipo() == 'ERROR') {
					$this->res->imprimirRespuesta($this->res->generarJson());
					exit;
				}
			}
		}

		////boletos en dolares
		$data = array("numberItems"=>"0","lastItemNumber"=>"0","officeID"=>"SRZOB0104","dateFrom"=>"20170808","dateTo"=>"20170808","monetary"=>"USD");
		$data_string = json_encode($data);
		//$request =  'http://wservices.obairlines.bo/Dotacion.AppService/SvcDotacion.svc/RevertirDotacionAlmacenes';
		$request =  'http://wservices.obairlines.bo/esb/RITISERP.svc/Boa_RITRetrieveSales';
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

				//var_dump($boleto->documentNumber->documentDetails->number->__toString());

				$this->objParam->addParametro('id_punto_venta', $this->objParam->getParametro('id_punto_venta'));
				$this->objParam->addParametro('nro_boleto', $boleto->documentNumber->documentDetails->number->__toString());
				//$this->objParam->addParametro('fecha_emision', $fecha);
				$this->objParam->addParametro('fecha_emision',"20170808");

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
				/*$this->objParam->addParametro('endoso',"");
				$this->objParam->addParametro('origen',"");
				$this->objParam->addParametro('destino',"");
				$this->objParam->addParametro('cupones',"");
				$this->objParam->addParametro('impuestos',"");*/
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

				//$this->objParam->addParametro('mandar_fp',"");
				if ($boleto->fopDetails->monetaryInfo->monetaryDetails->amount->__toString() == '') {
					//exception valor forma de pago no definida
					throw new Exception(__METHOD__ . 'VALOR FORMA DE PAGO NO DEFINIDO');
				}

				if ($boleto->fopDetails->fopDescription->formOfPayment->type->__toString() == 'CC') {
					if ($boleto->fopDetails->fopDescription->formOfPayment->vendorCode->__toString() == 'VI') {
						$this->objParam->addParametro('fp', 'CCVI');
					}
					if ($boleto->fopDetails->fopDescription->formOfPayment->vendorCode->__toString() == 'CA') {
						$this->objParam->addParametro('fp', 'CCCA');
					}
					if ($boleto->fopDetails->fopDescription->formOfPayment->vendorCode->__toString() == 'AX') {
						$this->objParam->addParametro('fp', 'CCAX');
					}
				}

				/*$this->objParam->addParametro('rutas',"");
				$this->objParam->addParametro('ruta_completa',"");
				$this->objParam->addParametro('vuelos',"");*/
				$this->objParam->addParametro('localizador', $boleto->reservationInformation->reservation->controlNumber->__toString());
				/*$this->objParam->addParametro('identificacion',"");
				$this->objParam->addParametro('fare_calc',"");
				$this->objParam->addParametro('vuelos2',"");*/

				if ($this->objParam->getParametro('id_usuario_cajero') != '') {
					$this->objParam->addParametro('id_usuario_cajero', $this->objParam->getParametro('id_usuario_cajero'));
					$this->objFunc = $this->create('MODBoleto');
					$this->res = $this->objFunc->actualizaBoletoServicioAmadeus($this->objParam);
				} else {
					$this->objFunc = $this->create('MODBoleto');
					$this->res = $this->objFunc->insertarBoletoServicioAmadeus($this->objParam);
				}

				if ($this->res->getTipo() == 'ERROR') {
					$this->res->imprimirRespuesta($this->res->generarJson());
					exit;
				}
			}
		}

		$this->mensajeRes=new Mensaje();
		$this->mensajeRes->setMensaje('EXITO','ACTBoleto.php','Se recuperaron los boletos',
				'Se recuperaron los boletos cone exito','control');
		$this->mensajeRes->imprimirRespuesta($this->mensajeRes->generarJson());
		//var_dump($xmlRespuesta->queryReportDataDetails->queryReportDataOfficeGroup->{'documentData'});
		//var_dump($xmlRespuesta->xpath('//documentData'));

	}
	
	function obtenerBoletoFromServicio(){
		
		if (!isset($_SESSION['_CREDENCIALES_RESIBER']) || $_SESSION['_CREDENCIALES_RESIBER'] == ''){
			throw new Exception('No se definieron las credenciales para conectarse al servicio de Resiber.');
		}
		$data = array(	"credenciales"=>"{ae7419a1-dbd2-4ea9-9335-2baa08ba78b4}{59331f3e-a518-4e1e-85ca-8df59d14a420}",
											"idioma"=>"ES",
                                            "tkt"=>"9302400053068",
                                            "pnr"=>"LOAKNP",
											"ip"=>"127.0.0.1",
											"xmlJson"=>false);

		$json_data = json_encode($data);

		$s = curl_init();
		curl_setopt($s, CURLOPT_URL, 'http://skbpruebas.cloudapp.net/ServicioINT/ServicioInterno.svc/TraerTkt');
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
			$this->objParam->addParametro('fecha_emision',$res['billete']['fecha_emision']['#text']);
			$this->objParam->addParametro('total',$res['billete']['total']['#text']);
			$this->objParam->addParametro('moneda',$res['billete']['moneda']['#text']);
			$this->objParam->addParametro('neto',$res['billete']['tarifa']['#text']);

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
                $this->objParam->addParametro('identificacion',$res['billete']['foids']['foid'][0]);
            }

			
			
			
			if (isset($res['billete']['endosoFields'])) {
				$this->objParam->addParametro('endoso', $res['billete']['endosoFields'][7]['Value'] . " " . $res['billete']['endosoFields'][8]['Value'] . " " . ($res['billete']['endosoFields'][1]['Value']!=''?$res['billete']['endosoFields'][1]['Value']:$res['billete']['endosoFields'][0]['Value']));
			} else {
				$this->objParam->addParametro('endoso','');
			}
			$ruta_completa = '';
			$vuelos = '';
			
			if (isset($res['billete']['vuelos']['vuelo'][0])) {
				$this->objParam->addParametro('origen',$res['billete']['vuelos']['vuelo'][0]['origen']);
				$cupones = count($res['billete']['vuelos']['vuelo']);
				$this->objParam->addParametro('destino',$res['billete']['vuelos']['vuelo'][$cupones-1]['destino']);
				//ingresar vuelo
				$vuelos = $res['billete']['vuelos']['vuelo'][0]['fecha_salida'];
				$vuelos = $vuelos . "|" . $res['billete']['vuelos']['vuelo'][0]['num_vuelo'];
				$vuelos = $vuelos . "|" . $res['billete']['vuelos']['vuelo'][0]['hora_salida'];
				$vuelos = $vuelos . "|" . $res['billete']['vuelos']['vuelo'][0]['origen'];
				$vuelos = $vuelos . "|" . $res['billete']['vuelos']['vuelo'][0]['destino'];
				$vuelos = $vuelos . "|" . $res['billete']['vuelos']['vuelo'][0]['fare_basis'];
				$vuelos = $vuelos . "|" . $res['billete']['vuelos']['vuelo'][0]['kgs'];
				$vuelos = $vuelos . "|" . $res['billete']['vuelos']['vuelo'][0]['status'];
                $vuelos = $vuelos . "|" . $res['billete']['vuelos']['vuelo'][0]['clase'];
                $vuelos = $vuelos . "|" . $res['billete']['vuelos']['vuelo'][0]['flight_status'];
                $vuelos = $vuelos . "|" . $res['billete']['vuelos']['vuelo'][0]['linea'];
				
				$ruta_completa = $res['billete']['vuelos']['vuelo'][0]['origen'] . '-' . $res['billete']['vuelos']['vuelo'][0]['destino'];
				for ($i = 1;$i < $cupones;$i++) {
					$ruta_completa .= "-" . $res['billete']['vuelos']['vuelo'][$i]['destino'];
					$vuelos = $vuelos . "$$$" . $res['billete']['vuelos']['vuelo'][$i]['fecha_salida'];
					$vuelos = $vuelos . "|" . $res['billete']['vuelos']['vuelo'][$i]['num_vuelo'];
					$vuelos = $vuelos . "|" . $res['billete']['vuelos']['vuelo'][$i]['hora_salida'];
					$vuelos = $vuelos . "|" . $res['billete']['vuelos']['vuelo'][$i]['origen'];
					$vuelos = $vuelos . "|" . $res['billete']['vuelos']['vuelo'][$i]['destino'];
					$vuelos = $vuelos . "|" . $res['billete']['vuelos']['vuelo'][$i]['fare_basis'];
					$vuelos = $vuelos . "|" . $res['billete']['vuelos']['vuelo'][$i]['kgs'];
					$vuelos = $vuelos . "|" . $res['billete']['vuelos']['vuelo'][$i]['status'];
                    $vuelos = $vuelos . "|" . $res['billete']['vuelos']['vuelo'][$i]['clase'];
                    $vuelos = $vuelos . "|" . $res['billete']['vuelos']['vuelo'][$i]['flight_status'];
                    $vuelos = $vuelos . "|" . $res['billete']['vuelos']['vuelo'][$i]['linea'];
				}
			} else {
				$ruta_completa = $res['billete']['vuelos']['vuelo']['origen'] . '-' . $res['billete']['vuelos']['vuelo']['destino'];
				$this->objParam->addParametro('origen',$res['billete']['vuelos']['vuelo']['origen']);
				$cupones = 1;
				$vuelos = $res['billete']['vuelos']['vuelo']['fecha_salida'];
				$vuelos = $vuelos . "|" . $res['billete']['vuelos']['vuelo']['num_vuelo'];
				$vuelos = $vuelos . "|" . $res['billete']['vuelos']['vuelo']['hora_salida'];
				$vuelos = $vuelos . "|" . $res['billete']['vuelos']['vuelo']['origen'];
				$vuelos = $vuelos . "|" . $res['billete']['vuelos']['vuelo']['destino'];
				$vuelos = $vuelos . "|" . $res['billete']['vuelos']['vuelo']['fare_basis'];
				$vuelos = $vuelos . "|" . $res['billete']['vuelos']['vuelo']['kgs'];
				$vuelos = $vuelos . "|" . $res['billete']['vuelos']['vuelo']['status'];
                $vuelos = $vuelos . "|" . $res['billete']['vuelos']['vuelo']['clase'];
                $vuelos = $vuelos . "|" . $res['billete']['vuelos']['vuelo']['flight_status'];
                $vuelos = $vuelos . "|" . $res['billete']['vuelos']['vuelo']['linea'];
				$this->objParam->addParametro('destino',$res['billete']['vuelos']['vuelo']['destino']);
				
			}
			$this->objParam->addParametro('vuelos',$vuelos);
			$this->objParam->addParametro('ruta_completa',$ruta_completa);
			$this->objParam->addParametro('cupones',$cupones);
			$posicion = strpos($res['billete']['fare_calc']['#text'], '*XT');
			if ($posicion) {
				$impuesto = substr ( $res['billete']['fare_calc']['#text'] , $posicion + 3 );
			} else {
				$impuesto = "";
			}
			$this->objParam->addParametro('impuestos',$impuesto);
            $this->objParam->addParametro('fare_calc',$res['billete']['fare_calc']['#text']);
			
			$tasa = "";
			if (isset($res['billete']['tasas']['tasa'])) {
				foreach ($res['billete']['tasas']['tasa'] as $dato) {
					if (!strpos($dato['valor'],'EXEMPT')) {
						$temporal = substr($dato['valor'], 3);
						$temporal = trim($temporal);
						$tasa .= $temporal;
					}
				} 
			}
			
			$this->objParam->addParametro('tasas',$tasa);
			$fps = explode('+', $res['billete']['forma_pago']['#text']);
			$fp = '';
			$moneda_fp = '';
			$valor_fp = '';
			foreach ($fps as $dato) {
				
				$temp_array = explode('/', $dato);
				if (strpos($temp_array[0], 'CASH')!== FALSE) { //Cash y MCO
					if (strpos($temp_array[0], 'CASH,MCO')!== FALSE) {
						$fp .= '#MCO';
						$moneda_fp .= '#' . substr($temp_array[1], 0,3);
						$temp_array[1] = substr($temp_array[1], 3);
						$temp_array[1] = trim($temp_array[1]);
						$valor_fp .= '#' . $temp_array[1];
					} else if(strpos($temp_array[0], 'TKT,CASH')!== FALSE)	{
						$fp .= '#EX';						
						$moneda_fp .= '#' . $res['billete']['moneda']['#text'];
						$valor_fp .= '#0';
					}else {
						$fp .= '#CA';
						$moneda_fp .= '#' . substr($temp_array[1], 0,3);
						$temp_array[1] = substr($temp_array[1], 3);
						$temp_array[1] = trim($temp_array[1]);
						$valor_fp .= '#' . $temp_array[1];
					}			
															
					
				} else if (strpos($temp_array[0], 'DEPU')!== FALSE) { //El boleto tiene un valor de 0						
						$fp .= '#CA';
						$moneda_fp .= '#' . $res['billete']['moneda']['#text'];		
						$valor_fp .= '#' . '0';											
					
				}else if (strpos($temp_array[0], 'SF') !== FALSE) { //forma de pago SF
					if (strpos($temp_array[0], 'SFCA')!== FALSE) {
						$fp .= '#SFCA';
						$moneda_fp .= '#' . substr($temp_array[1], 0,3);						
										
						$temp_array[1] = substr($temp_array[1], 3);
						$temp_array[1] = trim($temp_array[1]);
						$valor_fp .= '#' . $temp_array[1];
					} else if (strpos($temp_array[0], 'SFCC')!== FALSE){
						$temp_array[0] = str_replace('SFCC', 'SF', $temp_array[0]);
						$fp .= '#' . substr($temp_array[0], 0,4);
						$moneda_fp .= '#' . substr($temp_array[3], 0,3);					
						$temp_array[3] = substr($temp_array[3], 3);
						$temp_array[3] = trim($temp_array[3]);
						$valor_fp .= '#' . $temp_array[3];
					} else {
						throw new Exception('El billete tiene una forma de pago de tipo SF no reconocida.');
					}
				} else if (strpos($temp_array[0], ',') == 2){ //tarjeta de credito
					$fp .= '#CC' . substr($temp_array[0], 0,2);
					$moneda_fp .= '#' . substr($temp_array[3], 0,3);					
					$temp_array[3] = substr($temp_array[3], 3);
					$temp_array[3] = trim($temp_array[3]);
					$valor_fp .= '#' . $temp_array[3];				
					
				} else {
					throw new Exception('El billete tiene una forma de pago no reconocida.');
				}
			}
			
			$this->objParam->addParametro('fp',$fp);
			$this->objParam->addParametro('moneda_fp',$moneda_fp);
			$this->objParam->addParametro('valor_fp',$valor_fp);
			$rutas = '';
            if (isset($res['billete']['vuelos']['vuelo'][0])) {
                foreach ($res['billete']['vuelos']['vuelo'] as $dato) {

                    $rutas .= '#' . $dato['origen'];
                    $rutas .= '#' . $dato['destino'];
                }
            } else {
                $rutas .= '#' . $res['billete']['vuelos']['vuelo']['origen'];
                $rutas .= '#' . $res['billete']['vuelos']['vuelo']['destino'];
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

        $data = array(	"credenciales"=>$_SESSION['_CREDENCIALES_RESIBER'],
            "idioma"=>"ES",
            "pnr"=>$pnr,
            "apellido"=>$arreglo[0],
            "ip"=>"127.0.0.1",
            "xmlJson"=>false);

        $json_data = json_encode($data);

        $s = curl_init();
        curl_setopt($s, CURLOPT_URL, 'https://ef.boa.bo/Servicios/ServicioInterno.svc/TraerReserva');
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
        $cadena = str_replace('"terminal_salida":{,},', '', $res->TraerReservaResult);


        if(strpos($cadena, 'Error') !== false) {
            throw new Exception('No se encontro el pnr indicado.');

        } else {
            $res = json_decode($cadena, true);

            foreach ($res['reserva']['vuelos']['vuelo'] as $value) {
               $respuesta .= $value['origen'] . '|' .  $value['destino'] .'|' .  $value['fecha_salida'] .'|' .  $value['hora_salida'] . '|' . $value['hora_llegada'] . '$$$';
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



        }
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

}

?>