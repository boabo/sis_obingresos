<?php
/**
 *@package pXP
 *@file    SolModPresupuesto.php
 *@author  Rensi Arteaga Copari
 *@date    30-01-2014
 *@description permites subir archivos a la tabla de documento_sol
 */
header("content-type: text/css; charset=UTF-8");
?>

<style type="text/css" rel="stylesheet">
  .Inactivos{
      background-color: #EAA8A8;
      color: #900;
  }
  .Inactivos td {
      color: #000000;
      font-weight: bold;
      font-size: 12px;
  }
  .Inactivos:hover{
    background-color : #FFC57F;
  }
    /*************Verificado****************/
    .Verificado {
      background-color: #CFECF5;
    }

    .Verificado td {
        color: #0055FF;
        font-weight: bold;
        font-size: 12px;
    }

    .Verificado:hover {
      background-color: #b4f2a0;
    }

    /**************************************/
    /*************Canjeado****************/
    .Canjeado {
      background-color: #EEFCFF;
    }

    .Canjeado td {
        color: #000000;
        font-weight: bold;
        font-size: 12px;
    }
    /**************************************/

    /*************Canjeado****************/
    .No_Canjeado {
      background-color: #FFA4A4;
    }

    .No_Canjeado td {
        color: #000000;
        font-weight: bold;
        font-size: 12px;
    }
    /**************************************/



</style>
