<?
require_once("DaoUsuario.php");
class usuario{

        private $id;
        private $email;
        private $password;
        private $nombre;  
            
        private $valoraciones;
        private $recomendaciones;
            
    
    
	public function usuario($email,$password,$id,$nombre,$valoraciones,$recomendaciones){                                                       
            
            $this->id = $id;
            $this->email = $email;
            $this->password = $password;
            $this->nombre = $nombre;
            $this->valoraciones = $valoraciones;
            $this->recomendaciones = $recomendaciones;
            
	}
        
        public function dameId(){
            
            return $this->id;
        }
        
        public function dameEmail(){
            
            return $this->email;
        }
        
        public function damePassword(){
            
            return $this->password;
        }
        
        public function dameNombre(){
            
            return $this->nombre;
        }
        
        public function dameValoraciones(){
            
            return $this->valoraciones;
        }
        
        public function dameRecomendaciones(){
            
            return $this->recomendaciones;
        }
}
?>