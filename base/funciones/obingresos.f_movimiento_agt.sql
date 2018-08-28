CREATE OR REPLACE FUNCTION obingresos.f_movimiento_agt (
  p_id_agencia integer
)
RETURNS void AS
$body$
DECLARE
 v_nombre_funcion   	 text;
 v_resp    				 varchar;
 v_mensaje 			 	 varchar;
 v_ids_peridos			 integer[];
 v_id_periodo 			 integer;
 v_peridos			 varchar;
 v_monto				 numeric;
 v_consula 				 varchar;


BEGIN

select pxp.list( pe.id_periodo_venta::VARCHAR)
into
v_peridos
from obingresos.tperiodo_venta pe
where pe.id_gestion = 16;

  v_ids_peridos	 = string_to_array(v_peridos,',');

 CREATE TEMPORARY TABLE tempotal (	  id_agencia integer,
                                      id_periodo_venta integer,
                                      nombre varchar,
                                      mes varchar,
                                      fecha_ini date,
                                      fecha_fin date,
                                      credito varchar,
                                      total_credito numeric,
                                      debito varchar,
                                      total_debito numeric,
                                      saldo numeric
                                      )ON COMMIT DROP;

  FOREACH v_id_periodo IN ARRAY v_ids_peridos
  LOOP
  insert  into mat.tmovimiento (
   fecha_ini,
  fecha_fin,
  mes,
  fecha,
  autorizacion__nro_deposito,
  id_periodo_venta,
  monto_total,
  neto,
  monto,
  cierre_periodo,
  ajuste,
  tipo,
  transaccion )

   select pe.fecha_ini,
      pe.fecha_fin,
      pe.mes,
      mo.fecha,
      mo.autorizacion__nro_deposito,
      mo.id_periodo_venta,
      mo.monto_total,
      mo.neto,
      mo.monto,
      mo.cierre_periodo,
      mo.ajuste,
      mo.tipo,
      'A'::varchar as transaccion
      from obingresos.tmovimiento_entidad mo
      inner join obingresos.tperiodo_venta pe on pe.id_periodo_venta = mo.id_periodo_venta
      where mo.id_agencia = p_id_agencia and mo.tipo = 'credito' and mo.estado_reg = 'activo'
      and mo.id_periodo_venta = v_id_periodo and mo.garantia = 'no'
UNION
select  pe.fecha_ini,
		pe.fecha_fin,
     	pe.mes,
        null as fecha,
        null as autorizacion__nro_deposito,
        mo.id_periodo_venta,
   	    sum(mo.monto_total ) as monto_total,
        sum(mo.neto) as neto,
        sum(mo.monto) as monto,
        null as cierre_periodo,
        null as ajuste,
        mo.tipo,
        'B'::varchar as transaccion
        from obingresos.tmovimiento_entidad mo
        inner join obingresos.tperiodo_venta pe on pe.id_periodo_venta = mo.id_periodo_venta
        where mo.id_agencia = p_id_agencia and mo.tipo = 'credito' and mo.estado_reg = 'activo'
        and mo.id_periodo_venta = v_id_periodo and mo.garantia = 'no'
        group by pe.fecha_ini,
                  pe.fecha_fin,
                  pe.mes,
                  mo.id_periodo_venta,
                  mo.tipo
         order by transaccion;

        insert  into mat.tmovimiento (
   fecha_ini,
  fecha_fin,
  mes,
  fecha,
  autorizacion__nro_deposito,
  id_periodo_venta,
  monto_total,
  neto,
  monto,
  cierre_periodo,
  ajuste,
  tipo,
  transaccion )select  pe.fecha_ini,
  		pe.fecha_fin,
        pe.mes,
        null::date as fecha,
        mo.pnr,
        mo.id_periodo_venta,
        mo.monto,
        mo.neto,
        mo.monto_total,
        mo.cierre_periodo,
        mo.ajuste,
        mo.tipo,
        'A'::varchar as transaccion
        from obingresos.tmovimiento_entidad mo
        inner join obingresos.tperiodo_venta pe on pe.id_periodo_venta = mo.id_periodo_venta
        where mo.id_agencia = p_id_agencia and mo.tipo = 'debito' and mo.estado_reg = 'activo'
        and mo.id_periodo_venta = v_id_periodo and mo.cierre_periodo = 'no'

union
select  pe.fecha_ini,
  		pe.fecha_fin,
        pe.mes,
       null::date as fecha,
        null::varchar as pnr,
        mo.id_periodo_venta,
        sum (mo.monto) as monto,
        sum (mo.neto) as neto,
        sum (mo.monto_total) as monto_total,
        null::varchar as cierre_periodo,
        null::varchar as ajuste,
        mo.tipo,
        'B'::varchar as transaccion
        from obingresos.tmovimiento_entidad mo
        inner join obingresos.tperiodo_venta pe on pe.id_periodo_venta = mo.id_periodo_venta
        where mo.id_agencia = p_id_agencia and mo.tipo = 'debito' and mo.estado_reg = 'activo'
        and mo.id_periodo_venta = v_id_periodo and mo.cierre_periodo = 'no'
        group by
        pe.fecha_ini,
        pe.fecha_fin,
        pe.mes,
        mo.id_periodo_venta,
        mo.tipo
        order by transaccion;



  END LOOP;


  v_consula = 'select   id_agencia,
                        id_periodo_venta,
                        nombre,
                        mes,
                        fecha_ini,
                        fecha_fin,
                        credito,
                        total_credito,
                        debito,
                        total_debito,
                        saldo
                        from tempotal
                        order by id_periodo_venta';





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