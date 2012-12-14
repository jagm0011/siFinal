<?
require("modelo.php");
$modelo=new modelo('localhost', 'root', '', 'SI_DB');
$modInter=$modelo->getVariable('modelo_10_PCC_1554_1555_1944');
$modInter=unserialize($modInter);
//var_dump($modInter);
$con=0;
$con2=0;
$consulta="SELECT distinct(t1.idUser) 
		FROM ratings t1 
		inner join usuario t2 ON t1.idUser=t2.id		
		where (t2.idTabla>1554 and t2.idTabla<1944)  AND ";
		//echo count($modInter[17769]);
//while($con<count($modInter)){
foreach ($modInter as $idItem => $vectorK){
	$consulta.=" (t1.idItem='".$idItem."' AND (";
	while($con2<count($modInter[$idItem])){
		
		$consulta.=" t1.idItem='".$modInter[$idItem][$con]['idItem']."'  OR ";
		$con2++;	
	}
	$consulta=substr($consulta, 0, -3);
	$consulta.=")) AND ";
	//$con=0;
	$con2=0;;
}
$consulta=substr($consulta, 0, -4);
//echo $consulta.')';
$response=mysql_query($consulta);
 while($row=mysql_fetch_row($response)){
 		echo $row[0];
 		
 		
 }
 echo "fin";
?>