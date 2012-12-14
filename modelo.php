<?
require_once("usuario.php");
require_once("DaoUsuario.php");
require_once("pelicula.php");
require_once("DaoPelicula.php");
class modelo{
	private $configDB;
	private $db;	
	//private $usuario;
	private $pelicula;
	private $rating;
	//usados como indices para mejorar la eficiencia
	private $indiceRatingIdItem;
        private $indiceRatingIdUser;
                
	private $DaoUsuario;	
        private $DaoPelicula;
	public function modelo($server='', $user='', $pass='', $dataBase=''){
            if(isset($server)){
                $this->configDB['server']=$server;
                $this->configDB['user']=$user;
                $this->configDB['pass']=$pass;
                $this->configDB['dataBase']=$dataBase;
            }else{
                $this->configDB=array();
                $this->configDB['server']='localhost';
                $this->configDB['user']='root';
                $this->configDB['pass']='';
                $this->configDB['dataBase']='SI_DB';
            }
            $this->db=0;
            //$this->usuario=new usuario();
            //$this->pelicula=new pelicula();		
            $this->rating=array();

            $this->DaoUsuario=new DaoUsuario();
            $this->DaoPelicula=new DaoPelicula();
            $this->indiceRatingIdItem=array();
            $this->indiceRatingIdUser=array();
            $this->init();
		
	}	
        
	public  function cargaRatings($desde, $cuantos, $fin){
            $where="";
            
            $con=$desde;
            while($cuantos>=0){
                if($con<0){
                    $con=$fin;
                }
                $where.="idTabla='".($con+1)."' OR ";
               
                $con--;
                $cuantos--;
            }
            if($where!=''){
                $where=  substr($where, 0, -3);
                $where=' where '.$where; 
              
            }
            $consulta="SELECT id FROM  usuario ".$where."  ORDER BY id";
            //echo $consulta;
            $response=mysql_query($consulta);
            $con=0;
            while($row=mysql_fetch_row($response)){
                $this->indiceRatingIdUser[$con]=$row[0];
                $con++;
            }
            $consulta="SELECT idUser, idItem, rating 
                FROM  ratings 
                WHERE idUser BETWEEN '".$this->indiceRatingIdUser[0]."' AND '".$this->indiceRatingIdUser[count($this->indiceRatingIdUser)-1]."'
                ORDER BY idItem,idUser";	
            //echo $consulta;
            $response=mysql_query($consulta);
            $con=-1;;
            $this->indiceRatingIdItem[0]=0;
            while($row=mysql_fetch_row($response)){
                    //rating[idItem][idUsu]
                $this->rating[$row[1]][$row[0]]=intval($row[2]);
                if($con==-1 || $this->indiceRatingIdItem[$con]!=$row[1]){
                    $con++;
                    $this->indiceRatingIdItem[$con]=$row[1];				
                }
            }
            
		//print_r($this->indiceRatingIdItem);
	}
	public function init(){
            $this->db = mysql_connect($this->configDB['server'], $this->configDB['user'], $this->configDB['pass']) or die("Database error");		
            mysql_select_db($this->configDB['dataBase'], $this->db);
		
	}
	
	
	public function  dameRatings(){
            return $this->rating;			
	}
	
	public function  dameIndiceRatingIdItem(){
            return $this->indiceRatingIdItem;			
	}
        public function  dameIndiceRatingIdUser(){
            return $this->indiceRatingIdUser;			
	}
        public function loguearUsuario($email, $pass){
            $idUsuario=$this->DaoUsuario->dameIdUsuario($email);
            $vectorDatos=$this->DaoUsuario->dameUsuario($idUsuario);
            //nombre, pass, email
            if($vectorDatos[1]==$pass){
                $recomendaciones=$this->DaoUsuario->dameRecomendaciones($idUsuario);
                $valoraciones=$this->DaoUsuario->dameValoraciones($idUsuario);
                $usuario=new usuario($email, $pass, $idUsuario, $vectorDatos[0], $valoraciones, $recomendaciones);
                return $usuario;
            }
            else
                return null;
            
            
        }
        /*****ZONA VARIABLES ********/
	public function compruebaVariableEnDB($nombre){
            $consulta="SELECT id FROM variables WHERE nombre='".$nombre."'";
            $response=mysql_query($consulta);
            if(mysql_num_rows($response)>0)
                    return true;
            return false;	

	}
	public function setVariable($nombre, $valor){
            $consulta=	"INSERT INTO variables (nombre ,valor)
                    VALUES ('".$nombre."',  '".$valor."')";
            if($this->compruebaVariableEnDB($nombre))
                    $consulta=	"UPDATE  variables SET  valor =  '".urlencode($valor)."' WHERE  nombre ='".$nombre."'";

            //echo $consulta;
            $response=mysql_query($consulta);
		
	}
	public function getVariable($nombre){
            $consulta="SELECT valor FROM variables WHERE nombre='".$nombre."'";
            $response=mysql_query($consulta);
            if(mysql_num_rows($response)>0)
                    while($row=mysql_fetch_row($response)){
                            return urldecode($row[0]);	
                    }			
            return null;
	}
}

?>