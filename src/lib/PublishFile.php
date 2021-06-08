<?php


namespace RmTop\Rmpay\lib;


class PublishFile
{

    /**
     * 发布到系统
     */
    static function PublishFileToSys($output){
        $destination = root_path() . '/database/migrations/';
        if(!is_dir($destination)){
            mkdir($destination, 0755, true);
        }
        $source = dirname(__DIR__).'/database/migrations/';
        $handle = dir($source);
        while($entry=$handle->read()) {
            if(($entry!=".")&&($entry!="..")){
                if(is_file($source.$entry)){
                    copy($source.$entry, $destination.$entry);
                    $output->writeln("$source.$entry --- publish successfully！");
                }
            }
        }

    }

}