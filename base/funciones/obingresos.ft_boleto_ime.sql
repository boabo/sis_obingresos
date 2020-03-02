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
	v_tipo_moneda			varchar;
    v_tipo_cambio			numeric;

    v_autorizacion_fp		varchar[];
    v_tarjeta_fp			varchar[];
    v_monto_total_fp		numeric;
	v_identificador_reporte	varchar;

    v_reporte				varchar;
    v_moneda				varchar;
    v_boletos				varchar;
    v_nro_boleto			varchar;
    v_data_agencia			varchar;
    v_agente_venta			varchar;
    v_voided				varchar;
    v_pasajero				varchar;
    v_montos_boletos		varchar;
    v_tipo					varchar;
    v_total					varchar;
    v_impuestos				varchar;
    v_comision				varchar;
    v_impuestos_ob			varchar;
    v_tipo_pago_amadeus		varchar;
    v_monto_pago_amadeus	varchar;
    v_forma_pago_detalles	varchar;
    v_pnr					varchar;
    v_localizador			varchar;
    v_record_json_boletos	record;
    v_record_json_data_office record;
    v_record_json_montos_boletos record;
    v_record_json_pnr		record;
	v_importe_comision		numeric;
    v_comision_total		numeric;
    v_boletos_anulados_amadeus		text[];
    v_boletos_anulados_erp			record;
    v_boleto_anulado_amadeus		varchar;
    v_boletos_no_anulados_amadeus 	varchar[];
    v_boletos_no_anulados_erp		varchar[];
    v_boleto_voided			varchar;
    v_id_usuario			integer;
	v_id_fp_modo					integer;
    v_id_fpago				integer;
    v_id_viajero_frecuente integer;
    v_importe 				numeric;
	v_datos					record;
    v_id_log_viajero_frecuente	integer;
    v_contar	integer;
    v_record  record;
    v_error_mes varchar;
    v_id_boleto_a integer;

    v_id_correo_det_vw		varchar;

    --F.E.A
    v_code 					varchar;
    v_issue_indicator		varchar;
	v_cont_canje			integer = 1;
	v_exchange				varchar[];
    v_exch_sel				integer[];
    v_exch_orig_exch		record;
    v_exchange_json			jsonb;
    v_tipo_emision			varchar;
    v_nombre_pasajero		varchar = '';

    --AUMENTO VARIABLES TIPO CAMBIO
    v_id_sucursal			integer;
    v_id_moneda_moneda_sucursal	integer;
    v_id_moneda_tri			integer;
    v_tipo_cambio_actual		numeric;
    v_tiene_dos_monedas		varchar;
    v_moneda_desc			varchar;
    v_fecha_emision			varchar;

    v_nombre_punto_venta	varchar;
	v_cajero				varchar;

    v_calculo_boleto		numeric;
    v_diferencia			numeric;
    v_tolerancia			numeric;

    v_codigo_moneda				varchar;
    v_consultado			integer;
    v_estado_canjeado		varchar;
    v_puntos_venta			varchar;
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

            update obingresos.tboleto
            set comision = v_parametros.comision,
            tipo_comision=v_parametros.tipo_comision
            where id_boleto = v_parametros.id_boleto;

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

			if (v_parametros.estado = 'revisado') then
            	raise exception 'El boleto ya fue revisado, no puede modificarse';
            end if;

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
                  --ctacte,
                  numero_tarjeta,
                  codigo_tarjeta,
                  tarjeta,
                  id_usuario_fp_corregido,
                  id_auxiliar
                )
                VALUES (
                  p_id_usuario,
                  v_parametros.monto_forma_pago,
                  v_parametros.id_forma_pago,
                  v_parametros.id_boleto,
                  --v_parametros.ctacte,
                  v_parametros.numero_tarjeta,
                  v_parametros.codigo_tarjeta,
                  v_codigo_tarjeta,
                  p_id_usuario,
                  v_parametros.id_auxiliar
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
                      numero_tarjeta,
                      codigo_tarjeta,
                      tarjeta
                    )
                    VALUES (
                      p_id_usuario,
                      v_parametros.monto_forma_pago2,
                      v_parametros.id_forma_pago2,
                      v_parametros.id_boleto,
                      v_parametros.numero_tarjeta2,
                      v_parametros.codigo_tarjeta2,
                      v_codigo_tarjeta
                    );
                end if;

                select sum(param.f_convertir_moneda(fp.id_moneda,bol.id_moneda_boleto,bfp.importe,bol.fecha_emision,'O',2)) into v_monto_total_fp
                from obingresos.tboleto_forma_pago bfp
                inner join obingresos.tforma_pago fp on fp.id_forma_pago=bfp.id_forma_pago
                inner join obingresos.tboleto bol on bol.id_boleto=bfp.id_boleto
                where bfp.id_boleto=v_parametros.id_boleto;

                IF (COALESCE(v_monto_total_fp,0) = (v_boleto.total-COALESCE(v_boleto.comision,0)) and v_boleto.voided='no')THEN
                  update obingresos.tboleto
                  set
                  estado = 'revisado',
                  id_usuario_cajero=p_id_usuario
                  where id_boleto=v_parametros.id_boleto;
                END IF;
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
 	#TRANSACCION:  'OBING_BOLAMAVEN_UPD'
 	#DESCRIPCION:	Modificacion de boletos amdeus
 	#AUTOR:		Gonzalo Sarmiento
 	#FECHA:		26-10-2017
	***********************************/

	elsif(p_transaccion='OBING_BOLAMAVEN_UPD')then

        begin
        	select fp.codigo into v_codigo_fp
        	from obingresos.tforma_pago fp
        	where fp.id_forma_pago = v_parametros.id_forma_pago;

            update obingresos.tboleto_amadeus
            set comision = v_parametros.comision,
            tipo_comision=v_parametros.tipo_comision
            where id_boleto_amadeus = v_parametros.id_boleto_amadeus;

            select * into v_boleto
            from obingresos.tboleto_amadeus
            where id_boleto_amadeus = v_parametros.id_boleto_amadeus;
            v_mensaje = '';
            if (v_parametros.estado is null or v_parametros.estado = '') then
            	if (exists (select 1
                	from obingresos.tboleto_amadeus
                	where id_boleto_amadeus = v_parametros.id_boleto_amadeus and estado is not null)) then
                	raise exception 'El boleto ya fue registrado';
                end if;

                v_mensaje = v_boleto.mensaje_error;

                update obingresos.tboleto_amadeus set estado = 'borrador', id_punto_venta=v_parametros.id_punto_venta
                where id_boleto_amadeus = v_parametros.id_boleto_amadeus and estado is null;


            end if;

			if (v_parametros.estado = 'revisado') then
            	raise exception 'El boleto ya fue revisado, no puede modificarse';
            end if;

            if (left (v_parametros.mco,3)  <> '930' and v_parametros.mco <> '')then
            raise exception 'El numero del MCO tiene que empezar con 930';
            end if;

          	if (char_length(v_parametros.mco::varchar) <> 15 and v_parametros.mco <> '' ) then
            raise exception 'El numero del MCO debe tener 15 digitos obligatorios, 930000000012345';
            end if;


            if (v_parametros.id_forma_pago is not null and v_parametros.id_forma_pago != 0) then
        			-- Forma de pago igual y importe distinto

                     select a.id_forma_pago,
                     		a.importe
                     		into
                     		v_id_fpago,
                     		v_importe
                     from obingresos.tboleto_amadeus_forma_pago a
                     where a.id_forma_pago = v_parametros.id_forma_pago  and
                     a.id_boleto_amadeus = v_parametros.id_boleto_amadeus;

                    IF ( v_id_fpago = v_parametros.id_forma_pago and
                    		v_importe <> v_parametros.monto_forma_pago) THEN

                            delete from obingresos.tmod_forma_pago m
                            where m.billete = v_boleto.nro_boleto::numeric;

                             perform obingresos.f_forma_pago_amadeus_mod(v_parametros.id_boleto_amadeus,v_parametros.id_forma_pago,
            													v_parametros.numero_tarjeta::varchar, v_parametros.id_auxiliar,
                                                                p_id_usuario,v_parametros.codigo_tarjeta,v_parametros.monto_forma_pago,
                                                                 v_parametros.mco);

                    END IF;

                     --replicacion
                  IF EXISTS (select 1 from obingresos.tboleto_amadeus_forma_pago a
                                        where a.id_forma_pago = v_parametros.id_forma_pago  and
                                        a.id_boleto_amadeus = v_parametros.id_boleto_amadeus) THEN

                               RAISE NOTICE 'Existe una forma de Pago';

                   ELSE
                               delete from obingresos.tmod_forma_pago m
                            where m.billete = v_boleto.nro_boleto::numeric;
                             perform obingresos.f_forma_pago_amadeus_mod(v_parametros.id_boleto_amadeus,v_parametros.id_forma_pago,
            													v_parametros.numero_tarjeta::varchar, v_parametros.id_auxiliar,
                                                                p_id_usuario,v_parametros.codigo_tarjeta,v_parametros.monto_forma_pago,
                                                                 v_parametros.mco);
					END IF;

                    IF(v_boleto.estado_informix = 'no_migra')THEN

                    delete from obingresos.tmod_forma_pago m
                    where m.billete = v_boleto.nro_boleto::numeric;

                   perform obingresos.f_forma_pago_amadeus_mod(v_parametros.id_boleto_amadeus,v_parametros.id_forma_pago,
                                                            v_parametros.numero_tarjeta::varchar, v_parametros.id_auxiliar,
                                                            p_id_usuario,v_parametros.codigo_tarjeta,v_parametros.monto_forma_pago,
                                                             v_parametros.mco);
                    END IF;


                    delete from obingresos.tboleto_amadeus_forma_pago
          			where id_boleto_amadeus = v_parametros.id_boleto_amadeus;

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
                 select a.id_forma_pago,
                     		a.importe
                     		into
                     		v_id_fpago,
                     		v_importe
                     from obingresos.tboleto_amadeus_forma_pago a
                     where a.id_forma_pago = v_parametros.id_forma_pago  and
                     a.id_boleto_amadeus = v_parametros.id_boleto_amadeus;


                 --raise exception 'llega';
                INSERT INTO
                  obingresos.tboleto_amadeus_forma_pago
                (
                  id_usuario_reg,
                  importe,
                  id_forma_pago,
                  id_boleto_amadeus,
                  --ctacte,
                  numero_tarjeta,
                  codigo_tarjeta,
                  tarjeta,
                  id_usuario_fp_corregido,
                  id_auxiliar,
                  registro_mod,
                  mco
                )
                VALUES (
                  p_id_usuario,
                  v_parametros.monto_forma_pago,
                  v_parametros.id_forma_pago,
                  v_parametros.id_boleto_amadeus,
                  --v_parametros.ctacte,
                  v_parametros.numero_tarjeta,
                  v_parametros.codigo_tarjeta,
                  v_codigo_tarjeta,
                  p_id_usuario,
                  v_parametros.id_auxiliar,
                  null,
                  v_parametros.mco
                );

                if (left (v_parametros.mco2::varchar,3)<> '930' and v_parametros.mco2 <> '' )then
                    raise exception 'Segunda forma de pago el numero del MCO tiene que empezar con 390';
                    end if;

                    if (char_length(v_parametros.mco2::varchar) <> 15 and v_parametros.mco2 <> '') then
                    raise exception 'Segunda forma de pago el numero del MCO debe tener 15 digitos obligatorios, 930000000012345';
                    end if;
				 if (v_parametros.id_forma_pago2 is not null and v_parametros.id_forma_pago2 != 0) then
           			IF(v_boleto.estado_informix = 'no_migra')THEN

                    PERFORM obingresos.f_forma_pago_amadeus_mod(v_parametros.id_boleto_amadeus,v_parametros.id_forma_pago2,
            													v_parametros.numero_tarjeta2::varchar, v_parametros.id_auxiliar2,
                                                                p_id_usuario,v_parametros.codigo_tarjeta2,v_parametros.monto_forma_pago2,
                                                                 v_parametros.mco2);
                    ELSE
                    PERFORM obingresos.f_forma_pago_amadeus_mod(v_parametros.id_boleto_amadeus,v_parametros.id_forma_pago2,
            													v_parametros.numero_tarjeta2::varchar, v_parametros.id_auxiliar2,
                                                                p_id_usuario,v_parametros.codigo_tarjeta2,v_parametros.monto_forma_pago2,
                                                                 v_parametros.mco2);
                    END IF;
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
                  obingresos.tboleto_amadeus_forma_pago
                (
                  id_usuario_reg,
                  importe,
                  id_forma_pago,
                  id_boleto_amadeus,
                  --ctacte,
                  numero_tarjeta,
                  codigo_tarjeta,
                  tarjeta,
                  id_usuario_fp_corregido,
                  id_auxiliar,
                  registro_mod,
                  mco
                )
                VALUES (
                  p_id_usuario,
                  v_parametros.monto_forma_pago2,
                  v_parametros.id_forma_pago2,
                  v_parametros.id_boleto_amadeus,
                  --v_parametros.ctacte,
                  v_parametros.numero_tarjeta2,
                  v_parametros.codigo_tarjeta2,
                  v_codigo_tarjeta,
                  p_id_usuario,
                  v_parametros.id_auxiliar2,
                  1,
                  v_parametros.mco2
                );

                end if;

                select sum(param.f_convertir_moneda(fp.id_moneda,bol.id_moneda_boleto,bfp.importe,bol.fecha_emision,'O',2)) into v_monto_total_fp
                from obingresos.tboleto_amadeus_forma_pago bfp
                inner join obingresos.tforma_pago fp on fp.id_forma_pago=bfp.id_forma_pago
                inner join obingresos.tboleto_amadeus bol on bol.id_boleto_amadeus=bfp.id_boleto_amadeus
                where bfp.id_boleto_amadeus=v_parametros.id_boleto_amadeus;


				/*Verificamos si la moneda es ARS entonces tomamos el margen de error*/
				select mon.codigo_internacional into v_codigo_moneda
                from param.tmoneda mon
                where mon.tipo_moneda = 'base';


                IF (v_codigo_moneda = 'ARS') then

               /*Aumentando margen de error (Ismael Valdivia 06/01/2019)*/
                v_calculo_boleto = (COALESCE(v_boleto.total,0) - COALESCE(v_boleto.comision,0));
                v_diferencia = (COALESCE(v_monto_total_fp,0) - v_calculo_boleto);
                /*********************************************************/

                /*****RECUPERAMOS LA TOLERANCIA DESDE UNA VARIABLE GLOBAL*******/
                select va.valor into v_tolerancia
                from pxp.variable_global va
                where va.variable = 'tolerancia_amadeus';
                /*****************************************************************/

                    if ((v_diferencia >= (v_tolerancia * (1))) OR (v_diferencia <= (v_tolerancia * (-1)))) THEN
                        raise exception 'Existe una diferencia de % entre el monto ingresado: % y el monto total del Boleto %. la tolerancia maxima que se puede tener es de %',v_diferencia,v_monto_total_fp,v_calculo_boleto,v_tolerancia;

                    ELSE
                        if (v_boleto.voided='no') then
                            update obingresos.tboleto_amadeus
                            set
                            estado = 'revisado',
                            id_usuario_cajero=p_id_usuario
                            where id_boleto_amadeus=v_parametros.id_boleto_amadeus;
                        end if;
                    end if;

                else
				/*Si la moneda es BOB no tomamos la tolerancia*/
                IF (COALESCE(v_monto_total_fp,0) = (v_boleto.total-COALESCE(v_boleto.comision,0)) and v_boleto.voided='no')THEN
                  update obingresos.tboleto_amadeus
                  set
                  estado = 'revisado',
                  id_usuario_cajero=p_id_usuario
                  where id_boleto_amadeus=v_parametros.id_boleto_amadeus;
                END IF;
                /*****************************************************/
             end if;

            end if;



			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Boletos almacenado(a) con exito (id_boleto_amadeus'||v_id_boleto||')');
            v_resp = pxp.f_agrega_clave(v_resp,'id_boleto_amadeus',v_id_boleto::varchar);
            v_resp = pxp.f_agrega_clave(v_resp,'id_fp_modo',v_id_fp_modo::varchar);
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
                      --ctacte,
                      numero_tarjeta,
                      tarjeta
                    )
                    VALUES (
                      p_id_usuario,
                      v_valor,
                      v_parametros.id_forma_pago,
                      v_id_boleto,
                      --v_parametros.ctacte,
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
                      --ctacte,
                      numero_tarjeta,
                      tarjeta
                    )
                    VALUES (
                      p_id_usuario,
                      v_valor,
                      v_parametros.id_forma_pago2,
                      v_id_boleto,
                      --v_parametros.ctacte2,
                      v_parametros.numero_tarjeta2,
                      v_codigo_tarjeta
                    );
            	end if;
                select obingresos.f_valida_boleto_fp(v_id_boleto) into v_res;


               update obingresos.tboleto
               set id_usuario_cajero = p_id_usuario,
               estado = 'revisado'
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
 	#TRANSACCION:  'OBING_MODAMAFPGR_UPD'
 	#DESCRIPCION:	Modifica la forma de pago de un grupo de boletos amadeus
 	#AUTOR:		Gonzalo Sarmiento
 	#FECHA:		06-01-2016 22:42:25
	***********************************/

	elsif(p_transaccion='OBING_MODAMAFPGR_UPD')then

        begin

        	v_comision_total = 0;
        	v_saldo_fp1 = v_parametros.monto_forma_pago;
            v_saldo_fp2 = 	(case when v_parametros.monto_forma_pago2 is null then
            					0
            				else
                            	v_parametros.monto_forma_pago2
                            end);

            v_ids = string_to_array(v_parametros.ids_seleccionados,',');
            FOREACH v_id_boleto IN ARRAY v_ids
            LOOP
               if (pxp.f_existe_parametro(p_tabla,'tipo_comision') = TRUE) then
               	IF(v_parametros.tipo_comision = 'nacional')THEN
                	UPDATE obingresos.tboleto_amadeus
                	SET comision=neto*0.06
                    WHERE id_boleto_amadeus=v_id_boleto;
                ELSIF(v_parametros.tipo_comision='internacional')THEN
                	UPDATE obingresos.tboleto_amadeus
                	SET comision=neto*0.06
                    WHERE id_boleto_amadeus=v_id_boleto;
                ELSE
                	UPDATE obingresos.tboleto_amadeus
                	SET comision=0
                    WHERE id_boleto_amadeus=v_id_boleto;
                END IF;
               end if;

               select comision into v_importe_comision
               from obingresos.tboleto_amadeus
               where id_boleto_amadeus=v_id_boleto;
                if EXISTS (select 1 from obingresos.tforma_pago_ant
                                        where id_boleto_amadeus = v_id_boleto)  then
               delete from obingresos.tforma_pago_ant
               where id_boleto_amadeus = v_id_boleto;

              insert into obingresos.tforma_pago_ant(	importe,
                              id_forma_pago,
                              id_boleto_amadeus
                              )select a.importe,
              a.id_forma_pago,
              a.id_boleto_amadeus
              from obingresos.tboleto_amadeus_forma_pago a
              where a.id_boleto_amadeus = v_id_boleto;

               else
               insert into obingresos.tforma_pago_ant(	importe,
                                                        id_forma_pago,
                                                        id_boleto_amadeus
                                                        )select a.importe,
                                        a.id_forma_pago,
                                        a.id_boleto_amadeus
                                		from obingresos.tboleto_amadeus_forma_pago a
                                		where a.id_boleto_amadeus = v_id_boleto;

                end if ;

               v_comision_total = v_comision_total + v_importe_comision;




                delete from obingresos.tboleto_amadeus_forma_pago
                where id_boleto_amadeus = v_id_boleto;


            	if (v_saldo_fp1 > 0) then

                v_valor = obingresos.f_monto_pagar_boleto_amadeus(v_id_boleto,v_saldo_fp1,v_parametros.id_forma_pago );

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


               if (left (v_parametros.mco,3)  <> '930' and v_parametros.mco <> '')then
                        raise exception 'El numero del MCO tiene que empezar con 930';
                        end if;

                        if (char_length(v_parametros.mco::varchar) <> 15 and v_parametros.mco <> '' ) then
                        raise exception 'El numero del MCO debe tener 15 digitos obligatorios, 930000000012345';
                      end if;

                     select a.id_forma_pago,
                     		a.importe
                     		into
                     		v_id_fpago,
                     		v_importe
                     from obingresos.tforma_pago_ant a
                     where a.id_forma_pago = v_parametros.id_forma_pago  and
                     a.id_boleto_amadeus = v_id_boleto;

                    IF ( v_id_fpago = v_parametros.id_forma_pago and
                    		v_importe <> v_valor) THEN


                            delete from obingresos.tmod_forma_pago m
                            where m.billete = (select a.nro_boleto::numeric	from obingresos.tboleto_amadeus a
                            where a.id_boleto_amadeus = v_id_boleto );



                             perform obingresos.f_forma_pago_amadeus_mod(v_id_boleto,v_parametros.id_forma_pago,
            															v_parametros.numero_tarjeta::varchar, v_parametros.id_auxiliar,
                                                                		p_id_usuario,v_parametros.codigo_tarjeta,v_valor,
                                                                 		v_parametros.mco);

                    END IF;

                     IF EXISTS (select 1 from obingresos.tforma_pago_ant
                                        where id_forma_pago = v_parametros.id_forma_pago  and
                                        id_boleto_amadeus = v_id_boleto) THEN

                              RAISE notice 'Existe una forma de Pago';

                   ELSE


                      delete from obingresos.tmod_forma_pago m
                      where m.billete = (select a.nro_boleto::numeric	from obingresos.tboleto_amadeus a
                      where a.id_boleto_amadeus = v_id_boleto );

                             perform obingresos.f_forma_pago_amadeus_mod(v_id_boleto,v_parametros.id_forma_pago,
            															v_parametros.numero_tarjeta::varchar, v_parametros.id_auxiliar,
                                                                		p_id_usuario,v_parametros.codigo_tarjeta,v_valor,
                                                                 		v_parametros.mco);
					END IF;

                    IF( (select a.estado_informix
                    	from obingresos.tboleto_amadeus a
                        where a.id_boleto_amadeus = v_id_boleto)='no_migra')THEN

                           delete from obingresos.tmod_forma_pago m
                            where m.billete = (select a.nro_boleto::numeric	from obingresos.tboleto_amadeus a
                            where a.id_boleto_amadeus = v_id_boleto );
                                     perform obingresos.f_forma_pago_amadeus_mod(v_id_boleto,v_parametros.id_forma_pago,
            															v_parametros.numero_tarjeta::varchar, v_parametros.id_auxiliar,
                                                                		p_id_usuario,v_parametros.codigo_tarjeta,v_valor,
                                                                 		v_parametros.mco);
                    END IF;




					INSERT INTO obingresos.tboleto_amadeus_forma_pago( 	id_usuario_reg,
                                                                        importe,
                                                                        id_forma_pago,
                                                                        id_boleto_amadeus,
                                                                        numero_tarjeta,
                                                                        codigo_tarjeta,
                                                                        tarjeta,
                                                                        id_usuario_fp_corregido,
                                                                        id_auxiliar,
                                                                        registro_mod,
                                                                        mco
                                                                      )
                                                                      VALUES (
                                                                        p_id_usuario,
                                                                        v_valor,
                                                                        v_parametros.id_forma_pago,
                                                                        v_id_boleto,
                                                                        v_parametros.numero_tarjeta,
                                                                        v_parametros.codigo_tarjeta,
                                                                        v_codigo_tarjeta,
                                                                        p_id_usuario,
                                                                        v_parametros.id_auxiliar,
                                                                        null,
                                                                        v_parametros.mco
                                                                      );
                                                                         --raise exception 'llega';

            	end if;
                if (v_saldo_fp2 > 0) then
              		v_valor = obingresos.f_monto_pagar_boleto_amadeus(v_id_boleto,v_saldo_fp2,v_parametros.id_forma_pago2 );
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

                    if (left (v_parametros.mco2::varchar,3)<> '930' and v_parametros.mco2 <> '' )then
                    raise exception 'Segunda forma de pago el numero del MCO tiene que empezar con 390';
                    end if;

                    if (char_length(v_parametros.mco2::varchar) <> 15 and v_parametros.mco2 <> '') then
                    raise exception 'Segunda forma de pago el numero del MCO debe tener 15 digitos obligatorios, 930000000012345';
                    end if;
                     ---Replicacion Ingreso--

                    IF( (select a.estado_informix
                    	from obingresos.tboleto_amadeus a
                        where a.id_boleto_amadeus = v_id_boleto
                    			)='no_migra')THEN

                                 perform obingresos.f_forma_pago_amadeus_mod(v_id_boleto,v_parametros.id_forma_pago2,
                                                                v_parametros.numero_tarjeta2::varchar,v_parametros.id_auxiliar2,
                                                                p_id_usuario,v_parametros.codigo_tarjeta2,v_valor,
                                                                v_parametros.mco2);
                                ELSE
                                                perform obingresos.f_forma_pago_amadeus_mod(v_id_boleto,v_parametros.id_forma_pago2,
                                                                v_parametros.numero_tarjeta2::varchar,v_parametros.id_auxiliar2,
                                                                p_id_usuario,v_parametros.codigo_tarjeta2,v_valor,
                                                                v_parametros.mco2);
                    END IF;



                    INSERT INTO obingresos.tboleto_amadeus_forma_pago ( id_usuario_reg,
                                                                        importe,
                                                                        id_forma_pago,
                                                                        id_boleto_amadeus,
                                                                        --ctacte,
                                                                        numero_tarjeta,
                                                                        codigo_tarjeta,
                                                                        tarjeta,
                                                                        id_usuario_fp_corregido,
                                                                        id_auxiliar,
                                                                        registro_mod,
                                                                        mco
                                                                      )
                                                                      VALUES (
                                                                        p_id_usuario,
                                                                        v_valor,
                                                                        v_parametros.id_forma_pago2,
                                                                        v_id_boleto,
                                                                        --v_parametros.ctacte,
                                                                        v_parametros.numero_tarjeta2,
                                                                        v_parametros.codigo_tarjeta2,
                                                                        v_codigo_tarjeta,
                                                                        p_id_usuario,
                                                                        v_parametros.id_auxiliar2,
                                                                        null,
                                                                        v_parametros.mco2
                                                                      );

            	end if;

                select obingresos.f_valida_boleto_amadeus_fp(v_id_boleto) into v_res;


               update obingresos.tboleto_amadeus
               set id_usuario_cajero = p_id_usuario,
               estado = 'revisado'
               where id_boleto_amadeus=v_id_boleto;

               select * into v_boleto
               from obingresos.tboleto_amadeus
               where id_boleto_amadeus = v_id_boleto;

                --Si el usuario que cambia el estado del boleto a estado pagado no es cajero
                  --lanzamos excepcion
                  if (exists(	select 1
                                  from vef.tapertura_cierre_caja acc
                                  where acc.id_usuario_cajero = p_id_usuario and
                                  	acc.fecha_apertura_cierre = v_boleto.fecha_emision::date and
                                    acc.estado_reg = 'activo' and acc.estado = 'cerrado' and
                                    acc.id_punto_venta = v_boleto.id_punto_venta)) then
                      raise exception 'La caja ya fue cerrada, necesita tener la caja abierta para poder finalizar la venta del boleto';
                  end if;

                  if (not exists(	select 1
                                  from vef.tapertura_cierre_caja acc
                                  where acc.id_usuario_cajero = p_id_usuario and
                                  	acc.fecha_apertura_cierre = v_boleto.fecha_emision::date and
                                    acc.estado_reg = 'activo' and acc.estado = 'abierto' and
                                    acc.id_punto_venta = v_boleto.id_punto_venta)) then
                      raise exception 'Antes de emitir un boleto debe realizar una apertura de caja';
                  end if;

            END LOOP;

            if (v_saldo_fp1 - v_comision_total > 0 or v_saldo_fp2 - v_comision_total > 0) then
            	raise exception 'El monto total de las formas de pago es superior al monto de los boletos seleccionados:%,%',v_saldo_fp1,v_saldo_fp2;
            end if;

			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Forma de pago de Boletos modificado con exito (id_boletos'||v_parametros.ids_seleccionados||')');
            v_resp = pxp.f_agrega_clave(v_resp,'id_boleto_amadeus',v_id_boleto::varchar);

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
                      --ctacte,
                      numero_tarjeta,
                      tarjeta
                    )
                    VALUES (
                      p_id_usuario,
                      v_valor,
                      v_parametros.id_forma_pago,
                      v_id_boleto,
                      --v_parametros.ctacte,
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
                select obingresos.f_valida_boleto_fp(v_id_boleto) into v_res;

               update obingresos.tboleto
               set id_usuario_cajero = p_id_usuario,
               estado = 'revisado'
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
                id_agencia,
                forma_pago
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
                v_parametros.id_agencia,
                v_parametros.forma_pago_amadeus
                );
     			/*
                if(trim(v_parametros.fp)='')then
                	v_forma_pago='CA';
            	else
                	v_forma_pago=v_parametros.fp;
                end if;*/

                if(trim(v_parametros.fp)!='')then

                    SELECT id_forma_pago into v_id_forma_pago
                    FROM obingresos.tforma_pago
                    WHERE codigo=v_parametros.fp AND id_moneda=v_id_moneda;

                    INSERT INTO obingresos.tboleto_forma_pago
                    (id_usuario_reg,
                    id_boleto,
                    id_forma_pago,
                    importe
                    )
                    VALUES(
                    p_id_usuario,
                    v_id_boleto,
                    v_id_forma_pago,
                    v_parametros.valor_fp
                    );
                end if;

            select substring(max(nro_boleto)from 4) into v_identificador_reporte
            from obingresos.tboleto
            WHERE id_punto_venta=v_parametros.id_punto_venta
                        AND fecha_emision=v_parametros.fecha_emision::date
                        AND moneda=v_parametros.moneda;

        	IF NOT EXISTS(SELECT 1
            				FROM vef.tpunto_venta_reporte
                            WHERE id_punto_venta=v_parametros.id_punto_venta
                            AND fecha=v_parametros.fecha_emision::date
                            AND moneda=v_parametros.moneda)THEN

            	INSERT INTO vef.tpunto_venta_reporte(
                id_punto_venta,
                fecha,
                moneda,
                identificador_reporte)VALUES(
                v_parametros.id_punto_venta,
                v_parametros.fecha_emision::date,
                v_parametros.moneda,
                v_parametros.identificador_reporte);
            ELSE

            	UPDATE vef.tpunto_venta_reporte
                SET identificador_reporte=v_identificador_reporte
                WHERE id_punto_venta=v_parametros.id_punto_venta
                AND fecha=v_parametros.fecha_emision::date
                            AND moneda=v_parametros.moneda;
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
 	#TRANSACCION:  'OBING_SERVAMAJS_INS'
 	#DESCRIPCION:	Insercion de boletos json desde servicio REST de Amadeus
 	#AUTOR:		Gonzalo Sarmiento
 	#FECHA:		18-10-2016
	***********************************/

	elsif(p_transaccion='OBING_SERVAMAJS_INS')then

        begin
 /*************************************************CONTROL PARA EL TIPO DE CAMBIO*********************************************************************/
        /*****Recuperamos el id_sucursal para obtener la moneda***/
        select venta.id_sucursal into v_id_sucursal
        from vef.tpunto_venta venta
        where venta.id_punto_venta = v_parametros.id_punto_venta;
		/*********************************************************/

        /*Recuperamos el id_moneda de la sucursal obtenida*/
        select sm.id_moneda into v_id_moneda_moneda_sucursal
        from vef.tsucursal s
        inner join vef.tsucursal_moneda sm on s.id_sucursal = sm.id_sucursal
        where s.id_sucursal = v_id_sucursal and sm.tipo_moneda = 'moneda_base';
        /**************************************************/

    	/*Recuperamos el id_moneda de triangulacion*/
        select m.id_moneda,m.codigo_internacional,m.moneda || ' (' || m.codigo_internacional || ')' into v_id_moneda_tri
        from param.tmoneda m
        where m.estado_reg = 'activo' and m.triangulacion = 'si';
        /*****************************************************************************************************************************************/

        v_tiene_dos_monedas = 'no';
        v_tipo_cambio_actual = 1;

		if (v_id_moneda_tri != v_id_moneda_moneda_sucursal) then
            	v_tiene_dos_monedas = 'si';
                v_tipo_cambio_actual = param.f_get_tipo_cambio_v2(v_id_moneda_moneda_sucursal, v_id_moneda_tri,v_parametros.fecha_emision::date,'O');
        end if;
           /*********************VERIFICAMOS SI EXISTE EL TIPO DE CAMBIO*************************/
           IF (v_tipo_cambio_actual is null) then

            	select  mon.codigo into v_moneda_desc
                from param.tmoneda mon
                where mon.id_moneda = v_id_moneda_tri;

                SELECT to_char(v_parametros.fecha_emision,'DD/MM/YYYY') into v_fecha_emision;

            	raise exception 'No se pudo recuperar el tipo de cambio para la moneda: % en fecha: %, comuniquese con personal de Contabilidad Ingresos.',v_moneda_desc,v_fecha_emision;
            end if;
