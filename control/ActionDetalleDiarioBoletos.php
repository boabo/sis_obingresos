<?php
/***
Nombre: Intermediario.php
Proposito: Invocar al disparador de alarmas saltandose los pasos de
 *          autentificacion
 * 		    este archivo se  invoca desde un cron tab en servidor linux
 *          solo deberia llamarse desde ahí, otras llamadas no seran autorizadas
Autor:	Kplian (RAC)
Fecha:	19/07/2010
 */


include_once(dirname(__FILE__).'/../../lib/rest/PxpRestClient.php');
include(dirname(__FILE__).'/../../lib/DatosGenerales.php');


//Generamos el documento con REST
$pxpRestClient = PxpRestClient::connect('127.0.0.1',substr($_SESSION["_FOLDER"], 1) .'pxp/lib/rest/')
            ->setCredentialsPxp($_GET['user'],$_GET['pw']);

$res = $pxpRestClient->doPost('obingresos/Boleto/detalleDiarioBoletosWeb',
    array(	"prueba"=>'prueba'));

echo $res;

exit;

?>
