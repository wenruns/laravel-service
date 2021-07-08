#使用说明
##（1）资源下载：[https://download.csdn.net/download/qq_38421226/15514679](https://download.csdn.net/download/qq_38421226/15514679)
将解压目录放置项目下的指定路径：app/Admin/Extensions/Form。（可根据需求自己调整，注意：需要同步调整命名空间）
##（2）组件注册
```angular2
Form::extend('apiSelect', \App\Admin\Extensions\Form\ApiSelect\ApiSelect::class);
```
##（3）控件使用说明
**第一种：无默认选项，直接通过api查询。**
```angular2
$form->apiSelect('apply_id', '客户姓名')
     ->url(route('field.api_select'))
     ->attach([
         'type' => SelectLoanFieldController::IS_LOAN_FORM,
     ])->help('无默认选项');
```
该模式主要有两个要点：url()方法和attach()方法。url方法设置请求api ，attach方法设置请求附带的数据（参数attach的值）；

**第二种：有默认选项（如果设置url则通过api查询，否则在默认选项中匹配关键词）。**
```angular2
$form->apiSelect('apply_id', '客户姓名')
     ->url(route('field.api_select'))
     ->attach([
         'type' => SelectLoanFieldController::IS_LOAN_FORM,
     ])
     ->options([
         'S001' => '孙丽华',
         'S002' => '邓超',
         'S003' => '郭敬明',
         'S004' => '王力宏',
         'S005' => '周杰伦',
         'S006' => '林俊杰',
         'S007' => '邓紫棋',
         'S008' => '黄晓明',
     ])->help('有默认选项');
```
该模式比第一种不同的是，通过options方法设置默认选项，并无其他差别。

**第三种：启用默认选中功能（当选项中只有一个选项时，将默认选中这个选项）。**
```angular2
$form->apiSelect('apply_id', '客户姓名')
     ->url(route('field.api_select'))
     ->defaultSelect()
     ->attach([
         'type' => SelectLoanFieldController::IS_LOAN_FORM,
     ])->help('启用当查询选项中只有一个选项时，默认选中');
```
该模式比第一种不同的是，通过defaultSelect()方法启动了该功能。

**第四种：多选功能。**
```angular2
$form->apiSelect('apply_id', '客户姓名')
     ->url(route('field.api_select'))
     ->attach([
         'type' => SelectLoanFieldController::IS_LOAN_FORM,
     ])
     ->multiple()
     ->help('启用多选项');
```
该模式比第一种不同的是，通过multiple()方法启动了多选功能。

##（4）事件监听设置
```angular2
a、change事件 changed(javascriptFunction)，当值发生变化时触发

b、beforeUpdate事件  beforeUpdate(javascriptFunction)，当options发生变化时，刷新前触发

c、updated事件  updated(javascriptFunction)，当options发生变化时，刷新后触发

d、beforeClear事件 beforeClear(javascriptFunction)，当点击选项框右侧“x”号清空时，清空前触发

c、cleared事件 cleared(javascriptFunction)，当点击选项框右侧“x”号清空时，清空后触发
```
```angular2
$form->apiSelect('apply_id', '客户姓名')
     ->url(route('field.api_select'))
     ->attach([
         'type' => SelectLoanFieldController::IS_LOAN_FORM,
     ])
     ->changed('function(data){  console.log("changed", data); }') // 值发生变化时触发
     ->beforeUpdate('function(data){ console.log("beforeUpdate", data); }') // 选项更新前触发
     ->updated('function(data){ console.log("updated", data); }') // 选项更新后触发
     ->beforeClear('function(data){ console.log("beforeClear", data); }') // 清空所有值前触发
     ->cleared('function(data){ console.log("cleared", data); }') // 清空所有值后触发
     ->help('无默认选项');
```
事件回调参数data内容为：
```angular2
{
    period: "changed",
    options: {
        value1: text1,
        value2: text2,
        value3: text3
    },
    value: {
        value1: text1, 
    },
    apiResult: {},
    data: {
        _token: "laravel-admin的token值，由csrf_token()生成"
    },
    attach: {}, // 该内容由attach()方法设置
}
```
 
##（5）api实现介绍。
api接收参数：
```angular2
{
    q: "关键词",
    attach: {}, # 由attach()方法设置
    _token: "laravel-admin的token值，由csrf_token()生成"
}
```
api返回格式：
```angular2
{
    value1: text1,
    value2: text2,
    value3: text3,
    value4: text4,
    value5: text5,
}
```
或者
```angular2
{
    options: {
        value1: text1,
        value2: text2,
        value3: text3,
        value4: text4,
        value5: text5,
    },
    attach: {}
    ...
}
```
api返回的结果将通过参数的形式传递到事件回调中。

##（6）总结
如果有什么不清楚，欢迎到https://blog.csdn.net/qq_38421226/article/details/113612123发表提问。



