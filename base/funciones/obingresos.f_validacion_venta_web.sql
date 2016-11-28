CREATE OR REPLACE FUNCTION obingresos.f_validacion_venta_web (
)
  RETURNS void AS
  $body$
DECLARE
  v_res	text;
  v_id_alarma	integer;
BEGIN
  with consulta as (select b.nro_boleto,
       bfp.numero_tarjeta,
       b.fecha_emision
from obingresos.tboleto b
     inner join obingresos.tboleto_forma_pago bfp on bfp.id_boleto = b.id_boleto
     left join obingresos.tdetalle_boletos_web dbw on dbw.billete = b.nro_boleto
where b.fecha_emision >= '01/09/2016'::date and
      b.fecha_emision < now()::date and
      bfp.numero_tarjeta like '%000000005555' and
      bfp.tarjeta = 'VI' and
      dbw.billete is null and
      voided = 'no'
group by b.nro_boleto,
         b.fecha_emision,
         dbw.billete,
         bfp.numero_tarjeta
order by b.fecha_emision
         )
select string_agg(c.nro_boleto || ' , ' || c.numero_tarjeta || ' , ' || to_char(c.fecha_emision,'DD/MM/YYYY'),'<BR>') into v_res
from consulta c;
v_id_alarma = (select param.f_inserta_alarma_dblink (1,'Boletos con # tarjeta ****5555 no reportados',v_res,'jaime.rivera@boa.bo,aldo.zeballos@boa.bo'));


END;
$body$
LANGUAGE 'plpgsql'
VOLATILE
CALLED ON NULL INPUT
SECURITY INVOKER
COST 100;