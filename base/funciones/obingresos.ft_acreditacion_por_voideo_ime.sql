CREATE OR REPLACE FUNCTION obingresos.ft_acreditacion_por_voideo_ime (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Ingresos
 FUNCION: 		obingresos.ft_acreditacion_por_voideo_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'obingresos.tforma_pago'
 AUTOR: 		 (ivaldivia)
 FECHA:	        08-03-2019 16:05:00
 COMENTARIOS:
***************************************************************************
 HISTORIAL DE MODIFICACIONES:

 DESCRIPCION:
 AUTOR:
 FECHA:
***************************************************************************/

DECLARE

	v_nro_requerimiento    	integer;
	v_parametros           	record;
	v_id_requerimiento     	integer;
	v_resp		            varchar;
	v_nombre_funcion        text;
	v_mensaje_error         text;
	v_id_movimiento_entidad	integer;
    v_existe				record;
    v_nombre_agencia		varchar;
    v_id_moneda				integer;

BEGIN

    v_nombre_funcion = 'obingresos.ft_acreditacion_por_voideo_ime';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'OBING_ACREVOID_INS'
 	#DESCRIPCION:	Insercion de registros
 	#AUTOR:		ivaldivia
 	#FECHA:		08-03-2019 16:05:00
	***********************************/

	if(p_transaccion='OBING_ACREVOID_INS')then

        begin

        select count (mo.id_movimiento_entidad) as existe,
        mo.id_movimiento_entidad
        into v_existe
        from obingresos.tmovimiento_entidad mo
        where mo.apellido=v_parametros.id_boleto::varchar and mo.id_agencia=v_parametros.id_agencia::integer
        group by mo.id_movimiento_entidad;

        select ag.nombre
        into v_nombre_agencia
        from obingresos.tagencia ag
        where ag.id_agencia = v_parametros.id_agencia::integer;

        select mone.id_moneda
        into v_id_moneda
        from param.tmoneda mone
        where mone.codigo_internacional = v_parametros.codigo_moneda;


       --raise exception 'EL DATO ES %',v_parametros.fecha;


        if (v_parametros.pnr::varchar = '') THEN
        	raise exception 'El valor del pnr no puede ser vacio.';
        /*ELSIF(v_parametros.fecha::date is NULL) then
        	raise exception 'El valor de la fecha no puede ser vacio.';*/
        ELSIF(v_parametros.monto::numeric is NULL) then
        	raise exception 'El valor del monto no puede ser vacio.';
        ELSIF(v_parametros.codigo_moneda::varchar = '') then
        	raise exception 'El valor de la moneda no puede ser vacio.';
        ELSIF(v_parametros.autorizacion__nro_deposito::varchar = '') then
        	raise exception 'El valor del NroDeposito no puede ser vacio.';
        ELSIF(v_parametros.id_agencia::integer is NULL) then
        	raise exception 'El valor de la Agencia no puede ser vacio.';
        /*ELSIF(v_parametros.id_boleto::varchar = '') then
        	raise exception 'El valor id_boleto no puede ser vacio.';    */
        end if;

        if(v_existe.existe<>0)then
        	 raise exception 'La Acreditacion con ID_BOLETO=% ya existe registrado para la Agencia % con el ID_MOVIMIENTO: %',v_parametros.id_boleto,v_nombre_agencia,v_existe.id_movimiento_entidad::integer;
        else
        	--Sentencia de la insercion
        	insert into obingresos.tmovimiento_entidad(
			id_usuario_reg,
            fecha_reg,
            estado_reg,
            tipo,
            pnr,
            fecha,
            apellido,
            monto,
            id_moneda,
            autorizacion__nro_deposito,
            garantia,
            ajuste,
            id_agencia,
            monto_total,
            billete
          	) values(
			366,
			now(),
			'activo',
            'credito',
			v_parametros.pnr::varchar,
			now(),
            v_parametros.id_boleto::varchar,
			v_parametros.monto::numeric,
			v_id_moneda::integer,
			v_parametros.autorizacion__nro_deposito::varchar,
            'no',
            'no',
			v_parametros.id_agencia::integer,
			v_parametros.monto::numeric,
            v_parametros.billete::varchar

            )RETURNING id_movimiento_entidad into v_id_movimiento_entidad;

			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Acreditacion almacenado(a) con exito (id_movimiento_entidad'||v_id_movimiento_entidad||')');
            v_resp = pxp.f_agrega_clave(v_resp,'id_movimiento_entidad',v_id_movimiento_entidad::varchar);

            --Devuelve la respuesta
            return v_resp;
        end if;

		end;

    --Definicion de la respuesta
    v_resp = pxp.f_agrega_clave(v_resp,'mensaje','La Acreditacion se registro exitosamente');
    v_resp = pxp.f_agrega_clave(v_resp,'tipo_mensaje','exito');
    v_resp = pxp.f_agrega_clave(v_resp,'id_movimiento_entidad',v_id_movimiento_entidad::varchar);
    --Devuelve la respuesta
    return v_resp;

     else

    	raise exception 'Transaccion inexistente: %',p_transaccion;

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
