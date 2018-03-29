<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

define('ROOTPATH', dirname(__FILE__));
define('FILE_PATH', "/data/www/xs/");
class CreateCommand
{
    /**
     * 1 网站英文名
     * 2 英文名
     * 3 标题
     * 4 列表URL
     * @param type $arg
     */
    public static function run($arg)
    {
        $webName = NULL;
        switch ($arg[1]) {
            case 'kujiang':
                include_once ROOTPATH .'/KuJiang.php';
                $webName = new KuJiang();
                break;
            case '17k':
                
                break;
            default :
                exit("not website");
                break;
        }
        $webPath = FILE_PATH . $arg[2];
        if(!is_dir($webPath)) {
            if(!mkdir($webPath)) {
                exit("mkdir webpath fail3");
            }
        }
        if(!copy(ROOTPATH . '/list_temp.html', $webPath . '/list.html')) {
            exit("copy list_temp faile");
        }
        $list_content = file_get_contents($webPath . '/list.html');
        $list_content = str_replace("{title}", $arg[3], $list_content);
        file_put_contents($webPath . '/list.html',$list_content);
        $webName->runList($arg[4],$webPath);
    }
}
CreateCommand::run($argv);

