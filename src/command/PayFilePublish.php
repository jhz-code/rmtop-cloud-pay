<?php


namespace RmTop\Rmpay\command;


use RmTop\Rmpay\lib\PublishFile;
use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\Output;
use think\Exception;

class PayFilePublish extends Command
{



    protected function configure()
    {
        $this->setName('rmtop:publish_pay')
            ->setDescription('publish_pay ');
    }


    /**
     * 执行数据
     * @param Input $input
     * @param Output $output
     * @return int|void|null
     * @throws \ReflectionException
     */
    protected function execute(Input $input, Output $output)
    {

        try{
            PublishFile::PublishFileToSys($output);//发布文件
            $output->writeln("all publish successfully！");
        }catch (Exception $exception){
            $output->writeln($exception->getMessage());
        }

    }






}