CREATE OR REPLACE FUNCTION obingresos.ft_boleto_sel (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Ingresos
 FUNCION: 		obingresos.ft_boleto_sel
 DESCRIPCION:   Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'obingresos.tboleto'
 AUTOR: 		 (jrivera)
 FECHA:	        06-01-2016 22:42:25
 COMENTARIOS:	
***************************************************************************
 HISTORIAL DE MODIFICACIONES:

 DESCRIPCION:	
 AUTOR:			
 FECHA:		
***************************************************************************/

DECLARE

	v_consulta    		varchar;
	v_parametros  		record;
	v_nombre_funcion   	text;
	v_resp				varchar;
			    
BEGIN

	v_nombre_funcion = 'obingresos.ft_boleto_sel';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************    
 	#TRANSACCION:  'OBING_BOL_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		jrivera	
 	#FECHA:		06-01-2016 22:42:25
	***********************************/

	if(p_transaccion='OBING_BOL_SEL')then
     				
    	begin
    		--Sentencia de la consulta
			v_consulta:='with forma_pago_temporal as(
					    	select count(*)as cantidad_forma_pago,bfp.id_boleto,
					        	array_agg(fp.id_forma_pago) as id_forma_pago, array_agg(fp.nombre || '' - '' || mon.codigo_internacional) as forma_pago,
                                array_agg(bfp.importe) as monto_forma_pago,array_agg(fp.codigo) as codigo_forma_pago,
                                array_agg(bfp.numero_tarjeta) as numero_tarjeta,array_agg(bfp.ctacte) as ctacte,
                                array_agg(mon.codigo_internacional) as moneda_fp  
                                
					        from obingresos.tboleto_forma_pago bfp
					        inner join obingresos.tforma_pago fp on fp.id_forma_pago = bfp.id_forma_pago
                            inner join obingresos.tboleto bol on bol.id_boleto = bfp.id_boleto
                            inner join param.tmoneda mon on mon.id_moneda = fp.id_moneda
                            where ' || v_parametros.filtro || ' 
					        group by bfp.id_boleto        
					    )
            		select
						bol.id_boleto,
						bol.fecha_emision,
						age.codigo_noiata as codigo_noiata,
						bol.cupones,
						bol.ruta,
						bol.estado,
						bol.id_agencia,
						bol.moneda,
						bol.total,
						bol.pasajero,
						bol.id_moneda_boleto,
						bol.estado_reg,
						bol.gds,
						bol.comision,
						age.codigo as codigo_agencia,
						bol.neto,
						bol.tipopax,
						bol.origen,
						bol.destino,
						bol.retbsp,
						bol.monto_pagado_moneda_boleto,
						bol.tipdoc,
						bol.liquido,
						substring(bol.nro_boleto from 4)::varchar,
						bol.id_usuario_ai,
						bol.id_usuario_reg,
						bol.fecha_reg,
						bol.usuario_ai,
						bol.id_usuario_mod,
						bol.fecha_mod,
						usu1.cuenta as usr_reg,
						usu2.cuenta as usr_mod,
                        age.nombre	as nombre_agencia,
                        (case when (forpa.cantidad_forma_pago > 2) then
                        	0::integer
                        else
                        	forpa.id_forma_pago[1]::integer
                        end) as id_forma_pago,
                        (case when (forpa.cantidad_forma_pago > 2) then
                        	''DIVIDIDO''::varchar
                        else
                        	forpa.forma_pago[1]::varchar
                        end) as forma_pago,
                        (case when (forpa.cantidad_forma_pago > 2) then
                        	0::numeric
                        else
                        	forpa.monto_forma_pago[1]::numeric
                        end) as monto_forma_pago,
                        codigo_forma_pago[1],
                        forpa.numero_tarjeta[1],
                        forpa.ctacte[1],
                        forpa.moneda_fp[1],
                        
                        (case when (forpa.cantidad_forma_pago <> 2) then
                        	0::integer
                        else
                        	forpa.id_forma_pago[2]::integer
                        end) as id_forma_pago2,
                        (case when (forpa.cantidad_forma_pago > 2) then
                        	''DIVIDIDO''::varchar
                        when (forpa.cantidad_forma_pago < 2) then
                        	''NINGUNO''::varchar
                        else
                        	forpa.forma_pago[2]::varchar
                        end) as forma_pago2,
                        (case when (forpa.cantidad_forma_pago <> 2) then
                        	0::numeric
                        else
                        	forpa.monto_forma_pago[2]::numeric
                        end) as monto_forma_pago2,
                        codigo_forma_pago[2] as codigo_forma_pago2,
                        forpa.numero_tarjeta[2] as numero_tarjeta2,
                        forpa.ctacte[2] as ctacte2,
                        forpa.moneda_fp[2] as moneda_fp2,
                                                
                        bol.tc,
                        bol.moneda_sucursal
						from obingresos.tboleto bol
                        inner join obingresos.tagencia age on age.id_agencia = bol.id_agencia
                        left join forma_pago_temporal forpa on forpa.id_boleto = bol.id_boleto
                        inner join param.tmoneda mon on mon.id_moneda = bol.id_moneda_boleto
						inner join segu.tusuario usu1 on usu1.id_usuario = bol.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = bol.id_usuario_mod
				        where  ';
			
			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;
			raise notice '%', v_consulta;
			--Devuelve la respuesta
			return v_consulta;
						
		end;

	/*********************************    
 	#TRANSACCION:  'OBING_BOL_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		jrivera	
 	#FECHA:		06-01-2016 22:42:25
	***********************************/

	elsif(p_transaccion='OBING_BOL_CONT')then

		begin
			--Sentencia de la consulta de conteo de registros
			v_consulta:='select count(id_boleto)
					    from obingresos.tboleto bol
                        inner join obingresos.tagencia age on age.id_agencia = bol.id_agencia
                        inner join param.tmoneda mon on mon.id_moneda = bol.id_moneda_boleto
					    inner join segu.tusuario usu1 on usu1.id_usuario = bol.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = bol.id_usuario_mod
					    where ';
			
			--Definicion de la respuesta		    
			v_consulta:=v_consulta||v_parametros.filtro;

			--Devuelve la respuesta
			return v_consulta;

		end;
					
	else
					     
		raise exception 'Transaccion inexistente';
					         
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