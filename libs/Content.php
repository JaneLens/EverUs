<?php
/* 获取全局懒加载图*/
function get_Lazyload($type = true)
{
    return 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7';
}
function article_ccttext($post, $login) {
    $content = $post->content;

    $content = preg_replace_callback('/<img\s+([^>]*?)src="([^"]+)"([^>]*?)alt="([^"]+)"([^>]*?)>/i', function($matches) {
        // 重组属性，保留所有原始属性
        return '<a href="'.$matches[2].'" data-fancybox="gallery">'.
               '<img '.$matches[1].'src="'.$matches[2].'"'.$matches[3].'alt="'.$matches[4].'"'.$matches[5].'>'.
               '</a>';
    }, $content);

    $content = preg_replace('/<p\s*>\s*<a\s+href="([^"]+)"\s+data-fancybox="gallery"\s*>[^<]*<img\s+[^>]*?src="([^"]+)"[^>]*?alt="([^"]+)"[^>]*?>\s*<\/a>\s*<\/p>/is', 
        '<div class="gallery-item up">
            <div class="work-card">
                <a href="$1" data-fancybox="gallery" class="links">
                    <img src="$2" alt="$3">
                </a>
                <div class="mil-descr">
                    <a class="zoom">
                        <button class="Nug Nug-zhankai2 zoom" type="button"></button>
                    </a>
                </div>
            </div>
        </div>', 
        $content);
        
        // 只匹配不含 class 属性的 <a> 标签
        $content = preg_replace(
            '/<a\s+(?![^>]*class=)([^>]*href="([^"]+)")([^>]*)/is',
            '<a $1$3 class="text-link"',
            $content
        );

    // 输出处理后的内容
    echo $content;
}
