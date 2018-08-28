CREATE OR REPLACE FUNCTION obingresos.ft_periodo_venta_ime (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Ingresos
 FUNCION: 		obingresos.ft_periodo_venta_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'obingresos.tperiodo_venta'
 AUTOR: 		 (jrivera)
 FECHA:	        08-04-2016 22:44:37
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
	v_id_periodo_venta	integer;
	v_respuesta				varchar;
    v_max_fecha_fin			date;
    v_fecha_ini				date;
    v_fecha_fin				date;
    v_tipo_periodo			record;
    v_fecha_ultimo_dia_mes	date;
    v_mes					varchar;
    v_nro_periodo_mes		integer;
    v_id_moneda_base		integer;
    v_id_moneda_usd			integer;
    v_registros				record;
    v_codigo_periodo		varchar;
    v_fecha_max_boleto		date;
    v_fecha_max_boleto_ret	date;
    v_id_periodo_venta_agencia	integer;


BEGIN

    v_nombre_funcion = 'obingresos.ft_periodo_venta_ime';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'OBING_PERVEN_INS'
 	#DESCRIPCION:	Insercion de registros
 	#AUTOR:		jrivera
 	#FECHA:		08-04-2016 22:44:37
	***********************************/

	if(p_transaccion='OBING_PERVEN_INS')then

        begin

        	if (exists (select 1
                        from obingresos.ttipo_periodo tp
                        where tp.estado = 'inactivo' and
                        tp.id_tipo_periodo = v_parametros.id_tipo_periodo )) then
            	raise exception 'No es posible generar el periodo debido a que el tipo periodo seleccionado se encuentra inactivo';
            end if;

            select *  into v_tipo_periodo
            from obingresos.ttipo_periodo
            where id_tipo_periodo = v_parametros.id_tipo_periodo;

            select max(pv.fecha_fin) into v_max_fecha_fin
            from obingresos.tperiodo_venta pv
            inner join obingresos.ttipo_periodo tp on tp.id_tipo_periodo = pv.id_tipo_periodo
            where tp.tipo = v_tipo_periodo.tipo and tp.medio_pago = v_tipo_periodo.medio_pago;

            if (v_max_fecha_fin is null) then
            	v_max_fecha_fin = v_tipo_periodo.fecha_ini_primer_periodo - interval '1 day';
            end if;

            v_fecha_ini = v_max_fecha_fin + interval '1 day';
            v_fecha_ultimo_dia_mes = pxp.f_obtener_ultimo_dia_mes(to_char(v_fecha_ini,'MM')::numeric,to_char(v_fecha_ini,'YYYY')::numeric);
            if (v_tipo_periodo.tiempo = '1d') then
            	v_fecha_fin = v_fecha_ini;
            elsif (v_tipo_periodo.tiempo = '2d') then
            	v_fecha_fin = v_fecha_ini + interval '1 day';
                if ((to_char(v_fecha_fin,'DD') = '30' and to_char(v_fecha_ultimo_dia_mes,'DD') = '31') or
                	(to_char(v_fecha_fin,'DD') = '28' and to_char(v_fecha_ultimo_dia_mes,'DD') = '29')) then
                	v_fecha_fin = v_fecha_ultimo_dia_mes;
                end if;
            elsif (v_tipo_periodo.tiempo = '5d') then
            	v_fecha_fin = v_fecha_ini + interval '4 day';
                if (to_char(v_fecha_fin,'DD') = '25') then
                	v_fecha_fin = v_fecha_ultimo_dia_mes;
                end if;
            elsif (v_tipo_periodo.tiempo = 'bsp') then

            	if (to_char (v_fecha_ini,'DD') = '01') then
                	v_fecha_fin = ('08/' || to_char (v_fecha_ini,'MM/YYYY'))::date;
                elsif (to_char (v_fecha_ini,'DD') = '09') then
                	v_fecha_fin = ('15/' || to_char (v_fecha_ini,'MM/YYYY'))::date;
                elsif  (to_char (v_fecha_ini,'DD') = '16') then
                	v_fecha_fin = ('23/' || to_char (v_fecha_ini,'MM/YYYY'))::date;
                elsif  (to_char (v_fecha_ini,'DD') = '24') then
                	v_fecha_fin = v_fecha_ultimo_dia_mes;
                end if;
            elsif (v_tipo_periodo.tiempo = 'lun_mie_vie') then
            	v_fecha_fin = v_fecha_ini;
            	if (extract(dow from  v_fecha_ini)::integer in (1,2)) then
                	if (extract(dow from  v_fecha_ini)::integer in (1)) then
                    	v_fecha_fin = v_fecha_ini + interval '1 day';
                    end if;

                elsif (extract(dow from  v_fecha_ini)::integer in (3,4)) then
                	if (extract(dow from  v_fecha_ini)::integer in (3)) then
                    	v_fecha_fin = v_fecha_ini + interval '1 day';
                    end if;
                else
                	if (extract(dow from  v_fecha_ini)::integer in (5)) then
                    	v_fecha_fin = v_fecha_ini + interval '2 day';
                    elsif (extract(dow from  v_fecha_ini)::integer in (6)) then
                    	v_fecha_fin = v_fecha_ini + interval '1 day';
                    end if;
                end if;

            end if;

            --si llega el parametro fecha y la feha fin es mayor a esa fecha no se inserta el prediodo y se devuelve la respuesta
            if (pxp.f_existe_parametro(p_tabla,'fecha')) then
            	if (v_parametros.fecha < v_fecha_fin) then
                	--no es necesario generar el periodo a esta fecha
                	v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Periodo de Venta no generado porque todavia no corresponde a esta fecha');
            		v_resp = pxp.f_agrega_clave(v_resp,'fecha',to_char(v_parametros.fecha,'DD/MM/YYYY')::varchar);

            		--Devuelve la respuesta
            		return v_resp;
                end if;
            end if;
            v_mes = pxp.f_obtener_literal_periodo(to_char(v_fecha_ini,'MM')::integer,0);
            v_codigo_periodo = obingresos.f_get_codigo_periodo(v_parametros.id_tipo_periodo,v_fecha_ini);

		INSERT INTO
              obingresos.tperiodo_venta
            (
              id_usuario_reg,
              id_usuario_ai,
              usuario_ai,
              mes,
              nro_periodo_mes,
              fecha_ini,
              fecha_fin,
              estado,
              id_gestion,
              id_tipo_periodo,
              codigo_periodo
            )
            VALUES (
              p_id_usuario,
              v_parametros._id_usuario_ai,
              v_parametros._nombre_usuario_ai,
              v_mes,
              0,
              v_fecha_ini,
              v_fecha_fin,
              'activo',
              (select po_id_gestion from param.f_get_periodo_gestion(v_fecha_ini)),
              v_parametros.id_tipo_periodo,
              v_codigo_periodo
            )returning id_periodo_venta into v_id_periodo_venta;

            v_id_moneda_base = (select param.f_get_moneda_base());
            select m.id_moneda into v_id_moneda_usd
            from param.tmoneda m
            where m.codigo_internacional = 'USD';

            if (v_id_moneda_usd is null) then
            	raise exception 'No se ha definido la moneda USD';
            end if;

            /*Actualizar los movimientos relacionados a boletos de manera correcta con el id_periodo_venta*/
            --si el medio_pago es cuenta_corriente
            select max(b.fecha_emision) into v_fecha_max_boleto
            from obingresos.tboleto b
            where b.estado_reg = 'activo';


            if (v_tipo_periodo.medio_pago = 'cuenta_corriente') then

            	--actualizar boletos que esten relacionados por boleto
                with temp_bol as (
                    select dbw.numero_autorizacion,dbw.fecha,dbw.moneda,sum(dbw.importe) as importe
                    from obingresos.tboleto b
                    inner join obingresos.tdetalle_boletos_web dbw on dbw.billete = b.nro_boleto
                    where b.estado_reg = 'activo' and dbw.origen = 'portal' and
                    	b.voided = 'no' and b.fecha_emision <= v_fecha_fin and
                		b.fecha_emision <= v_fecha_max_boleto
                    group by dbw.numero_autorizacion,dbw.fecha,dbw.moneda
                )
                update obingresos.tmovimiento_entidad m
                set id_periodo_venta = v_id_periodo_venta
                from temp_bol dbw
                where m.tipo = 'debito' and m.ajuste = 'no' AND
                dbw.numero_autorizacion = m.autorizacion__nro_deposito and
                m.id_periodo_venta is null and m.estado_reg = 'activo' and
                m.monto_total = dbw.importe and m.fecha <= v_fecha_fin;


        /*        with temp_bol as (
                    select dbw.numero_autorizacion,dbw.fecha,dbw.moneda,sum(dbw.importe) as importe
                    from obingresos.tdetalle_boletos_web dbw
                    where dbw.estado_reg = 'activo' and
                              dbw.origen = 'portal' and
                              dbw.fecha='13/03/2018' and
                              dbw.void = 'no'
                    group by dbw.numero_autorizacion,dbw.fecha,dbw.moneda
                )
                update obingresos.tmovimiento_entidad m
                set id_periodo_venta = v_id_periodo_venta
                from temp_bol dbw
                where m.tipo = 'debito' and m.ajuste = 'no' AND
                dbw.numero_autorizacion = m.autorizacion__nro_deposito and
                m.id_periodo_venta is null and m.estado_reg = 'activo' and
                m.monto_total = dbw.importe and m.fecha <= v_fecha_fin;*/

                --actualizar boletos que esten relacionados por boleto_ret_web

                with temp_bol as (
                    select dbw.numero_autorizacion,dbw.fecha,dbw.moneda,sum(dbw.importe) as importe
                    from obingresos.tboleto b
                    inner join obingresos.tdetalle_boletos_web dbw on dbw.billete = b.nro_boleto
                    where b.estado_reg = 'activo' and dbw.origen = 'portal' and
                    	b.voided = 'no' and b.fecha_emision <= v_fecha_fin
                    group by dbw.numero_autorizacion,dbw.fecha,dbw.moneda
                )
                update obingresos.tmovimiento_entidad m
                set id_periodo_venta = v_id_periodo_venta
                from temp_bol dbw
                where m.tipo = 'debito' and ajuste = 'no' AND
                dbw.numero_autorizacion = m.autorizacion__nro_deposito and
                m.id_periodo_venta is null and m.fecha <= v_fecha_fin and
                m.fecha > v_fecha_max_boleto and m.estado_reg = 'activo' and
                m.id_periodo_venta is null  and
                m.monto_total = dbw.importe;

                --actualizar los creditos,  se toman en cuenta creditos incluso posteriores
                update obingresos.tmovimiento_entidad m
                set id_periodo_venta = v_id_periodo_venta
                where (m.tipo = 'credito') and
                m.id_periodo_venta is null and m.estado_reg = 'activo';

                --actualizar los debitos de tipo ajuste
                update obingresos.tmovimiento_entidad m
                set id_periodo_venta = v_id_periodo_venta
                where (m.tipo = 'debito' and ajuste = 'si') and
                m.id_periodo_venta is null and m.fecha <= v_fecha_fin and m.estado_reg = 'activo';

           -- si el tipo de periodo es
            elsif (v_tipo_periodo.medio_pago = 'banca_internet') then
            	--actualizar detalle boletos web asociados a boletos
                update obingresos.tdetalle_boletos_web dbw
                set id_periodo_venta = v_id_periodo_venta
                from obingresos.tboleto b
                where  b.nro_boleto = dbw.billete and
                dbw.medio_pago = 'portal' and dbw.entidad_pago = 'BUN' and
                dbw.id_periodo_venta is null and dbw.fecha <= v_fecha_fin and b.estado_reg = 'activo'
                and dbw.fecha <= v_fecha_max_boleto and b.voided = 'no' and dbw.estado_reg = 'activo';

                --actualizar detalle boletos web asociados a boletos ret web
                update obingresos.tdetalle_boletos_web dbw
                set id_periodo_venta = v_id_periodo_venta
                from obingresos.tboleto_retweb b
                where  b.nro_boleto = dbw.billete and
                dbw.medio_pago = 'portal' and dbw.entidad_pago = 'BUN' and
                dbw.id_periodo_venta is null and dbw.fecha <= v_fecha_fin and b.estado_reg = 'activo'
                and dbw.fecha > v_fecha_max_boleto and b.estado = '1' and dbw.estado_reg = 'activo';


            end if;
        	--crear periodo_venta para todas las agencias cuya moneda es restrictiva
            for v_registros in (select
            						me.id_agencia,
            					sum(case when me.id_moneda = v_id_moneda_base then
                                		(case when me.tipo = 'credito' and me.garantia = 'no' then
                                        	me.monto
                                        else
                                        	0
                                        end)
                                	else
                                    	0
                                    END) as monto_credito_mb,
                                sum(case when me.id_moneda != v_id_moneda_base then
                                		(case when me.tipo = 'credito' and me.garantia = 'no' then
                                        	me.monto
                                        else
                                        	0
                                        end)
                                	else
                                    	0
                                    END) as monto_credito_usd,
                                sum(case when me.id_moneda = v_id_moneda_base then
                                		(case when me.tipo = 'debito' then
                                        	me.monto
                                        else
                                        	0
                                        end)
                                	else
                                    	0
                                    END) as monto_debito_mb,
                                sum(case when me.id_moneda != v_id_moneda_base then
                                		(case when me.tipo = 'debito' then
                                        	me.monto
                                        else
                                        	0
                                        end)
                                	else
                                    	0
                                    END) as monto_debito_usd,

                                sum(case when me.id_moneda = v_id_moneda_base then
                                		(case when me.tipo = 'debito' and me.pnr is not null then
                                        	me.monto_total
                                        else
                                        	0
                                        end)
                                	else
                                    	0
                                    END) as monto_boletos_mb,
                                sum(case when me.id_moneda != v_id_moneda_base then
                                		(case when me.tipo = 'debito' and me.pnr is not null then
                                        	me.monto_total
                                        else
                                        	0
                                        end)
                                	else
                                    	0
                                    END) as monto_boletos_usd,
                                sum(case when me.id_moneda = v_id_moneda_base then
                                		(case when me.tipo = 'debito' and me.pnr is not null then
                                        	me.neto
                                        else
                                        	0
                                        end)
                                	else
                                    	0
                                    END) as monto_neto_mb,
                                sum(case when me.id_moneda != v_id_moneda_base then
                                		(case when me.tipo = 'debito' and me.pnr is not null then
                                        	me.neto
                                        else
                                        	0
                                        end)
                                	else
                                    	0
                                    END) as monto_neto_usd,
								sum(case when me.id_moneda = v_id_moneda_base then
                                		(case when me.tipo = 'debito' and me.pnr is not null then
                                        	me.monto_total - me.monto
                                        else
                                        	0
                                        end)
                                	else
                                    	0
                                    END) as monto_comision_mb,
                                sum(case when me.id_moneda != v_id_moneda_base then
                                		(case when me.tipo = 'debito' and me.pnr is not null then
                                        	me.monto_total - me.monto
                                        else
                                        	0
                                        end)
                                	else
                                    	0
                                    END) as monto_comision_usd,
                                sum(case when me.id_moneda = v_id_moneda_base then
                                		(case when me.tipo = 'debito' and me.pnr is not null then
                                        	me.monto_total - me.monto
                                        else
                                        	0
                                        end)
                                	else
                                    	(case when me.tipo = 'debito' and me.pnr is not null then
                                        	param.f_convertir_moneda(v_id_moneda_usd,v_id_moneda_base,me.monto_total - me.monto,me.fecha,'O',2)
                                        else
                                        	0
                                        end)
                                    END) as total_comision_mb,
                                c.moneda_restrictiva
            					from obingresos.tmovimiento_entidad me
                                inner join obingresos.tperiodo_venta pv on me.id_periodo_venta = pv.id_periodo_venta
                                inner join leg.tcontrato c on c.id_agencia = me.id_agencia and
                                			c.fecha_fin >= v_fecha_ini and c.fecha_inicio <= v_fecha_ini --and c.estado = 'finalizado'
                                inner join obingresos.ttipo_periodo tp on tp.id_tipo_periodo = pv.id_tipo_periodo
                                where me.id_periodo_venta = v_id_periodo_venta and c.moneda_restrictiva = 'si' and me.estado_reg = 'activo'
                                group by me.id_agencia,c.moneda_restrictiva )loop

            	--si la moneda es restrictiva registrar los saldos por pagar en periodo_venta_agencia
                --y si existe saldo a favor d ela agenicia llevar como movimiento al siguient eperiodo

                	INSERT INTO
                      obingresos.tperiodo_venta_agencia
                    (
                      id_usuario_reg,
                      id_usuario_ai,
                      usuario_ai,
                      id_agencia,
                      id_periodo_venta,
                      monto_usd,
                      monto_mb,
                      estado,
                      fecha_cierre,
                      moneda_restrictiva,
                      monto_credito_mb,
                      monto_credito_usd,
                      monto_debito_mb,
                      monto_debito_usd,
                      monto_boletos_mb,
                      monto_boletos_usd,
                      monto_neto_mb,
                      monto_neto_usd,
                      monto_comision_mb,
                      monto_comision_usd,
                      total_comision_mb

                    )
                    VALUES (
                      p_id_usuario,
                      v_parametros._id_usuario_ai,
              		  v_parametros._nombre_usuario_ai,
                      v_registros.id_agencia,
                      v_id_periodo_venta,
                      (case when v_registros.monto_credito_usd < v_registros.monto_debito_usd then
                      	v_registros.monto_credito_usd - v_registros.monto_debito_usd      --monto en negativo que debe la agencia en moneda usd
                      ELSE
                      	0
                      end),
                      (case when v_registros.monto_credito_mb < v_registros.monto_debito_mb then
                      	v_registros.monto_credito_mb - v_registros.monto_debito_mb    --monto en negativo que debe la agencia en moneda base
                      ELSE
                      	0
                      end),
                      (case when (v_registros.monto_credito_usd - v_registros.monto_debito_usd) >= 0 and
                      	(v_registros.monto_credito_mb - v_registros.monto_debito_mb) >= 0 then
                      		'cerrado'
                      else
                      		'abierto'
                      END),
                      now()::Date,
                      'si',
                      v_registros.monto_credito_mb,
                      v_registros.monto_credito_usd,
                      v_registros.monto_debito_mb,
                      v_registros.monto_debito_usd,
                      v_registros.monto_boletos_mb,
                      v_registros.monto_boletos_usd,
                      v_registros.monto_neto_mb,
                      v_registros.monto_neto_usd,
                      v_registros.monto_comision_mb,
                      v_registros.monto_comision_usd,
                      v_registros.total_comision_mb
                    )RETURNING id_periodo_venta_agencia into v_id_periodo_venta_agencia;

                    if (v_registros.monto_credito_usd - v_registros.monto_debito_usd > 0 ) then
                       --llevamos el saldo del credito al periodo siguiente
                        INSERT INTO
                        obingresos.tmovimiento_entidad
                      (
                        id_usuario_reg,
                        id_usuario_ai,
                        usuario_ai,
                        tipo,
                        pnr,
                        fecha,
                        apellido,
                        monto,
                        id_moneda,
                        autorizacion__nro_deposito,
                        garantia,
                        ajuste,
                        id_periodo_venta,
                        id_agencia,
                        monto_total,
                        cierre_periodo
                      )
                      VALUES (
                        p_id_usuario,
                         v_parametros._id_usuario_ai,
              		  	 v_parametros._nombre_usuario_ai,
                        'credito',
                        NULL,
                        v_fecha_fin + interval '1 day',
                        NULL,
                        v_registros.monto_credito_usd - v_registros.monto_debito_usd,
                        v_id_moneda_usd,
                        NULL,
                        'no',
                        'no',
                        NULL,
                        v_registros.id_agencia,
                        v_registros.monto_credito_usd - v_registros.monto_debito_usd,
                        'si'
                      );
                      --insertamos un debito al periodo actual por el credito que se paso al periodo siguiente
                      INSERT INTO
                        obingresos.tmovimiento_entidad
                      (
                        id_usuario_reg,
                        id_usuario_ai,
                        usuario_ai,
                        tipo,
                        pnr,
                        fecha,
                        apellido,
                        monto,
                        id_moneda,
                        autorizacion__nro_deposito,
                        garantia,
                        ajuste,
                        id_periodo_venta,
                        id_agencia,
                        monto_total,
                        cierre_periodo
                      )
                      VALUES (
                        p_id_usuario,
                         v_parametros._id_usuario_ai,
              		  	 v_parametros._nombre_usuario_ai,
                        'debito',
                        NULL,
                        v_fecha_fin,
                        NULL,
                        v_registros.monto_credito_usd - v_registros.monto_debito_usd,
                        v_id_moneda_usd,
                        NULL,
                        'no',
                        'no',
                        v_id_periodo_venta,
                        v_registros.id_agencia,
                        v_registros.monto_credito_usd - v_registros.monto_debito_usd,
                        'si'
                      );
                      --se incrementa el debito del periodo actual
                      update obingresos.tperiodo_venta_agencia
                      set monto_debito_usd = monto_debito_usd + (v_registros.monto_credito_usd - v_registros.monto_debito_usd)
                      where id_periodo_venta_agencia = v_id_periodo_venta_agencia;
                    end if;

                    if (v_registros.monto_credito_mb - v_registros.monto_debito_mb > 0 ) then
                    	INSERT INTO
                        obingresos.tmovimiento_entidad
                      (
                        id_usuario_reg,
                        id_usuario_ai,
                        usuario_ai,
                        tipo,
                        pnr,
                        fecha,
                        apellido,
                        monto,
                        id_moneda,
                        autorizacion__nro_deposito,
                        garantia,
                        ajuste,
                        id_periodo_venta,
                        id_agencia,
                        monto_total,
                        cierre_periodo
                      )
                      VALUES (
                        p_id_usuario,
                         v_parametros._id_usuario_ai,
              		  	 v_parametros._nombre_usuario_ai,
                        'credito',
                        NULL,
                        v_fecha_fin + interval '1 day',
                        NULL,
                        v_registros.monto_credito_mb - v_registros.monto_debito_mb,
                        v_id_moneda_base,
                        NULL,
                        'no',
                        'no',
                        NULL,
                        v_registros.id_agencia,
                        v_registros.monto_credito_mb - v_registros.monto_debito_mb,
                        'si'
                      );

                      --insertamos un debito al periodo actual por el credito que se paso al periodo siguiente
                      INSERT INTO
                        obingresos.tmovimiento_entidad
                      (
                        id_usuario_reg,
                        id_usuario_ai,
                        usuario_ai,
                        tipo,
                        pnr,
                        fecha,
                        apellido,
                        monto,
                        id_moneda,
                        autorizacion__nro_deposito,
                        garantia,
                        ajuste,
                        id_periodo_venta,
                        id_agencia,
                        monto_total,
                        cierre_periodo
                      )
                      VALUES (
                        p_id_usuario,
                         v_parametros._id_usuario_ai,
              		  	 v_parametros._nombre_usuario_ai,
                        'debito',
                        NULL,
                        v_fecha_fin,
                        NULL,
                        v_registros.monto_credito_mb - v_registros.monto_debito_mb,
                        v_id_moneda_base,
                        NULL,
                        'no',
                        'no',
                        v_id_periodo_venta,
                        v_registros.id_agencia,
                        v_registros.monto_credito_mb - v_registros.monto_debito_mb,
                        'si'
                      );

                      --se incrementa el debito del periodo actual
                      update obingresos.tperiodo_venta_agencia
                      set monto_debito_mb = monto_debito_mb + (v_registros.monto_credito_mb - v_registros.monto_debito_mb)
                      where id_periodo_venta_agencia = v_id_periodo_venta_agencia;
                    end if;
            end loop;
            --crear periodo_venta para todas las agencias cuya moneda es no restrictiva
            for v_registros in (select
            						me.id_agencia,
            					sum(case when me.id_moneda = v_id_moneda_base then
                                		(case when me.tipo = 'credito' and me.garantia = 'no' then
                                        	me.monto
                                        else
                                        	0
                                        end)
                                	else
                                    	(case when me.tipo = 'credito' and me.garantia = 'no' then
                                        	param.f_convertir_moneda(v_id_moneda_usd,v_id_moneda_base,me.monto,me.fecha,'O',2)
                                        else
                                        	0
                                        end)
                                    END) as monto_credito_mb,
                                 sum(case when me.id_moneda = v_id_moneda_base then
                                		(case when me.tipo = 'debito' then
                                        	me.monto
                                        else
                                        	0
                                        end)
                                	else
                                    	(case when me.tipo = 'debito' then
                                        	param.f_convertir_moneda(v_id_moneda_usd,v_id_moneda_base,me.monto,me.fecha,'O',2)
                                        else
                                        	0
                                        end)
                                    END) as monto_debito_mb,

                                 sum(case when me.id_moneda = v_id_moneda_base then
                                		(case when me.tipo = 'debito' and me.pnr is not null then
                                        	me.monto_total
                                        else
                                        	0
                                        end)
                                	else
                                    	(case when me.tipo = 'debito' and me.pnr is not null then
                                        	param.f_convertir_moneda(v_id_moneda_usd,v_id_moneda_base,me.monto_total,me.fecha,'O',2)
                                        else
                                        	0
                                        end)
                                    END) as monto_boletos_mb,
                                 sum(case when me.id_moneda = v_id_moneda_base then
                                		(case when me.tipo = 'debito' and me.pnr is not null then
                                        	me.neto
                                        else
                                        	0
                                        end)
                                	else
                                    	(case when me.tipo = 'debito' and me.pnr is not null then
                                        	param.f_convertir_moneda(v_id_moneda_usd,v_id_moneda_base,me.neto,me.fecha,'O',2)
                                        else
                                        	0
                                        end)
                                    END) as monto_neto_mb,
                                 sum(case when me.id_moneda = v_id_moneda_base then
                                		(case when me.tipo = 'debito' and me.pnr is not null then
                                        	me.monto_total - me.monto
                                        else
                                        	0
                                        end)
                                	else
                                    	(case when me.tipo = 'debito' and me.pnr is not null then
                                        	param.f_convertir_moneda(v_id_moneda_usd,v_id_moneda_base,me.monto_total - me.monto,me.fecha,'O',2)
                                        else
                                        	0
                                        end)
                                    END) as monto_comision_mb
            					from obingresos.tmovimiento_entidad me
                                inner join obingresos.tperiodo_venta pv on me.id_periodo_venta = pv.id_periodo_venta
                                inner join leg.tcontrato c on c.id_agencia = me.id_agencia and
                                			c.fecha_fin >= v_fecha_ini and c.fecha_inicio <= v_fecha_ini --and c.estado = 'finalizado'
                                inner join obingresos.ttipo_periodo tp on tp.id_tipo_periodo = pv.id_tipo_periodo
                                where me.id_periodo_venta = v_id_periodo_venta and (c.moneda_restrictiva = 'no' or c.moneda_restrictiva is null) and me.estado_reg = 'activo'
                                group by me.id_agencia,c.moneda_restrictiva )loop

            	--si la moneda no es restrictiva registrar los saldos por pagar en periodo_venta_agencia
                --y si existe saldo a favor d ela agenicia llevar como movimiento al siguient eperiodo

                	INSERT INTO
                      obingresos.tperiodo_venta_agencia
                    (
                      id_usuario_reg,
                      id_usuario_ai,
                      usuario_ai,
                      id_agencia,
                      id_periodo_venta,
                      monto_usd,
                      monto_mb,
                      estado,
                      fecha_cierre,
                      moneda_restrictiva,
                      monto_credito_mb,
                      monto_debito_mb,
                      monto_boletos_mb,
                      monto_neto_mb,
                      monto_comision_mb,
                      total_comision_mb
                    )
                    VALUES (
                      p_id_usuario,
                      v_parametros._id_usuario_ai,
              		  v_parametros._nombre_usuario_ai,
                      v_registros.id_agencia,
                      v_id_periodo_venta,
                      0,
                      (case when v_registros.monto_credito_mb < v_registros.monto_debito_mb then
                      	v_registros.monto_credito_mb - v_registros.monto_debito_mb    --monto en negativo que debe la agencia en moneda base
                      ELSE
                      	0
                      end),
                      (case when v_registros.monto_credito_mb >= v_registros.monto_debito_mb then
                      		'cerrado'
                      else
                      		'abierto'
                      END),
                      now()::Date,
                      'no',
                      v_registros.monto_credito_mb,
                      v_registros.monto_debito_mb,
                      v_registros.monto_boletos_mb,
                      v_registros.monto_neto_mb,
                      v_registros.monto_comision_mb,
                      v_registros.monto_comision_mb
                    )RETURNING id_periodo_venta_agencia into v_id_periodo_venta_agencia;

                    if (v_registros.monto_credito_mb > v_registros.monto_debito_mb) then
                    	INSERT INTO
                        obingresos.tmovimiento_entidad
                      (
                        id_usuario_reg,
                        id_usuario_ai,
                        usuario_ai,
                        tipo,
                        pnr,
                        fecha,
                        apellido,
                        monto,
                        id_moneda,
                        autorizacion__nro_deposito,
                        garantia,
                        ajuste,
                        id_periodo_venta,
                        id_agencia,
                        monto_total,
                        cierre_periodo
                      )
                      VALUES (
                        p_id_usuario,
                         v_parametros._id_usuario_ai,
              		  	 v_parametros._nombre_usuario_ai,
                        'credito',
                        NULL,
                        v_fecha_fin + interval '1 day',
                        NULL,
                        v_registros.monto_credito_mb - v_registros.monto_debito_mb,
                        v_id_moneda_base,
                        NULL,
                        'no',
                        'no',
                        NULL,
                        v_registros.id_agencia,
                        v_registros.monto_credito_mb - v_registros.monto_debito_mb,
                        'si'
                      );

                      --insertamos un debito al periodo actual por el credito que se paso al periodo siguiente
                      INSERT INTO
                        obingresos.tmovimiento_entidad
                      (
                        id_usuario_reg,
                        id_usuario_ai,
                        usuario_ai,
                        tipo,
                        pnr,
                        fecha,
                        apellido,
                        monto,
                        id_moneda,
                        autorizacion__nro_deposito,
                        garantia,
                        ajuste,
                        id_periodo_venta,
                        id_agencia,
                        monto_total,
                        cierre_periodo
                      )
                      VALUES (
                        p_id_usuario,
                         v_parametros._id_usuario_ai,
              		  	 v_parametros._nombre_usuario_ai,
                        'debito',
                        NULL,
                        v_fecha_fin,
                        NULL,
                        v_registros.monto_credito_mb - v_registros.monto_debito_mb,
                        v_id_moneda_base,
                        NULL,
                        'no',
                        'no',
                        v_id_periodo_venta,
                        v_registros.id_agencia,
                        v_registros.monto_credito_mb - v_registros.monto_debito_mb,
                        'si'
                      );

                      --se incrementa el debito del periodo actual
                      update obingresos.tperiodo_venta_agencia
                      set monto_debito_mb = monto_debito_mb + (v_registros.monto_credito_mb - v_registros.monto_debito_mb)
                      where id_periodo_venta_agencia = v_id_periodo_venta_agencia;
                    end if;
            end loop;

            --llevar todas las garantias al siguiente periodo
            for v_registros in select *
            					from obingresos.tmovimiento_entidad me
                                where me.id_periodo_venta = v_id_periodo_venta and
                                me.garantia = 'si' and me.tipo = 'credito' and me.estado_reg = 'activo' loop

            	INSERT INTO
                        obingresos.tmovimiento_entidad
                      (
                        id_usuario_reg,
                        id_usuario_ai,
                        usuario_ai,
                        tipo,
                        pnr,
                        fecha,
                        apellido,
                        monto,
                        id_moneda,
                        autorizacion__nro_deposito,
                        garantia,
                        ajuste,
                        id_periodo_venta,
                        id_agencia,
                        monto_total
                      )
                      VALUES (
                        p_id_usuario,
                         v_parametros._id_usuario_ai,
              		  	 v_parametros._nombre_usuario_ai,
                        'credito',
                        NULL,
                        (v_fecha_fin + interval '1 day')::date,
                        NULL,
                        v_registros.monto,
                        v_registros.id_moneda,
                        NULL,
                        'si',
                        'no',
                        NULL,
                        v_registros.id_agencia,
                        v_registros.monto
                      );
            end loop;



           --Registrar periodo venta para boletos banca por internet

            for v_registros in (select
            						b.id_agencia,
            					sum(case when b.id_moneda = v_id_moneda_base then
                                		b.importe
                                	else
                                    	0
                                    END) as monto_mb,
                                sum(case when b.id_moneda != v_id_moneda_base then
                                		b.importe
                                	else
                                    	0
                                END) as monto_usd
            					from obingresos.tdetalle_boletos_web b
                                where b.id_periodo_venta = v_id_periodo_venta and b.estado_reg = 'activo' AND
                                b.origen = 'portal'
                                group by b.id_agencia )loop

            	INSERT INTO
                      obingresos.tperiodo_venta_agencia
                    (
                      id_usuario_reg,
                      id_usuario_ai,
                      usuario_ai,
                      id_agencia,
                      id_periodo_venta,
                      monto_usd,
                      monto_mb,
                      estado,
                      fecha_cierre,
                      moneda_restrictiva
                    )
                    VALUES (
                      p_id_usuario,
                      v_parametros._id_usuario_ai,
              		  v_parametros._nombre_usuario_ai,
                      v_registros.id_agencia,
                      v_id_periodo_venta,
                      v_registros.monto_usd,      --monto en negativo que debe la agencia en moneda usd
                      v_registros.monto_mb    --monto en negativo que debe la agencia en moneda base
                      'cerrado',
                      now()::Date,
                      'si'
                    );
            end loop;
            --Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Periodo de Venta almacenado(a) con exito (id_periodo_venta'||v_id_periodo_venta||')');
            v_resp = pxp.f_agrega_clave(v_resp,'id_periodo_venta',v_id_periodo_venta::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'OBING_PERVEN_MOD'
 	#DESCRIPCION:	Modificacion de registros
 	#AUTOR:		jrivera
 	#FECHA:		08-04-2016 22:44:37
	***********************************/

	elsif(p_transaccion='OBING_PERVEN_MOD')then

		begin


			--Sentencia de la modificacion
			update obingresos.tperiodo_venta set
			fecha_mod = now(),
			id_usuario_mod = p_id_usuario,
			id_usuario_ai = v_parametros._id_usuario_ai,
			usuario_ai = v_parametros._nombre_usuario_ai,
            fecha_pago = v_parametros.fecha_pago
			where id_periodo_venta=v_parametros.id_periodo_venta;

			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Periodo de Venta modificado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_periodo_venta',v_parametros.id_periodo_venta::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'OBING_PERVEN_ELI'
 	#DESCRIPCION:	Eliminacion de registros
 	#AUTOR:		jrivera
 	#FECHA:		08-04-2016 22:44:37
	***********************************/

	elsif(p_transaccion='OBING_PERVEN_ELI')then

		begin
			--Sentencia de la eliminacion
			delete from obingresos.tperiodo_venta
            where id_periodo_venta=v_parametros.id_periodo_venta;

            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Periodo de Venta eliminado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_periodo_venta',v_parametros.id_periodo_venta::varchar);

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