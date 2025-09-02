<!DOCTYPE html>

<html lang="zh-CN" prefix="og: http://ogp.me/ns#">

<head>

    <meta charset="utf-8" />

    <meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=0, width=device-width"/>

    <meta itemprop="name" content="<?php $this->options->title(); ?> | <?php $this->options->description(); ?>">

    <meta itemprop="image" content="<?php $this->options->title(); ?> | <?php $this->options->description(); ?>">

    <meta name="keywords" content="<?php $this->options->keywords(); ?>" /> 

    <meta name="description" itemprop="description" content="<?php $this->options->title(); ?> | <?php $this->options->description(); ?>">


    <title>

      <?php $this->archiveTitle(array(
            'category'  =>  _t('分类 %s 下的文章'),
            'search'    =>  _t('包含关键字 %s 的文章'),
            'tag'       =>  _t('标签 %s 下的文章'),
            'author'    =>  _t('%s 发布的文章')
        ), '', ' - '); ?>

        <?php $this->options->title(); ?> | <?php $this->options->description(); ?>
          
    </title>


    <meta itemprop="name" content="<?php $this->options->title(); ?> | <?php $this->options->description(); ?>">

    <meta name="description" itemprop="description" content="<?php $this->options->title(); ?> | <?php $this->options->description(); ?>">

    <meta property="og:type" content="website" />

    <meta property="og:url" content="<?php $this->options->siteUrl(); ?>" />

    <meta property="og:image" content="<?php _getAssets('favicon.ico'); ?>" />

    <meta property="og:title" content="<?php $this->options->title(); ?> | <?php $this->options->description(); ?>" />

    <meta property="og:description" content="<?php $this->options->title(); ?> | <?php $this->options->description(); ?>" />

    <link rel="canonical" href="https://amrx.me/" />

    <link rel="shortcut icon" href="<?php _getAssets('assets/img/favicon.ico'); ?>">

    <link rel="bookmark" href="<?php _getAssets('assets/img/favicon.ico'); ?>">

    <link rel="apple-touch-icon" sizes="180x180" href="<?php _getAssets('assets/img/avatar.jpg'); ?>">

    <link rel="stylesheet" href="<?php _getAssets('assets/css/icon.css'); ?>" media="screen" type="text/css">
    <link rel="stylesheet" href="<?php _getAssets('assets/css/style.css'); ?>" media="screen" type="text/css">
    
    <?php $this->header(); ?>
    
    <?php
        $fontUrl = $this->options->CustomFont ?? ''; // 使用空字符串作为默认值
        $fontFormat = '';
        
        if (strpos($fontUrl, 'woff2') !== false) {
            $fontFormat = 'woff2';
        } elseif (strpos($fontUrl, 'woff') !== false) {
            $fontFormat = 'woff';
        } elseif (strpos($fontUrl, 'ttf') !== false) {
            $fontFormat = 'truetype';
        } elseif (strpos($fontUrl, 'eot') !== false) {
            $fontFormat = 'embedded-opentype';
        } elseif (strpos($fontUrl, 'svg') !== false) {
            $fontFormat = 'svg';
        }
    ?>
    <style>
        @font-face {
            font-family: 'wodeziti';
            font-weight: 400;
            font-style: normal;
            font-display: swap;
            src: url('<?php echo $fontUrl ?>');
            <?php if ($fontFormat) : ?>src: url('<?php echo $fontUrl ?>') format('<?php echo $fontFormat ?>');
            <?php endif; ?>
        }
        body {
            <?php if ($fontUrl) : ?>
            font-family: 'wodeziti';
            font-weight: 400;
            <?php else : ?>
            font-family: 'Helvetica Neue', Helvetica, 'PingFang SC', 'Hiragino Sans GB', 'Microsoft YaHei', '微软雅黑', Arial, sans-serif;
        <?php endif; ?>
      }
      </style>


</head>

<body>
    <?php $this->need('public/head.php');?>