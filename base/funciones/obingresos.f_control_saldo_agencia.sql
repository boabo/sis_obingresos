CREATE OR REPLACE FUNCTION obingresos.f_control_saldo_agencia (
  p_id_agencia integer
)
RETURNS varchar AS
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
v_nombre_funcion = 'vef.f_incrementar_credito_md';

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
  insert  into tempotal (id_agencia,
                        id_periodo_venta,
                        nombre,
                        mes,
                        fecha_ini,
                        fecha_fin,
                        credito,
                        total_credito,
                        debito,
                        total_debito,
                        saldo)
  WITH  credito as (select  mo.id_agencia,
							mo.id_periodo_venta,
							mo.tipo,
                            sum(mo.monto_total ) as monto_total_credito,
                            sum(mo.monto) as monto
                            from obingresos.tmovimiento_entidad mo
                            inner join obingresos.tperiodo_venta pe on pe.id_periodo_venta = mo.id_periodo_venta
                            where mo.id_agencia = p_id_agencia and mo.tipo = 'credito' and mo.estado_reg = 'activo'
                            and mo.id_periodo_venta = v_id_periodo and mo.garantia = 'no'
                            group by  mo.id_periodo_venta,mo.tipo,mo.id_agencia ),
debito as (select   mo.id_agencia,
					mo.id_periodo_venta,
                    mo.tipo,
                    sum (mo.monto) as monto_debito,
                    sum (mo.monto_total) as monto_total,
                    sum (mo.neto) as neto
                    from obingresos.tmovimiento_entidad mo
                    inner join obingresos.tperiodo_venta pe on pe.id_periodo_venta = mo.id_periodo_venta
                    where mo.id_agencia = p_id_agencia and mo.tipo = 'debito' and mo.estado_reg = 'activo'
                    and mo.id_periodo_venta = v_id_periodo and mo.cierre_periodo = 'no'
                    group by mo.id_periodo_venta,
                    mo.tipo,mo.id_agencia )select 	ag.id_agencia,
                    								pv.id_periodo_venta,
                                                    ag.nombre,
                                                    pv.mes,
                                                    pv.fecha_ini,
                                                    pv.fecha_fin,
                    								cr.tipo as credito,
                                                    cr.monto_total_credito as total_credito,
                                                    de.tipo as debito,
                                                    de.monto_debito as total_debito,
                                                    (cr.monto_total_credito - de.monto_debito ) as saldo
                                                    from obingresos.tagencia ag
                                                    inner join credito cr on cr.id_agencia = ag.id_agencia
                                                    inner join obingresos.tperiodo_venta pv on pv.id_periodo_venta = cr.id_periodo_venta
                                                    inner join debito de on de.id_agencia = ag.id_agencia
                                                    where ag.id_agencia = p_id_agencia;



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



  RETURN v_consula;

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