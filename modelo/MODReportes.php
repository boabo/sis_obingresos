<?php
/**
 *@package  BoA
 *@file     MODReportes.php
 *@author  franklin.espinoza
 *@date     11-04-2020 15:14:58
 *@description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
 */


//include_once(dirname(__FILE__).'/../../lib/lib_modelo/ConexionErpMSql.php');
include_once(dirname(__FILE__).'/../../lib/lib_modelo/ConexionSqlServer.php');
class MODReportes extends MODbase{

    function __construct(CTParametro $pParam){
        parent::__construct($pParam);
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

    function generarCruceTarjetasBoletos(){

        /*$this->procedimiento='obingresos.ft_reportes_sel';
        $this->transaccion='OBING_PUVE_CT_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion

        //Definicion de la lista del resultado del query
        $this->captura('id_punto_venta','int4');
        $this->captura('estado_reg','varchar');
        $this->captura('id_sucursal','int4');
        $this->captura('nombre','varchar');
        $this->captura('descripcion','text');
        $this->captura('id_usuario_reg','int4');
        $this->captura('fecha_reg','timestamp');
        $this->captura('id_usuario_ai','int4');
        $this->captura('usuario_ai','varchar');
        $this->captura('id_usuario_mod','int4');
        $this->captura('fecha_mod','timestamp');
        $this->captura('usr_reg','varchar');
        $this->captura('usr_mod','varchar');
        $this->captura('codigo','varchar');
        $this->captura('habilitar_comisiones','varchar');
        $this->captura('formato_comprobante','varchar');
        $this->captura('tipo','varchar');
        $this->captura('office_id','varchar');
        $this->captura('lugar','varchar');

        //Ejecuta la instruccion
        $this->armarConsulta(); //echo $this->consulta; exit;
        $this->ejecutarConsulta();*/

        //recuperamos los registros a migrar de la bd
        //$datos = $this->respuesta;//var_dump('datos',$datos->datos);exit;

        $this->respuesta = new Mensaje();

        $fuente = $this->objParam->getParametro('tipo_reporte');
        $office_id = $this->objParam->getParametro('id_punto_venta');

        /*$fecha_desde = $this->objParam->getParametro('fecha_desde');
        $fecha_desde = date_format(date_create($fecha_desde), 'Ydm');*/
        $fecha_desde = implode('',array_reverse(explode('/',$this->objParam->getParametro('fecha_desde'))));

        /*$fecha_hasta = $this->objParam->getParametro('fecha_hasta');
        $fecha_hasta =  date_format(date_create($fecha_hasta), 'Ydm');*/
        $fecha_hasta = implode('',array_reverse(explode('/',$this->objParam->getParametro('fecha_hasta'))));

        //variables para la conexion sql server.
        $bandera_conex='';
        $conn = '';
        $ids_fallas = '';
        $ids_exitos = '';
        $param_conex = array();
        $conexion = '';

        if ($conn != '') {
            $conexion->closeSQL();
        }

        $conexion = new ConexionSqlServer('172.17.110.6', 'SPConnection', 'Passw0rd', 'DBStage');//172.17.58.22
        $conn = $conexion->conectarSQL();


        if($conn=='connect') {
            $error = 'connect';
            throw new Exception("connect: La conexión a la bd SQL Server ".$param_conex[1]." ha fallado.");
        }else if($conn=='select_db'){
            $error = 'select_db';
            throw new Exception("select_db: La seleccion de la bd SQL Server ".$param_conex[1]." ha fallado.");
        }else {

            //$query = @mssql_query("exec DBStage.dbo.spa_GetCruceTarjetas '$fecha_desde','$fecha_hasta','$office_id','$fuente';", $conn);
            $query = @mssql_query("exec DBStage.dbo.spa_GetAtcLinkserInformation '$fecha_desde','$fecha_hasta','$office_id','$fuente';", $conn);
            //$query = @mssql_query(utf8_decode('select * from AuxBSPVersion'), $conn);

            $data = array();

            $cone = new conexion();
            $link = $cone->conectarpdo();
            /** Office ID Lugar **/
            $sql = "select tpv.nombre, tpv.office_id, tpv.codigo, tlug.nombre as lugar
                    from vef.tpunto_venta tpv 
                    inner join vef.tsucursal tsu on tsu.id_sucursal = tpv.id_sucursal
                    inner join param.tlugar tlug on tlug.id_lugar = tsu.id_lugar
                    where tpv.estado_reg = 'activo' /*and tpv.office_id is not null*/
                    order by lugar
                    ";

            $consulta = $link->query($sql);
            $consulta->execute();
            $office_ids = $consulta->fetchAll(PDO::FETCH_ASSOC);
            /** Office ID Lugar **/

            /** Establecimiento Punto Venta**/
            $sql = "SELECT 	tep.codigo_estable, tep.nombre_estable, tep.tipo_estable
                    FROM obingresos.testablecimiento_punto_venta tep
                    ";

            $consulta = $link->query($sql);
            $consulta->execute();
            $list = $consulta->fetchAll(PDO::FETCH_ASSOC);

            /*var_dump('$office_ids', $office_ids);
            var_dump('$list', $list);exit;*/
            /** Establecimiento Punto Venta**/
            while ($row = mssql_fetch_array($query, MSSQL_ASSOC)){

                $record = json_decode(json_encode($row));

                if($row['Iatacode']!=null) {
                    /*$punto_index = array_search($row['Iatacode'], $this->array_column($datos->datos, 'codigo'));
                    $record->NameOffice = $datos->datos[$punto_index]["nombre"];
                    $record->NamePlace = $datos->datos[$punto_index]["lugar"];*/
                    $punto_index = array_search($row['Iatacode'], $this->array_column($office_ids, 'codigo'));
                    $record->NameOffice = $office_ids[$punto_index]["nombre"];
                    $record->NamePlace = $office_ids[$punto_index]["lugar"];
                }

                if($row['EstablishmentCode']!=null) {
                    $estable_index = array_search(ltrim($row['EstablishmentCode'], '0'), $this->array_column($list, 'codigo_estable'));
                    $record->NameEstable = $list[$estable_index]["nombre_estable"];
                    $record->TypeEstable = $list[$estable_index]["tipo_estable"];
                }

                $data[] = $record;
            }
            //var_dump('$data',$data);exit;
            $this->respuesta->datos = $data;
            mssql_free_result($query);
            $conexion->closeSQL();
        }

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function generarCruceTigoBoletos(){

        $this->procedimiento='obingresos.ft_reportes_sel';
        $this->transaccion='OBING_DEPO_TIGO_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion

        $this->setParametro('fecha_desde','fecha_desde','date');
        $this->setParametro('fecha_hasta','fecha_hasta','date');

        //Definicion de la lista del resultado del query
        $this->captura('fecha_venta','date');
        $this->captura('monto_total','numeric');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //recuperamos los registros a migrar de la bd
        $depositos = $this->respuesta->datos;

        $depo_date = array();
        foreach ($depositos as $key => $dep){
            $d_key = DateTime::createFromFormat('Y-m-d', $dep['fecha_venta'])->format('dmY');
            $depo_date[$d_key] += $dep['monto_total'];

        }
        $office_id = $this->objParam->getParametro('id_punto_venta');
        $fecha_desde = implode('',array_reverse(explode('/',$this->objParam->getParametro('fecha_desde'))));
        $fecha_hasta = implode('',array_reverse(explode('/',$this->objParam->getParametro('fecha_hasta'))));
        //var_dump('fechas',$fecha_desde, $fecha_hasta, $office_id);exit;

        //variables para la conexion sql server.
        $bandera_conex = '';
        $conn = '';
        $param_conex = array();
        $conexion = '';
        $this->respuesta = new Mensaje();

        if ($conn != '') {
            $conexion->closeSQL();
        }

        $conexion = new ConexionSqlServer('172.17.110.6', 'SPConnection', 'Passw0rd', 'DBStage');//172.17.58.22
        $conn = $conexion->conectarSQL();


        if ($conn == 'connect') {
            $error = 'connect';
            throw new Exception("connect: La conexión a la bd SQL Server " . $param_conex[1] . " ha fallado.");
        } else if ($conn == 'select_db') {
            $error = 'select_db';
            throw new Exception("select_db: La seleccion de la bd SQL Server " . $param_conex[1] . " ha fallado.");
        } else {
            $query = @mssql_query("exec DBStage.dbo.spa_GetTigoInformation '$fecha_desde','$fecha_hasta','$office_id';", $conn);

            $data = array();
            while ($row = mssql_fetch_array($query, MSSQL_ASSOC)) {
                $record = json_decode(json_encode($row));
                $data[] = $record;
            }//var_dump('$data', $data);exit;

            $this->respuesta->datos = $data;
            $this->respuesta->depositos = $depo_date;
            mssql_free_result($query);
            $conexion->closeSQL();
        }
        //Devuelve la respuesta
        return $this->respuesta;
    }

    function listarAgencias(){
        $this->procedimiento='obingresos.ft_reportes_sel';
        $this->transaccion='OBING_PUVE_CT_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion

        //Definicion de la lista del resultado del query
        $this->captura('id_punto_venta','int4');
        $this->captura('estado_reg','varchar');
        $this->captura('id_sucursal','int4');
        $this->captura('nombre','varchar');
        $this->captura('descripcion','text');
        $this->captura('id_usuario_reg','int4');
        $this->captura('fecha_reg','timestamp');
        $this->captura('id_usuario_ai','int4');
        $this->captura('usuario_ai','varchar');
        $this->captura('id_usuario_mod','int4');
        $this->captura('fecha_mod','timestamp');
        $this->captura('usr_reg','varchar');
        $this->captura('usr_mod','varchar');
        $this->captura('codigo','varchar');
        $this->captura('habilitar_comisiones','varchar');
        $this->captura('formato_comprobante','varchar');
        $this->captura('tipo','varchar');
        $this->captura('office_id','varchar');
        $this->captura('lugar','varchar');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        return $this->respuesta;
    }
    /**{developer:franklin.espinoza, date:22/12/2020, description: Reporte Calculo A7}**/
    function generarReporteCalculoA7(){


        $tipo_rep = $this->objParam->getParametro('tipo_rep');
        $fecha_desde = implode('',array_reverse(explode('/',$this->objParam->getParametro('fecha_desde'))));
        $fecha_hasta = implode('',array_reverse(explode('/',$this->objParam->getParametro('fecha_hasta'))));
        //var_dump('A', $tipo_rep, $fecha_desde,$fecha_hasta );exit;
        //variables para la conexion sql server.
        $bandera_conex = '';
        $conn = '';
        $param_conex = array();
        $conexion = '';

        if ($conn != '') {
            $conexion->closeSQL();
        }

        $conexion = new ConexionSqlServer('172.17.110.6', 'SPConnection', 'Passw0rd', 'DBStage');//172.17.58.22
        $conn = $conexion->conectarSQL();


        if ($conn == 'connect') {
            $error = 'connect';
            throw new Exception("connect: La conexión a la bd SQL Server " . $param_conex[1] . " ha fallado.");
        } else if ($conn == 'select_db') {
            $error = 'select_db';
            throw new Exception("select_db: La seleccion de la bd SQL Server " . $param_conex[1] . " ha fallado.");
        } else {
            if($tipo_rep == 'normal'){
                $query = @mssql_query("exec Sabsa.Get_Datos_A7 '$fecha_desde','$fecha_hasta';", $conn);
            }else {
                $query = @mssql_query("exec Sabsa.Get_Datos_A7_Noexiste '$fecha_desde','$fecha_hasta';", $conn);
            }

            $data = array();
            while ($row = mssql_fetch_array($query, MSSQL_ASSOC)) {
                $record = json_decode(json_encode($row));
                $data[] = $record;
            }
            mssql_free_result($query);
            $conexion->closeSQL();
        } //var_dump('$data',$data);exit;

        $this->procedimiento='obingresos.ft_reportes_sel';
        $this->transaccion='OBING_CALCULO_A7_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion
        $this->setCount(false);
        $this->arreglo['dataA7'] = json_encode(array('dataA7'=>$data));

        $this->setParametro('fecha_desde','fecha_desde','date');
        $this->setParametro('fecha_hasta','fecha_hasta','date');
        $this->setParametro('dataA7','dataA7','jsonb');
        $this->setParametro('tipo_rep','tipo_rep','varchar');

        $this->captura('id_vuelo','integer');
        $this->captura('vuelo_id','integer');
        $this->captura('fecha_vuelo','date');
        $this->captura('nro_vuelo','varchar');
        $this->captura('ruta_vl','varchar');
        $this->captura('nro_pax_boa','varchar');
        $this->captura('importe_boa','numeric');
        $this->captura('nro_pax_sabsa','varchar');
        $this->captura('importe_sabsa','numeric');
        $this->captura('diferencia','numeric');
        $this->captura('total_nac','integer');
        $this->captura('total_inter','integer');
        $this->captura('total_cero','integer');
        $this->captura('matricula_boa','varchar');
        $this->captura('matricula_sabsa','varchar');
        $this->captura('ruta_sabsa','varchar');


        //Ejecuta la instruccion
        $this->armarConsulta(); //var_dump('consulta',$this->consulta);exit;
        $this->ejecutarConsulta();

        return $this->respuesta;
    }
    /**{developer:franklin.espinoza, date:22/12/2020, description: Reporte Calculo A7}**/

    /**{developer:franklin.espinoza, date:22/12/2020, description: Detalle Vuelo Calculo A7}**/
    function detalleVueloCalculoA7(){
        $this->procedimiento='obingresos.ft_reportes_sel';
        $this->transaccion='OBING_DETALLE_A7_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion

        $this->setParametro('detalle_vuelo','detalle_vuelo','jsonb');

        $this->setCount(false);
        //Definicion de la lista del resultado del query
        $this->captura('id_detalle','int4');
        $this->captura('ato_origen','varchar');
        $this->captura('ruta_completa','varchar');
        $this->captura('nombre_pasajero','varchar');
        $this->captura('nro_vuelo','varchar');
        $this->captura('nro_asiento','varchar');
        $this->captura('fecha_vuelo','date');
        $this->captura('pnr','varchar');
        $this->captura('nro_boleto','varchar');
        $this->captura('hora_vuelo','varchar');
        $this->captura('estado_vuelo','varchar');

        $this->captura('valor_a7','numeric');
        $this->captura('calculo_a7','numeric');
        $this->captura('pax_id','varchar');
        $this->captura('std_date','varchar');
        //Ejecuta la instruccion
        $this->armarConsulta();//var_dump('consulta',$this->consulta);exit;
        $this->ejecutarConsulta();

        return $this->respuesta;
    }
    /**{developer:franklin.espinoza, date:22/12/2020, description: Detalle Vuelo Calculo A7}**/


    /**{developer:franklin.espinoza, date:22/12/2020, description: Detalle Pasajero Calculo A7}**/
    function detallePasajeroCalculoA7(){
        $this->procedimiento='obingresos.ft_reportes_sel';
        $this->transaccion='OBING_DET_PAX_A7_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion

        $this->setParametro('detalle_pasajero','detalle_pasajero','jsonb');

        $this->setCount(false);
        //Definicion de la lista del resultado del query
        $this->captura('id_pasajero','int4');
        $this->captura('passenger_id','varchar');
        $this->captura('is_current','varchar');
        $this->captura('posicion','varchar');
        $this->captura('fecha_salida','varchar');
        $this->captura('fecha_salida_show','date');

        $this->captura('origen','varchar');
        $this->captura('destino','varchar');
        $this->captura('ticket','varchar');
        $this->captura('std','varchar');

        $this->captura('std_show','varchar');
        $this->captura('sta','varchar');
        $this->captura('sta_show','varchar');
        $this->captura('here_a7','varchar');
        $this->captura('is_sabsa','varchar');
        //Ejecuta la instruccion
        $this->armarConsulta();//var_dump('consulta',$this->consulta);exit;
        $this->ejecutarConsulta();

        return $this->respuesta;
    }
    /**{developer:franklin.espinoza, date:22/12/2020, description: Detalle Vuelo Calculo A7}**/

    /**{developer:franklin.espinoza, date:12/03/2021, description: Detalle Pagos realizados por la Administradora}**/
    function getDetallePagosAdministradora(){


        $tipo_administrador = $this->objParam->getParametro('tipo_administrador');
        $fecha_desde = implode('',array_reverse(explode('/',$this->objParam->getParametro('fecha_desde'))));
        $fecha_hasta = implode('',array_reverse(explode('/',$this->objParam->getParametro('fecha_hasta'))));

        //variables para la conexion sql server.
        $conn = '';
        $param_conex = array();
        $conexion = '';
        $this->respuesta = new Mensaje();

        if ($conn != '') {
            $conexion->closeSQL();
        }

        $conexion = new ConexionSqlServer('172.17.110.6', 'SPConnection', 'Passw0rd', 'DBStage');//172.17.58.22
        $conn = $conexion->conectarSQL();


        if($conn=='connect') {
            $error = 'connect';
            throw new Exception("connect: La conexión a la bd SQL Server ".$param_conex[1]." ha fallado.");
        }else if($conn=='select_db'){
            $error = 'select_db';
            throw new Exception("select_db: La seleccion de la bd SQL Server ".$param_conex[1]." ha fallado.");
        }else {
            var_dump('ANTES', $conn);exit;
            //$query = @mssql_query("exec DBStage.dbo.spa_GetCruceTarjetas '$fecha_desde','$fecha_hasta','$office_id','$fuente';", $conn);
            $query = @mssql_query("exec DBStage.dbo.spa_getAtcLinkserLoadedData '2560','$fecha_desde','$fecha_hasta','$tipo_administrador';", $conn);

            var_dump('DESPUES');
            //$query = @mssql_query(utf8_decode('select * from AuxBSPVersion'), $conn);

            $data = array();
            //$row = mssql_fetch_array($query, MSSQL_ASSOC);
            //var_dump('$row', $row);exit;
            while ( $row = mssql_fetch_array($query, MSSQL_ASSOC) ){
                var_dump('entra fila', $row);exit;
                $record = json_decode(json_encode($row));
                $data[] = $record;
            }
            var_dump('$data',$data);exit;
            $this->respuesta->datos = $data;
            mssql_free_result($query);
            $conexion->closeSQL();
        } var_dump('sale',$this->respuesta);exit;

        //Devuelve la respuesta
        return $this->respuesta;
    }
    /**{developer:franklin.espinoza, date:12/03/2021, description: Detalle Pagos realizados por la Administradora}**/


    /**{developer:franklin.espinoza, date:22/12/2020, description: Reporte Calculo A7}**/
    function getTicketInformationRecursive(){
        $nro_ticket = $this->objParam->getParametro('nro_ticket');

        $this->respuesta = new Mensaje();
        $conexion = new ConexionSqlServer('172.17.110.6', 'SPConnection', 'Passw0rd', 'DBStage');
        $conn = $conexion->conectarSQL();

        $query_string = "Select DBStage.dbo.fn_getTicketInformation('$nro_ticket') "; // boleto miami 9303852215072

        @mssql_query('SET CONCAT_NULL_YIELDS_NULL ON');
        @mssql_query('SET ANSI_WARNINGS ON');
        @mssql_query('SET ANSI_PADDING ON');

        $query = @mssql_query($query_string, $conn);
        $row = mssql_fetch_array($query, MSSQL_ASSOC);

        $data_json_string = $row['computed'];
        $data_json = json_decode(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $data_json_string), true);
        //var_dump('$data_json', $data_json);exit;
        mssql_free_result($query);
        $conexion->closeSQL();
        $temp = array();
        $temp['datajson'] = $data_json;
        //$this->respuesta->datos = $temp;

        //$this->respuesta->setDatos(array('data_json' => $data_json));
        $this->respuesta->addLastRecDatos($temp);
        //$this->respuesta->setMensaje('EXITO', 'MODReportes.php', 'Request Success', 'Se pudo encontrar el ticket solicitado desde AMADEUS.', 'modelo');
        
        return $this->respuesta;
    }
    /**{developer:franklin.espinoza, date:22/12/2020, description: Reporte Calculo A7}**/

    function getDetalleConciliacionAdministradora(){

        $fuente = $this->objParam->getParametro('tipo_reporte');
        $office_id = 'todos';

        $fecha_desde = implode('',array_reverse(explode('/',$this->objParam->getParametro('fecha_desde'))));
        $fecha_hasta = implode('',array_reverse(explode('/',$this->objParam->getParametro('fecha_hasta'))));



        //variables para la conexion sql server.
        $bandera_conex='';
        $conn = '';
        $ids_fallas = '';
        $ids_exitos = '';
        $param_conex = array();
        $conexion = '';

        if ($conn != '') {
            $conexion->closeSQL();
        }

        $conexion = new ConexionSqlServer('172.17.110.6', 'SPConnection', 'Passw0rd', 'DBStage');//172.17.58.22
        $conn = $conexion->conectarSQL();


        if($conn=='connect') {
            $error = 'connect';
            throw new Exception("connect: La conexión a la bd SQL Server ".$param_conex[1]." ha fallado.");
        }else if($conn=='select_db'){
            $error = 'select_db';
            throw new Exception("select_db: La seleccion de la bd SQL Server ".$param_conex[1]." ha fallado.");
        }else {

            $query = @mssql_query("exec DBStage.dbo.spa_GetAtcLinkserInformation '$fecha_desde','$fecha_hasta','$office_id','$fuente';", $conn);

            //$row = mssql_fetch_array($query, MSSQL_ASSOC);
            //var_dump('$row', $row);
            $data = array();
            while ($row = mssql_fetch_array($query, MSSQL_ASSOC)){
                $record = json_decode(json_encode($row));
                $data[] = $record;
            }


            mssql_free_result($query);
            $conexion->closeSQL();
        }

        $rec_conciliacion = array();
        $switch = true;
        $rec_adm = null;
        $rec_boa = array();//var_dump('$data', $data);exit;
        foreach ($data as $key => $rec){

            if ( $rec->ResultType == 'pago_administradora' ){
                $temp_adm = array(
                    'tipo'=>$rec->ResultType, 'admin'=>$rec->Formato,'pago_cod'=>$rec->PaymentKey,'establecimiento'=>ltrim($rec->EstablishmentCode,'0'),
                    'terminal'=>$rec->TerminalNumber,'lote'=>$rec->LotNumber,'pago_ticket'=>$rec->PaymentTicket,
                    'fecha_pago'=>DateTime::createFromFormat('M j Y g:i:s:a', $rec->PaymentDate)->format('d/m/Y'),
                    'hora_pago'=>$rec->PaymentHour,'moneda_adm'=>$rec->Currency,'monto_pago'=>$rec->PaymentAmmount,'auth_codigo_adm'=>$rec->AuthorizationCode,
                    'num_tarjeta_adm'=>$rec->CreditCardNumber
                    );
                $rec_conciliacion [] = array ('tipo'=>$rec->ResultType,'administradora'=>$temp_adm);
            } else if ( $rec->ResultType == 'pago_ret' ){

                $temp_bo = array(
                    'tipo'=>$rec->ResultType, 'admin'=>$rec->Formato,'numero_documento'=>$rec->TicketNumber, 'codigo_iata'=>$rec->Iatacode,
                    'oficina_venta'=>$rec->OIDIssue,'oficina_reserva'=>$rec->OIDReservation,'monto_venta'=>$rec->PaymentAmount,
                    'auth_codigo_boa'=>$rec->AuthorizationCodeFP,'num_tarjeta_boa'=>$rec->AccountCardNumber,
                    'fecha_venta'=>DateTime::createFromFormat('M j Y g:i:s:a', $rec->IssueDate)->format('d/m/Y'),
                    'moneda_boa'=>$rec->PaymentCurrency,'mp'=>$rec->mp,'IP'=>$rec->IP
                );
                $rec_conciliacion [] = array ('tipo'=>$rec->ResultType,'boa'=>$temp_bo);

            } else if ( $rec->ResultType == 'pago_both' ){
                if( $rec->AuthorizationCode != $data[$key+1]->AuthorizationCode ){
                    if($switch) {
                        $temp_adm = array(
                            'tipo' => $rec->ResultType, 'admin' => $rec->Formato, 'pago_cod' => $rec->PaymentKey, 'establecimiento' => ltrim($rec->EstablishmentCode,'0'),
                            'terminal' => $rec->TerminalNumber, 'lote' => $rec->LotNumber, 'pago_ticket' => $rec->PaymentTicket,
                            'fecha_pago' => DateTime::createFromFormat('M j Y g:i:s:a', $rec->PaymentDate)->format('d/m/Y'),
                            'hora_pago' => $rec->PaymentHour, 'moneda_adm' => $rec->Currency, 'monto_pago' => $rec->PaymentAmmount, 'auth_codigo_adm' => $rec->AuthorizationCode,
                            'num_tarjeta_adm' => $rec->CreditCardNumber
                        );

                        $temp_boa = array(
                            'tipo'=>$rec->ResultType, 'admin'=>$rec->Formato,'numero_documento' => $rec->TicketNumber, 'codigo_iata' => $rec->Iatacode,
                            'oficina_venta' => $rec->OIDIssue,'oficina_reserva' => $rec->OIDReservation, 'monto_venta' => $rec->PaymentAmount,
                            'auth_codigo_boa' => $rec->AuthorizationCodeFP, 'num_tarjeta_boa' => $rec->AccountCardNumber,
                            'fecha_venta' => DateTime::createFromFormat('M j Y g:i:s:a', $rec->IssueDate)->format('d/m/Y'),
                            'moneda_boa' => $rec->PaymentCurrency, 'mp' => $rec->mp, 'IP' => $rec->IP
                        );
                        $rec_conciliacion [] = array('tipo'=>$rec->ResultType, 'administradora' => $temp_adm, 'boa' => $temp_boa);
                    }else{
                        $temp_boa = array(
                            'tipo'=>$rec->ResultType, 'admin'=>$rec->Formato,'numero_documento'=>$rec->TicketNumber, 'codigo_iata'=>$rec->Iatacode,
                            'oficina_venta'=>$rec->OIDIssue,'oficina_reserva'=>$rec->OIDReservation,'monto_venta'=>$rec->PaymentAmount,
                            'auth_codigo_boa'=>$rec->AuthorizationCodeFP,'num_tarjeta_boa'=>$rec->AccountCardNumber,
                            'fecha_venta'=>DateTime::createFromFormat('M j Y g:i:s:a', $rec->IssueDate)->format('d/m/Y'),
                            'moneda_boa'=>$rec->PaymentCurrency,'mp'=>$rec->mp,'IP'=>$rec->IP
                        );
                        $rec_boa [] = $temp_boa;

                        $rec_conciliacion [] = array('tipo'=>$rec->ResultType, 'administradora' => $rec_adm, 'boa' => $rec_boa);
                    }
                    $rec_adm = null;
                    $rec_boa = array();
                    $switch = true;
                }else{
                    if($switch){
                        $temp_adm = array(
                            'tipo'=>$rec->ResultType, 'admin'=>$rec->Formato,'pago_cod'=>$rec->PaymentKey,'establecimiento'=>ltrim($rec->EstablishmentCode,'0'),
                            'terminal'=>$rec->TerminalNumber,'lote'=>$rec->LotNumber,'pago_ticket'=>$rec->PaymentTicket,
                            'fecha_pago'=>DateTime::createFromFormat('M j Y g:i:s:a', $rec->PaymentDate)->format('d/m/Y'),
                            'hora_pago'=>$rec->PaymentHour,'moneda_adm'=>$rec->Currency,'monto_pago'=>$rec->PaymentAmmount,'auth_codigo_adm'=>$rec->AuthorizationCode,
                            'num_tarjeta_adm'=>$rec->CreditCardNumber
                        );

                        $temp_boa = array(
                            'tipo'=>$rec->ResultType, 'admin'=>$rec->Formato,'numero_documento' => $rec->TicketNumber, 'codigo_iata' => $rec->Iatacode,
                            'oficina_venta' => $rec->OIDIssue,'oficina_reserva' => $rec->OIDReservation, 'monto_venta' => $rec->PaymentAmount,
                            'auth_codigo_boa' => $rec->AuthorizationCodeFP, 'num_tarjeta_boa' => $rec->AccountCardNumber,
                            'fecha_venta' => DateTime::createFromFormat('M j Y g:i:s:a', $rec->IssueDate)->format('d/m/Y'),
                            'moneda_boa' => $rec->PaymentCurrency, 'mp' => $rec->mp, 'IP' => $rec->IP
                        );

                        $rec_adm = $temp_adm;
                        $rec_boa [] = $temp_boa;
                        $switch = false;
                    }else{
                        $temp_boa = array(
                            'tipo'=>$rec->ResultType, 'admin'=>$rec->Formato,'numero_documento'=>$rec->TicketNumber, 'codigo_iata'=>$rec->Iatacode,
                            'oficina_venta'=>$rec->OIDIssue,'oficina_reserva'=>$rec->OIDReservation,'monto_venta'=>$rec->PaymentAmount,
                            'auth_codigo_boa'=>$rec->AuthorizationCodeFP,'num_tarjeta_boa'=>$rec->AccountCardNumber,
                            'fecha_venta'=>DateTime::createFromFormat('M j Y g:i:s:a', $rec->IssueDate)->format('d/m/Y'),
                            'moneda_boa'=>$rec->PaymentCurrency,'mp'=>$rec->mp,'IP'=>$rec->IP
                        );
                        $rec_boa [] = $temp_boa;
                        $switch = false;
                    }
                }
            }
        }

        //var_dump('$rec_conciliacion',$rec_conciliacion);exit;

        $this->procedimiento='obingresos.ft_conciliacion_sel';
        $this->transaccion='OBING_CONCILIACION';
        $this->tipo_procedimiento='SEL';//tipo de transaccion

        $this->setCount(false);

        $this->arreglo['dataStage'] = json_encode(array('dataStage'=>$rec_conciliacion));

        $this->setParametro('fecha_desde','fecha_desde','date');
        $this->setParametro('fecha_hasta','fecha_hasta','date');
        $this->setParametro('dataStage','dataStage','jsonb');
        $this->setParametro('tipo_reporte','tipo_reporte','varchar');
        $this->setParametro('tipo_rep','tipo_rep','varchar');

        //Definicion de la lista del resultado del query
        $this->captura('id_conciliacion','int4');
        $this->captura('dataPago','text');
        $this->captura('observaciones','varchar');
        $this->captura('id_relacion','varchar');
        $this->captura('tipo','varchar');


        //Ejecuta la instruccion
        $this->armarConsulta(); //echo $this->consulta; exit;
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

}
?>