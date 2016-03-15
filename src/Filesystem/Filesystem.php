<?php
/**
 * Project: lavaproto
 * User: stefanriedel
 * Date: 13.01.16
 * Time: 16:26
 */

namespace Lava83\LavaProto\Filesystem;

use \Illuminate\Filesystem\Filesystem as BaseFileSystem;
use Lava83\LavaProto\Exceptions\FilesystemException;

/**
 * Class Filesystem
 * @package Lava83\LavaProto\Filesystem
 */
class Filesystem extends BaseFileSystem
{

    /**
     *
     * return simple class infos like namespace and class name in a file
     *
     * @param string $file_path path to the class
     * @throws FilesystemException
     */
    public function getClassInfo($file_path) {
        if(!$this->exists($file_path)) {
            throw new FilesystemException(sprintf("The file '%s' doesn't exists.", $file_path));
        }
        $fp = $this->open($file_path);
        $cls = $namespace = $buffer = '';
        $i = 0;
        while(!$cls) {
            if($this->iseof($fp)) {
                break;
            }
            $buffer .= $this->read($fp);
            $tokens = token_get_all($buffer);

            if (strpos($buffer, '{') === false) continue;

            for (;$i<count($tokens);$i++) {
                if ($tokens[$i][0] === T_NAMESPACE) {
                    for ($j=$i+1;$j<count($tokens); $j++) {
                        if ($tokens[$j][0] === T_STRING) {
                            $namespace .= '\\'.$tokens[$j][1];
                        } else if ($tokens[$j] === '{' || $tokens[$j] === ';') {
                            break;
                        }
                    }
                }

                if ($tokens[$i][0] === T_CLASS) {
                    for ($j=$i+1;$j<count($tokens);$j++) {
                        if ($tokens[$j] === '{') {
                            $cls = $tokens[$i+2][1];
                        }
                    }
                }
            }
        }
        return [$namespace, $cls];
    }

    /**
     *
     * wrapper for phps fopen
     *
     * @param $file_path
     * @param string $mode
     * @param null $include_path
     * @param null $context
     * @return resource
     */
    public function open($file_path, $mode = 'r', $include_path = null, $context = null) {
        if($include_path == null && $context == null) {
            return fopen($file_path, $mode);
        } elseif($include_path != null && $context == null) {
            return fopen($file_path, $mode, $include_path);
        } elseif($include_path != null && $context != null) {
            return fopen($file_path, $mode, $include_path, $context);
        }
    }

    /**
     *
     * wrapper if phps feof
     *
     * @param $fp
     * @return bool
     */
    public function iseof($fp) {
        return feof($fp);
    }

    /**
     *
     * wrapper of phps fread
     *
     * @param $fp
     * @param int $length
     * @return string
     */
    public function read($fp, $length=1024) {
        return fread($fp, $length);
    }

}