/*******************************************************************************************************************************************************************************************************/

      -- RAISE EXCEPTION ' El servicio de Amadeus que recupera los boletos por punto de venta no responde, comuníquese con Informática para reportar el problema.';

        	if (pxp.f_get_variable_global('vef_tiene_apertura_cierre') = 'si') then

            	select id_usuario into v_id_usuario
                from vef.tsucursal_usuario
                where id_punto_venta=v_parametros.id_punto_venta
                and id_usuario=p_id_usuario
                and tipo_usuario='administrador';

            	IF p_administrador !=1 AND v_id_usuario IS NULL THEN
                  if (exists(	select 1
                                    from vef.tapertura_cierre_caja acc
                                    where acc.id_usuario_cajero = p_id_usuario and
                                      acc.fecha_apertura_cierre = v_parametros.fecha_emision::date and
                                      acc.estado_reg = 'activo' and acc.estado = 'cerrado' and
                                      acc.id_punto_venta = v_parametros.id_punto_venta)) then
                        raise exception 'La caja ya fue cerrada, necesita tener la caja abierta para poder finalizar la venta del boleto';
                    end if;

                    if (not exists(	select 1
                                    from vef.tapertura_cierre_caja acc
                                    where acc.id_usuario_cajero = p_id_usuario and
                                      acc.fecha_apertura_cierre = v_parametros.fecha_emision::date and
                                      acc.estado_reg = 'activo' and acc.estado = 'abierto' and
                                      acc.id_punto_venta = v_parametros.id_punto_venta)) then
                        raise exception 'Antes de traer boletos debe realizar una apertura de caja';
                    end if;
                END IF;
            end if;

			--raise exception 'id %',v_reporte = v_parametros.boletos;
           --- raise exception 'json %',v_parametros.boletos;
            if ( v_parametros.boletos = '' or v_parametros.boletos  is null )then
      RAISE EXCEPTION ' El servicio de Amadeus que recupera los boletos por punto de venta no responde, comuníquese con Informática para reportar el problema.';

     	      end if;

        	--recuperamos la moneda
        	v_reporte = v_parametros.boletos :: JSON ->> 'queryReportDataDetails';
            v_moneda = v_reporte :: JSON ->>'currencyInfo';
            v_moneda = v_moneda :: JSON ->>'currencyDetails';
            v_moneda = v_moneda :: JSON ->>'currencyIsoCode';
			--raise notice 'v_moneda %',v_moneda;
			--recuperamos los boletos
            v_data_agencia = v_reporte :: JSON ->>'queryReportDataOfficeGroup';
			--	raise exception 'h %',v_data_agencia;
            FOR v_record_json_data_office IN (SELECT json_array_elements(v_data_agencia :: JSON)
            ) LOOP
                v_boletos = v_record_json_data_office.json_array_elements :: JSON ->> 'documentData';
               --raise exception '%',v_boletos;
            	FOR v_record_json_boletos IN (SELECT json_array_elements(v_boletos :: JSON)
            	) LOOP
                  --recuperamos nro boleto

                  v_nro_boleto = v_record_json_boletos.json_array_elements::JSON ->> 'documentNumber';
                  v_nro_boleto = v_nro_boleto::JSON ->> 'documentDetails';
                  v_nro_boleto = v_nro_boleto::JSON ->> 'number';
                  raise notice 'v_nro_boleto %',v_nro_boleto;
                  --recuperamos agente de venta
                  v_agente_venta = v_record_json_boletos.json_array_elements::json->> 'bookingAgent';
                  v_agente_venta = v_agente_venta::json->> 'originIdentification';
                  v_agente_venta = v_agente_venta::json->> 'originatorId';
                  --raise notice 'v_agente_venta %',v_agente_venta;
                  --recuperamos estado boleto (voided)
                  v_voided = v_record_json_boletos.json_array_elements::json->> 'transactionDataDetails';
                  v_voided = v_voided::json->>'transactionDetails';

                  --F.E.A
                  v_code = v_voided::json->>'code';
                  v_issue_indicator = v_voided::json->>'issueIndicator';

                  v_voided = v_voided::json->>'code';

                  if v_voided = 'CANX' then
                  	 v_voided='si';
                  elsif v_voided = 'CANN' then
                     v_voided='si';
                  elsif v_voided = 'TKTT' then
                  	 v_voided='no';
                  elsif v_voided = 'EMDS' then
                  	 v_voided='no';
                  end if;
				  --raise notice 'v_voided %',v_voided;
                  --recuperamos pasajero
                  v_pasajero = v_record_json_boletos.json_array_elements::json->>'passengerName';
                  v_pasajero = v_pasajero::json->>'paxDetails';
                  v_pasajero = v_pasajero::json->>'surname';
				--raise notice 'v_pasajero %', v_pasajero;
               -- raise exception 'v_pasajero %', v_pasajero;
                  --recuperamos precio del boleto
                  v_montos_boletos = v_record_json_boletos.json_array_elements::json->>'monetaryInformation';
                  v_montos_boletos = v_montos_boletos::json->>'otherMonetaryDetails';

                  FOR v_record_json_montos_boletos IN (SELECT json_array_elements(v_montos_boletos :: JSON)
            	  )LOOP
                  		v_tipo =v_record_json_montos_boletos.json_array_elements::json->>'typeQualifier';

                  		IF v_tipo = 'T' THEN
                        	v_total = v_record_json_montos_boletos.json_array_elements::json->>'amount';
                            IF(v_total='' or v_total=' ')THEN
                            	v_total=0.00;
                            END IF;
                        ELSIF v_tipo = 'TTX' THEN
                        	v_impuestos = v_record_json_montos_boletos.json_array_elements::json->>'amount';
                            IF(v_impuestos='' or v_impuestos=' ')THEN
                            	v_impuestos=0.00;
                            END IF;
                        ELSIF v_tipo = 'F' THEN
                        	v_comision=0.00;
                        ELSIF v_tipo = 'OB' THEN
                        	v_impuestos_ob = v_record_json_montos_boletos.json_array_elements::json->>'amount';
                            IF(v_impuestos_ob='' or v_impuestos_ob=' ')THEN
                            	v_impuestos_ob=0.00;
                            END IF;
                        ELSE
                        	raise exception 'Tipo monto no definido %', v_tipo;
                        END IF;

                  END LOOP;
                  /*
				  raise notice 'v_total %', v_total;
                  raise notice 'v_impuestos %', v_impuestos;
                  raise notice 'v_comision %', v_comision;*/
                  v_forma_pago_detalles = v_record_json_boletos.json_array_elements::json->>'fopDetails';
                  --recuperamos tipo de pago
                  v_tipo_pago_amadeus = v_forma_pago_detalles::json->>'fopDescription';
                  v_tipo_pago_amadeus = v_tipo_pago_amadeus::json->>'formOfPayment';
                  v_tipo_pago_amadeus = v_tipo_pago_amadeus::json->>'type';
                  --raise notice 'v_tipo_pago_amadeus %', v_tipo_pago_amadeus;
                  --recuperamos monto de pago
                  v_monto_pago_amadeus = v_forma_pago_detalles::json->>'monetaryInfo';
                  v_monto_pago_amadeus = v_monto_pago_amadeus::json->>'monetaryDetails';
                  v_monto_pago_amadeus = v_monto_pago_amadeus::json->>'amount';
				  --raise notice 'v_monto_pago_amadeus %', v_monto_pago_amadeus;
                  v_pnr = v_record_json_boletos.json_array_elements::json->>'reservationInformation';

                  FOR v_record_json_pnr IN (SELECT json_array_elements(v_pnr :: JSON)
            	  )LOOP
                  		v_localizador =v_record_json_pnr.json_array_elements::json->>'controlNumber';
                  END LOOP;

                  --insercion de boleto
                  IF NOT EXISTS(SELECT 1
                            FROM obingresos.tboleto_amadeus
                            WHERE nro_boleto=v_nro_boleto)THEN

                  SELECT id_moneda, tipo_moneda into v_id_moneda, v_tipo_moneda
                  FROM param.tmoneda
                  WHERE codigo_internacional=v_moneda;

                  select oficial into v_tipo_cambio
                  from param.ttipo_cambio tc
                  inner join param.tmoneda mon on mon.id_moneda=tc.id_moneda
                  where mon.tipo_moneda='ref'
                  and fecha = v_parametros.fecha_emision;

                  select nextval('obingresos.tboleto_amadeus_id_boleto_amadeus_seq'::regclass) into v_id_boleto;

				  /*
                  raise notice 'v_nro_boleto % v_total % v_comision %', v_nro_boleto, v_total, v_comision;
                  raise notice 'v_moneda % v_voided % v_parametros.id_punto_venta %', v_moneda, v_voided, v_parametros.id_punto_venta;
                  raise notice 'v_localizador % v_parametros.fecha_emision % v_id_moneda %', v_localizador, v_parametros.fecha_emision, v_id_moneda;
                  raise notice 'v_pasajero % v_total % v_id_boleto %', v_pasajero, v_total, v_id_boleto;
                  raise notice 'v_agente_venta % v_parametros.id_agencia % v_tipo_pago_amadeus %', v_agente_venta, v_parametros.id_agencia, v_tipo_pago_amadeus;
                  raise notice 'v_id_boleto % p_id_usuario % v_tipo_pago_amadeus %', v_id_boleto, p_id_usuario, v_tipo_pago_amadeus;
                  */
                  /*
                  v_resp= 'INSERT INTO obingresos.tboleto_amadeus
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
                  tc,
                  pasajero,
                  liquido,
                  neto,
                  xt,
                  monto_pagado_moneda_boleto,
                  id_usuario_reg,
                  id_boleto,
                  agente_venta,
                  id_agencia,
                  forma_pago
                  )VALUES('''||v_nro_boleto||'''::varchar,
                  coalesce('||v_total||',0)::numeric,
                  coalesce('||v_comision||',0)::numeric,
                  '''||v_moneda||'''::varchar,
                  '''||v_voided||'''::varchar,
                  ''borrador'',
                  '||v_parametros.id_punto_venta||'::integer,
                  '''||v_localizador||'''::varchar,
                  '''||v_parametros.fecha_emision||'''::date,
                  '||v_id_moneda||'::integer,
                  '||v_tipo_cambio||'::numeric,
                  '''||v_pasajero||'''::varchar,
                  coalesce('||v_total||',0)::numeric,
                  coalesce('||v_total::numeric-v_impuestos::numeric||',0)::numeric,
                  0,
                  0.00,
                  '||p_id_usuario||'::INTEGER,
                  '||v_id_boleto||'::integer,
                  '''||v_agente_venta||'''::varchar,
                  '||v_parametros.id_agencia||'::integer,
                  '''||v_tipo_pago_amadeus||'''::varchar
                  )';*/
                  if v_voided = 'EMDS' then
                    if (select 1 from obingresos.tboleto_amadeus tba
                    			where tba.trans_code = 'TKTT' and tba.fecha_emision = v_parametros.fecha_emision::date
                                and tba.localizador = v_localizador::varchar and tba.pasajero = v_pasajero::varchar) then

                                update  obingresos.tboleto_amadeus set
                                	trans_code_exch = 'EXCH'
                                where trans_code = 'TKTT' and fecha_emision = v_parametros.fecha_emision::date
                                and localizador = v_localizador::varchar and pasajero = v_pasajero::varchar;
                    end if;
                  end if;

                  INSERT INTO obingresos.tboleto_amadeus
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
                  tc,
                  pasajero,
                  liquido,
                  neto,
                  monto_pagado_moneda_boleto,
                  id_usuario_reg,
                  id_boleto_amadeus,
                  agente_venta,
                  id_agencia,
                  forma_pago,
                  trans_code,
                  trans_issue_indicator
                  --trans_code_exch
                  )VALUES(v_nro_boleto::varchar,
                  v_total::numeric,
                  v_comision::numeric,
                  v_moneda::varchar,
                  v_voided::varchar,
                  'borrador',
                  v_parametros.id_punto_venta::integer,
                  v_localizador::varchar,
                  v_parametros.fecha_emision::date,
                  v_id_moneda::integer,
                  v_tipo_cambio::numeric,
                  v_pasajero::varchar,
                  v_total::numeric,
                  v_total::numeric-v_impuestos::numeric,
                  0.00,
                  p_id_usuario,
                  v_id_boleto,
                  v_agente_venta::varchar,
                  v_parametros.id_agencia::integer,
                  v_tipo_pago_amadeus::varchar,
                  v_code::varchar,
                  v_issue_indicator::varchar
                  --case when v_voided = 'EMDS' then 'EMDS' else 'ORIG' end
                  );

                  if(trim(v_tipo_pago_amadeus)='CA')then

                      SELECT id_forma_pago into v_id_forma_pago
                      FROM obingresos.tforma_pago
                      WHERE codigo=v_tipo_pago_amadeus AND id_moneda=v_id_moneda;

                      SELECT id_forma_pago into v_id_forma_pago
                      FROM obingresos.tforma_pago
                      WHERE codigo = v_tipo_pago_amadeus AND
                            id_moneda = v_id_moneda AND
                            id_lugar = param.f_obtener_padre_id_lugar((select suc.id_lugar
                                       from vef.tpunto_venta pv
                                       inner join vef.tsucursal suc on suc.id_sucursal=pv.id_sucursal
                                       where pv.id_punto_venta=v_parametros.id_punto_venta),'pais');

                      IF v_id_forma_pago IS NOT NULL THEN
                        INSERT INTO obingresos.tboleto_amadeus_forma_pago
                        (id_usuario_reg,
                        id_boleto_amadeus,
                        id_forma_pago,
                        importe
                        )
                        VALUES(
                        p_id_usuario,
                        v_id_boleto,
                        v_id_forma_pago,
                        v_monto_pago_amadeus::numeric
                        );
                      END IF;

                  end if;

                  select substring(max(nro_boleto)from 4) into v_identificador_reporte
                  from obingresos.tboleto_amadeus
                  WHERE id_punto_venta=v_parametros.id_punto_venta
                              AND fecha_emision=v_parametros.fecha_emision::date
                              AND moneda=v_moneda;

                  IF NOT EXISTS(SELECT 1
                                  FROM vef.tpunto_venta_reporte
                                  WHERE id_punto_venta=v_parametros.id_punto_venta
                                  AND fecha=v_parametros.fecha_emision::date
                                  AND moneda=v_moneda)THEN

                      INSERT INTO vef.tpunto_venta_reporte(
                      id_punto_venta,
                      fecha,
                      moneda,
                      identificador_reporte)VALUES(
                      v_parametros.id_punto_venta,
                      v_parametros.fecha_emision::date,
                      v_moneda,
                      v_identificador_reporte);
                  ELSE

                      UPDATE vef.tpunto_venta_reporte
                      SET identificador_reporte=v_identificador_reporte
                      WHERE id_punto_venta=v_parametros.id_punto_venta
                      AND fecha=v_parametros.fecha_emision::date
                                  AND moneda=v_moneda;
                  END IF;

                  END IF;
                END LOOP;
            END LOOP;

            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Boletos almacenado(a) con exito');
            v_resp = pxp.f_agrega_clave(v_resp,'id_boleto_amadeus',v_id_boleto::varchar);

            --Devuelve la respuesta
            return v_resp;

        end;

	/*********************************
 	#TRANSACCION:  'OBING_ANUBOL_UPD'
 	#DESCRIPCION:	Anulacion de Boletos
 	#AUTOR:		Gonzalo Sarmiento
 	#FECHA:		21-10-2017
	***********************************/

	elsif(p_transaccion='OBING_ANUBOL_UPD')then

               begin

    ---raise exception 'id %',v_parametros.id_boleto_amadeus;

    v_ids = string_to_array(v_parametros.id_boleto_amadeus,',');
            FOREACH v_id_boleto_a IN ARRAY v_ids
            LOOP

            --raise exception '%',v_record;

        	select * into v_boleto
            from obingresos.tboleto_amadeus
            where id_boleto_amadeus=v_id_boleto_a;

            IF EXISTS(SELECT 1
            			  FROM obingresos.tboleto_amadeus
            			  WHERE id_boleto_amadeus=v_id_boleto_a)THEN

				if (pxp.f_get_variable_global('vef_tiene_apertura_cierre') = 'si') then

                if (exists(	select 1
                                  from vef.tapertura_cierre_caja acc
                                  where acc.id_usuario_cajero = p_id_usuario and
                                  	acc.fecha_apertura_cierre = v_boleto.fecha_emision::date and
                                    acc.estado_reg = 'activo' and acc.estado = 'cerrado' and
                                    acc.id_punto_venta = v_boleto.id_punto_venta)) then
                      raise exception 'La caja ya fue cerrada, necesita tener la caja abierta para poder anular el boleto';
                  end if;

                  if (not exists(	select 1
                                  from vef.tapertura_cierre_caja acc
                                  where acc.id_usuario_cajero = p_id_usuario and
                                  	acc.fecha_apertura_cierre = v_boleto.fecha_emision::date and
                                    acc.estado_reg = 'activo' and acc.estado = 'abierto' and
                                    acc.id_punto_venta = v_boleto.id_punto_venta)) then
                      raise exception 'Antes de anular un boleto debe realizar una apertura de caja';
                  end if;
            	end if;



            	IF (v_boleto.voided = 'si')THEN
                  UPDATE obingresos.tboleto_amadeus
                  SET voided='no',
                  id_usuario_cajero = p_id_usuario
                  WHERE id_boleto_amadeus=v_id_boleto_a;

              PERFORM vef.f_anular_forma_pago_amadeus_replicar(v_id_boleto_a);

                ELSE
                  UPDATE obingresos.tboleto_amadeus
                  SET voided='si',
                  id_usuario_cajero = p_id_usuario
                  WHERE id_boleto_amadeus=v_id_boleto_a;

                  update obingresos.tboleto_amadeus
                  set comision = 0,
                  tipo_comision= 'ninguno'
                  where id_boleto_amadeus = v_id_boleto_a;


            delete from obingresos.tmod_forma_pago m
            where m.billete = (	select 	b.nro_boleto::numeric
                         		from obingresos.tboleto_amadeus b
             			 		where b.id_boleto_amadeus = v_id_boleto_a);
                  END IF;

            	--Definicion de la respuesta
				v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Boletos anulado con exito');
            	v_resp = pxp.f_agrega_clave(v_resp,'id_boleto_amadeus',v_id_boleto_a::varchar);

            END IF;
		END LOOP;
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
 	#TRANSACCION:  'OBING_BOLAMAERP_INS'
 	#DESCRIPCION:	Comparacion de boletos de Amadeus contra boletos ERP
 	#AUTOR:		Gonzalo Sarmiento
 	#FECHA:		22-11-2017
	***********************************/

	elsif(p_transaccion='OBING_BOLAMAERP_INS')then

        begin
        	v_boletos_anulados_amadeus = string_to_array(v_parametros.boletos, ',');

			FOR v_boletos_anulados_erp IN (select bol.nro_boleto
                                        from obingresos.tboleto_amadeus bol
                                        where bol.id_punto_venta=v_parametros.id_punto_venta
                                        and bol.fecha_emision=v_parametros.fecha_emision::date
                                        and bol.id_usuario_cajero=v_parametros.id_usuario_cajero
                                        and bol.voided='si'
                                        order by bol.nro_boleto)LOOP

                IF v_boletos_anulados_amadeus @> ARRAY[v_boletos_anulados_erp.nro_boleto]::text[] != true THEN
                    v_boletos_no_anulados_amadeus =  array_append(v_boletos_no_anulados_amadeus, v_boletos_anulados_erp.nro_boleto);
                END IF;
            END LOOP;

            FOREACH v_boleto_anulado_amadeus IN ARRAY v_boletos_anulados_amadeus
            LOOP
            	select bol.voided into v_boleto_voided
                from obingresos.tboleto_amadeus bol
                where bol.nro_boleto= v_boleto_anulado_amadeus
                and bol.id_usuario_cajero=v_parametros.id_usuario_cajero
                and bol.estado='revisado';

                IF v_boleto_voided='no' THEN
                	v_boletos_no_anulados_erp =  array_append(v_boletos_no_anulados_erp, v_boleto_anulado_amadeus);
                END IF;

            END LOOP;

            IF array_length(v_boletos_no_anulados_amadeus,1) > 0 THEN
            	raise exception 'Boletos no anulados en Amadeus pero si en el ERP %',v_boletos_no_anulados_amadeus;
            END IF;

            IF array_length(v_boletos_no_anulados_erp,1) > 0 THEN
            	raise exception 'Boletos no anulados en ERP pero si en Amadeus %',v_boletos_no_anulados_erp;
            END IF;

			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Los boletos anulados en Amadeus son los mismos boletos anulados en el ERP');
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje_respuesta','Los boletos anulados en Amadeus son los mismos boletos anulados en el ERP');
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
        	select * into v_boleto
            from obingresos.tboleto_amadeus
            where id_boleto_amadeus=v_parametros.id_boleto_amadeus;

        	if (pxp.f_get_variable_global('vef_tiene_apertura_cierre') = 'si') then
                if (exists(	select 1
                                  from vef.tapertura_cierre_caja acc
                                  where acc.id_usuario_cajero = p_id_usuario and
                                  	acc.fecha_apertura_cierre = v_boleto.fecha_emision::date and
                                    acc.estado_reg = 'activo' and acc.estado = 'cerrado' and
                                    acc.id_punto_venta = v_boleto.id_punto_venta)) then
                      raise exception 'La caja ya fue cerrada, necesita tener la caja abierta para poder finalizar la venta del boleto';
                  end if;

                  if (not exists(	select 1
                                  from vef.tapertura_cierre_caja acc
                                  where acc.id_usuario_cajero = p_id_usuario and
                                  	acc.fecha_apertura_cierre = v_boleto.fecha_emision::date and
                                    acc.estado_reg = 'activo' and acc.estado = 'abierto' and
                                    acc.id_punto_venta = v_boleto.id_punto_venta)) then
                      raise exception 'Antes de revisar un boleto debe realizar una apertura de caja';
                  end if;
            end if;

            IF(v_boleto.estado='borrador')THEN

              select sum(param.f_convertir_moneda(fp.id_moneda,bol.id_moneda_boleto,bfp.importe,bol.fecha_emision,'O',2)) into v_monto_total_fp
              from obingresos.tboleto_amadeus_forma_pago bfp
              inner join obingresos.tforma_pago fp on fp.id_forma_pago=bfp.id_forma_pago
              inner join obingresos.tboleto_amadeus bol on bol.id_boleto_amadeus=bfp.id_boleto_amadeus
              where bfp.id_boleto_amadeus=v_parametros.id_boleto_amadeus;

              IF (COALESCE(v_monto_total_fp,0) <(v_boleto.total-COALESCE(v_boleto.comision,0)) and v_boleto.voided='no')THEN
              	raise exception 'El monto total de las formas de pago no iguala con el monto del boleto';
              END IF;

			  IF (COALESCE(v_monto_total_fp,0) >(v_boleto.total-COALESCE(v_boleto.comision,0)+0.02) and v_boleto.voided='no')THEN
              	raise exception 'El monto total de las formas de pago no iguala con el monto del boleto';
              END IF;

              update obingresos.tboleto_amadeus
              set
              estado = 'revisado',
              id_usuario_cajero=p_id_usuario
              where id_boleto_amadeus=v_parametros.id_boleto_amadeus;
            ELSE
              IF(v_boleto.id_usuario_cajero != p_id_usuario)THEN
              	raise exception 'Solo el usuario que reviso puede cambiar el estado del boleto a no revisado';
              END IF;

              update obingresos.tboleto_amadeus
              set
              estado = 'borrador',
              estado_informix = 'no_migra',
              comision = 0.00,
              tipo_comision = 'ninguno',
              id_usuario_cajero=NULL
              where id_boleto_amadeus=v_parametros.id_boleto_amadeus;

              IF (v_boleto.forma_pago = 'CC') THEN
              	DELETE
                FROM obingresos.tboleto_amadeus_forma_pago
                WHERE id_boleto_amadeus=v_parametros.id_boleto_amadeus;

            delete from obingresos.tmod_forma_pago m
            where m.billete = (	select 	b.nro_boleto::numeric
                         		from obingresos.tboleto_amadeus b
             			 		where b.id_boleto_amadeus = v_parametros.id_boleto_amadeus);


              END IF;

            END IF;

            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Boletos revisado');
            v_resp = pxp.f_agrega_clave(v_resp,'id_boleto_amadeus',v_parametros.id_boleto_amadeus::varchar);

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

     /*********************************
 	#TRANSACCION:  'OBING_BOWEBFEC_VEF'
 	#DESCRIPCION:	viajero frecuente
 	#AUTOR:		mmv
 	#FECHA:
	***********************************/

	elsif(p_transaccion='OBING_BOWEBFEC_VEF')then

		begin

        	/*Verificamos si el nombre del boleto es vacio para recuperar de la tabla tboleto_amadeus*/
            --Ismael Valdivia 10/12/2019
            if (v_parametros.nombre_completo = '') then
            	select ama.pasajero into v_nombre_pasajero
                from obingresos.tboleto_amadeus ama
                where ama.id_boleto_amadeus = v_parametros.id_boleto_amadeus;
            else
            	v_nombre_pasajero = v_parametros.nombre_completo;
            end if ;
            /*****************************************************************************************/
			/***Recuperamos el punto de venta en donde se realizo en canje (Ismael Valdivia 19/12/2019) ***/
            select pv.nombre into v_nombre_punto_venta
            from vef.tpunto_venta pv
            where pv.id_punto_venta = v_parametros.id_punto_venta;
            /****************************************************************/

            /*Recuperamos el usuario que realizo el canje*/
            select per.nombre_completo2 into v_cajero
            from segu.tusuario usu
            inner join segu.vpersona2 per on per.id_persona = usu.id_persona
            where usu.id_usuario = p_id_usuario;
            /*********************************************/



        	INSERT INTO   obingresos.tviajero_frecuente
              (
              id_usuario_reg,
              id_usuario_mod,
              fecha_reg,
              fecha_mod,
              estado_reg,
              id_usuario_ai,
              usuario_ai,
              id_boleto_amadeus,
              ffid,
              pnr,
              ticket_number,
              voucher_code,
              id_pasajero_frecuente,
              nombre_completo,
              mensaje,
              status
              )
              VALUES (
              p_id_usuario,
              null,
              now(),
              null,
              'activo',
              v_parametros._id_usuario_ai,
              v_parametros._nombre_usuario_ai,
              v_parametros.id_boleto_amadeus,
              v_parametros.ffid,
              v_parametros.pnr,
              v_parametros.ticketNumber,
              'OB.FF.VO'||v_parametros.voucherCode,
              v_parametros.id_pasajero_frecuente,
              v_nombre_pasajero,--v_parametros.nombre_completo,
              v_parametros.mensaje,
              v_parametros.status
              )RETURNING id_viajero_frecuente into v_id_viajero_frecuente;



            if (v_parametros.status = 'OK' and v_parametros.bandera ='revisar') then

             select * into v_boleto
            from obingresos.tboleto_amadeus
            where id_boleto_amadeus=v_parametros.id_boleto_amadeus;

        	if (pxp.f_get_variable_global('vef_tiene_apertura_cierre') = 'si') then
                if (exists(	select 1
                                  from vef.tapertura_cierre_caja acc
                                  where acc.id_usuario_cajero = p_id_usuario and
                                  	acc.fecha_apertura_cierre = v_boleto.fecha_emision::date and
                                    acc.estado_reg = 'activo' and acc.estado = 'cerrado' and
                                    acc.id_punto_venta = v_boleto.id_punto_venta)) then
                      raise exception 'La caja ya fue cerrada, necesita tener la caja abierta para poder finalizar la venta del boleto';
                  end if;

                  if (not exists(	select 1
                                  from vef.tapertura_cierre_caja acc
                                  where acc.id_usuario_cajero = p_id_usuario and
                                  	acc.fecha_apertura_cierre = v_boleto.fecha_emision::date and
                                    acc.estado_reg = 'activo' and acc.estado = 'abierto' and
                                    acc.id_punto_venta = v_boleto.id_punto_venta)) then
                      raise exception 'Antes de revisar un boleto debe realizar una apertura de caja';
                  end if;
            end if;

            IF(v_boleto.estado='borrador')THEN

              select sum(param.f_convertir_moneda(fp.id_moneda,bol.id_moneda_boleto,bfp.importe,bol.fecha_emision,'O',2)) into v_monto_total_fp
              from obingresos.tboleto_amadeus_forma_pago bfp
              inner join obingresos.tforma_pago fp on fp.id_forma_pago=bfp.id_forma_pago
              inner join obingresos.tboleto_amadeus bol on bol.id_boleto_amadeus=bfp.id_boleto_amadeus
              where bfp.id_boleto_amadeus=v_parametros.id_boleto_amadeus;

              IF (COALESCE(v_monto_total_fp,0) <(v_boleto.total-COALESCE(v_boleto.comision,0)) and v_boleto.voided='no')THEN
              	raise exception 'El monto total de las formas de pago no iguala con el monto del boleto';
              END IF;

			  IF (COALESCE(v_monto_total_fp,0) >(v_boleto.total-COALESCE(v_boleto.comision,0)+0.02) and v_boleto.voided='no')THEN
              	raise exception 'El monto total de las formas de pago no iguala con el monto del boleto';
              END IF;

              update obingresos.tboleto_amadeus
              set
              estado = 'revisado',
              id_usuario_cajero=p_id_usuario
              where id_boleto_amadeus=v_parametros.id_boleto_amadeus;
            ELSE
              IF(v_boleto.id_usuario_cajero != p_id_usuario)THEN
              	raise exception 'Solo el usuario que reviso puede cambiar el estado del boleto a no revisado';
              END IF;

              update obingresos.tboleto_amadeus
              set
              estado = 'borrador',
              comision = 0.00,
              tipo_comision = 'ninguno',
              id_usuario_cajero=NULL
              where id_boleto_amadeus=v_parametros.id_boleto_amadeus;

              IF (v_boleto.forma_pago = 'CC') THEN
              	DELETE
                FROM obingresos.tboleto_amadeus_forma_pago
                WHERE id_boleto_amadeus=v_parametros.id_boleto_amadeus;
              END IF;

            	END IF;
            end if;

            /***************************Aumentando para alimentar en la tabla tconsulta_viajero_frecuente (Ismael Valdivia 19/12/2019)****************************/
            if (v_id_viajero_frecuente is not null) then

              /*Poniendo este control para ir actualizando si el cajero realiza el canjeado Ismael Valdivia (23/01/2020)*/
              select count (*) into v_consultado
              from obingresos.tconsulta_viajero_frecuente via
              where ffid = v_parametros.ffid and voucher_code = 'OB.FF.VO'||v_parametros.voucherCode;

              select via.estado into v_estado_canjeado
              FROM obingresos.tconsulta_viajero_frecuente via
              where ffid = v_parametros.ffid and voucher_code = 'OB.FF.VO'||v_parametros.voucherCode
              limit 1;

              if (v_consultado >= 1) then
                  if (v_estado_canjeado <> 'Canjeado') then
                    update obingresos.tconsulta_viajero_frecuente set
                    message_canjeado = 'Canjeado por Caja en el punto de venta:'||v_nombre_punto_venta||' por el cajero: '||v_cajero,
                    status_canjeado = 'OK',
                    nro_boleto = '930'||v_parametros.ticketNumber,
                    pnr = v_parametros.pnr,
                    estado = 'Canjeado'
                    where ffid = v_parametros.ffid and voucher_code = 'OB.FF.VO'||v_parametros.voucherCode;
              	  end if;
              else
              /**********************************************************************************/
              insert into obingresos.tconsulta_viajero_frecuente(
                  ffid,
                  estado_reg,
                  message,
                  message_canjeado,
                  voucher_code,
                  status,
                  status_canjeado,
                  nro_boleto,
                  pnr,
                  id_usuario_reg,
                  fecha_reg,
                  usuario_ai,
                  id_usuario_ai,
                  fecha_mod,
                  id_usuario_mod,
                  estado
                  ) values(
                  v_parametros.ffid,
                  'activo',
                  'Verificado por Caja en el punto de venta: '||v_nombre_punto_venta||' por el cajero: '||v_cajero,
                  'Canjeado por Caja en el punto de venta:'||v_nombre_punto_venta||' por el cajero: '||v_cajero,
                  'OB.FF.VO'||v_parametros.voucherCode,
                  v_parametros.status,
                  'OK',
                  '930'||v_parametros.ticketNumber,
                  v_parametros.pnr,
                  p_id_usuario,
                  now(),
                  v_parametros._nombre_usuario_ai,
                  v_parametros._id_usuario_ai,
                  null,
                  null,
                  'Canjeado'
                  );
                  end if;
              END IF;
            /*****************************************************************************************************************************************************/



            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','inserto');
            v_resp = pxp.f_agrega_clave(v_resp,'id_viajero_frecuente',v_id_viajero_frecuente::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

                 /*********************************
              #TRANSACCION:  'OBING_LOG_VEF'
              #DESCRIPCION:	viajero frecuente
              #AUTOR:		mmv
              #FECHA:
              ***********************************/
              elsif(p_transaccion='OBING_LOG_VEF')then

                  begin
             INSERT INTO
            obingresos.tlog_vajero_frecuente
          (
            id_usuario_reg,
            id_usuario_mod,
            fecha_reg,
            fecha_mod,
            estado_reg,
            id_usuario_ai,
            usuario_ai,

            tickert_number,
            pnr,
            importe,
            moneda,
            id_boleto_amadeus
          )
          VALUES (
            p_id_usuario,
            NULL,
            now(),
            NULL,
            'activo',
           v_parametros._id_usuario_ai,
           v_parametros._nombre_usuario_ai,

            v_parametros.tickert_number,
            v_parametros.pnr,
            v_parametros.importe,
            v_parametros.moneda,
            v_parametros.id_boleto_amadeus
           )RETURNING id_log_viajero_frecuente into v_id_log_viajero_frecuente;
            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','inserto');
            v_resp = pxp.f_agrega_clave(v_resp,'id_log_viajero_frecuente',v_id_log_viajero_frecuente::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;
	/*********************************
 	#TRANSACCION:  'OBING_MAIL_DET_VW'
 	#DESCRIPCION:	Envio de correos Error de Detalle Ventas Web
 	#AUTOR:		franklin.espinoza
 	#FECHA:		08-12-2019 11:42:25
	***********************************/

	elsif(p_transaccion='OBING_MAIL_DET_VW')then

		begin
			v_id_correo_det_vw = obingresos.f_send_correo_detalle_diario_vw();

            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Correo enviado');
            v_resp = pxp.f_agrega_clave(v_resp,'id_alarma',v_id_correo_det_vw::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;
	/*********************************
 	#TRANSACCION:  'OBING_VER_EXCH_IME'
 	#DESCRIPCION:	Verificar y actualizar si es un exchange.
 	#AUTOR:		franklin.espinoza
 	#FECHA:		02-02-2019 11:42:25
	***********************************/

	elsif(p_transaccion='OBING_VER_EXCH_IME')then

		begin

        	if jsonb_typeof(v_parametros.tipo_emision) = 'string' then
            	v_tipo_emision = v_parametros.tipo_emision::varchar;
                /*SELECT value
                into v_tipo_emision
   				FROM jsonb_array_elements_text(v_parametros.tipo_emision);*/
            else
                for v_exchange_json in SELECT * FROM jsonb_array_elements(v_parametros.tipo_emision)  loop
                    v_tipo_emision = v_exchange_json->>'tipo_emision';
                end loop;
            end if;

            v_exch_sel = string_to_array(v_parametros.id_boletos_amadeus,',');


            if v_parametros.data_field similar to '[0-9]%' then
            	select array_agg(tba.nro_boleto)
            	into v_exchange
            	from obingresos.tboleto_amadeus tba
            	where tba.nro_boleto = v_parametros.data_field::varchar;
            else
            	if v_parametros.exchange = true and array_length(v_exch_sel,1) = 1 then


                	for v_record in select tba.nro_boleto, tba.pasajero
                                            from obingresos.tboleto_amadeus tba
                                            where tba.localizador = v_parametros.data_field::varchar and tba.trans_code != 'EMDS' loop

                        if 	v_record.pasajero != v_nombre_pasajero then
                        	v_exchange[v_cont_canje] = v_record.nro_boleto;
                        	v_nombre_pasajero = v_record.pasajero;
                            v_cont_canje = v_cont_canje + 1;
                        end if;
                    end loop;

                    if array_length(v_exchange,1) = 1 then

                    	select max(tba.nro_boleto) as exchange, min(tba.nro_boleto) as original
                        into v_exch_orig_exch
                        from obingresos.tboleto_amadeus tba
                        where tba.localizador = v_parametros.data_field::varchar and tba.trans_code != 'EMDS';

                    	v_exchange = string_to_array(v_exch_orig_exch.exchange,',');

                        update obingresos.tboleto_amadeus set
                            trans_code_exch = 'ORIG'
                        where nro_boleto = v_exch_orig_exch.original::varchar;
                    end if;

                else
                	select array_agg(tba.nro_boleto)
                  	into v_exchange
                  	from obingresos.tboleto_amadeus tba
                  	where tba.localizador = v_parametros.data_field::varchar and tba.trans_code != 'EMDS';
                end if;

            end if;
--RAISE EXCEPTION 'v_tipo_emision:%',v_tipo_emision = '"R"';
            if v_parametros.exchange = true and v_tipo_emision = '"R"' then
              update  obingresos.tboleto_amadeus set
                  trans_code_exch = 'EXCH'
              where trans_code in ('TKTT') and fecha_emision = v_parametros.fecha_emision::date
              and /*(localizador = v_parametros.pnr or */nro_boleto = any(v_exchange);

            end if;

            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Actualización Exitosa');
            v_resp = pxp.f_agrega_clave(v_resp,'exchange', v_parametros.exchange::varchar);
            v_resp = pxp.f_agrega_clave(v_resp,'tipo_emision',v_tipo_emision::varchar);


            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
    #TRANSACCION: 'OBING_GET_PV'
    #DESCRIPCION: RECUPERA LOS PUNTOS DE VENTAS
    #AUTOR: ISMAEL VALDIVIA ARANIBAR
    #FECHA: 02/03/2020
    ***********************************/

	elsif (p_transaccion = 'OBING_GET_PV') then

  	BEGIN
			if (p_id_usuario = 1) then
            	v_puntos_venta = 'todos';
            else
            	select ex.usuario_externo into v_agente_venta
                from segu.tusuario_externo ex
                where ex.id_usuario = p_id_usuario;

                select list(distinct (ama.id_punto_venta)::varchar) into v_puntos_venta
                from obingresos.tboleto_amadeus ama
                where ama.agente_venta = v_agente_venta;
            end if;



      --Definition of the response
      	v_resp = pxp.f_agrega_clave(v_resp, 'message ', 'Puntos Venta');
        v_resp = pxp.f_agrega_clave(v_resp,'v_puntos_venta',v_puntos_venta::varchar);


      --Returns the answer
        return v_resp;

  	END;


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

ALTER FUNCTION obingresos.ft_boleto_ime (p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
  OWNER TO postgres;
