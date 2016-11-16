<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/18
 * Time: 9:35
 */

class CurlClient {

    /**curl资源
     * @var resource
     */
    private $client;

    /**目标url
     * @var
     */
    private $url;

    /**保存cookie的文件
     *
     */
    private $cookieFile = '../cookie';

    /**保存cookie的文件路径和名称
     * @var
     */
    private $cookieFilepath;

    public function __construct()
    {
        $this->client = curl_init();
    }

    /**设置url
     * @param mixed $url
     * @return $this
     */
    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    /**设置请求方法和请求数据
     * @param $method
     * @param $data
     * @return $this
     */
    public function setMethod($method,$data)
    {
        $method = strtolower($method);

        switch($method)
        {
            case 'post' :
                curl_setopt($this->client,CURLOPT_POST,1);
                curl_setopt($this->client,CURLOPT_POSTFIELDS,$data);
            break;

            default :
                $str = http_build_query($data);
                $this->url .= "?{$str}";
        }
        return $this;
    }

    /**设置代理服务器
     * @param $host
     * @return $this
     */
    public function setProxy($host)
    {
        curl_setopt($this->client,CURLOPT_PROXY,$host);
        return $this;
    }

    /**
     * 是否跟踪本次的cookie信息
     */
    public function setCookie()
    {
        $filename = tempnam($this->cookieFile,'coo');
        $filepath = $this->cookieFile."/{$filename}";
        $this->cookieFilepath = $filepath;
        if($filename)
        {
            curl_setopt($this->client,CURLOPT_COOKIEFILE,$filepath);
        }
        return $this;
    }

    /**是否允许重定向
     *
     */
    public function setLocation()
    {
        curl_setopt($this->client,CURLOPT_FOLLOWLOCATION,true);
        return $this;
    }

    /**
     * 发送请求销毁curl
     */
    public function performRequest()
    {
        curl_setopt($this->client,CURLOPT_URL,$this->url);
        curl_setopt($this->client,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($this->client, CURLOPT_SSL_VERIFYPEER, FALSE);    // https请求 不验证证书和hosts
        curl_setopt($this->client, CURLOPT_SSL_VERIFYHOST, FALSE);

        $res = curl_exec($this->client);
        curl_close($this->client);

        return $res;
    }
}