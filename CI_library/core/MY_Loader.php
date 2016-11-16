<?php
/**自定义扩展loader
 * Created by PhpStorm.
 * User: Cshiwei
 * Date: 2016/11/14
 * Time: 14:12
 */

class MY_Loader extends CI_Loader
{
    private $model_folder='public/';

    public function p_model($model, $name = '', $db_conn = FALSE)
    {
        if($model)
            $this->model($this->model_folder.$model,$name,$db_conn);

        return $this;
    }
}