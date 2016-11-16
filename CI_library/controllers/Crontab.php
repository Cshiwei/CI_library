<?php
/**
 *常驻内存，启动后台服务
 * 计划任务，定期关闭该进程，释放内存，并重新启动
 * Created by PhpStorm.
 * User: csw
 * Date: 2016/9/5
 * Time: 10:27
 */

class Crontab extends CI_Controller{

    private $register = array();

    private $p_task = array();

    private $log_path='cron/';

    public  function __construct()
    {
        parent::__construct();
        $this->load->helper('log');
    }

    public function begin()
    {
        $id = getmypid();
        log_msg('parent_id',$this->log_path,"开启主进程ID:{$id}");
        $this->regist();
        $this->start();
    }

    /** 检测服务器负载情况，重新启动主进程
     * 计划任务每一分钟检测一次
     */
    public function exe()
    {
        $cmd = 'ps aux | grep "crontab exe" | grep -v "grep" | grep -v "/bin/sh -c"';
        exec($cmd,$res);

        if(count($res) > 1)
        {
            log_msg('',$this->log_path,"抓取任务正在执行，等待下次检测...\n".var_export($res,true));
            exit();
        }
        else
            $this->begin();
    }

    private function regist()                      //注册服务
    {
        $this->twitter_regist();
        $this->facebook_regist();
    }

    private function twitter_regist()
    {
        $arr = array(
            'name'      => 'twitter抓取服务',
            'log_path'  =>  $this->log_path.'twitter/',
            'pathtobin' => '/usr/bin/php',
            'arg'       => 'php /data/www/Overseas/SearService/index.php twitter start',
            'exp'       => '600',
        );


        $this->register['twitter'] = $arr;
    }

    private function facebook_regist()
    {
        $arr = array(
            'name'      => 'facebook抓取服务',
            'log_path'  => $this->log_path.'facebook/',
            'pathtobin' => '/usr/bin/php',
            'arg'       => 'php /data/www/Overseas/SearService/index.php facebook start',
            'exp'       => '600',
        );

        $this->register['facebook'] = $arr;
    }


    private function start($register=array())
    {
        $register = $register ? $register : $this->register;
        foreach($register as $key=>$val)         //根据已经注册的服务开启子进程
        {
            $pid = pcntl_fork();
            $id = getmypid();
            if ($pid)                           //主进程
            {
                echo $id;
                log_msg('',$this->log_path,"开启子进程{$pid},分配任务 {$val['name']}");
            }
            else                                //子进程 执行twitter和facebook的抓取服务
            {
                do
                {
                    exec($val['arg']);                      //会产生新的子进程
                    $name = $val['name'];
                    $log_path = $val['log_path'];
                    $exp = $val['exp'];

                    log_msg('',$log_path,"{$name} 任务执行完毕，需要等待 {$exp}秒...");
                    sleep($exp);

                }while(true);

                exit();
            }
        }

        do
        {
            $pid = pcntl_wait($status);
            log_msg('',$this->log_path,"子进程{$pid}释放资源");
        }while($pid);

        log_msg('',$this->log_path,"程序运行终止，等待重新开启");
    }
}



