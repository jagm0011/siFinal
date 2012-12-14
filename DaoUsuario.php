<?
class DaoUsuario{
	
	public function DaoUsuario(){}
        
        /**
         * Devuelve el identificador del usuario
         * @param $email
         * @return id del usuario en la BD
         */
        public function dameIdUsuario($email){
            
            $consulta="SELECT id FROM usuario WHERE email = '".$email."'";	
            $response=mysql_query($consulta);  
            
            while($row=mysql_fetch_row($response)){
                return $row[0]; 
                
            }
        }
        
        /**
         * Obtiene datos del usuario
         * @param $idUser identificador del usuario en la BD
         * @return vector con nombre, password e email del usuario
         */
        public function dameUsuario($idUser){            
            
            $consulta="SELECT nombre,pass,email FROM usuario WHERE id = '".$idUser."'";	
            $response=mysql_query($consulta);  
            
            while($row=mysql_fetch_row($response)){
                return $row;
            }                                    
        } 
        
        /**
         * Obtiene las valoraciones del usuario indicado
         * @param $idUser identificador del usuario
         * @return devuelve una matriz con el identificador de la película, valoración 
         */
        public function dameValoraciones($idUser){
            
            $consulta="SELECT idItem,rating FROM ratings WHERE idUser = '".$idUser."'";
            //echo $consulta;
            $response=mysql_query($consulta);  
            
            $matrizValoraciones = array();                      
            while($row=mysql_fetch_row($response)){
                $matrizValoraciones[$row[0]] = $row[1];               
            }            
            
            return $matrizValoraciones;
        }
        
        /**
         * Añadir un nuevo usuario a la base de datos
         * @return false si ya existe un usuario con el mismo email registrado en la base de datos
         */
        
        public function anadeNuevoUsuario($nombre,$email,$password){
            
            $actualizacion = "INSERT INTO usuario (email,pass,nombre) VALUES ('".$email."','".$pass."','".$nombre."')";
            $consulta = "SELECT id FROM usuario WHERE email = '".$email."'";
            $reponse = mysql_query($consulta);
            
            $anadio = false;
            
            if(mysql_affected_rows($reponse) == 0){
                
                $inserta = mysql_query($actualizacion);
                $anadio = true;
            }
                        
                
            return $anadio;            
            
        }
        
        
        public function anadeValoracion($idUser,$idPelicula,$puntuacion){
            
            $consulta = "INSERT INTO ratings (idUser,idItem,rating) VALUES ('".$idUser."','".$idItem."','".$puntuacion."')";
            $inserta = mysql_query($consulta);
            
        }
        
        
        public function anadeRecomendacion($idUser,$idPelicula){
            
            $consulta = "INSERT INTO recomendacion (idUsuario,idPelicula) VALUES ('".$idUser."','".$idPelicula."')";
            $inserta = mysql_query($consulta);            
            
        }
        
        
        public function dameRecomendaciones($idUser){
            
            $consulta="SELECT idPelicula FROM recomendacion WHERE idUsuario = '".$idUser."'";	
            $response=mysql_query($consulta);                                      
            
            $matrizRecomendaciones = array();
            $contador = 0;
            
            while($row=mysql_fetch_row($response)){
                $matrizValoraciones[$contador] = $row[0];
                $contador++;
            }            
            
            return $matrizRecomendaciones;            
            
        }        
        
        public function eliminaRecomendacion($idUser, $idPelicula){
            
            $consulta = "DELETE FROM recomendacion WHERE idUsuario = '".$idUser."' AND idPelicula = '".$idPelicula."'";	
            $elimina = mysql_query($consulta);
            
        }
        
        public function modificaValoracion($idUser, $idPelicula, $puntuacion){
            
            $consulta = "UPDATE ratings SET rating = '".$puntuacion."' WHERE idItem = '".$idPelicula."' AND idUser = '".$idPelicula."'";	
            $actualiza = mysql_query($consulta);
        }
        
}

?>