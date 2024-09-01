<?php 
declare (strict_types = 1);
namespace scottmasson\elephant\verification;
class Regex extends \scottmasson\elephant\Autoload
{

    protected $pattern = [
        'isBase64'  =>  '/^(data:\s*image\/(svg\+xml|fax|gif|x\-icon|jpeg|pnetvue|png|tiff|webp);base64,)/',
        'imgFormat'   =>  '/.*?(\.png|\.jpg|\.jpeg|\.gif|\.webp|\.ico)$/',
        'imgLinkFormat'   =>  '/^(http)(s)?(\:\/\/).*?(\.png|\.jpg|\.jpeg|\.gif|\.webp|\.ico)$/',
        'videoFormat'  =>  '/.*?(\.mp4|\.wmv|\.webm|\.avi)$/',
        'videoLinkFormat'  =>  '/^(http)(s)?(\:\/\/).*?(\.mp4|\.wmv|\.webm|\.avi)$/'
    ];

    /**
     * Class constructor.
     */
    public function verification($bytes)
    {
        $this->bytes = trim($bytes);
        return $this;
    }
    public function isEmail(){
        return strlen(filter_var($this->bytes,FILTER_VALIDATE_EMAIL)) === 0?false:true;
    }
    public function isNumber(){
        return is_numeric($this->bytes);
    }
    public function isPhone(int $itac = 86) :Array 
    {
        $regexp = [
           86   =>  '/^(134|135|136|137|138|139|147|150|151|152|157|158|159|172|178|182|183|184|187|188|195|198|186|185|155|156|130|131|132|176|175|166|133|153|189|181|180|177|173|199|191)\d{8}$/'
        ];

        if (empty($regexp[$itac])) return [];
        preg_match_all($regexp[$itac],$this->bytes,$result);

        if (empty($result[1][0])) {
            return [];
        }else{
            $operator = false;
            if ($itac === 86) {
                if (in_array($result[1][0],[130,131,132,155,156,157])) $operator = [
                    'website'   =>  '//www.chinaunicom.com.cn',
                    'telephone' =>  10010
                ];
                if (in_array($result[1][0],[133,153,173])) $operator = [
                    'website'   =>  '//www.chinatelecom.com.cn',
                    'telephone' =>  10000
                ];
                if (in_array($result[1][0],[134,135,136,137,138,139,150,151,152,153,154,155,156,157,158,159,188])) $operator = [
                    'website'   =>  '//www.10086.cn',
                    'telephone' =>  10086
                ];
            }
            return $operator;
        } 
    }
    public function removeParam(string $symbol = '?') :String 
    {
      $pos = strpos($this->bytes,$symbol);
      if ($pos !== false) {
            $param = substr($this->bytes,$pos);
            $url = str_replace($param,'',$this->bytes,$count);
            return $url;
      }
      return $this->bytes;
    }
    public function matching(String $pattern)
    {
        return (bool)preg_match($this->pattern[$pattern],$this->bytes);
    }
    public function look(String $pattern){
        preg_match_all($this->pattern[$pattern],$this->bytes,$result);
        return $result;
    }
}