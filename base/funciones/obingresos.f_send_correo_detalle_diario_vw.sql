CREATE OR REPLACE FUNCTION obingresos.f_send_correo_detalle_diario_vw (
  p_id_uo_funcionario integer,
  p_id_uo integer,
  p_id_funcionario integer,
  p_fecha_asignacion date,
  p_fecha_finalizacion date,
  p_id_usuario integer,
  p_id_cargo integer
)
RETURNS varchar AS
$body$
DECLARE
    v_nombre_funcion   	text;
    v_resp    			varchar;
    v_mensaje 			varchar;
	v_record			record;
	v_correos			varchar;
    v_plantilla			varchar;
    v_hora_saludo		varchar;
    v_asunto			varchar;
    v_id_alarma			integer;
BEGIN
	v_nombre_funcion  = 'obingresos.f_send_correo_detalle_diario_vw';



    v_hora_saludo = case when current_time between '08:00:00'::time and '12:00:00'::time then '<b>Buenos dias' ::varchar
                           when current_time between '12:00:00'::time and '19:00:00'::time then '<b>Buenas tardes'::varchar end;

    v_plantilla = v_hora_saludo||' estimado Administrador:</b><br>
        <p>En fecha '||current_date||'.<br> se reportaron los siguientes incidentes.
        </p> <table>';

    for v_record in select tdd.asunto,tdd.desc_error,tdd.correos, tdd.fecha_venta
                    from obingresos.tdetalle_diario_error_vw tdd
                    where tdd.fecha_venta between (current_date - 60)::date and current_date loop

    	v_plantilla =  v_plantilla || '<tr> <td> '||v_record.desc_error||' </td> <td>en fecha '|| v_record.fecha_venta||'</td></tr>';
        v_asunto = v_record.asunto;
        v_correos = v_record.correos;
	end loop;
    v_id_alarma =  param.f_inserta_alarma(
                                              2134,
                                              v_plantilla,
                                              v_asunto,
                                              current_date,
                                              'notificacion',
                                              'Ninguna',
                                              612,
                                              '',
                                              v_asunto,--titulo
                                              '{filtro_directo:{campo:"id_funcionario",valor:"'||2134||'"}}',
                                              NULL::integer,
                                              'Incidentes reportados '||current_date,
                                              v_correos,
                                              null,
                                              null
                                              );
    return v_id_alarma::varchar;
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