<?php
$this->need('public/include.php');
?>
<div id="swup" class="content transition-fade post-content">
    
    <div class="page-main">
        <!-- 最外层内容区 -->
        <div class="page-img_content">
            <?php
                preg_match_all('/<img.*?src=[\"\'](.*?)[\"\'].*?>/i', $this->content, $matches);
                $images = $matches[1];
                $imageCount = count($images);
            ?>
            <!-- 横幅 -->
            <?php if ($imageCount > 0): ?>
                <?php if ($imageCount <= 1): ?>
                <header class="post-images">
                    <?php foreach ($images as $image): ?>
                        <li class="gallery-item work-card">
                            <a href="<?php echo $image; ?>" data-fancybox="gallery" class="">
                                <img src="<?php echo $image; ?>" alt="<?php $this->title() ?>">
                            </a>
                            <div class="mil-descr">
                                <a class="zoom">
                                    <button class="Nug Nug-zhankai2 zoom" type="button"></button>
                                </a>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </header>
                <?php endif; ?>
            <?php endif; ?>
            <div class="page-content">
                <div class="post">
                    <header class="post__header">
                        <h1 class="post__title"><?php $this->title() ?></h1>
                        <div class="post__meta">
                            <span class="post__category"><?php $this->category(' / '); ?></span>
                        </div>
                    </header>
            
                    <article class="post__content">
                        <?php article_ccttext($this, $this->user->hasLogin()) ?>
                    </article>
                    
                    <!-- 分割线 -->
                    <div class="post-divider up"></div>
                        
                    <!-- 文章信息 -->
                    <div class="post-footer up">
                        <div class="post-author"><span>author：</span><?php $this->author(); ?></div>
                        <div class="post-date"><span>COMPLETION DATE：</span><?php $this->date(); ?></div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="page-comments">
            <?php $this->need('public/comments.php');?>
        </div>
    </div>
    
    <?php $this->need('footer.php');?>
</div>