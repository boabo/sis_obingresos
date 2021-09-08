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

        $objMod = $this->create('MODCalculoOverComison');
        $lista_agencia = $objMod->listarAgenciaExcluida($this->objParam)->datos;//var_dump('$lista_agencia',$lista_agencia);exit;

        $record = $res->Data;
        $res->Data = array();

        if($action == 0) {

            foreach ($record as $data) {
                $data->habilitado = 'true';
                foreach ($lista_agencia as $agencia){
                   if ($data->AcmKey == $agencia['id_acm_key']){
                       $data->habilitado = 'false';
                   }
                }
                $data->status = 'elaborado';
                $res->Data [] = $data;
            }
        }else if($action == 1){

            foreach ($record as $data) {
                $data->habilitado = 'true';
                foreach ($lista_agencia as $agencia){
                    if ($data->AcmKey == $agencia['id_acm_key']){
                        $data->habilitado = 'false';
                    }
                }
                $data->status = 'validado';
                $res->Data [] = $data;
            }
        }else if($action == 2){

            foreach ($record as $data) {
                $data->habilitado = 'true';
                foreach ($lista_agencia as $agencia){
                    if ($data->AcmKey == $agencia['id_acm_key']){
                        $data->habilitado = 'false';
                    }
                }
                $data->status = 'generado';
                $res->Data [] = $data;
            }
        }else if($action == 3){

            foreach ($record as $data) {
                $data->habilitado = 'true';
                foreach ($lista_agencia as $agencia){
                    if ($data->AcmKey == $agencia['id_acm_key']){
                        $data->habilitado = 'false';
                    }
                }
                $data->status = 'abonado';
                $res->Data [] = $data;
            }
        }else if($action == 4){

            foreach ($record as $data) {
                $data->habilitado = 'true';
                foreach ($lista_agencia as $agencia){
                    if ($data->AcmKey == $agencia['id_acm_key']){
                        $data->habilitado = 'false';
                    }
                }
                $data->status = 'enviado';
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

    function array_column($array, $columnKey, $indexKey = null) {
        $result = array();
        foreach ($array as $subArray) {
            if (is_null($indexKey) && array_key_exists($columnKey, $subArray)) {
                $result[] = is_object($subArray)?$subArray->$columnKey: $subArray[$columnKey];
            } elseif (array_key_exists($indexKey, $subArray)) {
                if (is_null($columnKey)) {
                    $index = is_object($subArray)?$subArray->$indexKey: $subArray[$indexKey];
                    $result[$index] = $subArray;
                } elseif (array_key_exists($columnKey, $subArray)) {
                    $index = is_object($subArray)?$subArray->$indexKey: $subArray[$indexKey];
                    $result[$index] = is_object($subArray)?$subArray->$columnKey: $subArray[$columnKey];
                }
            }
        }
        return $result;
    }

    function generarMovimientoEntidad(){

        $from =  implode('',array_reverse(explode('/',$this->objParam->getParametro('fecha_desde'))));;
        $to =  implode('',array_reverse(explode('/',$this->objParam->getParametro('fecha_hasta'))));
        $type =  $this->objParam->getParametro('tipo');
        $action =  $this->objParam->getParametro('momento');

        $accion = $this->objParam->getParametro('accion');

        /*************************************************************** Service Generar ACMs ***************************************************************/
        if ( $accion == 'generar' ) {
            $data = array(
            "from" => $from,
            "to" => $to,
            "typePOS" => $type
            );
            //var_dump('$from',$from,'$to',$to,'$type',$type);exit;
            $json_data = json_encode($data);
            $s = curl_init();
            curl_setopt($s, CURLOPT_URL, 'http://sms.obairlines.bo/CommissionServices/ServiceComision.svc/AcmNumberAssign');
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
            $res = json_decode($res->AcmNumberAssignResult); //var_dump('$res', $res->Data[0]->Result);exit;
        }
        /*************************************************************** Service Generar ACMs ***************************************************************/

        /*******************************************************Procedimiento Validar, Abonar ACMs *******************************************************/

        $data = array(
            "from" => $from,
            "to" => $to,
            "type" => $type,
            "documentNumber" => "TODOS",
            "action" => $action ? $action : 0
        );

        if ( $accion == 'generar' && ( $type == 'IATA' || $type == 'NO-IATA' ) ) {
            if ( $res->Data[0]->Result == 1 ) {
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

                if ($action == 1) {
                    if (!empty($res->Data)) {
                        $this->objParam->addParametro('dataJson', json_encode($res->Data));
                        $this->objFunc = $this->create('MODCalculoOverComison');
                        $this->res = $this->objFunc->generarCreditoNoIata($this->objParam);
                    }
                }
            }else{
                $this->res = new Mensaje();
                $this->res->setMensaje(
                    'EXITO',
                    'driver.php',
                    'Acm Number Assign',
                    'Service Acm Number Assign',
                    'control',
                    'obingresos.ft_calculo_over_comison_ime',
                    'OBING_NUM_ASSIGN_IME',
                    'IME'
                );
                $this->res->datos = $res->Data;
            }
        }else{
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
            if ($action == 1) {
                if (!empty($res->Data)) {
                    $this->objParam->addParametro('dataJson', json_encode($res->Data));
                    $this->objFunc = $this->create('MODCalculoOverComison');
                    $this->res = $this->objFunc->generarCreditoNoIata($this->objParam);
                }
            }
        }
        /*******************************************************Procedimiento Validar, Abonar ACMs *******************************************************/
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


        $momento =  $this->objParam->getParametro('momento');
        $from =  implode('',array_reverse(explode('/',$this->objParam->getParametro('fecha_desde'))));;
        $to =  implode('',array_reverse(explode('/',$this->objParam->getParametro('fecha_hasta'))));
        //var_dump('$from',$from, '$to',$to);exit;
        //var_dump($from, $to, $momento);exit;

        $data = array(
            "from" => $from,
            "to" => $to
        );

        $json_data = json_encode($data);

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


        /***************************************** llamada para cambiar el estado a enviar *****************************************/
        if ($momento == 1) {
            if (!empty($res->Data)) {
                $this->objParam->addParametro('dataJson', json_encode($res->Data));
                $this->objFunc = $this->create('MODCalculoOverComison');
                $this->res = $this->objFunc->generarCreditoNoIata($this->objParam);
            }
        }
        /***************************************** llamada para cambiar el estado a enviar *****************************************/


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
