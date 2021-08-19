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
			id_usuario_mod
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
			null



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
            controlar_periodos_pago = v_parametros.controlar_periodos_pago
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
                where auxi.codigo_auxiliar = v_codigo_int or
                      auxi.nombre_auxiliar = v_nombre_agencia;

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
                end if;






            end if;


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
