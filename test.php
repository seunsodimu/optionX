<?php
$vin ="4T1BF3EKXBU615164";
$yr =2011;
$vin_details = file_get_contents('https://vpic.nhtsa.dot.gov/api/vehicles/decodevinextended/'.$vin.'?format=json&modelyear='.$yr);
$car = json_decode($vin_details);
print_r($car);
echo "<br><br>==================================================<br>";
echo $car->SearchCriteria;
//echo $car->Results->DecodedVariable->DecodedVariableId[144]->Variable;

?>