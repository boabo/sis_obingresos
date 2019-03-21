CREATE OR REPLACE FUNCTION obingresos.ft_detalle_boletos_web_sel (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
  /**************************************************************************
   SISTEMA:		Ingresos
   FUNCION: 		obingresos.ft_detalle_boletos_web_sel
   DESCRIPCION:
   AUTOR: 		 (admin)
   FECHA:	        18-11-2016
   COMENTARIOS:
  ***************************************************************************
   HISTORIAL DE MODIFICACIONES:

   DESCRIPCION:
   AUTOR:
   FECHA:
  ***************************************************************************/

  DECLARE

    v_consulta    		varchar;
    v_parametros  		record;
    v_nombre_funcion   	text;
    v_resp				varchar;
    v_banco_aux			varchar;
    v_cod_moneda		varchar;
    v_fecha_ini			date;
    v_fecha_fin			date;
    v_registros			record;
    v_id_moneda			integer;
    v_id_agencia		integer;
    v_id_tipo_periodo	integer;
    v_tabla				varchar;
    v_pnr_no_boleto		text;

    v_id_alarma			integer;
    v_monto_str			varchar;
    v_id_moneda_base	integer;
    v_id_moneda_usd		integer;
    v_filtro 			varchar;

  BEGIN

    v_nombre_funcion = 'obingresos.ft_detalle_boletos_web_sel';
    v_parametros = pxp.f_get_record(p_tabla);
    /*********************************
 	#TRANSACCION:  'OBING_DETBOL_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		jrivera
 	#FECHA:		28-09-2017 18:47:46
	***********************************/

	if(p_transaccion='OBING_DETBOL_SEL')then

    	begin
    		--Sentencia de la consulta
			v_consulta:='select
						detbol.id_detalle_boletos_web,
						detbol.billete,
						detbol.id_agencia,
						detbol.id_periodo_venta,
						detbol.id_moneda,
						detbol.procesado,
						detbol.estado_reg,
						detbol.void,
						detbol.importe,
						detbol.nit,
						detbol.fecha_pago,
						detbol.razon_social,
						detbol.numero_tarjeta,
						detbol.comision,
						detbol.neto,
						detbol.entidad_pago,
						detbol.fecha,
						detbol.medio_pago,
						detbol.moneda,
						detbol.razon_ingresos,
						detbol.origen,
						detbol.nit_ingresos,
						detbol.endoso,
						detbol.conjuncion,
						detbol.numero_autorizacion,
						detbol.id_usuario_reg,
						detbol.fecha_reg,
						detbol.usuario_ai,
						detbol.id_usuario_ai,
						detbol.id_usuario_mod,
						detbol.fecha_mod,
						usu1.cuenta as usr_reg,
						usu2.cuenta as usr_mod,
                        me.pnr
						from obingresos.tdetalle_boletos_web detbol
                        left join obingresos.tmovimiento_entidad me on detbol.numero_autorizacion = me.autorizacion__nro_deposito
						inner join segu.tusuario usu1 on usu1.id_usuario = detbol.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = detbol.id_usuario_mod
				        where  ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;
			raise notice 'consulta -> %',v_consulta;
			--Devuelve la respuesta
			return v_consulta;

		end;

	/*********************************
 	#TRANSACCION:  'OBING_DETBOL_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		jrivera
 	#FECHA:		28-09-2017 18:47:46
	***********************************/

	elsif(p_transaccion='OBING_DETBOL_CONT')then

		begin
			--Sentencia de la consulta de conteo de registros
			v_consulta:='select count(id_detalle_boletos_web),
            			sum(detbol.importe) as importe,
                        sum(detbol.neto) as neto,
                        sum(detbol.comision) as comision
					    from obingresos.tdetalle_boletos_web detbol
					    inner join segu.tusuario usu1 on usu1.id_usuario = detbol.id_usuario_reg
						left join obingresos.tmovimiento_entidad me on detbol.numero_autorizacion = me.autorizacion__nro_deposito
                        left join segu.tusuario usu2 on usu2.id_usuario = detbol.id_usuario_mod
					    where ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;

			--Devuelve la respuesta
			return v_consulta;

		end;

    /*********************************
     #TRANSACCION:  'OBING_DETBOWEB_SEL'
     #DESCRIPCION:	Reporte nit razon
     #AUTOR:		MAM
     #FECHA:		18-11-2016
    ***********************************/

    elsif(p_transaccion='OBING_DETBOWEB_SEL')then

      begin
        --Sentencia de la consulta
        v_consulta:='SELECT
                                b.fecha_emision,
                                d.billete,
                                d.entidad_pago,
                                d.nit,
                                d.razon_social,
                                d.importe,
                                d.nit_ingresos,
                                d.razon_ingresos
                                FROM obingresos.tboleto b
                                INNER JOIN  obingresos.tdetalle_boletos_web d on d.billete = b.nro_boleto
                                where b.fecha_emision >= '''||v_parametros.fecha_ini||'''and b.fecha_emision <= '''||v_parametros.fecha_fin||''' ';

        --Devuelve la respuesta
        return v_consulta;

      end;
    /*********************************
     #TRANSACCION:  'OBING_OBSERVA_SEL'
     #DESCRIPCION:	Insercion de detalle boletos del portal y generacion de observaciones
     #AUTOR:		JRR
     #FECHA:		09-08-2017
    ***********************************/
    elsif(p_transaccion='OBING_OBSERVA_SEL')then

      begin


        --generar observaciones

        --reportar los estan en movimiento y no fueron reportados por el portal o hay diferencia en monto
       for v_registros in (
           with aux as (
            select dbw.numero_autorizacion, sum(dbw.importe - dbw.comision)as total
            from obingresos.tdetalle_boletos_web dbw
            where dbw.estado_reg = 'activo' and
                dbw.origen = 'portal' and dbw.medio_pago = 'CUENTA-CORRI' and
                dbw.fecha =  v_parametros.fecha_emision
            group by dbw.numero_autorizacion)
            select me.pnr,me.autorizacion__nro_deposito,me.monto,a.total
            from obingresos.tmovimiento_entidad me
            left join aux a on a.numero_autorizacion = me.autorizacion__nro_deposito
            where me.tipo = 'debito' and me.pnr is not null and me.estado_reg = 'activo' and
            me.fecha = v_parametros.fecha_emision and
            (a.numero_autorizacion is null or a.total != me.monto))loop

            v_pnr_no_boleto = v_pnr_no_boleto ||
            					(case when v_registros.total is null then
            						'El pnr '|| v_registros.autorizacion__nro_deposito ||'ha sido autorizado pero no emitido. <BR>'
            					else
                                	'Los montos no igualan para el pnr '|| v_registros.autorizacion__nro_deposito || ' ,monto boletos : ' || v_registros.total || ' , monto reserva : '||v_registros.monto || '<BR>'
                                END);
       end loop;

       v_id_alarma = (select param.f_inserta_alarma_dblink (1,'PORTAL - Diferencias entre reservas autorizadas y boletos emitidos',v_pnr_no_boleto,'miguel.mamani@boa.bo,earrazola@boa.bo,fvargas@boa.bo'));



        --los q no fueron reportados por el portal y si por la ret
        insert into obingresos.tobservaciones_portal
        	(id_usuario_reg, billete, pnr,total,moneda,tipo_observacion,observacion,fecha_emision)
        with temp_bol as (
            select dbw.numero_autorizacion,dbw.fecha,dbw.moneda,sum(dbw.importe)
            from obingresos.tdetalle_boletos_web dbw
            where dbw.estado_reg = 'activo' and dbw.origen = 'portal'
            group by dbw.numero_autorizacion,dbw.fecha,dbw.moneda
        )
		select p_id_usuario as id_usuario_reg,br.nro_boleto as billete,
        		NULL::varchar,br.total as monto,mon.codigo_internacional as moneda,'ret_no_portal'::varchar as tipo_observacion,
                'El pnr no ha sido reportado por el portal y llego en el archivo de la ret'::text as observacion,
                br.fecha_emision
        from obingresos.tboleto_retweb br
        inner join param.tmoneda mon on mon.id_moneda = me.id_moneda
        left join obingresos.tdetalle_boletos_web  dbw on dbw.numero_autorizacion = me.autorizacion__nro_deposito and
        dbw.estado_reg = 'activo' and dbw.origen = 'portal'

        where me.estado_reg = 'activo' and me.fecha = '03/08/2017'
        	and me.tipo = 'debito' and me.autorizacion__nro_deposito is not null and
            me.ajuste = 'no' and me.pnr is not null and dbw.numero_autorizacion is null;


        --los q no fueron reportados por la ret y si por el portal
        insert into obingresos.tobservaciones_portal
        	(id_usuario_reg, billete, pnr,total,moneda,tipo_observacion,observacion,fecha_emision)
        select p_id_usuario as id_usuario_reg,dbw.billete,
        		br.pnr,dbw.importe as monto,dbw.moneda,'portal_no_ret'::varchar as tipo_observacion,
                'El boleto no ha sido reportado en el archivo RET pero si en el portal corporativo'::text as observacion,
                dbw.fecha

        from obingresos.tdetalle_boletos_web dbw
        left join obingresos.tboleto_retweb br on dbw.billete = br.nro_boleto and br.estado = '1'
        where dbw.fecha = v_parametros.fecha_emision and br.id_boleto_retweb is null;


        --Diferencias en monto
        insert into obingresos.tobservaciones_portal
        	(id_usuario_reg, billete, pnr,total,moneda,tipo_observacion,observacion,fecha_emision)
        select p_id_usuario as id_usuario_reg,dbw.billete,
        		br.pnr,br.total as monto,dbw.moneda,'diferencia_monto'::varchar as tipo_observacion,
                ('El monto en la RET es '|| br.total ||' y en el portal '||dbw.importe)::text as observacion,
                dbw.fecha

        from obingresos.tdetalle_boletos_web dbw
        inner join obingresos.tboleto_retweb br on dbw.billete = br.nro_boleto
        where br.estado = '1' and dbw.fecha = v_parametros.fecha_emision and
        br.total != dbw.importe;



        --generar periodo venta cc si correpsonde
        select id_tipo_periodo into v_id_tipo_periodo
        from obingresos.ttipo_periodo tp
        where tp.estado_reg = 'activo' and tp.medio_pago = 'cuenta_corriente' and tipo = 'portal';

        v_tabla = pxp.f_crear_parametro(ARRAY[	'_nombre_usuario_ai',
                                '_id_usuario_ai',
                                'id_tipo_periodo',
                                'fecha'],
            				ARRAY[	coalesce(v_parametros._nombre_usuario_ai,''),
                                coalesce(v_parametros._id_usuario_ai::varchar,''),
                                v_id_tipo_periodo,
                                v_parametros.fecha_emision
                                ],
                            ARRAY['varchar',
                                'integer',
                            	'integer',
                                'date']
                            );
        v_resp = obingresos.ft_periodo_venta_ime(p_administrador,p_id_usuario,v_tabla,'OBING_PERVEN_INS');

        --generar periodo banca_internet si corresponde
        select id_tipo_periodo into v_id_tipo_periodo
        from obingresos.ttipo_periodo tp
        where tp.estado_reg = 'activo' and tp.medio_pago = 'banca_internet' and tipo = 'portal' and
        tp.fecha_ini_primer_periodo <= v_parametros.fecha_emision;

        v_tabla = pxp.f_crear_parametro(ARRAY[	'_nombre_usuario_ai',
                                '_id_usuario_ai',
                                'id_tipo_periodo',
                                'fecha'],
            				ARRAY[	coalesce(v_parametros._nombre_usuario_ai,''),
                                coalesce(v_parametros._id_usuario_ai::varchar,''),
                                v_id_tipo_periodo,
                                v_parametros.fecha_emision
                                ],
                            ARRAY['varchar',
                                'integer',
                            	'integer',
                                'date']
                            );
        --solo se genera el periodo para ventas de banca si corresponde
        if (v_id_tipo_periodo is not null) then
        	v_resp = obingresos.ft_periodo_venta_ime(p_administrador,p_id_usuario,v_tabla,'OBING_PERVEN_INS');
        end if;
        --generar totales facturas si corresponde

        v_resp = obingresos.f_generar_totales_facturas_portal(p_id_usuario,v_parametros.fecha_emision);

        --Sentencia de la consulta d eobservaciones
        v_consulta:='SELECT
                                billete,
                                pnr,
                                total,
                                moneda,
                                tipo_observacion,
                                observacion
                                FROM obingresos.tobservaciones_portal
                                where fecha_emision = '''||v_parametros.fecha_emision||'''';

        --Devuelve la respuesta
        return v_consulta;

      end;
    /*********************************
     #TRANSACCION:  'OBING_CONBINTOT_SEL'
     #DESCRIPCION:	Totales por fecha en reporte de conciliacion banca por internet
     #AUTOR:		JRR
     #FECHA:		18-11-2016
    ***********************************/

    elsif(p_transaccion='OBING_CONBINTOT_SEL')then

      begin
      	select m.codigo_internacional into v_cod_moneda
        from param.tmoneda m
        where id_moneda = v_parametros.id_moneda;

      	if (v_cod_moneda = 'USD') then
        	v_banco_aux = v_parametros.banco || 'U';
        else
        	v_banco_aux = v_parametros.banco;
        end if;
        --Sentencia de la consulta
        v_consulta:='(select to_char(b.fecha_emision,''DD/MM/YYYY'')::varchar,sum(b.total),''ingresos''::varchar as tipo
                      from obingresos.tboleto  b
                      inner join obingresos.tboleto_forma_pago bfp on b.id_boleto =bfp.id_boleto
                      inner join obingresos.tforma_pago fp on fp.id_forma_pago = bfp.id_forma_pago
                      where b.fecha_emision BETWEEN ''' || v_parametros.fecha_ini || ''' and ''' || v_parametros.fecha_fin || ''' and
                      b.estado_reg = ''activo'' and b.voided = ''no'' and fp.codigo = ''' || v_banco_aux || '''
                      group by b.fecha_emision
                      order  by b.fecha_emision)

                      union all
                      (select to_char(ad.issue_date_time::date,''DD/MM/YYYY'')::varchar,sum(ad.total_amount),''archivos''::varchar as tipo
                      from obingresos.tskybiz_archivo a
                      inner join obingresos.tskybiz_archivo_detalle ad
                          on ad.id_skybiz_archivo = a.id_skybiz_archivo
                      where a.banco = ''' || v_parametros.banco || ''' and ad.issue_date_time::date between ''' || v_parametros.fecha_ini || ''' and ''' || v_parametros.fecha_fin || '''
                      and a.moneda = ''' || v_cod_moneda || '''
                      group by ad.issue_date_time::date
                      order by ad.issue_date_time::date)
                      union all
                      (select to_char(dep.fecha_venta,''DD/MM/YYYY'')::varchar, sum(dep.monto_deposito)::numeric, ''depositos''::varchar as tipo
					    from obingresos.tdeposito dep

					    where dep.tipo = ''banca'' and dep.estado_reg = ''activo'' and
                        dep.agt = ''' || v_parametros.banco || ''' and
                        dep.fecha_venta >= ''' || v_parametros.fecha_ini || ''' and dep.fecha_venta <= ''' || v_parametros.fecha_fin || ''' and
                        dep.id_moneda_deposito = '||v_parametros.id_moneda || '
                        group by to_char(dep.fecha_venta,''DD/MM/YYYY'')
                        order by 1)';

                      raise notice '%', v_consulta;

        --Devuelve la respuesta
        return v_consulta;

      end;
    elsif(p_transaccion='OBING_CONBINDET_SEL')then

      begin
      	select m.codigo_internacional into v_cod_moneda
        from param.tmoneda m
        where id_moneda = v_parametros.id_moneda;

      	if (v_cod_moneda = 'USD') then
        	v_banco_aux = v_parametros.banco || 'U';
        else
        	v_banco_aux = v_parametros.banco;
        end if;
        --Sentencia de la consulta
        v_consulta:='
        with
            anulados as (select max(b.id_boleto),b.localizador,b.fecha_emision,
            			(''Anulado Resiber'' || coalesce(''. Reemitido localizador : ''|| bree.localizador || ''(''||bree.fecha_emision,''''))::varchar as observaciones,vwm.tipo
            			from obingresos.tventa_web_modificaciones vwm
                        inner join obingresos.tboleto b on b.nro_boleto = vwm.nro_boleto and b.localizador is not null and trim(both '' '' from b.localizador) != ''''
                        left join obingresos.tboleto bree on bree.nro_boleto = vwm.nro_boleto_reemision and bree.estado_reg = ''activo''

                        where b.fecha_emision BETWEEN ''' || v_parametros.fecha_ini || ''' and ''' || v_parametros.fecha_fin || ''' and vwm.tipo in (''anulado'',''reemision'')
                        group by b.localizador,b.fecha_emision,bree.localizador,bree.fecha_emision,vwm.tipo),

            reemision as (select max(bree.id_boleto),bree.localizador,bree.fecha_emision,(''PNR : '' || coalesce(banu.localizador,''"no identificado"'') || coalesce(''('' || banu.fecha_emision || '')'',''()'') ||'' anulado y  reemitido con PNR ''||bree.localizador)::varchar as observaciones
            			from obingresos.tventa_web_modificaciones vwm
                        inner join obingresos.tboleto bree on bree.nro_boleto = vwm.nro_boleto_reemision and bree.estado_reg = ''activo''
                        left join obingresos.tboleto banu on banu.nro_boleto = vwm.nro_boleto and banu.localizador is not null and
                        trim(both '' '' from banu.localizador) != ''''

                        where bree.fecha_emision BETWEEN ''' || v_parametros.fecha_ini || ''' and ''' || v_parametros.fecha_fin || ''' and vwm.tipo in (''reemision'')
                        group by bree.localizador,bree.fecha_emision,banu.localizador,banu.fecha_emision),
            emision_manual as (select max(bree.id_boleto), vwm.pnr_antiguo,vwm.fecha_reserva_antigua,bree.localizador,bree.fecha_emision,(''Emision Manual del pnr :  (''||vwm.pnr_antiguo||'') con el nuevo pnr : ( ''||bree.localizador||'')'')::varchar as observaciones
            			from obingresos.tventa_web_modificaciones vwm
                        inner join obingresos.tboleto bree on bree.nro_boleto = vwm.nro_boleto_reemision and bree.estado_reg = ''activo''
                        where (bree.fecha_emision BETWEEN ''' || v_parametros.fecha_ini || ''' and ''' || v_parametros.fecha_fin || ''' or
                        vwm.fecha_reserva_antigua BETWEEN ''' || v_parametros.fecha_ini || ''' and ''' || v_parametros.fecha_fin || ''')
                        and vwm.tipo in (''emision_manual'')
                        group by vwm.pnr_antiguo,vwm.fecha_reserva_antigua,bree.localizador,bree.fecha_emision),


            ingresos as
            		(select to_char(b.fecha_emision,''DD/MM/YYYY'')::varchar as fecha_texto,b.fecha_emision as fecha,b.localizador as pnr,sum(b.total) as monto,
                    	(case when anu.localizador is not null then
                        	(''[Por anular ingresos] ''|| anu.observaciones)::varchar
                        when ree.localizador is not null then
                        	ree.observaciones
                        when man.localizador is not null then
                        	man.observaciones
                        else
                        	NULL::varchar
                        end) as observaciones
                      from obingresos.tboleto  b
                      inner join obingresos.tboleto_forma_pago bfp on b.id_boleto =bfp.id_boleto
                      inner join obingresos.tforma_pago fp on fp.id_forma_pago = bfp.id_forma_pago
                      left join anulados anu on anu.localizador = b.localizador and anu.fecha_emision = b.fecha_emision
                      left join reemision ree on ree.localizador = b.localizador and ree.fecha_emision = b.fecha_emision
                      left join emision_manual man on man.localizador = b.localizador and man.fecha_emision = b.fecha_emision
                      where b.fecha_emision BETWEEN ''' || v_parametros.fecha_ini || ''' and ''' || v_parametros.fecha_fin || ''' and
                      b.estado_reg = ''activo'' and b.voided = ''no'' and fp.codigo = ''' || v_banco_aux || '''
                      group by b.fecha_emision,b.localizador,anu.localizador,anu.observaciones,
                      	ree.localizador,man.localizador,ree.observaciones,man.observaciones
                      order  by b.fecha_emision),



        archivos as  (select to_char(to_timestamp(ad.issue_date_time,''YYYY-MM-DD HH24:MI:SS''),''DD/MM/YYYY HH24:MI:SS'')::varchar as fecha_texto,
        			to_timestamp(ad.issue_date_time,''YYYY-MM-DD HH24:MI:SS'')::timestamp as fecha,ad.pnr,ad.total_amount as monto,
        				a.fecha as fecha_archivo_pago,
        				(case when anu.localizador is not null then
                        	anu.observaciones
                        when man.pnr_antiguo is not null then
                        	man.observaciones
                        else
                        	NULL::varchar
                        end) as observaciones

                      from obingresos.tskybiz_archivo a
                      inner join obingresos.tskybiz_archivo_detalle ad
                          on ad.id_skybiz_archivo = a.id_skybiz_archivo
                      left join anulados anu on anu.localizador = ad.pnr and anu.fecha_emision = ad.issue_date_time::date
                      left join emision_manual man on man.pnr_antiguo = ad.pnr and man.fecha_emision = ad.issue_date_time::date
                      where a.banco = ''' || v_parametros.banco || ''' and ad.issue_date_time::date between ''' || v_parametros.fecha_ini || ''' and ''' || v_parametros.fecha_fin || '''
                      and a.moneda = ''' || v_cod_moneda || '''
                      order by ad.issue_date_time::date)

        SELECT (case when a.fecha_texto is null then i.fecha_texto else a.fecha_texto end)::varchar as fecha_hora,
        to_char((case when a.fecha is null then i.fecha else a.fecha end),''DD-MM-YYYY'')::varchar as fecha,
        (case when a.pnr is null then i.pnr else a.pnr end)::varchar as pnr,
        (case when i.monto is null then 0 else i.monto end)::numeric as monto_ingresos,
        (case when a.monto is null then 0 else a.monto end)::numeric as monto_archivos,
        to_char(a.fecha_archivo_pago,''DD/MM/YYYY'')::varchar as fecha_archivo_pago,
        (case when i.observaciones is not null then
        	i.observaciones
        else
        	a.observaciones
        end)

        from ingresos i
        full outer join archivos a on a.pnr = i.pnr and a.fecha::date = i.fecha
        order by 2,a.fecha,3';

                      raise notice '%', v_consulta;

        --Devuelve la respuesta
        return v_consulta;

      end;
    /*********************************
     #TRANSACCION:  'OBING_CONBINRES_SEL'
     #DESCRIPCION:	Resumen de conciliacion banca por inter
     #AUTOR:		JRR
     #FECHA:		18-11-2016
    ***********************************/

    elsif(p_transaccion='OBING_CONBINRES_SEL')then

      begin
      	SELECT p.fecha_ini,p.fecha_fin
        	into v_fecha_ini,v_fecha_fin
        from param .tperiodo p
        where p.id_periodo = v_parametros.id_periodo;
        --Sentencia de la consulta
        v_consulta:='
        	(select ''boletos''::varchar,substring(b.medio_pago from 1 for 3)::varchar, m.codigo_internacional::varchar,sum(b.total),NULL::numeric
            from obingresos.tboleto b
            inner join param.tmoneda m on m.id_moneda = b.id_moneda_boleto
            where b.fecha_emision between ''' || v_fecha_ini || ''' and ''' || v_fecha_fin || ''' and b.medio_pago not in (''OTROS'',''TMYU'') and
            b.estado_reg = ''activo'' and b.voided = ''no''
            group by b.medio_pago,m.codigo_internacional
            order by b.medio_pago,m.codigo_internacional)

            union all

            (select ''depositos''::varchar,d.agt::varchar,m.codigo_internacional::varchar,
            sum(d.monto_deposito),NULL::numeric
            from obingresos.tdeposito d
            inner join param.tmoneda m on m.id_moneda = d.id_moneda_deposito
            where d.tipo = ''banca'' and  d.estado_reg = ''activo'' and
            	d.fecha_venta between ''' || v_fecha_ini || ''' and ''' || v_fecha_fin || '''
            group by d.agt ,m.codigo_internacional
            order by d.agt,m.codigo_internacional)

            union all

            select ''ajustes''::varchar,b.medio_pago::varchar, mon.codigo_internacional::varchar,
            sum(case when a.fecha = ''' || v_fecha_ini || ''' then
							b.total
						else
                        	0
                        end) as saldo_anterior_mes,
            sum(case when a.fecha = ''' || (v_fecha_fin + interval '1 day')::date || ''' then
                b.total
            else
                0
            end) as saldo_siguiente_mes
            from obingresos.tskybiz_archivo a
            inner join obingresos.tskybiz_archivo_detalle ad on
                    ad.id_skybiz_archivo = a.id_skybiz_archivo
            inner join obingresos.tboleto b on b.localizador = ad.pnr and
                    to_timestamp(ad.issue_date_time,''YYYY-MM-DD HH24:MI:SS'')::timestamp::date = b.fecha_emision
            inner join param.tmoneda mon on mon.id_moneda = b.id_moneda_boleto
            where  ((a.fecha = ''' || v_fecha_ini || ''' and b.fecha_emision = ''' || (v_fecha_ini - interval '1 day')::date || ''') or
                    (a.fecha = ''' || (v_fecha_fin + interval '1 day')::date || ''' and b.fecha_emision = ''' || v_fecha_fin || ''')) and
            b.estado_reg = ''activo'' and b.voided = ''no'' and b.medio_pago not in (''OTROS'') and b.id_moneda_boleto = 1
            group by b.medio_pago , mon.codigo_internacional
            ';

            raise notice '%',v_consulta;



        --Devuelve la respuesta
        return v_consulta;

      end;
    /*********************************
     #TRANSACCION:  'OBING_CONBINOBS_SEL'
     #DESCRIPCION:	Listado
     #AUTOR:		JRR
     #FECHA:		18-11-2016
    ***********************************/

    elsif(p_transaccion='OBING_CONBINOBS_SEL')then

      begin
      	SELECT p.fecha_ini,p.fecha_fin
        	into v_fecha_ini,v_fecha_fin
        from param .tperiodo p
        where p.id_periodo = v_parametros.id_periodo;
        --Sentencia de la consulta
        v_consulta:='
        	select banco, to_char(fecha_observacion,''DD/MM/YYYY'')::varchar, observacion::text
            from obingresos.tobservaciones_conciliacion
            where fecha_observacion >= ''' || v_fecha_ini || ''' and fecha_observacion <= ''' || v_fecha_fin || ''' and
            tipo_observacion = ''' || v_parametros.tipo ||  '''
            ';

            raise notice '%',v_consulta;



        --Devuelve la respuesta
        return v_consulta;

      end;

    /*********************************
     #TRANSACCION:  'OBING_REPCENCOR_SEL'
     #DESCRIPCION:	Listado
     #AUTOR:		JRR
     #FECHA:		18-11-2016
    ***********************************/

    elsif(p_transaccion='OBING_REPCENCOR_SEL')then

      begin
      	v_id_moneda_base = (select param.f_get_moneda_base());
        select m.id_moneda into v_id_moneda_usd
        from param.tmoneda m
        where m.codigo_internacional = 'USD';

        v_monto_str = '(case when me.id_moneda = ' || v_id_moneda_base || ' then
                            	me.monto
                            else
                            	param.f_convertir_moneda(' || v_id_moneda_usd || ',' || v_id_moneda_base || ',me.monto,me.fecha,''O'',2)
                            end)';

      	v_consulta = 'WITH contrato as(
        				select
							max(id_contrato) as ultimo_contrato,
            				id_agencia
						from leg.tcontrato c
						where id_agencia is not null and c.estado = ''finalizado''
    					group by id_agencia )
					select a.id_agencia,a.nombre ,a.codigo_int, a.tipo_agencia,
                    		array_to_string(con.formas_pago, '','')::varchar as formas_pago,l.codigo,
                            sum(case when me.tipo = ''credito'' and me.ajuste = ''no'' then
                            		' || v_monto_str || '
                                else
                                	0
                                end)  as monto_creditos,
                            sum(case when me.tipo = ''debito'' and me.ajuste = ''no'' then
                            		' || v_monto_str || '
                                else
                                	0
                                end) as monto_debitos,
                            sum(case when me.ajuste = ''si'' and me.tipo = ''credito'' then
                            		' || v_monto_str || '
                            	when me.ajuste = ''si'' and me.tipo = ''debito'' then
                                	' || v_monto_str || ' * -1
                                else
                                	0
                                end) as monto_ajustes,
                                sum(case when me.tipo = ''credito'' and me.ajuste = ''no'' then
                            		' || v_monto_str || '
                                else
                                	0
                                end) -
                            sum(case when me.tipo = ''debito'' and me.ajuste = ''no'' then
                            		' || v_monto_str || '
                                else
                                	0
                                end) +
                            sum(case when me.ajuste = ''si'' and me.tipo = ''credito'' then
                            		' || v_monto_str || '
                            	when me.ajuste = ''si'' and me.tipo = ''debito'' then
                                	' || v_monto_str || ' * -1
                                else
                                	0
                                end) as saldo

					from obingresos.tmovimiento_entidad me
					inner join obingresos.tagencia a on a.id_agencia = me.id_agencia
					inner join contrato c on c.id_agencia = a.id_agencia
					inner join leg.tcontrato con on con.id_contrato = c.ultimo_contrato
					inner join param.tlugar l on l.id_lugar = a.id_lugar
					where me.estado_reg = ''activo'' and me.cierre_periodo = ''no''
                    and me.garantia = ''no''
                    and ' ||v_parametros.filtro  || '
                    group by a.id_agencia,a.nombre,a.codigo_int, a.tipo_agencia,con.formas_pago,l.codigo';

		v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;




        --Devuelve la respuesta
        raise notice 'consulta %',v_consulta;
        return v_consulta;

      end;
    /*********************************
     #TRANSACCION:  'OBING_REPCENCOR_CONT'
     #DESCRIPCION:	Listado
     #AUTOR:		JRR
     #FECHA:		18-11-2016
    ***********************************/

    elsif(p_transaccion='OBING_REPCENCOR_CONT')then

      begin
      	v_consulta = 'with contrato as(
        				select
							max(id_contrato) as ultimo_contrato,
            				id_agencia
						from leg.tcontrato c
						where id_agencia is not null and c.estado = ''finalizado''
    					group by id_agencia ),
					 detalle as (
                        select a.id_agencia,a.nombre ,a.codigo_int, a.tipo_agencia,
                    		array_to_string(con.formas_pago, '','')::varchar as formas_pago,l.codigo,
                            sum(case when me.tipo = ''credito'' and me.ajuste = ''no'' then
                            		me.monto
                                else
                                	0
                                end) as monto_creditos,
                            sum(case when me.tipo = ''debito'' and me.ajuste = ''no'' then
                            		me.monto
                                else
                                	0
                                end) as monto_debitos,
                            sum(case when me.ajuste = ''si'' and me.tipo = ''credito'' then
                            		me.monto
                            	when me.ajuste = ''si'' and me.tipo = ''debito'' then
                                	me.monto * -1
                                else
                                	0
                                end) as monto_ajustes,
                                sum(case when me.tipo = ''credito'' and me.ajuste = ''no'' then
                            		me.monto
                                else
                                	0
                                end) -
                            sum(case when me.tipo = ''debito'' and me.ajuste = ''no'' then
                            		me.monto
                                else
                                	0
                                end) +
                            sum(case when me.ajuste = ''si'' and me.tipo = ''credito'' then
                            		me.monto
                            	when me.ajuste = ''si'' and me.tipo = ''debito'' then
                                	me.monto * -1
                                else
                                	0
                                end) as saldo

					from obingresos.tmovimiento_entidad me
					inner join obingresos.tagencia a on a.id_agencia = me.id_agencia
					inner join contrato c on c.id_agencia = a.id_agencia
					inner join leg.tcontrato con on con.id_contrato = c.ultimo_contrato
					inner join param.tlugar l on l.id_lugar = a.id_lugar
					where me.estado_reg = ''activo'' and me.cierre_periodo = ''no'' and me.garantia = ''no'' and ' ||v_parametros.filtro  || '

                    group by a.id_agencia,a.nombre,a.codigo_int, a.tipo_agencia,con.formas_pago,l.codigo)
                    select count(id_agencia), sum(monto_creditos), sum(monto_debitos), sum(monto_ajustes),sum(saldo)
                     from detalle';




        --Devuelve la respuesta
        return v_consulta;

      end;

       /*********************************
     #TRANSACCION:  'OBING_REPDEP_SEL'
     #DESCRIPCION:  Listado
     #AUTOR:    MMV
     #FECHA:    18-11-2016
    ***********************************/
      elsif(p_transaccion='OBING_REPDEP_SEL')then

      begin

     	v_id_moneda_base = (select param.f_get_moneda_base());
        select m.id_moneda into v_id_moneda_usd
        from param.tmoneda m
        where m.codigo_internacional = 'USD';
		/*if(v_parametros.nro_deposito = '')then
        v_filtro =
        else
    	v_filtro =
        end if;*/


        v_monto_str = '(case when me.id_moneda = ' || v_id_moneda_base || ' then
                              me.monto
                            else
                              param.f_convertir_moneda(' || v_id_moneda_usd || ',' || v_id_moneda_base || ',me.monto,me.fecha,''O'',2)
                            end)';


         v_consulta = 'WITH contrato as(select 	max(id_contrato) as ultimo_contrato,
            									id_agencia
												from leg.tcontrato c
												where id_agencia is not null and c.estado = ''finalizado''
    											group by id_agencia
        ),saldos  as (select a.id_agencia,a.nombre ,a.codigo_int, a.tipo_agencia,
                    		array_to_string(con.formas_pago, '','')::varchar as formas_pago,
                            l.codigo,

        					 sum(case when me.tipo = ''credito'' and me.ajuste = ''no'' then
                            		' || v_monto_str || '
                                else
                                	0
                                end) as monto_creditos,
                            sum(case when me.tipo = ''debito'' and me.ajuste = ''no'' then
                            		' || v_monto_str || '
                                else
                                	0
                                end) as monto_debitos,
                            sum(case when me.ajuste = ''si'' and me.tipo = ''credito'' then
                            		' || v_monto_str || '
                            	when me.ajuste = ''si'' and me.tipo = ''debito'' then
                                	' || v_monto_str || ' * -1
                                else
                                	0
                                end) as monto_ajustes,
                                sum(case when me.tipo = ''credito'' and me.ajuste = ''no'' then
                            		' || v_monto_str || '
                                else
                                	0
                                end) -
                            sum(case when me.tipo = ''debito'' and me.ajuste = ''no'' then
                            		' || v_monto_str || '
                                else
                                	0
                                end) +
                            sum(case when me.ajuste = ''si'' and me.tipo = ''credito'' then
                            		' || v_monto_str || '
                            	when me.ajuste = ''si'' and me.tipo = ''debito'' then
                                	' || v_monto_str || ' * -1
                                else
                                	0
                                end) as saldo

					from obingresos.tmovimiento_entidad me
					inner join obingresos.tagencia a on a.id_agencia = me.id_agencia

					inner join contrato c on c.id_agencia = a.id_agencia
					inner join leg.tcontrato con on con.id_contrato = c.ultimo_contrato
					inner join param.tlugar l on l.id_lugar = a.id_lugar
					where me.estado_reg = ''activo'' and me.cierre_periodo = ''no'' and me.garantia = ''no'' and ' ||v_parametros.filtro || '
                    group by a.id_agencia,a.nombre,a.codigo_int, a.tipo_agencia,con.formas_pago,l.codigo)
                    select 		d.id_agencia,
                    			s.nombre,
                                s.codigo_int,
                                s.formas_pago,
                                s.codigo,
                                s.saldo,
								d.nro_deposito,
        					    d.monto_deposito,
                                to_char(d.fecha,''DD/MM/YYYY'')::varchar as fecha,
                                	obingresos.f_get_total_depositos_agencia(''agencia'','''||v_parametros.fecha_ini_de||''','''||v_parametros.fecha_fin_de||''',''validado'',d.id_agencia) as total_deposito,
                                ((-1 * s.saldo )- obingresos.f_get_total_depositos_agencia(''agencia'','''||v_parametros.fecha_ini_de||''','''||v_parametros.fecha_fin_de||''',''validado'',d.id_agencia)) as diferencia,
                                 d.observaciones::varchar
                                from obingresos.tdeposito d
                                inner join saldos s on s.id_agencia = d.id_agencia
								where d.tipo=''agencia'' and d.fecha BETWEEN '''||v_parametros.fecha_ini_de||''' and '''||v_parametros.fecha_fin_de||''' and d.estado = ''validado''

                                 order by nombre,fecha asc';

		--Devuelve la respuesta

       raise notice 'consular %',v_consulta;
        return v_consulta;
	end;




    else

      raise exception 'Transaccion inexistente';

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
