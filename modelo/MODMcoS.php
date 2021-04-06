<?php
/**
*@package pXP
*@file gen-MODMcoS.php
*@author  (breydi.vasquez)
*@date 28-04-2020 15:25:04
*@description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
*/

class MODMcoS extends MODbase{

	function __construct(CTParametro $pParam){
		parent::__construct($pParam);
	}

	function listarMcoS(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='obingresos.ft_mco_s_sel';
		$this->transaccion='OBING_IMCOS_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion

		//Definicion de la lista del resultado del query
		$this->captura('id_mco','int4');
		$this->captura('estado_reg','varchar');
		$this->captura('estado','int4');
		$this->captura('fecha_emision','date');
		$this->captura('id_moneda','int4');
		$this->captura('motivo','text');
		$this->captura('valor_total','numeric');
		$this->captura('id_gestion','int4');
		$this->captura('id_usuario_reg','int4');
		$this->captura('fecha_reg','timestamp');
		$this->captura('id_usuario_ai','int4');
		$this->captura('usuario_ai','varchar');
		$this->captura('id_usuario_mod','int4');
		$this->captura('fecha_mod','timestamp');
		$this->captura('usr_reg','varchar');
		$this->captura('usr_mod','varchar');
		$this->captura('codigo','varchar');
		$this->captura('desc_ingas','varchar');
		$this->captura('codigo_internacional','varchar');
		$this->captura('gestion','int4');
		$this->captura('id_boleto','int4');
		$this->captura('tkt','varchar');
		$this->captura('fecha_doc_or','date');
		$this->captura('val_total_doc_or','numeric');
		$this->captura('moneda_doc_or','varchar');
		$this->captura('val_conv_doc_or','numeric');
		$this->captura('t_c_doc_or','numeric');
		$this->captura('estacion_doc_or','varchar');
		$this->captura('pais_doc_or','varchar');
		$this->captura('id_punto_venta','int4');
		$this->captura('agt_tv_head','varchar');
		$this->captura('city_head','varchar');
		$this->captura('suc_head','varchar');
		$this->captura('nombre_suc_head','varchar');
		$this->captura('estacion_head','varchar');
		$this->captura('pais_head','varchar');
    $this->captura('desc_moneda','varchar');
		$this->captura('id_concepto_ingas','int4');
		$this->captura('tipo_cambio','numeric');
		$this->captura('nro_mco','varchar');
		$this->captura('pax','varchar');
		$this->captura('id_funcionario_emisor','int4');
		$this->captura('desc_funcionario1','text');

		//Ejecuta la instruccion
    $this->armarConsulta();
    // echo($this->consulta);exit;
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}

	function insertarMcoS(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='obingresos.ft_mco_s_ime';
		$this->transaccion='OBING_IMCOS_INS';
		$this->tipo_procedimiento='IME';

		//Define los parametros para la funcion
		$this->setParametro('estado','estado','int4');
		$this->setParametro('fecha_emision','fecha_emision','date');
		$this->setParametro('id_moneda','id_moneda','int4');
		$this->setParametro('motivo','motivo','text');
		$this->setParametro('valor_total','valor_total','numeric');
		$this->setParametro('id_boleto','id_boleto','varchar');
		$this->setParametro('id_gestion','id_gestion','int4');
		$this->setParametro('id_concepto_ingas','id_concepto_ingas','int4');
		$this->setParametro('id_punto_venta','id_punto_venta','int4');
		$this->setParametro('tipo_cambio','tipo_cambio','numeric');
		$this->setParametro('nro_mco','nro_mco','varchar');
		$this->setParametro('pax','pax','varchar');
		$this->setParametro('id_funcionario_emisor','id_funcionario_emisor','int4');

		$this->setParametro('pais_doc_or','pais_doc_or','varchar');
		$this->setParametro('estacion_doc_or','estacion_doc_or','varchar');
		$this->setParametro('fecha_doc_or','fecha_doc_or','date');
		$this->setParametro('t_c_doc_or','t_c_doc_or','numeric');
		$this->setParametro('moneda_doc_or','moneda_doc_or','varchar');
		$this->setParametro('val_total_doc_or','val_total_doc_or','numeric');
		$this->setParametro('val_conv_doc_or','val_conv_doc_or','numeric');


		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}

