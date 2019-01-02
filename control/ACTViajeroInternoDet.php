<?php
/**
*@package pXP
*@file gen-ACTViajeroInternoDet.php
*@author  (rzabala)
*@date 21-12-2018 14:21:07
*@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
*/

class ACTViajeroInternoDet extends ACTbase{    
			
	function listarViajeroInternoDet(){
		$this->objParam->defecto('ordenacion','id_viajero_interno_det');

		$this->objParam->defecto('dir_ordenacion','asc');
        if($this->objParam->getParametro('id_viajero_interno')!=''){
            $this->objParam->addFiltro("dvi.id_viajero_interno = ".$this->objParam->getParametro('id_viajero_interno'));
        }
		if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
			$this->objReporte = new Reporte($this->objParam,$this);
			$this->res = $this->objReporte->generarReporteListado('MODViajeroInternoDet','listarViajeroInternoDet');
		} else{
			$this->objFunc=$this->create('MODViajeroInternoDet');
			
			$this->res=$this->objFunc->listarViajeroInternoDet($this->objParam);
		}
 /*       $prueba = $this->res->generarJson();
		$prueba = json_decode($prueba);
        /*$_out = str_replace('\\','',$prueba);
        $_out = substr($_out,27);//23
        $_out = substr($_out,0,-2);
		foreach($_out as $valor){
		    var_dump($valor['nombre']);
        }*/
        /*foreach($prueba as $valor){
            var_dump($valor['nombre']);
        }*/
        //var_dump($prueba);exit;
        //$temp = Array();*/

        $this->res->imprimirRespuesta($this->res->generarJson());
	}
				
	function insertarViajeroInternoDet(){
		$this->objFunc=$this->create('MODViajeroInternoDet');	
		if($this->objParam->insertar('id_viajero_interno_det')){
			$this->res=$this->objFunc->insertarViajeroInternoDet($this->objParam);			
		} else{			
			$this->res=$this->objFunc->modificarViajeroInternoDet($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
    function validacionViajeroInternoDet(){
	    /*if($this->objParam->getParametro('id_viajero_interno') != ''){
        $this->objParam->addFiltro("dvi.id_viajero_interno = ". $this->objParam->getParametro('id_viajero_interno'));

    }*/
        $send_data=json_decode($this->objParam->getParametro('obj'),true);
        //var_dump($send_data);exit;
	    foreach ($send_data['listaDatosVoucher'] as $valor){
            //var_dump($valor);exit;
            //$this->resetParametros();
            $detalles=$valor['id_viajero_interno_det'];
            //var_dump($detalles);exit;
	        /*foreach ($valor as $atr){
	            $idViIntDet = $atr['id_viajero_interno_det'];
	            $codVoucher= $atr['codigoVoucher'];
	            $pnr = $atr['pnr'];
	            $boleto=$atr['numBoleto'];
	            $solicitud= $atr['solicitudID'];
	            var_dump($solicitud);exit;

            }*/
       }
        //var_dump($detalles);exit;
        $json_data = json_decode($this->objParam->getParametro('obj'));
        $json_data = json_encode($json_data);


        $s = curl_init();
        curl_setopt($s, CURLOPT_URL, 'http://sms.obairlines.bo/AppServicePasaje/servPasajes.svc/ActualizarDatosVoucher');
        curl_setopt($s, CURLOPT_POST, true);
        curl_setopt($s, CURLOPT_POSTFIELDS, $json_data);
        curl_setopt($s, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($s, CURLOPT_CONNECTTIMEOUT, 20);
        curl_setopt($s, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json;charset=UTF-8',
                'Content-Length: ' . strlen($json_data))
        );
        $_out = curl_exec($s);


        $status = curl_getinfo($s, CURLINFO_HTTP_CODE);

        if (!$status) {
            throw new Exception("No se pudo conectar con el servicio");
        }
        curl_close($s);
        $res = json_decode($_out,true);
        //var_dump($res);exit;
        //$respuesta = json_decode($res,true);
        $decode = json_decode($res['ActualizarDatosVoucherResult'],true);

        //var_dump($decode);exit;
        if ($decode['codigo']== 0) {
            throw new Exception('Error en el servicio.'.$decode['mensaje']);
//            var_dump($respuesta);
        } else {
           //var_dump($decode);exit;
            //$this->objParam->addParametro('estado_voucher', 'EMITIDO');
            //$this->objParam->addParametro('detalles',json_encode($detalles));
            $this->objParam->addParametro('request',json_encode($decode['objeto']));
            $this->objFunc=$this->create('MODViajeroInternoDet');

            $this->res=$this->objFunc->actualizarViajeroInternoDet($this->objParam);
        }
        $this->res->imprimirRespuesta($this->res->generarJson());

    }
	function eliminarViajeroInternoDet(){
			$this->objFunc=$this->create('MODViajeroInternoDet');	
		$this->res=$this->objFunc->eliminarViajeroInternoDet($this->objParam);
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
			
}

?>