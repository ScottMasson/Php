<?php
declare (strict_types = 1);
namespace scottmasson\elephant\bytes;
use SplFileInfo;
use SplFileObject;
class File extends \scottmasson\elephant\base\Arr
{

    public function open(string $file = '')
    {
        $this->splinfo = new SplFileInfo($file);
        return $this;
    }
    public function mkdirs($mode = 0777){
        try {
            if ($this->exists() === false) {
                if (mkdir($this->splinfo->getPathname().DIRECTORY_SEPARATOR, $mode, true)) {
                    $this->chmod($mode);
                }
            }
        } catch (\Throwable $th) {}
        return $this;
    }
    public function chmod($fileMode){
        if ($this->exists()) {
            // The file pattern must come from type octal. By converting octal to decimal, or vice versa
            // We want to make sure that the given value is octal. Any non-octal number will be detected.
            if (decoct(octdec($fileMode)) != $fileMode) {
                // Chmod fails because the permissions given are not from type octal.
                return false;
            }

            // Converts a given octal string to an octal integer
            if (is_string($fileMode)) {
                $fileMode = intval($fileMode, 8);
            }

            switch ($fileMode) {
                case 0600: // file owner read and write;
                case 0640: // file owner read and write; owner group read
                case 0660: // file owner read and write; owner group read and write
                case 0604: // file owner read and write; everbody read
                case 0606: // file owner read and write; everbody read and write
                case 0664: // file owner read and write; owner group read and write; everbody read
                case 0666: // file owner read and write; owner group read and write; everbody read and write
                case 0700: // file owner read, execute and write;
                case 0740: // file owner read, execute and write; owner group read
                case 0760: // file owner read, execute and write; owner group read and write
                case 0770: // file owner read, execute and write; owner group read, execute and write
                case 0704: // file owner read, execute and write; everbody read
                case 0706: // file owner read, execute and write; everbody read and write
                case 0707: // file owner read, execute and write; everbody read, execute and write
                case 0744: // file owner read, execute and write; owner group read; everbody read
                case 0746: // file owner read, execute and write; owner group read; everbody read and write
                case 0747: // file owner read, execute and write; owner group read; everbody read, execute and write
                case 0754: // file owner read, execute and write; owner group read and execute; everbody read
                case 0755: // file owner read, execute and write; owner group read and execute; everbody read and execute
                case 0756: // file owner read, execute and write; owner group read and execute; everbody read and write
                case 0757: // file owner read, execute and write; owner group read and execute; everbody read, execute and write
                case 0764: // file owner read, execute and write; owner group read and write; everbody read
                case 0766: // file owner read, execute and write; owner group read and write; everbody read and write
                case 0767: // file owner read, execute and write; owner group read and write; everbody read, execute and write
                case 0774: // file owner read, execute and write; owner group read, execute and write; everbody read
                case 0775: // file owner read, execute and write; owner group read, execute and write; everbody read, execute and write
                case 0776: // file owner read, execute and write; owner group read, execute and write; everbody read and write
                case 0777: // file owner read, execute and write; owner group read, execute and write; everbody read, execute and write
                    break;
                default:
                    $fileMode = 0777;
            }

            return chmod($this->getPathname(), $fileMode);
        }
        return false;
    }
    public function getPerms()
    {
        return substr(sprintf('%o', $this->splinfo->getPerms()), -4);
    }
    public function time(string $formatting = 'Y-m-d H:i:s'): object
    {
        $getATime = $this->splinfo->getATime();
        $getCTime = $this->splinfo->getCTime();
        $getMTime = $this->splinfo->getMTime();
        return parent::obj([
            'atime'  =>  [
                'timestamp'  =>  $getATime,
                'date'  =>  date($formatting,$getATime)
            ],
            'ctime'  =>  [
                'timestamp'  =>  $getCTime,
                'date'  =>  date($formatting,$getCTime)
            ],
            'mtime'  =>  [
                'timestamp'  =>  $getMTime,
                'date'  =>  date($formatting,$getMTime)
            ]
        ]);
    }
    public function exists(){
        $return = false;
        if ($this->splinfo->isFile() || $this->splinfo->isDir()) $return = true;
        return $return;
    }
    public function isAbsolute(){
        $filepath = $this->getPathname();
        return isset($filepath[0]) && $filepath[0] == '/';
    }
    public function isLink(){
        return $this->splinfo->isLink();
    }
    public function isFile(){
        if ($this->splinfo->isFile()) return $this->splinfo->getRealPath();
        return false;

    }
    public function isWritable(){
        return $this->splinfo->isWritable();
    }
    public function isDir(){
        return $this->splinfo->isDir();
    }
    public function isExecutable()
    {
        return $this->splinfo->isExecutable();
    }
    public function isReadable()
    {
        return $this->splinfo->isReadable();
    }
    public function printDir($dir = '') {
        $i = 0;
        if ($dir === '') {
            $dir = $this->getPathname();   
        }
        $dirs[] = $dir;
        
        $scandir = scandir($dir);
        foreach ($scandir as $entry) {
            if ($entry != '.' && $entry != '..') {
                $fullpath = $dir.DIRECTORY_SEPARATOR.$entry;
                if (is_dir($fullpath) === true) {
                    $dirs = array_merge($dirs,$this->printDir($fullpath));
                }else{
                    $dirs[] = $fullpath;
                }
            }
        }
        return $dirs;
    }
    public function getFileName(){
        return $this->splinfo->getFilename();
    }
    public function getBaseName($suffix = ''){
        return $this->splinfo->getBasename($suffix);
    }
    public function getRealPath()
    {
        return $this->splinfo->getRealPath();
    }
    public function getPath()
    {
        return $this->splinfo->getPath();
    }
    public function getPathInfo()
    {
        return $this->splinfo->getPathInfo();
    }
    public function getPathname(){
        return $this->splinfo->getPathname();
    }
    public function getExtension()
    {
        return $this->splinfo->getExtension();
    }
    public function getFileInfo()
    {
        return $this->splinfo->getFileInfo();
    }
    public function getInode()
    {
        return $this->splinfo->getInode();
    }
    public function getSize()
    {
        return $this->splinfo->getSize();
    }
    public function getType()
    {
        return $this->splinfo->getType();
    }
    public function getGroup()
    {
        return posix_getgrgid($this->splinfo->getGroup());
    }
    public function getOwner()
    {
        return posix_getpwuid($this->splinfo->getOwner());
    }
    public function continueToWrite($string = '')
    {
        if ($this->isWritable()) 
        {
            $fileobj = $this->splinfo->openFile('a');
            return $fileobj->fwrite($string);
        }
    }
    public function delete()
    {
        if ($this->isFile()) {
            // 删除文件和符号链接
            return unlink($this->getPathname());
        } elseif ($this->isDir()) {
            $dirs = $this->printDir();
            foreach (array_reverse($dirs) as $v) {
                if (is_dir($v)) {
                    rmdir($v);
                }else{
                    unlink($v);
                }
            }
            return true;
        }else{
            // 操作失败
            return false;
        }
    }
    public function deleteEmptyDir()
    {
        $dirs = $this->printDir();
        $i = 0;
        foreach (array_reverse($dirs) as $v) {
            if (is_dir($v)) {
                if (count(scandir($v)) === 2) {
                    $i++;
                    rmdir($v);
                }
            }
        }
        return $i;
    }

