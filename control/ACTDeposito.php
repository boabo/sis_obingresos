<?php
/**
 *@package pXP
 *@file gen-ACTDeposito.php
 *@author  (jrivera)
 *@date 06-01-2016 22:42:28
 *@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
 */
require_once(dirname(__FILE__).'/../reportes/RReporteDepositoOgone.php');
require_once(dirname(__FILE__).'/../reportes/RReporteDepositoBancaInternet.php');
include_once(dirname(__FILE__).'/../../lib/lib_general/ExcelInput.php');
include(dirname(__FILE__).'/../../lib/rest/NetOBRestClient.php');
class ACTDeposito extends ACTbase{

    function listarDeposito(){
        $this->objParam->defecto('ordenacion','id_deposito');

        $this->objParam->defecto('dir_ordenacion','desc');

        if ($this->objParam->getParametro('id_agencia') != '') {
            $this->objParam->addFiltro("dep.id_agencia = ". $this->objParam->getParametro('id_agencia'));
        }
        /****************************Listar depositos************************************/

          if ($this->objParam->getParametro('tipo') != '') {
              $this->objParam->addFiltro("dep.tipo = ''". $this->objParam->getParametro('tipo')."''");
          }

        /********************************************************************************/
        if ($this->objParam->getParametro('id_deposito') != '') {
            $this->objParam->addFiltro("dep.id_deposito = ". $this->objParam->getParametro('id_deposito'));
        }

        if ($this->objParam->getParametro('estado') != '') {
            $this->objParam->addFiltro("dep.estado = ''". $this->objParam->getParametro('estado')."''");
        }
        if($this->objParam->getParametro('id_apertura_cierre_caja') != '') {
            //filto ventas
            $this->objParam->addFiltro(" (dep.id_apertura_cierre_caja = " . $this->objParam->getParametro('id_apertura_cierre_caja')." or aper.id_apertura_cierre_caja =" . $this->objParam->getParametro('id_apertura_cierre_caja').")");
        }
        if($this->objParam->getParametro('bancos')!=''){
          if($this->objParam->getParametro('bancos')!='TODOS'){
                $this->objParam->addFiltro("dep.agt in (SELECT UNNEST(REGEXP_SPLIT_TO_ARRAY(''".$this->objParam->getParametro('bancos')."'', '','')))");
              }
              else{
                        $this->objParam->addFiltro("dep.agt not in (''".$this->objParam->getParametro('bancos')."'')");
                }

        }

        if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
            $this->objReporte = new Reporte($this->objParam,$this);
            $this->res = $this->objReporte->generarReporteListado('MODDeposito','listarDeposito');
        } else{
            $this->objFunc=$this->create('MODDeposito');
            if ($this->objParam->getParametro('tipo') == 'agencia') {
                $this->res=$this->objFunc->listarDeposito($this->objParam);
                $temp = Array();
                $temp['total_deposito'] = $this->res->extraData['total_deposito'];
                $temp['tipo_reg'] = 'summary';
                $temp['id_deposito'] = 0;

                $this->res->total++;
                $this->res->addLastRecDatos($temp);

            }else{
                $this->res=$this->objFunc->listarDeposito($this->objParam);

            }
        }
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function listarDepositoAgrupado(){

        $this->objParam->defecto('ordenacion','id_deposito');
        $this->objParam->defecto('dir_ordenacion','desc');

        /****************************Listar depositos************************************/
        if ($this->objParam->getParametro('pes_estado') == 'pendiente') {

            $this->objParam->addFiltro("(dep.diferencia_ml <>  0 and  dep.diferencia_me <> 0
                                          OR
                                          dep.diferencia_ml <> 0   and  dep.diferencia_me  = 0
                                          OR
                                          dep.diferencia_ml =  0  and  dep.diferencia_me <> 0
                                          OR
                                          dep.diferencia_ml is null and dep.diferencia_me = 0
                                          OR
                                          dep.diferencia_ml = 0 and dep.diferencia_me is null )");
        }else{
            $this->objParam->addFiltro("(dep.diferencia_ml =  0  and  dep.diferencia_me = 0)");
        }
        /********************************************************************************/

        if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
            $this->objReporte = new Reporte($this->objParam,$this);
            $this->res = $this->objReporte->generarReporteListado('MODDeposito','listarDeposito');
        } else{
            $this->objFunc=$this->create('MODDeposito');

                $this->res=$this->objFunc->listarDepositoAgrupado($this->objParam);

            }

