<?php
/**扩展CI视图操作
 * Created by PhpStorm.
 * User: Cshiwei
 * Date: 2016/9/12
 * Time: 19:17
 */
class My_view {

    private $CI;

    private $active_class = "active";
    private $def_index = 'nav_1';
    private $template = 'default';

    private $head = array();
    private $foot ='';
    private $body ='';

    public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->load->config('nav');
    }

    public function setTemplate($template = 'default')
    {
        $this->template = $template;
        return $this;
    }


    public function setHead($active,$head=array())
    {
        $active OR $active = $this->def_index;

        $nav = $this->CI->config->item('nav');
        $item = array();
        foreach($nav as $key=>$val)
        {
            if($val['id'] == $active)
            {
                $nav[$key]['active_cla'] = $this->active_class;
            }

            $item[$key] = $val['sort'];
        }
        array_multisort($item,SORT_DESC,$nav);

        $this->head['_nav'] = $nav;
        ! is_array($head) OR $data = array_merge($this->head,$head);

        $this->head = $data;
        return $this;
    }

    public function addHead($head)
    {
        ! is_array($head) OR $this->head = array_merge($this->head,$head);
        return $this;
    }

    public function setBody($body='')
    {
        $this->body = $body;
        return $this;
    }

    public function setFoot($foot='')
    {
        $this->foot = $foot;
        return $this;
    }

    //加载视图文件
    public function show($view,$data='')
    {
        $this->CI->load->view("{$this->template}/public/header",$this->head);
        $data = $data ? $data : $this->body;
        $this->CI->load->view("{$this->template}/$view",$data);
        $this->CI->load->view("{$this->template}/public/footer",$this->foot);
    }

    //加载单个视图
    public function perShow($view,$data=array())
    {
        $this->CI->load->view("{$this->template}/{$view}",$data);
    }

    public function getHead()
    {
        return $this->head;
    }

}