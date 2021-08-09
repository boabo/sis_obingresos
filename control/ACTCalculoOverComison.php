<?php
/**
 *@package pXP
 *@file ACTCalculoOverComison.php
 *@author  (franklin.espinoza)
 *@date 20/05/2021 12:33:10
 *@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
 */

require_once(dirname(__FILE__).'/../reportes/RReporteCalculoOverNoIataXLS.php');
class ACTCalculoOverComison extends ACTbase{

    function generarCalculoOverComison(){


        //var_dump('$this->objParam',$this->objParam);exit;
        $from =  implode('',array_reverse(explode('/',$this->objParam->getParametro('fecha_desde'))));;
        $to =  implode('',array_reverse(explode('/',$this->objParam->getParametro('fecha_hasta'))));
        $type =  $this->objParam->getParametro('tipo');
        $action =  $this->objParam->getParametro('momento');
        //var_dump('$this->objParam',$action);exit;
        $data = array(
            "from" => $from,
            "to" => $to,
            "type" => $type,
            "documentNumber" => "TODOS",
            "action" => $action ? $action : 0
        );

        $json_data = json_encode($data);
        $s = curl_init();
        curl_setopt($s, CURLOPT_URL, 'http://sms.obairlines.bo/CommissionServices/ServiceComision.svc/GetListDocumentsACM');
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
        $res = json_decode($res->GetListDocumentsACMResult);

        /*if( $action == 1 ){

            if ($res->Data[0]->TypePOS == 'NO-IATA') {
                if ( !empty($res->Data) ) {
                    $this->objParam->addParametro('dataJson', json_encode($res->Data));
                    $this->objFunc = $this->create('MODCalculoOverComison');
                    $this->res = $this->objFunc->generarCreditoNoIata($this->objParam);
                }
            }else{
                $this->objParam->addParametro('dataJson', json_encode('[]'));
                $this->objFunc = $this->create('MODCalculoOverComison');
                $this->res = $this->objFunc->generarCreditoNoIata($this->objParam);
            }
        }*/

        $record = $res->Data;
        $res->Data = array();

        if($action == 0) {

            foreach ($record as $data) {
                $data->status = 'abonado';
                $res->Data [] = $data;
            }
        }else{

            foreach ($record as $data) {
                $data->status = 'elaborado';
                $res->Data [] = $data;
            }
        }

        /*if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
            $this->objReporte = new Reporte($this->objParam,$this);
            $this->res = $this->objReporte->generarReporteListado('MODCalculoOverComison','generarCalculoOverComison');
        } else{
            $this->objFunc=$this->create('MODCalculoOverComison');

            $this->res=$this->objFunc->generarCalculoOverComison($this->objParam);
        }*/

        //var_dump('$this->res', $this->res);exit;
        $this->res = new Mensaje();
        $this->res->setMensaje(
            'EXITO',
            'driver.php',
            'Get Data Documents ACM ',
            'Service Get List Documents ACM',
            'control',
            'obingresos.ft_reportes_sel',
            'VEF_OVER_COM_SEL',
            'SEL'
        );

        $this->res->setTotal(count($res->Data));
        //$this->res->setDatos($res->Data);
        $this->res->datos = $res->Data;

        ///var_dump('$this->res',$this->res);exit;
        //$this->mensaje->setDatos(array("listado"=>$res->GetListDocumentsACMResult));
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function generarMovimientoEntidad(){

        //var_dump('$this->objParam',$this->objParam);exit;
        $from =  implode('',array_reverse(explode('/',$this->objParam->getParametro('fecha_desde'))));;
        $to =  implode('',array_reverse(explode('/',$this->objParam->getParametro('fecha_hasta'))));
        $type =  $this->objParam->getParametro('tipo');
        $action =  $this->objParam->getParametro('momento');
        //var_dump('$this->objParam',$from,$to,$type,$action);exit;
        $data = array(
            "from" => $from,
            "to" => $to,
            "type" => $type,
            "documentNumber" => "TODOS",
            "action" => $action ? $action : 0
        );

        $json_data = json_encode($data);
        $s = curl_init();
        curl_setopt($s, CURLOPT_URL, 'http://sms.obairlines.bo/CommissionServices/ServiceComision.svc/GetListDocumentsACM');
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
        $res = json_decode($res->GetListDocumentsACMResult);

        //var_dump('$response', $res->Data[0]->TypePOS);exit;
        //var_dump('$action',$action);exit;
        if( $action == 1 ){
            //var_dump('$res->Data',empty($res->Data));exit;
            //var_dump('$res->Data',$res->Data[0]->TypePOS, empty($res->Data));exit;
            if ($res->Data[0]->TypePOS == 'NO-IATA') {
                if ( !empty($res->Data) ) {
                    $this->objParam->addParametro('dataJson', json_encode($res->Data));
                    $this->objFunc = $this->create('MODCalculoOverComison');
                    $this->res = $this->objFunc->generarCreditoNoIata($this->objParam);
                }
            }/*else{
                $this->objParam->addParametro('dataJson', json_encode('[]'));
                $this->objFunc = $this->create('MODCalculoOverComison');
                $this->res = $this->objFunc->generarCreditoNoIata($this->objParam);
            }*/
        }

        /*$record = $res->Data;
        $res->Data = array();

        if($action == 1) {

            foreach ($record as $data) {
                $data->status = 'abonado';
                $res->Data [] = $data;
            }
        }else{

            foreach ($record as $data) {
                $data->status = 'elaborado';
                $res->Data [] = $data;
            }
        }*/
        //var_dump('$res->Data',$res->Data);Exit;
        /*if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
            $this->objReporte = new Reporte($this->objParam,$this);
            $this->res = $this->objReporte->generarReporteListado('MODCalculoOverComison','generarCalculoOverComison');
        } else{
            $this->objFunc=$this->create('MODCalculoOverComison');

            $this->res=$this->objFunc->generarCalculoOverComison($this->objParam);
        }*/

        //var_dump('$this->res', $this->res);exit;
        /*$this->res = new Mensaje();
        $this->res->setMensaje(
            'EXITO',
            'driver.php',
            'Get Data Documents ACM ',
            'Service Get List Documents ACM',
            'control',
            'vef.ft_boleto_sel',
            'VEF_OVER_COM_SEL',
            'SEL'
        );

        $this->res->setTotal(count($res->Data));
        //$this->res->setDatos($res->Data);
        $this->res->datos = $res->Data;*/

        ///var_dump('$this->res',$this->res);exit;
        //$this->mensaje->setDatos(array("listado"=>$res->GetListDocumentsACMResult));
        $this->res->imprimirRespuesta($this->res->generarJson());

    }

    function detalleCalculoACM(){

        $from =  implode('',array_reverse(explode('/',$this->objParam->getParametro('from'))));;
        $to =  implode('',array_reverse(explode('/',$this->objParam->getParametro('to'))));
        $documentNumber =  $this->objParam->getParametro('documentNumber');
        $type =  $this->objParam->getParametro('type');

        //var_dump('$this->objParam',$this->objParam);exit;

        $data = array(
            "from" => $from,
            "to" => $to,
            "type" => $type,
            "documentNumber" => $documentNumber
        );

        $json_data = json_encode($data);
        $s = curl_init();
        curl_setopt($s, CURLOPT_URL, 'http://sms.obairlines.bo/CommissionServices/ServiceComision.svc/GetDetailsDocumentACM');
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
        $res = json_decode($res->GetDetailsDocumentACMResult);


        /*if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
            $this->objReporte = new Reporte($this->objParam,$this);
            $this->res = $this->objReporte->generarReporteListado('MODCalculoOverComison','generarCalculoOverComison');
        } else{
            $this->objFunc=$this->create('MODCalculoOverComison');

            $this->res=$this->objFunc->generarCalculoOverComison($this->objParam);
        }*/

        $this->res = new Mensaje();
        $this->res->setMensaje(
            'EXITO',
            'driver.php',
            'Get Details Data Documents ACM ',
            'Service Details Get Documents ACM',
            'control',
            'vef.ft_boleto_sel',
            'VEF_OVER_COM_SEL',
            'SEL'
        );
        $this->res->setTotal(count($res->Data));
        //$this->res->setDatos($res->Data);
        $this->res->datos = $res->Data;
        ///var_dump('$this->res',$this->res);exit;
        //$this->mensaje->setDatos(array("listado"=>$res->GetListDocumentsACMResult));
        $this->res->imprimirRespuesta($this->res->generarJson());

    }
    //detalleCalculoACM

    function verificarPeriodoGenerado() {
        $this->funciones = $this->create('MODCalculoOverComison');
        $this->res=$this->funciones->verificarPeriodoGenerado();
        //Se imprime el json del arbol
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    //(f.e.a) 01/06/2021 reporte excel Calculo Over Comison
    function reporteCalculoOverComison(){

        $from =  implode('',array_reverse(explode('/',$this->objParam->getParametro('fecha_ini'))));;
        $to =  implode('',array_reverse(explode('/',$this->objParam->getParametro('fecha_fin'))));
        $type =  $this->objParam->getParametro('tipo');
        $action =  $this->objParam->getParametro('momento');

        $data = array(
            "from" => $from,
            "to" => $to,
            "type" => $type,
            "documentNumber" => "TODOS",
            "action" => $action ? $action : 0
        );

        $json_data = json_encode($data);
        $s = curl_init();
        curl_setopt($s, CURLOPT_URL, 'http://sms.obairlines.bo/CommissionServices/ServiceComision.svc/GetListDocumentsACM');
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
        $res = json_decode($res->GetListDocumentsACMResult);

        $titulo_archivo = 'Calculo Over Comison';

        $nombreArchivo = uniqid(md5(session_id()).$titulo_archivo).'.xls';
        $this->objParam->addParametro('nombre_archivo',$nombreArchivo);
        $this->objParam->addParametro('titulo_archivo',$titulo_archivo);
        $this->objParam->addParametro('datos',$res->Data);
        $this->objParam->addParametro('fecha_desde',$from);
        $this->objParam->addParametro('fecha_hasta',$to);
        $this->objParam->addParametro('tipo',$type);

        $this->objReporte = new RReporteCalculoOverNoIataXLS($this->objParam);
        $this->objReporte->generarReporte();

        $this->mensajeExito=new Mensaje();
        $this->mensajeExito->setMensaje('EXITO','Reporte.php','Reporte generado','Se generó con éxito el reporte: '.$nombreArchivo,'control');
        $this->mensajeExito->setArchivoGenerado($nombreArchivo);
        $this->res = $this->mensajeExito;
        $this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());
    }

    //(f.e.a) 06/02/2021 reporte excel de otros ingresos por periodo finanzas
    function reporteFileBSP(){

        $from =  implode('',array_reverse(explode('/',$this->objParam->getParametro('fecha_ini'))));;
        $to =  implode('',array_reverse(explode('/',$this->objParam->getParametro('fecha_fin'))));

        //var_dump($from, $to);exit;
        $data = array(
            "from" => $from,
            "to" => $to
        );

        $json_data = json_encode($data);


        /*******************************************SECCION PARA EL BACKGROUND*******************************************/
        $NEW_LINE = "\r\n";

        ignore_user_abort(true);

        header('Connection: close' . $NEW_LINE);
        header('Content-Encoding: none' . $NEW_LINE);
        ob_start();

        $this->mensajeExito=new Mensaje();
        $this->mensajeExito->setMensaje('EXITO','ACTCalculoOverComison.php','Reporte generando','Se generara y enviara por correo el reporte, una vez concluido el proceso','control');
        $this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());

        $size = ob_get_length();
        header('Content-Length: ' . $size, TRUE);
        ob_end_flush();
        ob_flush();
        flush();
        session_write_close();
        /*******************************************SECCION PARA EL BACKGROUND*******************************************/

        $s = curl_init();
        curl_setopt($s, CURLOPT_URL, 'http://sms.obairlines.bo/CommissionServices/ServiceComision.svc/CreateRETBSPFile');
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
        $res = json_decode($res->CreateRETBSPFileResult);

        //var_dump('$res 2', $res    );exit;

        /*****************************************enviar alert al usuario*****************************************/
        $evento = "enviarMensajeUsuario";

        $data = array(
            "mensaje" => 'Estimado Funcionario, su Reporte ya ha sido generado y enviado a su cuena de correo:'.$res->Message,
            "tipo_mensaje" => 'alert',
            "titulo" => 'Alerta Reporte',
            "id_usuario" => $_SESSION["ss_id_usuario"],
            "destino" => 'Unico',
            "evento" => $evento,
            "url" => 'url_prueba'
        );

        $send = array(
            "tipo" => "enviarMensajeUsuario",
            "data" => $data
        );

        $usuarios_socket = $this->dispararEventoWS($send);

        $usuarios_socket =json_decode($usuarios_socket, true);
        /*****************************************enviar alert al usuario*****************************************/

        /*$this->mensajeExito=new Mensaje();
        $this->mensajeExito->setMensaje('EXITO','Reporte.php','Reporte generado','Se generó con éxito el reporte: ','control');
        $this->res = $this->mensajeExito;
        $this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());*/
    }

    function revertirMovimientoEntidad() {
        $this->funciones = $this->create('MODCalculoOverComison');
        $this->res=$this->funciones->revertirMovimientoEntidad();
        //Se imprime el json del arbol
        $this->res->imprimirRespuesta($this->res->generarJson());
    }


}
?>