        $this->res->imprimirRespuesta($this->res->generarJson());
    }



    function insertarDeposito(){


        $this->objFunc=$this->create('MODDeposito');
        if($this->objParam->insertar('id_deposito')){
            $this->res=$this->objFunc->insertarDeposito($this->objParam);
        } else{
            $this->res=$this->objFunc->modificarDeposito($this->objParam);
        }
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function eliminarDeposito(){
        $this->objFunc=$this->create('MODDeposito');
        $this->res=$this->objFunc->eliminarDeposito($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function sincronizarDeposito(){
        $this->objFunc=$this->create('MODDeposito');
        $this->res=$this->objFunc->sincronizarDeposito($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function eliminarDepositoPortal(){
        $this->objFunc=$this->create('MODDeposito');
        $this->res=$this->objFunc->eliminarDeposito($this->objParam);

        if ($this->res->getTipo() == 'EXITO') {
            $datos = $this->res->getDatos();

            $rest_uri = str_replace('http://','',$_SESSION['_OBNET_REST_URI']);
            $rest_uri = str_replace('https://','',$rest_uri);


            $netOBRestClient = NetOBRestClient::connect($_SESSION['_OBNET_REST_URI'], '');
            $netOBRestClient->setHeaders(array('Content-Type: json;'));
            $arreglo_parametros = $this->objParam->getParametro(0);

            $res = $netOBRestClient->doPost('ModificarEstadoDeposito',
                array(
                    "idDepositoERP"=> $datos['id_deposito'],
                    "estadoDeposito"=> 'eliminado',
                    "observacion"=>$arreglo_parametros['observaciones']
                ));
            $bdlog=new MODLogError('LOG_TRANSACCION','Respuesta servicio ModificarEstadoDeposito','respuesta: '.$res);
            $bdlog->guardarLogError();

        }

        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function cambiaEstadoDeposito(){
        $this->objFunc=$this->create('MODDeposito');
        $this->res=$this->objFunc->cambiaEstadoDeposito($this->objParam);

        if ($this->res->getTipo() == 'EXITO') {
            $datos = $this->res->getDatos();

            $rest_uri = str_replace('http://','',$_SESSION['_OBNET_REST_URI']);
            $rest_uri = str_replace('https://','',$rest_uri);


            $netOBRestClient = NetOBRestClient::connect($_SESSION['_OBNET_REST_URI'], '');
            $netOBRestClient->setHeaders(array('Content-Type: json;'));



            $res = $netOBRestClient->doPost('ModificarEstadoDeposito',
                array(
                    "idDepositoERP"=> $datos['id_deposito'],
                    "estadoDeposito"=> 'validado'
                ));

            $bdlog=new MODLogError('LOG_TRANSACCION','Respuesta servicio ModificarEstadoDeposito','respuesta: '.$res);
            $bdlog->guardarLogError();

        }
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function subirCSVDeposito(){
        $arregloFiles = $this->objParam->getArregloFiles();
        $ext = pathinfo($arregloFiles['archivo']['name']);
        $extension = $ext['extension'];
        $error = 'no';
        $mensaje_completo = '';
        if ($this->objParam->getPArametro('tipo') == 'ogone') {
            //Valida extencio

            //Validar errores del archivo
            if (isset($arregloFiles['archivo']) && is_uploaded_file($arregloFiles['archivo']['tmp_name'])) {
                if ($extension != 'csv' && $extension != 'CSV') {
                    $mensaje_completo = "La extensión del archivo debe ser CSV";
                    $error = 'error_fatal';
                }
                $upload_dir = "/tmp/";
                $file_path = $upload_dir . $arregloFiles['archivo']['name'];
                if (!move_uploaded_file($arregloFiles['archivo']['tmp_name'], $file_path)) {
                    $mensaje_completo = "Error al guardar el archivo csv en disco";
                    $error = 'error_fatal';
                }
            } else {
                $mensaje_completo = "No se subio el archivo";
                $error = 'error_fatal';
            }
            //armar respuesta en error fatal
            if ($error == 'error_fatal') {
                $this->mensajeRes = new Mensaje();
                $this->mensajeRes->setMensaje('ERROR', 'ACTDeposito.php', $mensaje_completo, $mensaje_completo, 'control');
                //si no es error fatal proceso el archivo
            } else {
                $lines = file($file_path);
                foreach ($lines as $line_num => $line) {
                    $line = str_replace("'", "", $line);
                    $arr_temp = explode('|', $line);

                    $this->objParam->addParametro('nro_deposito', $arr_temp[0]);
                    $this->objParam->addParametro('pnr', $arr_temp[1]);
                    $this->objParam->addParametro('descripcion', $arr_temp[23]);
                    $arr_temp[12] = str_replace(',', '.', $arr_temp[12]);
                    $this->objParam->addParametro('monto_deposito', $arr_temp[12]);
                    $this->objParam->addParametro('moneda', $arr_temp[13]);
                    $this->objParam->addParametro('estado', $arr_temp[4]);
                    $this->objParam->addParametro('fecha', $arr_temp[2]);
                    $this->objParam->addParametro('observaciones', $arr_temp[16]);
                    $this->objFunc = $this->create('MODDeposito');
                    $this->res = $this->objFunc->subirDatos($this->objParam); // cambiar


                    if ($this->res->getTipo() == 'ERROR') {
                        $error = 'error';
                        $mensaje_completo .= $this->res->getMensaje() . " \n";
                    }

                }

            }
            //armar respuesta en caso de exito o error en algunas tuplas
            if ($error == 'error') {
                $this->mensajeRes = new Mensaje();
                $this->mensajeRes->setMensaje('ERROR', 'ACTDeposito.php', 'Ocurrieron los siguientes errores : ' . $mensaje_completo,
                    $mensaje_completo, 'control');
            } else if ($error == 'no') {
                $this->mensajeRes = new Mensaje();
                $this->mensajeRes->setMensaje('EXITO', 'ACTDeposito.php', 'El archivo fue ejecutado con éxito',
                    'El archivo fue ejecutado con éxito', 'control');
            }
        } else if ($this->objParam->getPArametro('tipo') == 'worldpay') {
            if(isset($arregloFiles['archivo']) && is_uploaded_file($arregloFiles['archivo']['tmp_name'])) {
                if (!in_array($extension, array('xls', 'xlsx', 'XLS', 'XLSX'))) {
                    $mensaje_completo = "La extensión del archivo debe ser XLS o XLSX";
                    $error = 'error_fatal';
                } else {
                    //procesa Archivo
                    $archivoExcel = new ExcelInput($arregloFiles['archivo']['tmp_name'], 'EXTWP');
                    $archivoExcel->recuperarColumnasExcel();
                    $arrayArchivo = $archivoExcel->leerColumnasArchivoExcel();
                    foreach ($arrayArchivo as $fila) {

                        $this->objParam->addParametro('order_code', $fila['order_code']);
                        $this->objParam->addParametro('fecha', $fila['fecha']);
                        $this->objParam->addParametro('hora', $fila['hora']);
                        $this->objParam->addParametro('metodo_pago', $fila['metodo_pago']);
                        $this->objParam->addParametro('estado', $fila['estado']);
                        $this->objParam->addParametro('tarjeta', $fila['tarjeta']);
                        $this->objParam->addParametro('moneda', $fila['moneda']);
                        $this->objParam->addParametro('monto', $fila['monto']);

                        $this->objFunc = $this->create('MODDeposito');
                        $this->res = $this->objFunc->subirDatosWP($this->objParam);

                        if ($this->res->getTipo() == 'ERROR') {
                            $error = 'error';
                            $mensaje_completo = "Error al guardar el fila en tabla " . $this->res->getMensajeTec();
                            break;
                        }
                    }
                }
            } else {
                $mensaje_completo = "No se subio el archivo";
                $error = 'error_fatal';
            }
        }

        if ($error == 'error_fatal') {
            $this->mensajeRes=new Mensaje();
            $this->mensajeRes->setMensaje('ERROR','ACTDeposito.php',$mensaje_completo,
                $mensaje_completo,'control');
            //si no es error fatal proceso el archivo
        }

        if ($error == 'error') {
            $this->mensajeRes=new Mensaje();
            $this->mensajeRes->setMensaje('ERROR','ACTDeposito.php','Ocurrieron los siguientes errores : ' . $mensaje_completo,
                $mensaje_completo,'control');

        } else if ($error == 'no') {
            $this->mensajeRes=new Mensaje();
            $this->mensajeRes->setMensaje('EXITO','ACTDeposito.php','El archivo fue ejecutado con éxito',
                'El archivo fue ejecutado con éxito','control');
        }

        //devolver respuesta
        $this->mensajeRes->imprimirRespuesta($this->mensajeRes->generarJson());
    }

    function reporteDepositoBancaInternet(){

        $this->objFunc = $this->create('MODDeposito');
        $this->res = $this->objFunc->reporteDepositoBancaInternet($this->objParam);
        //var_dump( $this->res);exit;
        //obtener titulo de reporte
        $titulo = 'Reporte Depositos';
        //Genera el nombre del archivo (aleatorio + titulo)
        $nombreArchivo = uniqid(md5(session_id()) . $titulo);

        $nombreArchivo .= '.xls';
        $this->objParam->addParametro('nombre_archivo', $nombreArchivo);
        $this->objParam->addParametro('datos_deposito', $this->res->datos);
        $this->objFunc = $this->create('MODDeposito');
        $this->res = $this->objFunc->reporteDepositoBancaInternetArchivo($this->objParam);
        $this->objParam->addParametro('datos_archivo', $this->res->datos);
        //Instancia la clase de excel
        $this->objReporteFormato = new RReporteDepositoBancaInternet($this->objParam);
        $this->objReporteFormato->imprimeCabecera();
        $this->objReporteFormato->generarDatos();

        $this->objReporteFormato->generarReporte();

        $this->mensajeExito = new Mensaje();
        $this->mensajeExito->setMensaje('EXITO', 'Reporte.php', 'Reporte generado','Se generó con éxito el reporte: ' . $nombreArchivo, 'control');
        $this->mensajeExito->setArchivoGenerado($nombreArchivo);
        $this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());

    }

    function reporteDeposito(){

        $this->objFunc = $this->create('MODDeposito');
        $this->res = $this->objFunc->listarDepositoReporte($this->objParam);
        //var_dump( $this->res);exit;
        //obtener titulo de reporte
        $titulo = 'Reporte Depositos';
        //Genera el nombre del archivo (aleatorio + titulo)
        $nombreArchivo = uniqid(md5(session_id()) . $titulo);

        $nombreArchivo .= '.xls';
        $this->objParam->addParametro('nombre_archivo', $nombreArchivo);
        $this->objParam->addParametro('datos', $this->res->datos);
        //Instancia la clase de excel
        $this->objReporteFormato = new RReporteDepositoOgone($this->objParam);
        if ($this->objParam->getParametro('por') == 'boleto') {
            $this->objReporteFormato->generarDatos();
        } else {
            $this->objReporteFormato->generarDatosDeposito();
        }
        $this->objReporteFormato->generarReporte();

        $this->mensajeExito = new Mensaje();
        $this->mensajeExito->setMensaje('EXITO', 'Reporte.php', 'Reporte generado','Se generó con éxito el reporte: ' . $nombreArchivo, 'control');
        $this->mensajeExito->setArchivoGenerado($nombreArchivo);
        $this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());

    }

    function subirArchivoDeposito(){
        //validar que todo este bien parametrizado

        if (!isset($_SESSION['_FTP_INGRESOS_SERV']) || !isset($_SESSION['_FTP_INGRESOS_USR']) ||!isset($_SESSION['_FTP_INGRESOS_PASS']) ||
            !isset($_SESSION['_LOCAL_REST_USR']) ||!isset($_SESSION['_LOCAL_REST_PASS']) ) {
            throw new Exception("No existe parametrizacion completa para conexion FTP o REST", 3);
        }

        //Crear conexion rest
        $folder = ltrim($_SESSION["_FOLDER"], '/');
        $pxpRestClient = PxpRestClient::connect($_SERVER['HTTP_HOST'], $folder . 'pxp/lib/rest/')
            ->setCredentialsPxp($_SESSION['_LOCAL_REST_USR'],$_SESSION['_LOCAL_REST_PASS']);

        //copiar archivo ftp al tmp

        $conn_id = ftp_connect($_SESSION['_FTP_INGRESOS_SERV']);



        // iniciar sesión con nombre de usuario y contraseña
        $login_result = ftp_login($conn_id, $_SESSION['_FTP_INGRESOS_USR'], $_SESSION['_FTP_INGRESOS_PASS']);


        ftp_pasv($conn_id,true);

        $local_file = "/tmp/" . $this->objParam->getParametro('nombre_archivo');
        //$server_file = "FTPContratos#ND/" . $this->objParam->getParametro('nombre_archivo');
        $server_file = "docAgencias/" . $this->objParam->getParametro('nombre_archivo');


        // intenta descargar $server_file y guardarlo en $local_file
        if (ftp_get($conn_id, $local_file, $server_file, FTP_BINARY)) {

        } else {
            throw new Exception("No se ha podido leer el archivo", 3);
        }

        // cerrar la conexión ftp
        ftp_close($conn_id);

        //

        $out = $pxpRestClient->doPost('parametros/Archivo/subirArchivo',
            array(
                "tabla"=>"obingresos.tdeposito",
                "codigo_tipo_archivo"=>"ESCANDEP",
                "nombre_descriptivo"=>"",
                "id_tabla"=>$this->objParam->getParametro('id_deposito'),
                "archivo" => '@' . $local_file . ';filename='.$this->objParam->getParametro('nombre_archivo')

            ));
        $obj_out = json_decode($out);
        $obj_out->ROOT->datos->url = $_SERVER['HTTP_HOST'] . $_SESSION["_FOLDER"] . str_replace('./../../../','',$obj_out->ROOT->datos->url);
        $out = json_encode($obj_out);
        echo $out;
        exit;

    }
    function eliminar(){
        $this->objFunc=$this->create('MODDeposito');
        $this->res=$this->objFunc->eliminar($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }
}

?>
