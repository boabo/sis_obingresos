CREATE OR REPLACE FUNCTION obingresos.f_dividir_void_credito_pagar_deuda (
  p_id_usuario integer,
  p_monto numeric,
  p_fecha date,
  p_codigo varchar,
  p_id_moneda integer,
  p_id_agencia integer,
  p_pnr varchar,
  p_billete varchar,
  p_id_void integer,
  p_tipo_void varchar,
  p_monto_comision numeric,
  p_id_movimiento integer
)
RETURNS numeric AS
$body$
/**************************************************************************
 FUNCION: 		obingresos.f_dividir_void_credito_pagar_deuda
 DESCRIPCION:   Relaciona un deposito con los periodos cerrados de ventas si corresponde
 AUTOR: 	    IRVA
 FECHA:			15/11/2019
 COMENTARIOS:
***************************************************************************
 HISTORIA DE MODIFICACIONES:

 DESCRIPCION:
 AUTOR:
 FECHA:
 ***************************************************************************/
DECLARE
	v_nombre_funcion		varchar;
    v_periodo_venta			record;
    v_id_moneda_base		integer;
    v_id_moneda_usd			integer;
    v_monto_pagar			numeric;
    v_monto_mb				numeric;
    v_resp					varchar;

    v_codigo				varchar;

    v_id_movimiento_entidad_dividido integer;
    v_id_movimiento_entidad integer;


