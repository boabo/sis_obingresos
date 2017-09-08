<?php
class  MODDetalleBoletosWeb extends MODbase{
    function __construct(CTParametro $pParam){
        parent::__construct($pParam);
    }
    function listarReporteNitRazon(){
        //Definicion de variables para ejecucion del procedimientp
            $this->procedimiento='obingresos.ft_detalle_boletos_web_sel';
            $this->transaccion='OBING_DETBOWEB_SEL';
            $this->tipo_procedimiento='SEL';//tipo de transaccion

            $this->setParametro('fecha_ini','fecha_ini','date');
            $this->setParametro('fecha_fin','fecha_fin','date');
            $this->setCount(false);

            $this->captura('fecha_emision', 'date');
            $this->captura('billete', 'varchar');
            $this->captura('entidad_pago', 'varchar');
            $this->captura('nit', 'varchar');
            $this->captura('razon_social', 'varchar');
            $this->captura('importe', 'numeric');
            $this->captura('nit_ingresos', 'varchar');
            $this->captura('razon_ingresos', 'varchar');

            //Ejecuta la instruccion
            $this->armarConsulta();
            $this->ejecutarConsulta();
            //var_dump($this->respuesta);exit;
            //Devuelve la respuesta
            return $this->respuesta;
    }

    function listarConciliacionTotales(){
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento='obingresos.ft_detalle_boletos_web_sel';
        $this->transaccion='OBING_CONBINTOT_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion

        $this->setParametro('fecha_ini','fecha_ini','date');
        $this->setParametro('fecha_fin','fecha_fin','date');
        $this->setParametro('moneda','moneda','varchar');
        $this->setParametro('id_moneda','id_moneda','integer');
        $this->setParametro('banco','banco','varchar');
        $this->setCount(false);

        $this->captura('fecha', 'varchar');
        $this->captura('monto', 'numeric');
        $this->captura('tipo', 'varchar');

        //Ejecuta la instruccion
        $this->armarConsulta();

        
        $this->ejecutarConsulta();

        return $this->respuesta;
    }

    function listarConciliacionDetalle(){
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento='obingresos.ft_detalle_boletos_web_sel';
        $this->transaccion='OBING_CONBINDET_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion

        $this->setParametro('fecha_ini','fecha_ini','date');
        $this->setParametro('fecha_fin','fecha_fin','date');
        $this->setParametro('moneda','moneda','varchar');
        $this->setParametro('id_moneda','id_moneda','integer');
        $this->setParametro('banco','banco','varchar');
        $this->setCount(false);

        $this->captura('fecha_hora', 'varchar');
        $this->captura('fecha', 'varchar');
        $this->captura('pnr', 'varchar');
        $this->captura('monto_ingresos', 'numeric');
        $this->captura('monto_archivos', 'numeric');
        $this->captura('fecha_pago', 'varchar');
        $this->captura('observaciones', 'varchar');

        //Ejecuta la instruccion
        $this->armarConsulta();


        $this->ejecutarConsulta();

        return $this->respuesta;
    }

    function listarConciliacionResumen(){
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento='obingresos.ft_detalle_boletos_web_sel';
        $this->transaccion='OBING_CONBINRES_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion

        $this->setParametro('id_periodo','id_periodo','integer');
        $this->setParametro('id_gestion','id_gestion','integer');

        $this->setCount(false);
        $this->captura('tipo', 'varchar');
        $this->captura('banco', 'varchar');
        $this->captura('moneda', 'varchar');
        $this->captura('monto1', 'numeric');
        $this->captura('monto2', 'numeric');


        //Ejecuta la instruccion
        $this->armarConsulta();



        $this->ejecutarConsulta();

        return $this->respuesta;
    }
    function insertarBilletePortal(){

        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='obingresos.ft_detalle_boletos_web_ime';
        $this->transaccion='OBING_DETBOPOR_INS';
        $this->tipo_procedimiento='IME';

        $this->setParametro('billete','billete','varchar');
        $this->setParametro('medio_pago','medio_pago','varchar');
        $this->setParametro('entidad','entidad','varchar');
        $this->setParametro('moneda','moneda','varchar');
        $this->setParametro('importe','importe','numeric');
        $this->setParametro('fecha_emision','fecha_emision','date');
        $this->setParametro('nit','nit','varchar');
        $this->setParametro('razon_social','razon_social','varchar');
        $this->setParametro('fecha_pago','fecha_pago','date');
        $this->setParametro('id_entidad','id_entidad','integer');
        $this->setParametro('comision','comision','numeric');
        $this->setParametro('neto','neto','numeric');
        $this->setParametro('numero_autorizacion','numero_autorizacion','varchar');


        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }


    function listarConciliacionObservaciones(){
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento='obingresos.ft_detalle_boletos_web_sel';
        $this->transaccion='OBING_CONBINOBS_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion

        $this->setParametro('id_periodo','id_periodo','integer');
        $this->setParametro('id_gestion','id_gestion','integer');
        $this->setParametro('tipo','tipo','varchar');

        $this->setCount(false);
        $this->captura('banco', 'varchar');
        $this->captura('fecha', 'varchar');
        $this->captura('observacion', 'text');


        //Ejecuta la instruccion
        $this->armarConsulta();


        $this->ejecutarConsulta();

        return $this->respuesta;
    }
	
	function validarBoletos(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='obingresos.ft_detalle_boletos_web_sel';
        $this->transaccion='OBING_OBSERVA_SEL';
        $this->tipo_procedimiento='SEL';
		$this->setCount(false);

        $this->setParametro('fecha_emision','fecha_emision','date');
        $this->setParametro('detalle','detalle','text');
		
		
        $this->captura('billete', 'varchar');
        $this->captura('pnr', 'varchar');
        $this->captura('total', 'numeric');
        $this->captura('moneda', 'varchar');
        $this->captura('tipo_observacion', 'varchar');
		$this->captura('observacion', 'text');		
		

        //Ejecuta la instruccion
        $this->armarConsulta();

        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

}
?>