<?php
namespace scottmasson\elephant\bytes\Image;
class Base64 extends \File
{
    public static function toImage(){
        return parent::obj(['a'=>time()]);
    }
    /** 
     ** 保存base64数据为图片
     *? @date 23/07/11 13:11
     *  @param $base64  base64编码
     *  @param $savePath    保存路径  entrance/uploads/temp/
     *  @param $date    保存路径日期
     *  @param $FileName    自动生产文件名
     *  @param $enableNotCloudStorage    是否强制开启或关闭 os
     *! @return 
     */
	public function base64ToImage(array $options = []){
        $options = 
        $this->pctco->utils->Arr->merge([],[
            'savePath' => 'uploads/temp/',
            'date' => ['y','m','d'],
            'fileName'   => true,
            'enableNotCloudStorage' =>  true
        ],$options);
        

        $options = $this->pctco->utils->Arr->obj($options);
        
		//匹配出图片的格式
        $result = $this->pctco->safety->verify->open($this->file)->rule('format.img.base64')->find();

        if (!empty($result[2][0])){
            // 格式 png
            $ext = $result[2][0];
            if ($ext === 'svg+xml') $ext = 'svg';
            $abc = '88888';
            if ($options->fileName === true) {
                $fileName = md5(time().rand(1,999999999)).'.'.$ext;
            }else{
                $fileName = $options->fileName.'.'.$ext;
            }

            $saveDate = '';
            foreach ($options->date as $v) {
                $saveDate .= date($v).'/';
            }

            $savePath = $this->entranceDir.'/'.$options->savePath.$saveDate;

            //创建保存目录
            if(!file_exists($savePath) && !mkdir($savePath,0777,true)){
                return [
                    'status'    =>  'error',
                    'code'  =>  101,
                    'tips'   => 'error',
                    'message'   => 'Create a save directory',
                    'system_message'    =>  'Create a save directory'
                ];
            }

            $savePath = $savePath.$fileName;
            if (file_put_contents($savePath,base64_decode(str_replace($result[1], '', $this->file)))){

                $absolute = '/'.$options->savePath.$saveDate.$fileName;

                if ($options->enableNotCloudStorage) {
                    if ($this->config->use === 1) {
                        $storage = new cloud\storage\Processor($this->config);
                        $upload = $storage->upload($options->savePath.$saveDate.$fileName);
                        if ($upload === true) {
                            $absolute = $this->config->domain.$absolute;
                            $this->pctco->files->utils('file')->utils->open($savePath,[])->delete();
                        }
                    }
                }

                return [
                    'status'    =>  'success',
                    'code'  =>  200,
                    'tips'   => 'success',
                    'message'   => 'Data request success',
                    'system_message'    =>  'Data request success',
                    'data'  =>  [
                        'date'  =>  $saveDate,
                        'name'  =>  $fileName,
                        'file'  =>  [
                            'relative'   =>   $saveDate.$fileName,
                            'system'   =>   $savePath,
                            'absolute'   =>   $absolute,
                        ]
                    ]
                ];
            }else{
                return [
                    'status'    =>  'error',
                    'code'  =>  102,
                    'tips'   => 'error',
                    'message'   => 'base64 Conversion failed',
                    'system_message'    =>  'base64 Conversion failed'
                ];
            }
        }else{
            return [
                'status'    =>  'error',
                'code'  =>  103,
                'tips'   => 'error',
                'message'   => 'Link format error',
                'system_message'    =>  'Link format error'
            ];
        }
	}
    /** 
     ** 图片 转 base64
     *? @date 23/07/11 13:10
     *  @param $image 图片文件 本地图片或远程链接图片
     *  @param myParam2 Explain the meaning of the parameter...
     *! @return base64
     */
    public function imageToBase64() {
        $image = $this->pctco->utils->Request->removeParam($this->file);
        $checkUrl = $this->pctco->safety->verify->open($this->file)->rule('html.href.link')->check();

        $base64 = '';
        if($checkUrl){
            $link = $this->open($image)->save([
                'path'   => 'uploads/temp/',
                'date'   => ['y','m'],
                'enableNotCloudStorage' => false
            ]);
            if ($link['status'] !== 'success') return $link;
            $image = $link['data']['file']['system'];
        }
        $ext = strrchr($image,'.');

        $info = getimagesize($image);

        $data = fread(fopen($image, 'r'), filesize($image));

        $base64 = 'data:' . $info['mime'] . ';base64,' . chunk_split(base64_encode($data));

        if ($checkUrl) {
            $this->pctco->files->utils('file')->utils->open($image,[])->delete();
        }
        return $base64;
    }
}
