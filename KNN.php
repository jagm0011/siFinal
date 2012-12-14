<?
class KNN {
		
    //estructura con las similitudes y las predicciones
    private $modeloIntermedio;
    private $predicciones;
    /* matriz con $matrizRating[i][j]
    * i= idItem
    * j= idUser
    */
    private $matrizRating;  
    private $indiceRatingIdItem;
    private $indiceRatingIdUsuario;
    //parametros configuracion. Por defecto PCC y IA
    // k vecinos
    private $PCC;
    private $SC;
    private $k;
    private $IA;
    private $WS;
    private $Nvalores;
    //Usuario activo para las predicciones
    private $usuarioActivo;
    
    public function KNN($matrizRating,$indiceRatingIdItem,$indiceRatingIdUsuario, $k,$PCC, $SC, $IA, $WS,$Nvalores){
        $this->matrizRating=$matrizRating;        
        $this->indiceRatingIdItem=$indiceRatingIdItem;
        $this->indiceRatingIdUsuario=$indiceRatingIdUsuario;
        $this->modeloIntermedio=array();
        $this->predicciones=array();
        $this->SC=$SC;
        $this->PCC=$PCC;
        $this->IA=$IA;
        $this->WS=$WS;
        $this->k=$k;     
        $this->Nvalores=$Nvalores;
        $this->usuarioActivo=null;
    }
    
    //*funcion usada para comprobar que el item no esta ya introducido en el modelo intermedio
    function compruebaNoEstaYaIntroducido($idItem, $quienNoDebeEstar ){
        if(!isset($this->modeloIntermedio[$idItem])){			
            $this->modeloIntermedio[$idItem]=array();	 		
            return true;
        }
        $con=count($this->modeloIntermedio[$idItem])-1;
        while($con>0){
            if(intval($this->modeloIntermedio[$idItem][$con]['idItem'])==intval($quienNoDebeEstar))
               return false;
            $con--;
        }
        return true;
     }
    
    
     /* Calcula modelo intermedio
      * rango de los datos para las pruebas
      */
    public function calculoVecinos(){        
        //Empezamos recorriendo el vector con las valoraciones
       // $total=$hasta;//count($this->indiceRatingIdItem)-1;
        $indiceRatingIdItem=$this->indiceRatingIdItem;
        $matrizRating=$this->matrizRating;
	$total=count($this->indiceRatingIdItem)-1;
        $con=$total;
        $con2=$total-1;
        while($con>=0){
       
            $item1=$matrizRating[$indiceRatingIdItem[$con]];
            $idItem1=intval($indiceRatingIdItem[$con]);
            while($con2>=0){
               
	        	   $item2=$matrizRating[$indiceRatingIdItem[$con2]];
	        	   $idItem2=intval($indiceRatingIdItem[$con2]);
	        	//Si no son el mismo item seguimos
	        	
	        	//sino tienen ya la similitud introducida seguimos
	                   // if($this->compruebaNoEstaYaIntroducido($idItem1, $idItem2) && $this->compruebaNoEstaYaIntroducido($idItem2,$idItem1)){
	        			//calcula la similitud        		
	        		$similitud=$this->calculaSimilitud($item1, $item2);
	        		//la almacenamos en el modelo intermedio           			     		
	             $this->almacenaSimilitud($idItem1, $idItem2, $similitud);
			   /// }
			
	        	$con2--;
                
            }
            unset($matrizRating[$indiceRatingIdItem[$con]]);
            unset($indiceRatingIdItem[$con]);
            
            $con--;
            $con2=$con-1;
        }
          
    }
   
