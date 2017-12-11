CREATE OR REPLACE FUNCTION obingresos.f_relacionar_periodo_deposito (
  p_id_usuario integer,
  p_id_deposito integer,
  p_monto_deposito numeric,
  p_id_moneda_deposito integer,
  p_fecha date,
  p_nro_deposito varchar,
  p_id_agencia integer
)
RETURNS numeric AS
$body$
/**************************************************************************
 FUNCION: 		obingresos.f_relacionar_periodo_deposito
 DESCRIPCION:   Relaciona un deposito con los periodos cerrados de ventas si corresponde
 AUTOR: 	    JRR
 FECHA:			06/09/2013
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
   

BEGIN
    v_nombre_funcion:='obingresos.f_relacionar_periodo_deposito';
    
    if (exists (select 1 
                from obingresos.tperiodo_venta_agencia pva
                where pva.estado_reg = 'activo' and pva.id_agencia = p_id_agencia and pva.estado = 'abierto' and
                (pva.monto_mb <= 0 or pva.monto_usd <= 0)) and p_monto_deposito > 0) then

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
        
        if (v_periodo_venta.moneda_restrictiva = 'si' ) then
            	
            	--si el deposito es en la moneda base
                if (p_id_moneda_deposito = v_id_moneda_base) then
                	             	
                    --actualizamos el periodo de la agencia
                    update obingresos.tperiodo_venta_agencia
                    set monto_mb = (case when p_monto_deposito >= (monto_mb*-1) then 0 else monto_mb + p_monto_deposito END),
                    monto_credito_mb = monto_credito_mb + (case when p_monto_deposito >= (monto_mb*-1) then (monto_mb*-1) else p_monto_deposito END)
                    where id_periodo_venta_agencia = v_periodo_venta.id_periodo_venta_agencia;
                	
                    v_monto_pagar =  (case when (p_monto_deposito >= (v_periodo_venta.monto_mb*-1)) then
                                          (v_periodo_venta.monto_mb*-1)
                                      ELSE
                                          p_monto_deposito
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
                        monto_total
                      )
                      VALUES (
                        p_id_usuario,
                        'credito',
                        NULL,
                        p_fecha,
                        NULL,
                        v_monto_pagar,
                        p_id_moneda_deposito,
                        p_nro_deposito,
                        'no',
                        'no',
                        v_periodo_venta.id_periodo_venta,
                        p_id_agencia,
                        v_monto_pagar
                      );
                else --en dolares
                	--actualizamos el periodo de la agencia
                    update obingresos.tperiodo_venta_agencia
                    set monto_usd = (case when p_monto_deposito >= (monto_usd*-1) then 0 else monto_usd + p_monto_deposito END),
                    monto_credito_usd = monto_credito_usd + (case when p_monto_deposito >= (monto_usd*-1) then (monto_usd*-1) else p_monto_deposito END) 
                    where id_periodo_venta_agencia = v_periodo_venta.id_periodo_venta_agencia;
                    
                	 v_monto_pagar =  (case when (p_monto_deposito >= (v_periodo_venta.monto_usd*-1)) then
                                          (v_periodo_venta.monto_usd*-1)
                                      ELSE
                                          p_monto_deposito
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
                        monto_total
                      )
                      VALUES (
                        p_id_usuario, 
                        'credito',
                        NULL,
                        p_fecha,
                        NULL,
                        v_monto_pagar,
                        p_id_moneda_deposito,
                        p_nro_deposito,
                        'no',
                        'no',
                        v_periodo_venta.id_periodo_venta,
                        p_id_agencia,
                        v_monto_pagar
                      );
                
                end if;
                
            else --si la moneda no es restrictiva
            	--actualizamos el periodo de la agencia
                	v_monto_mb = (case when p_id_moneda_deposito = v_id_moneda_base then
                    					p_monto_deposito
                    				else
                                    	param.f_convertir_moneda(v_id_moneda_usd,v_id_moneda_base,p_monto_deposito,p_fecha,'O',2)
                                    end);  
                    update obingresos.tperiodo_venta_agencia
                    set monto_mb = (case when v_monto_mb >= (monto_mb*-1) then 0 else monto_mb + v_monto_mb end),
                    monto_credito_mb = monto_credito_mb + (case when v_monto_mb >= (monto_mb*-1) then (monto_mb*-1) else v_monto_mb END)
                    where id_periodo_venta_agencia = v_periodo_venta.id_periodo_venta_agencia;
                	
                    if (p_id_moneda_deposito = v_id_moneda_base) then
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
                        monto_total
                      )
                      VALUES (
                        p_id_usuario,
                        'credito',
                        NULL,
                        p_fecha,
                        NULL,
                        v_monto_pagar,                       
                        p_id_moneda_deposito,
                        p_nro_deposito,
                        'no',
                        'no',
                        v_periodo_venta.id_periodo_venta,
                        p_id_agencia,
                        v_monto_pagar
                      );                    
            	             
            end if;
            
            --modificar el estado del periodo de venta si los monto_mb y monto_usd son 0 o null
            update obingresos.tperiodo_venta_agencia
            set estado = 'cerrado'
            where  id_periodo_venta_agencia = v_periodo_venta.id_periodo_venta_agencia and
            (monto_mb = 0 or monto_mb is null) and (monto_usd = 0 or monto_usd is null);
            
            if (p_monto_deposito - v_monto_pagar < 0) then
            	raise exception 'El monto a pagar es mayor que el monto del deposito';
            end if;
            
            --llamada recursiva con la diferencia entre p_monto_deposito y v_monto_pagar
            return obingresos.f_relacionar_periodo_deposito(p_id_usuario,p_id_deposito,p_monto_deposito - v_monto_pagar,p_id_moneda_deposito,p_fecha,p_nro_deposito,p_id_agencia);
        
    else
        return p_monto_deposito;
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