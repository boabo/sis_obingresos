<?php
/**
*@package pXP
*@file gen-ACTBoleto.php
*@author  (jrivera)
*@date 06-01-2016 22:42:25
*@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
*/

class ACTBoleto extends ACTbase{    
			
	function listarBoleto(){
		$this->objParam->defecto('ordenacion','id_boleto');

		$this->objParam->defecto('dir_ordenacion','desc');
		
		if ($this->objParam->getParametro('id_agencia') != '') {
			$this->objParam->addFiltro("bol.id_agencia = ". $this->objParam->getParametro('id_agencia'));
		}
		
		if ($this->objParam->getParametro('id_punto_venta') != '') {
			$this->objParam->addFiltro("bol.id_punto_venta = ". $this->objParam->getParametro('id_punto_venta'));
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
		$this->objFunc=$this->create('MODBoleto');	
		if($this->objParam->insertar('ids_seleccionados')) {
			$this->res=$this->objFunc->modificarBoletoVenta($this->objParam);
		} else {
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
				$this->res=$this->obtenerBoletoFromServicio();
				$this->objFunc=$this->create('MODBoleto');	
				$this->res=$this->objFunc->listarBoleto($this->objParam);
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
	
	function obtenerBoletoFromServicio(){
		
		if (!isset($_SESSION['_CREDENCIALES_RESIBER']) || $_SESSION['_CREDENCIALES_RESIBER'] == ''){
			throw new Exception('No se definieron las credenciales para conectarse al servicio de Resiber.');
		}
		$data = array(	"credenciales"=>$_SESSION['_CREDENCIALES_RESIBER'],
											"idioma"=>"ES",
											"tkt"=>$this->objParam->getParametro('nro_boleto'),
											"ip"=>"127.0.0.1",
											"xmlJson"=>false);
		$json_data = json_encode($data); 
		$s = curl_init();
		curl_setopt($s, CURLOPT_URL, 'https://ef.boa.bo/Servicios/ServicioInterno.svc/TraerTkt');
		curl_setopt($s, CURLOPT_POST, true);
		curl_setopt($s, CURLOPT_POSTFIELDS, $json_data);
		curl_setopt($s, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($s, CURLOPT_HTTPHEADER, array(                                                                          
		    'Content-Type: application/json',                                                                                
		    'Content-Length: ' . strlen($json_data))                                                                       
		);  
		$_out = curl_exec($s);		
		$status = curl_getinfo($s, CURLINFO_HTTP_CODE);
		curl_close($s);
		$res = json_decode($_out);
		$cadena = str_replace('"terminal_salida":{,},', '', $res->TraerTktResult);
		
		
		if(strpos($cadena, 'Error') !== false) {
			throw new Exception('No se encontro el numero de billete indicado.');
			
		} else {			
			$res = json_decode($cadena, true);
			
			$this->objParam->addParametro('pasajero',$res['billete']['nom_apdos']['#text']);
			$this->objParam->addParametro('fecha_emision',$res['billete']['fecha_emision']['#text']);
			$this->objParam->addParametro('total',$res['billete']['total']['#text']);
			$this->objParam->addParametro('moneda',$res['billete']['moneda']['#text']);
			$this->objParam->addParametro('neto',$res['billete']['tarifa']['#text']);
			
			if (isset($res['billete']['endosos']['#text'])) {
				$this->objParam->addParametro('endoso',$res['billete']['endosos']['#text']);
			} else {
				$this->objParam->addParametro('endoso',$res['billete']['nom_apdos']['#text']);
			}
			$ruta_completa = '';
			if (isset($res['billete']['vuelos']['vuelo'][0])) {
				$this->objParam->addParametro('origen',$res['billete']['vuelos']['vuelo'][0]['origen']);
				$cupones = count($res['billete']['vuelos']['vuelo']);
				$this->objParam->addParametro('destino',$res['billete']['vuelos']['vuelo'][$cupones-1]['destino']);
				
				$ruta = $res['billete']['vuelos']['vuelo'][0]['origen'] . '-' . $res['billete']['vuelos']['vuelo'][0]['destino'];
				for ($i = 1;$i < $cupones;$i++) {
					$ruta_completa .= "-" . $res['billete']['vuelos']['vuelo'][$i]['destino'];
				}
			} else {
				$this->objParam->addParametro('origen',$res['billete']['vuelos']['vuelo']['origen']);
				$cupones = 1;
				$this->objParam->addParametro('destino',$res['billete']['vuelos']['vuelo']['destino']);
				
			}
			$this->objParam->addParametro('ruta_completa',$ruta_completa);
			$this->objParam->addParametro('cupones',$cupones);
			$posicion = strpos($res['billete']['fare_calc']['#text'], '*XT');
			if ($posicion) {
				$impuesto = substr ( $res['billete']['fare_calc']['#text'] , $posicion + 3 );
			} else {
				$impuesto = "";
			}
			if (isset($res['billete']['tasas']['tasa'])) {
				foreach ($res['billete']['tasas']['tasa'] as $dato) {
					if (!strpos($dato['valor'],'XT') && !strpos($dato['valor'],'EXEMPT')) {
						$temporal = substr($dato['valor'], 4);
						$temporal = trim($temporal);
						$impuesto .= $temporal;
					}
				} 
			}
			
			$this->objParam->addParametro('impuestos',$impuesto);
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
			foreach ($res['billete']['vuelos']['vuelo'] as $dato) {
				$rutas .= '#' . $dato['origen'];
				$rutas .= '#' . $dato['destino'];
			} 			
			$this->objParam->addParametro('rutas',$rutas);
			
			$this->objFunc=$this->create('MODBoleto');
			$this->res=$this->objFunc->insertarBoletoServicio($this->objParam);
			if ($this->res->getTipo()=='ERROR') {
				$this->res->imprimirRespuesta($this->res->generarJson());
				exit;
			}
		}
		
	}
			
}

?>