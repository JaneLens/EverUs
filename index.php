<?php

/**
 * 这是一个极简、注重日常记录的博客主题<br>
 * "限typecho 1.2版本使用，1.3版本问题自行修改."
 * @package EverUs
 * @author JaneLens
 * @link https://github.com/JaneLens
 */
$this->need('public/include.php');
?>
<div id="swup" class="content transition-fade">
    <!-- 最外层内容区 -->
    <div class="page-content">
        <!-- 横幅：背景 + 计时 + 遮罩 -->
        <header class="banner banner--love" style="--img:url(<?php _getAssets('assets/img/bg.jpg'); ?>);">
            <!-- 遮罩层 -->
            <div class="banner-overlay"></div>
        </header>
        
        <!-- 外层容器 -->
        <div class="cat-group">
            <!-- 每个大分类块 -->
            <ul class="cat-group__list">
                
                <li class="cat-group__item">
                    <!-- 标题组 -->
                    <header class="cat-header up">
                        <h2 class="cat-header__title">
                            New Stories
                        </h2>
                        <div class="cat-header__line"></div>
                        <span class="cat-header__count"></span>
                    </header>
                            
                    <!-- 文章列表 -->
                    <ul class="cat-posts">
                        <?php if ($this->have()): ?>
                        <?php while($this->next()): ?>
                        <li class="cat-posts__item up">
                            <h3 class="line-clamp">
                                <span class="accent"></span>
                                <a class="link hover-link" href="<?php $this->permalink() ?>"><?php $this->title() ?></a>
                            </h3>
                            <p class="line-clamp">
                                <?php $this->excerpt(260, '...'); ?>
                            </p>
                            <a class="link hover-link" href="<?php $this->permalink() ?>">Unfold MY Story</a>
                        </li>
                        <?php endwhile; ?>
                        <?php else: ?>愣着干哈，还不快发文。<?php endif; ?>
                    </ul>
                </li>
                
                <?php
                $tasks = [];
                $raw  = $this->options->compass;
                if ($raw) {
                    foreach (explode("\r\n", $raw) as $line) {
                        $parts = array_pad(explode('||', $line), 4, '');
                        $tasks[] = [
                            'time'  => trim($parts[0]),
                            'title' => trim($parts[1]),
                            'desc'  => trim($parts[2]),
                            'proof' => trim($parts[3]),
                        ];
                    }
                }
                // 只留 4 条最新的
                $tasks = array_slice($tasks, 0, 4);
                ?>
                <?php if ($tasks): ?>
                <li class="cat-group__item">
                    <!-- 标题组 -->
                    <header class="cat-header up">
                        <h2 class="cat-header__title">
                            The Compass
                        </h2>
                        <div class="cat-header__line"></div>
                        <span class="cat-header__count"></span>
                    </header>
                    <!-- 安排表 -->
                    <ul class="schedule-list">
                        <?php foreach ($tasks as $task): ?>
                        <li class="schedule-item up">
                            <header class="schedule-header">
                                <h3 class="schedule-title">
                                    <span class="accent"></span>
                                    <?php echo $task['title'] ?>
                                </h3>
                                <div class="schedule-meta">
                                    <time class="schedule-date"><?php echo $task['time'] ?></time>
                                    <?php if ($task['proof']): ?><a class="text-link hover-link" href="<?php echo $task['proof'] ?>">At Rest</a><?php endif; ?>
                                </div>
                            </header>
                            <p class="schedule-note"><?php echo $task['desc'] ?></p>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </li>
                <?php endif; ?>
                
                <!-- 走心评论 -->
                <?php if ($this->options->zx) : ?>
                <li class="cat-group__item">
                    <!-- 标题组 -->
                    <header class="cat-header up">
                        <h2 class="cat-header__title">
                            Musings
                        </h2>
                        <div class="cat-header__line"></div>
                        <span class="cat-header__count"></span>
                    </header>
                    
                    <ul class="commentator-comment">
                        <div class="commentator-slick up">
                        <?php
                            $coid = Helper::options()->zx;
                            $arr = array_slice(explode(',', $coid), 0, 6);
                            foreach ($arr as $value) {
                                $db = Typecho_Db::get();
                                $select = $db->select()->from('table.comments')->where('coid = ?', $value)->limit(1);
                                $result = $db->fetchAll($select);
                                
                                foreach ($result as $res) {
                                    $id = $res['cid'];
                                    $post_titles = '';
                                    $permalinks = '';
                                    
                                    if ($id) {
                                        $getid = explode(',', $id);
                                        $resu = $db->fetchAll($db->select()->from('table.contents')
                                            ->where('cid in ?', $getid)
                                            ->order('cid', Typecho_Db::SORT_DESC));
                                        
                                        foreach ($resu as $val) {
                                            $val = Typecho_Widget::widget('Widget_Abstract_Contents')->push($val);
                                            $post_titles = htmlspecialchars($val['title']);
                                            $permalinks = $val['permalink'];
                                        }
                                    }
                                    
                                    // 获取头像和时间
                                    $createdTime = date('Y-m-d H:i', $res['created']);
                                    ?>
                                    <li class="comment-chat">
                                        <div class="comment-item">
                                            <header class="comment-header">
                                                <img class="comment-avatar" src="<?php _getAvatarByMail($res['mail']); ?>" alt="<?php echo htmlspecialchars($res['author']); ?>">
                                                <div class="comment-meta">
                                                    <a class="comment-author" href="<?php echo $res['url']; ?>" rel="external nofollow" target="_blank">
                                                        <?php echo htmlspecialchars($res['author']); ?>
                                                    </a>
                                                    <time class="comment-time"><?php echo $createdTime; ?></time>
                                                </div>
                                            </header>
                                        
                                            <p class="comment-content line-clamp">
                                                <?php echo nl2br(htmlspecialchars($res['text'])); ?>
                                            </p>
                                        
                                            <footer class="comment-footer">
                                                《<a class="comment-post-link" href="<?php echo $permalinks; ?>">
                                                    <?php echo $post_titles; ?>
                                                </a>》
                                            </footer>
                                        </div>
                                    </li>
                                    
                                    <?php
                                }
                            }
                        ?>
                        </div>
                        <div class="controls">
                            <button class="slick-custom-next Nug Nug-youbian"></button>
                        </div>
                    </ul>
                    
                </li>
                <?php endif; ?>
                
                <li class="cat-group__item">
                    <header>
                        <div class="cat-header__line"></div>
                        <p class="cat__quote">A piece of my heart, a fragment of a dream, a slice of life—thank you for reading ❤️ . The story continues on <a href="<?php $this->options->siteUrl(); ?>" class="text-link">my blog</a>. I'd be delighted to have your company.</p>
                    </header>
                </li>
            </ul>
        </div>

        
    </div>
    
    <?php $this->need('footer.php');?>
</div>