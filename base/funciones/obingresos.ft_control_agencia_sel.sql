CREATE OR REPLACE FUNCTION obingresos.ft_control_agencia_sel (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Ingresos
 FUNCION: 		obingresos.ft_control_agencia_sel
 DESCRIPCION:   Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'obingresos.treporte_cuenta'
 AUTOR: 		 (Ismael.Valdivia)
 FECHA:	        11-06-2018 15:14:58
 COMENTARIOS:
***************************************************************************
 HISTORIAL DE MODIFICACIONES:
#ISSUE				FECHA				AUTOR				DESCRIPCION
 #0				11-06-2018 15:14:58								Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'obingresos.treporte_cuenta'
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

BEGIN

	v_nombre_funcion = 'obingresos.ft_control_agencia_sel';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'OBING_REPORIS_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		IVALDIVIA
 	#FECHA:		02-01-2018 20:57:30
	***********************************/

	if(p_transaccion='OBING_REPORIS_SEL')then


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




        FOR v_record in ( select  mo.id_periodo_venta,
                                  Sum(mo.monto_total) as saldos,
                                  mo.tipo
                                  from obingresos.tmovimiento_entidad mo
                                  where mo.tipo = 'credito' and
                                    mo.id_agencia = v_parametros.id_agencia AND
                                    mo.estado_reg = 'activo' and
                                    mo.garantia = 'no'
                                  group by mo.id_periodo_venta,mo.tipo
                                  order by mo.id_periodo_venta asc
                        )

                      LOOP
        					insert into	temp ( id_agencia  ,
                                      id_periodo_venta ,
                                      tipo,
                                      depositos_con_saldos


                                        )
                                        values(
                                        v_parametros.id_agencia,
                                        v_record.id_periodo_venta,
                                        v_record.tipo,
                                        v_record.saldos

                                      );

    	END LOOP;

        FOR v_depositos in (
        					Select
                            mo.id_periodo_venta,
                            sum(mo.monto_total) as depositos,
                            mo.tipo,
                            COALESCE(TO_CHAR(pe.fecha_ini,'DD')||' al '|| TO_CHAR(pe.fecha_fin,'DD')||' '||pe.mes||' '||EXTRACT(YEAR FROM pe.fecha_ini),'Periodo Vigente')::text as periodo

                            from obingresos.tmovimiento_entidad mo
                            LEFT JOIN obingresos.tperiodo_venta pe ON pe.id_periodo_venta = mo.id_periodo_venta
                            where mo.tipo = 'credito' and
                                                mo.id_agencia = v_parametros.id_agencia AND
                                                    mo.estado_reg = 'activo' and
                                                    mo.garantia = 'no' and
                                                    mo.cierre_periodo = 'no'
                            group by mo.id_periodo_venta,mo.tipo, pe.fecha_ini, pe.fecha_fin, pe.mes
                            order by mo.id_periodo_venta asc

                            )

                            LOOP



                    insert into	temp ( id_agencia  ,
                                      id_periodo_venta ,
                                      tipo,
                                      depositos,
                                      periodo

                                        )
                                        values(
                                        v_parametros.id_agencia,
                                        v_depositos.id_periodo_venta,
                                        'depositos',
                                        v_depositos.depositos,
                                        v_depositos.periodo
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
                            where mo.tipo = 'debito' and
                                                mo.id_agencia = v_parametros.id_agencia AND
                                                    mo.estado_reg = 'activo' and
                                                    mo.garantia = 'no' and
                                                    mo.cierre_periodo = 'no'
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
                                        'debitos',
                                        v_debitos.debitos
                                      );
                    end loop;


                    FOR v_arrastre in (
        					select  mo.id_periodo_venta,
                            		Sum(mo.monto_total) as arrastre
                                    from obingresos.tmovimiento_entidad mo
                                    where mo.tipo = 'credito' and
                                      mo.id_agencia = v_parametros.id_agencia AND
                                          mo.estado_reg = 'activo' and
                                          mo.garantia = 'no' and
                                          mo.cierre_periodo = 'si'
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
                                        'arrastre',
                                        v_arrastre.arrastre
                                      );
                    end loop;

    		--Sentencia de la consulta


			v_consulta:='select 	id_agencia  ,
            						id_periodo_venta ,
                                    tipo,
                                    COALESCE(depositos_con_saldos,0) as depositos_con_saldos ,
                                    COALESCE(depositos,0) as depositos ,
                                    debitos ,
                                    saldo_arrastrado,
                                    periodo
                                	from temp';

			--Devuelve la respuesta
			return v_consulta;

		end;



    /*********************************
     #TRANSACCION:  'OBING_LISTA_SEL'
     #DESCRIPCION:	Reporte saldo vigente
     #AUTOR:		IRVA
     #FECHA:		23-11-2018
    ***********************************/
    elsif(p_transaccion='OBING_LISTA_SEL')then
        begin



        v_consulta ='with credito as (	select  mo.id_agencia,
		sum(case when mo.ajuste = ''no'' and  mo.garantia = ''no''   then
            	(case when mo.id_moneda = 1 then
                            	mo.monto
                            else
                            	param.f_convertir_moneda(2,1,mo.monto,mo.fecha,''O'',2)

                                end)
        else
            0
        end) as monto_credito,

		 sum(case when  mo.garantia = ''si'' and mo.id_periodo_venta is null then
           	(case when mo.id_moneda = 1 then
                            	mo.monto
                            else
                            	param.f_convertir_moneda(2,1,mo.monto,mo.fecha,''O'',2)

                                end)
        else
            0
        end) as garantia,

        sum(case when mo.ajuste = ''si''  then
            	(case when mo.id_moneda = 1 then
                            	mo.monto
                            else
                            	param.f_convertir_moneda(2,1,mo.monto,mo.fecha,''O'',2)

                                end)
        else
            0
        end) as ajuste_credito
        from obingresos.tmovimiento_entidad mo
		where mo.fecha >= ''8/1/2017'' and mo.fecha <= '''||v_parametros.fecha_fin||''' and
         mo.cierre_periodo = ''no'' and
      	 mo.estado_reg = ''activo'' and
         mo.tipo = ''credito''
		group by mo.id_agencia),