    /** 
     ** obj
     *? @date 23/02/26 17:26
     */
    public function openSplObj($mode = 'w+'){
        $isFile = $this->splinfo->getRealPath();
        if ($isFile !== false) {
            $this->splobj = new SplFileObject($isFile,$mode);
            return $this;
        }
        return false;
    }
    public function write($string){
        if ($this->openSplObj() === false) return false;
        return $this->splobj->fwrite($string);
    }
    public function clear(){
        if ($this->openSplObj() === false) return false;
        $this->splobj->ftruncate(0);
        return $this;
    }
    public function current(Array $c = [])
    {

        if ($this->openSplObj('a+') === false) return false;

        $content = '';

        $file = $this->splobj;

        $c = 
        parent::obj(parent::merge([],[
            'isLineNumber'   =>  false,
            'textView'  =>  false
        ],$c));

        foreach ($file as $k => $line) {
            if ($c->isLineNumber === true) $content .= ($file->key() + 1) . ': ';
            $content .=  $file->current();
            if ($c->textView === true) $content .= '<br>';
        }

        return $content;
    }
    public function fgetc()
    {
        if ($this->openSplObj('r+') === false) return false;
        $content = '';

        while (false !== ($char = $this->splobj->fgetc())) {
            $content .= "$char<br>";
        }
        return $content;
    }
    public function fgetcsv()
    {
        if ($this->openSplObj('a+') === false) return false;
        if ($this->splinfo->getExtension() !== 'csv') return false;
        $lines = [];

        while (!$this->splobj->eof()) {
            $fgetcsv = $this->splobj->fgetcsv();
            if (!empty(array_filter($fgetcsv))) $lines[] = $fgetcsv;
        }
        
        return empty($lines)?false:$lines;
    }
    /** 
     ** 
     *? @date 24/08/30 23:12
     *  @param myParam1 Explain the meaning of the parameter...
     *  @param myParam2 Explain the meaning of the parameter...
     *! @return 
     */
    /** 
    ** Load json file
    *? @date 24/06/29 16:41
    *  @param string $operationType Data processing operations such as path, get, set
    *  @param array $data Data to be saved
    *! @return array|string|boolean
    */
   public function json(
        $operationType = 'get',
        array $data = [])
    {

        $jsonFile =  $this->getRealPath();

        if ($operationType === 'path') return $jsonFile;
        // return Array
        if ($operationType === 'get') return json_decode(file_get_contents($jsonFile),true);
        // set JSON
        if ($operationType === 'set') file_put_contents($jsonFile,json_encode($data));

        // Gets the specified node data
        if (is_string($operationType)) 
        {
        $operationType = explode('::',$operationType);
        if ($operationType[0] === 'get') 
        {
            unset($operationType[0]);
            return $this->baseArr()->findLadderNode($this->config('get'),$operationType);
        }
        return false;
        }

        if (is_object($operationType)) 
        {
        $data = json_encode($operationType);
        file_put_contents($jsonFile,$data);
        return $data;
        }
        return false;
    }
}