<?php
/**
 * Description of Canvas
 *
 * @author Edgar
 */
class Canvas {
    
     private $idList;
     private $AvList;
     private $imagePath;
     private $imagebasePath;
     private $rowsPath;
     private $elementsPath;
     private $with;
     private $height;
     private $avPixels;
     private $xPosition;
     private $yPositon;
     private $testCode;
     
     function __construct($with, $height, $avPixels, $testCode) {
         $this->idList =  array('uno','dos','tres','cuatro','cinco','seis','siete','ocho','nueve','diez','once');
         $this->AvList = array('Av1','Av2','Av3','Av4','Av5','Av6','Av7','Av8','Av9','Av10','Av11');
         $this->imagePath = "C:/xampp/htdocs/WSOptotype/src/OptometricCard/";
         $this->elementsPath = "C:/xampp/htdocs/WSOptotype/src/OptotypeForCard/";
         $this->imagebasePath = "C:/xampp/htdocs/WSOptotype/src/BaseCarta/";
         $this->rowsPath = "C:/xampp/htdocs/WSOptotype/src/rowsBase/";
         $this->avPixels = $avPixels;
         $this->with = $with;
         $this->height = $height;
         $this->testCode = $testCode;
         $this->xPosition = 80;
         $this->yPositon = 100;
     }
     
     function setXPosition($xPosition) {
         $this->xPosition = $xPosition;
     }

     function setYPositon($yPositon) {
         $this->yPositon = $yPositon;
     }
     
     function newCanvasImage(){
         
        $auxX = $this->xPosition;
        $auxY = $this->yPositon;
        //$pathImage = $this->imagePath."prueba.png";
        $pathImage = $this->imagePath.$this->testCode.".png";
        $img = imagecreate($this->with, $this->height);
 
        // Color de fondo
        imagecolorallocate($img, 255, 255, 255);

        // Guardo la imagen
        header("Content-type: image/png");
        imagepng($img,$pathImage);
        imagedestroy($img);
        
        $this->getBaseElementsforCanvas($pathImage,$this->idList);
        $this->setYPositon($auxY);
        $this->setXPosition($this->with - $auxX);
        $this->getBaseElementsforCanvas($pathImage,$this->AvList);
     }
     
     // creo imagenes para utilizarlas de filas en la carta principal
     function rowsForCanvas(){
         //$canvasCode = $this->rowsPath."prueba"; //// esto es atributo de clase
         $canvasCode = $this->rowsPath. $this->testCode;
         $row = 0;
         $totalRows = 11;
         $array = array_reverse($this->avPixels);
         
         While ($row < $totalRows){
             
             $canvasCode = $canvasCode."_".($row + 1).".png";
             $img = imagecreate((($array[$row]*4) + 200) , $array[$row] + 20);
            // Color de fondo
            imagecolorallocate($img, 255, 255, 255);

            // Guardo la imagen
            header("Content-type: image/png");
            imagepng($img,$canvasCode);
            imagedestroy($img);
             
            $row ++; 
            //$canvasCode = $this->rowsPath."prueba";
            $canvasCode = $this->rowsPath. $this->testCode;
         }
         
     }
     
     // inserto los optotypos en cada una de las filas creadas
     private function getBaseElementsforCanvas ($canvas ,$arrayElements){
         
         $sizeArray = count($arrayElements);
         $position = 0;
         $typeElemnt = 0;

         //echo 'elementos identificadores'."<br>";
         while ($position < $sizeArray){
             
             $this->insertElementsInCanvas($canvas,$arrayElements[$position],$position, $typeElemnt);
             $position ++;
             
         }   
     }
     
     private function insertElementsInCanvas ($canvas,$element, $position, $typeElemnt){
         
         //indico la dirección de la imagen a utilizar
        $imageCanvas = $canvas;
        
         /// Este bloque de codigo debe ir a un metodo
        if ($typeElemnt == 0){
            $xPixel = 57;
            $yPixel = 57;
            $imageElement = $this->imagebasePath.$element.".png";
        }elseif ($typeElemnt == 1) {
            $imageElement = $this->elementsPath.$element.".png";
            $array = array_reverse($this->avPixels);
            $xPixel = $array[$position];
            $yPixel = $array[$position];
        }
         
         /// creo un identificador en memoria para las imagnes
         $imgCanvas = imagecreatefrompng($imageCanvas);
         $imgElement = imagecreatefrompng($imageElement);
         imagealphablending($imgElement, false);
         imagesavealpha($imgElement, false);
         
         
         //Combino las imagenes
         imagecopyresampled(
                 $imgCanvas,
                 $imgElement, 
                 $this->xPosition, 
                 $this->yPositon, 
                 0, 
                 0, 
                 $xPixel, /*Nuevo tamaño en x*/
                 $yPixel, /*Nuevo tamaño en y*/
                 imagesx($imgElement), 
                 imagesy($imgElement)
                 );
         
         //sobreescribo el lienzo creado generando un resultado final
         imagepng($imgCanvas, $imageCanvas);
         
         // limpio de la memoria los identificadores (imagenes)
         imagedestroy($imgCanvas);
         imagedestroy($imgElement);
         
         if ($typeElemnt == 0){
             $this->setPositionelemnts($position); 
         }

     }
     
     private function setPositionelemnts($position){
         
         $extraSpcaceY = 60;
         $array = array_reverse($this->avPixels);
         $this->setYPositon($array[$position] + $this->yPositon + $extraSpcaceY);
       
     }
    
}
