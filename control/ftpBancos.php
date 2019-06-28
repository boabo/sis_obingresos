<?php
include_once(dirname(__FILE__).'/../../lib/rest/PxpRestClient.php');
include(dirname(__FILE__).'/../../lib/DatosGenerales.php');

include_once(dirname(__FILE__).'/../../lib/lib_general/ExcelInput.php');

//Generamos el documento con REST
$pxpRestClient = PxpRestClient::connect('127.0.0.1',substr($_SESSION["_FOLDER"], 1) .'pxp/lib/rest/')
    ->setCredentialsPxp($_GET['user'],$_GET['pw']);


$datos_skybiz_archivo_bd = $pxpRestClient->doPost('obingresos/SkybizArchivo/listarSkybizArchivo',
    array(	"start"=>0,"limit"=>500000000,"sort"=>"id_skybiz_archivo","dir"=>"ASC"));

$resp_datos = json_decode($datos_skybiz_archivo_bd);



//si existe error al traer los archivos existentes que ya se registaron entonces exit
if($resp_datos->ROOT->error == true){
    echo "error al traer datos del erp";
    exit;
}





//concetamos el ftp

// definir algunas variables

$file_a_buscar = '20170201 SPP_BCO BOB.xlsx';
$folder_ftp = 'SkyBiz/';

//$local_file = '/var/www/html/kerp_capacitacion/';
//$local_file = '/var/www/html/kerp_capacitacion/uploaded_files/sis_obingresos/';
$local_file = dirname(__FILE__).'/../../uploaded_files/sis_obingresos/';

//$local_file = '/var/www/html/kerp_capacitacion/uploaded_files/sis_obingresos/';
$server_file = 'SkyBiz/20170201 SPP_BCO BOB.xlsx';

// establecer una conexión básica
//$conn_id = ftp_connect("172.17.45.4");
$conn_id = ftp_connect("ftp.boa.aero"); /**/
//$conn_id = ftp_connect("172.17.58.28");


 
// iniciar sesión con nombre de usuario y contraseña
$login_result = ftp_login($conn_id,"Skybizr", "xdbskybizr");
ftp_pasv($conn_id, true);
$contents_on_server = ftp_nlist($conn_id, $folder_ftp); //Returns an array of filenames from the specified directory on success or FALSE on error.


//obtenemos en un array los archivos que estan registrados concatenando la ruta de archivo para poder comparar con la lista del ftp
foreach ($resp_datos->datos as $datos){
    $arra_archivos_registrados[] = 'SkyBiz/'.$datos->nombre_archivo;
}

$arra_excel_registrados = array();

//recorremos la lista ftp y veremos si ya esta o no
foreach ($contents_on_server as $archivo_server) {

    $nombre_solo_archivo =explode("/", $archivo_server);
    $nombre_solo_archivo = $nombre_solo_archivo[1];

    if (in_array($archivo_server, $arra_archivos_registrados)) {
        //ya existe registrado el archivo
        echo "<br>";
        echo $archivo_server." ya fue registrado anteriormente";

        $arra_excel_registrados[] = array(
            "nombre_archivo" => $nombre_solo_archivo,
            "subido" => "no",
            "comentario" => "",
            "moneda" => ""
        );


    }else{
        //no existe registrado el archivo asi que se debe copiar y registrar a la bd
        echo "<br>";
        echo $archivo_server." recien encontrado ";




        // intenta descargar $server_file y guardarlo en $local_file

        if (ftp_get($conn_id, $local_file.$archivo_server,$archivo_server , FTP_BINARY)) {
            echo "Se ha guardado satisfactoriamente en $local_file\n";

            $arra_excel_registrados[] = array(
                "nombre_archivo" => $nombre_solo_archivo,
                "subido" => "si",
                "comentario" => "",
                "moneda" => ""
            );

        } else {
            $arra_excel_registrados[] = array(
                "nombre_archivo" => $nombre_solo_archivo,
                "subido" => "no",
                "comentario" => "problema al descargarlo",
                "moneda" => ""
            );
            echo "Ha habido un problema\n ";
            //print_r(error_get_last());
        }

    }
}






$arra_json = json_encode($arra_excel_registrados);



/*$res = $pxpRestClient->doPost('obingresos/SkybizArchivo/insertarSkybizArchivoJson',
    array(	"arra_json"=>$arra_json));

$resp_root = json_decode($res);*/




//if($resp_root->ROOT->error == false){


    foreach ($arra_excel_registrados as $registrados){



        $arra_excel_detalle = array();
        if($registrados["subido"] == "si"){





            $archivoExcel = new ExcelInput($local_file.'SkyBiz/'.$registrados["nombre_archivo"], "SKYBIZR");
            $res = $archivoExcel->recuperarColumnasExcel();


            $arrayArchivo = $archivoExcel->leerColumnasArchivoExcel();




            foreach ($arrayArchivo as $fila) {

                //echo $fila["authorization_"];
                if($fila["entity"] != 'TOTAL'){
                    $arra_excel_detalle[] = array(
                        "entity" => (string)$fila["entity"],
                        "ip" =>(string)$fila["ip"],
                        "request_date_time" =>(string)$fila["request_date_time"],
                        "issue_date_time" =>(string)$fila["issue_date_time"],
                        "pnr" =>(string)$fila["pnr"],
                        "identifier_pnr" =>(string)$fila["identifier_pnr"],
                        "authorization_" =>(string)$fila["authorization_"],
                        "total_amount" =>(string)$fila["total_amount"],
                        "currency" =>(string)$fila["currency"],
                        "status" =>(string)$fila["status"],
                        "nombre_archivo" =>(string)$registrados["nombre_archivo"]

                    );
                }





            }


            $json = json_encode($arra_excel_detalle);


            $res = $pxpRestClient->doPost('obingresos/SkybizArchivoDetalle/insertarSkybizArchivoDetalleJson',
                array(	"arra_json"=>$json,"nombre_archivo"=>$registrados["nombre_archivo"]));

            $resp_root = json_decode($res);




            echo '<br>';
            var_dump($resp_root->ROOT->error);



        }
    }

//}












// cerrar la conexión ftp
ftp_close($conn_id);

?>
