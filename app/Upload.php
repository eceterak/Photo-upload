<?php

namespace app;

use DateTime;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Exception;

class Upload 
{
    private $files = array();
    private $allowed_extensions = array();
    private $max_size = 350000;
    private $dir = './assets/upload';
    public $errors = array();
    private $resizeEngine = null;
    private $logger = null;

    public function __construct($files, $resizeEngine)
    {
        $this->files = $files;
        $this->resizeEngine = $resizeEngine;
        $this->logger = new Logger('image-resize');

        $this->logger->pushHandler(new StreamHandler('./logs/entries.log', Logger::INFO));
    }
    
    public function setAllowedExtensions(array $extensions)
    {
        $this->allowed_extensions = $extensions;
    }

    public function setMaxFileSize($size)
    {
        $this->max_size = (int) $size;
    }

    public function setUploadDirectory($dir)
    {
        $this->dir = $dir;
    }

    public function persist()
    {
        $_SESSION['resized'] = '';

        try {
            $this->checkIfValid();
            $this->upload();
            $this->resize();
        }
        catch(Exception $e)
        {
            $this->setErrors($e->getMessage());
        }
    }

    public function resize()
    {
        $resized = array();
        
        for($i = 0; $i < count($this->files['name']); $i++)
        {
            if(!empty($this->files['name'][$i])) {
                $img = realpath($this->dir.'/'.$this->files['name'][$i]);

                if(file_exists($img) && is_file($img)) 
                {
                    if($this->resizeEngine->persist($img))
                    {
                        $resized[] = $this->dir.'/'.$this->files['name'][$i];

                        $this->logger->info('Image resized', [
                            'name' => $this->files['name'][$i],
                            'new_size' => $this->files['size'][$i],
                            'new_dimensions' => getimagesize($img)[3],
                            'date' => new DateTime()
                        ]);
                    }
                }
            }
        }

        if(!empty($resized)) $_SESSION['resized'] = $resized;
    }

    public function checkIfValid()
    {
        for($i = 0; $i < count($this->files['name']); $i++)
        {
            if(!empty($this->files['name'][$i])) 
            {
                if($this->checkExtension($this->files['tmp_name'][$i], $this->files['name'][$i]) == false)
                {
                    throw new Exception('Invalid broken image file. Allowed extensions are: '.join(", ", $this->allowed_extensions));
                }
                elseif($this->files['size'][$i] > $this->max_size)
                {
                    throw new Exception('File is too large. Maximum file size is '.$this->max_size);
                }
            }
            else 
            {
                throw new Exception('You need to choose a file.');
            }
        }
    }

    private function checkExtension($tmpName, $name) 
    {
        return (boolean) getimagesize($tmpName) && in_array($this->getExtension($name), $this->allowed_extensions);
    }

    private function getExtension($file)
    {
        return pathinfo($file)['extension'];
    }

    public function upload()
    {
        if(!file_exists($this->dir))
        {
            mkdir($this->dir);
        }

        for($i = 0; $i < count($this->files['name']); $i++)
        {
            if(!empty($this->files['name'][$i])) {

                $dir = $this->dir.'/'.$this->files['name'][$i];

                if(move_uploaded_file($this->files['tmp_name'][$i], $dir))
                {
                    $this->logger->info('Image uploaded', [
                        'name' => $this->files['name'][$i],
                        'size' => $this->files['size'][$i],
                        'dimensions' => getimagesize($dir)[3],
                        'date' => new DateTime()
                    ]);
                }
                else
                {
                    throw new Exception('Upload error. Try again.');
                }
            }
        }
    }

    private function setErrors($error)
    {
        $this->errors = true;

        $_SESSION['upload_error'] = $error;
    }
}