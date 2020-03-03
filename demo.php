<?php
require_once "./vendor/autoload.php";

use Sensitive\SensitiveWordFilter;

$instance = SensitiveWordFilter::getInstance();

//引入你的敏感词库文件
$instance->addSensitiveWords();

#$instance->addSensitiveWords('./SensitiveWords.txt');//引入你的敏感词库文件
$txt = "相信，花木深处，不管记忆如何零落，不管擦肩为何转瞬即空，相遇的时光就是一卷铺衬的秋词，我慢慢写，你静静读。在秋的眉眼固执的开
，提笔描绘枫红的流年，渲染一抹思念，泅渡着素简岁月的牵牵念念。";//需要过滤的文本
#$txt = "念念。";//需要过滤的文本
echo $instance->execFilter($txt);