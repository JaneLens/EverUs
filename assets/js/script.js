/* ==========  全局立即执行函数  ========== */
(function ($) {

    /* ---------  Scroll Box  --------- */
    // 点击事件：滚动到顶部并给 body 添加类名
    $(".huojian__toggle").click(function () {
        $('html,body').animate({ scrollTop: 0 }, 500, function () {
            // 动画完成后给 body 添加类名
            $('body').removeClass('nav-fixed');
        });
    });

    // 滚动事件：根据滚动位置显示或隐藏 .huojian__toggle 按钮，并给 body 添加或移除类名
    $(window).on("scroll", function () {
        var fromTop = $(window).scrollTop();
        if (fromTop > 50) {  // 判断滚动后高度超过50px,就显示
            $('.huojian__toggle').removeClass('hidden');
            $('body').addClass('nav-fixed'); // 添加类名
        } else {
            $('.huojian__toggle').addClass('hidden');
            $('body').removeClass('nav-fixed'); // 移除类名
        }
    });
    
    /* ---------  nav  --------- */
    $(function () {
        // 开关
        $('.daohang').on('click', function (e) {
            $('body').toggleClass('nav-open');
        });
    
        // 页面刚渲染就关闭
        $('body').removeClass('nav-open');
    
        // 导航内部所有链接点击后关闭
        $(document).on('click', '.site-nav a', function () {
            $('body').removeClass('nav-open');
        });
    });
    
})(jQuery);

