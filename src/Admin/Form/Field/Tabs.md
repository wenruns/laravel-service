#使用说明
##（1）资源下载：[laravel-admin：form表单tab标签切换组件.rar](https://download.csdn.net/download/qq_38421226/15501972)
将解压目录放置项目下的指定路径：app/Admin/Extensions/Form。（可根据需求自己调整，注意：需要同步调整命名空间）
##（2）组件注册
```angular2
// 自定义Tab切换组件（注意：不可注册tab，因为form表单本身已存在tab这个控件）
Form::extend('tabs', \App\Admin\Extensions\Form\Tabs\Tabs::class);
```
##（3）使用Example--Code
```angular2
$form->tabs('页面与功能权限', function (Form $form) use ($permission, $roles) {
    $form->column(1 / 2, function (Form $form) use ($permission, $roles) {
        $form->checkboxTree('permission_id', '页面与功能权限')
             ->options($optionsArr)
             ->disableLabel() // 隐藏左边的label
             ->default($defaultArr) // 设置默认值
//             ->disabled() // 禁用复选框
//             ->hideCheckBox() // 隐藏复选框
             ->spreadChecked() // 展开选中的选项
//             ->spread() // 全部展开
             ->onReady('function(treeObj, data, elementId){
                 console.log(treeObj, data, elementId);       
             }')  // 实例化控件js后执行（注意：该参数为js闭包函数）
             ->onChange('function(val, name, text, checked){
                  console.log(111, val, name, text,checked);
              }');   // 复选框值发生变化时触发（注意：该参数为js闭包函数）
    });
})->addTab('数据范围', function (Form $form) use ($id, $roles) {
    $form->column(1 / 2, function (Form $form) use ($id, $roles) {
         $form->checkboxTree('org_code', '数据范围')
              ->options($optionsArr)
              ->disableLabel()
              ->default($defaultArr)
              ->spreadChecked();
     });
})->max('100%', '490px')
```
##（4）总结
如果有什么不清楚，欢迎到https://blog.csdn.net/qq_38421226/article/details/114261550发表提问。


