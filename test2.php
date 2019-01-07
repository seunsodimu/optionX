<?php
require 'top.php';
?>
<html>
    <body>
        <form method="post" action="test2.php">
            <input type="text" name="vin" placeholder="VIN" value="<?php if(isset($_POST['vin'])){ echo $_POST['vin']; } ?>"/>
            <input type="submit"/>
            
        </form>
    </body>
</html>
<?php
if(isset($_POST['vin'])){

$vin =$_POST['vin'];
//$yr =$_POST['year'];
$vin_details = file_get_contents('https://vpic.nhtsa.dot.gov/api/vehicles/decodevinextended/'.$vin.'?format=json');
$car = json_decode($vin_details);
$msg = explode(" ", $car->Results[1]->Value);
$msg1 = explode(";", $car->Results[1]->Value);
if($msg[0]==0){
echo "<br><br>==================================================<br>";
echo $car->SearchCriteria;
echo "<br>";
echo "Message: ".$car->Results[1]->Value;
echo "<br>";
echo "Vehicle: ".$car->Results[8]->Value." ".$car->Results[5]->Value." ".$car->Results[7]->Value;
echo "<br>Type1: ".$car->Results[12]->Value;
echo "<br>Type2: ".$car->Results[22]->Value;
echo "<br>Doors: ".$car->Results[23]->Value;
echo "<br>Cylinders: ".$car->Results[66]->Value;
echo "<br>Engine Size: ".$car->Results[69]->Value;
echo "<br>";
echo "Drive Type: ".$car->Results[47]->Value;
echo "<br>";
echo "Trim: ".$car->Results[11]->Value;
echo "<br>";
echo "Fuel Type: ".$car->Results[73]->Value;
echo "<br>";
//$new = returnVehicle($car->Results[5]->Value, $car->Results[7]->Value);
//echo $new['makeid']." ".$new['make'];
//echo "<br>";
//echo $new['modelid']." ".$new['model'];
}
else{
    echo $msg1[0];
}
}
?>