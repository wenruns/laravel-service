#使用说明
##（1）资源下载： [multiList控件](https://download.csdn.net/download/qq_38421226/15450166)
将解压目录放置项目下的指定路径：app/Admin/Extensions/Form。（可根据需求自己调整，注意：需要同步调整命名空间）
##（2）控件注册
下载资源后，放置到项目指定目录中（app/Admin/Extensions/Form），然后在app/Admin/bootstrap.php文件中注册
```angular2
Form::extend('multiList', \App\Admin\Extensions\Form\MultiList\MultiList::class);
```
##（3）控件使用介绍以及example代码
```angular2
$form->multiList('setting', '一级列表', function (TableList $tableList) {
    $tableList->expand('id_1', '二级列表', function (Row $row) {
        $row->multiList(function (TableList $tableList) {
            $tableList->display('display', 'display显示');
            $tableList->text('text', 'text文本框');
            $tableList->switch('switch', 'switch开关');
            $tableList->date('date', 'date日期');
            $tableList->select('select', 'select单选')->options([
                'before' => '之前',
                'after'  => '之后',
            ]);
            $tableList->multiSelect('multiSelect', 'multiSelect多选')->options([
                10 => '10岁',
                20 => '20岁',
                30 => '30岁',
                40 => '40岁',
            ]);
        })->default([
            [
                'display'     => '显示文本',
                'text'        => '文本框',
                'switch'      => true,
                'date'        => '2020-01-02',
                'select'      => 'before',
                'multiSelect' => '20',
            ], [
                'display'     => '显示文本',
                'text'        => '文本框',
                'switch'      => false,
                'date'        => null,
                'select'      => null,
                'multiSelect' => null,
            ]
        ]);
    });
    $tableList->expand('id_2', '默认显示', function (Row $row) {
        $row->text('text', 'text方法')->default('文本框');
        $row->date('date', 'date方法');
        $row->switch('switch', 'switch方法');
        $row->display('display', 'display方法')->default('仅做显示');
        $row->select('select', 'select方法')->options(['boy' => '男', 'girl' => '女']);
        $row->multiSelect('multiSelect', 'multiSelect方法')->options([
            10 => '10岁',
            20 => '20岁',
            30 => '30岁',
            40 => '40岁',
        ]);
    });
    $tableList->expand('id_3', '排序显示', function (Row $row) {
        $row->isRowBool()->setWidth(4);
        $row->text('text', 'text方法')->default('文本框');
        $row->date('date', 'date方法');
        $row->display('display', 'display方法')->default('仅做显示');
        $row->select('select', 'select方法')->options(['boy' => '男', 'girl' => '女']);
        $row->multiSelect('multiSelect', 'multiSelect方法')->options([
            10 => '10岁',
            20 => '20岁',
            30 => '30岁',
            40 => '40岁',
        ]);
        $row->switch('switch', 'switch方法');
    });
    $tableList->expand('id_4', '分列显示', function (Row $row) {
        $row->column(1 / 2, function (Column $column) {
            $column->text('text', 'text方法')->default('文本框');
            $column->date('date', 'date方法');
            $column->switch('switch', 'switch方法');
        });
        $row->column(1 / 2, function (Column $column) {
            $column->display('display', 'display方法')->default('仅做显示');
            $column->select('select', 'select方法')->options(['boy' => '男', 'girl' => '女']);
            $column->multiSelect('multiSelect', 'multiSelect方法')->options([
                10 => '10岁',
                20 => '20岁',
                30 => '30岁',
                40 => '40岁',
            ]);
        });
    });
    $tableList->modal('id_5', '模态框', function (Row $row) {
        $row->text('ss', 'bbb');
        $row->column(1 / 2, function (Column $column) {
            $column->text('name', '姓名');
            $column->text('sex', '性别');
            $column->text('age', '年龄');
        });
        $row->column(1 / 2, function (Column $column) {
            $column->text('name', '姓名');
            $column->text('sex', '性别');
            $column->text('age', '年龄');
        });
    });
    $tableList->display('display', 'display显示');
    $tableList->text('text', 'text文本框');
})->default([
    'id_1'    => '展开',
    'id_2'    => '展开',
    'id_3'    => '展开',
    'id_4'    => '展开',
    'id_5'    => '查看',
    'display' => '显示内容',
    'text'    => '输入框',
]);
```
##（4）总结
如果有什么不清楚，欢迎到https://blog.csdn.net/qq_38421226/article/details/113646273发表提问。