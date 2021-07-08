<div class="{{$viewClass['form-group']}} {!! !$errors->has($errorKey) ? '' : 'has-error' !!}">
    <label for="{{$id}}" class="{{$viewClass['label']}} control-label">{{$label}}</label>
    <div class="{{$viewClass['field']}}" id="input-loan-{{$column}}" style="position:relative;">
        @include('admin::form.error')

        <div class="input-group">

            @if ($prepend)
                <span class="input-group-addon">{!! $prepend !!}</span>
            @endif
            <input {!! $attributes !!} data-isLoanLoanArea="true"/>
            @if ($append)
                <span class="input-group-addon clearfix">{!! $append !!}</span>
            @endif
        </div>
        @include('admin::form.help-block')
    </div>
    <style>
        .select-{{$column}}:hover {
            background-color: #3c8dbc;
            color: white;
        }
    </style>
    <script>
        let inputLoanValue_{{$column}} = '',
            inputLoanHandle_{{$column}} = null,
            inputLoanElement_{{$column}} = null,
            resultData_{{$column}}= null,
            ulElement_{{$column}}= null,
            defaultValueIndex_{{$column}}= null;

        function options_{!! $column !!}(rst, optionFields = ['{{$column}}']) {
            if (ulElement_{{$column}}) {
                return false;
            }
            if (inputLoanElement_{{$column}}) {
                inputLoanElement_{{$column}}.style.display = 'none';
            }
            ulElement_{{$column}} = document.createElement('ul');
            let liHtml = '';
            if (rst.optionFields && rst.optionFields.length) {
                optionFields = rst.optionFields;
            }
            let columnID = '{{$column}}', top = $("#" + columnID).parent().height() + 2, val = $("#" + columnID).val();
            ulElement_{{$column}}.classList.value = 'select2-results__options select2-dropdown select2-container--default select2-dropdown-{{$column}}';
            ulElement_{{$column}}.style = 'position:absolute;left: 15px;top:' + top + 'px;z-index:9999;padding:0px;max-height:300px;overflow:auto;width:calc(100% - 30px);box-shadow:0px 0px 3px 0px orange;';
            rst.data.forEach((item, index) => {
                liHtml += `<li class="select2-results__option col-md-12 select-{{$column}}" style="display: flex;justify-content: space-between;cursor: pointer;${index == defaultValueIndex_{{$column}}? 'background-color: #3c8dbc;color: white;' : ''}" data-index="${index}" data-isLoanLoanArea="true">`;
                optionFields.forEach((field, dex) => {
                    liHtml += `<span>${item[field].value}<\/span>`;
                })
                liHtml += `<\/li>`;
            })
            ulElement_{{$column}}.innerHTML = liHtml;
            $(ulElement_{{$column}}).insertAfter("#input-loan-{{$column}} .input-group");
            $(".select-{{$column}}").click(function (e) {
                ulElement_{{$column}}.remove();
                ulElement_{{$column}} = null;
                let index = e.currentTarget.dataset.index, res = rst;
                res.data = rst.data[index] ? rst.data[index] : rst.data;
                showDetail_{!! $column !!}(res)
                defaultValueIndex_{{$column}}= index;
            });
        }

        function showDetail_{!! $column !!}(rst) {
            $('#{{$column}}').val(rst.data['{{$column}}'].value);
            var callback = `{!! $javascriptCallback !!}`;
            if (callback) {
                eval("var fn =" + callback + '; if(fn){ fn.call(this, rst); }');
            }
            if ({!! $disableDetail !!}) {
                if (inputLoanElement_{{$column}}) {
                    inputLoanElement_{{$column}}.style.display = 'none';
                }
                return false;
            }
            if (inputLoanElement_{{$column}}) {
                inputLoanElement_{{$column}}.style.display = 'block';
            }
            let html = '<div class="box-body">';
            if (typeof rst.data == 'object') {
                for (var column in rst.data) {
                    let item = rst.data[column];
                    html += `<div class="col-md-${item['col_md']}" style="display:${item['show']}">
                                            <label class="control-label">${item['label']}<\/label>
                                            <div>
                                                <div class="box box-solid box-default no-margin">
                                                    <div class="box-body product_name" id="show-{{$column}}-${column}">${item['value']}<\/div>
                                                <\/div>
                                            <\/div>
                                        <\/div>`;
                }
            } else {
                html += rst.data;
            }
            html += '<\/div>';
            if (rst.footerCallback) {
                eval('var footerCallback = ' + rst.footerCallback + '; try{ html += footerCallback.call(this, rst); }catch (e) {}');
            }
            inputLoanElement_{{$column}}.innerHTML = html;
        }

        $(function () {
            $('#{{$column}}').focus(function (e) {
                if (resultData_{{$column}} && '[object Array]' == Object.prototype.toString.call(resultData_{{$column}}.data)) {
                    let rst = $.extend({}, resultData_{{$column}});
                    $(this).val(inputLoanValue_{{$column}});
                    options_{!! $column !!}(rst);
                }
            });
            $('body').click(function (e) {
                if (typeof e.target.dataset.isloanloanarea == 'undefined') {
                    if (ulElement_{{$column}}) {
                        ulElement_{{$column}}.remove();
                        ulElement_{{$column}} = null;
                        if (resultData_{{$column}}.data[defaultValueIndex_{{$column}}]) {
                            let data = resultData_{{$column}}.data[defaultValueIndex_{{$column}}];
                            $('#{{$column}}').val(data['{{$column}}'].value);
                        } else {
                            if (inputLoanElement_{{$column}}) {
                                inputLoanElement_{{$column}}.innerHTML = `<div class="box-body">查询失败：查询不到相关信息<\/div>`;
                            }
                        }
                    }
                }

            });
            $('#{{$column}}').keyup(function (e) {
                let exceptCodes = [18, 91, 17, 18, 92, 93, 17, 45, 36, 35, 9];
                if (exceptCodes.indexOf(e.keyCode) >= 0) {
                    return false;
                }
                inputLoanValue_{{$column}} = e.target.value;
                if (inputLoanHandle_{{$column}}) {
                    clearTimeout(inputLoanHandle_{{$column}});
                }
                if (ulElement_{{$column}}) {
                    ulElement_{{$column}}.remove();
                    ulElement_{{$column}} = null;
                }
                if (!inputLoanValue_{{$column}}) {
                    if (inputLoanElement_{{$column}}) {
                        inputLoanElement_{{$column}}.remove();
                        inputLoanElement_{{$column}}= null;
                    }
                    return false;
                }
                inputLoanHandle_{{$column}} = setTimeout(function () {
                    clearTimeout(inputLoanHandle_{{$column}});
                    if (inputLoanValue_{{$column}} == e.target.value && inputLoanValue_{{$column}}) {
                        resultData_{{$column}} = null;
                        defaultValueIndex_{{$column}}= null;

                        let url = '{{$api}}', extraData = {!! $extraData !!};
                        if (!url) url = window.location.href;

                        if (!inputLoanElement_{{$column}}) {
                            inputLoanElement_{{$column}} = document.createElement('div');
                            inputLoanElement_{{$column}}.classList.value = 'box box-solid box-default no-margin';
                        } else {
                            inputLoanElement_{{$column}}.style.display = 'block';
                        }
                        inputLoanElement_{{$column}}.innerHTML = `<div class="box-body">正在查询<\/div>`;
                        $(inputLoanElement_{{$column}}).insertAfter('#input-loan-{{$column}} .input-group');
                        $.ajax({
                            url: url,
                            method: '{{$method}}',
                            data: {
                                value: inputLoanValue_{{$column}},
                                extra: extraData.data,
                                _token: '{!! csrf_token() !!}',
                            },
                            dataType: 'json',
                            success: rst => {
                                if (rst.status) {
                                    resultData_{{$column}} = $.extend({}, rst);
                                    if ('[object Array]' == Object.prototype.toString.call(rst.data)) {
                                        options_{!! $column !!}(rst)
                                    } else {
                                        showDetail_{!! $column !!}(rst);
                                    }
                                } else {
                                    inputLoanElement_{{$column}}.innerHTML = `<div class="box-body">查询失败：${rst.msg}<\/div>`;
                                }
                            },
                            fail: err => {
                                inputLoanElement_{{$column}}.innerHTML = `<div class="box-body">查询失败：${err.message}<\/div>`;
                            }
                        });
                    } else {
                        if (inputLoanElement_{{$column}}) inputLoanElement_{{$column}}.remove();
                    }
                }, 500);
            });
        })
    </script>
</div>
