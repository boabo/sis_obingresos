CREATE OR REPLACE FUNCTION obingresos.f_verificar_saldo_agencia (
  p_id_agencia integer,
  p_monto numeric,
  p_moneda varchar,
  p_id_usuario integer,
  p_pnr varchar = NULL::character varying,
  p_apellido varchar = NULL::character varying,
  p_insertar varchar = 'si'::character varying,
  p_monto_total numeric = NULL::numeric,
  p_fecha date = now()::date,
  out po_autorizacion varchar,
  out po_saldo numeric
)
RETURNS record AS
$body$
/**************************************************************************
 FUNCION: 		obingresos.f_verificar_saldo_agencia
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

    v_terciariza				varchar;
    v_monto						numeric;
    /*Auementando variables Ismael Valdivia (11/08/2021)*/
    v_tipo_agencia				varchar;
    v_validar_boleta			varchar;
    v_tipo_pago					varchar;

BEGIN
    v_nombre_funcion:='obingresos.f_verificar_saldo_agencia';
    po_saldo = obingresos.f_get_saldo_agencia(p_id_agencia,p_moneda);

    select coalesce (a.terciariza,'no'),
    		a.tipo_agencia,
            a.validar_boleta,
            a.tipo_pago
     into v_terciariza,
     	  v_tipo_agencia,
          v_validar_boleta,
          v_tipo_pago
    from obingresos.tagencia a
    where a.id_agencia = p_id_agencia;

    if (v_terciariza = 'no') then
    	v_monto = p_monto;
    else
    	v_monto = p_monto_total;
    end if;

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

    if (exists (select 1 from obingresos.tmovimiento_entidad
    			where pnr = p_pnr and fecha = p_fecha and estado_reg = 'activo' )) then
    	--raise exception 'El pnr ya ha sido autorizado';


        select mov.autorizacion__nro_deposito
        into po_autorizacion
        from obingresos.tmovimiento_entidad mov
    	where pnr = p_pnr and fecha = p_fecha and estado_reg = 'activo';

	else

          v_id_moneda_base = (select param.f_get_moneda_base());

          /*Auemntando condicion para omitir control de saldo (Ismael Valdivia 11/08/2021)*/
          if (v_tipo_agencia = 'corporativa' and v_tipo_pago = 'postpago' and v_validar_boleta = 'no') THEN

          	if (p_insertar = 'si')then

                  INSERT INTO
                      obingresos.tmovimiento_entidad
                    (
                      id_usuario_reg,
                      id_usuario_ai,
                      usuario_ai,
                      tipo,
                      pnr,
                      fecha,
                      apellido,
                      monto,
                      id_moneda,
                      autorizacion__nro_deposito,
                      garantia,
                      ajuste,
                      id_periodo_venta,
                      id_agencia,
                      monto_total,
                      comision_terciarizada
                    )
                    VALUES (
                      p_id_usuario,
                      NULL,
                      NULL,
                      'debito',
                      p_pnr,
                      p_fecha,
                      p_apellido,
                      v_monto,
                      v_id_moneda,
                      po_autorizacion,
                      'no',
                      'no',
                      NULL,
                      p_id_agencia,
                      p_monto_total,
                      (case when v_terciariza = 'si' then p_monto_total - p_monto else NULL END)
                    );
                  end if;

          else

              if (po_saldo >= v_monto ) then
                po_autorizacion = uuid_generate_v4()::varchar;
                po_saldo = po_saldo - v_monto;
                if (p_insertar = 'si')then

                    INSERT INTO
                        obingresos.tmovimiento_entidad
                      (
                        id_usuario_reg,
                        id_usuario_ai,
                        usuario_ai,
                        tipo,
                        pnr,
                        fecha,
                        apellido,
                        monto,
                        id_moneda,
                        autorizacion__nro_deposito,
                        garantia,
                        ajuste,
                        id_periodo_venta,
                        id_agencia,
                        monto_total,
                        comision_terciarizada
                      )
                      VALUES (
                        p_id_usuario,
                        NULL,
                        NULL,
                        'debito',
                        p_pnr,
                        p_fecha,
                        p_apellido,
                        v_monto,
                        v_id_moneda,
                        po_autorizacion,
                        'no',
                        'no',
                        NULL,
                        p_id_agencia,
                        p_monto_total,
                        (case when v_terciariza = 'si' then p_monto_total - p_monto else NULL END)
                      );
                    end if;
            else
                raise exception 'La agencia no tiene saldo suficiente para emitir el boleto. El saldo es de % %',po_saldo,v_moneda;
            end if;

          end if;





   end if;

	return;

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

ALTER FUNCTION obingresos.f_verificar_saldo_agencia (p_id_agencia integer, p_monto numeric, p_moneda varchar, p_id_usuario integer, p_pnr varchar, p_apellido varchar, p_insertar varchar, p_monto_total numeric, p_fecha date, out po_autorizacion varchar, out po_saldo numeric)
  OWNER TO postgres;
