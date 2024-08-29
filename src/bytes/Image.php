<?php
namespace scottmasson\elephant\bytes;
use scottmasson\elephant\Autoload;
class Image extends File
{
    public function src($images)
    {
        $this->images = $images;
        $this->format = '.webp';
        $autoload = new Autoload();
        $this->publicPath = $autoload->publicPath.DIRECTORY_SEPARATOR;
        return $this;
    }
    public function save()
    {
        // dump(parent::open('/usr/local/var/www/frame/php/laravel/public/test.md'));

        dump(parent::merge([],[
            'date'  =>  [
                'formatting'    =>  'Y-m-d H:i:s'
            ]
        ],[
            'abc'   =>  123
        ]));

    }
    public function base64(Array $options = [
        // 执行方向 true:base64->image、false:image->base64
        'direction' =>  true,
        // 图片名称 true:随机名称、false只能名称
        'names' =>  true,
        'path'  =>  'uploads/temp/{$y}/{$m}/{$d}/'
    ])
    {
        $options = parent::obj($options);
        list($dataType) = explode(';',$this->images);

        switch ($dataType) {
            case 'data:image/svg+xml':
                $this->format = '.svg';
                break;
            case 'data:image/jpg':
                $this->format = '.jpg';
                break;
            case 'data:image/jpeg':
                $this->format = '.jpeg';
                break;
            case 'data:image/png':
                $this->format = '.png';
                break;
            case 'data:image/ico':
                $this->format = '.ico';
                break;
            case 'data:image/gif':
                $this->format = '.gif';
                break;
        }

        /** 
         ** file path
         */
        $options->path = str_replace(['{$y}','{$m}','{$d}'], [date('y'), date('m'), date('d')], $options->path);

        $dir = $this->publicPath.$options->path;
        $this->open($dir)->mkdirs();

        /** 
         ** file names
         */
        $names = $options->names === true ? md5(time().rand(1,999999999)) : $options->names;
        $imagesNames = $names.$this->format;

        $images = $dir.$imagesNames;
        // return $images;
        file_put_contents($images,base64_decode(str_replace([0=>$dataType.';base64,'], '', $this->images)));

        return $images;

        if ($options->direction) 
        {
            // base64->image

        }else{
            // image->base64
        }
    }
}