CREATE OR REPLACE FUNCTION obingresos.f_get_saldo_agencia (
  p_id_agencia integer,
  p_moneda varchar
)
RETURNS numeric AS
$body$
/**************************************************************************
 FUNCION: 		obingresos.f_get_saldo_agencia
 DESCRIPCION:   Verifica saldo y si tiene  saldo lo toma
 AUTOR: 	    RCM
 FECHA:			06/09/2013
 COMENTARIOS:
***************************************************************************
 HISTORIA DE MODIFICACIONES:

 DESCRIPCION:
 AUTOR:
 FECHA:
 ***************************************************************************/
DECLARE

    v_resp                      varchar;
    v_nombre_funcion            text;
    v_mensaje_error             text;
    v_boleto           			record;
    v_id_moneda_base            integer;
    v_id_moneda_usd				integer;
    v_id_moneda					integer;
    v_tipo_cambio				numeric;
    v_acumulado_moneda_boleto	numeric;
    v_registros					record;
    v_suma_movimientos			numeric;
    v_suma_periodos_ant			numeric;
    v_moneda					varchar;
    v_saldo						numeric;
    v_codigo_auto				varchar;
    v_id_agencia				integer;

BEGIN
    v_nombre_funcion:='obingresos.f_get_saldo_agencia';
    if (pxp.f_is_positive_integer(p_moneda)) then
    	v_id_moneda = p_moneda::integer;

        select m.codigo_internacional into v_moneda
        from param.tmoneda m
        where m.id_moneda = v_id_moneda;
    else
    	select m.id_moneda into v_id_moneda
        from param.tmoneda m
        where codigo_internacional = p_moneda;

        v_moneda = p_moneda;
    end if;

    v_id_moneda_base = (select param.f_get_moneda_base());

    select m.id_moneda into v_id_moneda_usd
    from param.tmoneda m
    where m.codigo_internacional = 'USD';

    --sumar los periodos de la agencia en estado abierto
    select sum(coalesce (pva.monto_mb,0) +
                param.f_convertir_moneda(v_id_moneda_usd,v_id_moneda_base,COALESCE(pva.monto_usd,0),now()::date,'O',2)) into v_suma_periodos_ant
    from obingresos.tperiodo_venta_agencia pva
    where pva.id_agencia = p_id_agencia and pva.estado= 'abierto';
    
	select id_agencia into v_id_agencia
    from obingresos.tmovimiento_entidad me
    where me.id_periodo_venta is null and me.id_agencia = p_id_agencia and me.estado_reg = 'activo'
    for share;

    --sumar todos los movimientos debito en - y credito en +
    select sum(case when me.id_moneda = v_id_moneda_base then
                                (case when me.tipo = 'credito' then
                                    me.monto
                                else
                                    me.monto * -1
                                end)
                            else
                                (case when me.tipo = 'credito' then
                                    param.f_convertir_moneda(v_id_moneda_usd,v_id_moneda_base,me.monto,me.fecha,'O',2)
                                else
                                    param.f_convertir_moneda(v_id_moneda_usd,v_id_moneda_base,me.monto,me.fecha,'O',2) *-1
                                end)
                            END) into v_suma_movimientos
    from obingresos.tmovimiento_entidad me
    where me.id_periodo_venta is null and me.id_agencia = p_id_agencia and me.estado_reg = 'activo';

    --saldo en mb
    v_saldo = coalesce(v_suma_movimientos,0) + coalesce(v_suma_periodos_ant,0);

    if ( v_moneda = 'USD') then
        v_saldo = param.f_convertir_moneda(v_id_moneda_base ,v_id_moneda_usd,v_saldo,now()::date,'O',2);
    end if;

    return v_saldo;

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