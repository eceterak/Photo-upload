<?php

namespace app;

class Resize
{
    private $image = null;
    private $image_info = null;
    private $width = null;
    private $height = null;

    public function __construct($width, $height)
    {
        $this->width = (int) $width;
        $this->height = (int) $height;
    }

    public function persist($image)
    {
        $this->init($image);
        $resized = $this->resize();
        
        if($resized)
        {
            $this->createImage($resized, $image);
        }
        else
        {
            throw new Exception('Image can not be resized.');
        }


        return true;
    }

    private function init($image)
    {
        $this->image_info = getimagesize($image);

        switch($this->image_info['mime'])
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

    private function resize()
    {
        $width = $this->width ? $this->width : $this->image_info[0];
        $height = $this->height ? $this->height : $this->image_info[1];
        
        $resized = imagescale($this->image, $width, $height);

        return $resized;
    }

    private function createImage($resized, $image)
    {
        switch($this->image_info['mime'])
        {
            case 'image/jpeg':
                $this->image = imagejpeg($resized, $image);
            break;
            case 'image/png':
                $this->image = imagepng($image);
            break;
            case 'image/gif':
                $this->image = imagegif($image);
            break;
        }
    }
}