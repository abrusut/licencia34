<?php
namespace AppBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use AppBundle\Service\Barcode\Barcode;

class BarcodeTwigExtension extends AbstractExtension
{
    private $barcodeService;
    private $barcodeDirectory;

    public function __construct(Barcode $barcodeService, $barcodeDirectory = '../../web/barcode/')
    {
        $this->barcodeService = $barcodeService;        
        $this->barcodeDirectory = $barcodeDirectory;
    }
    public function getFunctions()
    {
        return array(
            new TwigFunction('barcodeGif', array($this, 'getBarCodeGif')),
        );
    }

    public function getBarCodeGif($numeroCodigoBarra, $outPut='html')
    {            

        $fontSize = 10;   // GD1 in px ; GD2 in point
        $marge    = 10;   // between barcode and hri in pixel
        $x        = 150;  // barcode center
        $y        = 125;  // barcode center
        $height   = 50;   // barcode height in 1D ; module size in 2D
        $width    = 1;    // barcode height in 1D ; not use in 2D
        $angle    = 0;   // rotation in degrees : nb : non horizontable barcode might not be usable because of pixelisation

        // $code     = '0000397100000000000000001437120120331000000003';
        $code     = $numeroCodigoBarra;

        // barcode, of course ;)
        $type     = 'code128';



        // -------------------------------------------------- //
        //            ALLOCATE GD RESSOURCE
        // -------------------------------------------------- //
        $im     = imagecreatetruecolor(300, 300);
        $black  = ImageColorAllocate($im, 0x00, 0x00, 0x00);
        $white  = ImageColorAllocate($im, 0xff, 0xff, 0xff);
        $red    = ImageColorAllocate($im, 0xff, 0x00, 0x00);
        $blue   = ImageColorAllocate($im, 0x00, 0x00, 0xff);
        imagefilledrectangle($im, 0, 0, 300, 300, $white);

        // -------------------------------------------------- //
        //                      BARCODE
        // -------------------------------------------------- //
        $data = $this->barcodeService->gd($im, $black, $x, $y, $angle, $type, array('code' => $code), $width, $height);

        // -------------------------------------------------- //
        //                        HRI
        // -------------------------------------------------- //
        if (isset($font)) {
            $box = imagettfbbox($fontSize, 0, $font, $data['hri']);
            $len = $box[2] - $box[0];
            $this->barcodeService->rotate(-$len / 2, ($data['height'] / 2) + $fontSize + $marge, $angle, $xt, $yt);
            imagettftext($im, $fontSize, $angle, $x + $xt, $y + $yt, $blue, $font, $data['hri']);
        }
        
        $pathFile = $this->barcodeDirectory.'codigoBarra' ;
        imagegif($im,$pathFile);        
        
        $type = pathinfo($pathFile, PATHINFO_EXTENSION);
        
        // Read image path, convert to base64 encoding
        $imgData = base64_encode(file_get_contents($pathFile));

        // Format the image SRC:  data:{mime};base64,{data};
        $src = 'data: '.mime_content_type($pathFile).';base64,'.$imgData;

        // Liberar memoria
        imagedestroy($im);
        
        if($outPut == 'pdf'){            
            return $pathFile;
            //return $imgData;
        }else{
            return $src;              
        }
    }
   
}

?>