    private function dameEscogidos(&$v1, &$v2, $desde, $cuantos, $fin){
        $vectorConRango1=array();
        $vectorConRango2=array();
        $con=$desde;
       // print_r($v1).'<br><br>';
        while($cuantos>=0){
            
            
            $aux=$this->indiceRatingIdUsuario[$con];
            if(isset($v1[$aux])){
                $vectorConRango1[$aux]=$v1[$aux];
            }
            if(isset($v2[$aux])){
                $vectorConRango2[$aux]=$v2[$aux];
            }
            $con--;
            $cuantos--;
        }
        
        
        if($con==0 && $cuantos>0){
            $con=$fin;            
       // print_r($v1).'<br><br>';
            while($cuantos>=0){
                $aux=$this->indiceRatingIdUsuario[$con];
                if(isset($v1[$aux])){
                    $vectorConRango1[$aux]=$v1[$aux];
                }
                if(isset($v2[$aux])){
                    $vectorConRango2[$aux]=$v2[$aux];
                }
                $con--;
                $cuantos--;
            }
        }
        $v1=$vectorConRango1;
        //print_r($v1).'<br><br>';
        $v2=$vectorConRango2;
    }
    /*****FUNCIONES CALCULO DE SIMILITUD******/
    //Coeficiente de correlacion de Pearson
    /*
     * r=1 correlacion perfect
     * 0<r<1 correlacion positiva
     * r=0 no existe relacion
     * 1<r<0 correlacion negativa
     * r=-1 correlacion negativa perfecta
     */     
    public function PCC($v1, $v2){
    	
       //$this->dameEscogidos($v1, $v2, $desde, $cuantos, $fin);
        
        $mediaValoracionItem1=$this->calculaMedia($v1);
        $mediaValoracionItem2=$this->calculaMedia($v2);
        
       //Calculo numerador
        $sumatoriaNumerador=0;
        $sumatoriaDenominador1=0;
	     $sumatoriaDenominador2=0;
        
        
        $con=0;   
        foreach($v1 as $idUsu => $valor){        	
            //compruebo que los dos usuarios con el mismo id han hecho una valoracion del item 
            if(isset($v2[$idUsu])){        		
                $sumatoriaNumerador+=($valor-$mediaValoracionItem1)*($v2[$idUsu]-$mediaValoracionItem2);
                $sumatoriaDenominador1+=($v1[$idUsu]-$mediaValoracionItem1)*($v1[$idUsu]-$mediaValoracionItem1);
                $sumatoriaDenominador2+=($v2[$idUsu]-$mediaValoracionItem2)*($v2[$idUsu]-$mediaValoracionItem2);
                $con++;        		
            } 
        }
        $sumatoriaDenominador=sqrt($sumatoriaDenominador1*$sumatoriaDenominador2);
        if($sumatoriaNumerador==0){
                return 0;
        }
         $resultado=$sumatoriaNumerador/$sumatoriaDenominador;
			if($con<=10){
				return (($resultado+1)/2)/$this->k;
			}
        //hay que hacer esta cuenta para transformala a medida de similitud
        return ($resultado+1)/2;       
    }
        
