<?php
$this->need('public/include.php');
?>
<div id="swup" class="content transition-fade post-content">
    
    <div class="page-main">
        <!-- 最外层内容区 -->
        <div class="page-img_content">
            <div class="page-content">
                <div class="post">
                    <header class="post__header">
                        <h1 class="post__title"><?php $this->title() ?></h1>
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