BEGIN
    v_nombre_funcion:='obingresos.f_dividir_void_credito_pagar_deuda';

    if (exists (select 1
                from obingresos.tperiodo_venta_agencia pva
                where pva.estado_reg = 'activo' and pva.id_agencia = p_id_agencia and pva.estado = 'abierto' and
                (pva.monto_mb <= 0 or pva.monto_usd <= 0)) and p_monto > 0) then

        select per.fecha_ini,per.fecha_fin,pva.*  into v_periodo_venta
        from obingresos.tperiodo_venta_agencia pva
        inner join obingresos.tperiodo_venta per on pva.id_periodo_venta = per.id_periodo_venta
        where pva.estado_reg = 'activo' and pva.id_agencia = p_id_agencia and pva.estado = 'abierto' and
        (pva.monto_mb <= 0 or pva.monto_usd <= 0)
        order by per.fecha_ini asc
        limit 1 offset 0;

        v_id_moneda_base = (select param.f_get_moneda_base());
        select m.id_moneda into v_id_moneda_usd
        from param.tmoneda m
        where m.codigo_internacional = 'USD';

        /*Aumentamos el codigo para que muestre el total*/
        v_codigo = p_codigo;

        if (v_periodo_venta.moneda_restrictiva = 'si' ) then

            	--si el deposito es en la moneda base
                if (p_id_moneda = v_id_moneda_base) then

                    --actualizamos el periodo de la agencia
                    update obingresos.tperiodo_venta_agencia
                    set monto_mb = (case when p_monto >= (monto_mb*-1) then 0 else monto_mb + p_monto END),
                    monto_credito_mb = monto_credito_mb + (case when p_monto >= (monto_mb*-1) then (monto_mb*-1) else p_monto END)
                    where id_periodo_venta_agencia = v_periodo_venta.id_periodo_venta_agencia;

                    v_monto_pagar =  (case when (p_monto >= (v_periodo_venta.monto_mb*-1)) then
                                          (v_periodo_venta.monto_mb*-1)
                                      ELSE
                                          p_monto
                                      end);

                    --insertamos el movimiento en el periodo actual
                    INSERT INTO
                        obingresos.tmovimiento_entidad
                      (
                        id_usuario_reg,
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
                        billete,
                        id_void,
                        tipo_void
                      )
                      VALUES (
                        p_id_usuario,
                        'credito',
                        p_pnr,
                        p_fecha,
                        NULL,
                        v_monto_pagar,
                        p_id_moneda,
                        v_codigo,
                        'no',
                        'no',
                        v_periodo_venta.id_periodo_venta,
                        p_id_agencia,
                        v_monto_pagar,
                        p_billete,
                        p_id_void,
                        p_tipo_void
                      )RETURNING id_movimiento_entidad into v_id_movimiento_entidad_dividido;
                else --en dolares
                	--actualizamos el periodo de la agencia
                    update obingresos.tperiodo_venta_agencia
                    set monto_usd = (case when p_monto >= (monto_usd*-1) then 0 else monto_usd + p_monto END),
                    monto_credito_usd = monto_credito_usd + (case when p_monto >= (monto_usd*-1) then (monto_usd*-1) else p_monto END)
                    where id_periodo_venta_agencia = v_periodo_venta.id_periodo_venta_agencia;

                	 v_monto_pagar =  (case when (p_monto >= (v_periodo_venta.monto_usd*-1)) then
                                          (v_periodo_venta.monto_usd*-1)
                                      ELSE
                                          p_monto
                                      end);

                    --insertamos el movimiento en el periodo actual
                    INSERT INTO
                        obingresos.tmovimiento_entidad
                      (
                        id_usuario_reg,
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
                        billete,
                        id_void,
                        tipo_void
                      )
                      VALUES (
                        p_id_usuario,
                        'credito',
                        p_pnr,
                        p_fecha,
                        NULL,
                        v_monto_pagar,
                        p_id_moneda,
                        v_codigo,
                        'no',
                        'no',
                        v_periodo_venta.id_periodo_venta,
                        p_id_agencia,
                        v_monto_pagar,
                        p_billete,
                        p_id_void,
                        p_tipo_void
                      )RETURNING id_movimiento_entidad into v_id_movimiento_entidad_dividido;

                end if;

            else --si la moneda no es restrictiva
            	--actualizamos el periodo de la agencia
                	v_monto_mb = (case when p_id_moneda = v_id_moneda_base then
                    					p_monto
                    				else
                                    	param.f_convertir_moneda(v_id_moneda_usd,v_id_moneda_base,p_monto,p_fecha,'O',2)
                                    end);
                    update obingresos.tperiodo_venta_agencia
                    set monto_mb = (case when v_monto_mb >= (monto_mb*-1) then 0 else monto_mb + v_monto_mb end),
                    monto_credito_mb = monto_credito_mb + (case when v_monto_mb >= (monto_mb*-1) then (monto_mb*-1) else v_monto_mb END)
                    where id_periodo_venta_agencia = v_periodo_venta.id_periodo_venta_agencia;

                    if (p_id_moneda = v_id_moneda_base) then
                        v_monto_pagar =  (case when (v_monto_mb >= (v_periodo_venta.monto_mb*-1)) then
                                              (v_periodo_venta.monto_mb*-1)
                                          ELSE
                                              v_monto_mb
                                          end);
                    else
                    	v_monto_pagar =  (case when (v_monto_mb >= (v_periodo_venta.monto_mb*-1)) then
                                              param.f_convertir_moneda(v_id_moneda_base,v_id_moneda_usd,(v_periodo_venta.monto_mb*-1),p_fecha,'O',2)
                                          ELSE
                                              param.f_convertir_moneda(v_id_moneda_base,v_id_moneda_usd,v_monto_mb,p_fecha,'O',2)
                                          end);

                    end if;

                    INSERT INTO
                        obingresos.tmovimiento_entidad
                      (
                        id_usuario_reg,

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
                        billete,
                        id_void,
                        tipo_void
                      )
                      VALUES (
                        p_id_usuario,
                        'credito',
                        p_pnr,
                        p_fecha,
                        NULL,
                        v_monto_pagar,
                        p_id_moneda,
                        v_codigo,
                        'no',
                        'no',
                        v_periodo_venta.id_periodo_venta,
                        p_id_agencia,
                        v_monto_pagar,
                        p_billete,
                        p_id_void,
                        p_tipo_void
                      )RETURNING id_movimiento_entidad into v_id_movimiento_entidad_dividido;

            end if;

            --modificar el estado del periodo de venta si los monto_mb y monto_usd son 0 o null
            update obingresos.tperiodo_venta_agencia
            set estado = 'cerrado'
            where  id_periodo_venta_agencia = v_periodo_venta.id_periodo_venta_agencia and
            (monto_mb = 0 or monto_mb is null) and (monto_usd = 0 or monto_usd is null);

            if (p_monto - v_monto_pagar < 0) then
            	raise exception 'El monto a pagar es mayor que el monto del deposito';
            end if;
            --llamada recursiva con la diferencia entre p_monto y v_monto_pagar
            return obingresos.f_dividir_void_credito_pagar_deuda(p_id_usuario,p_monto - v_monto_pagar,p_fecha,v_codigo,p_id_moneda,p_id_agencia,p_pnr,p_billete,p_id_void,p_tipo_void,p_monto_comision,v_id_movimiento_entidad_dividido);

    else

    	if (p_monto > 0) then

              --Sentencia de la insercion
                  insert into obingresos.tmovimiento_entidad(
                  id_usuario_reg,
                  fecha_reg,
                  estado_reg,
                  tipo,
                  pnr,
                  fecha,
                  monto,
                  id_moneda,
                  autorizacion__nro_deposito,
                  garantia,
                  ajuste,
                  id_agencia,
                  monto_total,
                  billete,
                  id_void,
                  tipo_void
                  ) values(
                  p_id_usuario,
                  now(),
                  'activo',
                  'credito',
                  p_pnr::varchar,
                  now(),
                  p_monto,--v_parametros.monto::numeric,
                  p_id_moneda::integer,
                  p_codigo::varchar,
                  'no',
                  'no',
                  p_id_agencia::integer,
                  p_monto,--v_parametros.monto::numeric,
                  p_billete::varchar,
                  p_id_void,
                  p_tipo_void
                  )RETURNING id_movimiento_entidad into v_id_movimiento_entidad;


                  /*Actualizamos el Fk_id_movimiento_entidad*/
                  update obingresos.tmovimiento_entidad set
                  fk_id_movimiento_entidad = v_id_movimiento_entidad
                  where id_movimiento_entidad = p_id_movimiento;
                  /******************************************/


              end if;

              /*Insertamos la comision siempre y cuando sea distinto de 0*/

              if (p_monto_comision <> 0) then

              v_codigo = ('VOID:'||p_pnr||'->'||p_billete||'-Comisión')::varchar;
              --raise exception 'llega aqui el dato v_id_movimiento_entidad %, v_id_movimiento_entidad_dividido %',v_id_movimiento_entidad,p_id_movimiento;
                      --Sentencia de la insercion
                      insert into obingresos.tmovimiento_entidad(
                      id_usuario_reg,
                      fecha_reg,
                      estado_reg,
                      tipo,
                      pnr,
                      fecha,
                      monto,
                      id_moneda,
                      autorizacion__nro_deposito,
                      garantia,
                      ajuste,
                      id_agencia,
                      monto_total,
                      billete,
                      id_void,
                      tipo_void,
                      fk_id_movimiento_entidad
                      ) values(
                      p_id_usuario,
                      now(),
                      'activo',
                      'debito',
                      p_pnr::varchar,
                      now(),
                      p_monto_comision::numeric,
                      p_id_moneda::integer,
                      v_codigo::varchar,
                      'no',
                      'no',
                      p_id_agencia::integer,
                      p_monto_comision::numeric,
                      p_billete::varchar,
                      p_id_void,
                      p_tipo_void,
                      COALESCE(v_id_movimiento_entidad,p_id_movimiento)
                      );
              end if;
              /********************************************************************************/


        return COALESCE(v_id_movimiento_entidad,p_id_movimiento);
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

ALTER FUNCTION obingresos.f_dividir_void_credito_pagar_deuda (p_id_usuario integer, p_monto numeric, p_fecha date, p_codigo varchar, p_id_moneda integer, p_id_agencia integer, p_pnr varchar, p_billete varchar, p_id_void integer, p_tipo_void varchar, p_monto_comision numeric, p_id_movimiento integer)
  OWNER TO postgres;
