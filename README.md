# Sensitive Word Filter 敏感词过滤


### 使用示例
~~~
1. 生成实例;
use Sensitive\SensitiveWordFilter;

# 实例化的同时, 更改敏感词占位符, 默认为 * 
$instance = SensitiveWordFilter::getInstance('?');

# 实例化时, 不更敏感词改占位符
$instance = SensitiveWordFilter::getInstance('?');
~~~

```
2. 引入敏感词库, 现在只支持文本的方式引入敏感词库

#这里填入您的敏感词库文件, 文件请参考resource里面的示例文件
$instance->addSensitiveWords('./Resource/SensitiveWords.txt'); 
```

```
3. 执行过滤

//需要过滤的文本
$txt = "相信，花木深处，不管记忆如何零落，不管擦肩为何转瞬即空，相遇的时光就是一卷铺衬的秋词，我慢慢写，你静静读。在秋的眉眼固执的开
，提笔描绘枫红的流年，渲染一抹思念，泅渡着素简岁月的牵牵念念。";

echo $instance->execFilter($txt);
```

### 过滤的一些实例结果:

1. 这里我使用的敏感词库中含有"泅渡", 将其替换为 "??"

![1583226788416](C:\Users\ADMINI~1\AppData\Local\Temp\1583226788416.png)

2. 这里过滤了"不管记忆如何零落"; 

![1583226871356](C:\Users\ADMINI~1\AppData\Local\Temp\1583226871356.png)

### 其他事项:

```
bug, 请提到ng_sou@163.com. 请详细说明, 谢谢!
```