    //Similitud del coseno
    /*
     * r=0 minima similitus
     * r=1 maxima similitud
     */
    public function SC($v1, $v2){
        //$this->dameEscogidos($v1, $v2, $desde, $cuantos, $fin);
        $sumatoriaNumerador=0;
        $sumatoriaDenominador=0;
        $sumatoriaDenominador1=0;
        $sumatoriaDenominador2=0;
			$con=0;	        
        //recorro solo un vector y compruebo que el usuario haya valorado tambien en el otro
        foreach($v1 as $idUsu => $valor){
        	//compruebo que los dos usuarios con el mismo id han hecho una valoracion del item 
            if(isset($v2[$idUsu])){
                $sumatoriaNumerador+=$v1[$idUsu]*$v2[$idUsu];
                $sumatoriaDenominador1+=$v1[$idUsu]*$v1[$idUsu];
                $sumatoriaDenominador2+=$v2[$idUsu]*$v2[$idUsu];
                    //$indicesComunes[$con]=$idUsu;
                    $con++;
            } 
        }
                    //si el numerador es 0 -> no hay valoraciones hechas por los mismos usuarios por lo que return 0;
        if($sumatoriaNumerador==0){
            return 0;	
        }	        
        $sumatoriaDenominador1=sqrt($sumatoriaDenominador1);
        $sumatoriaDenominador2=sqrt($sumatoriaDenominador2);
        if($con<=10){
				return ($sumatoriaNumerador/($sumatoriaDenominador1*$sumatoriaDenominador2))/$this->k;
			}
        return $sumatoriaNumerador/($sumatoriaDenominador1*$sumatoriaDenominador2);	     	     
    }
     /*
   * Funcin que almacena las similitudes en el modelo intermedio
   * $idItem1, $idItem2 y similitud entre ambos
   *	Al hacerlo de esta forma hacemos más eficiente el programa ya que con la misma pasada guardamos 2 valores
   */ 
    public function almacenaSimilitud($idItem1, $idItem2, $similitud){
        if(!isset($this->modeloIntermedio[$idItem1])){
            $this->modeloIntermedio[$idItem1][0]['idItem']=$idItem2;
            $this->modeloIntermedio[$idItem1][0]['similitud']=$similitud; 
        }else{			
            if(count($this->modeloIntermedio[$idItem1]) > $this->k){	   		   	
                $con=count($this->modeloIntermedio[$idItem1])-1;
                $actual=$con;
                $con--;   		
                while($con>=0){
                     if($this->modeloIntermedio[$idItem1][$actual]['similitud']>$this->modeloIntermedio[$idItem1][$con]['similitud']){
                         $actual=$con;
                     }
                     $con--;
                }
                $this->modeloIntermedio[$idItem1][$actual]['idItem']=$idItem2;
                $this->modeloIntermedio[$idItem1][$actual]['similitud']=$similitud;
            }else{
                $posActual=count($this->modeloIntermedio[$idItem1]);
                $this->modeloIntermedio[$idItem1][$posActual]['idItem']=$idItem2;
                $this->modeloIntermedio[$idItem1][$posActual]['similitud']=$similitud;   		
            }		
        }
                 //Similitud en la fila del item2
        if(!isset($this->modeloIntermedio[$idItem2])){
            $this->modeloIntermedio[$idItem2][0]['idItem']=$idItem1;
            $this->modeloIntermedio[$idItem2][0]['similitud']=$similitud; 
        }else{
            if(count($this->modeloIntermedio[$idItem2]) > $this->k){ 		   		
              $con=count($this->modeloIntermedio[$idItem2])-1;
              $actual=$con;
              $con--;   		
              while($con>=0){
                 if($this->modeloIntermedio[$idItem2][$actual]['similitud']>$this->modeloIntermedio[$idItem2][$con]['similitud']){
                     $actual=$con;
                 }
                 $con--;
              }
              $this->modeloIntermedio[$idItem2][$actual]['idItem']=$idItem1;
              $this->modeloIntermedio[$idItem2][$actual]['similitud']=$similitud;
            }else{
              $posActual=count($this->modeloIntermedio[$idItem2]);
              $this->modeloIntermedio[$idItem2][$posActual]['idItem']=$idItem1;
              $this->modeloIntermedio[$idItem2][$posActual]['similitud']=$similitud;
            }
        }
   }
   
    //Calcula la similitud entre 2 peliculas.
   //funcion para escoger la funcionalidad con la que calculamos el modelo intermedio
    private function calculaSimilitud($v1, $v2){     
        //echo $this->PCC.'eeeeeeeee';
        if($this->PCC==1)
            return $this->PCC($v1,$v2);
        else
            return $this->SC($v1,$v2);               
    }
   
