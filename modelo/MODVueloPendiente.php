<?php
/**
 *@package  BoA
 *@file     MODVueloPendiente.php
 *@author  franklin.espinoza
 *@date     29-08-2022 15:14:58
 *@description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
 */

include_once(dirname(__FILE__).'/../../lib/lib_modelo/ConexionSqlServer.php');
class MODVueloPendiente extends MODbase{

    function __construct(CTParametro $pParam){
        parent::__construct($pParam);
    }

    /**{developer:franklin.espinoza, date:29/08/2022, description: Listar Vuelos Pendientes SICNO TRAFICO}**/
    function generarVueloPendiente(){

        $fecha_desde = implode('',array_reverse(explode('/',$this->objParam->getParametro('fecha_desde'))));
        $fecha_hasta = implode('',array_reverse(explode('/',$this->objParam->getParametro('fecha_hasta'))));
        $conn = '';
        $param_conex = array();
        $conexion = '';
        //var_dump('generarReporteCalculoA7', $fecha_desde, $fecha_hasta);exit;
        if ($conn != '') {
            $conexion->closeSQL();
        }

        $conexion = new ConexionSqlServer('172.17.110.8', 'userA7Sicno', 'u53r@751cn0', 'ControlOperacion');
        $conn = $conexion->conectarSQL();


        if ($conn == 'connect') {
            $error = 'connect';
            throw new Exception("connect: La conexión a la bd SQL Server " . $param_conex[1] . " ha fallado.");
        } else if ($conn == 'select_db') {
            $error = 'select_db';
            throw new Exception("select_db: La seleccion de la bd SQL Server " . $param_conex[1] . " ha fallado.");
        } else {

            $query = @mssql_query("exec dbo.Sp_A7GetVuelospendienteCierreSicnoTraf '$fecha_desde','$fecha_hasta';", $conn);


            $data = array();
            while ($row = mssql_fetch_array($query, MSSQL_ASSOC)) {
                //var_dump('$row',$row);
                $row['fechaSalidaReal'] = DateTime::createFromFormat('M j Y g:i:s:a', $row['fechaSalidaReal'])->format('d/m/Y');
                $record = json_decode(json_encode($row));
                $data[] = $record;
            }
            mssql_free_result($query);
            $conexion->closeSQL();
        } //var_dump('$data',$data);exit;

        $this->respuesta = new Mensaje();

        $this->respuesta->setMensaje(
            'EXITO',
            'driver.php',
            'Service Get Pending Flights, CALCULOA7',
            'Service Get Pending Flights, CALCULOA7',
            'modelo',
            'obingresos.ft_reportes_sel',
            'VEF_OVER_COM_SEL',
            'SEL'
        );

        $this->respuesta->datos = $data;

        return $this->respuesta;
    }
    /**{developer:franklin.espinoza, date:29/08/2022, description: Listar Vuelos Pendientes SICNO TRAFICO}**/
}
?>