debitos as (select  mo.id_agencia,
sum(case when mo.ajuste = ''no'' then
          	(case when mo.id_moneda = 1 then
                            	mo.monto
                            else
                            	param.f_convertir_moneda(2,1,mo.monto,mo.fecha,''O'',2)

                                end)
        else
            0
        end) as monto_debito,
        sum(case when mo.ajuste = ''si'' then
          	(case when mo.id_moneda = 1 then
                            	mo.monto
                            else
                            	param.f_convertir_moneda(2,1,mo.monto,mo.fecha,''O'',2)

                                end)
        else
            0
        end) as ajuste_debito
from obingresos.tmovimiento_entidad mo
where mo.fecha >= ''8/1/2017'' and mo.fecha <= '''||v_parametros.fecha_fin||''' and
      mo.tipo = ''debito'' and
      mo.cierre_periodo = ''no'' and
      mo.estado_reg = ''activo''
group by mo.id_agencia),
contrato as( select max(id_contrato) as ultimo_contrato,
                  id_agencia
                  from leg.tcontrato c
                  where id_agencia is not null and c.estado = ''finalizado''
                  group by id_agencia)
        select  ag.id_agencia,
        		ag.nombre,
                ag.codigo_int,
       			ag.tipo_agencia,
                array_to_string(con.formas_pago, '','')::varchar as formas_pago,
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
        where  '||v_parametros.filtro||'
        order by nombre';
        return v_consulta;
		end;
    /*********************************
     #TRANSACCION:  'OBING_SALVIG_CONT'
     #DESCRIPCION:	Listado
     #AUTOR:		IRVA
     #FECHA:		23-11-2018
    ***********************************/

    elsif(p_transaccion='OBING_LISTA_CONT')then

      begin
      	v_consulta = 'with credito as (	select  mo.id_agencia,
		sum(case when mo.ajuste = ''no'' and  mo.garantia = ''no''   then
            	(case when mo.id_moneda = 1 then
                            	mo.monto
                            else
                            	param.f_convertir_moneda(2,1,mo.monto,mo.fecha,''O'',2)

                                end)
        else
            0
        end) as monto_credito,

		 sum(case when  mo.garantia = ''si'' and mo.id_periodo_venta is null then
           	(case when mo.id_moneda = 1 then
                            	mo.monto
                            else
                            	param.f_convertir_moneda(2,1,mo.monto,mo.fecha,''O'',2)

                                end)
        else
            0
        end) as garantia,

        sum(case when mo.ajuste = ''si''  then
            	(case when mo.id_moneda = 1 then
                            	mo.monto
                            else
                            	param.f_convertir_moneda(2,1,mo.monto,mo.fecha,''O'',2)

                                end)
        else
            0
        end) as ajuste_credito
        from obingresos.tmovimiento_entidad mo
		where mo.fecha >= ''8/1/2017'' and mo.fecha <= '''||v_parametros.fecha_fin||''' and
         mo.cierre_periodo = ''no'' and
      	 mo.estado_reg = ''activo'' and
         mo.tipo = ''credito''
		group by mo.id_agencia),

