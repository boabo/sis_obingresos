CREATE OR REPLACE FUNCTION obingresos.ft_control_agencia_sel (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS'
/**************************************************************************
 SISTEMA:		Ingresos
 FUNCION: 		obingresos.ft_control_agencia_sel
 DESCRIPCION:   Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla ''obingresos.treporte_cuenta''
 AUTOR: 		 (Ismael.Valdivia)
 FECHA:	        11-06-2018 15:14:58
 COMENTARIOS:
***************************************************************************
 HISTORIAL DE MODIFICACIONES:
#ISSUE				FECHA				AUTOR				DESCRIPCION
 #0				11-06-2018 15:14:58								Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla ''obingresos.treporte_cuenta''
 #
 ***************************************************************************/

DECLARE

	v_consulta    		varchar;
	v_parametros  		record;
	v_nombre_funcion   	text;
	v_resp				varchar;

    v_record			record;
    v_depositos			record;
    v_debitos			record;
    v_arrastre			record;
    v_saldo_vigente		record;
    v_depositos_vigente	record;
    v_debitos_vigente	record;
    v_arrastre_vigente	record;
    v_arrastre_vigente_maximo	record;
    v_saldo_vigente_maximo		record;
    v_depositos_vigente_maximo	record;
    v_debitos_vigente_maximo	record;
    v_maximo_credito	integer;
    v_maximo_debito		integer;
    v_valor_maximo		integer;

    v_saldo_calculado_arre 			numeric[];
    v_saldo_arrastrado_arre			numeric[];
    v_id_periodo_venta_arrastre		integer[];
    v_id_periodo_venta_calculado	integer[];
    v_length						integer;

    v_saldo_calculado				record;
    v_datos_saldo_calculado			record;
    v_datos_saldo_arrastre			record;
    v_length_arrastre			integer;
    v_diferencia				numeric;
    v_saldo_cal					numeric;
    v_saldo_arrastre			numeric;
    v_saldo						numeric;

    v_datos_saldo_calculado_vigente record;
    v_id_periodo_venta			integer;
    v_datos_saldo_arrastre_vigente numeric;


BEGIN

	v_nombre_funcion = ''obingresos.ft_control_agencia_sel'';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  ''OBING_REPORIS_SEL''
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		IVALDIVIA
 	#FECHA:		02-01-2018 20:57:30
	***********************************/

	if(p_transaccion=''OBING_REPORIS_SEL'')then


    	begin

        CREATE TEMPORARY TABLE temp ( id_agencia  int4,
                                      id_periodo_venta int4,
                                      tipo varchar,
                                      depositos_con_saldos NUMERIC,
                                      depositos NUMERIC,
                                      debitos NUMERIC,
                                      saldo_arrastrado NUMERIC,
                                      periodo varchar
                                       )ON COMMIT DROP;

        select  max(mo.id_periodo_venta)
        into v_maximo_credito
        from obingresos.tmovimiento_entidad mo
        where mo.tipo = ''credito'' and
          mo.id_agencia = v_parametros.id_agencia AND
          mo.estado_reg = ''activo'' and
          mo.garantia = ''no'' and
          mo.id_periodo_venta is not null;

    	Select
        max(mo.id_periodo_venta)
        into v_maximo_debito
        from obingresos.tmovimiento_entidad mo
        where mo.tipo = ''debito'' and
          mo.id_agencia = v_parametros.id_agencia AND
          mo.estado_reg = ''activo'' and
          mo.garantia = ''no'' and
          mo.cierre_periodo = ''no'' and
          mo.id_periodo_venta is not null;

        if (v_maximo_credito > v_maximo_debito) then
        	v_valor_maximo = v_maximo_credito;
        else
        	v_valor_maximo = v_maximo_debito ;
        end if;

		--raise exception ''LLEGA AQUI %'',v_valor_maximo;
        FOR v_record in ( select  mo.id_periodo_venta,
                                  Sum(mo.monto_total) as saldos,
                                  mo.tipo,
                                  COALESCE(TO_CHAR(pe.fecha_ini,''DD'')||'' al ''|| TO_CHAR(pe.fecha_fin,''DD'')||'' ''||pe.mes||'' ''||EXTRACT(YEAR FROM pe.fecha_ini),''Periodo Vigente'')::text as periodo
                                  from obingresos.tmovimiento_entidad mo
                                  LEFT JOIN obingresos.tperiodo_venta pe ON pe.id_periodo_venta = mo.id_periodo_venta
                                  where mo.tipo = ''credito'' and
                                    mo.id_agencia = v_parametros.id_agencia AND
                                    mo.estado_reg = ''activo'' and
                                    mo.garantia = ''no'' and
                                    mo.id_periodo_venta is not null
                                    and mo.id_periodo_venta < v_valor_maximo
                                  group by mo.id_periodo_venta,mo.tipo, pe.fecha_ini, pe.fecha_fin, pe.mes
                                  order by mo.id_periodo_venta asc
                        )

                      LOOP
        					insert into	temp ( id_agencia  ,
                                      id_periodo_venta ,
                                      tipo,
                                      depositos_con_saldos,
                                      periodo


                                        )
                                        values(
                                        v_parametros.id_agencia,
                                        v_record.id_periodo_venta,
                                        v_record.tipo,
                                        v_record.saldos,
                                        v_record.periodo

                                      );

    	END LOOP;

        FOR v_depositos in (
        					Select
                            mo.id_periodo_venta,
                            sum(mo.monto_total) as depositos,
                            mo.tipo

                            from obingresos.tmovimiento_entidad mo
                            where mo.tipo = ''credito'' and
                                                mo.id_agencia = v_parametros.id_agencia AND
                                                    mo.estado_reg = ''activo'' and
                                                    mo.garantia = ''no'' and
                                                    mo.cierre_periodo = ''no'' and
                                                    mo.id_periodo_venta is not null
                                                    and mo.id_periodo_venta < v_valor_maximo
                            group by mo.id_periodo_venta,mo.tipo
                            order by mo.id_periodo_venta asc

                            )

                            LOOP



                    insert into	temp ( id_agencia  ,
                                      id_periodo_venta ,
                                      tipo,
                                      depositos


                                        )
                                        values(
                                        v_parametros.id_agencia,
                                        v_depositos.id_periodo_venta,
                                        ''depositos'',
                                        v_depositos.depositos

                                      );
                    end loop;


    FOR v_debitos in (
        					Select
                            mo.id_periodo_venta,
                            pv.fecha_ini,
                            pv.fecha_fin,
                            sum(mo.monto) as debitos,
                            mo.tipo
                            from obingresos.tmovimiento_entidad mo
                            left join obingresos.tperiodo_venta pv on pv.id_periodo_venta=mo.id_periodo_venta
                            where mo.tipo = ''debito'' and
                                                mo.id_agencia = v_parametros.id_agencia AND
                                                    mo.estado_reg = ''activo'' and
                                                    mo.garantia = ''no'' and
                                                    mo.cierre_periodo = ''no'' and
                                                    mo.id_periodo_venta is not null
                                                    and mo.id_periodo_venta < v_valor_maximo
                            group by mo.id_periodo_venta,pv.fecha_ini, pv.fecha_fin,mo.tipo
                            order by mo.id_periodo_venta asc

                            )

                            LOOP



                    insert into	temp ( id_agencia  ,
                                      id_periodo_venta ,
                                      tipo,
                                      debitos

                                        )
                                        values(
                                        v_parametros.id_agencia,
                                        v_debitos.id_periodo_venta,
                                        ''debitos'',
                                        v_debitos.debitos
                                      );
                    end loop;


                    FOR v_arrastre in (
        					select  mo.id_periodo_venta,
                            		Sum(mo.monto_total) as arrastre
                                    from obingresos.tmovimiento_entidad mo
                                    where mo.tipo = ''credito'' and
                                      mo.id_agencia = v_parametros.id_agencia AND
                                          mo.estado_reg = ''activo'' and
                                          mo.garantia = ''no'' and
                                          mo.cierre_periodo = ''si'' and
                                          mo.id_periodo_venta is not null
                                          and mo.id_periodo_venta < v_valor_maximo
                                    group by mo.id_periodo_venta
                                    order by mo.id_periodo_venta asc

                            )

                            LOOP



                    insert into	temp ( id_agencia  ,
                                      id_periodo_venta ,
                                      tipo,
                                      saldo_arrastrado

                                        )
                                        values(
                                        v_parametros.id_agencia,
                                        v_arrastre.id_periodo_venta,
                                        ''arrastre'',
                                        v_arrastre.arrastre
                                      );
                    end loop;


                    select  	  mo.id_periodo_venta,
                                  Sum(mo.monto_total) as saldos,
                                  mo.tipo,
                                  COALESCE(TO_CHAR(pe.fecha_ini,''DD'')||'' al ''|| TO_CHAR(pe.fecha_fin,''DD'')||'' ''||pe.mes||'' ''||EXTRACT(YEAR FROM pe.fecha_ini),''Periodo Vigente'')::text as periodo
								  into v_saldo_vigente_maximo
                                  from obingresos.tmovimiento_entidad mo
                                  LEFT JOIN obingresos.tperiodo_venta pe ON pe.id_periodo_venta = mo.id_periodo_venta
                                  where mo.tipo = ''credito'' and
                                    mo.id_agencia = v_parametros.id_agencia AND
                                    mo.estado_reg = ''activo'' and
                                    mo.garantia = ''no'' and
                                    mo.id_periodo_venta is not null
                                    and mo.id_periodo_venta = v_valor_maximo
                                  group by mo.id_periodo_venta,mo.tipo, pe.fecha_ini, pe.fecha_fin, pe.mes
                                  order by mo.id_periodo_venta asc;

                    Select
                            mo.id_periodo_venta,
                            sum(mo.monto_total) as depositos,
                            mo.tipo
                            into v_depositos_vigente_maximo
                            from obingresos.tmovimiento_entidad mo
                            where mo.tipo = ''credito'' and
                                                mo.id_agencia = v_parametros.id_agencia AND
                                                    mo.estado_reg = ''activo'' and
                                                    mo.garantia = ''no'' and
                                                    mo.cierre_periodo = ''no''and
                                                    mo.id_periodo_venta is not null
                                                    and mo.id_periodo_venta = v_valor_maximo
                            group by mo.id_periodo_venta,mo.tipo
                            order by mo.id_periodo_venta asc;

                    Select
                            mo.id_periodo_venta,
                            sum(mo.monto) as debitos,
                            COALESCE(TO_CHAR(pe.fecha_ini,''DD'')||'' al ''|| TO_CHAR(pe.fecha_fin,''DD'')||'' ''||pe.mes||'' ''||EXTRACT(YEAR FROM pe.fecha_ini),''Periodo Vigente'')::text as periodo

                            into v_debitos_vigente_maximo
                            from obingresos.tmovimiento_entidad mo
                            LEFT JOIN obingresos.tperiodo_venta pe ON pe.id_periodo_venta = mo.id_periodo_venta
                            where mo.tipo = ''debito'' and
                                                mo.id_agencia = v_parametros.id_agencia AND
                                                    mo.estado_reg = ''activo'' and
                                                    mo.garantia = ''no'' and
                                                    mo.cierre_periodo = ''no'' and
                                                    mo.id_periodo_venta is not null
                                                    and mo.id_periodo_venta = v_valor_maximo
                            group by mo.id_periodo_venta, pe.fecha_ini, pe.fecha_fin, pe.mes
                            order by mo.id_periodo_venta asc;

                    select  mo.id_periodo_venta,
                            		Sum(mo.monto_total) as arrastre
                                    into v_arrastre_vigente_maximo
                                    from obingresos.tmovimiento_entidad mo
                                    where mo.tipo = ''credito'' and
                                      mo.id_agencia = v_parametros.id_agencia AND
                                          mo.estado_reg = ''activo'' and
                                          mo.garantia = ''no'' and
                                          mo.cierre_periodo = ''si'' and
                                          mo.id_periodo_venta is not null
                                          and mo.id_periodo_venta = v_valor_maximo
                                    group by mo.id_periodo_venta
                                    order by mo.id_periodo_venta asc;


                    insert into	temp ( id_agencia,
                    				   id_periodo_venta,
                                       tipo,
                                       depositos_con_saldos,
                                       depositos,
                                       debitos,
                                       saldo_arrastrado,
                                       periodo

                                        )
                                        values(
                                        v_parametros.id_agencia,
                                        CASE WHEN v_saldo_vigente_maximo.id_periodo_venta is null
                                        THEN v_debitos_vigente_maximo.id_periodo_venta
                                              ELSE v_saldo_vigente_maximo.id_periodo_venta
                                        END,
                                        ''ultimo_periodo'',
                                        v_saldo_vigente_maximo.saldos,
                                        v_depositos_vigente_maximo.depositos,
                                        v_debitos_vigente_maximo.debitos,
                                        v_arrastre_vigente_maximo.arrastre,
                                        CASE WHEN v_saldo_vigente_maximo.periodo is null
                                        THEN v_debitos_vigente_maximo.periodo
                                              ELSE v_saldo_vigente_maximo.periodo
                                        END


                                      );





					select  	  mo.id_periodo_venta,
                                  Sum(mo.monto_total) as saldos,
                                  mo.tipo,
                                  COALESCE(TO_CHAR(pe.fecha_ini,''DD'')||'' al ''|| TO_CHAR(pe.fecha_fin,''DD'')||'' ''||pe.mes||'' ''||EXTRACT(YEAR FROM pe.fecha_ini),''Periodo Vigente'')::text as periodo
								  into v_saldo_vigente
                                  from obingresos.tmovimiento_entidad mo
                                  LEFT JOIN obingresos.tperiodo_venta pe ON pe.id_periodo_venta = mo.id_periodo_venta
                                  where mo.tipo = ''credito'' and
                                    mo.id_agencia = v_parametros.id_agencia AND
                                    mo.estado_reg = ''activo'' and
                                    mo.garantia = ''no'' and
                                    mo.id_periodo_venta is null
                                  group by mo.id_periodo_venta,mo.tipo, pe.fecha_ini, pe.fecha_fin, pe.mes
                                  order by mo.id_periodo_venta asc;

                    Select
                            mo.id_periodo_venta,
                            sum(mo.monto_total) as depositos,
                            mo.tipo
                            into v_depositos_vigente
                            from obingresos.tmovimiento_entidad mo
                            where mo.tipo = ''credito'' and
                                                mo.id_agencia = v_parametros.id_agencia AND
                                                    mo.estado_reg = ''activo'' and
                                                    mo.garantia = ''no'' and
                                                    mo.cierre_periodo = ''no''and
                                                    mo.id_periodo_venta is null
                            group by mo.id_periodo_venta,mo.tipo
                            order by mo.id_periodo_venta asc;

                    Select
                            mo.id_periodo_venta,
                            pv.fecha_ini,
                            pv.fecha_fin,
                            sum(mo.monto) as debitos,
                            mo.tipo
                            into v_debitos_vigente
                            from obingresos.tmovimiento_entidad mo
                            left join obingresos.tperiodo_venta pv on pv.id_periodo_venta=mo.id_periodo_venta
                            where mo.tipo = ''debito'' and
                                                mo.id_agencia = v_parametros.id_agencia AND
                                                    mo.estado_reg = ''activo'' and
                                                    mo.garantia = ''no'' and
                                                    mo.cierre_periodo = ''no'' and
                                                    mo.id_periodo_venta is null
                            group by mo.id_periodo_venta,pv.fecha_ini, pv.fecha_fin,mo.tipo
                            order by mo.id_periodo_venta asc;

                    select  mo.id_periodo_venta,
                            		Sum(mo.monto_total) as arrastre
                                    into v_arrastre_vigente
                                    from obingresos.tmovimiento_entidad mo
                                    where mo.tipo = ''credito'' and
                                      mo.id_agencia = v_parametros.id_agencia AND
                                          mo.estado_reg = ''activo'' and
                                          mo.garantia = ''no'' and
                                          mo.cierre_periodo = ''si'' and
                                          mo.id_periodo_venta is null
                                    group by mo.id_periodo_venta
                                    order by mo.id_periodo_venta asc;


                    insert into	temp ( id_agencia,

                                       tipo,
                                       depositos_con_saldos,
                                       depositos,
                                       debitos,
                                       saldo_arrastrado,
                                       periodo

                                        )
                                        values(
                                        v_parametros.id_agencia,

                                        ''periodo_vigente'',
                                        v_saldo_vigente.saldos,
                                        v_depositos_vigente.depositos,
                                        v_debitos_vigente.debitos,
                                        v_arrastre_vigente.arrastre,
                                        ''Periodo Vigente''


                                      );


                    --raise exception ''LLEGA EL SALDO %'',v_debitos_vigente.debitos;

    		--Sentencia de la consulta


			v_consulta:=''select 	id_agencia  ,
            						id_periodo_venta ,
                                    tipo,
                                    COALESCE(depositos_con_saldos,0) as depositos_con_saldos ,
                                    COALESCE(depositos,0) as depositos ,
                                    debitos ,
                                    saldo_arrastrado,
                                    periodo
                                	from temp'';

			--Devuelve la respuesta
			return v_consulta;

		end;



    /*********************************
     #TRANSACCION:  ''OBING_LISTA_SEL''
     #DESCRIPCION:	Reporte saldo vigente
     #AUTOR:		IRVA
     #FECHA:		23-11-2018
    ***********************************/
    elsif(p_transaccion=''OBING_LISTA_SEL'')then
        begin



        v_consulta =''with credito as (	select  mo.id_agencia,
		sum(case when mo.ajuste = ''''no'''' and  mo.garantia = ''''no''''   then
            	(case when mo.id_moneda = 1 then
                            	mo.monto
                            else
                            	param.f_convertir_moneda(2,1,mo.monto,mo.fecha,''''O'''',2)

                                end)
        else
            0
        end) as monto_credito,

		 sum(case when  mo.garantia = ''''si'''' and mo.id_periodo_venta is null then
           	(case when mo.id_moneda = 1 then
                            	mo.monto
                            else
                            	param.f_convertir_moneda(2,1,mo.monto,mo.fecha,''''O'''',2)

                                end)
        else
            0
        end) as garantia,

        sum(case when mo.ajuste = ''''si''''  then
            	(case when mo.id_moneda = 1 then
                            	mo.monto
                            else
                            	param.f_convertir_moneda(2,1,mo.monto,mo.fecha,''''O'''',2)

                                end)
        else
            0
        end) as ajuste_credito
        from obingresos.tmovimiento_entidad mo
		where mo.fecha >= ''''8/1/2017'''' and mo.fecha <= ''''''||v_parametros.fecha_fin||'''''' and
         mo.cierre_periodo = ''''no'''' and
      	 mo.estado_reg = ''''activo'''' and
         mo.tipo = ''''credito''''
		group by mo.id_agencia),
debitos as (select  mo.id_agencia,
sum(case when mo.ajuste = ''''no'''' then
          	(case when mo.id_moneda = 1 then
                            	mo.monto
                            else
                            	param.f_convertir_moneda(2,1,mo.monto,mo.fecha,''''O'''',2)

                                end)
        else
            0
        end) as monto_debito,
        sum(case when mo.ajuste = ''''si'''' then
          	(case when mo.id_moneda = 1 then
                            	mo.monto
                            else
                            	param.f_convertir_moneda(2,1,mo.monto,mo.fecha,''''O'''',2)

                                end)
        else
            0
        end) as ajuste_debito
from obingresos.tmovimiento_entidad mo
where mo.fecha >= ''''8/1/2017'''' and mo.fecha <= ''''''||v_parametros.fecha_fin||'''''' and
      mo.tipo = ''''debito'''' and
      mo.cierre_periodo = ''''no'''' and
      mo.estado_reg = ''''activo''''
group by mo.id_agencia),
contrato as( select max(id_contrato) as ultimo_contrato,
                  id_agencia
                  from leg.tcontrato c
                  where id_agencia is not null and c.estado = ''''finalizado''''
                  group by id_agencia)
        select  ag.id_agencia,
        		ag.nombre,
                ag.codigo_int,
       			ag.tipo_agencia,
                array_to_string(con.formas_pago, '''','''')::varchar as formas_pago,
    			l.codigo,
                COALESCE( cr.monto_credito,0),
                cr.garantia as codigo_ciudad,
                COALESCE( de.monto_debito,0),
               (COALESCE( cr.ajuste_credito,0) - COALESCE(de.ajuste_debito,0)) as monto_ajustes,
               (COALESCE( cr.garantia,0) + COALESCE(cr.monto_credito,0) - COALESCE(de.monto_debito,0) )+( COALESCE(cr.ajuste_credito,0)- COALESCE(de.ajuste_debito,0)) as saldo_con_boleto,
               (COALESCE( cr.monto_credito,0) - COALESCE(de.monto_debito,0)) + (COALESCE(cr.ajuste_credito,0) - COALESCE(de.ajuste_debito,0)) as saldo_sin_boleto
        from obingresos.tagencia ag
        inner join credito cr on cr.id_agencia = ag.id_agencia
        left join  debitos de on de.id_agencia = ag.id_agencia
        inner join contrato c on c.id_agencia = ag.id_agencia
		inner join leg.tcontrato con on con.id_contrato = c.ultimo_contrato
		inner join param.tlugar l on l.id_lugar = ag.id_lugar
        where  ''||v_parametros.filtro||''
        order by nombre'';
        return v_consulta;
		end;
    /*********************************
     #TRANSACCION:  ''OBING_SALVIG_CONT''
     #DESCRIPCION:	Listado
     #AUTOR:		IRVA
     #FECHA:		23-11-2018
    ***********************************/

    elsif(p_transaccion=''OBING_LISTA_CONT'')then

      begin
      	v_consulta = ''with credito as (	select  mo.id_agencia,
		sum(case when mo.ajuste = ''''no'''' and  mo.garantia = ''''no''''   then
            	(case when mo.id_moneda = 1 then
                            	mo.monto
                            else
                            	param.f_convertir_moneda(2,1,mo.monto,mo.fecha,''''O'''',2)

                                end)
        else
            0
        end) as monto_credito,

		 sum(case when  mo.garantia = ''''si'''' and mo.id_periodo_venta is null then
           	(case when mo.id_moneda = 1 then
                            	mo.monto
                            else
                            	param.f_convertir_moneda(2,1,mo.monto,mo.fecha,''''O'''',2)

                                end)
        else
            0
        end) as garantia,

        sum(case when mo.ajuste = ''''si''''  then
            	(case when mo.id_moneda = 1 then
                            	mo.monto
                            else
                            	param.f_convertir_moneda(2,1,mo.monto,mo.fecha,''''O'''',2)

                                end)
        else
            0
        end) as ajuste_credito
        from obingresos.tmovimiento_entidad mo
		where mo.fecha >= ''''8/1/2017'''' and mo.fecha <= ''''''||v_parametros.fecha_fin||'''''' and
         mo.cierre_periodo = ''''no'''' and
      	 mo.estado_reg = ''''activo'''' and
         mo.tipo = ''''credito''''
		group by mo.id_agencia),

debitos as (select  mo.id_agencia,
sum(case when mo.ajuste = ''''no'''' then
          	(case when mo.id_moneda = 1 then
                            	mo.monto
                            else
                            	param.f_convertir_moneda(2,1,mo.monto,mo.fecha,''''O'''',2)

                                end)
        else
            0
        end) as monto_debito,
        sum(case when mo.ajuste = ''''si'''' then
          	(case when mo.id_moneda = 1 then
                            	mo.monto
                            else
                            	param.f_convertir_moneda(2,1,mo.monto,mo.fecha,''''O'''',2)

                                end)
        else
            0
        end) as ajuste_debito
from obingresos.tmovimiento_entidad mo
where mo.fecha >= ''''8/1/2017'''' and mo.fecha <=''''''||v_parametros.fecha_fin||'''''' and
      mo.tipo = ''''debito'''' and
      mo.cierre_periodo = ''''no'''' and
      mo.estado_reg = ''''activo''''
group by mo.id_agencia),
contrato as( select max(id_contrato) as ultimo_contrato,
                  id_agencia
                  from leg.tcontrato c
                  where id_agencia is not null and c.estado = ''''finalizado''''
                  group by id_agencia),
 detalle as  (select  ag.id_agencia,
        		ag.nombre,
                ag.codigo_int,
       			ag.tipo_agencia,
                array_to_string(con.formas_pago, '''','''')::varchar as formas_pago,
    			l.codigo,
                cr.monto_credito ,
                cr.garantia as codigo_ciudad,
                de.monto_debito,
               (cr.ajuste_credito - de.ajuste_debito) as monto_ajustes,
               ( cr.garantia+ cr.monto_credito - de.monto_debito )+(cr.ajuste_credito- de.ajuste_debito) as saldo_con_boleto,
               ( cr.monto_credito - de.monto_debito) + (cr.ajuste_credito - de.ajuste_debito) as saldo_sin_boleto
        from obingresos.tagencia ag
        inner join credito cr on cr.id_agencia = ag.id_agencia
        left join  debitos de on de.id_agencia = ag.id_agencia
        inner join contrato c on c.id_agencia = ag.id_agencia
		inner join leg.tcontrato con on con.id_contrato = c.ultimo_contrato
		inner join param.tlugar l on l.id_lugar = ag.id_lugar
        where  ''||v_parametros.filtro||'')
select count(id_agencia),
 sum(monto_credito) as total_creditos,
 sum(monto_debito)as total_debitos,
 sum(monto_ajustes) as total_ajuste,
 sum(saldo_con_boleto) as total_saldo_con_boleto,
 sum(saldo_sin_boleto) as total_saldo_sin_boleto
from detalle'';
        --Devuelve la respuesta
        return v_consulta;

      end;
/*********************************
 	#TRANSACCION:  ''OBING_MONE_SEL''
 	#DESCRIPCION:	Tipo Moneda
 	#AUTOR:		Ismael Valdivia
 	#FECHA:		07-01-2018 10:30:00
	***********************************/

	elsif (p_transaccion=''OBING_MONE_SEL'')then

        begin
		v_consulta = ''Select
                             mo.id_agencia,
                             mo.id_moneda,
                             mone.codigo,
                             mone.moneda,
                             ag.nombre
                    from obingresos.tmovimiento_entidad mo
                    inner join param.tmoneda mone on mone.id_moneda = mo.id_moneda
                    inner join obingresos.tagencia ag on ag.id_agencia = mo.id_agencia
                    where'';


       --Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
            v_consulta:=v_consulta||''GROUP BY mo.id_agencia,  mo.id_moneda, mone.codigo, mone.moneda, ag.nombre'';
			v_consulta:=v_consulta||'' order by '' ||v_parametros.ordenacion|| '' '' || v_parametros.dir_ordenacion || '' limit '' || v_parametros.cantidad || '' offset '' || v_parametros.puntero;
				--Devuelve la respuesta
			return v_consulta;
		end;
    /*********************************
     #TRANSACCION:  ''OBING_MONE_CONT''
     #DESCRIPCION:	Reporte saldo vigente
     #AUTOR:		MMV
     #FECHA:		18-11-2018
    ***********************************/
    elsif(p_transaccion=''OBING_MONE_CONT'')then

		begin
			--Sentencia de la consulta de conteo de registros

			v_consulta:=''select  count(mo.id_agencia)
            					 from obingresos.tmovimiento_entidad mo
                                  inner join param.tmoneda mone on mone.id_moneda = mo.id_moneda
                                  inner join obingresos.tagencia ag on ag.id_agencia = mo.id_agencia
                              	 where '';
			v_consulta:=v_consulta||v_parametros.filtro;
            --v_consulta:=v_consulta||''GROUP BY mo.id_agencia,  mo.id_moneda, mone.codigo, mone.moneda, ag.nombre'';
            raise notice ''cos -> %'',v_consulta;
			--Devuelve la respuest
			return v_consulta;
    end;


    /*********************************
 	#TRANSACCION:  ''OBING_CORRSAL_SEL''
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		IVALDIVIA
 	#FECHA:		22-10-2019 09:37:00
	***********************************/

	elsif(p_transaccion=''OBING_CORRSAL_SEL'')then


    	begin
        select  max(mo.id_periodo_venta)
        into v_maximo_credito
        from obingresos.tmovimiento_entidad mo
        where mo.tipo = ''credito'' and
          mo.id_agencia = v_parametros.id_agencia AND
          mo.estado_reg = ''activo'' and
          mo.garantia = ''no'' and
          mo.id_periodo_venta is not null;

    	Select
        max(mo.id_periodo_venta)
        into v_maximo_debito
        from obingresos.tmovimiento_entidad mo
        where mo.tipo = ''debito'' and
          mo.id_agencia = v_parametros.id_agencia AND
          mo.estado_reg = ''activo'' and
          mo.garantia = ''no'' and
          mo.cierre_periodo = ''no'' and
          mo.id_periodo_venta is not null;

        if (v_maximo_credito > v_maximo_debito) then
        	v_valor_maximo = v_maximo_credito;
        else
        	v_valor_maximo = v_maximo_debito ;
        end if;


		/*Creamos una tabla temporal para insertar los creditos + saldos*/
           CREATE TEMPORARY TABLE creditos_saldos (  id_agencia  int4,
                                                  id_periodo_venta int4,
                                                  tipo varchar,
                                                  depositos_con_saldos NUMERIC,
                                                  saldo_arrastrado NUMERIC,
                                                  periodo varchar
                                              )ON COMMIT DROP;




        FOR v_record in ( select  mo.id_periodo_venta,
                                  Sum(mo.monto_total) as saldos,
                                  mo.tipo,
                                  COALESCE(TO_CHAR(pe.fecha_ini,''DD'')||'' al ''|| TO_CHAR(pe.fecha_fin,''DD'')||'' ''||pe.mes||'' ''||EXTRACT(YEAR FROM pe.fecha_ini),''Periodo Vigente'')::text as periodo
                                  from obingresos.tmovimiento_entidad mo
                                  LEFT JOIN obingresos.tperiodo_venta pe ON pe.id_periodo_venta = mo.id_periodo_venta
                                  where mo.tipo = ''credito'' and
                                    mo.id_agencia = v_parametros.id_agencia AND
                                    mo.estado_reg = ''activo'' and
                                    mo.garantia = ''no'' and
                                    mo.id_periodo_venta is not null
                                    and mo.id_periodo_venta <= v_valor_maximo
                                  group by mo.id_periodo_venta,mo.tipo, pe.fecha_ini, pe.fecha_fin, pe.mes
                                  order by mo.id_periodo_venta asc
                        )

                      LOOP
        					insert into	creditos_saldos ( id_agencia  ,
                                                          id_periodo_venta ,
                                                          tipo,
                                                          depositos_con_saldos,
                                                          periodo
                                        				)
                                                          values(
                                                          v_parametros.id_agencia,
                                                          v_record.id_periodo_venta,
                                                          v_record.tipo,
                                                          v_record.saldos,
                                                          v_record.periodo
                                                        );

                      END LOOP;

        /*Tabla para el periodo vigente*/
        CREATE TEMPORARY TABLE creditos_saldos_vigentes (  id_agencia  int4,
                                                  id_periodo_venta int4,
                                                  tipo varchar,
                                                  depositos_con_saldos NUMERIC,
                                                  saldo_arrastrado NUMERIC,
                                                  periodo varchar
                                              )ON COMMIT DROP;




        FOR v_record in ( select  mo.id_periodo_venta,
                                  Sum(mo.monto_total) as saldos,
                                  mo.tipo,
                                  COALESCE(TO_CHAR(pe.fecha_ini,''DD'')||'' al ''|| TO_CHAR(pe.fecha_fin,''DD'')||'' ''||pe.mes||'' ''||EXTRACT(YEAR FROM pe.fecha_ini),''Periodo Vigente'')::text as periodo
                                  from obingresos.tmovimiento_entidad mo
                                  LEFT JOIN obingresos.tperiodo_venta pe ON pe.id_periodo_venta = mo.id_periodo_venta
                                  where mo.tipo = ''credito'' and
                                    mo.id_agencia = v_parametros.id_agencia AND
                                    mo.estado_reg = ''activo'' and
                                    mo.garantia = ''no'' and
                                    mo.id_periodo_venta is null
                                    --and mo.id_periodo_venta < v_valor_maximo
                                  group by mo.id_periodo_venta,mo.tipo, pe.fecha_ini, pe.fecha_fin, pe.mes
                                  order by mo.id_periodo_venta asc
                        )

                      LOOP
        					insert into	creditos_saldos_vigentes ( id_agencia  ,
                                                          id_periodo_venta ,
                                                          tipo,
                                                          depositos_con_saldos,
                                                          periodo
                                        				)
                                                          values(
                                                          v_parametros.id_agencia,
                                                          0,
                                                          v_record.tipo,
                                                          v_record.saldos,
                                                          v_record.periodo
                                                        );

                      END LOOP;
        /*******************************/

        /******************************************************************************************************************/

        /******Creamos tabla temporal para los depositos******/
                    CREATE TEMPORARY TABLE depositos (  id_agencia  int4,
                                                              id_periodo_venta int4,
                                                              tipo varchar,
                                                              depositos NUMERIC
                                                          )ON COMMIT DROP;

        			FOR v_depositos in (
        					Select
                            mo.id_periodo_venta,
                            sum(mo.monto_total) as depositos,
                            mo.tipo
                            from obingresos.tmovimiento_entidad mo
                            where mo.tipo = ''credito'' and
                                                mo.id_agencia = v_parametros.id_agencia AND
                                                    mo.estado_reg = ''activo'' and
                                                    mo.garantia = ''no'' and
                                                    mo.cierre_periodo = ''no'' and
                                                    mo.id_periodo_venta is not null
                                                    and mo.id_periodo_venta <= v_valor_maximo
                            group by mo.id_periodo_venta,mo.tipo
                            order by mo.id_periodo_venta asc

                            )

                            LOOP
                              insert into	depositos (  id_agencia  ,
                                                    id_periodo_venta ,
                                                    tipo,
                                                    depositos
                                                  )
                                                  values(
                                                    v_parametros.id_agencia,
                                                    v_depositos.id_periodo_venta,
                                                    ''depositos'',
                                                    v_depositos.depositos
                                                );
                              end loop;

       	/*Tabla para el periodo Vigente*/
        				CREATE TEMPORARY TABLE depositos_vigente (  id_agencia  int4,
                                                              id_periodo_venta int4,
                                                              tipo varchar,
                                                              depositos NUMERIC
                                                          )ON COMMIT DROP;

        			FOR v_depositos_vigente in (
        					Select
                            mo.id_periodo_venta,
                            sum(mo.monto_total) as depositos,
                            mo.tipo
                            from obingresos.tmovimiento_entidad mo
                            where mo.tipo = ''credito'' and
                                                mo.id_agencia = v_parametros.id_agencia AND
                                                    mo.estado_reg = ''activo'' and
                                                    mo.garantia = ''no'' and
                                                    mo.cierre_periodo = ''no'' and
                                                    mo.id_periodo_venta is null
                                                    --and mo.id_periodo_venta < v_valor_maximo
                            group by mo.id_periodo_venta,mo.tipo
                            order by mo.id_periodo_venta asc

                            )

                            LOOP
                              insert into depositos_vigente (  id_agencia  ,
                                                    id_periodo_venta ,
                                                    tipo,
                                                    depositos
                                                  )
                                                  values(
                                                    v_parametros.id_agencia,
                                                    0,
                                                    ''depositos'',
                                                    v_depositos_vigente.depositos
                                                );
                              end loop;

		/*********************************************************/

        /*Creamos Tabla temporal para los debitos*/
    					CREATE TEMPORARY TABLE debitos (  	  id_agencia  int4,
                                                              id_periodo_venta int4,
                                                              tipo varchar,
                                                              debitos NUMERIC
                                                          )ON COMMIT DROP;
        FOR v_debitos in (
        					Select
                            mo.id_periodo_venta,
                            pv.fecha_ini,
                            pv.fecha_fin,
                            sum(mo.monto) as debitos,
                            mo.tipo
                            from obingresos.tmovimiento_entidad mo
                            left join obingresos.tperiodo_venta pv on pv.id_periodo_venta=mo.id_periodo_venta
                            where mo.tipo = ''debito'' and
                                                mo.id_agencia = v_parametros.id_agencia AND
                                                    mo.estado_reg = ''activo'' and
                                                    mo.garantia = ''no'' and
                                                    mo.cierre_periodo = ''no'' and
                                                    mo.id_periodo_venta is not null
                                                    and mo.id_periodo_venta <= v_valor_maximo
                            group by mo.id_periodo_venta,pv.fecha_ini, pv.fecha_fin,mo.tipo
                            order by mo.id_periodo_venta asc

                            )

                            LOOP
                                                insert into	debitos ( id_agencia  ,
                                                                  id_periodo_venta ,
                                                                  tipo,
                                                                  debitos

                                                )
                                                values(
                                                                  v_parametros.id_agencia,
                                                                  v_debitos.id_periodo_venta,
                                                                  ''debitos'',
                                                                  v_debitos.debitos
                                              );
                            end loop;

        /*Tabla periodo Vigente*/
        				CREATE TEMPORARY TABLE debitos_vigente (  	  id_agencia  int4,
                                                                      id_periodo_venta int4,
                                                                      tipo varchar,
                                                                      debitos NUMERIC
                                                                  )ON COMMIT DROP;
        FOR v_debitos in (
        					Select
                            mo.id_periodo_venta,
                            pv.fecha_ini,
                            pv.fecha_fin,
                            sum(mo.monto) as debitos,
                            mo.tipo
                            from obingresos.tmovimiento_entidad mo
                            left join obingresos.tperiodo_venta pv on pv.id_periodo_venta=mo.id_periodo_venta
                            where mo.tipo = ''debito'' and
                                                mo.id_agencia = v_parametros.id_agencia AND
                                                    mo.estado_reg = ''activo'' and
                                                    mo.garantia = ''no'' and
                                                    mo.cierre_periodo = ''no'' and
                                                    mo.id_periodo_venta is null
                                                    --and mo.id_periodo_venta < v_valor_maximo
                            group by mo.id_periodo_venta,pv.fecha_ini, pv.fecha_fin,mo.tipo
                            order by mo.id_periodo_venta asc

                            )

                            LOOP
                                                insert into	debitos_vigente ( id_agencia  ,
                                                                  id_periodo_venta ,
                                                                  tipo,
                                                                  debitos

                                                )
                                                values(
                                                                  v_parametros.id_agencia,
                                                                  0,
                                                                  ''debitos'',
                                                                  v_debitos.debitos
                                              );
                            end loop;


        /***************************************************************************************/


        /*Creamos tabla temporal para el saldo arrastrado*/
                          CREATE TEMPORARY TABLE saldo_arrastrado (    id_agencia  int4,
                                                                       id_periodo_venta int4,
                                                                       tipo varchar,
                                                                       saldo_arrastrado NUMERIC
                                                          )ON COMMIT DROP;
        FOR v_arrastre in (
        					select  mo.id_periodo_venta,
                            		Sum(mo.monto_total) as arrastre
                                    from obingresos.tmovimiento_entidad mo
                                    where mo.tipo = ''credito'' and
                                      mo.id_agencia = v_parametros.id_agencia AND
                                          mo.estado_reg = ''activo'' and
                                          mo.garantia = ''no'' and
                                          mo.cierre_periodo = ''si'' and
                                          mo.id_periodo_venta is not null
                                          and mo.id_periodo_venta <= v_valor_maximo
                                    group by mo.id_periodo_venta
                                    order by mo.id_periodo_venta asc

                            )

                            LOOP
                            					insert into	saldo_arrastrado ( id_agencia  ,
                                                                              id_periodo_venta ,
                                                                              tipo,
                                                                              saldo_arrastrado
                                                                              )
                                                                              values(
                                                                              v_parametros.id_agencia,
                                                                              v_arrastre.id_periodo_venta,
                                                                              ''arrastre'',
                                                                              --COALESCE(v_arrastre.arrastre,0)
                                                                              v_arrastre.arrastre
                                                                            );
                            end loop;
        /*Periodo Vigente*/
                      CREATE TEMPORARY TABLE saldo_arrastrado_vigente (    id_agencia  int4,
                                                                                     id_periodo_venta int4,
                                                                                     tipo varchar,
                                                                                     saldo_arrastrado NUMERIC
                                                                        )ON COMMIT DROP;
                      FOR v_arrastre in (
                                          select  mo.id_periodo_venta,
                                                  Sum(mo.monto_total) as arrastre
                                                  from obingresos.tmovimiento_entidad mo
                                                  where mo.tipo = ''credito'' and
                                                    mo.id_agencia = v_parametros.id_agencia AND
                                                        mo.estado_reg = ''activo'' and
                                                        mo.garantia = ''no'' and
                                                        mo.cierre_periodo = ''si'' and
                                                        mo.id_periodo_venta is null
                                                        --and mo.id_periodo_venta < v_valor_maximo
                                                  group by mo.id_periodo_venta
                                                  order by mo.id_periodo_venta asc

                                          )

                                          LOOP
                                                              insert into	saldo_arrastrado_vigente ( id_agencia  ,
                                                                                            id_periodo_venta ,
                                                                                            tipo,
                                                                                            saldo_arrastrado
                                                                                            )
                                                                                            values(
                                                                                            v_parametros.id_agencia,
                                                                                            0,
                                                                                            ''arrastre'',
                                                                                            --COALESCE(v_arrastre.arrastre,0)
                                                                                            v_arrastre.arrastre
                                                                                          );
                                          end loop;


        /*************************************************/


        /*Calculamos la diferencia*/


      CREATE TEMPORARY TABLE saldo_calculado (    	  id_agencia  int4,
                                                      id_periodo_venta int4,
                                                      tipo varchar,
                                                      saldo_calculado NUMERIC
                                              )ON COMMIT DROP;

      	FOR v_saldo_calculado in (

                            select 	cr.id_agencia  ,
                                    cr.id_periodo_venta ,
                                    cr.tipo,
                                    COALESCE(cr.depositos_con_saldos,0) as depositos_con_saldos ,
                                    COALESCE(de.depositos,0) as depositos ,
                                    COALESCE(deb.debitos,0) as debitos,
                                    COALESCE((COALESCE(cr.depositos_con_saldos,0)-COALESCE(deb.debitos,0)),0) as saldo_calculado,
                                    COALESCE(arr.saldo_arrastrado,0) as saldo_arrastrado,
                                    cr.periodo
                            from creditos_saldos cr
                            left join depositos de on de.id_periodo_venta = cr.id_periodo_venta
                            left join debitos deb on deb.id_periodo_venta = cr.id_periodo_venta
                            left join saldo_arrastrado arr on arr.id_periodo_venta = cr.id_periodo_venta
                            ORDER BY cr.id_periodo_venta ASC
                            )
                            LOOP
                            					insert into	saldo_calculado ( id_agencia  ,
                                                                              id_periodo_venta ,
                                                                              tipo,
                                                                              saldo_calculado
                                                                              )
                                                                              values(
                                                                              v_parametros.id_agencia,
                                                                              v_saldo_calculado.id_periodo_venta,
                                                                              ''saldo_calculado'',
                                                                              v_saldo_calculado.saldo_calculado
                                                                            );
                            end loop;



        CREATE TEMPORARY TABLE saldo_calculado_vigente (    	  id_agencia  int4,
                                                      id_periodo_venta int4,
                                                      tipo varchar,
                                                      saldo_calculado NUMERIC
                                              )ON COMMIT DROP;

      	FOR v_saldo_calculado in (

                            select 	cr.id_agencia  ,
                                    cr.id_periodo_venta ,
                                    cr.tipo,
                                    COALESCE(cr.depositos_con_saldos,0) as depositos_con_saldos ,
                                    COALESCE(de.depositos,0) as depositos ,
                                    COALESCE(deb.debitos,0) as debitos,
                                    COALESCE((COALESCE(cr.depositos_con_saldos,0)-COALESCE(deb.debitos,0)),0) as saldo_calculado,
                                    COALESCE(arr.saldo_arrastrado,0) as saldo_arrastrado,
                                    cr.periodo
                            from creditos_saldos_vigentes cr
                            left join depositos_vigente de on de.id_periodo_venta = cr.id_periodo_venta
                            left join debitos_vigente deb on deb.id_periodo_venta = cr.id_periodo_venta
                            left join saldo_arrastrado_vigente arr on arr.id_periodo_venta = cr.id_periodo_venta
                            ORDER BY cr.id_periodo_venta ASC
                            )
                            LOOP
                            					insert into	saldo_calculado ( id_agencia  ,
                                                                              id_periodo_venta ,
                                                                              tipo,
                                                                              saldo_calculado
                                                                              )
                                                                              values(
                                                                              v_parametros.id_agencia,
                                                                              v_saldo_calculado.id_periodo_venta,
                                                                              ''saldo_calculado'',
                                                                              v_saldo_calculado.saldo_calculado
                                                                            );
                            end loop;





        --raise exception ''llega aqui el sado arrastre %'',v_length_arrastre;

         CREATE TEMPORARY TABLE diferencia_calculada (      id_agencia  int4,
                                                      		id_periodo_venta int4,
                                                       		diferencia NUMERIC
                                              )ON COMMIT DROP;

         							    /*Tomar el saldo calculado desde el siguiente periodo minimo*/
                                          select id_periodo_venta into v_id_periodo_venta
                                          from saldo_arrastrado
                                          ORDER BY id_periodo_venta ASC
                                          limit 1;



                                          /*select id_periodo_venta into v_id_periodo_venta
                                          from saldo_calculado
                                          ORDER BY id_periodo_venta ASC
                                          limit 1;*/
                                        /************************************************************/

                                        --raise exception ''llega aqui %'',v_id_periodo_venta;

                              			for  v_datos_saldo_calculado in (

                                                                          select *
                                                                          from saldo_calculado
                                                                          where id_periodo_venta > v_id_periodo_venta
                                                                          ORDER BY id_periodo_venta ASC

                                        								) loop

                                                                          select saldo_arrastrado,
                                                                          		 id_periodo_venta into v_datos_saldo_arrastre
                                                                          from saldo_arrastrado
                                                                          where id_periodo_venta = v_datos_saldo_calculado.id_periodo_venta;

                                                                           --if ( v_datos_saldo_arrastre.saldo_arrastrado is not null ) then
                                                                           if (v_datos_saldo_arrastre.saldo_arrastrado is null) then
                                                                           	v_datos_saldo_arrastre.saldo_arrastrado = 0;
                                                                           end if;


                                                                            	 select saldo_calculado into v_saldo
                                                                                 from saldo_calculado
                                                                                 where id_periodo_venta = (v_datos_saldo_calculado.id_periodo_venta-1);

                                                                                  v_diferencia = v_saldo - v_datos_saldo_arrastre.saldo_arrastrado;

                                                                                 -- raise exception ''llega aqui el saldo:%.'',v_diferencia;

                                                                                      insert into	diferencia_calculada (
                                                                                                        id_agencia,
                                                                                                        id_periodo_venta ,
                                                                                                        diferencia
                                                                                                        )
                                                                                    values(
                                                                                            v_parametros.id_agencia,
                                                                                            v_datos_saldo_calculado.id_periodo_venta,
                                                                                            v_diferencia
                                                                                          );




                                                                            --end if;



                                                                        end loop;

         	/*Diferencia Periodo Vigente*/
            CREATE TEMPORARY TABLE diferencia_calculada_vigente (      id_agencia  int4,
                                                               id_periodo_venta int4,
                                                               diferencia NUMERIC
                                                )ON COMMIT DROP;


                              			for  v_datos_saldo_calculado_vigente in (

                                                                          select *
                                                                          from saldo_calculado
                                                                          order by id_periodo_venta DESC
                                                                          limit 1

                                        								) loop

                                                                          select saldo_arrastrado,
                                                                          		 id_periodo_venta into v_datos_saldo_arrastre_vigente
                                                                          from saldo_arrastrado_vigente
                                                                          where id_periodo_venta = 0;

                                                                         --raise exception ''la diferencia periodo vigente es:%.'',v_datos_saldo_arrastre;

                                                                            if ( v_datos_saldo_arrastre is not null ) then

                                                                            	 select saldo_calculado into v_saldo
                                                                                 from saldo_calculado
                                                                                 where id_periodo_venta = 0;

                                                                           -- raise exception ''la diferencia periodo vigente es:%.'',v_datos_saldo_calculado_vigente.saldo_calculado;

                                                                                  v_diferencia = v_datos_saldo_calculado_vigente.saldo_calculado - v_datos_saldo_arrastre_vigente;
                                                                                 -- raise exception ''la diferencia periodo vigente es:%.'',v_diferencia;
                                                                                      insert into	diferencia_calculada_vigente (
                                                                                                        id_agencia,
                                                                                                        id_periodo_venta ,
                                                                                                        diferencia
                                                                                                        )
                                                                                    values(
                                                                                            v_parametros.id_agencia,
                                                                                            0,
                                                                                            v_diferencia
                                                                                          );




                                                                            end if;



                                                                        end loop;
            /****************************/



        /**************************/



			v_consulta:=''(select 	cr.id_agencia  ,
            						cr.id_periodo_venta ,
                                    cr.tipo,
                                    COALESCE(cr.depositos_con_saldos,0) as depositos_con_saldos ,
                                    COALESCE(de.depositos,0) as depositos,
                                    COALESCE(deb.debitos,0) as debitos,
                                    COALESCE((COALESCE(cr.depositos_con_saldos,0)-COALESCE(deb.debitos,0)),0) as saldo_calculado,
                                    COALESCE(arr.saldo_arrastrado,0) as saldo_arrastrado,
                                    cr.periodo,
                                    COALESCE(dif.diferencia,0) as diferencia
                                	from creditos_saldos cr
                                    left join depositos de on de.id_periodo_venta = cr.id_periodo_venta
                                    left join debitos deb on deb.id_periodo_venta = cr.id_periodo_venta
                                    left join saldo_arrastrado arr on arr.id_periodo_venta = cr.id_periodo_venta
                                    left join diferencia_calculada dif on dif.id_periodo_venta = cr.id_periodo_venta
                                    ORDER BY cr.id_periodo_venta ASC)

            				UNION

            				(select cr.id_agencia  ,
            						Null::integer as id_periodo_venta ,
                                    cr.tipo,
                                    COALESCE(cr.depositos_con_saldos,0) as depositos_con_saldos ,
                                    COALESCE(de.depositos,0) as depositos,
                                    COALESCE(deb.debitos,0) as debitos,
                                    COALESCE((COALESCE(cr.depositos_con_saldos,0)-COALESCE(deb.debitos,0)),0) as saldo_calculado,
                                    COALESCE(arr.saldo_arrastrado,0) as saldo_arrastrado,
                                    cr.periodo,
                                    COALESCE(dif.diferencia,0) as diferencia
                                	from creditos_saldos_vigentes cr
                                    left join depositos_vigente de on de.id_periodo_venta = cr.id_periodo_venta
                                    left join debitos_vigente deb on deb.id_periodo_venta = cr.id_periodo_venta
                                    left join saldo_arrastrado_vigente arr on arr.id_periodo_venta = cr.id_periodo_venta
                                    left join diferencia_calculada_vigente dif on dif.id_periodo_venta = cr.id_periodo_venta
                                    )'';

			--Devuelve la respuesta
			return v_consulta;

		end;






	else

		raise exception ''Transaccion inexistente'';

	end if;

EXCEPTION

	WHEN OTHERS THEN
			v_resp='''';
			v_resp = pxp.f_agrega_clave(v_resp,''mensaje'',SQLERRM);
			v_resp = pxp.f_agrega_clave(v_resp,''codigo_error'',SQLSTATE);
			v_resp = pxp.f_agrega_clave(v_resp,''procedimientos'',v_nombre_funcion);
			raise exception ''%'',v_resp;
END;
'LANGUAGE 'plpgsql'
VOLATILE
CALLED ON NULL INPUT
SECURITY INVOKER
COST 100;

ALTER FUNCTION obingresos.ft_control_agencia_sel (p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
  OWNER TO postgres;
