CREATE OR REPLACE FUNCTION obingresos.ft_periodo_venta_sel (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Ingresos
 FUNCION: 		obingresos.ft_periodo_venta_sel
 DESCRIPCION:   Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'obingresos.tperiodo_venta'
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

	v_consulta    		varchar;
	v_parametros  		record;
	v_nombre_funcion   	text;
	v_resp				varchar;
    v_id_periodo_venta	varchar;
    v_id_moneda_mb		integer;
    v_id_moneda_base	integer;
    v_id_moneda_usd		integer;
    v_medio_pago		varchar;
    v_group_by			varchar;
			    
BEGIN

	v_nombre_funcion = 'obingresos.ft_periodo_venta_sel';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************    
 	#TRANSACCION:  'OBING_PERVEN_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		jrivera	
 	#FECHA:		08-04-2016 22:44:37
	***********************************/

	if(p_transaccion='OBING_PERDETAG_SEL')then
     				
    	begin
        	
        	if (v_parametros.tipo = 'cuenta_corriente') then
            	if (v_parametros.id_periodo_venta is null) then
                	v_id_periodo_venta = ' is null ';
                else
                	v_id_periodo_venta = ' = ' || v_parametros.id_periodo_venta ||' and garantia = ''no''';
                end if;
                
                v_id_moneda_base = (select param.f_get_moneda_base()); 
                select m.id_moneda into v_id_moneda_usd
                from param.tmoneda m
                where m.codigo_internacional = 'USD';
            
            	--Sentencia de la consulta
				v_consulta:='select me.id_periodo_venta, me.tipo, to_char(me.fecha,''DD/MM/YYYY''),me.pnr,
                              me.apellido, mon.codigo_internacional as moneda ,me.monto_total,(me.monto_total-me.monto) as comision,
                              me.monto,me.ajuste,me.garantia,me.autorizacion__nro_deposito,
                              (case when me.id_moneda = ' || v_id_moneda_base || ' then
                            	me.monto
                            else
                            	param.f_convertir_moneda(' || v_id_moneda_usd || ',' || v_id_moneda_base || ',me.monto,me.fecha,''O'',2)
                            end)::numeric,
                            (case when me.id_moneda = ' || v_id_moneda_base || ' then
                                1
                            else
                                param.f_get_tipo_cambio(' || v_id_moneda_usd ||  ',me.fecha,''O'')
                            end)::numeric as tipo_cambio,me.cierre_periodo 
                              from obingresos.tmovimiento_entidad me
                              
                              inner join param.tmoneda mon on mon.id_moneda = me.id_moneda
                              where me.id_agencia = ' || v_parametros.id_agencia || ' and me.id_periodo_venta ' || v_id_periodo_venta || ' and 
                              me.estado_reg= ''activo'' and me.garantia = ''no''';
            else
            
            
            end if;
    		
			
			
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion;
			raise notice '%',v_consulta;
			--Devuelve la respuesta
			return v_consulta;
						
		end;
    /*********************************    
 	#TRANSACCION:  'OBING_PERAGTOT_SEL'
 	#DESCRIPCION:	Totales de Periodo de venta por agencia
 	#AUTOR:		jrivera	
 	#FECHA:		08-04-2016 22:44:37
	***********************************/
    elsif(p_transaccion='OBING_PERAGTOT_SEL')then
     				
    	begin
        		select param.f_get_moneda_base() into v_id_moneda_mb;
                
                select m.id_moneda into v_id_moneda_usd
                from param.tmoneda m
                where m.codigo_internacional = 'USD';
                
                select tp.medio_pago into v_medio_pago
                from obingresos.tperiodo_venta pv
                inner join obingresos.ttipo_periodo tp on 
                	tp.id_tipo_periodo = pv.id_tipo_periodo
                where pv.id_periodo_venta = v_parametros.id_periodo_venta;
                v_group_by = '';
        		if (v_medio_pago = 'cuenta_corriente') then
                    --Sentencia de la consulta
                    v_consulta:='select pva.id_periodo_venta_agencia,pv.codigo_periodo,pva.id_agencia,
                                    tp.medio_pago,to_char(pv.fecha_ini,''MM'')::varchar as mes,
                                    to_char(pv.fecha_ini,''YYYY'')::varchar as gestion,
                                    pv.id_periodo_venta,to_char (pv.fecha_ini,''DD/MM/YYYY'')::varchar,to_char(pv.fecha_fin,''DD/MM/YYYY'')::varchar,
                                    pva.moneda_restrictiva,age.codigo_int,age.nombre,
                                    to_char (pv.fecha_ini,''YYYY-MM-DD'')::varchar,to_char(pv.fecha_fin,''YYYY-MM-DD'')::varchar,
                                    pva.estado,
                                    pva.monto_credito_mb,
                                    pva.monto_credito_usd,
                                    pva.monto_boletos_mb,
                                    pva.monto_boletos_usd,
                                    pva.monto_comision_mb,
                                    pva.monto_comision_usd,
                                    pva.monto_debito_mb,
                                    pva.monto_debito_usd,
									pva.monto_neto_mb,
                                    pva.monto_neto_usd,
                                    pva.monto_mb,
                                    pva.monto_usd,
                                    string_agg(dbw.billete, '','')::text
                                    from obingresos.tperiodo_venta pv
                                    inner join obingresos.ttipo_periodo tp on tp.id_tipo_periodo = pv.id_tipo_periodo
                                    inner join obingresos.tperiodo_venta_agencia pva on pva.id_periodo_venta = pv.id_periodo_venta
                                    inner join obingresos.tagencia age on age.id_agencia = pva.id_agencia  
                                    left join obingresos.tmovimiento_entidad me on me.id_periodo_venta = pv.id_periodo_venta and me.id_agencia = age.id_agencia
                                    left join obingresos.tdetalle_boletos_web dbw on dbw.numero_autorizacion = me.autorizacion__nro_deposito
                                    where  pv.id_periodo_venta = ' || v_parametros.id_periodo_venta || ' and 
                                    ';
                                    
                                    v_group_by = ' group by  pva.id_periodo_venta_agencia,
                                    				pv.codigo_periodo,
                                                    pva.id_agencia,
                                                  tp.medio_pago,
                                                  pv.fecha_ini,
                                                  pv.fecha_ini,
                                                  pv.id_periodo_venta,
                                                  pv.fecha_ini,
                                                  pv.fecha_fin,
                                                  pva.moneda_restrictiva,age.codigo_int,age.nombre,
                                                  pv.fecha_ini,
                                                  pv.fecha_fin,
                                                  pva.estado,
                                                  pva.monto_credito_mb,
                                                  pva.monto_credito_usd,
                                                  pva.monto_boletos_mb,
                                                  pva.monto_boletos_usd,
                                                  pva.monto_comision_mb,
                                                  pva.monto_comision_usd,
                                                  pva.monto_debito_mb,
                                                  pva.monto_debito_usd,
                                                  pva.monto_neto_mb,
                                                  pva.monto_neto_usd';
                                    
                                    raise notice '%',v_consulta;
                ELSE
                	--Sentencia de la consulta
                    v_consulta:='select pva.id_periodo_venta_agencia,pva.id_agencia,
                                    tp.medio_pago,to_char(pv.fecha_ini,''MM'')::varchar as mes,
                                    to_char(pv.fecha_ini,''YYYY'')::varchar as gestion,
                                    pv.id_periodo_venta,to_char (pv.fecha_ini,''DD/MM/YYYY'')::varchar,to_char(pv.fecha_fin,''DD/MM/YYYY'')::varchar,
                                    0::numeric as total_credito_mb,
                                    0::numeric as total_credito_me,
                                    sum(case when me.id_moneda =' ||v_id_moneda_mb || ' then me.importe else 0 end) as total_boletos_mb,
                                    sum(case when me.id_moneda =' ||v_id_moneda_usd || ' then me.importe else 0 end) as total_boletos_usd,
                                    sum(case when me.id_moneda =' ||v_id_moneda_mb || ' then me.comision else 0 end) as total_boletos_mb,
                                    sum(case when me.id_moneda =' ||v_id_moneda_usd || ' then me.comision else 0 end) as total_boletos_usd,
                                    sum(case when me.id_moneda =' ||v_id_moneda_mb || ' then (me.importe - me.comision) else 0 end) as total_debito_mb,
                                    sum(case when me.id_moneda =' ||v_id_moneda_usd || ' then (me.importe - me.comision) else 0 end) as total_debito_usd

                                    from obingresos.tperiodo_venta pv
                                    inner join obingresos.ttipo_periodo tp on tp.id_tipo_periodo = pv.id_tipo_periodo
                                    inner join obingresos.tperiodo_venta_agencia pva on pva.id_periodo_venta = pv.id_periodo_venta 
                                    inner join obingresos.tdetalle_boletos_web me on me.id_periodo_venta = pv.id_periodo_venta

                                    where me.estado_reg = ''activo'' and me.id_periodo_venta = ' || v_parametros.id_periodo_venta || '
                                    group by  pva.id_periodo_venta_agencia,pva.id_agencia,tp.medio_pago,pv.fecha_ini,
                                    pv.fecha_fin,pv.id_periodo_venta';
                end if;
            
    		
			
			v_consulta:=v_consulta|| v_parametros.filtro;
            v_consulta:=v_consulta|| v_group_by;
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion|| ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;
			raise notice '%',v_consulta;
			--Devuelve la respuesta
			return v_consulta;
						
		end;
     /*********************************    
 	#TRANSACCION:  'OBING_RESESTCC_SEL'
 	#DESCRIPCION:	Resumen Estado Cuenta Corriente
 	#AUTOR:		jrivera	
 	#FECHA:		08-04-2016 22:44:37
	***********************************/
    elsif(p_transaccion='OBING_RESESTCC_SEL')then
     				
    	begin
        		select param.f_get_moneda_base() into v_id_moneda_mb;
                
                select m.id_moneda into v_id_moneda_usd
                from param.tmoneda m
                where m.codigo_internacional = 'USD';
                
            v_consulta = 'select me.id_agencia,''boleta_garantia''::varchar as tipo,mon.codigo_internacional, me.monto , param.f_convertir_moneda(me.id_moneda,' || v_id_moneda_mb || ',me.monto,me.fecha,''O'',2) as monto_mb
                            from obingresos.tmovimiento_entidad me
                            inner join param.tmoneda mon on mon.id_moneda = me.id_moneda
                            where me.estado_reg = ''activo'' and me.id_periodo_venta is null 
                            and me.id_agencia = ' || v_parametros.id_agencia || ' and me.tipo = ''credito'' and
                            me.garantia = ''si'' and me.id_moneda = ' || v_id_moneda_mb || '
                            union all

                            select me.id_agencia,''boleta_garantia''::varchar as tipo,mon.codigo_internacional, me.monto , param.f_convertir_moneda(me.id_moneda,' || v_id_moneda_mb || ',me.monto,me.fecha,''O'',2) as monto_mb
                            from obingresos.tmovimiento_entidad me
                            inner join param.tmoneda mon on mon.id_moneda = me.id_moneda
                            where me.estado_reg = ''activo'' and me.id_periodo_venta is null 
                            and me.id_agencia = ' || v_parametros.id_agencia || ' and me.tipo = ''credito'' and
                            me.garantia = ''si'' and me.id_moneda = ' || v_id_moneda_usd || '

                            union ALL

                            select me.id_agencia,''saldo_anterior''::varchar as tipo,mon.codigo_internacional, me.monto, param.f_convertir_moneda(me.id_moneda,' || v_id_moneda_mb || ',me.monto,me.fecha,''O'',2) as monto_mb
                            from obingresos.tmovimiento_entidad me
                            inner join param.tmoneda mon on mon.id_moneda = me.id_moneda
                            where me.estado_reg = ''activo'' and me.id_periodo_venta is null 
                            and me.id_agencia = ' || v_parametros.id_agencia || ' and me.tipo = ''credito'' and
                            me.garantia = ''no'' and me.ajuste = ''no'' and me.cierre_periodo = ''si'' and
                            me.id_moneda = ' || v_id_moneda_mb || '

                            union all 

                            select me.id_agencia,''saldo_anterior''::varchar as tipo,mon.codigo_internacional, me.monto, param.f_convertir_moneda(me.id_moneda,' || v_id_moneda_mb || ',me.monto,me.fecha,''O'',2) as monto_mb
                            from obingresos.tmovimiento_entidad me
                            inner join param.tmoneda mon on mon.id_moneda = me.id_moneda
                            where me.estado_reg = ''activo'' and me.id_periodo_venta is null 
                            and me.id_agencia = ' || v_parametros.id_agencia || ' and me.tipo = ''credito'' and
                            me.garantia = ''no'' and me.ajuste = ''no'' and me.cierre_periodo = ''si'' and
                            me.id_moneda = ' || v_id_moneda_usd || '

                            union all 

                            select me.id_agencia,''deposito''::varchar as tipo,mon.codigo_internacional, sum(me.monto), sum(param.f_convertir_moneda(me.id_moneda,' || v_id_moneda_mb || ',me.monto,me.fecha,''O'',2)) as monto_mb
                            from obingresos.tmovimiento_entidad me
                            inner join param.tmoneda mon on mon.id_moneda = me.id_moneda
                            where me.estado_reg = ''activo'' and me.id_periodo_venta is null 
                            and me.id_agencia = ' || v_parametros.id_agencia || ' and me.tipo = ''credito'' and
                            me.garantia = ''no'' and me.ajuste = ''no'' and me.cierre_periodo = ''no'' and
                            me.id_moneda = ' || v_id_moneda_mb || '
                            group by me.id_agencia,mon.codigo_internacional

                            union all 

                            select me.id_agencia,''deposito''::varchar as tipo,mon.codigo_internacional, sum(me.monto), sum(param.f_convertir_moneda(me.id_moneda,' || v_id_moneda_mb || ',me.monto,me.fecha,''O'',2)) as monto_mb
                            from obingresos.tmovimiento_entidad me
                            inner join param.tmoneda mon on mon.id_moneda = me.id_moneda
                            where me.estado_reg = ''activo'' and me.id_periodo_venta is null 
                            and me.id_agencia = ' || v_parametros.id_agencia || ' and me.tipo = ''credito'' and
                            me.garantia = ''no'' and me.ajuste = ''no'' and me.cierre_periodo = ''no'' and
                            me.id_moneda = ' || v_id_moneda_usd || '
                            group by me.id_agencia,mon.codigo_internacional

                            union all 

                            select me.id_agencia,''comision''::varchar as tipo,mon.codigo_internacional, sum(me.monto_total-me.monto), sum(param.f_convertir_moneda(me.id_moneda,' || v_id_moneda_mb || ',me.monto_total-me.monto,me.fecha,''O'',2)) as monto_mb
                            from obingresos.tmovimiento_entidad me
                            inner join param.tmoneda mon on mon.id_moneda = me.id_moneda
                            where me.estado_reg = ''activo'' and me.id_periodo_venta is null 
                            and me.id_agencia = ' || v_parametros.id_agencia || ' and me.tipo = ''debito'' and
                            me.ajuste = ''no'' and me.pnr is not null and
                            me.id_moneda = ' || v_id_moneda_mb || '
                            group by me.id_agencia,mon.codigo_internacional

                            union all 

                            select me.id_agencia,''comision''::varchar as tipo,mon.codigo_internacional, sum(me.monto_total-me.monto), sum(param.f_convertir_moneda(me.id_moneda,' || v_id_moneda_mb || ',me.monto_total-me.monto,me.fecha,''O'',2)) as monto_mb
                            from obingresos.tmovimiento_entidad me
                            inner join param.tmoneda mon on mon.id_moneda = me.id_moneda
                            where me.estado_reg = ''activo'' and me.id_periodo_venta is null 
                            and me.id_agencia = ' || v_parametros.id_agencia || ' and me.tipo = ''debito'' and
                            me.ajuste = ''no'' and me.pnr is not null and
                            me.id_moneda = ' || v_id_moneda_usd || '
                            group by me.id_agencia,mon.codigo_internacional

                            union all

                            select me.id_agencia,''otro_credito''::varchar as tipo,mon.codigo_internacional, sum(me.monto), sum(param.f_convertir_moneda(me.id_moneda,' || v_id_moneda_mb || ',me.monto,me.fecha,''O'',2)) as monto_mb
                            from obingresos.tmovimiento_entidad me
                            inner join param.tmoneda mon on mon.id_moneda = me.id_moneda
                            where me.estado_reg = ''activo'' and me.id_periodo_venta is null 
                            and me.id_agencia = ' || v_parametros.id_agencia || ' and me.tipo = ''credito'' and
                            me.garantia = ''no'' and me.ajuste = ''si'' and me.cierre_periodo = ''no'' and
                            me.id_moneda = ' || v_id_moneda_mb || '
                            group by me.id_agencia,mon.codigo_internacional

                            union all

                            select me.id_agencia,''otro_credito''::varchar as tipo,mon.codigo_internacional, sum(me.monto), sum(param.f_convertir_moneda(me.id_moneda,' || v_id_moneda_mb || ',me.monto,me.fecha,''O'',2)) as monto_mb
                            from obingresos.tmovimiento_entidad me
                            inner join param.tmoneda mon on mon.id_moneda = me.id_moneda
                            where me.estado_reg = ''activo'' and me.id_periodo_venta is null 
                            and me.id_agencia = ' || v_parametros.id_agencia || ' and me.tipo = ''credito'' and
                            me.garantia = ''no'' and me.ajuste = ''si'' and me.cierre_periodo = ''no'' and
                            me.id_moneda = ' || v_id_moneda_usd || '
                            group by me.id_agencia,mon.codigo_internacional

                            union all

                            select me.id_agencia,''boleto''::varchar as tipo,mon.codigo_internacional, sum(me.monto_total), sum(param.f_convertir_moneda(me.id_moneda,' || v_id_moneda_mb || ',me.monto_total,me.fecha,''O'',2)) as monto_mb
                            from obingresos.tmovimiento_entidad me
                            inner join param.tmoneda mon on mon.id_moneda = me.id_moneda
                            where me.estado_reg = ''activo'' and me.id_periodo_venta is null 
                            and me.id_agencia = ' || v_parametros.id_agencia || ' and me.tipo = ''debito'' and
                            me.ajuste = ''no'' and me.pnr is not null and
                            me.id_moneda = ' || v_id_moneda_mb || '
                            group by me.id_agencia,mon.codigo_internacional

                            union all 

                            select me.id_agencia,''boleto''::varchar as tipo,mon.codigo_internacional, sum(me.monto_total) as monto, sum(param.f_convertir_moneda(me.id_moneda,' || v_id_moneda_mb || ',me.monto_total,me.fecha,''O'',2)) as monto_mb
                            from obingresos.tmovimiento_entidad me
                            inner join param.tmoneda mon on mon.id_moneda = me.id_moneda
                            where me.estado_reg = ''activo'' and me.id_periodo_venta is null 
                            and me.id_agencia = ' || v_parametros.id_agencia || ' and me.tipo = ''debito'' and
                            me.ajuste = ''no'' and me.pnr is not null and
                            me.id_moneda = ' || v_id_moneda_usd || '
                            group by me.id_agencia,mon.codigo_internacional

                            union ALL
                            select pva.id_agencia,''periodo_adeudado''::varchar as tipo,mon.codigo_internacional,sum(pva.monto_mb * -1) as monto,sum(pva.monto_mb * -1) as monto_mb
                            from obingresos.tperiodo_venta_agencia pva 
                            inner join param.tmoneda mon on mon.id_moneda = ' || v_id_moneda_mb || '
                            where pva.estado_reg = ''activo'' and pva.monto_mb < 0
                            and pva.id_agencia = ' || v_parametros.id_agencia || ' and pva.estado = ''abierto''
                            group by pva.id_agencia,mon.codigo_internacional

                            union ALL

                            select pva.id_agencia,''periodo_adeudado''::varchar as tipo,mon.codigo_internacional,sum(pva.monto_usd * -1) as monto,sum(param.f_convertir_moneda( ' || v_id_moneda_usd || ', ' || v_id_moneda_mb || ',pva.monto_usd * -1,now()::date,''O'',2)) as monto_mb
                            from obingresos.tperiodo_venta_agencia pva 
                            inner join param.tmoneda mon on mon.id_moneda = ' || v_id_moneda_usd || '
                            where pva.estado_reg = ''activo'' and pva.monto_usd < 0
                            and pva.id_agencia = ' || v_parametros.id_agencia || ' and pva.estado = ''abierto''
                            group by pva.id_agencia,mon.codigo_internacional


                            union all

                            select me.id_agencia,''otro_debito''::varchar as tipo,mon.codigo_internacional, sum(me.monto), sum(param.f_convertir_moneda(me.id_moneda, ' || v_id_moneda_mb || ',me.monto,me.fecha,''O'',2)) as monto_mb
                            from obingresos.tmovimiento_entidad me
                            inner join param.tmoneda mon on mon.id_moneda = me.id_moneda
                            where me.estado_reg = ''activo'' and me.id_periodo_venta is null 
                            and me.id_agencia = ' || v_parametros.id_agencia || ' and me.tipo = ''debito'' and
                            me.garantia = ''no'' and me.ajuste = ''si'' and me.cierre_periodo = ''no'' and
                            me.id_moneda =  ' || v_id_moneda_mb || '
                            group by me.id_agencia,mon.codigo_internacional

                            union all

                            select me.id_agencia,''otro_debito''::varchar as tipo,mon.codigo_internacional, sum(me.monto), sum(param.f_convertir_moneda(me.id_moneda, ' || v_id_moneda_mb || ',me.monto,me.fecha,''O'',2)) as monto_mb
                            from obingresos.tmovimiento_entidad me
                            inner join param.tmoneda mon on mon.id_moneda = me.id_moneda
                            where me.estado_reg = ''activo'' and me.id_periodo_venta is null 
                            and me.id_agencia = ' || v_parametros.id_agencia || ' and me.tipo = ''debito'' and
                            me.garantia = ''no'' and me.ajuste = ''si'' and me.cierre_periodo = ''no'' and
                            me.id_moneda =  ' || v_id_moneda_usd || '
                            group by me.id_agencia,mon.codigo_internacional

                            ';     
			
			raise notice '%',v_consulta;
			--Devuelve la respuesta
			return v_consulta;
						
		end;
    elsif(p_transaccion='OBING_PERAGTOT_CONT')then
     				
    	begin
        		                
                select tp.medio_pago into v_medio_pago
                from obingresos.tperiodo_venta pv
                inner join obingresos.ttipo_periodo tp on 
                	tp.id_tipo_periodo = pv.id_tipo_periodo
                where pv.id_periodo_venta = v_parametros.id_periodo_venta;
        		if (v_medio_pago = 'cuenta_corriente') then
                    --Sentencia de la consulta
                    v_consulta:='select count(pva.id_periodo_venta_agencia),
                    				sum(pva.monto_credito_mb),
                                    sum(pva.monto_credito_usd),
                                    sum(pva.monto_boletos_mb),
                                    sum(pva.monto_boletos_usd),
                                    sum(pva.monto_comision_mb),
                                    sum(pva.monto_comision_usd),
                                    sum(pva.monto_debito_mb),
                                    sum(pva.monto_debito_usd),
									sum(pva.monto_neto_mb),
                                    sum(pva.monto_neto_usd)
                                    from obingresos.tperiodo_venta pv                                    
                                    inner join obingresos.tperiodo_venta_agencia pva on pva.id_periodo_venta = pv.id_periodo_venta
                                    inner join obingresos.tagencia age on age.id_agencia = pva.id_agencia  
                                    where pva.id_periodo_venta = ' || v_parametros.id_periodo_venta || ' and ';
                                    
                ELSE
                	--Sentencia de la consulta
                    v_consulta:='select pva.id_periodo_venta_agencia,pva.id_agencia,
                                    tp.medio_pago,to_char(pv.fecha_ini,''MM'')::varchar as mes,
                                    to_char(pv.fecha_ini,''YYYY'')::varchar as gestion,
                                    pv.id_periodo_venta,to_char (pv.fecha_ini,''DD/MM/YYYY'')::varchar,to_char(pv.fecha_fin,''DD/MM/YYYY'')::varchar,
                                    0::numeric as total_credito_mb,
                                    0::numeric as total_credito_me,
                                    sum(case when me.id_moneda =' ||v_id_moneda_mb || ' then me.importe else 0 end) as total_boletos_mb,
                                    sum(case when me.id_moneda =' ||v_id_moneda_usd || ' then me.importe else 0 end) as total_boletos_usd,
                                    sum(case when me.id_moneda =' ||v_id_moneda_mb || ' then me.comision else 0 end) as total_boletos_mb,
                                    sum(case when me.id_moneda =' ||v_id_moneda_usd || ' then me.comision else 0 end) as total_boletos_usd,
                                    sum(case when me.id_moneda =' ||v_id_moneda_mb || ' then (me.importe - me.comision) else 0 end) as total_debito_mb,
                                    sum(case when me.id_moneda =' ||v_id_moneda_usd || ' then (me.importe - me.comision) else 0 end) as total_debito_usd

                                    from obingresos.tperiodo_venta pv
                                    inner join obingresos.ttipo_periodo tp on tp.id_tipo_periodo = pv.id_tipo_periodo
                                    inner join obingresos.tperiodo_venta_agencia pva on pva.id_periodo_venta = pv.id_periodo_venta 
                                    inner join obingresos.tdetalle_boletos_web me on me.id_periodo_venta = pv.id_periodo_venta

                                    where me.estado_reg = ''activo'' and me.id_periodo_venta = ' || v_parametros.id_periodo_venta || '
                                    group by  pva.id_periodo_venta_agencia,pva.id_agencia,tp.medio_pago,pv.fecha_ini,
                                    pv.fecha_fin,pv.id_periodo_venta';
                end if;
            
    		v_consulta:=v_consulta||v_parametros.filtro;
			--Devuelve la respuesta
			return v_consulta;
						
		end;
    elsif(p_transaccion='OBING_PERVEN_SEL')then
     				
    	begin
    		--Sentencia de la consulta
			v_consulta:='select
						perven.id_periodo_venta,						
						perven.id_gestion,
						perven.mes,
						perven.estado,
						perven.nro_periodo_mes,
						perven.fecha_fin,
						perven.fecha_ini,
						tp.tipo,
                        tp.medio_pago,
                        tp.tipo_cc,
						perven.estado_reg,
						perven.id_usuario_ai,
						perven.id_usuario_reg,
						perven.usuario_ai,
						perven.fecha_reg,
						perven.fecha_mod,
						perven.id_usuario_mod,
						usu1.cuenta as usr_reg,
						usu2.cuenta as usr_mod,
                        (to_char(perven.fecha_ini,''DD/MM/YYYY'') || ''-'' ||to_char(perven.fecha_fin,''DD/MM/YYYY'') || '' '' ||
                        tp.tipo_cc)::text as desc_periodo,
                        perven.fecha_pago	
						from obingresos.tperiodo_venta perven
						inner join segu.tusuario usu1 on usu1.id_usuario = perven.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = perven.id_usuario_mod
				        inner join obingresos.ttipo_periodo tp on tp.id_tipo_periodo = perven.id_tipo_periodo
                        where  ';
			
			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

			--Devuelve la respuesta
			return v_consulta;
						
		end;

	/*********************************    
 	#TRANSACCION:  'OBING_PERVEN_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		jrivera	
 	#FECHA:		08-04-2016 22:44:37
	***********************************/

	elsif(p_transaccion='OBING_PERVEN_CONT')then

		begin
			--Sentencia de la consulta de conteo de registros
			v_consulta:='select count(id_periodo_venta)
					    from obingresos.tperiodo_venta perven
					    inner join segu.tusuario usu1 on usu1.id_usuario = perven.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = perven.id_usuario_mod
					    inner join obingresos.ttipo_periodo tp on tp.id_tipo_periodo = perven.id_tipo_periodo
                        where ';
			
			--Definicion de la respuesta		    
			v_consulta:=v_consulta||v_parametros.filtro;

			--Devuelve la respuesta
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