<?
require_once("modelo.php");
require_once("vista.php");
require_once("KNN.php");


class controlador{
	private  $vista;
	private  $modelo;
	private $KNN;
	
	public function controlador($vista, $modelo){
		$this->vista=$vista;
		$this->modelo=$modelo;
		
	}	
	public function loguearUsuario($email, $pass){
            return $this->modelo->loguearUsuario($email, $pass);
            
        }
        public function initSistema(){
            //parametros escogidos para el sistema
            //algoritmo similitudes "Similitud del coseno"
            $PCC=0;
            $SC=1;
           //algoritmo de prediccion "Weighted Sum"
            $IA=0;
            $WS=1;
            //Nvalores no usada al elegir WS
            $Nvalores=0;
            //numero de similitudes vecinas = 10
            $K=10;
            
            $desde=1944;
            $cuantos=1944;
            $fin=1944;
            $this->modelo->cargaRatings($desde, $cuantos, $fin);
            $ratings=$this->modelo->dameRatings();	
            //y un vector que es un indice con las posiciones a los item en el vector $ratings	
            $indiceItem=$this->modelo->dameIndiceRatingIdItem();
            $indiceUser=$this->modelo->dameIndiceRatingIdUser();
             //instanciamos la clase KNN que es donde se van a realizar todos los calculos
            $this->KNN=new KNN($ratings,$indiceItem,$indiceUser,$K, $PCC, $SC, $IA, $WS,$Nvalores);

            //variable usuada para recoger el modelo de la BD
            $nombreModeloIntermedio='modelo_'.$K.'_SC_'.$desde.'_'.$cuantos.'_'.$fin;
            $tablaModeloIntermedio=$this->modelo->getVariable($nombreModeloIntermedio);
            if($tablaModeloIntermedio!=null) {
                
                $tablaModeloIntermedio=  unserialize($tablaModeloIntermedio);
                //sacamos los K vecinos que nos interesan
                //$tablaModeloIntermedio=$this->dameLosKMejoresParaCadaItem($K, $tablaModeloIntermedio);
                //lo introduce como modelo actual
                $this->KNN->ajustaModeloIntermedio($tablaModeloIntermedio);                     
           }else{
               $this->KNN->calculoVecinos($desde, $cuantos, $fin);
               $tablaModeloIntermedio=$this->KNN->dameModeloIntermedio();
                //lo serializo el resultado para almacenarlo en la BD y tner el modelo guardado
               $tablaModeloIntermedio=serialize($tablaModeloIntermedio);
               $this->modelo->setVariable($nombreModeloIntermedio, $tablaModeloIntermedio);
           }
           
           
           
        }
	//funcion que ejecuta el algoritmo con los parametros dados.
	public function initPruebas($K,$calculoSimilitud, $algPredicion,$Nvalores ){
            //cargamos y recogemos las valoraciones
            
            //funcion para calcular el tiempo
            
            //Configuracion de los parametros del KNN
            $PCC=0;
            $SC=0;
            if($calculoSimilitud=='PCC')
                    $PCC=1;
            else
                    $SC=1;
            $IA=0;
            $WS=0;
            if($algPredicion=='IA')
                    $IA=1;
            else
                    $WS=1;
           
            //empezamos con el calculo del modelo intermedio     
            /*$desde=2549;
            $cuantos=2550;
            $fin=3186;
            */
            for($i=0;$i<5;$i++){
                switch ($i){
                    case 0:{
                        //0-1554
                        $desde=1554;
                        $desdeUsu=1555;
                        $hastaUsu=1944;
                       // $desde=10;
                        $i=6;
                        break;
                    }
                    case 1:{
                        //389-1944
                        $desde=1944;  
                        $desdeUsu=0;
                        $hastaUsu=388;
                        break;
                    }
                    case 2:{
                        //777-1944, 0-388
                        $desde=388;  
                        $desdeUsu=389;
                        $hastaUsu=776;
                        break;
                    }
                    case 3:{
                        //1166-1944, 0-778
                        $desde=777;  
                        $desdeUsu=779;
                        $hastaUsu=1165;
                        break;
                    }
                    case 4:{
                        //1555- 1944, 0-1166
                        $desde=1166;  
                        $desdeUsu=1167;
                        $hastaUsu=1554;
                        break;
                    }
                }
                $time=time();
                $cuantos=1555;
                $fin=1944;
                $this->modelo->cargaRatings($desde, $cuantos, $fin);
                $ratings=$this->modelo->dameRatings();	
                //y un vector que es un indice con las posiciones a los item en el vector $ratings	
                $indiceItem=$this->modelo->dameIndiceRatingIdItem();
                $indiceUser=$this->modelo->dameIndiceRatingIdUser();
                 //instanciamos la clase KNN que es donde se van a realizar todos los calculos
                $this->KNN=new KNN($ratings,$indiceItem,$indiceUser,$K, $PCC, $SC, $IA, $WS,$Nvalores);
                
                //variable usuada para recoger el modelo de la BD
                $nombre='modelo_'.$K.'_'.$calculoSimilitud.'_'.$desde.'_'.$cuantos.'_'.$fin;
                //variable usuada para recoger el modelo de la BD
                //$nombreAux='modelo_30_'.$calculoSimilitud.'_'.$desde.'_'.$cuantos.'_'.$fin;
                if($this->modelo->getVariable($nombre)!=null) {
                     $tablaModeloIntermedio=$this->modelo->getVariable($nombre);
                     $tablaModeloIntermedio=  unserialize($tablaModeloIntermedio);
                     //sacamos los K vecinos que nos interesan
                     //$tablaModeloIntermedio=$this->dameLosKMejoresParaCadaItem($K, $tablaModeloIntermedio);
                     //lo introduce como modelo actual
                     $this->KNN->ajustaModeloIntermedio($tablaModeloIntermedio);                     
                }else{
                    $this->KNN->calculoVecinos($desde, $cuantos, $fin);
                    $tablaModeloIntermedio=$this->KNN->dameModeloIntermedio();
                     //lo serializo el resultado para almacenarlo en la BD y tner el modelo guardado
                    $tablaModeloIntermedio=serialize($tablaModeloIntermedio);
                    $this->modelo->setVariable($nombre, $tablaModeloIntermedio);
                }
                print_r($tablaModeloIntermedio);
                $time2=time();
                //id=147042
                $tiempoModelo=($time2-$time);
                $time=time();
                $con=$desdeUsu;
                $con2=0;
                $vectorMAE=array();
                $total=0;
                $mae=0;
               // while($con<$hastaUsu){
                    //$usuario=$this->loguearUsuario('email'.$con.'@email.com', $con);
                    $usuario=$this->loguearUsuario('email1563@email.com', '1563');
                    //$items=$this->modelo->dameListaPeliculas();
                   $mae=$this->KNN->prediccionPruebas($usuario);
                    if($mae>0){
                        $vectorMAE[$con2]=$mae;
                        $total+=$vectorMAE[$con2];
                        $con2++;
                    }
                    $con++;
              // }
                //print_r($vectorMAE);
                $time2=time();
                $tiempoPrediccion=($time2-$time);
                 echo $tiempoModelo.'####'.$tiempoPrediccion.'####';
                //printf de un vector
               // var_dump($this->KNN->dameModeloIntermedio());
                echo $nombre;
                echo '####';
                if(count($vectorMAE)>0){
                    echo $total/count($vectorMAE);
                }else{
                    echo "No existen prediciones";
                }
                echo '||';
                unset($this->KNN);
            }
           
	}
        public function dameLosKMejoresParaCadaItem($K, $tablaModeloIntermedio){
            $nuevoModelo=array();
            foreach($tablaModeloIntermedio as $idItem=>$item){
                $vecinosElegidos=array();
                $con=count($item)-1;
                 
                while($con>=0){
                    //si el vector es <K pues solo lo anado
                    if(count($vecinosElegidos)<$K){
                        $vecinosElegidos[count($vecinosElegidos)]=$item[$con];
                    //cuando ya tenemos los primeros K completos tenemos que echar a los peores
                    //recorro el vectornuevo y elimino al peor
                    }else{
                        $con2=$K-1;
                        $actual=$vecinosElegidos[$con2];
                        $pos=$con2;
                        while($con2>=0){
                            if($vecinosElegidos[$con2]['similitud']<$actual['similitud']){
                                $actual=$vecinosElegidos[$con2]['similitud'];
                                $pos=$con2;
                            }
                            $con2--;
                        }
                        $vecinosElegidos[$pos]=$item[$con];
                    }
                    $con--;
                }
                $nuevoModelo[$idItem]=$vecinosElegidos;
            }
            return $nuevoModelo;
        }
	public function showVista(){
		$this->vista->show();
	}
}



?>