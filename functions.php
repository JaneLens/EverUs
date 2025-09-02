<?php
require_once('libs/CommentPush.php');
require_once('libs/Comments.php');
require_once('libs/Content.php');
/* 初始化主题 */
function themeInit(Widget_Archive $archive){
    
    //暴力解决访问加密文章会被 pjax 刷新页面的问题
    if ($archive->hidden) header('HTTP/1.1 200 OK');
    //评论回复楼层最高999层.这个正常设置最高只有7层
    Helper::options()->commentsMaxNestingLevels = 999;
    //强制评论关闭反垃圾保护
    Helper::options()->commentsAntiSpam = false;
    //将最新的评论展示在前
    Helper::options()->commentsOrder = 'DESC';
    //关闭检查评论来源URL与文章链接是否一致判断
    Helper::options()->commentsCheckReferer = false;
    // 强制开启评论markdown
    Helper::options()->commentsMarkdown = '1';
    Helper::options()->commentsHTMLTagAllowed .= '<img class src alt><div class>';
    if ($archive->is('category')) {
        $archive->parameter->pageSize = 9; // 所有分类页统一条数
    }
}
/* 获取资源路径 */
function _getAssets($assets, $type = true)
{
  $assetsURL = "";
  // 是否本地化资源
  if (Helper::options()->AssetsURL) {
    $assetsURL = Helper::options()->AssetsURL . '/' . $assets;
  } else {
    $assetsURL = Helper::options()->themeUrl . '/' . $assets;
  }
  if ($type) echo $assetsURL;
  else return  $assetsURL;
}

