CREATE OR REPLACE FUNCTION obingresos.f_get_validez_tarifaria (
  p_id_boleto_vuelo integer
)
RETURNS integer AS
$body$
/**************************************************************************
 FUNCION: 		obingresos.f_get_validez_tarifaria
 DESCRIPCION:   devuelve la validez tarifaria por un vuelo de un billete
 AUTOR: 	    JRR
 FECHA:			07/04/2017
 COMENTARIOS:
***************************************************************************
 HISTORIA DE MODIFICACIONES:

 DESCRIPCION:
 AUTOR:
 FECHA:
 ***************************************************************************/
DECLARE

    v_boleto				record;

    v_validez			integer;
    v_nombre_funcion	varchar;
    v_resp				varchar;

BEGIN

    v_nombre_funcion:='obingresos.f_get_validez_tarifaria';
    select b.nro_boleto,bv.id_boleto,string_to_array(b.ruta_completa,'-')as ruta_completa, po.codigo as pais_origen,
    pd.codigo as pais_destino, ao.codigo as aeropuerto_origen, ad.codigo as aeropuerto_destino,
    bv.clase
    into v_boleto
    from obingresos.tboleto_vuelo bv
    inner join obingresos.tboleto b on b.id_boleto = bv.id_boleto
    inner join obingresos.taeropuerto ao on ao.id_aeropuerto = bv.id_aeropuerto_origen
    inner join obingresos.taeropuerto ad on ad.id_aeropuerto = bv.id_aeropuerto_destino
    inner join param.tlugar po on po.id_lugar = param.f_get_id_lugar_pais(ao.id_lugar)
    inner join param.tlugar pd on pd.id_lugar = param.f_get_id_lugar_pais(ad.id_lugar)
    where bv.id_boleto_vuelo = p_id_boleto_vuelo;

    select ct.duracion_meses into v_validez
    from obingresos.tclase_tarifaria ct
    where ct.tipo_condicion = 'general' and
    ct.ruta::varchar[] <@ v_boleto.ruta_completa::varchar[] and ct.codigo = v_boleto.clase;

    if (v_validez is null) then
    	select ct.duracion_meses into v_validez
        from obingresos.tclase_tarifaria ct
        where ct.tipo_condicion = 'aeropuerto' and ct.codigo = v_boleto.clase and
        v_boleto.ruta_completa::varchar[] && ct.aeropuerto;
    end if;

    if (v_validez is null) then
    	select ct.duracion_meses into v_validez
        from obingresos.tclase_tarifaria ct
        where ct.tipo_condicion = 'pais' and ct.codigo = v_boleto.clase and
        (	v_boleto.pais_origen = ct.pais or
        	v_boleto.pais_destino = ct.pais);
    end if;

    if (v_validez is null) then
    	raise exception 'No se encontro la configuracion de validez para la clase tarifaria : %,
        					ruta : %. Boleto : %',v_boleto.clase,v_boleto.aeropuerto_origen || '- ' || v_boleto.aeropuerto_destino,v_boleto.nro_boleto ;
    end if;
    return v_validez;

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