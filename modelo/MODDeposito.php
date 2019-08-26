<?php
/**
 *@package pXP
 *@file gen-MODDeposito.php
 *@author  (jrivera)
 *@date 06-01-2016 22:42:28
 *@description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
 */

class MODDeposito extends MODbase{

    var $cone;
    var $informix;
    var $tabla_deposito_informix;
    var $nro_deposito;

    function __construct(CTParametro $pParam){
        parent::__construct($pParam);
        $this->monedaBase();
        if ($this->monedaBase() == 'BOB') {
          $this->cone = new conexion();
          $this->informix = $this->cone->conectarPDOInformix();
          $this->link = $this->cone->conectarpdo(); //conexion a pxp(postgres)
        }

    }

    function listarDeposito(){
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento='obingresos.ft_deposito_sel';
        $this->transaccion='OBING_DEP_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion

        $this->capturaCount('total_deposito','numeric');

        //Definicion de la lista del resultado del query
        $this->captura('id_deposito','int4');
        $this->captura('estado_reg','varchar');
        $this->captura('nro_deposito','varchar');
        $this->captura('nro_deposito_boa','varchar');
        $this->captura('monto_deposito','numeric');
        $this->captura('id_moneda_deposito','int4');
        $this->captura('id_agencia','int4');
        $this->captura('fecha','date');
        $this->captura('saldo','numeric');
        $this->captura('id_usuario_reg','int4');
        $this->captura('fecha_reg','timestamp');
        $this->captura('id_usuario_ai','int4');
        $this->captura('usuario_ai','varchar');
        $this->captura('id_usuario_mod','int4');
        $this->captura('fecha_mod','timestamp');
        $this->captura('usr_reg','varchar');
        $this->captura('usr_mod','varchar');
        $this->captura('desc_moneda','varchar');
        $this->captura('agt','varchar');
        $this->captura('fecha_venta','date');
        $this->captura('monto_total','numeric');
        $this->captura('nombre_agencia','varchar');
        $this->captura('desc_periodo','text');
        $this->captura('estado','varchar');
        $this->captura('id_apertura_cierre_caja','int4');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function listarDepositoAgrupado(){
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento='obingresos.ft_deposito_sel';
        $this->transaccion='OBING_DEPAG_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion

        $this->setCount(false);
        //Definicion de la lista del resultado del query
        $this->captura('id_deposito','int4');
        $this->captura('estado_reg','varchar');
        $this->captura('nro_deposito','varchar');
        $this->captura('nro_deposito_boa','varchar');
        $this->captura('monto_deposito','numeric');
        $this->captura('id_moneda_deposito','int4');
        $this->captura('id_agencia','int4');
        $this->captura('fecha','date');
        $this->captura('saldo','numeric');
        $this->captura('id_usuario_reg','int4');
        $this->captura('fecha_reg','timestamp');
        $this->captura('id_usuario_ai','int4');
        $this->captura('usuario_ai','varchar');
        $this->captura('id_usuario_mod','int4');
        $this->captura('fecha_mod','timestamp');
        $this->captura('usr_reg','varchar');
        $this->captura('usr_mod','varchar');
        $this->captura('desc_moneda','varchar');
        $this->captura('agt','varchar');
        $this->captura('fecha_venta','date');
        $this->captura('monto_total','numeric');
        $this->captura('nombre_agencia','varchar');
        $this->captura('desc_periodo','text');
        $this->captura('estado','varchar');
        $this->captura('id_apertura_cierre_caja','int4');
        $this->captura('nro_cuenta','varchar');
        $this->captura('monto_total_ml','numeric');
        $this->captura('monto_total_me','numeric');
        $this->captura('diferencia_ml','numeric');
        $this->captura('diferencia_me','numeric');


        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function insertarDeposito(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='obingresos.ft_deposito_ime';
        $this->transaccion='OBING_DEP_INS';
        $this->tipo_procedimiento='IME';

        //Define los parametros para la funcion
        /*RECUPERAMOS EL ID_PUNTO_VENTA PARA CONSULTAR LA CUENTABANCARIA*/
        $this->setParametro('id_punto_venta','id_punto_venta','integer');
        $this->setParametro('id_usuario_cajero','id_usuario_cajero','integer');
        $this->setParametro('relacion_deposito','relacion_deposito','varchar');
        $this->setParametro('detalle','detalle','varchar');
        /*---------------------------------------------------------------*/
        $this->setParametro('estado_reg','estado_reg','varchar');
        $this->setParametro('nro_deposito','nro_deposito','varchar');
        //$this->setParametro('nro_deposito_boa','nro_deposito_boa','varchar');
        $this->setParametro('agt','agt','varchar');
        $this->setParametro('monto_deposito','monto_deposito','numeric');
        $this->setParametro('id_moneda_deposito','id_moneda_deposito','int4');
        $this->setParametro('id_agencia','id_agencia','int4');
        $this->setParametro('fecha','fecha','date');
        $this->setParametro('saldo','saldo','numeric');
        $this->setParametro('descripcion','descripcion','varchar');
        $this->setParametro('pnr','pnr','varchar');
        $this->setParametro('moneda','moneda','varchar');
        $this->setParametro('tipo','tipo','varchar');
        $this->setParametro('fecha_venta','fecha_venta','date');
        $this->setParametro('monto_total','monto_total','numeric');
        $this->setParametro('id_periodo_venta','id_periodo_venta','int4');
        $this->setParametro('id_apertura_cierre_caja','id_apertura_cierre_caja','int4');
        //Ejecuta la instruccion
        $this->monedaBase();

        $this->armarConsulta();
        $this->ejecutarConsulta();

        if ($this->monedaBase() == 'BOB') {
        /* Replica al sistema Ingresos Franklin Espinoza Alvarez*/
          if($this->aParam->getParametro('tipo') == 'venta_propia'){

            $this->insertatDepositoInformix();
        }
      }
        //Devuelve la respuesta
        return $this->respuesta;
    }


    function monedaBase(){

      $cone = new conexion();
      $link = $cone->conectarpdo();
      $copiado = false;

      $consulta ="select m.codigo_internacional
                                        from param.tmoneda m
                                        where m.tipo_moneda =  'base'";

      $res = $link->prepare($consulta);
      $res->execute();
      $result = $res->fetchAll(PDO::FETCH_ASSOC);
      return $result[0]['codigo_internacional'];

    }
    function modificarDeposito(){
        //Definicion de variables para ejecucion del procedimiento

        $this->procedimiento='obingresos.ft_deposito_ime';
        $this->transaccion='OBING_DEP_MOD';
        $this->tipo_procedimiento='IME';

        /*RECUPERAMOS EL ID_PUNTO_VENTA PARA CONSULTAR LA CUENTABANCARIA*/
        $this->setParametro('id_punto_venta','id_punto_venta','integer');
        /*---------------------------------------------------------------*/

        //Define los parametros para la funcion
        $this->setParametro('id_deposito','id_deposito','int4');
        $this->setParametro('estado_reg','estado_reg','varchar');
        $this->setParametro('nro_deposito','nro_deposito','varchar');
        $this->setParametro('nro_deposito_boa','nro_deposito_boa','varchar');
        $this->setParametro('agt','agt','varchar');
        $this->setParametro('monto_deposito','monto_deposito','numeric');
        $this->setParametro('id_moneda_deposito','id_moneda_deposito','int4');
        $this->setParametro('id_agencia','id_agencia','int4');
        $this->setParametro('fecha','fecha','date');
        $this->setParametro('saldo','saldo','numeric');
        $this->setParametro('descripcion','descripcion','varchar');
        $this->setParametro('pnr','pnr','varchar');
        $this->setParametro('moneda','moneda','varchar');
        $this->setParametro('tipo','tipo','varchar');
        $this->setParametro('fecha_venta','fecha_venta','date');
        $this->setParametro('monto_total','monto_total','numeric');

        $this->monedaBase();
        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        if ($this->monedaBase() == 'BOB') {
            if($this->aParam->getParametro('tipo') == 'venta_propia'){
             $this->modificarDepositoInformix();
            }
        }


        //Devuelve la respuesta
        return $this->respuesta;
    }

    function eliminarDeposito(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='obingresos.ft_deposito_ime';
        $this->transaccion='OBING_DEP_ELI';
        $this->tipo_procedimiento='IME';

        //Define los parametros para la funcion
        $this->setParametro('id_deposito','id_deposito','int4');
        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function cambiaEstadoDeposito(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='obingresos.ft_deposito_ime';
        $this->transaccion='OBING_VALIDEPO_UPD';
        $this->tipo_procedimiento='IME';

        //Define los parametros para la funcion
        //var_dump($this->aParam->getParametro('id_deposito'));exit;
        $this->setParametro('id_deposito','id_deposito','int4');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }
    function subirDatos(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='obingresos.ft_deposito_ime';
        $this->transaccion='OBING_DEP_SUB';
        $this->tipo_procedimiento='IME';

        //Define los parametros para la funcion
        $this->setParametro('nro_deposito','nro_deposito','varchar');
        //$this->setParametro('id_agencia','id_agencia','int4');
        $this->setParametro('monto_deposito','monto_deposito','numeric');
        $this->setParametro('moneda','moneda','varchar');
        $this->setParametro('descripcion','descripcion','varchar');
        $this->setParametro('pnr','pnr','varchar');
        $this->setParametro('estado','estado','varchar');
        $this->setParametro('fecha','fecha','varchar');
        $this->setParametro('tipo','tipo','varchar');
        $this->setParametro('observaciones','observaciones','varchar');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function subirDatosWP(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='obingresos.ft_deposito_ime';
        $this->transaccion='OBING_DEPWP_SUB';
        $this->tipo_procedimiento='IME';

        $this->setParametro('order_code','order_code','varchar');
        $this->setParametro('fecha','fecha','varchar');
        $this->setParametro('hora','hora','varchar');
        $this->setParametro('metodo_pago','metodo_pago','varchar');
        $this->setParametro('estado','estado','varchar');
        $this->setParametro('tarjeta','tarjeta','varchar');
        $this->setParametro('moneda','moneda','varchar');
        $this->setParametro('monto','monto','numeric');


        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }
    function  listarDepositoReporte()
    {
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento='obingresos.ft_deposito_sel';
        $this->transaccion='OBING_DEREP_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion


        $this->setParametro('fecha_ini','fecha_ini','date');
        $this->setParametro('fecha_fin','fecha_fin','date');
        $this->setParametro('por','por','varchar');
        $this->setParametro('tipo_deposito','tipo_deposito','varchar');
        $this->setCount(false);


        $this->captura('nro_deposito','varchar');
        $this->captura('fecha_deposito','varchar');
        $this->captura('pnr','varchar');
        $this->captura('monto_deposito','numeric');
        $this->captura('moneda','varchar');
        $this->captura('numero_tarjeta_deposito','varchar');

        $this->captura('total_boletos','numeric');
        $this->captura('nro_boletos','text');
        $this->captura('fecha_boletos','text');
        $this->captura('numero_tarjeta','text');
        $this->captura('detalle_boletos','text');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();



        //Devuelve la respuesta
        return $this->respuesta;
    }

    function  reporteDepositoBancaInternet()
    {
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento='obingresos.ft_deposito_sel';
        $this->transaccion='OBING_DEPBIN_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion


        $this->setParametro('fecha_ini','fecha_ini','date');
        $this->setParametro('fecha_fin','fecha_fin','date');
        $this->setParametro('id_moneda','id_moneda','integer');

        $this->setCount(false);


        $this->captura('fecha','varchar');
        $this->captura('banco','varchar');
        $this->captura('monto','numeric');


        //Ejecuta la instruccion
        $this->armarConsulta();


        $this->ejecutarConsulta();



        //Devuelve la respuesta
        return $this->respuesta;
    }

    function  reporteDepositoBancaInternetArchivo()
    {
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento='obingresos.ft_deposito_sel';
        $this->transaccion='OBING_DEPBINARC_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion


        $this->setParametro('fecha_ini','fecha_ini','date');
        $this->setParametro('fecha_fin','fecha_fin','date');
        $this->setParametro('id_moneda','id_moneda','integer');

        $this->setCount(false);


        $this->captura('fecha','varchar');
        $this->captura('banco','varchar');
        $this->captura('monto','numeric');


        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();



        //Devuelve la respuesta
        return $this->respuesta;
    }

    function sincronizarDeposito (){
      try {
          $hora= date ("h:i:s");
          $codigo_padre = $this->aParam->getParametro('codigo_padre');
          $estacion = $this->aParam->getParametro('estacion');
          $codigo = $this->aParam->getParametro('codigo');
          $tipo_cambio = $this->aParam->getParametro('tipo_cambio');
          $fechaventa =  $this->aParam->getParametro('fecha_venta');
          $this->informix->beginTransaction();
          $sql_in = "INSERT INTO ingresos:deposito ( pais,
                                                        estacion,
                                                        agt,
                                                        fecini,
                                                        fecfin,
                                                        tcambio,
                                                        montoitf,
                                                        observa,
                                                        fechareg,
                                                        horareg
                                                       )
                                                       VALUES
                                                       (
                                                       '".$codigo_padre."',
                                                       '".$estacion."' ,
                                                       '".$codigo."' ,
                                                       '".$fechaventa."',
                                                       '".$fechaventa."',
                                                       '".$tipo_cambio."',
                                                       0,
                                                       'ERP BOA',
                                                       TODAY,
                                                       '".$hora."' )";
          $info_nota_ins = $this->informix->prepare($sql_in);

          $info_nota_ins->execute();
          $this->informix->commit();
          $this->insertarDepositoSincronizado();
          return true;
      } catch (Exception $e) {
          $this->informix->rollBack();
      }
    }

    function insertarDepositoSincronizado(){
        try{
            $codigo_padre = $this->aParam->getParametro('codigo_padre');
            $estacion = $this->aParam->getParametro('estacion');
            $codigo = $this->aParam->getParametro('codigo');
            $fechaventa =  $this->aParam->getParametro('fecha_venta');
            $nro_deposito =  $this->aParam->getParametro('nro_deposito');
            $monto_deposito =  $this->aParam->getParametro('monto_deposito');
            $fecha = str_replace('/','-',$this->aParam->getParametro('fecha'));
            $this->informix->beginTransaction();
            $sql_in = "INSERT INTO ingresos:deposito_boleta(
					  pais,
					  estacion,
					  agt,
					  fecini,
					  fecfin,
					  nroboleta,
					  fecha,
					  ctabanco,
					  moneda,
					  monto,
					  usuario,
					  tipdoc,
					  documento
					 )
					 VALUES
					 (
					 '".$codigo_padre."' ,
					 '".$estacion."' ,
					 '".$codigo."',
					 '".$fechaventa."' ,
					 '".$fechaventa."' ,
					 '".$nro_deposito."' ,
					 '".$fecha."',
					 '".$this->nroCuenta()."',
					 '".$this->tipoMoneda()."' ,
					 '".$monto_deposito."' ,
					 '".$_SESSION['_LOGIN']."',
					 'DPENDE',
					 '0'
					 )";
            $info_nota_ins = $this->informix->prepare($sql_in);
            $info_nota_ins->execute();
            $this->informix->commit();
            return true;
        }catch (Exception $e){
            $this->informix->rollBack();
        }

    }

    function insertatDepositoInformix (){
        try {
            $hora= date ("h:i:s");
            $fechaventa =  $this->aParam->getParametro('fecha_venta');
            $this->informix->beginTransaction();
            $sql_in = "INSERT INTO ingresos:deposito ( pais,
                                                          estacion,
                                                          agt,
                                                          fecini,
                                                          fecfin,
                                                          tcambio,
                                                          montoitf,
                                                          observa,
                                                          fechareg,
                                                          horareg
                                                         )
                                                         VALUES
                                                         (
                                                         '".$this->aParam->getParametro('codigo_padre')."',
                                                         '".$this->aParam->getParametro('estacion')."' ,
                                                         '".$this->aParam->getParametro('codigo')."' ,
                                                         '".$fechaventa."',
                                                         '".$fechaventa."',
                                                         '".$this->aParam->getParametro('tipo_cambio')."',
                                                         0,
                                                         'ERP BOA',
                                                         TODAY,
                                                         '".$hora."' )";
            $info_nota_ins = $this->informix->prepare($sql_in);

            $info_nota_ins->execute();
            $this->informix->commit();
            $this->insertarBoletaDeposito();
            return true;
        } catch (Exception $e) {
            $this->informix->rollBack();
        }
    }
    function insertarBoletaDeposito (){
        try{
            $fecha = str_replace('/','-',$this->aParam->getParametro('fecha'));
            $this->informix->beginTransaction();
            $sql_in = "INSERT INTO ingresos:deposito_boleta(
					  pais,
					  estacion,
					  agt,
					  fecini,
					  fecfin,
					  nroboleta,
					  fecha,
					  ctabanco,
					  moneda,
					  monto,
					  usuario,
					  tipdoc,
					  documento
					 )
					 VALUES
					 (
					 '".$this->aParam->getParametro('codigo_padre')."' ,
					 '".$this->aParam->getParametro('estacion')."' ,
					 '".$this->aParam->getParametro('codigo')."',
					 '".$this->aParam->getParametro('fecha_venta')."' ,
					 '".$this->aParam->getParametro('fecha_venta')."' ,
					 '".$this->aParam->getParametro('nro_deposito')."' ,
					 '".$fecha."',
					 '".$this->nroCuenta()."',
					 '".$this->tipoMoneda()."' ,
					 '".$this->aParam->getParametro('monto_deposito')."' ,
					 '".$_SESSION['_LOGIN']."',
					 'DPENDE',
					 '0'
					 )";
            $info_nota_ins = $this->informix->prepare($sql_in);
            $info_nota_ins->execute();
            $this->informix->commit();
            return true;
        }catch (Exception $e){
            $this->informix->rollBack();
        }

    }
    function tipoMoneda(){
        $consulta ="select m.codigo_internacional
                                          from param.tmoneda m
                                          where m.id_moneda =  '".$this->aParam->getParametro('id_moneda_deposito')."'";

        $res = $this->link->prepare($consulta);
        $res->execute();
        $result = $res->fetchAll(PDO::FETCH_ASSOC);
        return $result[0]['codigo_internacional'];

    }
    function nroCuenta(){
        $consulta ="select cuen.nro_cuenta
                             from vef.tsucursal s
                             inner join vef.tpunto_venta p on p.id_sucursal = s.id_sucursal
                             inner join tes.tdepto_cuenta_bancaria de on de.id_depto = s.id_depto
                             inner join tes.tcuenta_bancaria cuen on cuen.id_cuenta_bancaria = de.id_cuenta_bancaria
                             where cuen.id_moneda = '".$this->aParam->getParametro('id_moneda_deposito')."' and p.id_punto_venta = '".$this->aParam->getParametro('id_punto_venta')."'";

        $res = $this->link->prepare($consulta);
        $res->execute();
        $result = $res->fetchAll(PDO::FETCH_ASSOC);

        return $result[0]['nro_cuenta'];

    }
    function recuperarNumberDeposito(){
        $consulta ="select obingresos.f_recuperar_nro_deposito_pas(".$this->aParam->getParametro('id_deposito').")";
        $res = $this->link->prepare($consulta);
        $res->execute();
        $result = $res->fetchAll(PDO::FETCH_ASSOC);
        return  $result[0]['f_recuperar_nro_deposito_pas'];
    }
    function recuperarDeposito(){
        $consulta ="select d.nro_deposito
from obingresos.tdeposito d
where d.tipo = 'venta_propia' and d.id_deposito ='".$this->aParam->getParametro('id_deposito')."'";

        $res = $this->link->prepare($consulta);
        $res->execute();
        $result = $res->fetchAll(PDO::FETCH_ASSOC);

        return  $result[0]['nro_deposito'];

    }
    function modificarDepositoInformix (){
        $this->informix->beginTransaction();
        try {

            $fecha = str_replace('/','-',$this->aParam->getParametro('fecha'));
            $sql = " UPDATE ingresos:deposito_boleta
              SET
                  nroboleta =  '".$this->aParam->getParametro('nro_deposito')."' ,
                  fecha =   '".$fecha."',
                  moneda = '".$this->tipoMoneda()."',
                  monto =  '" . $this->aParam->getParametro('monto_deposito') . "'
              WHERE

                  nroboleta =  '".$this->recuperarNumberDeposito()."'
                  ";
            $info_nota_ins = $this->informix->prepare($sql);
            //var_dump($info_nota_ins);exit;
            $info_nota_ins->execute();
            $this->informix->commit();
            return true;
        }catch (Exception $e){
            $this->informix->rollBack();
        }
    }
    function eliminar(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='obingresos.ft_deposito_ime';
        $this->transaccion='OBING_DEP_MI';
        $this->tipo_procedimiento='IME';

        //Define los parametros para la funcion

        $this->setParametro('nro_deposito','nro_deposito','varchar');
        $this->setParametro('codigo','codigo','varchar');
        $this->setParametro('fecha_venta','fecha_venta','date');
        $this->setParametro('monto_deposito','monto_deposito','numeric');
        $this->setParametro('desc_moneda','desc_moneda','varchar');

        $this->monedaBase();

        if ($this->monedaBase() == 'BOB') {
          $this->eliminarDepositoInformix();
        }
        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }
    function eliminarDepositoInformix (){
        $this->informix->beginTransaction();
        try {
            $sql = "DELETE FROM ingresos:deposito_boleta
                    WHERE  agt = '".$this->aParam->getParametro('codigo')."' AND  fecini = '".$this->aParam->getParametro('fecha_venta')."' AND fecfin = '".$this->aParam->getParametro('fecha_venta')."'
                    AND nroboleta = '".$this->aParam->getParametro('nro_deposito')."' AND moneda = '".$this->aParam->getParametro('desc_moneda')."' AND monto = '".$this->aParam->getParametro('monto_deposito')."' AND tipdoc = 'DPENDE'";
            $info_nota_ins = $this->informix->prepare($sql);
            $info_nota_ins->execute();
            $this->informix->commit();
            return true;
        }catch (Exception $e){
            $this->informix->rollBack();
        }

    }
}
?>
