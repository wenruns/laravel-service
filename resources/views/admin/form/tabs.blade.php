<div class="{{$viewClass['form-group']}} wen-tabs-box">
    @include('admin::form.error')
    <div class="wen-tabs-list">
        @foreach($tabs as $k => $tab)
            <div class="{{$tab['class']}}-tab-name wen-tabs-name @if($activeClass==$tab['class']) wen-tabs-name-active @endif"
                 data-class="{{$tab['class']}}" data-name="{{$tab['tabName']}}">
                {{$tab['tabName']}}
            </div>
        @endforeach
        @if($fieldName)
            <input type="text" name="{{$fieldName}}" value="{{$activeTab}}" hidden>
        @endif
    </div>
    <div class="wen-tabs-contents">
        @foreach($tabs as $k => $tab)
            <div class="{{$tab['class']}}-tab-content wen-tabs-content @if($activeClass==$tab['class']) wen-tabs-show @endif">
                {!! $tab['content'] !!}
            </div>
        @endforeach
    </div>
    @include('admin::form.help-block')
</div>
<script>
    $(".wen-tabs-name").click(function (e) {
        $(this).addClass('wen-tabs-name-active');
        $(this).siblings().removeClass('wen-tabs-name-active');
        $("." + e.currentTarget.dataset.class + "-tab-content").addClass('wen-tabs-show');
        $("." + e.currentTarget.dataset.class + "-tab-content").siblings().removeClass('wen-tabs-show');
        $("input[name='{{$fieldName}}']").val(e.currentTarget.dataset.name);
        var fn = {!! $eventFunc?$eventFunc:'false' !!};
        if (fn) {
            fn.call(this, e)
        }
    });
</script>
<style>
    .wen-tabs-box {
        position: relative;
    }

    .wen-tabs-list {
        display: flex;
        border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        /*border-top: 1px solid rgba(0, 0, 0, 0.1);*/
    }

    .wen-tabs-name {
        cursor: pointer;
        margin-left: 20px;
        margin-bottom: -1px;
        padding: 10px 15px;
        border-top: 2px solid transparent;
        border-top-right-radius: 3px;
        border-top-left-radius: 3px;
    }

    .wen-tabs-name-active {
        border-bottom: 1px solid white;
        border-top: 2px solid #3c8dbc;
        border-left: 1px solid rgba(0, 0, 0, 0.1);
        border-right: 1px solid rgba(0, 0, 0, 0.1);
    }

    .wen-tabs-contents {
        /*margin-top: 20px;*/
        max-height: {{$maxHeight}};
        max-width: {{$maxWidth}};
        min-height: {{$minHeight}};
        min-width: {{$minWidth}};
        overflow: auto;
    }

    .wen-tabs-content {
        display: none;
    }

    .wen-tabs-show {
        display: block;
    }

    .wen-tabs-contents .box-body {
        padding: 10px 10px !important;
    }

</style>