<div class="{{$viewClass['form-group']}} {!! !$errors->has($errorKey) ? '' : 'has-error' !!}">
    <label for="{{$id}}" class="{{$viewClass['label']}} control-label">{{$showLabel?$label:' '}}</label>
    <div class="{{$viewClass['field']}}">
        @include('admin::form.error')
<<<<<<< HEAD
        <div class="checkbox-{{$unique}}" id="checkbox-{{$unique}}"></div>
=======
        <div class="checkbox-{{$unique}}" id="checkbox-{{$unique}}">
        </div>
>>>>>>> adb22b581a67098d408abc23d6a26ccf56eef808
        @include('admin::form.help-block')
    </div>
</div>

<script>
    $(function () {
        var treeData = {
// 复选框change事件
            onchange: function (input, yntree) {
                let {value, text, name, checked} = this;
                var func = {!! $changeEvent?$changeEvent: 'false' !!};
                if (func) {
                    func(value, name, text, checked);
                }
            },
            checkStrictly: true, //是否父子互相关联，默认true
            data: {!! $options !!}, //数据
        };
        /**下面是数据的初始化设置**/
        var yntree{{$unique}} = new YnTree(document.getElementById('checkbox-{{$unique}}'), treeData, {!! $configs !!});
        var fn = {!! $javascriptFunc ? $javascriptFunc: 'false'  !!};
        if (fn) {
            fn.call(this, yntree{{$unique}}, treeData, "checkbox-{{$unique}}");
        }
    })
</script>

<style>
<<<<<<< HEAD
    .checkbox-{{$unique}}{
=======
    .checkbox-{{$unique}}             {
>>>>>>> adb22b581a67098d408abc23d6a26ccf56eef808
        overflow: auto;
        max-width: {{$maxWidth}};
        max-height: {{$maxHeight}};
        min-width: {{$minWidth}};
        min-height: {{$minHeight}};
    }
</style>