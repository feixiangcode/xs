<?php

/**
 * Description of UpdateCommand
 *
 * @author chengweidong <chengweidong@smartisan.cn>
 */
define('ROOTPATH', dirname(__FILE__));
define('FILE_PATH', "/data/www/xs/");
error_reporting(E_ALL);
class UpdateCommand 
{
     public static function run($arg)
    {
        $webName = NULL;
        switch ($arg[1]) {
            case 'kujiang':
                include_once ROOTPATH .'/KuJiang.php';
                $webName = new KuJiang();
                break;
            case '17k':
                include_once ROOTPATH .'/K17.php';
                $webName = new K17();
                break;
            default :
                exit("not website");
                break;
        }
        $webPath = FILE_PATH . $arg[2];
        if(!is_dir($webPath)) {
                exit("webpath fail3");
        }
        $new_ary = explode('.', $arg[4]);
        if(!ctype_digit($new_ary[0])) {
            exit("newest error");
        }
        $webName->updateList($webPath,$arg[3],$new_ary[0]);
    }    
}
UpdateCommand::run($argv);
