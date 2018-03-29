<?php
class KuJiang
{
    const err_log = "/data/logs/error_content.log";
    public $pre = "";
    public $next = "";
    public $item_temp = '<li class="item-content" style="width:50%;float:left;background:#fff">
        			<div class="item-inner"><div class="item-title"><a href="{content_url}">{title}</a></div></div>
                        </li>';
    public $content_temp = "";
    public $book_dir = "";
    public function runList($list_url,$book)
    {
        $this->content_temp = ROOTPATH . "/content_temp.html";
        $this->book_dir = $book;
        $content = file_get_contents($list_url);
        preg_match_all("/<ol class=\"zero unstyled kjlist clearfix\">([\s\S]*?)<\/ol>/i",$content,$list_matches);
        if(count($list_matches[1] > 0)) {
            foreach($list_matches[1] as $val) {
                preg_match_all("/<li id=([\s\S]*?)<\/li>/i", $val, $content_matches);
                if(count($content_matches)==2) {
                    foreach($content_matches[1] as $li) {
                        preg_match_all("/<a href=\"([\s\S]*?)\"[\s\S]*?>([\s\S]*?)<\/a>/i", $li, $result_matches);
                        if(count($result_matches) == 3) {
                            $this->runContent($result_matches[1][0],$result_matches[2][0]);
                        }
                    }
                }
            }
        } else {
            exit("list is null");
        }
    }
    public function runContent($content_url,$title)
    {
            $content = @file_get_contents($content_url);
            if(empty($content)) {
                    error_log($content_url . "\r\n",3,self::err_log);
                    return;
            }
            if(empty($title)) {
                error_log($content_url . "-title is null\r\n",3,self::err_log);
                return;
            }
            /*preg_match("/【(\d{1,5})】/i",$title,$title_matches);
            $index = 0;
            if(count($title_matches) != 2) {
                error_log($content_url . "-title is not match\r\n",3,self::err_log);
                return;
            }
            $index = intval($title_matches[1]);*/
	    $url_ary = explode('/',$content_url);
            $index = $url_ary[count($url_ary)-1];
            //preg_match("/<li\sclass=\"kjbook-chapter\">(.*?)<\/li>/i",$content,$title_matches);
            preg_match("/<div\sclass=\"content\">([\s\S]*?)<\/div>/i",$content,$content_matches);
            if(is_array($content_matches) && count($content_matches) == 2) {
                    
            } else {
                    error_log($content_url . "\r\n",3,"/data/logs/lhxd.log");
                    return;
            }
            $rt = preg_replace("/<span\sstyle=\'color:red\'>[\s\S]*/i","",$content_matches[1]);

            $content = file_get_contents($this->content_temp);
            $content = str_replace("{title}", $title, $content);
            $content = str_replace("{content}", $rt, $content);
            $file_name = $this->book_dir . "/" . $index . ".html";
            $relative_path = "./" . $index . ".html";
            if($this->pre) {
                $pre_relative = str_replace($this->book_dir, '.', $this->pre);
                $content = str_replace("{pre}", $pre_relative, $content);
                $pre_content = file_get_contents($this->pre);
                $pre_content = str_replace("{next}", $relative_path, $pre_content);
                file_put_contents($this->pre,$pre_content);
            }
            file_put_contents($file_name,$content);
            $this->pre = $file_name;
            
            $item_content = str_replace(array("{content_url}","{title}"), array($relative_path,$title), $this->item_temp);          
            $list_content = file_get_contents($this->book_dir .  "/list.html");
            $pos = strrpos($list_content,"</ul></div>");
            $list_content = substr_replace($list_content,$item_content,$pos,0);
            file_put_contents($this->book_dir . "/list.html",$list_content);
    }

    public $sdt = "http://www.kujiang.com/book/";
    public function updateList($path,$bookId,$newItem)
    {
        $this->book_dir = $path;
        $this->sdt = $this->sdt . $bookId . "/";
        $dst_list = $this->sdt . "catalog";
        $this->content_temp = ROOTPATH . "/content_temp.html";
        $this->pre = $this->book_dir . "/" . $newItem . ".html";
        $content = file_get_contents($dst_list);
        $newstr = $this->sdt . $newItem;
        $pos = strrpos($content,$newstr);
        if (!$pos) {
            exit("not find newset");
        }
        preg_match_all("/<li id=([\s\S]*?) class=\"one small-tablet third([\s\S]*?)<\/li>/i",$content,$list_matches,PREG_PATTERN_ORDER,$pos);
        if(count($list_matches[2] > 0)) {
            foreach($list_matches[2] as $val) {
                preg_match_all("/<a href=\"([\s\S]*?)\"[\s\S]*?>([\s\S]*?)<\/a>/i", $val, $result_matches);
                if(count($result_matches) == 3) {
                    $this->runContent($result_matches[1][0],$result_matches[2][0]);
                }
            }
        } else {
            exit("list is null");
        }
    }
}

