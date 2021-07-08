<div class="{{$viewClass['form-group']}} {!! !$errors->has($errorKey) ? '' : 'has-error' !!} select2-box-wen">

    <label for="{{$id}}" class="{{$viewClass['label']}} control-label">{{$label}}</label>

    <div class="{{$viewClass['field']}} wen-selected-box">
        @include('admin::form.error')
        <select class="form-control {{$class}} wen-selected-box-{{$unique_key}}"
                style="width: 100%;"
                multiple
                data-placeholder="{{ $placeholder }}" {!! $attributes !!} >
            @foreach($options as $select => $option)
                <option value="{{$select}}" {{  in_array($select, (array)old($column, $value)) ?'selected':'' }}>{{$option}}</option>
            @endforeach
        </select>
        <input type="hidden" name="{{$format=='object' ? $column.'[value]' : $column}}[]" value="">
        <div class="checkbox-options-box-wen">
            <div class="sub-box-scroll sub-box-wen hide">
                <div class="sub-box-checkbox-wen sub-box-show-wen"></div>
                <div class="sub-box-wen-tips-box hide">
                    <p class="title-box-wen "></p>
                    <div class="tips">
                        <i class="fa fa-frown-o"></i>
                        <div class="content-wen">空空如也</div>
                    </div>
                </div>
            </div>
            <div class="show-checked-box-wen hide">
                <div class=" sub-box-checkbox-wen sub-box-checked-{{$unique_key}}"></div>
            </div>
        </div>
        @include('admin::form.help-block')
    </div>
</div>

<script>
    $(function () {
        let obj = new MultiCheckBox({
            column: '{{$name}}',
            label: '{{$label}}',
            checkedValues: {!! $checked_values !!},
            url: '{{$request_url}}',
            uniqueKey: '{{$unique_key}}',
            format: '{{$format}}',
            token: '{{csrf_token()}}',
            attach: {!! $attach !!},
        });
        console.log(obj);
    });
</script>

