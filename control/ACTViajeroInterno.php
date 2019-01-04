<?php
/**
*@package pXP
*@file gen-ACTViajeroInterno.php
*@author  (rzabala)
*@date 21-12-2018 14:21:03
*@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
*/

class ACTViajeroInterno extends ACTbase{    
			
	function listarViajeroInterno(){
		$this->objParam->defecto('ordenacion','id_viajero_interno');

		$this->objParam->defecto('dir_ordenacion','asc');
		if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
			$this->objReporte = new Reporte($this->objParam,$this);
			$this->res = $this->objReporte->generarReporteListado('MODViajeroInterno','listarViajeroInterno');
		} else{
			$this->objFunc=$this->create('MODViajeroInterno');
			
			$this->res=$this->objFunc->listarViajeroInterno($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
				
	function insertarViajeroInterno(){
        $data = array("codigoVoucher" => 'OB.PD.VO'.$this->objParam->getParametro('codigo_voucher'),
    );
        $data_string = json_encode($data);
        $request = 'http://sms.obairlines.bo/AppServicePasaje/servPasajes.svc/GetValidarVoucher';


        $session = curl_init($request);
        curl_setopt($session, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($session, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($session, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data_string)

        ));
        $result = curl_exec($session);
        curl_close($session);
        $respuesta = json_decode($result,true);
        $decode = json_decode($respuesta['GetValidarVoucherResult'],true);
        $prueba = $decode['objeto'];
/*        foreach($prueba as $valor){
            var_dump($valor['estadoVoucher']);
        }*/
        $_out = str_replace('\\','',$result);
        $_out = substr($_out,27);//23
        $_out = substr($_out,0,-2);
        //var_dump(json_decode(json_encode($decode['objeto'])));exit;

        if ($decode['codigo']== 0) {
            //var_dump($decode["dato_valor"]);exit;
            $this->objParam->addParametro('mensaje', $decode["mensaje"]);
            $this->objParam->addParametro('estado', $decode["dato_valor"]);
            $this->objParam->addParametro('detalles', json_encode($decode['objeto']));
            $this->objFunc = $this->create('MODViajeroInterno');
            $this->objParam->insertar('id_viajero_interno');
            $this->res = $this->objFunc->insertarViajeroInterno($this->objParam);
            //$msg = $decode["mensaje"];
            //echo "<script>alert('Usuario ya existe:'.($msg);</script>";
             //throw new Exception('Error en el Servicio:  '.$decode['mensaje']);
            //('Error en el Servicio:  '.$decode['mensaje']);
//            var_dump($respuesta);
        } else {
//            var_dump($respuesta["mensaje"]);
            $this->objParam->addParametro('mensaje', $decode["mensaje"]);
            $this->objParam->addParametro('estado', $decode["dato_valor"]);
            $this->objParam->addParametro('detalles', json_encode($decode['objeto']));
            $this->objFunc = $this->create('MODViajeroInterno');
            if ($this->objParam->insertar('id_viajero_interno')) {
                $this->res = $this->objFunc->insertarViajeroInterno($this->objParam);
            } else {
                $this->res = $this->objFunc->modificarViajeroInterno($this->objParam);
            }
        }
        $this->res->imprimirRespuesta($this->res->generarJson());
	}
						
	function eliminarViajeroInterno(){
			$this->objFunc=$this->create('MODViajeroInterno');	
		$this->res=$this->objFunc->eliminarViajeroInterno($this->objParam);
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
			
}

?>