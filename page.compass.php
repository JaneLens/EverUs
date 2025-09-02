<?php 
/**
 * 指南针
 * 
 * @package custom 
 * 
 */
$this->need('public/include.php');
?>

<div id="swup" class="content transition-fade page">
    <!-- 最外层内容区 -->
    <div class="page-content">
        <!-- 外层容器 -->
        <div class="cat-group">
            <!-- 标题组 -->
            <header class="compass-header">
                <h2 class="compass-header__title">
                    <?php $this->title() ?>
                </h2>
                <p>这里是我的个人指南针——为未来日子收集的意图与方向。不仅是任务，更是通往我梦境与日常生活核心的坐标。</p>
            </header>
            <?php
            /* 解析任务表 */
            $tasks = [];
            $raw  = $this->options->compass;
            if ($raw) {
                foreach (explode("\r\n", $raw) as $line) {
                    $parts = array_pad(explode('||', $line), 4, '');
                    $tasks[] = [
                        'time'   => trim($parts[0]),
                        'title'  => trim($parts[1]),
                        'desc'   => trim($parts[2]),
                        'proof'  => trim($parts[3]),
                    ];
                }
            }
            ?>
            <!-- 安排表 -->
            <?php if ($tasks): ?>
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
            <?php endif; ?>
        </div>
    </div>
    
    <!-- 评论 -->
    <div class="page-comments">
        <?php $this->need('public/comments.php');?>
    </div>
    
    <?php $this->need('footer.php');?>
</div>