
<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; 
 $GLOBALS['isLogin'] = $this->user->hasLogin();
 $GLOBALS['rememberEmail'] = $this->remember('mail',true);
 $GLOBALS['convertip'] = $this->options->convertip;
?>
    <?php function threadedComments($comments, $options) {
        $commentClass = '';$group = '';
            if ($comments->authorId) {
                if ($comments->authorId == $comments->ownerId) {
                    $group = '博主';
                        $commentClass .= 'By-authors';  //如果是文章作者的评论添加 .comment-by-author 样式
                    } else {
                        $group = '游客';
                        $commentClass .= 'By-user';  //如果是评论作者的添加 .comment-by-user 样式
                    }
                } 
        $commentLevelClass = $comments->_levels > 0 ? ' comment-child' : ' comment-parent';  //评论层数大于0为子级，否则是父级
        $depth = $comments->levels +1;
        if ($comments->url) {
            $author = '<a href="' . $comments->url . '"' . '" target="_blank"' . ' rel="external nofollow">' . $comments->author . '</a>';
        } else {
            $author = $comments->author;
        }
    ?>
        <li class="m-comments-list <?php 
            if ($comments->levels > 0) {
                echo 'comment-child';
                $comments->levelsAlt(' comment-level-odd', ' comment-level-even');
            } else {
                echo 'comment-parent';
            }
            $comments->alt(' comment-odd', ' comment-even');
            ?> depth-<?php echo $depth ?>" id="li-<?php $comments->theId(); ?>">
                <!--class  -->
                <div id="<?php $comments->theId(); ?>" class="comment-item">
                    <div class="comment-card" id="<?php echo $commentClass; ?>">
                
                        <!-- 头像区域 -->
                        <div class="comment-avatar-wrap">
                            <!-- 头部：作者 + 时间 -->
                            <header class="comment-header">
                                <div class="comment-author">
                                    <img class="avatar-img" width="48" height="48"
                                         src="<?php _getAvatarByMail($comments->mail); ?>" alt="头像" />
                                    <a id="createReply"
                                       class="comment-reply-btn"
                                       rel="nofollow"
                                       href="#回复给<?php $comments->author(false); ?>"
                                       onclick="createReply('<?php $comments->coid(); ?>','<?php $comments->author(false); ?>')"
                                       title="回复给<?php $comments->author(false); ?>">@</a>
                                </div>
                                  
                                <div class="comment-meta">
                                    <b><?php echo $author ?></b>
                                    <?php echo Comments::getPermalinkFromCoid($comments->parent); ?>
                                    <time class="comment-date"><?php $comments->dateWord(); ?></time>
                                </div>
                            </header>
                        </div>
                
                        <!-- 内容区域 -->
                        <div class="comment-body">
                
                            <!-- 正文 -->
                            <div class="comment-content">
                                <?php
                                $parentMail = $parentMail ?? '';
                                $isTime     = false;
                
                                echo preg_replace(
                                    '/\<img src="(.*?)"(.*?)>/is',
                                    '<figure class="comment-image">
                                        <a class="comment-image__link" data-fancybox="gallery" href="$1">
                                            <img class="comment-image__img lazy" data-src="$1" src="$1" alt="点击查看大图">
                                        </a>
                                    </figure>',
                                    Comments::postCommentContent(
                                        $comments->content,
                                        $GLOBALS['isLogin'],
                                        $GLOBALS['rememberEmail'],
                                        $comments->mail,
                                        $parentMail,
                                        $isTime
                                    )
                                );
                                ?>
                
                                <?php if ('waiting' == $comments->status) : ?>
                                    <p class="comment-notice">您的评论正在等待审核……</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!--class  -->
                <?php if ($comments->children) { ?>
                    <div class="comment-children">
                        <?php $comments->threadedComments($options); ?>
                    </div>
                <?php } ?>
        </li>
    <?php } ?>

    <section class="Comments-warpper" id="comments">
        <!--  -->
        <?php if($this->allow('comment')): ?>
			<div id="<?php $this->respondId(); ?>" class="comment-respond">
                <div class="vcomment">
                    <!--form-->

                    <!-- 最外层表单 -->
                    <form  id="comment-form"
                           action="<?php $this->commentUrl() ?>"
                           method="post"
                           role="form"
                           class="comment-form comment-form--root">
                    
                        
                        <!-- 编辑器外层 -->
                        <div class="comment-form__editor comment-editor">
                            <!-- 输入区 -->
                            <div class="comment-editor__input">
                                <textarea name="text" value="" id="textarea" class="comment-editor__textarea comment-emoji" onkeydown="if((event.ctrlKey||event.metaKey)&&event.keyCode==13){document.getElementById('submitComment').click();return false};" placeholder="注意文明发言哦！" emoji="😀😃😄😁😆😅🤣😂🙂🙃😉😊😇🥰😍🤩😘😗😚😙😋😛😜🤪🤝🤑🤗🤭🤫🤔🤐🤨😐😑😶😏😒🙄😬🤥😌😔😪🤤😴😷🤒🤕🤢🤮🤧🥵🥶🥴😵🤯🤠🥳😎🤓🧐😕😟🙁☹️😮😯😲😳🥺😦😧😨😰😥😢😭😱😖😣😞😓😩😫🥱😤😡😠🤬"></textarea>
                                <!-- 右侧“取消回复” -->
                                <span id="cancelReply"
                                      class="comment-toolbar__cancel-btn"
                                      onclick="cancelReply()"
                                      title="点击取消回复"
                                      style="display: none;">取消</span>
                                
                                <!-- 弹出emoji -->
                                <div class="comment-emoji">
                                    <div class="comment_emoji_block"><span onclick="$('textarea.comment-emoji').val($('textarea.comment-emoji').val() + '😀')">😀</span><span onclick="$('textarea.comment-emoji').val($('textarea.comment-emoji').val() + '😃')">😃</span><span onclick="$('textarea.comment-emoji').val($('textarea.comment-emoji').val() + '😄')">😄</span><span onclick="$('textarea.comment-emoji').val($('textarea.comment-emoji').val() + '😁')">😁</span><span onclick="$('textarea.comment-emoji').val($('textarea.comment-emoji').val() + '😆')">😆</span><span onclick="$('textarea.comment-emoji').val($('textarea.comment-emoji').val() + '😅')">😅</span><span onclick="$('textarea.comment-emoji').val($('textarea.comment-emoji').val() + '🤣')">🤣</span><span onclick="$('textarea.comment-emoji').val($('textarea.comment-emoji').val() + '😂')">😂</span><span onclick="$('textarea.comment-emoji').val($('textarea.comment-emoji').val() + '🙂')">🙂</span><span onclick="$('textarea.comment-emoji').val($('textarea.comment-emoji').val() + '🙃')">🙃</span><span onclick="$('textarea.comment-emoji').val($('textarea.comment-emoji').val() + '😉')">😉</span><span onclick="$('textarea.comment-emoji').val($('textarea.comment-emoji').val() + '😊')">😊</span><span onclick="$('textarea.comment-emoji').val($('textarea.comment-emoji').val() + '😇')">😇</span><span onclick="$('textarea.comment-emoji').val($('textarea.comment-emoji').val() + '🥰')">🥰</span><span onclick="$('textarea.comment-emoji').val($('textarea.comment-emoji').val() + '😍')">😍</span><span onclick="$('textarea.comment-emoji').val($('textarea.comment-emoji').val() + '🤩')">🤩</span><span onclick="$('textarea.comment-emoji').val($('textarea.comment-emoji').val() + '😘')">😘</span><span onclick="$('textarea.comment-emoji').val($('textarea.comment-emoji').val() + '😗')">😗</span><span onclick="$('textarea.comment-emoji').val($('textarea.comment-emoji').val() + '😚')">😚</span><span onclick="$('textarea.comment-emoji').val($('textarea.comment-emoji').val() + '😙')">😙</span><span onclick="$('textarea.comment-emoji').val($('textarea.comment-emoji').val() + '😋')">😋</span><span onclick="$('textarea.comment-emoji').val($('textarea.comment-emoji').val() + '😛')">😛</span><span onclick="$('textarea.comment-emoji').val($('textarea.comment-emoji').val() + '😜')">😜</span><span onclick="$('textarea.comment-emoji').val($('textarea.comment-emoji').val() + '🤪')">🤪</span><span onclick="$('textarea.comment-emoji').val($('textarea.comment-emoji').val() + '🤝')">🤝</span><span onclick="$('textarea.comment-emoji').val($('textarea.comment-emoji').val() + '🤑')">🤑</span><span onclick="$('textarea.comment-emoji').val($('textarea.comment-emoji').val() + '🤗')">🤗</span><span onclick="$('textarea.comment-emoji').val($('textarea.comment-emoji').val() + '🤭')">🤭</span><span onclick="$('textarea.comment-emoji').val($('textarea.comment-emoji').val() + '🤫')">🤫</span><span onclick="$('textarea.comment-emoji').val($('textarea.comment-emoji').val() + '🤔')">🤔</span><span onclick="$('textarea.comment-emoji').val($('textarea.comment-emoji').val() + '🤐')">🤐</span><span onclick="$('textarea.comment-emoji').val($('textarea.comment-emoji').val() + '🤨')">🤨</span><span onclick="$('textarea.comment-emoji').val($('textarea.comment-emoji').val() + '😐')">😐</span><span onclick="$('textarea.comment-emoji').val($('textarea.comment-emoji').val() + '😑')">😑</span><span onclick="$('textarea.comment-emoji').val($('textarea.comment-emoji').val() + '😶')">😶</span><span onclick="$('textarea.comment-emoji').val($('textarea.comment-emoji').val() + '😏')">😏</span><span onclick="$('textarea.comment-emoji').val($('textarea.comment-emoji').val() + '😒')">😒</span><span onclick="$('textarea.comment-emoji').val($('textarea.comment-emoji').val() + '🙄')">🙄</span><span onclick="$('textarea.comment-emoji').val($('textarea.comment-emoji').val() + '😬')">😬</span><span onclick="$('textarea.comment-emoji').val($('textarea.comment-emoji').val() + '🤥')">🤥</span><span onclick="$('textarea.comment-emoji').val($('textarea.comment-emoji').val() + '😌')">😌</span><span onclick="$('textarea.comment-emoji').val($('textarea.comment-emoji').val() + '😔')">😔</span><span onclick="$('textarea.comment-emoji').val($('textarea.comment-emoji').val() + '😪')">😪</span><span onclick="$('textarea.comment-emoji').val($('textarea.comment-emoji').val() + '🤤')">🤤</span><span onclick="$('textarea.comment-emoji').val($('textarea.comment-emoji').val() + '😴')">😴</span><span onclick="$('textarea.comment-emoji').val($('textarea.comment-emoji').val() + '😷')">😷</span><span onclick="$('textarea.comment-emoji').val($('textarea.comment-emoji').val() + '🤒')">🤒</span><span onclick="$('textarea.comment-emoji').val($('textarea.comment-emoji').val() + '🤕')">🤕</span><span onclick="$('textarea.comment-emoji').val($('textarea.comment-emoji').val() + '🤢')">🤢</span><span onclick="$('textarea.comment-emoji').val($('textarea.comment-emoji').val() + '🤮')">🤮</span><span onclick="$('textarea.comment-emoji').val($('textarea.comment-emoji').val() + '🤧')">🤧</span><span onclick="$('textarea.comment-emoji').val($('textarea.comment-emoji').val() + '🥵')">🥵</span><span onclick="$('textarea.comment-emoji').val($('textarea.comment-emoji').val() + '🥶')">🥶</span><span onclick="$('textarea.comment-emoji').val($('textarea.comment-emoji').val() + '🥴')">🥴</span><span onclick="$('textarea.comment-emoji').val($('textarea.comment-emoji').val() + '😵')">😵</span><span onclick="$('textarea.comment-emoji').val($('textarea.comment-emoji').val() + '🤯')">🤯</span><span onclick="$('textarea.comment-emoji').val($('textarea.comment-emoji').val() + '🤠')">🤠</span><span onclick="$('textarea.comment-emoji').val($('textarea.comment-emoji').val() + '🥳')">🥳</span><span onclick="$('textarea.comment-emoji').val($('textarea.comment-emoji').val() + '😎')">😎</span><span onclick="$('textarea.comment-emoji').val($('textarea.comment-emoji').val() + '🤓')">🤓</span><span onclick="$('textarea.comment-emoji').val($('textarea.comment-emoji').val() + '🧐')">🧐</span><span onclick="$('textarea.comment-emoji').val($('textarea.comment-emoji').val() + '😕')">😕</span><span onclick="$('textarea.comment-emoji').val($('textarea.comment-emoji').val() + '😟')">😟</span><span onclick="$('textarea.comment-emoji').val($('textarea.comment-emoji').val() + '🙁')">🙁</span><span onclick="$('textarea.comment-emoji').val($('textarea.comment-emoji').val() + '☹️')">☹️</span><span onclick="$('textarea.comment-emoji').val($('textarea.comment-emoji').val() + '😮')">😮</span><span onclick="$('textarea.comment-emoji').val($('textarea.comment-emoji').val() + '😯')">😯</span><span onclick="$('textarea.comment-emoji').val($('textarea.comment-emoji').val() + '😲')">😲</span><span onclick="$('textarea.comment-emoji').val($('textarea.comment-emoji').val() + '😳')">😳</span><span onclick="$('textarea.comment-emoji').val($('textarea.comment-emoji').val() + '🥺')">🥺</span><span onclick="$('textarea.comment-emoji').val($('textarea.comment-emoji').val() + '😦')">😦</span><span onclick="$('textarea.comment-emoji').val($('textarea.comment-emoji').val() + '😧')">😧</span><span onclick="$('textarea.comment-emoji').val($('textarea.comment-emoji').val() + '😨')">😨</span><span onclick="$('textarea.comment-emoji').val($('textarea.comment-emoji').val() + '😰')">😰</span><span onclick="$('textarea.comment-emoji').val($('textarea.comment-emoji').val() + '😥')">😥</span><span onclick="$('textarea.comment-emoji').val($('textarea.comment-emoji').val() + '😢')">😢</span><span onclick="$('textarea.comment-emoji').val($('textarea.comment-emoji').val() + '😭')">😭</span><span onclick="$('textarea.comment-emoji').val($('textarea.comment-emoji').val() + '😱')">😱</span><span onclick="$('textarea.comment-emoji').val($('textarea.comment-emoji').val() + '😖')">😖</span><span onclick="$('textarea.comment-emoji').val($('textarea.comment-emoji').val() + '😣')">😣</span><span onclick="$('textarea.comment-emoji').val($('textarea.comment-emoji').val() + '😞')">😞</span><span onclick="$('textarea.comment-emoji').val($('textarea.comment-emoji').val() + '😓')">😓</span><span onclick="$('textarea.comment-emoji').val($('textarea.comment-emoji').val() + '😩')">😩</span><span onclick="$('textarea.comment-emoji').val($('textarea.comment-emoji').val() + '😫')">😫</span><span onclick="$('textarea.comment-emoji').val($('textarea.comment-emoji').val() + '🥱')">🥱</span><span onclick="$('textarea.comment-emoji').val($('textarea.comment-emoji').val() + '😤')">😤</span><span onclick="$('textarea.comment-emoji').val($('textarea.comment-emoji').val() + '😡')">😡</span><span onclick="$('textarea.comment-emoji').val($('textarea.comment-emoji').val() + '😠')">😠</span><span onclick="$('textarea.comment-emoji').val($('textarea.comment-emoji').val() + '🤬')">🤬</span>
                                    </div>
                                    <div class="button emoji-btn Nug Nug-guanbi"></div>
                                </div>
                            </div>
                            
                            <!-- 未登录时出现的用户信息面板 -->
                            <?php if (!$this->user->hasLogin()): ?>
                                <div class="comment-userinfo__row">
                                    <input type="text" id="author" name="author"
                                           class="comment-userinfo__input comment-userinfo__name"
                                           placeholder="* 怎么称呼"
                                           value="<?php $this->remember('author'); ?>" required>
                                    <input type="email" id="mail" name="mail"
                                           class="comment-userinfo__input comment-userinfo__email"
                                           placeholder="邮箱（放心，会保密）"
                                           value="<?php $this->remember('mail'); ?>" <?php if ($this->options->commentsRequireMail): ?>required<?php endif; ?>>
                    
                                    <input type="url" id="url" name="url"
                                           class="comment-userinfo__input comment-userinfo__url"
                                           placeholder="您的主页"
                                           value="<?php $this->remember('url'); ?>" <?php if ($this->options->commentsRequireURL): ?>required<?php endif; ?>>
                                </div>
                            <?php endif; ?>
                                
                            <!-- 底部：提交按钮 -->
                            <div class="comment-form__footer comment-footer">
                                <button type="submit" id="submitComment" class="comment-footer__submit">
                                    <span>提交</span>
                                </button>
                            </div>
                            
                        </div>
                    </form>
                </div>
            </div>
        <?php else : ?>
            <h3 class="vcount" style="text-align: center;"> 评论已关闭 &gt;_&lt; </h3>
		<?php endif; ?>
		
		
        <?php $this->comments()->to($comments); ?>
        <div class="v-comment" >
            <div class="Comments-lists">
                <?php if ($comments->have()): ?>
                    <?php $comments->listComments(); ?>
                    <div class="paging">
                        <?php $comments->pageNav('<div class="pagination-prve"><i class="Nug Nug-youbian"></i></div>', '<div class="pagination-prve"><i class="Nug Nug-youbian"></i></div>', 1, '...'); ?>
                    </div>
                <?php else: ?>
                    <div class="comment-none">
                        <p class="comment-none__text">沙发还空着，快来抢沙发吧！</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

	</section>

    