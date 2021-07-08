<div class="form-group-{{$uniqueKey}} {{$viewClass['form-group']}} {!! !$errors->has($errorKey) ? '' : 'has-error' !!}">
    <label for="{{$id}}" class="{{$viewClass['label']}} control-label">{{$label}}</label>
    <div class="{{$viewClass['field']}}">
        @include('admin::form.error')
        <span style="display: none;" class="input-box">
            @if($isMultiple)
                @if(empty($value))
                    <input type="hidden" name="{{$column}}[]">
                @else
                    @foreach($value as $v)
                        <input type="hidden" name="{{$column}}[]" value="{{$v}}">
                    @endforeach
                @endif
            @else
                <input type="hidden" name="{{$column}}" value="{{$value}}">
            @endif
        </span>
        <select class="form-control {{$column}} select2-{{$column}} select2-hidden-accessible"
                id="{{$column}}"
                style="width: 100%;"
                data-value=""
                tabindex="-1" aria-hidden="true" {!! $attributes !!}></select>
        <span class="select2 select2-container select2-container--default select2-container--below select2-{{$column}}"
              style="width: 100%;">
            <span class="selection">
                @if($isMultiple)
                    <span class="select2-selection select2-selection--multiple select2-selection-rendered"
                          role="combobox" aria-haspopup="true" aria-expanded="false" tabindex="-1"
                          data-uniquekey="{{$uniqueKey}}">
                         <span class="select2-selection__placeholder" style="padding: 0px 10px;">{{$placeholder}}</span>
                    </span>
                @else
                    <span class="select2-selection select2-selection--single" data-uniquekey="{{$uniqueKey}}">
                        <span class="select2-selection__rendered select2-selection-rendered"
                              data-uniqueKey="{{$uniqueKey}}">
                            <span class="select2-selection__placeholder">{{$placeholder}}</span>
                        </span>
                        <span class="select2-selection__arrow" data-uniqueKey="{{$uniqueKey}}">
                            <b class="presentation" data-uniquekey="{{$uniqueKey}}"></b>
                        </span>
                    </span>
                @endif
            </span>
            <span class="select2-container select2-container--default select2-container__options"
                  data-uniquekey="{{$uniqueKey}}"
                  style="position: absolute;  display: block;width: 100%;display: none;left: 0px;text-align: left;">
                <span class="select2-dropdown select2-dropdown--below" style="width: 100%; left: 0px;">
                    <span class="select2-search select2-search--dropdown" style="display: flex">
                        <input class="select2-search__field" data-uniquekey="{{$uniqueKey}}" type="search"
                               placeholder="请输入关键词，按【回车】搜索"/>
                    </span>
                    <span class="select2-results">
                        <ul class="select2-results__options" data-uniquekey="{{$uniqueKey}}"></ul>
                    </span>
                </span>
            </span>
        </span>
        @include('admin::form.help-block')
    </div>
</div>
<script>
    $(function () {
        new Selector({
            column: '{{$column}}',
            label: '{{$label}}',
            placeholder: '{{$placeholder}}',
            uniqueKey: '{{$uniqueKey}}',
            values: {!! $values !!},
            options: {!! $options !!},
            url: '{{$url}}',
            attach: {!! $attach !!},
            defaultSelect: {!! $defaultSelect ? 'true' : 'false' !!},
            data: {
                _token: '{!! csrf_token() !!}'
            },
            isMultiple:{!! $isMultiple?'true':'false' !!},
            pagination: {!! $pagination?'true':'false' !!},
            perPage: {{$perPage}},
            totalPages: {{$totalPages}},
            changed: function () {
                let event = '{!! $changed !!}';
                if (event) {
                    eval(`var fn = ${event};  fn.call(this, ...arguments);`);
                }
            },
            updated: function () {
                let event = '{!! $updated !!}';
                if (event) {
                    eval(`var fn = ${event};  fn.call(this, ...arguments);`);
                }
            },
            beforeUpdate: function () {

                let event = '{!! $beforeUpdate !!}';
                if (event) {
                    eval(`var fn = ${event};  fn.call(this, ...arguments);`);
                }
            },
            beforeClear: function () {
                let event = '{!! $beforeClear !!}';
                if (event) {
                    eval(`var fn = ${event};  fn.call(this, ...arguments);`);
                }
            },
            cleared: function () {
                let event = '{!! $cleared !!}';
                if (event) {
                    eval(`var fn = ${event};  fn.call(this, ...arguments);`);
                }
            },
        });
    })


</script>