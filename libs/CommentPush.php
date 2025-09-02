<?php

require_once("mail/phpmailer.php");
require_once("mail/smtp.php");

/* 邮件通知 */
if (
  Helper::options()->CommentMail === 'on' &&
  Helper::options()->CommentMailHost &&
  Helper::options()->CommentMailPort &&
  Helper::options()->CommentMailFromName &&
  Helper::options()->CommentMailAccount &&
  Helper::options()->CommentMailPassword &&
  Helper::options()->CommentSMTPSecure
) {
  Typecho_Plugin::factory('Widget_Feedback')->finishComment = array('Email', 'send');
}

class Email
{
  public static function send($comment)
  {
    $mail = new PHPMailer();
    $mail->isSMTP();
    $mail->SMTPAuth = true;
    $mail->CharSet = 'UTF-8';
    $mail->SMTPSecure = Helper::options()->CommentSMTPSecure;
    $mail->SMTPSecure = Helper::options()->CommentSMTPSecure;
    $mail->Host = Helper::options()->CommentMailHost;
    $mail->Port = Helper::options()->CommentMailPort;
    $mail->FromName = Helper::options()->CommentMailFromName;
    $mail->Username = Helper::options()->CommentMailAccount;
    $mail->From = Helper::options()->CommentMailAccount;
    $mail->Password = Helper::options()->CommentMailPassword;
    $mail->isHTML(true);
    $text = $comment->text;
    // 获取博客名称
    $blogName = Helper::options()->title;
    $html = '
            <style>.email-container{max-width:600px;width:100%;background:#fdfdfd;overflow:hidden;border:solid 0.1rem rgb(241 241 241 / 50%);position:relative;margin:auto;}.email-header{padding:40px 30px;text-align:center;color:#DBA91C;position:relative;}.email-header::before{content:"";position:absolute;top:0;left:0;right:0;height:1px;background:linear-gradient(90deg,transparent,#DBA91C,transparent);}.email-logo{font-size:28px;font-weight:500;letter-spacing:4px;margin-bottom:15px;text-transform:uppercase;text-shadow:0 0 10px rgba(212,175,55,0.3);}.email-title{font-size:22px;font-weight:400;margin-top:15px;color:#ffffff;}.email-subtitle{font-size:16px;color:#b8b8b8;margin-top:10px;font-weight:300;}.email-body{padding:40px 35px;color:#A5A5A5;line-height:1.6;}.email-content{margin-bottom:30px;}.email-content p{margin-bottom:20px;font-size:15px;color:#3f3f3f;}.highlight{color:#DBA91C;font-weight:500;}.email-button{display:inline-block;padding:14px 35px;background:#DBA91C;color:#181818 !important;text-decoration:none;font-weight:500;margin:25px 0;text-transform:uppercase;letter-spacing:1px;font-size:14px;border:none;cursor:pointer;transition:all 0.3s ease;box-shadow:0 4px 15px rgba(212,175,55,0.2);}.email-button:hover{transform:translateY(-2px);box-shadow:0 6px 20px rgba(212,175,55,0.3);}.email-divider{height:1px;background:linear-gradient(90deg,transparent,#f1f1f1,transparent);margin-block-start:2rem}.feature{margin-bottom:20px;padding:15px;background:#1c1c1c;border-radius:6px;border-left:2px solid #DBA91C;background-color:#f5f5f5;}.email-footer{padding:30px;color:#A5A5A5;font-size:13px;text-align:center;border-top:dotted 2px #f1f1f1;}.email-footer a{color:#DBA91C;text-decoration:none;margin:0 10px;transition:color 0.3s;}.email-footer a:hover{color:#ffffff;text-decoration:underline;}</style>
            <div class="email-container">
        <div class="email-header"><div class="email-logo">' . $blogName . '</div><div class="email-divider"></div></div><div class="email-body"><div class="email-content"><p>{author}</p><p class="feature">{content}</p><div style="text-align: center;"><a href="{permalink}" class="email-button">前往查看</a></div></div></div><div class="email-footer"><p>© 2025 ' . $blogName . '. 保留所有权利.</p><p style="margin-top: 20px;">请注意：此邮件由系统自动发送，请勿直接回复。</p></div></div>';
    /* 如果是博主发的评论 */
    if ($comment->authorId == $comment->ownerId) {
      /* 发表的评论是回复别人 */
      if ($comment->parent != 0) {
        $db = Typecho_Db::get();
        $parentInfo = $db->fetchRow($db->select('mail')->from('table.comments')->where('coid = ?', $comment->parent));
        $parentMail = $parentInfo['mail'];
        /* 被回复的人不是自己时，发送邮件 */
        if ($parentMail != $comment->mail) {
          $mail->Body = strtr(
            $html,
            array(
              "{title}" => $comment->title,
              "{author}" => $comment->author.'在《 '.$comment->title.' 》上回复您：',
              "{permalink}" => substr($comment->permalink, 0, strrpos($comment->permalink, "#")),
              "{content}" => $text,
            )
          );
          $mail->addAddress($parentMail);
          $mail->Subject = '您在 [' . $comment->title . '] 的评论有了新的回复！';
          $mail->send();
        }
      }
      /* 如果是游客发的评论 */
    } else {
      /* 如果是直接发表的评论，不是回复别人，那么发送邮件给博主 */
      if ($comment->parent == 0) {
        $db = Typecho_Db::get();
        $authoInfo = $db->fetchRow($db->select()->from('table.users')->where('uid = ?', $comment->ownerId));
        $authorMail = $authoInfo['mail'];
        if ($authorMail) {
          $mail->Body = strtr(
            $html,
            array(
              "{title}" => $comment->title,
              "{author}" => '尊敬的 <span class="highlight">'.$comment->author.'</span> 在您的《 '.$comment->title.' 》上发表评论:',
              "{ip}" => $comment->ip,
              "{permalink}" => substr($comment->permalink, 0, strrpos($comment->permalink, "#")),
              "{content}" => $text,
            )
          );
          $mail->addAddress($authorMail);
          $mail->Subject = '您的文章 [' . $comment->title . '] 收到一条新的评论！';
          $mail->send();
        }
        /* 如果发表的评论是回复别人 */
      } else {
        $db = Typecho_Db::get();
        $parentInfo = $db->fetchRow($db->select('mail')->from('table.comments')->where('coid = ?', $comment->parent));
        $parentMail = $parentInfo['mail'];
        /* 被回复的人不是自己时，发送邮件 */
        if ($parentMail != $comment->mail) {
          $mail->Body = strtr(
            $html,
            array(
              "{title}" => $comment->title,
              "{author}" => $comment->author.'在《 '.$comment->title.' 》上回复您：',
              "{permalink}" => substr($comment->permalink, 0, strrpos($comment->permalink, "#")),
              "{content}" => $text,
            )
          );
          $mail->addAddress($parentMail);
          $mail->Subject = '您在 [' . $comment->title . '] 的评论有了新的回复！';
          $mail->send();
        }
      }
    }
  }
}
