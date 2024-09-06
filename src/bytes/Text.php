<?php 
declare (strict_types = 1);
namespace scottmasson\elephant\bytes;
class Text extends Image
{
    /**
     * Class constructor.
     */
    public function document(
        string $texts = '',
        string $format = 'html'
    )
    {
        if ($format === 'file') 
        {
            
            $file = $this->open($this->rootPath.DIRECTORY_SEPARATOR.$texts);

            $texts = 
            $file->current([
                'isLineNumber'   =>  false,
                'textView'  =>  false
            ]);

            $format = $file->getExtension();
        }

        if ($format === 'md') {
            # 将md转html
        }

        $this->texts = $texts;
        $this->format = $format;
        return $this;
    }
    
    public function htmlUrlEncryption(array $options = [])
    {
        $options = $this->obj($this->merge([],[
            'domain' =>  'test.com',
            'urls'   =>  '//www.{$domain}/link/{$url}'
        ],$options));

        $JWT = new \scottmasson\elephant\algorithm\JSONWebToken;

        if ($this->texts === '') return $this->texts;
        $links = $this->verification($this->texts)->look('htmlAHrefLink');

        if (empty($links[1])) return $this->texts;
        $urls = str_replace('{$domain}',$options->domain,$options->urls);
        $original = [];
        $new = [];
        foreach ($links[1] as $url) {
            if (strpos($url,$options->domain) === false && $this->verification($url)->matching('http')) {
                $original[] = 'href="'.$url.'"';
                $new[] = 'href="'.str_replace('{$url}',$JWT->result(['direction' =>  true,'bytes' =>  $url]),$urls).'" target="_blank"';
            }
        }
        return str_replace($original,$new,$this->texts);
    }
    public function markdownToHTML(array $options = [])
    {
        $options = 
        $this->obj($this->merge([],[
            'resource'  =>  [
                'path'  =>  $this->smVendorPath . DIRECTORY_SEPARATOR . 'text' . DIRECTORY_SEPARATOR . 'markdown' . DIRECTORY_SEPARATOR
            ],
            'terminal'  =>  [
                // false 不开启 terminal template
                // terminal-macos.html 启动 terminal template
                'template'  =>  false
            ],
            'model'  =>  [
                'module'  =>  []
            ],
            // 'ad'  => false,
            'ad'    =>  [
                // 在文本的30行里添加该广告
                30   => [
                    'adsense'   => 'google',
                    'size'   => 'responsive',
                    'id'  => '6142688203',
                    // false 不显示广告
                    // true 显示广告
                    'display'   =>  true
                ]
            ],
            'safety'    =>  [
                // 预防
                'prevent'   =>  [
                    // 预防采集
                    // false 不开启 预防采集
                    // safety-prevent-collection.json 启动 预防采集
                    'collection'    =>  false
                ]
            ],
            'toc'   =>  [
                // false 不开启 toc
                // html 开启toc 返回 html code
                // json 开启toc 返回 json
                'type'  =>  false
            ]
        ],$options));

        /** 
         ** terminal
         *? @date 24/09/06 21:33
         */
        if ($options->terminal->template !== false) {
            $options->terminal->template = 
            file_get_contents($options->resource->path . $options->terminal->template);
        }
        /** 
         ** safety.prevent.collection
         *? @date 24/09/06 21:57
         */
        if ($options->safety->prevent->collection !== false) {
            $options->safety->prevent->collection = 
            $this->open($options->resource->path.$options->safety->prevent->collection)->json('get');
        }
        /** 
         ** ad
         *? @date 24/09/06 23:14
         */
        if ($options->ad !== false) {
            $ad = (array)$options->ad;
            $AdKeys = array_keys($ad);
            $options->ad = [
                'keys'  =>  array_keys((array)$options->ad),
                'list'  =>  $ad
            ];
        }

        $to = new Text\Markdown\To($options);
        
        $result = [
            'html'  =>  $to->text($this->texts)
        ];
        
        if ($options->toc->type !== false) $result['toc'] = $to->contentsList($options->toc->type);

        return $this->obj($result);
    }
}