debitos as (select  mo.id_agencia,
sum(case when mo.ajuste = ''no'' then
          	(case when mo.id_moneda = 1 then
                            	mo.monto
                            else
                            	param.f_convertir_moneda(2,1,mo.monto,mo.fecha,''O'',2)

                                end)
        else
            0
        end) as monto_debito,
        sum(case when mo.ajuste = ''si'' then
          	(case when mo.id_moneda = 1 then
                            	mo.monto
                            else
                            	param.f_convertir_moneda(2,1,mo.monto,mo.fecha,''O'',2)

                                end)
        else
            0
        end) as ajuste_debito
from obingresos.tmovimiento_entidad mo
where mo.fecha >= ''8/1/2017'' and mo.fecha <='''||v_parametros.fecha_fin||''' and
      mo.tipo = ''debito'' and
      mo.cierre_periodo = ''no'' and
      mo.estado_reg = ''activo''
group by mo.id_agencia),
contrato as( select max(id_contrato) as ultimo_contrato,
                  id_agencia
                  from leg.tcontrato c
                  where id_agencia is not null and c.estado = ''finalizado''
                  group by id_agencia),
 detalle as  (select  ag.id_agencia,
        		ag.nombre,
                ag.codigo_int,
       			ag.tipo_agencia,
                array_to_string(con.formas_pago, '','')::varchar as formas_pago,
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
        where  '||v_parametros.filtro||')
select count(id_agencia),
 sum(monto_credito) as total_creditos,
 sum(monto_debito)as total_debitos,
 sum(monto_ajustes) as total_ajuste,
 sum(saldo_con_boleto) as total_saldo_con_boleto,
 sum(saldo_sin_boleto) as total_saldo_sin_boleto
from detalle';
        --Devuelve la respuesta
        return v_consulta;

      end;
/*********************************
 	#TRANSACCION:  'OBING_MONE_SEL'
 	#DESCRIPCION:	Tipo Moneda
 	#AUTOR:		Ismael Valdivia
 	#FECHA:		07-01-2018 10:30:00
	***********************************/

	elsif (p_transaccion='OBING_MONE_SEL')then

        begin
		v_consulta = 'Select
                             mo.id_agencia,
                             mo.id_moneda,
                             mone.codigo,
                             mone.moneda,
                             ag.nombre
                    from obingresos.tmovimiento_entidad mo
                    inner join param.tmoneda mone on mone.id_moneda = mo.id_moneda
                    inner join obingresos.tagencia ag on ag.id_agencia = mo.id_agencia
                    where';


       --Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
            v_consulta:=v_consulta||'GROUP BY mo.id_agencia,  mo.id_moneda, mone.codigo, mone.moneda, ag.nombre';
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;
				--Devuelve la respuesta
			return v_consulta;
		end;
    /*********************************
     #TRANSACCION:  'OBING_MONE_CONT'
     #DESCRIPCION:	Reporte saldo vigente
     #AUTOR:		MMV
     #FECHA:		18-11-2018
    ***********************************/
    elsif(p_transaccion='OBING_MONE_CONT')then

		begin
			--Sentencia de la consulta de conteo de registros

			v_consulta:='select  count(mo.id_agencia)
            					 from obingresos.tmovimiento_entidad mo
                                  inner join param.tmoneda mone on mone.id_moneda = mo.id_moneda
                                  inner join obingresos.tagencia ag on ag.id_agencia = mo.id_agencia
                              	 where ';
			v_consulta:=v_consulta||v_parametros.filtro;
            --v_consulta:=v_consulta||'GROUP BY mo.id_agencia,  mo.id_moneda, mone.codigo, mone.moneda, ag.nombre';
            raise notice 'cos -> %',v_consulta;
			--Devuelve la respuest
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