	function modificarMcoS(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='obingresos.ft_mco_s_ime';
		$this->transaccion='OBING_IMCOS_MOD';
		$this->tipo_procedimiento='IME';

		//Define los parametros para la funcion
		$this->setParametro('id_mco','id_mco','int4');
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('estado','estado','int4');
		$this->setParametro('fecha_emision','fecha_emision','date');
		$this->setParametro('id_moneda','id_moneda','int4');
		$this->setParametro('motivo','motivo','text');
		$this->setParametro('valor_total','valor_total','numeric');
		$this->setParametro('id_detalle_boletos_web','id_detalle_boletos_web','int4');
		$this->setParametro('id_gestion','id_gestion','int4');
		$this->setParametro('tipo_cambio','tipo_cambio','numeric');
		$this->setParametro('nro_mco','nro_mco','varchar');
		$this->setParametro('pax','pax','varchar');
		$this->setParametro('id_funcionario_emisor','id_funcionario_emisor','int4');
		$this->setParametro('id_punto_venta','id_punto_venta','int4');

		$this->setParametro('id_boleto','id_boleto','varchar');
		$this->setParametro('pais_doc_or','pais_doc_or','varchar');
		$this->setParametro('estacion_doc_or','estacion_doc_or','varchar');
		$this->setParametro('fecha_doc_or','fecha_doc_or','date');
		$this->setParametro('t_c_doc_or','t_c_doc_or','numeric');
		$this->setParametro('moneda_doc_or','moneda_doc_or','varchar');
		$this->setParametro('val_total_doc_or','val_total_doc_or','numeric');
		$this->setParametro('val_conv_doc_or','val_conv_doc_or','numeric');


		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}

	function eliminarMcoS(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='obingresos.ft_mco_s_ime';
		$this->transaccion='OBING_IMCOS_ELI';
		$this->tipo_procedimiento='IME';

		//Define los parametros para la funcion
		$this->setParametro('id_mco','id_mco','int4');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
    }

	function getDatatoFormRegMcoS(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='obingresos.ft_mco_s_ime';
		$this->transaccion='OBING_GPTFIN_IME';
		$this->tipo_procedimiento='IME';

        //Define los parametros para la funcion
        $this->setParametro('id_punto_venta','id_punto_venta','int4');
		//Ejecuta la instruccion
		$this->armarConsulta(); //echo $this->consulta;exit;
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}


    function listarTkts() {

        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento = 'obingresos.ft_mco_s_sel';
        $this->transaccion = 'OBING_GETKTS_SEL';
        $this->tipo_procedimiento = 'SEL';//tipo de transaccion

        $this->setParametro('tkt', 'tkt', 'varchar');

        //Definicion de la lista del resultado del query
        $this->captura('id_boleto', 'int4');
        $this->captura('id_agencia', 'int4');
        $this->captura('id_moneda_boleto', 'int4');
        $this->captura('tkt', 'varchar');
        $this->captura('moneda', 'varchar');
        $this->captura('total', 'numeric');
        $this->captura('fecha_emision', 'date');
        $this->captura('tkt_estac', 'varchar');
        $this->captura('tkt_pais', 'varchar');
				$this->captura('val_conv', 'numeric');
				$this->captura('tipo_cambio', 'numeric');


        //Ejecuta la instruccion
        $this->armarConsulta();
        //echo($this->consulta);exit;
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

		function listarTktFiltro() {

        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento = 'obingresos.ft_mco_s_sel';
        $this->transaccion = 'OBING_GETKTFIL_SEL';
        $this->tipo_procedimiento = 'SEL';//tipo de transaccion

        $this->setParametro('nro_mco', 'nro_mco', 'varchar');

        //Definicion de la lista del resultado del query
				$this->captura('id_mco', 'int4');
        $this->captura('id_boleto', 'int4');
        $this->captura('nro_mco', 'varchar');
        $this->captura('moneda', 'varchar');
        $this->captura('total', 'numeric');
        //Ejecuta la instruccion
        $this->armarConsulta();
        //echo($this->consulta);exit;
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

		function listarRepoMcoS() {
			//Definicion de variables para ejecucion del procedimiento
      $this->procedimiento = 'obingresos.ft_mco_s_sel';
      $this->transaccion = 'OBING_REPMCOS_SEL';
			$this->tipo_procedimiento='SEL';
			$this->setCount(false);

			//Captura de datos
			$this->captura('id_mco', 'int4');
			$this->captura('fecha_emision', 'date');
			$this->captura('t_concepto', 'varchar');
			$this->captura('nro_mco', 'varchar');
			$this->captura('pax', 'varchar');
			$this->captura('tkt', 'varchar');
			$this->captura('desc_funcionario2','text');
			$this->captura('motivo', 'text');
			$this->captura('valor_total', 'numeric');
			$this->captura('cajero', 'text');
			$this->captura('codi_moneda', 'varchar');

			//Ejecuta la instruccion
			$this->armarConsulta();
			// echo($this->consulta);exit;
			$this->ejecutarConsulta();

			//Devuelve la respuesta
			return $this->respuesta;
		}
}
?>
