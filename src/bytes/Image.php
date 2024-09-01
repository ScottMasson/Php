<?php
declare (strict_types = 1);
namespace scottmasson\elephant\bytes;
class Image extends File
{
    /**
     * Class constructor.
     */
    public function src(string $images = '')
    {
        $this->images = $images;
        $this->format = '.webp';
        return $this;
    }
    public function download(Array $options = []): object
    {

        $options = parent::obj(parent::merge([],[
            'names' =>  true,
            'path'  =>  'uploads/temp/{$y}/{$m}/{$d}/'
        ],$options));

        if ($this->verification($this->images)->matching('imgLinkFormat') === false) return $this->obj($this->ERROR(10010));

        /** 
         ** image path
        */
        $options->path = str_replace(['{$y}','{$m}','{$d}'], [date('y'), date('m'), date('d')], $options->path);

        $dir = $this->publicPath.DIRECTORY_SEPARATOR.$options->path;
        $this->open($dir)->mkdirs();

        /** 
         ** image names
         */

        $this->images = $this->verification($this->images)->removeParam();
        $this->format = '.'.$this->open($this->images)->getExtension();
        
        $names = $options->names === true ? md5(time().rand(1,999999999)) : $options->names;
        $imagesNames = $names.$this->format;
        $systemImages = $dir.$imagesNames;

        /** 
         ** image download
         */
        
        ob_start();
        @readfile($this->images);
        $images = ob_get_contents();
        ob_end_clean();

        $fp2 = @fopen($systemImages,'a');
        fwrite($fp2,$images);
        fclose($fp2);
        unset($images,$this->images);

        return $this->obj([
            'system'    =>  $systemImages,
            'website'   =>  DIRECTORY_SEPARATOR.$options->path.$imagesNames
        ]);

    }
    public function base64(Array $options = []): object
    {
        $options = parent::obj(parent::merge([],[
            'direction' =>  true,
            'names' =>  true,
            'path'  =>  'uploads/temp/{$y}/{$m}/{$d}/'
        ],$options));

        if ($options->direction) 
        {
            /** 
             ** base64->image
             *? @date 24/08/30 19:50
             */

            list($dataType) = explode(';',$this->images);

            switch ($dataType) {
                case 'data:image/svg+xml': $this->format = '.svg'; break;
                case 'data:image/jpg': $this->format = '.jpg'; break;
                case 'data:image/jpeg': $this->format = '.jpeg'; break;
                case 'data:image/png': $this->format = '.png'; break;
                case 'data:image/ico': $this->format = '.ico'; break;
                case 'data:image/gif': $this->format = '.gif'; break;
                default: return $this->obj($this->ERROR(10010)); break;
            }

            /** 
             ** file path
            */
            $options->path = str_replace(['{$y}','{$m}','{$d}'], [date('y'), date('m'), date('d')], $options->path);

            $dir = $this->publicPath.DIRECTORY_SEPARATOR.$options->path;
            $this->open($dir)->mkdirs();
            /** 
             ** file names
            */
            $names = $options->names === true ? md5(time().rand(1,999999999)) : $options->names;
            $imagesNames = $names.$this->format;

            $images = $dir.$imagesNames;
            
            file_put_contents($images,base64_decode(str_replace([0=>$dataType.';base64,'], '', $this->images)));

            return $this->obj([
                'system'    =>  $images,
                'website'   =>  DIRECTORY_SEPARATOR.$options->path.$imagesNames
            ]);

        }else{
            /** 
             ** image->base64
             *? @date 24/08/30 19:50
             */

            $this->images = $this->verification($this->images)->removeParam();
            $this->format = $this->open($this->images)->getExtension();

            if ($this->verification($this->images)->matching('imgLinkFormat')) {
                $images = $this->download();
                $this->images = $images->system;
            }

            $imagesize = getimagesize($this->images);
            
            $base64Coding = fread(fopen($this->images, 'r'), filesize($this->images));

            $base64 = 'data:' . $imagesize['mime'] . ';base64,' . chunk_split(base64_encode($base64Coding));

            return $this->obj([
                'coding'    =>  $base64
            ]);
        }
    }
}