// 通过邮箱生成头像地址
function _getAvatarByMail($mail)
{
    $gravatarsUrl = Helper::options()->CustomAvatarSource ? Helper::options()->CustomAvatarSource : 'https://gravatar.helingqi.com/avatar/';
    $mailLower = strtolower($mail);
    $md5MailLower = md5($mailLower);
    $qqMail = str_replace('@qq.com', '', $mailLower);
    if (strstr($mailLower, "qq.com") && is_numeric($qqMail) && strlen($qqMail) < 11 && strlen($qqMail) > 4) {
        echo 'https://thirdqq.qlogo.cn/g?b=qq&nk=' . $qqMail . '&s=100';
    } else {
        echo $gravatarsUrl . $md5MailLower . '?d=mm';
    }
};
// 文章字数统计
function art_count ($cid){
    $db=Typecho_Db::get ();
    $rs=$db->fetchRow ($db->select ('table.contents.text')->from ('table.contents')->where ('table.contents.cid=?',$cid)->order ('table.contents.cid',Typecho_Db::SORT_ASC)->limit (1));
    $text = preg_replace("/[^\x{4e00}-\x{9fa5}]/u", "", $rs['text']);
    echo mb_strlen($text,'UTF-8');
}
// 点击数量
function get_post_view($archive)
{
    $cid    = $archive->cid;
    $db     = Typecho_Db::get();
    $prefix = $db->getPrefix();
    if (!array_key_exists('views', $db->fetchRow($db->select()->from('table.contents')))) {
        $db->query('ALTER TABLE `' . $prefix . 'contents` ADD `views` INT(10) DEFAULT 0;');
        echo 0;
        return;
    }
    $row = $db->fetchRow($db->select('views')->from('table.contents')->where('cid = ?', $cid));
    if ($archive->is('single')) {
        $views = Typecho_Cookie::get('extend_contents_views');
        if(empty($views)){
            $views = array();
        }else{
            $views = explode(',', $views);
        }
        if(!in_array($cid,$views)){
            $db->query($db->update('table.contents')->rows(array('views' => (int) $row['views'] + 1))->where('cid = ?', $cid));
            array_push($views, $cid);
            $views = implode(',', $views);
            Typecho_Cookie::set('extend_contents_views', $views); //记录查看cookie
        }
    }
    echo $row['views'];
}
// 文章缩略图
function _getThumbnails($item)
{
    $result = [];
    $pattern = '/\<img.*?src\=\"(.*?)\"[^>]*>/i';
    $patternMD = '/\!\[.*?\]\((http(s)?:\/\/.*?(jpg|jpeg|gif|png|webp))/i';
    $patternMDfoot = '/\[.*?\]:\s*(http(s)?:\/\/.*?(jpg|jpeg|gif|png|webp))/i';

    /* 如果填写了自定义缩略图，则优先显示填写的缩略图 */
    if ($item->fields->thumb) {
        $fields_thumb_arr = explode("\r\n", $item->fields->thumb);
        foreach ($fields_thumb_arr as $list) {
            $result[] = $list;
        }
    }

    /* 如果匹配到正则，则继续补充匹配到的图片 */
    if (preg_match_all($pattern, $item->content, $thumbUrl)) {
        foreach ($thumbUrl[1] as $list) {
            $result[] = $list;
        }
    }
    if (preg_match_all($patternMD, $item->content, $thumbUrl)) {
        foreach ($thumbUrl[1] as $list) {
            $result[] = $list;
        }
    }
    if (preg_match_all($patternMDfoot, $item->content, $thumbUrl)) {
        foreach ($thumbUrl[1] as $list) {
            $result[] = $list;
        }
    }

    /* 如果上面的数量不足3个，则直接补充一张默认图 */
    if (sizeof($result) < 3) {
        $result[] = _getAssets('assets/thumb/' . rand(1, 18) . '.jpg', false);
    }

    return $result;
}
//总访问量
function theAllViews()
{
    $db = Typecho_Db::get();
    $row = $db->fetchAll('SELECT SUM(VIEWS) FROM `typecho_contents`');
        echo number_format($row[0]['SUM(VIEWS)']);
}
// 文章发布时间
function formatTime($time){
        $text = '';
        $time = intval($time);
        $ctime = time();
        $t = $ctime - $time; //时间差
        if ($t < 0) {
            return date('Y-m-d', $time);
        }
        $y = date('Y', $ctime) - date('Y', $time);//是否跨年
        switch ($t) {
            case $t == 0:
                $text = '刚刚';
                break;
            case $t < 60://一分钟内
                $text = $t . '秒前';
                break;
            case $t < 3600://一小时内
                $text = floor($t / 60) . '分钟前';
                break;
            case $t < 86400://一天内
                $text = floor($t / 3600) . '小时前'; // 一天内
                break;
            case $t < 2592000://30天内
                if($time > strtotime(date('Ymd',strtotime("-1 day")))) {
                    $text = '昨天';
                } elseif($time > strtotime(date('Ymd',strtotime("-2 days")))) {
                    $text = '前天';
                } else {
                    $text = floor($t / 86400) . '天前';
                }
                break;
            case $t < 31536000 && $y == 0://一年内 不跨年
                $m = date('m', $ctime) - date('m', $time) -1;
                if($m == 0) {
                    $text = floor($t / 86400) . '天前';
                } else {
                    $text = $m . '个月前';
                }
                break;
            case $t < 31536000 && $y > 0://一年内 跨年
                $text = (11 - date('m', $time) + date('m', $ctime)) . '个月前';
                break;
            default:
                $text = (date('Y', $ctime) - date('Y', $time)) . '年前';
                break;
    }
    return $text;
}
// 添加主题后台设置
function themeConfig($form) {
    $zx = new Typecho_Widget_Helper_Form_Element_Text(
        'zx',
        NULL,
        '2022-07-01',
        _t('走心评论'),
        _t('多个coid中间用,分割，如111,222,333这样即可')
    );
    $form->addInput($zx);

    // 任务表
    $compass = new Typecho_Widget_Helper_Form_Element_Textarea(
        'compass',
        NULL,
        NULL,
        _t('任务表'),
        _t('格式：时间线 || 标题 || 说点 || 证明  <br>
            注意：可留空 比如未完成 2025 - 2026 ||  || 拿到大专毕业证 ||  ')
    );
    $form->addInput($compass);
    
    // 评论
     $CommentMail = new Typecho_Widget_Helper_Form_Element_Select(
    'CommentMail',
    array('off' => '关闭（默认）', 'on' => '开启'),
    'off',
    '是否开启评论邮件通知',
    '介绍：开启后评论内容将会进行邮箱通知 <br />
         注意：此项需要您完整无错的填写下方的邮箱设置！！ <br />
         其他：下方例子以QQ邮箱为例，推荐使用QQ邮箱'
  );
  $form->addInput($CommentMail->multiMode());

  $CommentMailHost = new Typecho_Widget_Helper_Form_Element_Text(
    'CommentMailHost',
    NULL,
    NULL,
    '邮箱服务器地址',
    '例如：smtp.qq.com'
  );
  $form->addInput($CommentMailHost->multiMode());
  
    $CommentSMTPSecure = new Typecho_Widget_Helper_Form_Element_Select(
    'CommentSMTPSecure',
    array('ssl' => 'ssl（默认）', 'tsl' => 'tsl'),
    'ssl',
    '加密方式',
    '介绍：用于选择登录鉴权加密方式'
  );
  $form->addInput($CommentSMTPSecure->multiMode());

  $CommentMailPort = new Typecho_Widget_Helper_Form_Element_Text(
    'CommentMailPort',
    NULL,
    NULL,
    '邮箱服务器端口号',
    '例如：465'
  );
  $form->addInput($CommentMailPort->multiMode());
  
  $CommentMailFromName = new Typecho_Widget_Helper_Form_Element_Text(
    'CommentMailFromName',
    NULL,
    NULL,
    '发件人昵称',
    '例如：帅气的象拔蚌'
  );
  $form->addInput($CommentMailFromName->multiMode());

  $CommentMailAccount = new Typecho_Widget_Helper_Form_Element_Text(
    'CommentMailAccount',
    NULL,
    NULL,
    '发件人邮箱',
    '例如：2323333339@qq.com'
  );
  $form->addInput($CommentMailAccount->multiMode());

  $CommentMailPassword = new Typecho_Widget_Helper_Form_Element_Text(
    'CommentMailPassword',
    NULL,
    NULL,
    '邮箱授权码',
    '介绍：这里填写的是邮箱生成的授权码 <br>
         获取方式（以QQ邮箱为例）：<br>
         QQ邮箱 > 设置 > 账户 > IMAP/SMTP服务 > 开启 <br>
         其他：这个可以百度一下开启教程，有图文教程'
  );
  $form->addInput($CommentMailPassword->multiMode());




}