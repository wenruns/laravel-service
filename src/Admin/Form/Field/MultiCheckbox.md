#使用说明：

##1、下载资源：[Laravel-admin表单Form多级下拉复选框组件multiCheckbox（优化版）.rar](https://download.csdn.net/download/qq_38421226/15480612)
   将解压目录放置项目下的指定路径：app/Admin/Extensions/Form。（可根据需求自己调整，注意：需要同步调整命名空间）

##2、注册组件。
   在文件app/Admin/bootstrap.php中添加一下代码：
```angular2
Form::extend('multiCheckbox', \App\Admin\Extensions\Form\MultiCheckbox\MultiCheckbox::class);
```
   
##3、使用。
```angular2
$form->multiCheckbox('organizations', '组织机构权限')
            ->options([
               'A001'=>'东莞公司',
               'A002'=>'深圳公司',
               'A003'=>'广州公司',
               'A004'=>'白云公司',
               'A005'=>'佛山公司',
               'A006'=>'惠州公司',
               'A007'=>'龙岗公司',
               'A008'=>'杭州公司',
            ])
            ->requestUrl(route('admin.admin_staff.get_options')) // 下级选项请求api
            ->attach([
                'type' => 'test',
            ])  // 设置请求api时附带的参数数据
//            ->objectFormat() // 设置提交数据格式为 多维数组（含有隶属关系）， 默认为一维数组（不存在隶属关系）
//            ->default(['B001', 'B002', 'B003', 'D001']) // 设置默认勾选值
            ->required()
            ->help('测试撒的否');
```
   
##4、api结果返回格式。
```angular2
{
    "A001":{
        "label":"佛山公司",
        "sub":{
            "B001":{
                "label":"总经办",
                "sub":[]
            },
            "B002":{
                "label":"综合管理部",
                "sub":[]
            },
            "B003":{
                "label":"市场部",
                "sub":{
                    "C001":{
                        "label":"三水",
                        "sub":[]
                    },
                    "C002":{
                        "label":"顺德二区",
                        "sub":[]
                    },
                    "C003":{
                        "label":"顺德一区",
                        "sub":[]
                    },
                    "C004":{
                        "label":"禅城",
                        "sub":[]
                    },
                    "C005":{
                        "label":"南海",
                        "sub":{
                            "D001":{
                                "label":"南海一区",
                                "sub":[]
                            },
                            "D002":{
                                "label":"南海二区",
                                "sub":[]
                            }
                        }
                    }
                }
            },
            "B004":{
                "label":"客服部",
                "sub":[]
            },
            "B005":{
                "label":"呼叫中心",
                "sub":[
            },
            "B006":{
                "label":"运营部",
                "sub":[]
            }
        }
    }
}
```
##5、提交数据结构。
####（1）多维数组，含有隶属关系
```angular2
[
    "organizations" => [
        "value" =>  [
            0 => "",
            1 => "D001",
            2 => "D003",
        ],
        "sub" =>  [
            "D001" => [
                "value" =>  [
                    0 => "B001",
                    1 => "B002",
                    2 => "B003",
                ]
            ],
            "D003" => [
                "value" =>  [
                    0 => "B030",
                ],
                "sub" => [
                    "B030" =>  [
                        "value" =>  [
                            0 => "B079",
                            1 => "B080",
                            2 => "B082",
                            3 => "B083",
                            4 => "B084",
                        ],
                        "sub" =>  [
                            "B084" =>  [
                                "value" =>  [
                                    0 => "B092",
                                    1 => "B093",
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ],
    "_token" => "8r7rB4xQi9zXUW9B6s5z4ltcOK3GBWjGhr3K"
]
```
####（2）一维数组，不含隶属关系
```angular2
[
    "organizations" => [
        "D001",
        "D013",
        "D003",
        "B030",
        "B079",
        "B080",
        "B082",
        "B083",
        "B084",
        "B092",
        "B093",
        "D017",
    ],
    "_token"        => "8r7rB4xQi9zXUW9B6s5z4cOK3GBWjGhr3K",
]
```

##6、总结
如果有什么不清楚，欢迎到https://blog.csdn.net/qq_38421226/article/details/105076693发表提问。
