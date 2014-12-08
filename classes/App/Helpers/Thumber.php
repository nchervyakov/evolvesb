<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 23.10.2014
 * Time: 13:16
 */

namespace App\Helpers;


use App\Pixie;
use PHPThumb\GD;

class Thumber
{
    protected $variants = [];
    protected $thumbDir;
    protected $defaultVariant = [
        'maxWidth' => 100,
        'maxHeight' => 100,
        'format' => 'jpg',
        'quality' => 80
    ];

    public function __construct(Pixie $pixie, array $options = array())
    {
        $this->pixie = $pixie;

        $variants = $this->pixie->config->get('parameters.thumb_variants');
        $defaults = [
            'dir' => $this->pixie->config->get('parameters.thumb_path'),
            'variants' => [
                'default' => $this->defaultVariant
            ]
        ];

        if (is_array($variants)) {
            $options = array_merge($defaults, ['variants' => $variants]);
        }

        $this->thumbDir = $options['dir'];
        $this->addVariants($options['variants']);
    }

    public function getThumb($path, $variant = 'default')
    {
        $var = $this->variants[$variant];

        if (!$var) {
            throw new \Exception('Incorrect thumb type: ' . $variant);
        }

        $filePath = $this->getThumbPath($path, $variant);
        if (file_exists($filePath) && is_file($filePath)) {
            return realpath($filePath);
        }

        try {
            $thumb = new GD($path);

        } catch (\Exception $e) {
            return false;
        }

        $operation = $var['operation'] ?: 'resize';
        $thumb->$operation($var['maxWidth'], $var['maxHeight']);
        $thumb->setOptions(['jpegQuality' => $var['quality']]);

        $dir = dirname($filePath);

        if (!file_exists($dir) || !is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $thumb->save($filePath, $var['format']);

        return realpath($filePath);
    }

    public function setVariants(array $variants = array())
    {
        $this->variants = [];
        $this->addVariants($variants);
    }

    public function addVariants(array $variants)
    {
        foreach ($variants as $name => $options) {
            $this->addVariant($name, $options);
        }
    }

    public function addVariant($name, array $options = [])
    {
        $options = array_merge($this->defaultVariant, $options);
        $this->variants[$name] = $options;
    }

    public function getThumbPath($path, $variant)
    {
        $var = $this->variants[$variant];
        $sha = sha1($path . serialize($var));
        return $this->thumbDir . substr($sha, 0, 2) . '/' . substr($sha, 2) . '_'
                . $var['maxWidth'] . 'x' . $var['maxHeight'] . '.' .$var['format'];
    }

    function getImageThumb($path, $variant)
    {
        $rootDir = realpath(__DIR__.'/../../../web');
        $fullPath = $rootDir . '/products_pictures/' . $path;

        try {
            $thumb = $this->getThumb($fullPath, $variant);
            //
            if (strpos($thumb, $rootDir) == 0) {
                $thumb = substr($thumb, strlen($rootDir));
            }

            return str_replace('\\', '/', $thumb);
        } catch (\Exception $e) {
            return null;
        }
    }
}