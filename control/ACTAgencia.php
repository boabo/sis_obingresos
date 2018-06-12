<?php
/**
 *@package pXP
 *@file gen-ACTAgencia.php
 *@author  (jrivera)
 *@date 06-01-2016 21:30:12
 *@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
 */

class ACTAgencia extends ACTbase{

    function listarAgencia(){
        $this->objParam->defecto('ordenacion','id_agencia');

        $this->objParam->defecto('dir_ordenacion','asc');

        if($this->objParam->getParametro('tipo_agencia') != ''){
            $this->objParam->addFiltro(" age.tipo_agencia = ''".$this->objParam->getParametro('tipo_agencia')."''");
            //$this->objParam->addFiltro(" age.id_lugar in (select id_lugar from param.tlugar where codigo=''".$this->objParam->getParametro('lugar')."'')");
        }

        if($this->objParam->getParametro('vista') == 'corporativo'){
            $this->objParam->addFiltro(" age.tipo_agencia in(''corporativa'', ''noiata'') ");

        }

        if($this->objParam->getParametro('comision') == 'si'){
            $this->objParam->addFiltro(" age.tipo_agencia in(''noiata'') and age.boaagt  in ( ''A'')");

        }

        if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
            $this->objReporte = new Reporte($this->objParam,$this);
            $this->res = $this->objReporte->generarReporteListado('MODAgencia','listarAgencia');
        } else{
            $this->objFunc=$this->create('MODAgencia');

            $this->res=$this->objFunc->listarAgencia($this->objParam);
        }
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function getDocumentosContrato(){

        $this->objFunc=$this->create('MODAgencia');
        $this->res=$this->objFunc->getDocumentosContrato($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function finalizarContratoPortal(){

        $this->objFunc=$this->create('MODAgencia');
        $this->res=$this->objFunc->finalizarContratoPortal($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function insertarAgencia(){
        $this->objFunc=$this->create('MODAgencia');
        if($this->objParam->insertar('id_agencia')){
            $this->res=$this->objFunc->insertarAgencia($this->objParam);
        } else{
            $this->res=$this->objFunc->modificarAgencia($this->objParam);
        }
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function insertarAgenciaPortal(){
        $this->objFunc=$this->create('MODAgencia');

        $this->res=$this->objFunc->insertarAgenciaPortal($this->objParam);

        //insertar documentos

        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function insertarBoletaAgencia(){
        $this->objFunc=$this->create('MODAgencia');

        $this->res=$this->objFunc->insertarBoletaAgencia($this->objParam);

        //insertar documentos

        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function verificarSaldo(){
        $this->objFunc=$this->create('MODAgencia');

        $this->res=$this->objFunc->verificarSaldo($this->objParam);

        //insertar documentos

        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function getSaldoAgencia(){
        $this->objFunc=$this->create('MODAgencia');

        $this->res=$this->objFunc->getSaldoAgencia($this->objParam);

        //insertar documentos

        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function insertarDepositoAgencia(){
        $this->objFunc=$this->create('MODAgencia');

        $this->res=$this->objFunc->insertarDepositoAgencia($this->objParam);

        //insertar documentos

        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function insertarComisionAgencia(){
        $this->objFunc=$this->create('MODAgencia');

        $this->res=$this->objFunc->insertarComisionAgencia($this->objParam);

        //insertar documentos

        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function insertarContratoPortal(){
        $this->objFunc=$this->create('MODAgencia');

        $this->res=$this->objFunc->insertarContratoPortal($this->objParam);

        //insertar documentos

        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function eliminarAgencia(){
        $this->objFunc=$this->create('MODAgencia');
        $this->res=$this->objFunc->eliminarAgencia($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function subirArchivoContrato(){
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

        //$login_result = ftp_login($conn_id,'usuarioftp', 'Passw0rd');

        ftp_pasv($conn_id,true);

        $local_file = "/tmp/" . $this->objParam->getParametro('nombre_archivo');
        $server_file = "FTPContratos/" . $this->objParam->getParametro('nombre_archivo');

        // intenta descargar $server_file y guardarlo en $local_file
        if (ftp_get($conn_id, $local_file, $server_file, FTP_BINARY)) {
            //    echo "Se ha guardado satisfactoriamente en $local_file\n";
        } else {
            throw new Exception("No se ha podido leer el archivo", 3);
        }

        // cerrar la conexión ftp
        ftp_close($conn_id);

        // var_dump(basename($local_file,$this->objParam->getParametro('nombre_archivo')));exit;
        echo $pxpRestClient->doPost('workflow/DocumentoWf/subirArchivoWf',
            array(
                "id_documento_wf"=>$this->objParam->getParametro('id_documento_wf'),
                "num_tramite"=>"",
                "archivo" => "@". $local_file. ";filename=".$this->objParam->getParametro('nombre_archivo')
            ));



    }
}

?>
