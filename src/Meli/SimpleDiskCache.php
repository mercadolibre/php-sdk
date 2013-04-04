<?php

namespace Meli;

/**
 *
 * @author Pablo Moretti <pablomoretti@gmail.com>
 */
class SimpleDiskCache
{

    private $basePath;

    public function __construct() {
        if (getenv('PHPSimpleDiskCachePath')) {
            $this -> basePath = getenv('PHPSimpleDiskCachePath');
        } else {
            $this -> basePath = sys_get_temp_dir() . '/' . 'PHPSimpleDiskCache' . '/';
        }
    }

    private function encodeFileName($data) {
        return rtrim(md5(json_encode($data)));
        /* return rtrim(strtr(base64_encode($data), '+/', '-_'), '='); */
    }

    private function getPath($key) {

        $keyMD5 = md5($key);

        return $this -> basePath . (intval(substr($keyMD5, 0, 16)) % 100) . '/' . (intval(substr($keyMD5, 16, 32)) % 100) . '/';

    }

    public function get($key) {

        $resource = $this -> getPath($key) . $this -> encodeFileName($key);

        $data = unserialize(gzinflate(@file_get_contents($resource)));

        if (time() < $data['expires']) {
            return $data['content'];
        }
    }

    public function put($key, $content, $ttl = 0) {

        if ($ttl > 300) {

            $expires = time() + $ttl;

            $path = $this -> getPath($key);

            @mkdir($path, 0777, true);

            $resource = $path . $this -> encodeFileName($key);

            $data = array('content' => $content, 'expires' => $expires);

            file_put_contents($resource, gzdeflate(serialize($data)), FILE_APPEND | LOCK_EX);
        }

    }

}
