CREATE OR REPLACE FUNCTION obingresos.ft_agencia_ime (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Ingresos
 FUNCION: 		obingresos.ft_agencia_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'obingresos.tagencia'
 AUTOR: 		 (jrivera)
 FECHA:	        06-01-2016 22:02:33
 COMENTARIOS:
***************************************************************************
 HISTORIAL DE MODIFICACIONES:

 DESCRIPCION:
 AUTOR:
 FECHA:
***************************************************************************/

DECLARE

	v_nro_requerimiento    	integer;
	v_parametros           	record;
	v_id_requerimiento     	integer;
	v_resp		            varchar;
	v_nombre_funcion        text;
	v_mensaje_error         text;
	v_id_agencia	integer;
    v_num_tramite			varchar;
    v_id_proceso_wf			integer;
    v_id_estado_wf			integer;
    v_codigo_estado			varchar;
    v_id_estado_registro	integer;
    v_id_contrato			integer;
    v_id_boleta				integer;
    v_id_deposito			integer;
    v_id_comision			integer;
    v_id_cuenta_bancaria	integer;
    v_contrato				record;
    v_suma_periodos_ant		numeric;
    v_id_moneda_base		integer;
    v_id_moneda_usd			integer;
    v_suma_movimientos		numeric;
    v_saldo					numeric;
    v_codigo_auto			varchar;
    v_id_tipo_estado_registro	integer;
    v_id_funcionario_responsable	integer;
    v_res_bool				BOOLEAN;
    /*Variables para el update de agencia Ismael Valdivia*/
    v_tipo_institucion		varchar;
    v_existe_agencia		integer;
    v_nombre_agencia		varchar;
    v_existencia_auxiliar	integer;
    v_codigo_int			varchar;
    v_tipo_agencia			varchar;
	v_tipo_auxiliar			varchar;
    v_id_auxiliar			integer;
    v_id_anexo				integer;
    v_id_movimiento_entidad	integer;
    v_id_moneda				integer;

    v_eliminar				varchar;
    v_eliminar_2			varchar;
    v_officeId				varchar;
    v_existe_auxiliar		integer;
    v_existe_auxiliar_officeID	integer;
    v_id_agencia_recuperado	integer;
    v_existe_agencia_activa	integer;
    v_fecha_reg_agencia		date;
    v_diferencia_data		integer;
	v_office_id_actual		varchar;
    v_nombre_agencia_recu	varchar;
    v_existe_auxiliar_officeID_nuevo	integer;
    v_nombre_agencia_actual	varchar;
    v_codigo_no_iata		varchar;
    v_existencia_auxiliar_nombre	integer;
    v_activar_contrato		varchar;
    v_nombre_auxiliar		varchar;
BEGIN

    v_nombre_funcion = 'obingresos.ft_agencia_ime';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'OBING_AGE_INS'
 	#DESCRIPCION:	Insercion de registros
 	#AUTOR:		jrivera
 	#FECHA:		06-01-2016 22:02:33
	***********************************/

	if(p_transaccion='OBING_AGE_INS')then

        begin
        	--Sentencia de la insercion
        	insert into obingresos.tagencia(
			id_moneda_control,
			tipo_cambio,
			codigo,
			monto_maximo_deuda,
			tipo_agencia,
			codigo_int,
			nombre,
			tipo_pago,
			estado_reg,
			depositos_moneda_boleto,
			id_usuario_ai,
			id_usuario_reg,
			usuario_ai,
			fecha_reg,
			fecha_mod,
			id_usuario_mod,
            id_lugar,
            boaagt
          	) values(
			v_parametros.id_moneda_control,
			v_parametros.tipo_cambio,
			v_parametros.codigo,
			v_parametros.monto_maximo_deuda,
			v_parametros.tipo_agencia,
			v_parametros.codigo_int,
			v_parametros.nombre,
			v_parametros.tipo_pago,
			'activo',
			v_parametros.depositos_moneda_boleto,
			v_parametros._id_usuario_ai,
			p_id_usuario,
			v_parametros._nombre_usuario_ai,
			now(),
			null,
			null,
            v_parametros.id_lugar,
            v_parametros.boaagt



			)RETURNING id_agencia into v_id_agencia;

			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Agencias almacenado(a) con exito (id_agencia'||v_id_agencia||')');
            v_resp = pxp.f_agrega_clave(v_resp,'id_agencia',v_id_agencia::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

    /*********************************
 	#TRANSACCION:  'OBING_AGEPOR_INS'
 	#DESCRIPCION:	Insercion de agencia a traves  del protal corporativo
 	#AUTOR:		jrivera
 	#FECHA:		06-01-2016 22:02:33
	***********************************/

	elsif(p_transaccion='OBING_AGEPOR_INS')then

        begin
        	--Sentencia de la insercion
        	insert into obingresos.tagencia(
			id_moneda_control,
			tipo_cambio,
			codigo,
			monto_maximo_deuda,
			tipo_agencia,
			codigo_int,
			nombre,
			tipo_pago,
			estado_reg,
			depositos_moneda_boleto,
			id_usuario_ai,
			id_usuario_reg,
			usuario_ai,
			fecha_reg,
			fecha_mod,
			id_usuario_mod,
            id_lugar,
            tipo_persona,
            boaagt
          	) values(
			param.f_get_moneda_base(),
			'venta',
			'56991152',
			0,
			v_parametros.tipo_agencia,
			'56991152',
			v_parametros.nombre,
			'postpago',
			'activo',
			'si',
			v_parametros._id_usuario_ai,
			p_id_usuario,
			v_parametros._nombre_usuario_ai,
			now(),
			null,
			null,
            (select id_lugar from param.tlugar where codigo = v_parametros.ciudad),
            v_parametros.tipo_persona,
            'B'


			)RETURNING id_agencia into v_id_agencia;



			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Agencias almacenado(a) con exito (id_agencia'||v_id_agencia||')');
            v_resp = pxp.f_agrega_clave(v_resp,'id_agencia',v_id_agencia::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

    /*********************************
 	#TRANSACCION:  'OBING_VERSALAGE_MOD'
 	#DESCRIPCION:	Verificar saldo de agencias
 	#AUTOR:		jrivera
 	#FECHA:		06-01-2016 22:02:33
	***********************************/

	elsif(p_transaccion='OBING_VERSALAGE_MOD')then

        begin
        	--verificar que la agencia tenga contrato vigente con la forma de pago indicada
            select c.*,a.controlar_periodos_pago,a.validar_boleta,a.bloquear_emision into v_contrato
                from leg.tcontrato c
                inner join obingresos.tagencia a on a.id_agencia = c.id_agencia
                where c.id_agencia = v_parametros.id_agencia and c.estado = 'finalizado' and
                c.fecha_inicio <= now()::date and c.fecha_fin >= now()::date;
            if (v_contrato.id_contrato is null) then
            	raise exception 'La agencia no tiene un contrato activo';
            end if;
            --verificar que la boleta de garantia este vigente si es postpago
        	if ('postpago' = ANY(v_contrato.formas_pago) and v_contrato.validar_boleta = 'si') then
            	if (not exists (
                	select 1
                    from leg.tanexo a
                    where a.tipo = 'boleta_garantia' and a.estado_reg = 'activo' and
                    a.fecha_desde <= now()::date and a.fecha_hasta >= now()::date and
                    a.id_contrato =v_contrato.id_contrato)) then
                    raise exception 'La boleta de garantia de la cuenta corriente no se encuentra vigente';
                end if;
            end if;

            if (exists (select 1
            			from obingresos.tperiodo_venta_agencia pva
                        inner join obingresos.tperiodo_venta pv on pv.id_periodo_venta = pva.id_periodo_venta
                        where pv.fecha_pago is not null and fecha_pago < now()::date and pva.id_agencia = v_parametros.id_agencia
                        and (pva.monto_mb < 0 or pva.monto_usd < 0)) and v_contrato.controlar_periodos_pago = 'si') then
            	raise exception 'La agencia tiene periodos adeudados vencidos. Verifique su estado de cuenta!!!';
        	end if;

            if (v_contrato.bloquear_emision = 'si') then
            	raise exception 'La emision para esta agencia ha sido bloqueada por el administrador, consulte con el area de ingresos';
            end if;

            select po_autorizacion, po_saldo into v_codigo_auto ,v_saldo
            from obingresos.f_verificar_saldo_agencia(v_parametros.id_agencia,
                							v_parametros.monto,v_parametros.moneda::varchar,p_id_usuario,v_parametros.pnr,v_parametros.apellido,'si',v_parametros.monto_total,v_parametros.fecha);




			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','La entidad tiene saldo para emitir la reserva');
            v_resp = pxp.f_agrega_clave(v_resp,'tipo_mensaje','exito');
            v_resp = pxp.f_agrega_clave(v_resp,'codigo_autorizacion',v_codigo_auto::varchar);
			v_resp = pxp.f_agrega_clave(v_resp,'saldo',v_saldo::varchar);
            --Devuelve la respuesta
            return v_resp;

		end;

    /*********************************
 	#TRANSACCION:  'OBING_GETSALAGE_MOD'
 	#DESCRIPCION:	Obtener saldo de agencia
 	#AUTOR:		jrivera
 	#FECHA:		06-01-2016 22:02:33
	***********************************/

	elsif(p_transaccion='OBING_GETSALAGE_MOD')then

        begin


            v_saldo = obingresos.f_get_saldo_agencia(v_parametros.id_agencia,
                							v_parametros.moneda::varchar);




			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Saldo Agencia');
            v_resp = pxp.f_agrega_clave(v_resp,'tipo_mensaje','exito');
            v_resp = pxp.f_agrega_clave(v_resp,'saldo',v_saldo::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

    /*********************************
 	#TRANSACCION:  'OBING_CONPOR_INS'
 	#DESCRIPCION:	Insercion de contrato para agencia
 	#AUTOR:		jrivera
 	#FECHA:		06-01-2016 22:02:33
	***********************************/

	elsif(p_transaccion='OBING_CONPOR_INS')then

        begin

        --11/12-2019 (Alan.felipez) revision si el numero de contrato ya se encuentra registrado
        perform leg.f_verificar_numero_contrato( 'contrato',v_parametros.numero_contrato, null,'insertar');


        select coalesce(max (id_contrato),0) into v_id_contrato
        from leg.tcontrato;


        SELECT
             ps_num_tramite ,
             ps_id_proceso_wf ,
             ps_id_estado_wf ,
             ps_codigo_estado
          into
             v_num_tramite,
             v_id_proceso_wf,
             v_id_estado_wf,
             v_codigo_estado

        FROM wf.f_inicia_tramite(
             p_id_usuario,
             v_parametros._id_usuario_ai,
             v_parametros._nombre_usuario_ai,
             (select po_id_gestion from param.f_get_periodo_gestion(now()::date)),
             'CON',
             NULL,
             NULL,
             NULL,
             'CON-' ||v_id_contrato
             );



            INSERT INTO
          leg.tcontrato
        (
          id_usuario_reg,
          id_usuario_ai,
          usuario_ai,
          id_estado_wf,
          id_proceso_wf,
          estado,
          tipo,
          id_gestion,
          id_agencia,
          monto,
          id_moneda,

          contrato_adhesion,

          id_funcionario,
          id_lugar,
          fecha_inicio,
          objeto,
          solicitud,
          numero,
          fecha_elaboracion,
          fecha_fin,
          tipo_agencia,
          formas_pago,
          moneda_restrictiva,
          cuenta_bancaria1,
          entidad_bancaria1,
          nombre_cuenta_bancaria1,
          cuenta_bancaria2,
          entidad_bancaria2,
          nombre_cuenta_bancaria2
        )
        VALUES (
          p_id_usuario,
          v_parametros._id_usuario_ai,
          v_parametros._nombre_usuario_ai,
          v_id_estado_wf,
          v_id_proceso_wf,
          v_codigo_estado,
          'comercial',
          (select po_id_gestion from param.f_get_periodo_gestion(now()::date)),
          v_parametros.id_agencia,
         0,
          param.f_get_moneda_base(),
          'no',

          v_parametros.id_funcionario,

          (select id_lugar from obingresos.tagencia where id_agencia = v_parametros.id_agencia),
          v_parametros.fecha_inicio,
          v_parametros.objeto,
          'Elaboracion de contrato comercial',
          v_parametros.numero_contrato,
          v_parametros.fecha_firma,
          v_parametros.fecha_fin,
          v_parametros.tipo_agencia,
          string_to_array(v_parametros.formas_pago, ','),
          v_parametros.moneda_restrictiva,
          v_parametros.cuenta_bancaria1,
          v_parametros.entidad_bancaria1,
          v_parametros.nombre_cuenta_bancaria1,
          v_parametros.cuenta_bancaria2,
          v_parametros.entidad_bancaria2,
          v_parametros.nombre_cuenta_bancaria2

        )returning id_contrato into v_id_contrato;
            v_res_bool =  wf.f_inserta_documento_wf(p_id_usuario, v_id_proceso_wf, v_id_estado_wf);

			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Contrato almacenado(a) con exito (id_contrato'||v_id_contrato||')');
            v_resp = pxp.f_agrega_clave(v_resp,'id_contrato',v_id_contrato::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

    /*********************************
 	#TRANSACCION:  'OBING_BOLAGE_INS'
 	#DESCRIPCION:	Insercion de boleta para agencia
 	#AUTOR:		jrivera
 	#FECHA:		06-01-2016 22:02:33
	***********************************/

	elsif(p_transaccion='OBING_BOLAGE_INS')then

        begin

            INSERT INTO
          leg.tanexo
        (
          id_usuario_reg,
          id_usuario_ai,
          usuario_ai,
          id_contrato,
          tipo,
          fecha_desde,
          fecha_hasta,
          monto,
          moneda,
          banco,
          tipo_boleta

        )
        VALUES (
          p_id_usuario,
          v_parametros._id_usuario_ai,
          v_parametros._nombre_usuario_ai,
          v_parametros.id_contrato,
          'boleta_garantia',
          v_parametros.fecha_inicio,
          v_parametros.fecha_fin,
          v_parametros.monto,
          v_parametros.moneda,
          v_parametros.banco,
          v_parametros.tipo_boleta


        )returning id_anexo into v_id_boleta;


			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Boleta almacenado(a) con exito (id_boleta'||v_id_boleta||')');
            v_resp = pxp.f_agrega_clave(v_resp,'id_boleta',v_id_boleta::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

    /*********************************
 	#TRANSACCION:  'OBING_FINCONPOR_MOD'
 	#DESCRIPCION:	Finalizar contrato desde portal
 	#AUTOR:		jrivera
 	#FECHA:		06-01-2016 22:02:33
	***********************************/

	elsif(p_transaccion='OBING_FINCONPOR_MOD')then

        begin

        	select c.id_proceso_wf,c.id_estado_wf into v_id_proceso_wf,v_id_estado_wf
            from leg.tcontrato c
            where c.id_contrato = v_parametros.id_contrato;

             select te.id_tipo_estado into v_id_tipo_estado_registro
             from wf.ttipo_estado te
             inner join wf.ttipo_proceso tp
                on te.id_tipo_proceso = tp.id_tipo_proceso
             inner join wf.tproceso_wf p
                on p.id_tipo_proceso = tp.id_tipo_proceso
             where te.codigo = 'vobo_comercial' and p.id_proceso_wf = v_id_proceso_wf and te.estado_reg = 'activo';


            select id_funcionario  into v_id_funcionario_responsable
     		from wf.f_funcionario_wf_sel(p_id_usuario, v_id_tipo_estado_registro,now()::date,v_id_estado_wf) as (id_funcionario integer,desc_funcionario text,desc_cargo text,prioridad integer);



			v_id_estado_registro =  wf.f_registra_estado_wf(v_id_tipo_estado_registro,   --p_id_tipo_estado_siguiente
                                                         v_id_funcionario_responsable,
                                                         v_id_estado_wf,   --  p_id_estado_wf_anterior
                                                         v_id_proceso_wf,
                                                         p_id_usuario,
                                                         v_parametros._id_usuario_ai,
          												 v_parametros._nombre_usuario_ai,
                                                         NULL,
                                                         'Paso de estado automatico desde portal corporativo');

            update leg.tcontrato
     		set id_estado_wf = v_id_estado_registro,
     		estado = 'vobo_comercial'
     		where id_proceso_wf = v_id_proceso_wf;

			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Contrato finalizado con exito');
            v_resp = pxp.f_agrega_clave(v_resp,'id_contrato',v_parametros.id_contrato::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

    /*********************************
 	#TRANSACCION:  'OBING_DEPAGE_INS'
 	#DESCRIPCION:	Insercion de depositos hechos por agencia
 	#AUTOR:		jrivera
 	#FECHA:		06-01-2016 22:02:33
	***********************************/

	elsif(p_transaccion='OBING_DEPAGE_INS')then

        begin
        	select id_cuenta_bancaria into v_id_cuenta_bancaria
            from tes.tcuenta_bancaria cb
            where cb.nro_cuenta = v_parametros.cuenta_bancaria;

            if (v_id_cuenta_bancaria is null) then
            	raise exception 'No existe la cuenta bancaria registrada';
            end if;

        	INSERT INTO
                obingresos.tdeposito
              (
                id_usuario_reg,
                id_usuario_ai,
                usuario_ai,
                nro_deposito,
                monto_deposito,
                id_moneda_deposito,
                id_agencia,
                fecha,
                saldo,
                moneda,



                id_cuenta_bancaria,
                tipo
              )
              VALUES (
                p_id_usuario,
                v_parametros._id_usuario_ai,
                v_parametros._nombre_usuario_ai,
                v_parametros.numero,
                v_parametros.monto,
                (select id_moneda from param.tmoneda where codigo_internacional = v_parametros.moneda),
                v_parametros.id_agencia,
                v_parametros.fecha,
                0,
                v_parametros.moneda,
                v_id_cuenta_bancaria,
                'agencia'
              )returning id_deposito into v_id_deposito;



			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Deposito almacenado(a) con exito (id_deposito'||v_id_deposito||')');
            v_resp = pxp.f_agrega_clave(v_resp,'id_deposito',v_id_deposito::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

    /*********************************
 	#TRANSACCION:  'OBING_COMAGE_INS'
 	#DESCRIPCION:	Insercion de comisiones hechos por agencia
 	#AUTOR:		jrivera
 	#FECHA:		06-01-2016 22:02:33
	***********************************/

	elsif(p_transaccion='OBING_COMAGE_INS')then

        begin

            select id_agencia into v_id_agencia
            from leg.tcontrato c
            where c.id_contrato = v_parametros.id_contrato;

            if (v_id_agencia is null) then
            	raise exception 'Contrato no valido o no existe una agencia relacionada';
            end if;
        	INSERT INTO
              obingresos.tcomision_agencia
            (
              id_usuario_reg,
              id_usuario_ai,
              usuario_ai,
              id_contrato,
              id_agencia,
              descripcion,
              tipo_comision,
              mercado,
              porcentaje,
              moneda,
              limite_superior,
              limite_inferior
            )
            VALUES (
              p_id_usuario,
              v_parametros._id_usuario_ai,
              v_parametros._nombre_usuario_ai,
              v_parametros.id_contrato,
              v_id_agencia,
              v_parametros.descripcion,
              v_parametros.tipo_comision,
              v_parametros.mercado,
              v_parametros.porcentaje,
              v_parametros.moneda,
              v_parametros.limite_superior,
              v_parametros.limite_inferior
            )returning id_comision into v_id_comision;



			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Comision almacenado(a) con exito (id_comision'||v_id_comision||')');
            v_resp = pxp.f_agrega_clave(v_resp,'id_comision',v_id_comision::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'OBING_AGE_MOD'
 	#DESCRIPCION:	Modificacion de registros
 	#AUTOR:		jrivera
 	#FECHA:		06-01-2016 22:02:33
	***********************************/

	elsif(p_transaccion='OBING_AGE_MOD')then

		begin
			--Sentencia de la modificacion
			update obingresos.tagencia set
			id_moneda_control = v_parametros.id_moneda_control,
			tipo_cambio = v_parametros.tipo_cambio,
			codigo = v_parametros.codigo,
			monto_maximo_deuda = v_parametros.monto_maximo_deuda,
			tipo_agencia = v_parametros.tipo_agencia,
			codigo_int = v_parametros.codigo_int,
			nombre = v_parametros.nombre,
			tipo_pago = v_parametros.tipo_pago,
			depositos_moneda_boleto = v_parametros.depositos_moneda_boleto,
			fecha_mod = now(),
			id_usuario_mod = p_id_usuario,
			id_usuario_ai = v_parametros._id_usuario_ai,
			usuario_ai = v_parametros._nombre_usuario_ai,
            bloquear_emision = v_parametros.bloquear_emision,
            validar_boleta = v_parametros.validar_boleta,
            controlar_periodos_pago = v_parametros.controlar_periodos_pago,
            estado_reg = v_parametros.estado_reg
			where id_agencia=v_parametros.id_agencia;

			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Agencias modificado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_agencia',v_parametros.id_agencia::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'OBING_AGE_ELI'
 	#DESCRIPCION:	Eliminacion de registros
 	#AUTOR:		jrivera
 	#FECHA:		06-01-2016 22:02:33
	***********************************/

	elsif(p_transaccion='OBING_AGE_ELI')then

		begin
			--Sentencia de la eliminacion
			delete from obingresos.tagencia
            where id_agencia=v_parametros.id_agencia;

            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Agencias eliminado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_agencia',v_parametros.id_agencia::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

    /*********************************
 	#TRANSACCION:  'OBING_AGE_UPD'
 	#DESCRIPCION:	Actualizacion de Registros
 	#AUTOR:		Ismael Valdivia
 	#FECHA:		09-08-2021 08:30:33
	***********************************/

	elsif(p_transaccion='OBING_AGE_UPD')then

		begin

        	/*Aumentando Control para no duplicar officeId*/
        	select count(ag.id_agencia)
            		into
                    v_existe_agencia
            from obingresos.tagencia ag
            where (trim(ag.codigo_int) = trim(v_parametros.codigo_noiata) or trim(ag.codigo_noiata) = trim(v_parametros.codigo_noiata))
            and ag.id_agencia != v_parametros.id_agencia
            limit 1;
        	/**********************************************/

            if (v_existe_agencia > 0) then

            	select ag.nombre
                       into
                        v_nombre_agencia
                from obingresos.tagencia ag
                where (trim(ag.codigo_int) = trim(v_parametros.codigo_noiata) or trim(ag.codigo_noiata) = trim(v_parametros.codigo_noiata))
                and ag.id_agencia != v_parametros.id_agencia
                limit 1;


            	raise exception 'No se pudo actualizar la informacion porque el office_id ya esta relacionada a la Agencia % favor verifique la información',v_nombre_agencia;
            else
            	--Sentencia de la eliminacion
                update obingresos.tagencia set
                      codigo=v_parametros.codigo,
                      codigo_noiata=v_parametros.codigo_noiata,
                      codigo_int=v_parametros.codigo_noiata
                where id_agencia=v_parametros.id_agencia;


                select ag.nombre,
                	   ag.codigo_int,
                       ag.tipo_agencia
                       into
                        v_nombre_agencia,
                        v_codigo_int,
                        v_tipo_agencia
                from obingresos.tagencia ag
                where ag.id_agencia = v_parametros.id_agencia;

                if (v_tipo_agencia = 'noiata') then
                	v_tipo_auxiliar = 'Agencia No IATA';
                elsif (v_tipo_agencia = 'corporativa') then
                	v_tipo_auxiliar = 'Corporativo';
                end if;

                select count(*) into v_existencia_auxiliar
                from conta.tauxiliar auxi
                where auxi.codigo_auxiliar = v_codigo_int;

                if v_existencia_auxiliar = 0 then
                 --Sentencia de la insercion
                  insert into conta.tauxiliar(
                  --id_empresa,
                  estado_reg,
                  codigo_auxiliar,
                  nombre_auxiliar,
                  fecha_reg,
                  id_usuario_reg,
                  id_usuario_mod,
                  fecha_mod,
                  corriente,
                  tipo,
                  cod_antiguo
                  ) values(
                  --v_parametros.id_empresa,
                  'activo',
                  v_codigo_int,
                  v_nombre_agencia,
                  now(),
                  p_id_usuario,
                  null,
                  null,
                  --24-03-2021 (may) modificacion que se quite el campo y se registre todos como NO
                  --v_parametros.corriente
                  'si',
                  v_tipo_auxiliar,
                  null
                  );
                else
                	raise exception 'Ya existe una cuenta corriente registrada con el OfficeID: %, Favor verificar la información.',v_codigo_int;
                end if;






            end if;


            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Datos Actualizados Correctamente');
            v_resp = pxp.f_agrega_clave(v_resp,'id_agencia',v_parametros.id_agencia::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

        /*********************************
        #TRANSACCION:  'OBING_INS_AGEYCONT'
        #DESCRIPCION:	Insercion de agencia y contrato a traves  del protal corporativo
        #AUTOR:		Ismael Valdivia
        #FECHA:		19-08-2021 13:40:33
        ***********************************/

        elsif(p_transaccion='OBING_INS_AGEYCONT')then

            begin

            /*Control para verificar si el Office Id Ingresado existe*/
            /*Aumentando Control para no duplicar officeId*/
        	select count(ag.id_agencia)
            		into
                    v_existe_agencia
            from obingresos.tagencia ag
            where (trim(ag.codigo_int) = trim(v_parametros.office_id))
            limit 1;
        	/**********************************************/

            if (v_existe_agencia > 0) then

            	select ag.nombre
                       into
                        v_nombre_agencia
                from obingresos.tagencia ag
                where (trim(ag.codigo_int) = trim(v_parametros.office_id))
                limit 1;


            	raise exception 'No se pudo registrar la agencia ya que el OfficeID: %, esta relacionada a la Agencia % favor verifique la información',trim(v_parametros.office_id),v_nombre_agencia;
            else
            	/*Control de combinacion en formulario*/



                if (v_parametros.tipo_institucion is not null and v_parametros.tipo_institucion != '' and v_parametros.tipo_institucion != 'Null' and v_parametros.tipo_institucion != 'NULL' AND v_parametros.tipo_institucion != 'null') then
                    v_tipo_institucion = v_parametros.tipo_institucion;
                else
                    v_tipo_institucion = 'privada';
                end if;
                --Comentnado esta parte a pedido de Ever ya que solo se necesita controlar el de validar boleta
                /*if (v_parametros.formas_pago = 'postpago' and v_tipo_institucion = 'privada' and v_parametros.validar_boleta = 'no') then
                	raise exception 'Una Agencia con forma de pago postpago necesita registrar una boleta de garantia, y el dato de validar boleta se encuentra como %, favor verificarlo',v_parametros.validar_boleta;
                end if;*/

                 if (pxp.f_existe_parametro(p_tabla, 'codigo_noiata'))then
                 	if (v_parametros.codigo_noiata != '') then
                 		v_codigo_no_iata = v_parametros.codigo_noiata;
                    else
                    	v_codigo_no_iata = v_parametros.office_id;
                    end if;
                 else
                 	v_codigo_no_iata = v_parametros.office_id;
                 end if;


                 /*Validacion para que la combinacion de las agencias prepagos llegue como validar boleta no*/
                 if (trim(v_parametros.formas_pago) = 'prepago' and trim(v_parametros.validar_boleta) = 'si') then
                 	raise exception 'La combinación % con validar boleta %, no existe, favor verificar los datos',v_parametros.formas_pago,v_parametros.validar_boleta;
                 end if;
                 /*******************************************************************************************/

                --Sentencia de la insercion
                insert into obingresos.tagencia(
                id_moneda_control,
                tipo_cambio,
                codigo,
                tipo_agencia,
                codigo_int,
                nombre,
                tipo_pago,
                estado_reg,
                depositos_moneda_boleto,
                id_usuario_ai,
                id_usuario_reg,
                usuario_ai,
                fecha_reg,
                fecha_mod,
                id_usuario_mod,
                id_lugar,
                tipo_persona,
                boaagt,
                /*Aumentando este campo para diferencias si es Corporativa(publica,privada) Ismael Valdivia 04/08/2021*/
                tipo_institucion,
                codigo_noiata,
                /*Aumentando para control de Boleta de Garantia*/
                validar_boleta,
                monto_maximo_deuda,
                /***********************************************/
                /*Nuevos Campos Portal Agencias*/
                iata_status,
                osd,
                terciariza,
                business_name,
                representante_legal,
                pasaporte_ci,
                expedido
                /*******************************/
                ) values(
                param.f_get_moneda_base(),
                'venta',
                v_parametros.codigo,
                v_parametros.tipo_agencia,
                v_parametros.office_id,
                v_parametros.nombre,
                'postpago',
                'activo',
                'si',
                v_parametros._id_usuario_ai,
                p_id_usuario,
                v_parametros._nombre_usuario_ai,
                now(),
                null,
                null,
                (select id_lugar from param.tlugar where codigo = v_parametros.ciudad),
                v_parametros.tipo_persona,
                'B',
                /*Aumentando este campo para diferencias si es Corporativa(publica,privada) Ismael Valdivia 04/08/2021*/
                v_tipo_institucion,
                v_codigo_no_iata,
                /*Aumentando para control de Boleta de Garantia*/
                v_parametros.validar_boleta,
                0,--v_parametros.monto_maximo_deuda,
                v_parametros.iata_status,
                v_parametros.osd,
                v_parametros.terciariza,
                v_parametros.business_name,
                v_parametros.representante_legal,
                v_parametros.ci,
                v_parametros.expedido
                /***********************************************/
                )RETURNING id_agencia into v_id_agencia;

                /*Si se inserta correctamente la Agencia procedemos a Insertar la cuenta Corriente*/
				select ag.nombre,
                	   ag.codigo_int,
                       ag.tipo_agencia
                       into
                        v_nombre_agencia,
                        v_codigo_int,
                        v_tipo_agencia
                from obingresos.tagencia ag
                where ag.id_agencia = v_id_agencia;


                SELECT cat.descripcion
                	   into
                       v_tipo_auxiliar
                FROM param.tcatalogo_tipo tipcat
                inner join param.tcatalogo cat on cat.id_catalogo_tipo = tipcat.id_catalogo_tipo
                WHERE tipcat.nombre = 'Tipo Agencias' and tipcat.tabla = 'tagencia'
                and cat.codigo = v_tipo_agencia;


                select count(*) into v_existencia_auxiliar
                from conta.tauxiliar auxi
                where auxi.codigo_auxiliar = v_codigo_int;

                select count(*) into v_existencia_auxiliar_nombre
                from conta.tauxiliar auxi
                where UPPER(TRIM(auxi.nombre_auxiliar)) = UPPER(TRIM(v_nombre_agencia));


                select auxi.nombre_auxiliar into v_nombre_auxiliar
                from conta.tauxiliar auxi
                where auxi.codigo_auxiliar = v_codigo_int;


                 if v_existencia_auxiliar = 0 then

                 if (v_existencia_auxiliar_nombre > 0) then
                	raise exception 'El nombre %, ya esta registrado en los auxiliares',v_nombre_agencia;
                 end if;

                 if (v_nombre_auxiliar != v_nombre_agencia) then
                 	raise exception 'El nombre del auxiliar: <b>%</b> es distinto al nombre de la agencia: <b>%</b>',v_nombre_auxiliar,v_nombre_agencia;
                 end if;

                 --Sentencia de la insercion
                  insert into conta.tauxiliar(
                  --id_empresa,
                  estado_reg,
                  codigo_auxiliar,
                  nombre_auxiliar,
                  fecha_reg,
                  id_usuario_reg,
                  id_usuario_mod,
                  fecha_mod,
                  corriente,
                  tipo,
                  cod_antiguo
                  ) values(
                  --v_parametros.id_empresa,
                  'activo',
                  v_codigo_int,
                  UPPER(v_nombre_agencia),
                  now(),
                  p_id_usuario,
                  null,
                  null,
                  --24-03-2021 (may) modificacion que se quite el campo y se registre todos como NO
                  --v_parametros.corriente
                  'si',
                  v_tipo_auxiliar,
                  null
                  )RETURNING id_auxiliar into v_id_auxiliar;
                else
                	if (v_nombre_auxiliar != v_nombre_agencia) then
                 	  raise exception 'El nombre del auxiliar: <b>%</b> es distinto al nombre de la agencia: <b>%</b>',v_nombre_auxiliar,v_nombre_agencia;
                 	end if;
                end if;
			/**********************************************************************************/


            /*Insertamos el Contrato de la Agencia*/
            --11/12-2019 (Alan.felipez) revision si el numero de contrato ya se encuentra registrado
            perform leg.f_verificar_numero_contrato( 'contrato',v_parametros.numero_contrato, null,'insertar');


            select coalesce(max (id_contrato),0) into v_id_contrato
            from leg.tcontrato;


            SELECT
                 ps_num_tramite ,
                 ps_id_proceso_wf ,
                 ps_id_estado_wf ,
                 ps_codigo_estado
              into
                 v_num_tramite,
                 v_id_proceso_wf,
                 v_id_estado_wf,
                 v_codigo_estado

            FROM wf.f_inicia_tramite(
                 p_id_usuario,
                 v_parametros._id_usuario_ai,
                 v_parametros._nombre_usuario_ai,
                 (select po_id_gestion from param.f_get_periodo_gestion(now()::date)),
                 'CON',
                 NULL,
                 NULL,
                 NULL,
                 'CON-' ||v_id_contrato
                 );



                INSERT INTO
              leg.tcontrato
            (
              id_usuario_reg,
              id_usuario_ai,
              usuario_ai,
              id_estado_wf,
              id_proceso_wf,
              estado,
              tipo,
              id_gestion,
              id_agencia,
              monto,
              id_moneda,

              contrato_adhesion,

              id_funcionario,
              id_lugar,
              fecha_inicio,
              objeto,
              solicitud,
              numero,
              fecha_elaboracion,
              fecha_fin,
              tipo_agencia,
              formas_pago,
              moneda_restrictiva--,
              /*cuenta_bancaria1,
              entidad_bancaria1,
              nombre_cuenta_bancaria1,
              cuenta_bancaria2,
              entidad_bancaria2,
              nombre_cuenta_bancaria2*/
            )
            VALUES (
              p_id_usuario,
              v_parametros._id_usuario_ai,
              v_parametros._nombre_usuario_ai,
              v_id_estado_wf,
              v_id_proceso_wf,
              v_codigo_estado,
              'comercial',
              (select po_id_gestion from param.f_get_periodo_gestion(now()::date)),
              v_id_agencia,--v_parametros.id_agencia,
             0,
              param.f_get_moneda_base(),
              'no',

              v_parametros.id_funcionario,

              (select id_lugar from obingresos.tagencia where id_agencia = v_id_agencia),
              v_parametros.fecha_inicio,
              v_parametros.objeto,
              'Elaboracion de contrato comercial',
              v_parametros.numero_contrato,
              v_parametros.fecha_firma,
              v_parametros.fecha_fin,
              v_parametros.tipo_agencia,
              string_to_array(v_parametros.formas_pago, ','),
              v_parametros.moneda_restrictiva--,
              /*v_parametros.cuenta_bancaria1,
              v_parametros.entidad_bancaria1,
              v_parametros.nombre_cuenta_bancaria1,
              v_parametros.cuenta_bancaria2,
              v_parametros.entidad_bancaria2,
              v_parametros.nombre_cuenta_bancaria2*/

            )returning id_contrato into v_id_contrato;
                v_res_bool =  wf.f_inserta_documento_wf(p_id_usuario, v_id_proceso_wf, v_id_estado_wf);
            /**************************************/

            /*Aqui insertamos en la tabla leg.tanexo en caso que tenga boleta de garantia*/
              if (v_parametros.validar_boleta = 'si') then

                if (v_parametros.monto_boleta_garantia = '') then
                  raise exception 'Debe registrar un monto para la boleta de Garantia';
                end if;

                if (v_parametros.monto_boleta_garantia::numeric = 0) then
                  raise exception 'El monto de la boleta de garantia no puede ser 0';
                end if;


               /* INSERT INTO
                  leg.tanexo
                (
                  id_usuario_reg,
                  id_usuario_ai,
                  usuario_ai,
                  id_contrato,
                  tipo,
                  fecha_desde,
                  fecha_hasta,
                  monto,
                  moneda,
                  banco,
                  tipo_boleta

                )
                VALUES (
                  p_id_usuario,
                  v_parametros._id_usuario_ai,
                  v_parametros._nombre_usuario_ai,
                  v_id_contrato,
                  'boleta_garantia',
                  v_parametros.fecha_inicio,
                  v_parametros.fecha_fin,
                  v_parametros.monto,
                  v_parametros.moneda,
                  v_parametros.banco,
                  v_parametros.tipo_boleta


                )returning id_anexo into v_id_boleta;*/




                INSERT INTO leg.tanexo
                (
                  id_usuario_reg,
                  id_usuario_ai,
                  usuario_ai,
                  fecha_reg,
                  id_contrato,
                  tipo,
                  fecha_desde,--
                  fecha_hasta,--
                  tipo_boleta,--
                  moneda,--
                  monto,--
                  banco,--
                  nro_documento,
                  fecha_fin_uso
                )
                VALUES (
                  p_id_usuario,
                  v_parametros._id_usuario_ai,
                  v_parametros._nombre_usuario_ai,
                  now(),
                  v_id_contrato,
                  'boleta_garantia',
                  v_parametros.fecha_inicio_boleta,--contrato
                  v_parametros.fecha_fin_boleta,--contrato
                  'boleta',
                  v_parametros.moneda_boleta,--recibe el codigo internacional (BOB, USD)
                  v_parametros.monto_boleta_garantia::numeric,--nuevo_parametro
                  v_parametros.banco_boleta, --nuevo Parametro
                  v_parametros.nro_documento,
                  v_parametros.fecha_fin_uso

                )returning id_anexo into v_id_anexo;


                /*Recuperamos el ID Moneda para insertar en el movimiento entidad*/
                select mon.id_moneda into v_id_moneda
                from param.tmoneda mon
                where mon.codigo_internacional = v_parametros.moneda_boleta;
                /*****************************************************************/


              /*Insertamos en la tabla obingresos.tmovimiento_entidad*/
              INSERT INTO obingresos.tmovimiento_entidad
                (
                  id_usuario_reg,
                  id_usuario_ai,
                  usuario_ai,
                  fecha_reg,
                  tipo,
                  fecha,
                  monto,
                  id_moneda,
                  autorizacion__nro_deposito,
                  garantia,
                  ajuste,
                  id_periodo_venta,
                  id_agencia,
                  monto_total,
                  cierre_periodo,
                  observaciones

                )
                VALUES (
                  p_id_usuario,
                  v_parametros._id_usuario_ai,
                  v_parametros._nombre_usuario_ai,
                  now(),
                  'credito',
                  now()::date,
                  v_parametros.monto_boleta_garantia::numeric,
                  v_id_moneda, --Recuperar en base a lo que se envie en el anexo
                  'Boleta de garantía',
                  'si',
                  'no',
                  null,
                  v_id_agencia,
                  v_parametros.monto_boleta_garantia::numeric,
                  'no',
                  'Boleta de Garantia registrada desde el portal de Agencias'

                )returning id_movimiento_entidad into v_id_movimiento_entidad;


              end if;
            /*****************************************************************************/


			end if;



                --Definicion de la respuesta
                v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Se registró la Agencia, La cuenta corriente y el Contrato Exitosamente');
                v_resp = pxp.f_agrega_clave(v_resp,'id_agencia',v_id_agencia::varchar);
                v_resp = pxp.f_agrega_clave(v_resp,'id_auxiliar',v_id_auxiliar::varchar);
                v_resp = pxp.f_agrega_clave(v_resp,'id_contrato',v_id_contrato::varchar);
                v_resp = pxp.f_agrega_clave(v_resp,'id_anexo',v_id_anexo::varchar);
                v_resp = pxp.f_agrega_clave(v_resp,'id_movimiento_entidad',v_id_movimiento_entidad::varchar);


                --Devuelve la respuesta
                return v_resp;

            end;


        /*********************************
        #TRANSACCION:  'OBING_ENTIDAD_UPD'
        #DESCRIPCION:	Actualizacion de Entidad (Se llama desde un servicio del portal)
        #AUTOR:		Ismael Valdivia
        #FECHA:		22-09-2021 10:30:33
        ***********************************/

        elsif(p_transaccion='OBING_ENTIDAD_UPD')then

            begin
            		/*Aqui para las validacion en la cuenta Auxiliar*/

                    select
                          trim(ag.codigo_int),
                          ag.tipo_agencia,
                          ag.fecha_reg,
                          trim(ag.nombre)
                          into
                          v_officeId,
                          v_tipo_agencia,
                          v_fecha_reg_agencia,
                          v_nombre_agencia_actual
                    from obingresos.tagencia ag
                    where ag.id_agencia = v_parametros.id_agencia;

                    /*Control para que la agencia solo se pueda editar en cierto tiempo*/
                    select (now()::date - v_fecha_reg_agencia::Date) into v_diferencia_data;


                    if (v_diferencia_data >= 15) then
                    	raise exception 'El tiempo permitido de la modificación ya fue superado, por lo tanto no se puede realizar modificaciones';
                    end if;


                    /*******************************************************************/

                    SELECT cat.descripcion
                       into
                       v_tipo_auxiliar
                    FROM param.tcatalogo_tipo tipcat
                    inner join param.tcatalogo cat on cat.id_catalogo_tipo = tipcat.id_catalogo_tipo
                    WHERE tipcat.nombre = 'Tipo Agencias' and tipcat.tabla = 'tagencia'
                    and cat.codigo = v_tipo_agencia;

                    if (trim(v_parametros.nombre) != trim(v_nombre_agencia_actual)) then
                    	select count(aux.id_auxiliar) into v_existe_auxiliar
                        from conta.tauxiliar aux
                        where UPPER(TRIM(aux.nombre_auxiliar)) = UPPER(trim(v_parametros.nombre));
                    else
                    	select count(aux.id_auxiliar) into v_existe_auxiliar
                        from conta.tauxiliar aux
                        where UPPER(TRIM(aux.nombre_auxiliar)) = UPPER(trim(v_parametros.nombre))
                        and trim(aux.codigo_auxiliar) != trim(v_officeId);
                    end if;


                    select count(aux.id_auxiliar) into v_existe_auxiliar_officeID
                    from conta.tauxiliar aux
                    where trim(aux.codigo_auxiliar) = trim(v_officeId);


                    if (v_existe_auxiliar = 0) then

                    	if (v_existe_auxiliar_officeID = 1) then
                    		 update conta.tauxiliar set
                                    nombre_auxiliar = trim(v_parametros.nombre),
                                    id_usuario_mod = p_id_usuario,
                                    fecha_mod = now()
                              where trim(codigo_auxiliar) = trim(v_officeId);
                        /*En caso que no exista una cuenta auxiliar con el officeId crearemos*/
                        elsif (v_existe_auxiliar_officeID = 0) then
                        	insert into conta.tauxiliar(
                                                        --id_empresa,
                                                        estado_reg,
                                                        codigo_auxiliar,
                                                        nombre_auxiliar,
                                                        fecha_reg,
                                                        id_usuario_reg,
                                                        id_usuario_mod,
                                                        fecha_mod,
                                                        corriente,
                                                        tipo,
                                                        cod_antiguo
                                                        ) values(
                                                        --v_parametros.id_empresa,
                                                        'activo',
                                                        trim(v_officeId),
                                                        trim(v_parametros.nombre),
                                                        now(),
                                                        p_id_usuario,
                                                        null,
                                                        null,
                                                        'si',
                                                        v_tipo_auxiliar,
                                                        null
                                                        )RETURNING id_auxiliar into v_id_auxiliar;
                            elsif(v_existe_auxiliar_officeID > 1) then
                              	raise exception 'Existen dos cuentas axuliares con el Office Id: %, favor contactarse con personal de contabilidad.',trim(v_officeId);
                    	end if;

                    elsif (v_existe_auxiliar >= 1) then
                    	raise exception 'Ya existe la cuenta corriente con el nombre: %, y el nombre debe ser único en las cuentas corrientes.',trim(v_parametros.nombre);
                    end if;
                    /************************************************/


                    update obingresos.tagencia set
                          nombre = trim(v_parametros.nombre),
                          id_lugar = (select id_lugar from param.tlugar where codigo = upper(trim(v_parametros.ciudad))),
                          terciariza = trim(v_parametros.terciariza),
                          tipo_agencia = trim(v_parametros.tipo_agencia),
                          tipo_institucion = trim(v_parametros.tipo_institucion),
                          fecha_mod = now(),
                          id_usuario_mod = p_id_usuario
                    where id_agencia = v_parametros.id_agencia;


                --Definicion de la respuesta
                v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Datos Actualizados a la Entidad');
                v_resp = pxp.f_agrega_clave(v_resp,'id_agencia',v_parametros.id_agencia::varchar);

                --Devuelve la respuesta
                return v_resp;

            end;

            /*********************************
            #TRANSACCION:  'OBING_CONTRA_UPD'
            #DESCRIPCION:	Actualizacion de Entidad (Se llama desde un servicio del portal)
            #AUTOR:		Ismael Valdivia
            #FECHA:		22-09-2021 10:30:33
            ***********************************/

            elsif(p_transaccion='OBING_CONTRA_UPD')then

                begin
                		/*Recuperamos el id agencia relacionada al contrato*/
                        select con.id_agencia into v_id_agencia_recuperado
                        from leg.tcontrato con
                        where con.id_contrato = v_parametros.id_contrato
                        and con.estado_reg = 'activo';

                        /*Verificamos si la agencia esta activa*/
                        select count(ag.id_agencia),
                        	   ag.nombre
                        into
                        v_existe_agencia_activa,
                        v_nombre_agencia
                        from obingresos.tagencia ag
                        where ag.id_agencia = v_id_agencia_recuperado
                        and ag.estado_reg = 'activo'
                        group by ag.nombre;

                        if (v_existe_agencia_activa = 0) then
                        	raise exception 'El contrato que quiere modificar esta relacionado a la agencia: % que se encuentra en estado inactivo por lo tanto no es posible realizar la modificación',v_nombre_agencia;
                        end if;
                        /***************************************************/


                        /*Actualizamos los datos de la Agencia*/
                        update obingresos.tagencia set
                              business_name = trim(v_parametros.business_name),
                              nit = trim(v_parametros.nit),
                              representante_legal = trim(v_parametros.representante_legal),
                              pasaporte_ci = trim(v_parametros.ci),
                              expedido = trim(v_parametros.expedido),
                              tipo_persona = trim(v_parametros.tipo_persona),
                              fecha_mod = now(),
                              id_usuario_mod = p_id_usuario
                        where id_agencia = v_id_agencia_recuperado;
                        /**************************************/

                        --Sentencia de la eliminacion
                        update leg.tcontrato set
                              numero = trim(v_parametros.numero),
                              tipo = trim(v_parametros.tipo),
                              fecha_inicio = v_parametros.fecha_inicio::date,
                              fecha_fin = v_parametros.fecha_fin::date,
                              fecha_elaboracion = v_parametros.fecha_firma::date,
                              fecha_mod = now(),
                              id_usuario_mod = p_id_usuario
                        where id_contrato = v_parametros.id_contrato;


                    --Definicion de la respuesta
                    v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Datos Actualizados al contrato');
                    v_resp = pxp.f_agrega_clave(v_resp,'id_contrato',v_parametros.id_contrato::varchar);

                    --Devuelve la respuesta
                    return v_resp;

                end;

        /*********************************
        #TRANSACCION:  'OBING_OFICINA_UPD'
        #DESCRIPCION:	Actualizacion de Registros Oficina
        #AUTOR:		Ismael Valdivia
        #FECHA:		22-09-2021 13:17:33
        ***********************************/

        elsif(p_transaccion='OBING_OFICINA_UPD')then

            begin
            	 /*Control para verificar si no existe el officeId a actualizar en agencia*/
                 select count (ag.id_agencia) into v_existe_agencia
                 from obingresos.tagencia ag
                 where trim(ag.codigo_int) = trim(v_parametros.codigo_int)
                 and ag.id_agencia not in (v_parametros.id_agencia)
                 and ag.estado_reg = 'activo';


                 if (v_existe_agencia > 0) then
                 	select ag.nombre
                           into
                            v_nombre_agencia
                    from obingresos.tagencia ag
                    where trim(ag.codigo_int) = trim(v_parametros.codigo_int)
                    and ag.id_agencia not in (v_parametros.id_agencia)
                    limit 1;
                  raise exception 'La Agencia: %. ya tiene registrado el OfficeId: %, y no puede existir OfficeId duplicados.',trim(v_nombre_agencia),trim(v_parametros.codigo_int);
                 end if;
            	 /*************************************************************************/

                 /*Control para la cuenta Corriente de la agencia*/
                   select ag.codigo_int, ag.nombre, ag.tipo_agencia, ag.fecha_reg into v_office_id_actual, v_nombre_agencia_recu, v_tipo_agencia, v_fecha_reg_agencia
                   from obingresos.tagencia ag
                   where ag.id_agencia = v_parametros.id_agencia;

                   /*Control para que la agencia solo se pueda editar en cierto tiempo*/
                    select (now()::date - v_fecha_reg_agencia::Date) into v_diferencia_data;


                    if (v_diferencia_data >= 15) then
                    	raise exception 'El tiempo permitido de la modificación ya fue superado, por lo tanto no se puede realizar modificaciones';
                    end if;


                    /*******************************************************************/

                   /*Verificamos si el office Id tiene creado la cuenta corriente*/
                   select count(aux.id_auxiliar) into v_existe_auxiliar_officeID
                   from conta.tauxiliar aux
                   where trim(aux.codigo_auxiliar) = trim(v_office_id_actual);

                   /*Verificamos si el office Id tiene creado la cuenta corriente*/

                   if (trim(v_parametros.codigo_int) != trim(v_office_id_actual)) then
                      select count(aux.id_auxiliar) into v_existe_auxiliar_officeID_nuevo
                      from conta.tauxiliar aux
                      where trim(aux.codigo_auxiliar) = trim(v_parametros.codigo_int)
                      and trim(aux.codigo_auxiliar) != trim(v_office_id_actual);


                      if (v_existe_auxiliar_officeID_nuevo >= 1) then
                           raise exception 'El office Id: %, que intenta modificar ya existe en las cuentas corrientes, favor verificarlo',trim(v_parametros.codigo_int);
                      end if;
                   end if;



                   if (v_existe_auxiliar_officeID = 0) then

                   SELECT cat.descripcion
                       into
                       v_tipo_auxiliar
                    FROM param.tcatalogo_tipo tipcat
                    inner join param.tcatalogo cat on cat.id_catalogo_tipo = tipcat.id_catalogo_tipo
                    WHERE tipcat.nombre = 'Tipo Agencias' and tipcat.tabla = 'tagencia'
                    and cat.codigo = v_tipo_agencia;

                    /*Si no existe la cuenta corriente entonces la registramos con los nuevos datos*/

                     select count(aux.id_auxiliar) into v_existe_auxiliar
                     from conta.tauxiliar aux
                     where UPPER(trim(aux.nombre_auxiliar)) = UPPER(trim(v_nombre_agencia_recu))
                     and aux.estado_reg = 'activo'
                     and trim(aux.codigo_auxiliar) != trim(v_office_id_actual);

                     if(v_existe_auxiliar >= 1) then
                       raise exception 'Ya existe una cuenta corriente con el nombre: %, por lo tanto no se puede modificar la agencia ya que afectaria a la cuenta corriente',v_nombre_agencia_recu;
                     end if;


                   insert into conta.tauxiliar(
                                                        --id_empresa,
                                                        estado_reg,
                                                        codigo_auxiliar,
                                                        nombre_auxiliar,
                                                        fecha_reg,
                                                        id_usuario_reg,
                                                        id_usuario_mod,
                                                        fecha_mod,
                                                        corriente,
                                                        tipo,
                                                        cod_antiguo
                                                        ) values(
                                                        --v_parametros.id_empresa,
                                                        'activo',
                                                        trim(v_parametros.codigo_int),
                                                        UPPER(trim(v_nombre_agencia_recu)),
                                                        now(),
                                                        p_id_usuario,
                                                        null,
                                                        null,
                                                        'si',
                                                        v_tipo_auxiliar,
                                                        null
                                                        )RETURNING id_auxiliar into v_id_auxiliar;
    			   elsif (v_existe_auxiliar_officeID = 1) then

                    update conta.tauxiliar set
                          codigo_auxiliar = trim(v_parametros.codigo_int),
                          id_usuario_mod = p_id_usuario,
                          fecha_mod = now()
                    where trim(codigo_auxiliar) = trim(v_office_id_actual);

                   end if;
                   /*******************************************************************************/



                 /************************************************/

                 if (pxp.f_existe_parametro(p_tabla, 'codigo_noiata'))then
                 	if (v_parametros.codigo_noiata != '') then
                 		v_codigo_no_iata = v_parametros.codigo_noiata;
                    else
                    	v_codigo_no_iata = v_parametros.codigo_int;
                    end if;
                 else
                 	v_codigo_no_iata = v_parametros.codigo_int;
                 end if;


                  update obingresos.tagencia set
                  		 codigo_int= v_parametros.codigo_int,
                         codigo= v_parametros.codigo,
                         codigo_noiata = v_codigo_no_iata,
                         iata_status = v_parametros.iata_status,
                         osd = v_parametros.osd,
                         --credencial = v_parametros.credencial,
                         --key_credencial = v_parametros.key_credencial,
                         fecha_mod = now(),
                         id_usuario_mod = p_id_usuario
                  where id_agencia=v_parametros.id_agencia;


                --Definicion de la respuesta
                v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Datos Actualizados Correctamente');
                v_resp = pxp.f_agrega_clave(v_resp,'id_agencia',v_parametros.id_agencia::varchar);

                --Devuelve la respuesta
                return v_resp;

            end;



	else

    	raise exception 'Transaccion inexistente: %',p_transaccion;

	end if;

EXCEPTION

	WHEN OTHERS THEN
		v_resp='';
		v_resp = pxp.f_agrega_clave(v_resp,'mensaje',SQLERRM);
		v_resp = pxp.f_agrega_clave(v_resp,'codigo_error',SQLSTATE);
		v_resp = pxp.f_agrega_clave(v_resp,'procedimientos',v_nombre_funcion);
		raise exception '%',v_resp;

END;
$body$
LANGUAGE 'plpgsql'
VOLATILE
CALLED ON NULL INPUT
SECURITY INVOKER
COST 100;

ALTER FUNCTION obingresos.ft_agencia_ime (p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
  OWNER TO postgres;
