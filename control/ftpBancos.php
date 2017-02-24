<?php
include_once(dirname(__FILE__).'/../../lib/rest/PxpRestClient.php');
include(dirname(__FILE__).'/../../lib/DatosGenerales.php');

include_once(dirname(__FILE__).'/../../lib/lib_general/ExcelInput.php');

//Generamos el documento con REST
$pxpRestClient = PxpRestClient::connect('127.0.0.1',substr($_SESSION["_FOLDER"], 1) .'pxp/lib/rest/')
    ->setCredentialsPxp("admin","123");








//concetamos el ftp

// definir algunas variables

$file_a_buscar = '20170201 SPP_BCO BOB.xlsx';
$folder_ftp = 'SkyBiz/';

$local_file = '/var/www/html/kerp_capacitacion/';
$server_file = 'SkyBiz/20170201 SPP_BCO BOB.xlsx';

// establecer una conexión básica
$conn_id = ftp_connect("172.17.45.4");

// iniciar sesión con nombre de usuario y contraseña
$login_result = ftp_login($conn_id,"Skybizr", "xdbskybizr");

$contents_on_server = ftp_nlist($conn_id, $folder_ftp); //Returns an array of filenames from the specified directory on success or FALSE on error.



$date = new DateTime();

$date->modify('-2 day');

$hoy = $date->format('Ymd');
echo $hoy;
$arra_bancos = array( "ECO",
                        //"ECOU",
                        //"BUNU",
                        "BNB",
                        //"BNBU",
                        "BUN",
                        "BIS",
                        //"BISU",
                        "BME",
                       // "BMEU",
                        "TMY",
                        //"TMYU",
                        "ECF",
                       // "ECFU",
                        "BEC",
                        //"BECU",
                        "BCR",
                        //"BCRU",
                        "BCO",
                        //"BCOU"
                        );

$arra_excel_registrados = array();

foreach ($arra_bancos as $valor) {


    $monedas = array("BOB","USD");

    foreach ($monedas as $moneda) {
        $cadena_archivo_a_descargar = $hoy." "."SPP_".$valor." ".$moneda.".xlsx";




        if (in_array("SkyBiz/".$cadena_archivo_a_descargar, $contents_on_server))
        {
            echo "<br>";
            echo $cadena_archivo_a_descargar." fue encontrado";

            // intenta descargar $server_file y guardarlo en $local_file
            if (ftp_get($conn_id, $local_file.$cadena_archivo_a_descargar,"SkyBiz/".$cadena_archivo_a_descargar , FTP_BINARY)) {
                echo "Se ha guardado satisfactoriamente en $local_file\n";

                $arra_excel_registrados[] = array(
                    "nombre_archivo" => $cadena_archivo_a_descargar,
                    "subido" => "si",
                    "comentario" => "",
                    "moneda" => $moneda
                );

            } else {
                $arra_excel_registrados[] = array(
                    "nombre_archivo" => $cadena_archivo_a_descargar,
                    "subido" => "si",
                    "comentario" => "problema al descargarlo",
                    "moneda" => $moneda
                );
                echo "Ha habido un problema\n";
            }


        }
        else
        {
            $arra_excel_registrados[] = array(
                "nombre_archivo" => $cadena_archivo_a_descargar,
                "subido" => "no",
                "comentario" => "",
                "moneda" => $moneda
            );

            echo "<br>";
            echo $cadena_archivo_a_descargar." no fue encontrado : ";
        };
    }

}


$arra_json = json_encode($arra_excel_registrados);



$res = $pxpRestClient->doPost('obingresos/SkybizArchivo/insertarSkybizArchivoJson',
    array(	"arra_json"=>$arra_json,"fecha"=>$date->format('Y/m/d')));

$resp_root = json_decode($res);

if($resp_root->ROOT->error == false){


    foreach ($arra_excel_registrados as $registrados){
        $arra_excel_detalle = array();
        if($registrados["subido"] == "si"){

            $archivoExcel = new ExcelInput($local_file.$registrados["nombre_archivo"], "SKYBIZR");
            $archivoExcel->recuperarColumnasExcel();
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



        }
    }

}












// cerrar la conexión ftp
ftp_close($conn_id);

?>