document.addEventListener('DOMContentLoaded', function () {
    const swup = new Swup({
        containers: ['#swup'],
        animateHistoryBrowsing: true, // 这个最重要！开启历史记录动画
        animationSelector: '.transition-fade' , // CSS类名是否正确
        cache: true // 启用缓存提高速度
    });

    /* ---------  gsap  --------- */
    function animateParagraphs() {
        gsap.registerPlugin(ScrollTrigger);

        $('.post__content p, .up').each((i, el) => {
            gsap.fromTo(el, {
                opacity: 0,
                y: 30,
                pointerEvents: 'none'
            }, {
                opacity: 1,
                y: 0,
                duration: 0.3,
                delay: i * 0.01,          // 0.01 s 步长，足够短
                pointerEvents: 'all',
                scrollTrigger: {
                    trigger: el,          // 以段落本身触发
                    start: 'top 100%',
                    once: false,          // 允许重复
                    toggleActions: 'restart none none reverse'
                }
            });
        });
    }
    
    animateParagraphs();
    
    /* ---------  nav  --------- */
    function setActiveLink() {
        const currentUrl = window.location.href;
        const links = document.querySelectorAll('.site-nav__dropdown-item > a');

        // 先移除所有 mm-active
        links.forEach(link => {
            link.classList.remove('mm-active');
            link.parentElement.classList.remove('mm-active');
        });

        // 再添加匹配的类
        links.forEach(link => {
            if (link.href === currentUrl) {
                link.classList.add('mm-active');
                link.parentElement.classList.add('mm-active');
            }
        });
    }
    
    setActiveLink();

    /* ---------  Comment Emoji  --------- */
    function bindEmojiButton() {
        // 绑定点击事件
        $('.emoji-btn').off('click').on('click', function() {
            $('.comment-emoji').toggleClass('show');
        });
    }

    bindEmojiButton();

    /* ---------  Comment Submit  --------- */
    function bindCommentForm() {
        $('.comment-form').off('submit').on('submit', function(event) {
            event.preventDefault();
            var commentdata = $(this).serializeArray();
            $.ajax({
                url: $(this).attr('action'),
                type: $(this).attr('method'),
                data: commentdata,
                beforeSend: function() {
                    // 在提交前的逻辑
                },
                error: function(request) {
                    // 错误处理逻辑
                },
                success: function(data) {
                    $('#submitComment').addClass('submit').text('发表评论');
                    var error = /<title>Error<\/title>/;
                    if (error.test(data)) {
                        var text = data.match(/<div(.*?)>(.*?)<\/div>/is);
                        var str = '发生了未知错误'; if (text != null) str = text[2];
                        var text = $("#textarea").val();
                        var author = $("#author").val();
                        var mail = $("#mail").val();
                        var newUrl = str.replace(".html", ".html/comment?text=" + text + "&author=" + author + "&mail=" + mail + "&url=");
                    } else {
                        $('#comment-parent').remove();
                        $("#textarea").val('');
                        $(".comment-respond textarea").attr('placeholder', '评论成功！');
                        $('#cancelReply').text('').css('display', 'none');

                        $('.comment').html($('.comment', data).html());
                        $('.Comments-lists').html($('.Comments-lists', data).html());

                        var biggestNum = 0;
                        $('li[id^="li-comment-"]').each(function() {
                            var currentNum = parseInt($(this).attr('id').replace('li-comment-', ''), 0);
                            if (currentNum > biggestNum) {
                                biggestNum = currentNum;
                            }
                        });
                        $("html, body").animate({
                            scrollTop: $('#li-comment-' + biggestNum).offset().top - 60 + "px"
                        }, {
                            duration: 600,
                            easing: "linear"
                        });
                    }
                }
            });
        });
    }

    bindCommentForm();

    /* ---------  Comment Reply  --------- */
    // 定义 createReply 函数
    window.createReply = function(coid, author) {
        $('.comment-form').addClass('Comments_publisher');
        console.log('#coid-' + coid);
        $('#comment-parent').remove();
        $('#comment-form').append('<input type="hidden" name="parent" id="comment-parent" value="' + coid + '">');
        $('#cancelReply').html("<span>取消回复：" + author + "</span>").css('display', 'inline-flex');
        $("html, body").animate({
            scrollTop: $('#comment-form').offset().top - 120 + "px"
        }, {
            duration: 250,
            easing: "linear"
        });
        $('.comment-respond textarea').attr('placeholder', '正在回复：' + author);
        $('#textarea').focus();
    };

    // 定义 cancelReply 函数
    window.cancelReply = function() {
        $('#comment-parent').remove();
        $('#cancelReply').text('').css('display', 'none');
        $('.comment-respond textarea').attr('placeholder', '来都来了，说点什么呗~');
    };

    // 绑定点击事件
    function bindReplyEvents() {
        $('.comment-reply-link').off('click').on('click', function(event) {
            event.preventDefault();
            const coid = $(this).data('coid');
            const author = $(this).data('author');
            window.createReply(coid, author);
        });

        $('#cancelReply').off('click').on('click', function(event) {
            event.preventDefault();
            window.cancelReply();
        });
    }

    // 首次加载时绑定事件
    bindReplyEvents();
    
    // 走心
    function initCarousel() {
        $('.commentator-slick').not('.slick-initialized').each(function () {
            $(this).slick({
                dots: true,
                infinite: true,
                speed: 500,
                fade: true,
                cssEase: 'linear',
                autoplay: true,
                autoplaySpeed: 3000,
                pauseOnHover: true,
                pauseOnFocus: true,
                arrows: false,
                responsive: [
                    { breakpoint: 1024, settings: { slidesToShow: 1, slidesToScroll: 1 } },
                    { breakpoint: 600, settings: { arrows: false, dots: true } }
                ]
            });
        });
        /* 自定义上一页 / 下一页按钮 */
        $('.slick-custom-prev').on('click', function () {
            $('.commentator-slick').slick('slickPrev');
        });
        $('.slick-custom-next').on('click', function () {
            $('.commentator-slick').slick('slickNext');
        });
    }

    /* 首次加载 & 每次换页后重新初始化 */
    initCarousel();

    // Fancybox
    function initFancybox() {
        // 初始化 Fancybox
        Fancybox.bind("[data-fancybox='gallery']", {
            hideScrollbar: false,
            idle: false,
            Carousel: {
                transition: "slide",
            },
        });

        // 绑定点击事件到 .zoom 按钮
        document.querySelectorAll('.zoom').forEach(button => {
            button.addEventListener('click', function (event) {
                event.preventDefault();
                const parentCard = button.closest('.work-card');
                const image = parentCard?.querySelector('a[data-fancybox="gallery"]');
                if (image) image.click();
            });
        });
    }

    initFancybox();
    
    // 每次内容替换（前进 / 后退 / 刷新）后重播动画
    swup.hooks.on('content:replace', () => {
        setTimeout(() => {
            animateParagraphs();
            setActiveLink();
            bindEmojiButton();
            bindCommentForm();
            bindReplyEvents();
            initCarousel();
            initFancybox();
        }, 100); // 延迟 100ms 确保 DOM 元素已经加载完成
    });
});
