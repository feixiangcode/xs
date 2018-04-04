<?php

/**
 * Description of K17
 *
 * @author chengweidong <chengweidong@smartisan.cn>
 */
class K17 {
    public $pre = "";
    public $next = "";
    public $item_temp = '<li class="item-content" style="width:50%;float:left;background:#fff">
        			<div class="item-inner"><div class="item-title"><a href="{content_url}">{title}</a></div></div>
                        </li>';
    public $content_temp = "";
    public $book_dir = "";
    public $sdt = "http://www.17k.com";
    public function runList($bookId,$book)
    {
        $this->book_dir = $book;
        $this->content_temp = ROOTPATH . "/content_temp.html";
        $list_url = $this->sdt . "/list/{$bookId}.html";
        
        $content = file_get_contents($list_url);
        preg_match_all("/<dl class=\"Volume\">([\s\S]*?)<\/dl>/i",$content,$list_matches);
        if(count($list_matches[1] > 0)) {
            foreach($list_matches[1] as $val) {
                preg_match_all("/<a target=\"_blank\" href=\"([\s\S]*?)\"[\s\S]*?<span class=\"ellipsis\">([\s\S]*?)<\/span>/i", $val, $content_matches);
                if(count($content_matches) == 3) {
                    foreach($content_matches[1] as $key=>$val) {
                        $this->runContent($this->sdt . $val, trim($content_matches[2][$key]));
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
                return;
        }
        if(empty($title)) {
            return;
        }

	    $url_ary = explode('/',$content_url);
        $index = $url_ary[count($url_ary)-1];
        preg_match("/<div class=\"readAreaBox content\"\>[\s\S]*?<div class=\"p\">([\s\S]*?)<\/div>/i",$content,$content_matches);
        
        if(is_array($content_matches) && count($content_matches) == 2) {

        } else {
            return;
        }
        $rt = preg_replace("/<div class=\"author-say\">/i","",$content_matches[1]);

        $content = file_get_contents($this->content_temp);
        $content = str_replace("{title}", $title, $content);
        $content = str_replace("{content}", $rt, $content);
        $file_name = $this->book_dir . "/" . $index;
        $relative_path = "./" . $index;
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
    
    public function updateList($path,$bookId,$newItem)
    {
        $this->book_dir = $path;
        $dst_list = $list_url = $this->sdt . "/list/{$bookId}.html";
        $this->content_temp = ROOTPATH . "/content_temp.html";
        $this->pre = $this->book_dir . "/" . $newItem . ".html";
        $content = file_get_contents($dst_list);
        $newstr = "/chapter/{$bookId}/" . $newItem . ".html";
        $pos = strrpos($content,$newstr);
        if(!$pos) {
            exit("not find newset");
        }
        preg_match_all("/<a target=\"_blank\" href=\"([\s\S]*?)\"[\s\S]*?<span class=\"ellipsis\">([\s\S]*?)<\/span>/i",$content,$list_matches,PREG_PATTERN_ORDER,$pos);
        
        if(count($list_matches == 3)) {
            foreach($list_matches[1] as $key=>$val) {
                $this->runContent($this->sdt . $val, trim($list_matches[2][$key]));
            }
        } else {
            exit("list is null");
        }
    }
}
