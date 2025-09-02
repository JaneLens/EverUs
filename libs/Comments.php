<?php
class Comments
{

    /**
     * 评论加@
     * @param $coid
     */
    public static function getPermalinkFromCoid($coid) {
        $db = Typecho_Db::get();
        $row = $db->fetchRow($db->select('author')->from('table.comments')->where('coid = ? AND status = ?', $coid, 'approved'));
        if (empty($row)) return '';
        return '<a class="comments-at" href="#comment-'.$coid.'"> @ '.$row['author'].'</a>';
    }

     /**
     * 私密内容正则替换回调函数
     * @param $matches
     * @return bool|string
     */
    public static function secretContentParseCallback($matches)
    {
        if ($matches[1] == '[' && $matches[6] == ']') {
            return substr($matches[0], 1, -1);
        }
        return '<div class="secret">' . $matches[5] . '</div>';
    }
    public static function parseContentPublic($content)
    {
        
        return $content;
    }

    /**
     * 解析文章页面的评论内容
     * @param $content
     * @param boolean $isLogin 是否登录
     * @param $rememberEmail
     * @param $currentEmail
     * @param $parentEmail
     * @param bool $isTime
     * @return mixed
     */
    public static function postCommentContent($content, $isLogin, $rememberEmail, $currentEmail, $parentEmail, $isTime = false)
    {
        $flag = true;
        if (strpos($content, '[secret]') !== false) {
            $pattern = self::get_shortcode_regex(array('secret'));
            $content = preg_replace_callback("/$pattern/", array('Comments', 'secretContentParseCallback'), $content);
            if ($isLogin || ($currentEmail == $rememberEmail && $currentEmail != "") || ($parentEmail == $rememberEmail && $rememberEmail != "")) {
                $flag = true;
            } else {
                $flag = false;
            }
        }
        if ($flag) {  
             if (strpos($content, '{lamp/}') !== false) {
                $content = strtr($content, array(
                    "{lamp/}" => '<span class="joe_lamp"></span>',
                ));
            }
            if (strpos($content, '{x}') !== false || strpos($content, '{ }') !== false) {
                $content = strtr($content, array(
                    "{x}" => '<input type="checkbox" class="joe_checkbox" checked disabled></input>',
                    "{ }" => '<input type="checkbox" class="joe_checkbox" disabled></input>'
                ));
            }
            if (strpos($content, '{music') !== false) {
                $content = preg_replace('/{music-list([^}]*)\/}/SU', '<joe-mlist $1></joe-mlist>', $content);
                $content = preg_replace('/{music([^}]*)\/}/SU', '<joe-music $1></joe-music>', $content);
            }
            if (strpos($content, '{mp3') !== false) {
                $content = preg_replace('/{mp3([^}]*)\/}/SU', '<joe-mp3 $1></joe-mp3>', $content);
            }
            if (strpos($content, '{bilibili') !== false) {
                $content = preg_replace('/{bilibili([^}]*)\/}/SU', '<joe-bilibili $1></joe-bilibili>', $content);
            }
            if (strpos($content, '{dplayer') !== false) {
                $player = Helper::options()->CustomPlayer ? Helper::options()->CustomPlayer : Helper::options()->themeUrl . '/libs/player.php?url=';
                $content = preg_replace('/{dplayer([^}]*)\/}/SU', '<joe-dplayer player="' . $player . '" $1></joe-dplayer>', $content);
            }
            if (strpos($content, '{mtitle') !== false) {
                $content = preg_replace('/{mtitle([^}]*)\/}/SU', '<joe-mtitle $1></joe-mtitle>', $content);
            }
            if (strpos($content, '{abtn') !== false) {
                $content = preg_replace('/{abtn([^}]*)\/}/SU', '<joe-abtn $1></joe-abtn>', $content);
            }
            if (strpos($content, '{cloud') !== false) {
                $content = preg_replace('/{cloud([^}]*)\/}/SU', '<joe-cloud $1></joe-cloud>', $content);
            }
            if (strpos($content, '{anote') !== false) {
                $content = preg_replace('/{anote([^}]*)\/}/SU', '<joe-anote $1></joe-anote>', $content);
            }
            if (strpos($content, '{dotted') !== false) {
                $content = preg_replace('/{dotted([^}]*)\/}/SU', '<joe-dotted $1></joe-dotted>', $content);
            }
            if (strpos($content, '{message') !== false) {
                $content = preg_replace('/{message([^}]*)\/}/SU', '<joe-message $1></joe-message>', $content);
            }
            if (strpos($content, '{progress') !== false) {
                $content = preg_replace('/{progress([^}]*)\/}/SU', '<joe-progress $1></joe-progress>', $content);
            }
            if (strpos($content, '{hide') !== false) {
                $db = Typecho_Db::get();
                $hasComment = $db->fetchAll($db->select()->from('table.comments')->where('cid = ?', $post->cid)->where('mail = ?', $post->remember('mail', true))->limit(1));
                if ($hasComment || $login) {
                    $content = strtr($content, array("{hide}" => "", "{/hide}" => ""));
                } else {
                    $content = preg_replace('/{hide[^}]*}([\s\S]*?){\/hide}/', '<joe-hide></joe-hide>', $content);
                }
            }
            if (strpos($content, '{card-default') !== false) {
                $content = preg_replace('/{card-default([^}]*)}([\s\S]*?){\/card-default}/', '<section style="margin-bottom: 15px"><joe-card-default $1><span class="_temp" style="display: none">$2</span></joe-card-default></section>', $content);
            }
            if (strpos($content, '{callout') !== false) {
                $content = preg_replace('/{callout([^}]*)}([\s\S]*?){\/callout}/', '<section style="margin-bottom: 15px"><joe-callout $1><span class="_temp" style="display: none">$2</span></joe-callout></section>', $content);
            }
            if (strpos($content, '{alert') !== false) {
                $content = preg_replace('/{alert([^}]*)}([\s\S]*?){\/alert}/', '<section style="margin-bottom: 15px"><joe-alert $1><span class="_temp" style="display: none">$2</span></joe-alert></section>', $content);
            }
            if (strpos($content, '{card-describe') !== false) {
                $content = preg_replace('/{card-describe([^}]*)}([\s\S]*?){\/card-describe}/', '<section style="margin-bottom: 15px"><joe-card-describe $1><span class="_temp" style="display: none">$2</span></joe-card-describe></section>', $content);
            }
            if (strpos($content, '{tabs') !== false) {
                $content = preg_replace('/{tabs}([\s\S]*?){\/tabs}/', '<section style="margin-bottom: 15px"><joe-tabs><span class="_temp" style="display: none">$1</span></joe-tabs></section>', $content);
            }
            if (strpos($content, '{card-list') !== false) {
                $content = preg_replace('/{card-list}([\s\S]*?){\/card-list}/', '<section style="margin-bottom: 15px"><joe-card-list><span class="_temp" style="display: none">$1</span></joe-card-list></section>', $content);
            }
            if (strpos($content, '{timeline') !== false) {
                $content = preg_replace('/{timeline}([\s\S]*?){\/timeline}/', '<section style="margin-bottom: 15px"><joe-timeline><span class="_temp" style="display: none">$1</span></joe-timeline></section>', $content);
            }
            if (strpos($content, '{collapse') !== false) {
                $content = preg_replace('/{collapse}([\s\S]*?){\/collapse}/', '<section style="margin-bottom: 15px"><joe-collapse><span class="_temp" style="display: none">$1</span></joe-collapse></section>', $content);
            }
            if (strpos($content, '{gird') !== false) {
                $content = preg_replace('/{gird([^}]*)}([\s\S]*?){\/gird}/', '<section style="margin-bottom: 15px"><joe-gird $1><span class="_temp" style="display: none">$2</span></joe-gird></section>', $content);
            }
            if (strpos($content, '{copy') !== false) {
                $content = preg_replace('/{copy([^}]*)\/}/SU', '<joe-copy $1></joe-copy>', $content);
            }
            if (strpos($content, '{copy') !== false) {
                $content = preg_replace('/{copy([^}]*)\/}/SU', '<joe-copy $1></joe-copy>', $content);
            }
            if (strpos($content, '[post') !== false) {
                $content = preg_replace('/\[post title="(.*?)" intro="(.*?)" url="(.*?)"(.*?)\]/sm', '
                <a target="_blank" href="${3}" class="LinkCard">
                    <span class="LinkCard-content">
                        <span class="LinkCard-text">
                            <span class="LinkCard-title">${1}</span>
                            <span class="LinkCard-excerpt text-ell">${2}</span>
                            <span class="LinkCard-meta">
                                <span style="display:inline-flex;">
                                <svg fill="currentColor" viewBox="0 0 24 24" width="17" height="17"><path d="M6.77 17.23c-.905-.904-.94-2.333-.08-3.193l3.059-3.06-1.192-1.19-3.059 3.058c-1.489 1.489-1.427 3.954.138 5.519s4.03 1.627 5.519.138l3.059-3.059-1.192-1.192-3.059 3.06c-.86.86-2.289.824-3.193-.08zm3.016-8.673l1.192 1.192 3.059-3.06c.86-.86 2.289-.824 3.193.08.905.905.94 2.334.08 3.194l-3.059 3.06 1.192 1.19 3.059-3.058c1.489-1.489 1.427-3.954-.138-5.519s-4.03-1.627-5.519-.138L9.786 8.557zm-1.023 6.68c.33.33.863.343 1.177.029l5.34-5.34c.314-.314.3-.846-.03-1.176-.33-.33-.862-.344-1.176-.03l-5.34 5.34c-.314.314-.3.846.03 1.177z" fill-rule="evenodd"></path></svg>
                                </span>
                                <span>${3}</span>
                            </span>
                        </span>
                        <span class="LinkCard-imageCell">
                        <span class="LinkCard-image LinkCard-image-default">
                            <svg fill="currentColor" viewBox="0 0 24 24" width="32" height="32"><path d="M11.991 3C7.023 3 3 7.032 3 12s4.023 9 8.991 9C16.968 21 21 16.968 21 12s-4.032-9-9.009-9zm6.237 5.4h-2.655a14.084 14.084 0 0 0-1.242-3.204A7.227 7.227 0 0 1 18.228 8.4zM12 4.836A12.678 12.678 0 0 1 13.719 8.4h-3.438A12.678 12.678 0 0 1 12 4.836zM5.034 13.8A7.418 7.418 0 0 1 4.8 12c0-.621.09-1.224.234-1.8h3.042A14.864 14.864 0 0 0 7.95 12c0 .612.054 1.206.126 1.8H5.034zm.738 1.8h2.655a14.084 14.084 0 0 0 1.242 3.204A7.188 7.188 0 0 1 5.772 15.6zm2.655-7.2H5.772a7.188 7.188 0 0 1 3.897-3.204c-.54.999-.954 2.079-1.242 3.204zM12 19.164a12.678 12.678 0 0 1-1.719-3.564h3.438A12.678 12.678 0 0 1 12 19.164zm2.106-5.364H9.894A13.242 13.242 0 0 1 9.75 12c0-.612.063-1.215.144-1.8h4.212c.081.585.144 1.188.144 1.8 0 .612-.063 1.206-.144 1.8zm.225 5.004c.54-.999.954-2.079 1.242-3.204h2.655a7.227 7.227 0 0 1-3.897 3.204zm1.593-5.004c.072-.594.126-1.188.126-1.8 0-.612-.054-1.206-.126-1.8h3.042c.144.576.234 1.179.234 1.8s-.09 1.224-.234 1.8h-3.042z"></path></svg>
                        </span>
                        </span>
                    </span>
                </a>
                ', $content);
            }
      
                
            $content = Comments::parseContentPublic($content);
            return ''.$content.'';
        } else {
            if ($isTime) {
                echo Comments::getPermalinkFromCoid($comments->parent);
                return '<div class="secret">此条为悄悄话，仅发布者可见</div>';
            } else {
                echo Comments::getPermalinkFromCoid($comments->parent);
                return '<div class="secret">此条为悄悄话，仅发布者可见</div>';
            }
        }
        
    }

     /**
     * 获取匹配短代码的正则表达式
     * @param null $tagnames
     * @return string
     * @link https://github.com/WordPress/WordPress/blob/master/wp-includes/shortcodes.php#L254
     */
    public static function get_shortcode_regex($tagnames = null)
    {
        global $shortcode_tags;
        if (empty($tagnames)) {
            $tagnames = array_keys($shortcode_tags);
        }
        $tagregexp = join('|', array_map('preg_quote', $tagnames));
        // WARNING! Do not change this regex without changing do_shortcode_tag() and strip_shortcode_tag()
        // Also, see shortcode_unautop() and shortcode.js.
        // phpcs:disable Squiz.Strings.ConcatenationSpacing.PaddingFound -- don't remove regex indentation
        return
            '\\['                                // Opening bracket
            . '(\\[?)'                           // 1: Optional second opening bracket for escaping shortcodes: [[tag]]
            . "($tagregexp)"                     // 2: Shortcode name
            . '(?![\\w-])'                       // Not followed by word character or hyphen
            . '('                                // 3: Unroll the loop: Inside the opening shortcode tag
            . '[^\\]\\/]*'                   // Not a closing bracket or forward slash
            . '(?:'
            . '\\/(?!\\])'               // A forward slash not followed by a closing bracket
            . '[^\\]\\/]*'               // Not a closing bracket or forward slash
            . ')*?'
            . ')'
            . '(?:'
            . '(\\/)'                        // 4: Self closing tag ...
            . '\\]'                          // ... and closing bracket
            . '|'
            . '\\]'                          // Closing bracket
            . '(?:'
            . '('                        // 5: Unroll the loop: Optionally, anything between the opening and closing shortcode tags
            . '[^\\[]*+'             // Not an opening bracket
            . '(?:'
            . '\\[(?!\\/\\2\\])' // An opening bracket not followed by the closing shortcode tag
            . '[^\\[]*+'         // Not an opening bracket
            . ')*+'
            . ')'
            . '\\[\\/\\2\\]'             // Closing shortcode tag
            . ')?'
            . ')'
            . '(\\]?)';                          // 6: Optional second closing brocket for escaping shortcodes: [[tag]]
        // phpcs:enable
    }

    
    
    
    
    
    
}