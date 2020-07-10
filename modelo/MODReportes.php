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

        //recuperamos los registros a migrar de la bd
        $datos = $this->respuesta;

        $this->respuesta = new Mensaje();

        $fuente = $this->objParam->getParametro('tipo_reporte');;
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

        $conexion = new ConexionSqlServer('172.17.58.22', 'SPConnection', 'Passw0rd', 'DBStage');
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

            while ($row = mssql_fetch_array($query, MSSQL_ASSOC)){

                $record = json_decode(json_encode($row));
                $punto_index = array_search($row['Iatacode'], $this->array_column($datos->datos, 'codigo'));
                $record->NameOffice = $datos->datos[$punto_index]["nombre"];
                $record->NamePlace = $datos->datos[$punto_index]["lugar"];
                $data[] = $record;
            }

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


        //variables para la conexion sql server.
        $bandera_conex = '';
        $conn = '';
        $param_conex = array();
        $conexion = '';
        $this->respuesta = new Mensaje();

        if ($conn != '') {
            $conexion->closeSQL();
        }

        $conexion = new ConexionSqlServer('172.17.58.22', 'SPConnection', 'Passw0rd', 'DBStage');
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
            }

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
}
?>