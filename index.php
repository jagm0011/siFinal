<?
/*Utilizado como Front Controller
* $action variable que envia la vista para pedir una accion al controlador
* 
*/
require_once("controlador.php");
require_once("vista.php");
require_once("modelo.php");
//
$action='';
if(isset($_REQUEST['action'])) {
    $action=$_REQUEST['action'];	
}

switch($action) {
	//ejecutado al pulsar el boton "ejecutar" en la vista
    case "calculoVecino":{
        if(isset($_POST['kVecinos']) && isset($_POST['calculoSimilitud'])){
            $kVecinos=$_POST['kVecinos'];
            $calculoSimilitud=$_POST['calculoSimilitud'];
            $algPredicion=$_POST['algoritmoPrediccion'];
            $Nvalores=$_POST['Nvalores'];
            $conexion=explode('|',$_POST['conexion']);
            
            $vista=new vista();
            $modelo=new modelo($conexion[0],$conexion[1],$conexion[2],$conexion[3]);
            $controlador=new controlador($vista,$modelo);
            $controlador->initPruebas($kVecinos,$calculoSimilitud,$algPredicion,$Nvalores );

        }	
    break;
    }
    //se ejecuta al iniciar la app
    default:{
        $vista=new vista();
        $modelo=new modelo();
        $controlador=new controlador($vista,$modelo);
        $controlador->showVista();
    }	
}
?>