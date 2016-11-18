<?php
/**
*@package pXP
*@file gen-ACTDeposito.php
*@author  (jrivera)
*@date 06-01-2016 22:42:28
*@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
*/

class ACTDeposito extends ACTbase{    
			
	function listarDeposito(){
		$this->objParam->defecto('ordenacion','id_deposito');

		$this->objParam->defecto('dir_ordenacion','desc');
		if ($this->objParam->getParametro('id_agencia') != '') {
			$this->objParam->addFiltro("dep.id_agencia = ". $this->objParam->getParametro('id_agencia'));
		}
		if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
			$this->objReporte = new Reporte($this->objParam,$this);
			$this->res = $this->objReporte->generarReporteListado('MODDeposito','listarDeposito');
		} else{
			$this->objFunc=$this->create('MODDeposito');
			
			$this->res=$this->objFunc->listarDeposito($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
				
	function insertarDeposito(){
		$this->objFunc=$this->create('MODDeposito');	
		if($this->objParam->insertar('id_deposito')){
			$this->res=$this->objFunc->insertarDeposito($this->objParam);			
		} else{			
			$this->res=$this->objFunc->modificarDeposito($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
						
	function eliminarDeposito(){
			$this->objFunc=$this->create('MODDeposito');	
		$this->res=$this->objFunc->eliminarDeposito($this->objParam);
		$this->res->imprimirRespuesta($this->res->generarJson());
	}

	function subirCSVDeposito(){
	    //Valida extencio
        $arregloFiles = $this->objParam->getArregloFiles();
        $ext = pathinfo($arregloFiles['archivo']['name']);
        $extension = $ext['extension'];
        $error = 'no';
        $mensaje_completo = '';
        //Validar errores del archivo
        if(isset($arregloFiles['archivo']) && is_uploaded_file($arregloFiles['archivo']['tmp_name'])){
            if ($extension != 'csv' && $extension !='CSV' ) {
                $mensaje_completo = "La extensión del archivo debe ser CSV";
                $error = 'error_fatal';
            }
            $upload_dir = "/tmp/";
            $file_path = $upload_dir . $arregloFiles['archivo']['name'];
            if(!move_uploaded_file($arregloFiles['archivo']['tmp_name'],$file_path)){
                $mensaje_completo = "Error al guardar el archivo csv en disco";
                $error ='error_fatal';
            }
        } else {
            $mensaje_completo = "No se subio el archivo";
            $error = 'error_fatal';
        }
        //armar respuesta en error fatal
            if ($error == 'error_fatal') {
                $this->mensajeRes=new Mensaje();
                $this->mensajeRes->setMensaje('ERROR','ACTDeposito.php',$mensaje_completo, $mensaje_completo,'control');
            //si no es error fatal proceso el archivo
        }else{
            $lines = file($file_path);
                foreach ($lines as $line_num => $line){
                    $arr_temp = explode('|',$line);
                    if(count($arr_temp)!=2){
                        $error = 'error';
                        $mensaje_completo .= "No se proceso la linea: $line_num, por un error en el formato \n";
                    }else{
                        $arr_temp[1] = str_replace(',', '.', $arr_temp[1]);
                        $this->objFunc=$this->create('MODDeposito');
                        $this->res=$this->objFunc->insertarDeposito($this->objParam); // cambiar
                        if ($this->res->getTipo() == 'ERROR') {
                            $error = 'error';
                            $mensaje_completo .= $this->res->getMensaje() . " \n";
                        }

                    }
                }
            }
        //armar respuesta en caso de exito o error en algunas tuplas
        if ($error == 'error') {
            $this->mensajeRes=new Mensaje();
            $this->mensajeRes->setMensaje('ERROR','ACTDeposito.php','Ocurrieron los siguientes errores : ' . $mensaje_completo,
                $mensaje_completo,'control');
        } else if ($error == 'no') {
            $this->mensajeRes=new Mensaje();
            $this->mensajeRes->setMensaje('EXITO','ACTDeposito.php','El archivo fue ejecutado con éxito',
                'El archivo fue ejecutado con éxito','control');
        }

        //devolver respuesta
        $this->mensajeRes->imprimirRespuesta($this->mensajeRes->generarJson());
    }
}

?>