    public function prediccion($usuario, $items){
    	 if($this->IA==1)
           return $this->prediccionItemAverage($usuario, $items);
      else
           return $this->prediccionWeigthedSum($usuario, $items);        
       
    	
    }
    public function prediccionPruebas($usuario ){
       
        if($this->IA==1){             
          $vectorPrediccionesSobreLasValoradas=$this->prediccionItemAveragePruebas($usuario);
          
        }else{
          $vectorPrediccionesSobreLasValoradas= $this->prediccionWeigthedSumPruebas($usuario);        
        }
        $itemsValoradas=$usuario->dameValoraciones();
       var_dump($itemsValoradas);
      echo '<br><br>';
      var_dump($vectorPrediccionesSobreLasValoradas);
        return $this->MAE($itemsValoradas,$vectorPrediccionesSobreLasValoradas);
    }
    /*
     * MAE
     * @param $itemsValoradas por el usuario
     * @param $vectorPredicciones nuevas valoraciones realizadas por el sistema
     * @return valor medio
     */
    public function MAE($itemsValoradas,$vectorPrediccionesSobreLasValoradas){
        $numerador=0;
        $P=count($vectorPrediccionesSobreLasValoradas);
        if($P>0){
        //Vectores asociativos con las valoraciones y en ambos casos
            foreach($vectorPrediccionesSobreLasValoradas as $idItem => $item){
                $aux=$item-$itemsValoradas[$idItem];
                if($aux<0)
                    $aux*=(-1);
                $numerador+=$aux;
            }
            //$P=count($vectorPrediccionesSobreLasValoradas);

            return $numerador/$P;
        }
        return 0;
    }
    /*
     * Funcion realizada para las pruebas del sistema
     * @param usuario activo
     */
    public function prediccionItemAveragePruebas($usuario){
    	
        $itemsValoradas=$usuario->dameValoraciones();
        //Calcula lo media de las peliculas valoradas
        
        $mediaValoracionUsuario=$this->calculaMedia($itemsValoradas);
        $idUsuario=$usuario->dameId();
        $vectorConPrediccionesSobreLasValoradas=array();
        //recorremos su vector de valoraciones 
        //Si Nvalores == 1 utilizamos todas sus valoraciones
        if($this->Nvalores==1){
            foreach($itemsValoradas as $idItem=>$valoracion){           
                $vectorConPrediccionesSobreLasValoradas[$idItem]=$this->itemAverage($idItem,$idUsuario, $mediaValoracionUsuario); 
            }
        }else{
            //hace NValores predicciones
            $con=$this->Nvalores;
            foreach($itemsValoradas as $idItem=>$valoracion){           
                $vectorConPrediccionesSobreLasValoradas[$idItem]=$this->itemAverage($idItem,$idUsuario, $mediaValoracionUsuario);                         
                $con--;
                if($con==0)
                   break;
            }
        }
        return $vectorConPrediccionesSobreLasValoradas;
        
    }
    /*
     * Funcion del sistema final
     * @param $usuario activo
     * @param $items todos los items del sistema
     * No esta finalizada
     */
    public function prediccionItemAverage($usuario, $items){
    	/*
         * falta eliminar los items valorados por el usuario
         */	
        $itemsValoradas=$usuario->damePeliculasValoradas();
        //Calcula lo media de las peliculas valoradas
        
        $mediaValoracionUsuario=calculaMedia($itemsValoradas);
        $idUsuario=$usuario->dameId();
        
        foreach($itemsValoradas as $idItem=>$valoracion){
           
            $prediccion=itemAverage($idItem,$idUsuario, $mediaValoracionUsuario); 
            $this->insertaPrediccion($idItem, $prediccion);
        }
        
    }
    //Funcion que calcula la prediccion de un item para un usuario
    /*
     * @param $idItem sobre el que vamos a realizar la prediccion
     * @param $idUsuario activo
     * @param $mediaValoracionUsuario media de las valoraciones del usuario
     * @return prediccion sobre el item que le pasamos
     */
   public function itemAverage($idItem,$idUsuario, $mediaValoracionUsuario){
   	    
       //con representa K vecinos
        $con=count($this->modeloIntermedio[$idItem])-1;
       // $prediccion=0;
        $mediaValoracionItem=$this->calculaMedia($this->matrizRating[$idItem]);
        $sumatoriaSimilitudes=0;
        $numerador=0;
        while($con>=0){        
            $valoracionQueElUsuarioLeDaAlItemVecino=0;
            if(isset($this->matrizRating[$this->modeloIntermedio[$idItem][$con]['idItem']][$idUsuario])){
                $valoracionQueElUsuarioLeDaAlItemVecino=$this->matrizRating[$this->modeloIntermedio[$idItem][$con]['idItem']][$idUsuario];
            
                $numerador+=$this->modeloIntermedio[$idItem][$con]['similitud']*
                        ( $valoracionQueElUsuarioLeDaAlItemVecino-$mediaValoracionUsuario);                                
               
             }
            $sumatoriaSimilitudes+=$this->modeloIntermedio[$idItem][$con]['similitud'];
            $con--;
        }
        return $mediaValoracionItem+($numerador/$sumatoriaSimilitudes);
   }
   
