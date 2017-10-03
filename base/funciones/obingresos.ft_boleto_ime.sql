CREATE OR REPLACE FUNCTION obingresos.ft_boleto_ime (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Ingresos
 FUNCION: 		obingresos.ft_boleto_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'obingresos.tboleto'
 AUTOR: 		 (jrivera)
 FECHA:	        06-01-2016 22:42:25
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
	v_id_boleto	integer;
    v_id_agencia			integer;
    v_fecha					date;
    v_id_moneda				integer;
    v_id_lugar_sucursal		integer;
    v_id_lugar_pais			integer;
    v_registros				record;
    v_id_impuesto			integer;
    v_tipdoc				varchar;
    v_rutas					varchar[];
    v_fp					varchar[];
    v_moneda_fp				varchar[];
    v_valor_fp				varchar[];
    v_forma_pago			varchar;
    v_posicion				integer;
    v_id_forma_pago			integer;
    v_agt					varchar;
    v_codigo_fp				varchar;
    v_res					varchar;
    v_id_moneda_sucursal	integer;
    v_id_moneda_usd			integer;
    v_cod_moneda_sucursal	varchar;
    v_tc					numeric;
    v_codigo_tarjeta		varchar;
    v_saldo_fp1				numeric;
    v_valor					numeric;
    v_saldo_fp2				numeric;
    v_ids					INTEGER[];
    v_boleto				record;
    v_suma_impuestos		numeric;
    v_vuelo					varchar;
    v_vuelos				varchar[];
    v_vuelo_fields			varchar[];
    v_mensaje				varchar;
    v_suma_tasas			numeric;
    v_cupon					integer;
    v_aux_separacion		VARCHAR[];
    v_codigo_pais			varchar;
    v_aux_string			varchar;
    v_fecha_llegada			date;
    v_fecha_hora_origen		timestamp;
    v_fecha_hora_destino	timestamp;
    v_fecha_hora_destino_ant timestamp;
    v_aeropuertos 			varchar[];
    v_retorno				varchar;
    v_id_boleto_vuelo		integer;

    v_valor_forma_pago		numeric;


    v_autorizacion_fp		varchar[];
    v_tarjeta_fp			varchar[];
    v_estado				varchar;
    v_id_usuario_cajero		integer;
    v_total					numeric;
    v_monto_total_fp		numeric;



BEGIN

    v_nombre_funcion = 'obingresos.ft_boleto_ime';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'OBING_BOL_INS'
 	#DESCRIPCION:	Insercion de registros
 	#AUTOR:		jrivera
 	#FECHA:		06-01-2016 22:42:25
	***********************************/

	if(p_transaccion='OBING_BOL_INS')then

        begin

        	--Sentencia de la insercion
        	insert into obingresos.tboleto(
			id_agencia,
			id_moneda_boleto,
			estado_reg,
			comision,
			fecha_emision,
			total,
			pasajero,
			monto_pagado_moneda_boleto,
			liquido,
			nro_boleto,
			id_usuario_reg,
			fecha_reg,
			usuario_ai,
			id_usuario_ai,
			id_usuario_mod,
			fecha_mod
          	) values(
			v_parametros.id_agencia,
			v_parametros.id_moneda_boleto,
			'activo',
			v_parametros.comision,
			v_parametros.fecha_emision,
			v_parametros.total,
			v_parametros.pasajero,
			v_parametros.monto_pagado_moneda_boleto,
			v_parametros.liquido,
			v_parametros.nro_boleto,
			p_id_usuario,
			now(),
			v_parametros._nombre_usuario_ai,
			v_parametros._id_usuario_ai,
			null,
			null



			)RETURNING id_boleto into v_id_boleto;

			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Boletos almacenado(a) con exito (id_boleto'||v_id_boleto||')');
            v_resp = pxp.f_agrega_clave(v_resp,'id_boleto',v_id_boleto::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

    /*********************************
 	#TRANSACCION:  'OBING_BOLVEN_UPD'
 	#DESCRIPCION:	Insercion de boleto por counter
 	#AUTOR:		jrivera
 	#FECHA:		06-01-2016 22:42:25
	***********************************/

	elsif(p_transaccion='OBING_BOLVEN_UPD')then

        begin
        	select fp.codigo into v_codigo_fp
        	from obingresos.tforma_pago fp
        	where fp.id_forma_pago = v_parametros.id_forma_pago;

            select * into v_boleto
            from obingresos.tboleto
            where id_boleto = v_parametros.id_boleto;
            v_mensaje = '';
            if (v_parametros.estado is null or v_parametros.estado = '') then
            	if (exists (select 1
                	from obingresos.tboleto
                	where id_boleto = v_parametros.id_boleto and estado is not null)) then
                	raise exception 'El boleto ya fue registrado';
                end if;

                v_mensaje = v_boleto.mensaje_error;

                update obingresos.tboleto set estado = 'borrador', id_punto_venta=v_parametros.id_punto_venta
                where id_boleto = v_parametros.id_boleto and estado is null;


            end if;

            update obingresos.tboleto set comision = v_parametros.comision
            where id_boleto = v_parametros.id_boleto;

            if (v_parametros.id_forma_pago is not null and v_parametros.id_forma_pago != 0) then
                delete from obingresos.tboleto_forma_pago
                where id_boleto = v_parametros.id_boleto;

                select fp.codigo into v_codigo_tarjeta
                from obingresos.tforma_pago fp
                where fp.id_forma_pago = v_parametros.id_forma_pago;

                v_codigo_tarjeta = (case when v_codigo_tarjeta like 'CC%' or v_codigo_tarjeta like 'SF%' then
                                        substring(v_codigo_tarjeta from 3 for 2)
                                else
                                      NULL
                              end);

                if (v_codigo_tarjeta is not null) then
                    if (substring(v_parametros.numero_tarjeta::varchar from 1 for 1) != 'X') then
                		v_res = pxp.f_valida_numero_tarjeta_credito(v_parametros.numero_tarjeta::varchar,v_codigo_tarjeta);
                	end if;
                end if;
                 --raise exception 'llega';
                INSERT INTO
                  obingresos.tboleto_forma_pago
                (
                  id_usuario_reg,
                  importe,
                  id_forma_pago,
                  id_boleto,
                  ctacte,
                  numero_tarjeta,
                  codigo_tarjeta,
                  tarjeta,
                  forma_pago_amadeus,
                  fp_amadeus_corregido,
                  id_usuario_fp_amadeus_corregido
                )
                VALUES (
                  p_id_usuario,
                  v_parametros.monto_forma_pago,
                  v_parametros.id_forma_pago,
                  v_parametros.id_boleto,
                  v_parametros.ctacte,
                  v_parametros.numero_tarjeta,
                  v_parametros.codigo_tarjeta,
                  v_codigo_tarjeta,
                  v_parametros.forma_pago_amadeus,
                  v_parametros.fp_amadeus_corregido,
                  p_id_usuario
                );

                if (v_parametros.id_forma_pago2 is not null and v_parametros.id_forma_pago2 != 0) then
                    select fp.codigo into v_codigo_tarjeta
                    from obingresos.tforma_pago fp
                    where fp.id_forma_pago = v_parametros.id_forma_pago2;

                    v_codigo_tarjeta = (case when v_codigo_tarjeta like 'CC%' or v_codigo_tarjeta like 'SF%' then
                                            substring(v_codigo_tarjeta from 3 for 2)
                                    else
                                          NULL
                                  end);
                    if (v_codigo_tarjeta is not null) then
                        v_res = pxp.f_valida_numero_tarjeta_credito(v_parametros.numero_tarjeta2,v_codigo_tarjeta);
                    end if;
                    INSERT INTO
                      obingresos.tboleto_forma_pago
                    (
                      id_usuario_reg,
                      importe,
                      id_forma_pago,
                      id_boleto,
                      ctacte,
                      numero_tarjeta,
                      codigo_tarjeta,
                      tarjeta
                    )
                    VALUES (
                      p_id_usuario,
                      v_parametros.monto_forma_pago2,
                      v_parametros.id_forma_pago2,
                      v_parametros.id_boleto,
                      v_parametros.ctacte2,
                      v_parametros.numero_tarjeta2,
                      v_parametros.codigo_tarjeta2,
                      v_codigo_tarjeta
                    );
                end if;
            end if;

            update obingresos.tboleto_vuelo
            set retorno = 'si'
            where id_boleto_vuelo = v_parametros.id_boleto_vuelo
            returning cupon into v_cupon;

            update obingresos.tboleto_vuelo
            set retorno = 'no'
            where id_boleto = v_parametros.id_boleto
            and cupon < v_cupon;

            update obingresos.tboleto_vuelo
            set retorno = 'si_sec'
            where id_boleto = v_parametros.id_boleto
            and cupon > v_cupon;



			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Boletos almacenado(a) con exito (id_boleto'||v_id_boleto||')');
            v_resp = pxp.f_agrega_clave(v_resp,'id_boleto',v_id_boleto::varchar);
			if (v_mensaje != '') then
            	v_resp = pxp.f_agrega_clave(v_resp,'alertas',v_mensaje::varchar);
            end if;
            --Devuelve la respuesta
            return v_resp;

		end;

    /*********************************
 	#TRANSACCION:  'OBING_MODFPGRUPO_UPD'
 	#DESCRIPCION:	Modifica la forma de pago de un grupo de boletos
 	#AUTOR:		jrivera
 	#FECHA:		06-01-2016 22:42:25
	***********************************/

	elsif(p_transaccion='OBING_MODFPGRUPO_UPD')then

        begin

        	v_saldo_fp1 = v_parametros.monto_forma_pago;
            v_saldo_fp2 = 	(case when v_parametros.monto_forma_pago2 is null then
            					0
            				else
                            	v_parametros.monto_forma_pago2
                            end);

            v_ids = string_to_array(v_parametros.ids_seleccionados,',');
            FOREACH v_id_boleto IN ARRAY v_ids
            LOOP
              delete from obingresos.tboleto_forma_pago
              where id_boleto = v_id_boleto;

            	if (v_saldo_fp1 > 0) then
              		v_valor = obingresos.f_monto_pagar_boleto(v_id_boleto,v_saldo_fp1,v_parametros.id_forma_pago );

                    v_saldo_fp1 = v_saldo_fp1 - v_valor;

                    select fp.codigo into v_codigo_tarjeta
                    from obingresos.tforma_pago fp
                    where fp.id_forma_pago = v_parametros.id_forma_pago;

                    v_codigo_tarjeta = (case when v_codigo_tarjeta like 'CC%' or v_codigo_tarjeta like 'SF%' then
                                            substring(v_codigo_tarjeta from 3 for 2)
                                    else
                                          NULL
                                  end);
                    if (v_codigo_tarjeta is not null) then
                        v_res = pxp.f_valida_numero_tarjeta_credito(v_parametros.numero_tarjeta,v_codigo_tarjeta);
                    end if;

                    INSERT INTO
                      obingresos.tboleto_forma_pago
                    (
                      id_usuario_reg,
                      importe,
                      id_forma_pago,
                      id_boleto,
                      ctacte,
                      numero_tarjeta,
                      tarjeta
                    )
                    VALUES (
                      p_id_usuario,
                      v_valor,
                      v_parametros.id_forma_pago,
                      v_id_boleto,
                      v_parametros.ctacte,
                      v_parametros.numero_tarjeta,
                      v_codigo_tarjeta
                    );
            	end if;
                if (v_saldo_fp2 > 0) then
              		v_valor = obingresos.f_monto_pagar_boleto(v_id_boleto,v_saldo_fp2,v_parametros.id_forma_pago2 );
             		v_saldo_fp2 = v_saldo_fp2 - v_valor;
                    select fp.codigo into v_codigo_tarjeta
                    from obingresos.tforma_pago fp
                    where fp.id_forma_pago = v_parametros.id_forma_pago2;

                    v_codigo_tarjeta = (case when v_codigo_tarjeta like 'CC%' or v_codigo_tarjeta like 'SF%' then
                                            substring(v_codigo_tarjeta from 3 for 2)
                                    else
                                          NULL
                                  end);
                    if (v_codigo_tarjeta is not null) then
                        v_res = pxp.f_valida_numero_tarjeta_credito(v_parametros.numero_tarjeta2,v_codigo_tarjeta);
                    end if;

                    INSERT INTO
                      obingresos.tboleto_forma_pago
                    (
                      id_usuario_reg,
                      importe,
                      id_forma_pago,
                      id_boleto,
                      ctacte,
                      numero_tarjeta,
                      tarjeta
                    )
                    VALUES (
                      p_id_usuario,
                      v_valor,
                      v_parametros.id_forma_pago2,
                      v_id_boleto,
                      v_parametros.ctacte2,
                      v_parametros.numero_tarjeta2,
                      v_codigo_tarjeta
                    );
            	end if;
                select obingresos.f_valida_boleto_fp(v_id_boleto) into v_res;


               update obingresos.tboleto
               set id_usuario_cajero = p_id_usuario,
               estado = 'pagado'
               where id_boleto=v_id_boleto;

               select * into v_boleto
               from obingresos.tboleto
               where id_boleto = v_id_boleto;

                --Si el usuario que cambia el estado del boleto a estado pagado no es cajero
                  --lanzamos excepcion
                  if (exists(	select 1
                                  from vef.tapertura_cierre_caja acc
                                  where acc.id_usuario_cajero = p_id_usuario and
                                  	acc.fecha_apertura_cierre = v_boleto.fecha_reg::date and
                                    acc.estado_reg = 'activo' and acc.estado = 'cerrado' and
                                    acc.id_punto_venta = v_boleto.id_punto_venta)) then
                      raise exception 'La caja ya fue cerrada, necesita tener la caja abierta para poder finalizar la venta del boleto';
                  end if;

                  if (not exists(	select 1
                                  from vef.tapertura_cierre_caja acc
                                  where acc.id_usuario_cajero = p_id_usuario and
                                  	acc.fecha_apertura_cierre = v_boleto.fecha_reg::date and
                                    acc.estado_reg = 'activo' and acc.estado = 'abierto' and
                                    acc.id_punto_venta = v_boleto.id_punto_venta)) then
                      raise exception 'Antes de emitir un boleto debe realizar una apertura de caja';
                  end if;

            END LOOP;

            if (v_saldo_fp1 > 0 or v_saldo_fp2 > 0) then
            	raise exception 'El monto total de las formas de pago es superior al monto de los boletos seleccionados:%,%',v_saldo_fp1,v_saldo_fp2;
            end if;

			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Forma de pago de Boletos modificado con exito (id_boletos'||v_parametros.ids_seleccionados||')');
            v_resp = pxp.f_agrega_clave(v_resp,'id_boleto',v_id_boleto::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

    /*********************************
 	#TRANSACCION:  'OBING_MODFPPNR_UPD'
 	#DESCRIPCION:	Modifica la forma de pago de un grupo de boletos PNR
 	#AUTOR:		Gonzalo Sarmiento Sejas
 	#FECHA:		23-08-2017
	***********************************/

	elsif(p_transaccion='OBING_MODFPPNR_UPD')then

        begin
        	raise notice 'llega 000';
        	v_saldo_fp1 = v_parametros.monto_forma_pago;
            /*v_saldo_fp2 = 	(case when v_parametros.monto_forma_pago2 is null then
            					0
            				else
                            	v_parametros.monto_forma_pago2
                            end);*/

            --v_ids = string_to_array(v_parametros.ids_seleccionados,',');
            FOR v_id_boleto IN (select id_boleto
									 from obingresos.tboleto
									 where localizador=v_parametros.localizador)LOOP
              delete from obingresos.tboleto_forma_pago
              where id_boleto = v_id_boleto;

              --raise notice 'valores % % % ',v_id_boleto,v_saldo_fp1,v_parametros.id_forma_pago;
            	if (v_saldo_fp1 > 0) then
              		v_valor = obingresos.f_monto_pagar_boleto(v_id_boleto,v_saldo_fp1,v_parametros.id_forma_pago);
             		v_saldo_fp1 = v_saldo_fp1 - v_valor;
                    select fp.codigo into v_codigo_tarjeta
                    from obingresos.tforma_pago fp
                    where fp.id_forma_pago = v_parametros.id_forma_pago;

                    v_codigo_tarjeta = (case when v_codigo_tarjeta like 'CC%' or v_codigo_tarjeta like 'SF%' then
                                            substring(v_codigo_tarjeta from 3 for 2)
                                    else
                                          NULL
                                  end);
                    if (v_codigo_tarjeta is not null) then
                        v_res = pxp.f_valida_numero_tarjeta_credito(v_parametros.numero_tarjeta::varchar,v_codigo_tarjeta);
                    end if;

                    INSERT INTO
                      obingresos.tboleto_forma_pago
                    (
                      id_usuario_reg,
                      importe,
                      id_forma_pago,
                      id_boleto,
                      ctacte,
                      numero_tarjeta,
                      tarjeta
                    )
                    VALUES (
                      p_id_usuario,
                      v_valor,
                      v_parametros.id_forma_pago,
                      v_id_boleto,
                      v_parametros.ctacte,
                      v_parametros.numero_tarjeta,
                      v_codigo_tarjeta
                    );
            	end if;
                /*if (v_saldo_fp2 > 0) then
              		v_valor = obingresos.f_monto_pagar_boleto(v_id_boleto,v_saldo_fp2,v_parametros.id_forma_pago2 );
             		v_saldo_fp2 = v_saldo_fp2 - v_valor;
                    select fp.codigo into v_codigo_tarjeta
                    from obingresos.tforma_pago fp
                    where fp.id_forma_pago = v_parametros.id_forma_pago2;

                    v_codigo_tarjeta = (case when v_codigo_tarjeta like 'CC%' or v_codigo_tarjeta like 'SF%' then
                                            substring(v_codigo_tarjeta from 3 for 2)
                                    else
                                          NULL
                                  end);
                    if (v_codigo_tarjeta is not null) then
                        v_res = pxp.f_valida_numero_tarjeta_credito(v_parametros.numero_tarjeta2,v_codigo_tarjeta);
                    end if;

                    INSERT INTO
                      obingresos.tboleto_forma_pago
                    (
                      id_usuario_reg,
                      importe,
                      id_forma_pago,
                      id_boleto,
                      ctacte,
                      numero_tarjeta,
                      tarjeta
                    )
                    VALUES (
                      p_id_usuario,
                      v_valor,
                      v_parametros.id_forma_pago2,
                      v_id_boleto,
                      v_parametros.ctacte2,
                      v_parametros.numero_tarjeta2,
                      v_codigo_tarjeta
                    );
            	end if;
                */
                	raise notice 'llega 2';
                select obingresos.f_valida_boleto_fp(v_id_boleto) into v_res;
raise notice 'llega 0';
               update obingresos.tboleto
               set id_usuario_cajero = p_id_usuario,
               estado = 'pagado'
               where id_boleto=v_id_boleto;
               raise notice 'llega 1';
               select * into v_boleto
               from obingresos.tboleto
               where id_boleto = v_id_boleto;
               raise notice 'llega 2';
                --Si el usuario que cambia el estado del boleto a estado pagado no es cajero
                  --lanzamos excepcion
                  if (exists(	select 1
                                  from vef.tapertura_cierre_caja acc
                                  where acc.id_usuario_cajero = p_id_usuario and
                                  	acc.fecha_apertura_cierre = v_boleto.fecha_reg::date and
                                    acc.estado_reg = 'activo' and acc.estado = 'cerrado' and
                                    acc.id_punto_venta = v_boleto.id_punto_venta)) then
                      raise exception 'La caja ya fue cerrada, necesita tener la caja abierta para poder finalizar la venta del boleto';
                  end if;

                  if (not exists(	select 1
                                  from vef.tapertura_cierre_caja acc
                                  where acc.id_usuario_cajero = p_id_usuario and
                                  	acc.fecha_apertura_cierre = v_boleto.fecha_reg::date and
                                    acc.estado_reg = 'activo' and acc.estado = 'abierto' and
                                    acc.id_punto_venta = v_boleto.id_punto_venta)) then
                      raise exception 'Antes de emitir un boleto debe realizar una apertura de caja';
                  end if;

            END LOOP;

            if (v_saldo_fp1 > 0) then
            	raise exception 'El monto total de las formas de pago es superior al monto de los boletos seleccionados, excendente de: % ',v_saldo_fp1;
            end if;
			--raise exception 'llega %, % ',v_saldo_fp1,v_parametros.id_forma_pago;
			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Forma de pago de Boletos modificado con exito (PNR: '||v_parametros.localizador||')');
            v_resp = pxp.f_agrega_clave(v_resp,'pnr',v_parametros.localizador::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

    /*********************************
 	#TRANSACCION:  'OBING_BOLSERV_INS'
 	#DESCRIPCION:	Insercion de boletos desde servicio REST de Resiber
 	#AUTOR:		jrivera
 	#FECHA:		06-01-2016 22:42:25
	***********************************/

	elsif(p_transaccion='OBING_BOLSERV_INS')then

        begin


        	select ag.id_agencia,ag.codigo,sm.id_moneda,suc.id_lugar,mon.codigo_internacional
            	into v_id_agencia,v_agt,v_id_moneda_sucursal,v_id_lugar_sucursal,v_cod_moneda_sucursal
            from vef.tpunto_venta pv
            inner join obingresos.tagencia ag on ag.codigo = pv.codigo
            inner join vef.tsucursal suc on suc.id_sucursal = pv.id_sucursal
            inner join vef.tsucursal_moneda sm on sm.id_sucursal = suc.id_sucursal
            									and sm.tipo_moneda = 'moneda_base'
            inner join param.tmoneda mon on mon.id_moneda = sm.id_moneda
            where pv.id_punto_venta = v_parametros.id_punto_venta;

            select m.id_moneda into v_id_moneda
            from param.tmoneda m
            where m.codigo_internacional = v_parametros.moneda;



            select m.id_moneda into v_id_moneda_usd
            from param.tmoneda m
            where m.codigo_internacional = 'USD';

            if (length(v_parametros.fecha_emision) = 5) then
            	v_parametros.fecha_emision = v_parametros.fecha_emision || to_char(now(),'YY');
            end if;

            select to_date(v_parametros.fecha_emision, 'DDMONYY') into v_fecha;


            v_tc = (select param.f_get_tipo_cambio_v2(v_id_moneda_sucursal,v_id_moneda_usd,v_fecha,'O'));

            if (v_tc is null) then
            	raise exception 'No existe tipo de cambio para la moneda USD,  en la fecha %',v_fecha;
            end if;

            if (v_id_lugar_sucursal is null) then
            	raise exception 'El punto de venta con el que esta logueado en este momento no tiene un lugar asignado. Comuniquese con el administrador';
            end if;

            v_id_lugar_pais = (select param.f_get_id_lugar_pais(v_id_lugar_sucursal));

            if (v_id_lugar_pais is null) then
            	raise exception 'El punto de venta con el que esta logueado en este momento no tiene un pais relacionado. Comuniquese con el administrador';
            end if;

            select l.codigo into v_codigo_pais
            from param.tlugar l
            where id_lugar = v_id_lugar_pais;

            if (position(v_codigo_pais in pxp.f_get_variable_global('obingresos_remover_q_pais')) > 0 ) then
            	v_aux_separacion = string_to_array(v_parametros.fare_calc,' ');
                FOREACH v_aux_string IN ARRAY v_aux_separacion LOOP

                    if (substring(v_aux_string from 1 for 1) = 'Q' and pxp.f_is_positive_integer(substring(v_aux_string from 2 for 1))) THEN
                    	v_parametros.fare_calc = replace(v_parametros.fare_calc, v_aux_string || ' ', '');
                    end if;
                END LOOP;
            end if;

            v_rutas = string_to_array(v_parametros.rutas,'#');
            v_tipdoc = 'ETN';

            if (pxp.f_existe_parametro(p_tabla,'id_boleto') = TRUE) then
            	if (	(select tipdoc
                		from obingresos.tboleto
                        where id_boleto = v_parametros.id_boleto) = 'ETI') then
            		v_tipdoc = 'ETI';
            	end if;
            end if;
            if (exists (select 1
            			from obingresos.taeropuerto a
                        where a.codigo = ANY(v_rutas) and a.estado_reg = 'activo' and a.tipo_nalint ='I')) then

                v_tipdoc = 'ETI';
            end if;
           select nextval('obingresos.tboleto_id_boleto_seq'::regclass) into v_id_boleto;

        	--Sentencia de la insercion
        	insert into obingresos.tboleto(
            id_boleto,
			id_agencia,
			id_moneda_boleto,
			estado_reg,
			comision,
			fecha_emision,
			total,
			pasajero,
			monto_pagado_moneda_boleto,
			liquido,
			nro_boleto,
			id_usuario_reg,
			fecha_reg,
			usuario_ai,
			id_usuario_ai,
			id_usuario_mod,
			fecha_mod,
            neto,
            endoso,
            origen,
            destino,
            cupones,
            tipdoc,
            moneda,
            agt,
            retbsp,
            tc,
            moneda_sucursal,
            id_punto_venta,
            ruta_completa,
            localizador,
            identificacion,
            fare_calc
          	) values(
            v_id_boleto,
			v_id_agencia,
			v_id_moneda,
			'activo',
			0,
			v_fecha,
			v_parametros.total,
			v_parametros.pasajero,
			0,
			v_parametros.total,
			v_parametros.nro_boleto,
			p_id_usuario,
			now(),
			v_parametros._nombre_usuario_ai,
			v_parametros._id_usuario_ai,
			null,
			null,
            v_parametros.neto,
            v_parametros.endoso,
            v_parametros.origen,
            v_parametros.destino,
            v_parametros.cupones,
			v_tipdoc,
            v_parametros.moneda,
            v_agt,
            'RET',
            v_tc,
            v_cod_moneda_sucursal,
            v_parametros.id_punto_venta,
            v_parametros.ruta_completa,
            v_parametros.localizador,
            v_parametros.identificacion,
            v_parametros.fare_calc
			);

            v_mensaje = '';
            v_suma_impuestos = 0;
            for v_registros in (select out_impuesto,out_valor
            					from obingresos.f_get_impuestos_from_cadena(v_parametros.impuestos))LOOP

                if (v_registros.out_impuesto = 'ERROR') THEN
                	v_mensaje = v_mensaje  || 'Error en la definicion del calculo tarifario XT <br>';
                end if;

                v_id_impuesto = NULL;
                select id_impuesto into v_id_impuesto
                from obingresos.timpuesto i
                where i.codigo = v_registros.out_impuesto and i.id_lugar = v_id_lugar_pais
                and i.tipodoc = v_tipdoc;

                if (v_id_impuesto is null) THEN
                	v_mensaje = v_mensaje  || 'No se encontro un impuesto parametrizado para : ' || v_registros.out_impuesto || ' ,pais:' || v_id_lugar_pais || ' , tipdoc,' || v_tipdoc || '<br>';
                end if;

                INSERT INTO
                  obingresos.tboleto_impuesto
                (
                  id_usuario_reg,
                  importe,
                  id_impuesto,
                  id_boleto,
                  calculo_tarifa
                )
                VALUES (
                  p_id_usuario,
                  v_registros.out_valor,
                  v_id_impuesto,
                  v_id_boleto,
                  'si'
                );
                v_suma_impuestos = v_suma_impuestos + v_registros.out_valor;

            end loop;

             v_suma_tasas = 0;
            for v_registros in (select out_impuesto,out_valor
            					from obingresos.f_get_impuestos_from_cadena(v_parametros.tasas))LOOP

                v_id_impuesto = NULL;
                select id_impuesto into v_id_impuesto
                from obingresos.timpuesto i
                where i.codigo = v_registros.out_impuesto and i.id_lugar = v_id_lugar_pais
                and i.tipodoc = v_tipdoc;

                if (v_id_impuesto is null and v_registros.out_impuesto != 'XT') THEN
                	raise exception 'No se encontro un impuesto parametrizado para : % ,pais:% , tipdoc,%',v_registros.out_impuesto,v_id_lugar_pais,v_tipdoc;
                end if;
                if (v_registros.out_impuesto != 'XT') then
                    INSERT INTO
                      obingresos.tboleto_impuesto
                    (
                      id_usuario_reg,
                      importe,
                      id_impuesto,
                      id_boleto
                    )
                    VALUES (
                      p_id_usuario,
                      v_registros.out_valor,
                      v_id_impuesto,
                      v_id_boleto
                    );
                else
                	update obingresos.tboleto
                    set xt = v_registros.out_valor
                    where id_boleto = v_id_boleto;

                    if (v_suma_impuestos != v_registros.out_valor) THEN
                		v_mensaje = v_mensaje  || 'La suma de las tasas/impuestos definidos en el calculo tarifario XT : ' || v_suma_impuestos || ' ,no es igual al valor de la tasa XT :' || v_registros.out_valor || '<br>';
                	end if;
                end if;
                v_suma_tasas = v_suma_tasas + v_registros.out_valor;

            end loop;

            --if (v_suma_tasas + v_parametros.neto != v_parametros.total) then
            --	raise exception 'El importe total del boleto no es igual a la suma del neto y las tasas/impuestos%',v_suma_tasas;
            --end if;

            v_fp = string_to_array(substring(v_parametros.fp from 2),'#');
            v_moneda_fp = string_to_array(substring(v_parametros.moneda_fp from 2),'#');
            v_valor_fp = string_to_array(substring(v_parametros.valor_fp from 2),'#');
            v_autorizacion_fp = string_to_array(substring(v_parametros.autorizacion_fp from 2),'#');
            v_tarjeta_fp = string_to_array(substring(v_parametros.tarjeta_fp from 2),'#');
            v_posicion = 1;
            FOREACH v_forma_pago IN ARRAY v_fp
            LOOP
            	v_id_forma_pago = NULL;
                select id_forma_pago into v_id_forma_pago
                from obingresos.tforma_pago fp
                inner join param.tmoneda m on m.id_moneda = fp.id_moneda
                where fp.codigo = v_forma_pago and
                	m.codigo_internacional = (case when v_moneda_fp[v_posicion] = '' or v_moneda_fp[v_posicion] is null then v_parametros.moneda else v_moneda_fp[v_posicion] end)   and
                    fp.id_lugar = v_id_lugar_pais;

                if (v_id_forma_pago is null) then
                	raise exception 'No existe la forma de pago:%,%,%',v_forma_pago,v_moneda_fp[v_posicion],v_id_lugar_pais;
                end if;

            	 INSERT INTO
                    obingresos.tboleto_forma_pago
                  (
                    id_usuario_reg,
                    importe,
                    id_forma_pago,
                    id_boleto,
                    numero_tarjeta,
                    codigo_tarjeta

                  )
                  VALUES (
                    p_id_usuario,
                    v_valor_fp[v_posicion]::numeric,
                    v_id_forma_pago,
                    v_id_boleto,
                    v_tarjeta_fp[v_posicion],
                    v_autorizacion_fp[v_posicion]
                  );

                  v_posicion = v_posicion + 1;
            END LOOP;
            --vuelos 1
            v_vuelos = string_to_array(v_parametros.vuelos,'$$$');

             --Boleto en conjuncion
          if (pxp.f_existe_parametro(p_tabla,'id_boleto') = TRUE) then
              	select max(cupon) + 1 into v_cupon
                from obingresos.tboleto_vuelo bv
                where id_boleto = v_parametros.id_boleto;

           end if;
           if (v_cupon is null) then
           		v_cupon = 1;
           end if;

            FOREACH v_vuelo IN ARRAY v_vuelos
            LOOP
            	 v_vuelo_fields = string_to_array(v_vuelo,'|');


            	 INSERT INTO
                    obingresos.tboleto_vuelo
                  (
                    id_usuario_reg,
                    id_boleto,
                    vuelo,
                    hora_origen,
                    id_aeropuerto_origen,
                    id_aeropuerto_destino,
                    tarifa,
                    equipaje,
                    status,
                    cupon,
                    aeropuerto_origen,
                    aeropuerto_destino,
                    clase,
                    flight_status,
                    linea
                  )
                  VALUES (
                    p_id_usuario,
                    v_id_boleto,
                    v_vuelo_fields[2],
                    to_timestamp(v_vuelo_fields[3],'HH24MI')::time,
                    (select id_aeropuerto from obingresos.taeropuerto a where a.codigo = v_vuelo_fields[4]),
                    (select id_aeropuerto from obingresos.taeropuerto a where a.codigo = v_vuelo_fields[5]),
                    v_vuelo_fields[6],
                    v_vuelo_fields[7],
                    v_vuelo_fields[8],
                    v_cupon,
                    v_vuelo_fields[4],
                    v_vuelo_fields[5],
                    v_vuelo_fields[9],
                    'OK',
                    v_vuelo_fields[11]

                  )returning id_boleto_vuelo into v_id_boleto_vuelo;

                  update obingresos.tboleto_vuelo
                  set validez_tarifa = obingresos.f_get_validez_tarifaria(v_id_boleto_vuelo)
                  where id_boleto_vuelo = v_id_boleto_vuelo;
                  v_cupon = v_cupon +1;

            END LOOP;

            --vuelos 2

            v_vuelos = string_to_array(v_parametros.vuelos2,'$$$');
            v_retorno = 'no';
            FOREACH v_vuelo IN ARRAY v_vuelos
            LOOP

            	 v_vuelo_fields = string_to_array(v_vuelo,'|');
                 if (v_vuelo_fields[2] = ANY(v_aeropuertos) and v_retorno = 'no') then
                 	v_retorno = 'si';
                 elsif (v_retorno = 'no') then
                 	v_aeropuertos = array_append(v_aeropuertos, v_vuelo_fields[1]);
                 else
                 	v_retorno = 'si_sec';
                 end if;
                 v_fecha =to_date(v_vuelo_fields[3] , 'DDMONYY');
                 if (position('+1' in v_vuelo_fields[5]) > 0) then
                 	v_fecha_llegada = v_fecha + interval '1 day';
                    v_vuelo_fields[5] = replace(v_vuelo_fields[5], '+1', '');
                 else
                 	v_fecha_llegada = v_fecha;
                 end if;

                 v_fecha_hora_origen = to_timestamp(to_char(v_fecha,'DD/MM/YYYY') || ' ' || v_vuelo_fields[4], 'DD/MM/YYYY HH24MI');
                 v_fecha_hora_destino = to_timestamp(to_char(v_fecha_llegada,'DD/MM/YYYY') || ' ' || v_vuelo_fields[5], 'DD/MM/YYYY HH24MI');


                 update obingresos.tboleto_vuelo
                 set fecha_hora_origen =  v_fecha_hora_origen,
                 fecha_hora_destino =  v_fecha_hora_destino,
                 retorno = v_retorno,
                 tiempo_conexion = EXTRACT('epoch' FROM v_fecha_hora_origen - v_fecha_hora_destino_ant ) / 60
                 where id_boleto = v_id_boleto and
                 id_aeropuerto_origen = (select id_aeropuerto from obingresos.taeropuerto a where a.codigo = v_vuelo_fields[1]) and
                 id_aeropuerto_destino = (select id_aeropuerto from obingresos.taeropuerto a where a.codigo = v_vuelo_fields[2])
                 returning cupon into v_cupon;

                 v_fecha_hora_destino_ant = v_fecha_hora_destino;



            END LOOP;

            --Boleto en conjuncion
            if (pxp.f_existe_parametro(p_tabla,'id_boleto') = TRUE) then
            	if (v_parametros.id_boleto is not null) then
                	if not EXISTS(	select 1
                    				from obingresos.tboleto b
                                    where id_boleto = v_parametros.id_boleto and
                                    	b.total = v_parametros.total and b.pasajero = v_parametros.pasajero) then
                    	raise exception 'No se encontro un boleto en conjuncion para este billete';
                    else
                    	update obingresos.tboleto set tiene_conjuncion = 'si',
                        destino = v_parametros.destino,
                        ruta_completa = ruta_completa || '-' || substr (v_parametros.ruta_completa,5)
                        where id_boleto = v_parametros.id_boleto;

                        update obingresos.tboleto_vuelo
                        	set id_boleto_conjuncion = v_parametros.id_boleto
                        where id_boleto = v_id_boleto;

                        update obingresos.tboleto
                        	set id_boleto_conjuncion = v_parametros.id_boleto
                        where id_boleto = v_id_boleto;
                    end if;
                end if;
            end if;

            if (v_mensaje != '') then
            	update obingresos.tboleto
                    set mensaje_error = v_mensaje
                where id_boleto = v_id_boleto;
            end if;

			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Boletos almacenado(a) con exito (id_boleto'||v_id_boleto||')');
            v_resp = pxp.f_agrega_clave(v_resp,'id_boleto',v_id_boleto::varchar);


            --Devuelve la respuesta
            return v_resp;

		end;

    /*********************************
 	#TRANSACCION:  'OBING_BOLSERVAMA_INS'
 	#DESCRIPCION:	Insercion de boletos desde servicio REST de Amadeus
 	#AUTOR:		Gonzalo Sarmiento
 	#FECHA:		19-07-2016
	***********************************/

	elsif(p_transaccion='OBING_BOLSERVAMA_INS')then

        begin

            IF NOT EXISTS(SELECT 1
            			  FROM obingresos.tboleto
            			  WHERE nro_boleto=v_parametros.nro_boleto)THEN

                SELECT id_moneda into v_id_moneda
                FROM param.tmoneda
                WHERE codigo_internacional=v_parametros.moneda;

                select nextval('obingresos.tboleto_id_boleto_seq'::regclass) into v_id_boleto;

                INSERT INTO obingresos.tboleto
                (nro_boleto,
                total,
                comision,
                moneda,
                voided,
                estado,
                id_punto_venta,
                localizador,
                fecha_emision,
                id_moneda_boleto,
                pasajero,
                liquido,
                neto,
                xt,
                monto_pagado_moneda_boleto,
                id_usuario_reg,
                id_boleto,
                agente_venta,
                id_agencia
                )VALUES(v_parametros.nro_boleto::varchar,
                v_parametros.total::numeric,
                v_parametros.comision::numeric,
                v_parametros.moneda::varchar,
                v_parametros.voided::varchar,
                'borrador',
                v_parametros.id_punto_venta,
                v_parametros.localizador::varchar,
                v_parametros.fecha_emision::date,
                v_id_moneda,
                v_parametros.pasajero::varchar,
                v_parametros.liquido::numeric,
                v_parametros.neto::numeric,
                0,
                0.00,
                p_id_usuario,
                v_id_boleto,
                v_parametros.agente_venta,
                v_parametros.id_agencia
                );

                if(trim(v_parametros.fp)='')then
                	v_forma_pago='CA';
            	else
                	v_forma_pago=v_parametros.fp;
                end if;

                SELECT id_forma_pago into v_id_forma_pago
                FROM obingresos.tforma_pago
                WHERE codigo=v_forma_pago AND id_moneda=v_id_moneda;

				/*if(trim(v_parametros.fp)='')then
                	v_valor_forma_pago=0;
            	else
                	v_valor_forma_pago=v_parametros.valor_fp;
                end if;*/

                INSERT INTO obingresos.tboleto_forma_pago
                (id_usuario_reg,
                id_boleto,
                id_forma_pago,
                importe,
                forma_pago_amadeus
                )
                VALUES(
                p_id_usuario,
                v_id_boleto,
                v_id_forma_pago,
                v_parametros.valor_fp,
                v_parametros.forma_pago_amadeus
                );

                IF EXISTS(
                SELECT 1
                FROM obingresos.tpnr_forma_pago
                WHERE pnr=v_parametros.localizador
                AND id_forma_pago=v_id_forma_pago
                )THEN
                	UPDATE
                    obingresos.tpnr_forma_pago
                    SET
                    importe=importe+v_parametros.valor_fp
                    WHERE pnr=v_parametros.localizador and
                    id_forma_pago=v_id_forma_pago;
                ELSE
                	INSERT INTO obingresos.tpnr_forma_pago
                    (pnr, id_forma_pago, importe, forma_pago_amadeus)
                    VALUES(v_parametros.localizador, v_id_forma_pago, v_parametros.valor_fp, v_parametros.forma_pago_amadeus);
                END IF;

            	--Definicion de la respuesta
				v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Boletos almacenado(a) con exito (id_boleto'||v_id_boleto||')');
            	v_resp = pxp.f_agrega_clave(v_resp,'id_boleto',v_id_boleto::varchar);
            ELSE
            	--Definicion de la respuesta
				v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Boleto '||v_parametros.nro_boleto||' ya se encuentraba registrado');
            END IF;

            --Devuelve la respuesta
            return v_resp;

        end;

	/*********************************
 	#TRANSACCION:  'OBING_ACTBOLAMA_INS'
 	#DESCRIPCION:	Actualizacion de boletos desde servicio REST de Amadeus
 	#AUTOR:		Gonzalo Sarmiento
 	#FECHA:		10-08-2017
	***********************************/

	elsif(p_transaccion='OBING_ACTBOLAMA_INS')then

        begin

            IF EXISTS(SELECT 1
            			  FROM obingresos.tboleto
            			  WHERE nro_boleto=v_parametros.nro_boleto
                          AND id_punto_venta=v_parametros.id_punto_venta
                          AND id_usuario_cajero=v_parametros.id_usuario_cajero
                          AND estado='pagado')THEN

            	UPDATE obingresos.tboleto
                SET voided=v_parametros.voided
                WHERE nro_boleto=v_parametros.nro_boleto
                          AND id_punto_venta=v_parametros.id_punto_venta
                          AND id_usuario_cajero=v_parametros.id_usuario_cajero
                          AND estado='pagado';

            	--Definicion de la respuesta
				v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Boletos actualizado(a) con exito (nro_boleto'||v_parametros.nro_boleto||')');
            	v_resp = pxp.f_agrega_clave(v_resp,'id_boleto',v_id_boleto::varchar);
            ELSE
            	IF EXISTS(SELECT 1
            			  FROM obingresos.tboleto
            			  WHERE nro_boleto=v_parametros.nro_boleto
                          AND id_punto_venta=v_parametros.id_punto_venta
                          AND id_usuario_cajero=v_parametros.id_usuario_cajero
                          AND estado='borrador')THEN

	            	--Definicion de la respuesta
					v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Boleto '||v_parametros.nro_boleto||' no se encuentra en estado pagado');
                ELSE
                	v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Boleto '||v_parametros.nro_boleto||' no se encuentra en la base de datos ERPBOA.');

                END IF;
            END IF;

            --Devuelve la respuesta
            return v_resp;

        end;

	/*********************************
 	#TRANSACCION:  'OBING_BOL_MOD'
 	#DESCRIPCION:	Modificacion de registros
 	#AUTOR:		jrivera
 	#FECHA:		06-01-2016 22:42:25
	***********************************/

	elsif(p_transaccion='OBING_BOL_MOD')then

		begin
			--Sentencia de la modificacion
			update obingresos.tboleto set
			id_agencia = v_parametros.id_agencia,
			id_moneda_boleto = v_parametros.id_moneda_boleto,
			comision = v_parametros.comision,
			fecha_emision = v_parametros.fecha_emision,
			total = v_parametros.total,
			pasajero = v_parametros.pasajero,
			monto_pagado_moneda_boleto = v_parametros.monto_pagado_moneda_boleto,
			liquido = v_parametros.liquido,
			nro_boleto = v_parametros.nro_boleto,
			id_usuario_mod = p_id_usuario,
			fecha_mod = now(),
			id_usuario_ai = v_parametros._id_usuario_ai,
			usuario_ai = v_parametros._nombre_usuario_ai
			where id_boleto=v_parametros.id_boleto;

			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Boletos modificado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_boleto',v_parametros.id_boleto::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'OBING_BOL_ELI'
 	#DESCRIPCION:	Eliminacion de registros
 	#AUTOR:		jrivera
 	#FECHA:		06-01-2016 22:42:25
	***********************************/

	elsif(p_transaccion='OBING_BOL_ELI')then

		begin
			--Sentencia de la eliminacion
			update obingresos.tboleto set estado = NULL
            where id_boleto=v_parametros.id_boleto;

            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Boletos eliminado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_boleto',v_parametros.id_boleto::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

    /*********************************
 	#TRANSACCION:  'OBING_REVBOL_MOD'
 	#DESCRIPCION:	Revision de boleto
 	#AUTOR:		Gonzalo Sarmiento Sejas
 	#FECHA:		26-09-2017
	***********************************/

	elsif(p_transaccion='OBING_REVBOL_MOD')then

		begin
        	select estado, id_usuario_cajero, total into v_estado, v_id_usuario_cajero, v_total
            from obingresos.tboleto
            where id_boleto=v_parametros.id_boleto;

            IF(v_estado is NULL)THEN

              select sum(param.f_convertir_moneda(fp.id_moneda,bol.id_moneda_boleto,bfp.importe,bol.fecha_emision,'O',2)) into v_monto_total_fp
              from obingresos.tboleto_forma_pago bfp
              inner join obingresos.tforma_pago fp on fp.id_forma_pago=bfp.id_forma_pago
              inner join obingresos.tboleto bol on bol.id_boleto=bfp.id_boleto
              where bfp.id_boleto=v_parametros.id_boleto;

              IF (v_monto_total_fp <>v_total)THEN
              	raise exception 'El monto total de las formas de pago no iguala con el monto del boleto';
              END IF;

              update obingresos.tboleto
              set
              estado = 'revisado',
              id_usuario_cajero=p_id_usuario
              where id_boleto=v_parametros.id_boleto;
            ELSE
              IF(v_id_usuario_cajero != p_id_usuario)THEN
              	raise exception 'Solo el usuario que reviso puede cambiar el estado del boleto a no revisado';
              END IF;

              update obingresos.tboleto
              set
              estado = NULL,
              id_usuario_cajero=p_id_usuario
              where id_boleto=v_parametros.id_boleto;

            END IF;

            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Boletos revisado');
            v_resp = pxp.f_agrega_clave(v_resp,'id_boleto',v_parametros.id_boleto::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

    /*********************************
 	#TRANSACCION:  'OBING_BOLEST_MOD'
 	#DESCRIPCION:	Cambia el estado del boleto y valida que la forma de pago sea igual al total del boleto
 	#AUTOR:		jrivera
 	#FECHA:		06-01-2016 22:42:25
	***********************************/

	elsif(p_transaccion='OBING_BOLEST_MOD')then

		begin
        	select * into v_boleto
            from obingresos.tboleto
            where id_boleto = v_parametros.id_boleto;

        	select obingresos.f_valida_boleto_fp(v_parametros.id_boleto) into v_res;

			update obingresos.tboleto
            	set estado = v_parametros.accion
            where id_boleto=v_parametros.id_boleto;

            IF (v_parametros.accion = 'pagado') then
            	update obingresos.tboleto
            	set id_usuario_cajero = p_id_usuario
            	where id_boleto=v_parametros.id_boleto;

                 --Si el usuario que cambia el estado del boleto a estado pagado no es cajero
                  --lanzamos excepcion
                  raise notice 'v_boleto.fecha_reg % p_id_usuario % v_boleto.id_punto_venta %', v_boleto.fecha_reg, p_id_usuario, v_boleto.id_punto_venta;
                  if (exists(	select 1
                                  from vef.tapertura_cierre_caja acc
                                  where acc.id_usuario_cajero = p_id_usuario and
                                  	acc.fecha_apertura_cierre = v_boleto.fecha_reg::date and
                                    acc.estado_reg = 'activo' and acc.estado = 'cerrado' and
                                    acc.id_punto_venta = v_boleto.id_punto_venta)) then
                      raise exception 'La caja ya fue cerrada, necesita tener la caja abierta para poder finalizar la venta del boleto';
                  end if;


                  if (not exists(	select 1
                                  from vef.tapertura_cierre_caja acc
                                  where acc.id_usuario_cajero = p_id_usuario and
                                  	acc.fecha_apertura_cierre = v_boleto.fecha_reg::date and
                                    acc.estado_reg = 'activo' and acc.estado = 'abierto' and
                                    acc.id_punto_venta = v_boleto.id_punto_venta)) then
                      raise exception 'Antes de emitir un boleto debe realizar una apertura de caja';
                  end if;
            end if;

            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Boletos Cambiado de estadocon exito');
            v_resp = pxp.f_agrega_clave(v_resp,'id_boleto',v_parametros.id_boleto::varchar);

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