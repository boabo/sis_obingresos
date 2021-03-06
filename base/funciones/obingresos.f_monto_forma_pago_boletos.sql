CREATE OR REPLACE FUNCTION obingresos.f_monto_forma_pago_boletos (
  p_moneda varchar,
  p_id_usuario_cajero integer,
  p_fecha date
)
RETURNS numeric AS
$body$
DECLARE
  v_monto_total		numeric;
BEGIN
   	select coalesce(sum(bfp.importe),0) into v_monto_total
 	from obingresos.tboleto_amadeus bol
 	inner join obingresos.tboleto_amadeus_forma_pago bfp on bfp.id_boleto_amadeus=bol.id_boleto_amadeus
 	inner join obingresos.tforma_pago fp on fp.id_forma_pago=bfp.id_forma_pago
 	inner join param.tmoneda mon on mon.id_moneda=fp.id_moneda
 	where bol.id_usuario_cajero = p_id_usuario_cajero
    and bol.estado='revisado' and bol.voided='no'
 	and bol.fecha_emision=p_fecha
 	and mon.codigo_internacional=p_moneda;

    return v_monto_total;

END;
$body$
LANGUAGE 'plpgsql'
VOLATILE
CALLED ON NULL INPUT
SECURITY INVOKER
COST 100;