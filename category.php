<?php
$this->need('public/include.php');
?>

<div id="swup" class="content transition-fade category">
    
    <div class="page-main">
        <!-- 最外层内容区 -->
        <ul class="category-list">
            <?php if ($this->have()): ?>
            <?php while($this->next()): ?>
            <li class="category-item">
                <img src="<?php echo _getThumbnails($this)[0] ?>" class="category-image" alt="Category Image">
                <hgroup class="category-info">
                    <div class="category-title">
                        <h3 class="category-heading line-clamp"><a href="<?php $this->permalink() ?>"><?php $this->title() ?></a>	</h3>
                        <a href="<?php $this->permalink() ?>" class="category-link">Unfold Our Story</a>
                    </div>
                </hgroup>
            </li>
            <?php endwhile; ?>
            
            <?php else: ?>暂无文章<?php endif; ?>
        </ul>
    </div>
    <!-- 分页 -->
    <?php $this->pageNav('<div class="pagination-prve"><i class="Nug Nug-youbian"></i><span> PREV </span></div>', '<div class="pagination-next"><span> NEXT </span><i class="Nug Nug-youbian"></i></div>'); ?>
    
    <?php $this->need('footer.php');?>
</div>