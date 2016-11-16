<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/13
 * Time: 13:05
 */
class MY_Output extends CI_Output{

    public function __construct()
    {
        parent::__construct();
    }

    public function ajaxReturn($data,$code=200,$type='json')
    {
        $temp = array();
        switch ($type)
        {
            case 'json':
                $this->set_content_type('application/json');
                $temp['code'] = $code;
                $temp['data'] = $data;
                $this->set_output(json_encode($temp));
             break;

            case 'html':
                $this->set_content_type('html/text');
                $this->set_output($data);
            break;
        }
    }
}