   /*
    * FUncion para calcular las prediciones basandonos en Weigthed Sum
    * calcula la prediccion para un usuario y un conjunto de peliculas
    * @param $usuario usuario activo
    * @param $items conjunto de peliculas para realizar la recomendacion
    */
   public function prediccionWeigthedSumPruebas($usuario){
    	$itemsValoradas=$usuario->dameValoraciones();
        //Calcula lo media de las peliculas valoradas
        
        $idUsuario=$usuario->dameId();
        $vectorConPrediccionesSobreLasValoradas=array();
        //recorremos su vector de valoraciones 
        foreach($itemsValoradas as $idItem=>$valoracion){           
            $aux=$this->weigthedSum($idItem,$idUsuario); 
            if($aux!=0){
                $vectorConPrediccionesSobreLasValoradas[$idItem]=$aux;
              
            }            
        }
        return $vectorConPrediccionesSobreLasValoradas;
    }
    /*
     * Funcion de prediccion del weigthedSum
     * @param $idItem activo
     * @param $idUsuario activo
     * @return predicion del item
     */
   public function weigthedSum($idItem,$idUsuario){
       //con son los Kvecinos que tiene el item enviado
        $con=count($this->modeloIntermedio[$idItem])-1;
       // $prediccion=0;
        
        $sumatoriaSimilitudes=0;
        $numerador=0;
        while($con>=0){        
           // $valoracionQueElUsuarioLeDaAlItemVecino=0;
            //si el usuario ha valorado este item se recoge su valoracion, sino se considera 0
          //  echo $this->modeloIntermedio[$idItem][$con]['idItem'].'<br>';
            if(isset($this->matrizRating[$this->modeloIntermedio[$idItem][$con]['idItem']][$idUsuario])){
                //echo $con.'---';
                $valoracionQueElUsuarioLeDaAlItemVecino=$this->matrizRating[$this->modeloIntermedio[$idItem][$con]['idItem']][$idUsuario];
            //echo $this->modeloIntermedio[$idItem][$con]['similitud']* $valoracionQueElUsuarioLeDaAlItemVecino
                $numerador+=$this->modeloIntermedio[$idItem][$con]['similitud']* $valoracionQueElUsuarioLeDaAlItemVecino;               
                $sumatoriaSimilitudes+=$this->modeloIntermedio[$idItem][$con]['similitud'];
            }
            
           
            $con--;
        }
        if($sumatoriaSimilitudes>0)      
            return ($numerador/$sumatoriaSimilitudes);
        return 0;
   }
   
   /*
    * Inserta las predicciones que obtiene en un vector
    * Solo guarda las 15 mejores
    * Ese vector será el vector con las recomendaciones del usuario
    */
   public function insertaPrediccion($idItem, $prediccion){
        $tamaPredicciones=count($this->predicciones);	
        if($tamaPredicciones<15){
            $this->predicciones[$tamaPredicciones]=array();
            $this->predicciones[$tamaPredicciones]['idItem']=$idItem;
            $this->predicciones[$tamaPredicciones]['prediccion']=$prediccion;
        }else{
            $actual=$this->predicciones[$tamaPredicciones]['prediccion'];
            $pos=$tamaPredicciones;
            $tamaPredicciones--;
            while($tamaPredicciones>=0){
                if($this->predicciones[$tamaPredicciones]['prediccion']<$actual){
                    $actual=$this->predicciones[$tamaPredicciones]['prediccion'];
                    $pos=$tamaPredicciones;
                }
                $tamaPredicciones--;	
            }	
            $this->predicciones[$pos]['idItem']=$idItem;
            $this->predicciones[$pos]['prediccion']=$prediccion;
        }
    }
    
    /*
     * FUNCIONES AUXILIARES 
     * get, set y algunas otras
     */
    /*
    * Calcular la media de las valoraciones de un item
    */
    public function calculaMedia($v){
        if(count($v)<=0)
             return 0;
        $media=0;
        //$con=count($v));       
        foreach($v as $valor ){        
            $media+=$valor;        	
        }        
        
        return $media/count($v);
    }
    public function dameModeloIntermedio(){
    	return $this->modeloIntermedio;	
    }
    public function ajustaModeloIntermedio($modeloIntermedio){
    	$this->modeloIntermedio=$modeloIntermedio;	
    }
    public function resetModeloIntermedio(){
    	$this->modeloIntermedio=array();	
    }
}
?>