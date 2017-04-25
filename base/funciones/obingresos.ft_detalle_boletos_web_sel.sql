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

  BEGIN

    v_nombre_funcion = 'obingresos.ft_detalle_boletos_web_sel';
    v_parametros = pxp.f_get_record(p_tabla);

    /*********************************
     #TRANSACCION:  'OBING_DETBOWEB_SEL'
     #DESCRIPCION:	Reporte nit razon
     #AUTOR:		MAM
     #FECHA:		18-11-2016
    ***********************************/

    if(p_transaccion='OBING_DETBOWEB_SEL')then

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