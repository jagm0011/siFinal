<?

class DaoPelicula{  
    
    private $vectorPeliculas;
    
    public function DaoPelicula(){
        $this->vectorPeliculas = array();      
    }        
    
    public function cargarPeliculas() {
        $consulta = "SELECT idItem, year_numerical, name FROM peliculas ORDER BY idItem";
        $response = mysql_query($consulta);       
        //$this->vectorPeliculas=array();        
        while($row = mysql_fetch_row($response)){
            //$this->vectorPeliculas[$row[0]][$row[1]][$row[2]];
            $pelicula=new pelicula($row[0], $row[1], $row[2]);
            $this->vectorPeliculas[$row[2]]=$pelicula;
            
        }          
        return $this->vectorPeliculas;
    }     
    
}

?>