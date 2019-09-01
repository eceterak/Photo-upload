<?php

namespace app;

class Resize
{
    private $height = null;
    private $width = null;
    private $image = null;

    public function __construct($height, $width)
    {
        $this->height = (int) $height;
        $this->width = (int) $width;
    }

    public function persist($image)
    {
        $this->getImageType($image);

        $this->resize($image);

        return $this->image;
    }

    private function getImageType($image)
    {
        $data = getimagesize($image);

        switch($data['mime'])
        {
            case 'image/jpeg':
                $this->image = imagecreatefromjpeg($image);
            break;
            case 'image/png':
                $this->image = imagecreatefrompng($image);
            break;
            case 'image/gif':
                $this->image = imagecreatefromgif($image);
            break;
        }
    }

    private function resize($image)
    {
        var_dump($this);

        $resized = imagescale($this->image, 100, 100);

        imagejpeg($resized, $image);
    }
}