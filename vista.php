<?
	class vista{
		public function vista(){
			}
		function show(){
			?>
<html>
	 <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>Pruebas de SSII</title>
        <script  type="text/javascript" async="" src="lib/js/jquery-1.8.3.min.js"></script>
        <!--<script  type="text/javascript" async="" src="lib/js/jquery-ui-1.8.16.custom.min.js"></script>
        <link rel="stylesheet" href="lib/css/cupertino/jquery-ui-1.8.16.custom.css" type="text/css" media="screen">-->
        <script  type="text/javascript" async="" src="lib/js/funcionesParametros.js"></script>
        <style>
            .cabecera{
                width:100%;
                height: 100px;
            }    
            .parametros{
                float:left;
                width:25%;
                
            }
            .resultado{
                float:left;
                width:70%;
                border-width: 1px;
        			border-style: solid;
        			height: 75%;
        			overflow: auto;
            }
            .bloqueo{
            	 background-color:#000000;
			      color:#CC0000;
			      position:absolute;
			      z-index:10;
			      filter:alpha(opacity=60);
			      float:left;
			      -moz-opacity:.60;
			      opacity:.60;			      
			      width:100%;
			      height: 100%;
			      display:none;
            }
        </style>        
    </head>
    <body >
    <div class="bloqueo" id="capaBloqueoApp"></div>
        <div class="cabecera">
            <span style="width:70%;font-size:34px; color: green;float:left;">Test de pruebas SRC SSII</span>
            <span style="width:25%;text-align:right;"><img src="lib/images/uja.jpg" width="100px"  > </img></span>  
            <hr>
        </div>
        <div class="parametros">
            <span>Elige una conexion para la BD</span>
            <select id="conexion">
                <option value="localhost|root||SI_DB">Casa</option>
                <option value="">Clase (not found)</option>
            </select>
            <br /><br />
        	<span>Elige un metodo para calcular</span>
            <select id="calculoSimilitud">
                <option value="PCC">Coeficiente de correlacion de Pearson</option>
                <option value="SC">Similitud del coseno</option>
            </select>
            <br /><br />
            <span>Introduce cuantos K-vecinos tendrá el modelo</span>
            <select id="kVecinos">
                <option value="10">10</option>
                <option value="20">20</option>
                <option value="30">30</option>
                <option value="40">40</option>
                <option value="50">50</option>
            </select><br><br>
            <span>Algoritmo de prediccion</span><br>
            <select id="algoritmoPrediccion" onchange="apareceValoresN()">
                <option value="IA">Item average</option>
                <option value="WS">Weighted sum</option>                
            </select>  
            <select id="Nvalores">
                <option value="1">1 (todas menos la que miramos)</option>
                <option value="2">2</option>
                <option value="4">4</option>
                <option value="8">8</option>
            </select>
            <br /><br>
            <input type="button" value="Ejecutar con parametros especificados" onclick="ejecuta()"/>
        </div>
        <div class="resultado" id="resultad">
            <h3>Aqu&iacute; se mostrar&aacute; el resultado...</h3>
        </div>
    </body>
</html>
<?			
			
		}	
	}

?>

