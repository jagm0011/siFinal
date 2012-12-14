<?

class pelicula{

    private $id;
    private $anio;
    private $titulo;    
    
    public function pelicula($id, $anio, $titulo){      
        $this->id = $id;
        $this->anio = $anio;
        $this->titulo = $titulo;
    }
    
    public function dameId() {
        return $this->id;       
    }
    
    public function dameTitulo() {
        return $this->anio;
    }
    
    public function dameAnio() {
        return $this->titulo;
    }
    
}

?>