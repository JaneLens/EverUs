<!--最外层导航容器  -->
<div class="site-nav">
    <div class="site-nav__inner">

         <!--主导航 -->
        <nav class="site-nav__menu" role="navigation">
            <ul class="site-nav__dropdown site-nav__dropdown--cat">
                <li class="site-nav__dropdown-item">
                    <a class="site-nav__dropdown-link" href="<?php $this->options->siteUrl(); ?>"><?php _e('Overview'); ?></a>
                </li>
                <?php $this->widget('Widget_Metas_Category_List')->to($category); ?>
                <?php while($category->next()): ?>
                <li class="site-nav__dropdown-item">
                    <a href="<?php $category->permalink(); ?>"
                       title="<?php $category->name(); ?>"
                       class="site-nav__dropdown-link">
                        <?php $category->name(); ?>
                    </a>
                </li>
                <?php endwhile; ?>

                <?php $this->widget('Widget_Contents_Page_List')->to($pages); ?>
                <?php while($pages->next()): ?>
                <li class="site-nav__dropdown-item">
                    <a href="<?php $pages->permalink(); ?>"
                        title="<?php $pages->title(); ?>"
                        class="site-nav__dropdown-link"><?php $pages->title(); ?>
                    </a>
                </li>
                <?php endwhile; ?>
            </ul>
            <button class="daohang">
                <i class="Nug Nug-daohang"></i>
            </button>
        </nav>

          <!--右侧按钮  -->
        <div class="site-nav__actions">
            
            <button class="huojian__toggle hidden">
                <i class="Nug Nug-huojian"></i>
            </button>
        </div>

    </div>
</div>