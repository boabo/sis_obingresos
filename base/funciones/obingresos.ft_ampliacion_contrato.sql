CREATE OR REPLACE FUNCTION obingresos.ft_ampliacion_contrato (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Ingresos
 FUNCION: 		obingresos.ft_ampliacion_contrato
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'obingresos.tagencia'
 AUTOR: 		 (Ismael Valdivia)
 FECHA:	        23-11-2021 11:13:33
 COMENTARIOS:
***************************************************************************
 HISTORIAL DE MODIFICACIONES:

 DESCRIPCION:
 AUTOR:
 FECHA:
***************************************************************************/

DECLARE


	v_parametros           	record;
	v_id_requerimiento     	integer;
	v_resp		            varchar;
	v_nombre_funcion        text;
	v_mensaje_error         text;

    v_id_contrato			integer;
    v_num_tramite			varchar;
    v_id_proceso_wf			integer;
    v_id_estado_wf			integer;
    v_codigo_estado			varchar;
    v_res_bool				BOOLEAN;
    v_datos_contrato_anterior	record;
    v_validar_boleta		varchar;

    v_existe_boleta			numeric;
    v_anexo_anterior		record;
    v_id_anexo				integer;
    v_tiene_ampliacion		numeric;
    v_id_contrato_actual	integer;
    v_existe_en_fecha		integer;
    v_fecha_final			date;
    v_tipo_contrato_ultimo	varchar;
    v_datos_ampliacion		record;
BEGIN

    v_nombre_funcion = 'obingresos.ft_ampliacion_contrato';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'OBING_AMPLI_CONT_INS'
 	#DESCRIPCION:	Registro de ampliacion de contrato
    #AUTOR:		Ismael Valdivia
    #FECHA:		25-11-2021 22:02:33
	***********************************/

	if(p_transaccion='OBING_AMPLI_CONT_INS')then

        begin

        	/*Insertamos el Contrato de la Agencia*/
            --11/12-2019 (Alan.felipez) revision si el numero de contrato ya se encuentra registrado
           -- perform leg.f_verificar_numero_contrato( 'contrato',v_parametros.numero_contrato, null,'insertar');

           /*Aqui recuperamos los datos del contrato anterior*/
            SELECT con.id_agencia,
            	   con.monto,
                   con.id_moneda,
                   con.contrato_adhesion,
                   con.id_funcionario,
                   con.id_lugar,
                   con.objeto,
                   con.solicitud,
                   con.tipo_agencia,
                   con.formas_pago,
                   con.moneda_restrictiva,
                   con.fecha_fin,
                   con.fecha_inicio,
                   con.tipo
            into
             	   v_datos_contrato_anterior
           	FROM leg.tcontrato con
            WHERE con.id_contrato = v_parametros.id_contrato_anterior;
            /**************************************************/

            /*Control para ver si es ampliacion*/
            if (v_datos_contrato_anterior is null) then
            	raise exception 'No se encontró el contrato anterior verifique el Id Contrato';
            end if;
            /***********************************/


            /*Control para ver si es ampliacion*/
            if (v_datos_contrato_anterior.tipo = 'ampliacion') then
            	raise exception 'El id_contrato enviado es de una ampliación se necesita el contrato anterior de la agencia';
            end if;
            /***********************************/


            /*Control para que la fecha Inicial no sea menor a la fecha final*/
            --if (v_parametros.fecha_inicio::date <= v_datos_contrato_anterior.fecha_fin) then
                --raise exception 'La fecha de la ampliacion no puede ser menor o igual a la fecha final del contrato <b>Fecha Final Contrato:</b> %, <b>Fecha Ampliacion que intenta Registrar:</b> %',to_char(v_datos_contrato_anterior.fecha_fin,'DD/MM/YYYY'),to_char(v_parametros.fecha_inicio::date,'DD/MM/YYYY');
            --end if;
            /*****************************************************************/

            /*Verificamos si existe una ampliacion para inactivarlo*/
            /*select count (con.id_contrato) into v_tiene_ampliacion
            from leg.tcontrato con
            where con.id_agencia = v_datos_contrato_anterior.id_agencia
            and con.tipo = 'ampliacion'
            and con.estado_reg = 'activo';*/
            /*******************************************************/


            select con.tipo into v_tipo_contrato_ultimo
            from leg.tcontrato con
            where con.id_agencia = v_datos_contrato_anterior.id_agencia
            and con.estado_reg = 'activo'
            order by con.id_contrato DESC
            limit 1;




            /*Inactivamos la ampliacion actual*/
            if (v_tipo_contrato_ultimo = 'ampliacion') then
            	raise exception 'Ya existe una ampliacion registrada';
            end if;


          -- else

           		select coalesce(max (id_contrato),0) into v_id_contrato
                from leg.tcontrato;


                SELECT
                     ps_num_tramite ,
                     ps_id_proceso_wf ,
                     ps_id_estado_wf ,
                     ps_codigo_estado
                  into
                     v_num_tramite,
                     v_id_proceso_wf,
                     v_id_estado_wf,
                     v_codigo_estado

                FROM wf.f_inicia_tramite(
                     p_id_usuario,
                     v_parametros._id_usuario_ai,
                     v_parametros._nombre_usuario_ai,
                     (select po_id_gestion from param.f_get_periodo_gestion(now()::date)),
                     'CON',
                     NULL,
                     NULL,
                     NULL,
                     'CON-' ||v_id_contrato
                     );



              /*Aqui el Se inserta la ampliacion del contrato*/
               INSERT INTO
                leg.tcontrato
              (
                id_usuario_reg,--1
                id_usuario_ai,--2
                usuario_ai,--3
                id_estado_wf,--4
                id_proceso_wf,  --5
                estado,--6
                tipo,--7

                id_gestion,--8
                id_agencia,--9
                monto,--10
                id_moneda,--11
                contrato_adhesion,--12
                id_funcionario,--13
                id_lugar,--14
                fecha_inicio,--15
                objeto,--16
                solicitud,--17
                numero,--18
                fecha_elaboracion,--19
                fecha_fin,--20
                tipo_agencia,--21
                formas_pago,--22
                moneda_restrictiva,--23
                id_contrato_fk,--24
                fk_id_contrato,--25
                observaciones,--26
                fecha_inicio_contrato,--27
                fecha_fin_contrato--28
              )
              VALUES (
                p_id_usuario,--1
                v_parametros._id_usuario_ai,--2
                v_parametros._nombre_usuario_ai,--3
                v_id_estado_wf,--4
                v_id_proceso_wf,--5
                v_codigo_estado,--6
                'ampliacion',--7
                (select po_id_gestion from param.f_get_periodo_gestion(now()::date)),--8
                v_datos_contrato_anterior.id_agencia,--9
                v_datos_contrato_anterior.monto,--10
                v_datos_contrato_anterior.id_moneda,--11
                v_datos_contrato_anterior.contrato_adhesion,--12
                v_parametros.id_funcionario,--13
                v_datos_contrato_anterior.id_lugar,--14
                (v_datos_contrato_anterior.fecha_fin::DATE + 1)::date::date,--15
                v_datos_contrato_anterior.objeto,--16
                'Elaboracion de contrato comercial (Ampliación)',--17
                '',--18
                (v_datos_contrato_anterior.fecha_fin::DATE + 1)::date,--19
                v_parametros.fecha_fin::date,--20
                v_datos_contrato_anterior.tipo_agencia,--21
                v_datos_contrato_anterior.formas_pago,--22
                v_datos_contrato_anterior.moneda_restrictiva,--23
                v_parametros.id_contrato_anterior,--24
                v_parametros.id_contrato_anterior,--25
                v_parametros.observaciones,--26
                (v_datos_contrato_anterior.fecha_fin::DATE + 1)::date,--27
                v_parametros.fecha_fin::date--28

              )returning id_contrato into v_id_contrato;
                  v_res_bool =  wf.f_inserta_documento_wf(p_id_usuario, v_id_proceso_wf, v_id_estado_wf);
              /*************************************/

              --Definicion de la respuesta
              v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Contrato de Ampliacion registrado Correctamente');
              v_resp = pxp.f_agrega_clave(v_resp,'Detalle','Contrato de Ampliacion registrado Correctamente');
              v_resp = pxp.f_agrega_clave(v_resp,'id_contrato_ampliacion',v_id_contrato::varchar);

			--end if;
            /*****************************************************************************/
            --Devuelve la respuesta
            return v_resp;

		end;

        /*********************************
        #TRANSACCION:  'OBING_UPDT_AMPL_INS'
        #DESCRIPCION:	Modificación de la ampliacion de contratos
        #AUTOR:		Ismael Valdivia
        #FECHA:		25-11-2021 22:02:33
        ***********************************/

    	elsif(p_transaccion='OBING_UPDT_AMPL_INS')then

            begin

        		select con.estado_reg,
                	   con.tipo,
                       con.id_agencia
                into
                	   v_datos_ampliacion
                from leg.tcontrato con
                where con.id_contrato = v_parametros.id_contrato_ampliacion;


                if (v_datos_ampliacion.estado_reg = 'inactivo') then
                	raise exception 'La ampliación que intenta modificar esta en estado inactivo, favor registre otra ampliación.';
                end if;

        		 if (v_datos_ampliacion.tipo != 'ampliacion') then
                	raise exception 'La modificación que intenta realizar no corresponde a una ampliación si no a un contrato.';
                end if;


                select con.tipo into v_tipo_contrato_ultimo
                from leg.tcontrato con
                where con.id_agencia = v_datos_ampliacion.id_agencia
                and con.estado_reg = 'activo'
                order by con.id_contrato DESC
                limit 1;

                if (v_tipo_contrato_ultimo = 'ampliacion') then
                	select con.id_contrato, con.fecha_fin into v_id_contrato_actual, v_fecha_final
                    from leg.tcontrato con
                    where con.id_agencia = v_datos_ampliacion.id_agencia
                    and con.estado_reg = 'activo'
                    and con.tipo not in ('ampliacion')
                    order by con.id_contrato DESC
                    limit 1;

                    update leg.tcontrato set
                    fecha_inicio = (v_fecha_final + 1)::date,
                    fecha_fin = v_parametros.fecha_fin::date ,
                    observaciones = v_parametros.observaciones
                    where id_contrato = v_parametros.id_contrato_ampliacion;
                else
                	raise exception 'No se puede realizar la modificación ya que existe un contrato despues de la ampliación';
                end if;





                v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Se actualizó los datos del contrato de ampliacion');
                v_resp = pxp.f_agrega_clave(v_resp,'Detalle','Se actualizó los datos del contrato de ampliacion');
                v_resp = pxp.f_agrega_clave(v_resp,'id_contrato_ampliacion',v_parametros.id_contrato_ampliacion::varchar);

                 return v_resp;
         end;

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

ALTER FUNCTION obingresos.ft_ampliacion_contrato (p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
  OWNER TO postgres;
