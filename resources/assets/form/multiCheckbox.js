var MultiCheckBox = function ({
                                  column = '',
                                  url = '',
                                  label = '',
                                  uniqueKey = null,
                                  checkedValues = [],
                                  format = 'array',
                                  token = null,
                                  attach = {},
                              }) {
    this.uniqueKey = uniqueKey;
    this.column = column;
    this.url = url;
    this.checked_values = checkedValues;
    this.label = label;
    this.dataFormat = format;
    this._token = token;
    this.attach = attach;
    this.eventRegister().loadSubOptions(false, function () {
        this.checkedDefault().updateCheckedBox();
    });
    return this;
}

MultiCheckBox.prototype = {
    uniqueKey: null,
    // stop_to_options: false, //鼠标是否停留在某个选项中
    options_checked: {}, // 勾选的值默认值
    options_cache: {}, // 子选项缓存
    checked_values: [], // 选中的选项
    column: '', // 字段名称
    url: '', // 请求路由
    label: '', // 显示文本
    initCheckedOptions: true,
    dataFormat: 'object', // array提交一维数组， object提交多维数组（含有隶属关系）
    _token: '',
    attach: {},
};

MultiCheckBox.prototype.checkedDefault = function () {
    this.initCheckedOptions = false;
    return this;
}


/**
 * 更新更新已选列表
 * @returns {*}
 */
MultiCheckBox.prototype.updateCheckedBox = function () {
    // 已选的值
    let data = this.options_checked;
    let keys = Object.keys(data);
    // 获取select[multiple]的值
    let values = this.getSelectedValues();
    // 判断哪个的长度
    let obj = (keys.length > values.length ? keys : values);
    obj.forEach((index) => {
        if (values.indexOf(index) < 0) {
            this.bindCheckedToSubOptions(data[index], index);
            this.deleteSelectedValues([index])
            delete data[index];
        } else if (!data[index]) {
            data[index] = {
                label: this.options_cache[index].label,
                sub: {},
            }
        }
    });
    this.options_checked = data;
    this.updateChecked();
    return this;
}


/**
 * 加载子复选框
 * @param isChange
 * @returns {*}
 */
MultiCheckBox.prototype.loadSubOptions = function (isChange = false, callback = null) {
    let values = this.getSelectedValues(true);
    if (values.length) {
        this.ajaxRequest({
            data: values,
            url: this.url,
            callback: (rst) => {
                this.options_cache = Object.assign(this.options_cache, rst);
                this.updateSubOptions();
                if (callback) {
                    callback.call(this);
                } else if (isChange) {
                    this.updateCheckedBox();
                }
            }
        });
    } else {
        if (callback) {
            callback.call(this);
        } else if (isChange) {
            this.updateCheckedBox();
        }
    }
    return this;
}

/**
 * 获取select[multiple]选中的值
 * @param filter 是否过滤已经缓存过的值
 * @returns {any[]}
 */
MultiCheckBox.prototype.getSelectedValues = function (filter = false) {
    let options = document.querySelector('.wen-selected-box-' + this.uniqueKey).options;
    let values = new Array();
    for (var index in options) {
        let item = options[index];
        if (item.selected) {
            if (filter) {
                if (Object.keys(this.options_cache).indexOf(item.value) < 0) {
                    values.push(item.value);
                }
            } else {
                values.push(item.value);
            }
        }
    }
    return values;
}

/**
 * 发送ajax请求
 * @param data
 * @param url
 * @param callback
 */
MultiCheckBox.prototype.ajaxRequest = function ({
                                                    data,
                                                    url,
                                                    callback
                                                }) {
    $.ajax({
        url: url,
        dataType: 'json',
        data: {
            diffArr: data,
            attach: this.attach,
            _token: this._token,
        },
        success: function (rst) {
            callback && callback(rst);
        },
        fail: function (err) {
            console.error(err);
        }
    })
}

/**
 *
 * @param level
 * @returns {boolean}
 */
MultiCheckBox.prototype.checkSubOptions = function (level) {
    // console.log(1121212, this.checked_values, level);
    if (this.checked_values.indexOf(level) >= 0) {
        return true;
    }
    return false;
}

/**
 * 创建子复选框选项
 * @param data
 * @param level
 * @param checked
 * @returns {{li_html: string, last_level: boolean}}
 */
MultiCheckBox.prototype.appendLi = function (data, level = '', checked = '') {
    let liHtml = '', last_level = true, lastIndex = level.lastIndexOf('-'), name = '';
    for (var index in data) {
        let le = (level ? level + '-' : '') + index,
            id = this.column + '-' + (checked ? le : le + '-options'),
            checkAttr = '';
        if (checked || this.checkSubOptions(index)) {
            checkAttr = 'checked';
            if (checked) {
                this.pushValues(index);
                if (this.dataFormat == 'object') {
                    if (!name) {
                        if (level) {
                            if (lastIndex >= 0) {
                                name = this.column + '[sub][' + level.replace(/-/g, '][sub][') + '][value][]';
                            } else {
                                name = this.column + '[sub][' + level + '][value][]';
                            }
                        } else {
                            name = this.column + '[value][]';
                        }
                    }
                } else {
                    name = (data[index].column ? data[index].column : this.column) + '[]';
                }
            } else if (this.initCheckedOptions) { //初始化默认选项
                this.addObj(this.options_cache, le.split('-'), this.options_checked, '', false);
            }
        }
        liHtml += `<li>
            <div class="one-option-box">
                <div class="checkbox-box-wen">
                    <input  id="${id}" 
                            type="checkbox"
                            name="${name}" 
                            class="checkbox-input-wen" 
                            ${checkAttr} 
                            value="${index}"  
                            data-level="${le}" 
                            data-column="${name}" 
                            data-label="${data[index].label}" />
                    <label class="checkbox-fake-box" for="${id}"></label>
                </div>
                <div>${data[index].label}</div>
            </div>`;
        if (data[index].sub && Object.keys(data[index].sub).length) {
            liHtml += this.appendUl(data[index].sub, le, checked);
            last_level = false;
        }
        liHtml += `</li>`;
    }
    return {
        "li_html": liHtml,
        "last_level": last_level,
    };
}

/**
 * 创建子复选框列表
 * @param data
 * @param level
 * @param checked
 * @param custom_class
 * @param title
 * @returns {string}
 */
MultiCheckBox.prototype.appendUl = function (data, level = '', checked = '', custom_class = '', title = '') {
    if (title) {
        title = `<p class="title-box-wen">${title}</p>`
    }
    let liAppendRes = this.appendLi(data, level, checked);
    if (!liAppendRes.li_html) {
        return '';
    }
    let html = liAppendRes.li_html + `</div></ul>`;
    if (liAppendRes.last_level === true) {
        html = `<ul class="sub-options-box last-level-box ${custom_class} ${checked ? '' : custom_class ? ' hide sub-options-box-tttt ' : ''}">${title}<div class="sub-options-box-bbbb">` + html;
    } else {
        html = `<ul class="sub-options-box ${custom_class} ${checked ? '' : custom_class ? ' hide sub-options-box-tttt ' : ''}">${title}<div class="sub-options-box-bbbb">` + html;
    }
    return html;
}

/**
 * 更新子选择框的列表
 * @returns {*}
 */
MultiCheckBox.prototype.updateSubOptions = function () {
    let data = this.options_cache;
    let parentObj = document.querySelector('.sub-box-show-wen');
    for (var dex in data) {
        if (!document.querySelector('.' + dex)) {
            parentObj.innerHTML += this.appendUl(data[dex].sub, dex, '', dex, data[dex].label)
        }
    }
    return this;
}

/**
 * 更新选中的列表
 * @returns {*}
 */
MultiCheckBox.prototype.updateChecked = function () {
    if (Object.keys(this.options_checked).length) {
        document.querySelector('.show-checked-box-wen').classList.remove('hide');
    } else {
        document.querySelector('.show-checked-box-wen').classList.add('hide');
    }
    document.querySelector('.sub-box-checked-' + this.uniqueKey).innerHTML = this.appendUl(this.options_checked, '', 'checked');
    return this;
}


/**
 * 删除（多级）对象中某个指定属性
 * @param obj
 * @param name
 * @returns {*}
 */
MultiCheckBox.prototype.deleteObj = function (obj, name) {
    if (!(name instanceof Array)) {
        throw new Error('参数name不正确,请输入一个一维数组');
    }
    let index = name.shift();
    if (obj[index] || (obj.sub && obj.sub[index])) {
        if (name.length) {
            if (obj.sub) {
                obj.sub[index] = this.deleteObj(obj.sub[index], name);
            } else {
                obj[index] = this.deleteObj(obj[index], name);
            }
        } else {
            this.bindCheckedToSubOptions(obj[index] ? obj[index] : obj.sub[index], index)
            obj[index] ? delete obj[index] : delete obj.sub[index];
        }
    }
    return obj;
}

/**
 * 新增对象属性
 * @param obj
 * @param name
 * @param origin_obj
 * @param dex
 * @returns {*}
 */
MultiCheckBox.prototype.addObj = function (obj, name, origin_obj, dex = '', attendSub = true) {
    if (!(name instanceof Array)) {
        throw new Error('参数name不正确,请输入一个一维数组');
    }
    let index = name.shift();
    dex += (dex ? '-' + index : index);
    if (obj[index] && obj[index].label) {
        let dd = document.querySelector('#' + this.column + '-' + dex + '-options');
        dd ? dd.checked = true : '';
        if (!origin_obj[index]) {
            origin_obj[index] = {
                label: obj[index].label,
                sub: {},
            };
        }
        if (name.length) {
            origin_obj[index].sub = this.addObj(obj[index].sub, name, origin_obj[index].sub, dex, attendSub)
        } else if (attendSub) {
            origin_obj[index].sub = $.extend({}, obj[index].sub);
        } else {
            origin_obj[index].sub = {};
        }
    }
    return origin_obj;
}

/**
 *
 * @param val
 * @returns {MultiCheckBox}
 */
MultiCheckBox.prototype.pushValues = function (val) {
    if (typeof val == 'object') {
        for (var index in val) {
            this.checked_values.indexOf(index) < 0 && this.checked_values.push(index);
            if (val.sub && Object.keys(val.sub).length) {
                this.pushValues(val.sub);
            }
        }
    } else {
        this.checked_values.indexOf(val) < 0 && this.checked_values.push(val);
    }
    return this;
}

/**
 * 清空子选项中打钩的复选框
 * @param data
 * @param level
 */
MultiCheckBox.prototype.clearSubOptionsChecked = function (data, level) {
    if (Object.keys(data).length) {
        let hadChecked = this.checked_values;
        for (var dex in data) {
            let le = level + '-' + dex;
            let index = hadChecked.indexOf(le);
            if (index >= 0) {
                this.checked_values.splice(index, 1);
            }
            let dd = document.querySelector('#' + this.column + '-' + le + '-options');
            dd ? ($(dd).prop('checked', false) && $(dd).removeAttr('checked')) : '';
            if (Object.keys(data[dex].sub).length) {
                this.clearSubOptionsChecked(data[dex].sub, le);
            }
        }
    }
    return this;
}


/**
 * 清空某个以及选项下的所有子选项
 */
MultiCheckBox.prototype.bindCheckedToSubOptions = function (data, index) {
    if (data && data.label) {
        let obj = document.querySelector('#' + this.column + '-' + index + '-options');
        obj ? ($(obj).prop('checked', false) && $(obj).removeAttr('checked')) : '';
        obj ? ($(obj).parent().parent().siblings('ul').find('input[type="checkbox"]').prop('checked', false) && $(obj).parent().parent().siblings('ul').find('input[type="checkbox"]').removeAttr('checked')) : '';
    }
    return this;
}
/**
 * input[type="checkbox"]:checked 与 select[multiple]同步绑定
 * @param values
 * @returns {*}
 */
MultiCheckBox.prototype.deleteSelectedValues = function (values) {
    let hadChecked = this.checked_values;
    let data = this.options_checked;
    if (typeof values != 'object') {
        values = values.split('-');
    }
    values.forEach(function (item, index) {
        data = data[item] ? data[item] : data.sub[item];
    });
    let level = values.join('-');
    let index = hadChecked.indexOf(level);
    if (index >= 0) {
        this.checked_values.splice(index, 1);
    }
    let dd = document.querySelector('#' + this.column + '-' + level + '-options');
    dd ? ($(dd).prop('checked', false) && $(dd).removeAttr('checked')) : '';
    this.clearSubOptionsChecked(data.sub, level);
    return this;
}

MultiCheckBox.prototype.eventRegister = function () {
    // 第一级鼠标经过事件
    $(document).on('mouseover', '.select2-selection__choice', (e) => {
        // console.log(e.target, e.currentTarget);
        let classList = $(e.currentTarget).parent().parent().parent().parent().siblings('select')[0].classList.value;
        if (classList.indexOf('wen-selected-box-' + this.uniqueKey) >= 0) {
            // this.stop_to_options = true;
            let subWenObj = document.querySelector('.sub-box-wen');
            let obj = document.querySelector('.wen-selected-box-' + this.uniqueKey);
            for (var dex in obj.options) {
                if (obj.options[dex].innerText == e.currentTarget.title) {
                    let subObj = document.querySelector('.sub-box-show-wen .' + obj.options[dex].value);
                    subWenObj.classList.remove('hide');
                    if (subObj) {
                        subObj.classList.remove('hide');
                        $(subObj).siblings().addClass('hide');
                        $('.sub-box-wen-tips-box').addClass('hide');
                    } else {
                        $('.sub-box-scroll ul').addClass('hide');
                        $('.sub-box-wen-tips-box>.title-box-wen').html(e.currentTarget.title);
                        $('.sub-box-wen-tips-box').removeClass('hide');
                    }
                }
            }
            let winHeight = document.body.clientHeight - $(document).scrollTop();
            let bottom = (document.body.clientHeight - $(obj).offset().top - ($(obj).siblings('span').height())) * 0.8;
            let top = (winHeight - bottom - ($(obj).siblings('span').height())) * 0.80;
            if (top > bottom) {
                subWenObj.classList.add('sub-box-wen-top');
                subWenObj.style['max-height'] = top + 'px';
                $('.sub-options-box-tttt >.sub-options-box-bbbb').css('max-height', (top - 35) + 'px')
            } else {
                subWenObj.classList.add('sub-box-wen-bottom')
                subWenObj.style['max-height'] = bottom + 'px';
                $('.sub-options-box-tttt>.sub-options-box-bbbb').css('max-height', (bottom - 35) + 'px')
            }
        }
    });

    // // 离开事件以及标签
    // $(document).on('mouseleave', '.select2-selection__choice', (e) => {
    //     let classList = $(e.currentTarget).parent().parent().parent().parent().siblings('select')[0].classList.value;
    //     // 判断是否为当前页面的标签触发事件
    //     if (classList.indexOf('wen-selected-box-' + this.uniqueKey) >= 0) {
    //         this.stop_to_options = false;
    //     }
    // });

    // 隐藏选项框
    $(document).on('mouseleave', '.wen-selected-box', (e) => {
        let subObj = document.querySelector('.sub-box-wen ul');
        subObj ? subObj.classList.add('hide') : '';
        $(".sub-box-wen").addClass('hide');
        $('.sub-box-wen-tips-box').addClass('hide');
    })

    /**
     * 选中复选框值改变事件
     */
    $(document).on('change', '.show-checked-box-wen input[type=checkbox]', (e) => {
        // console.log('change1', e);
        let level = e.currentTarget.dataset.level.split('-');
        let dataset = e.currentTarget.dataset;
        this.checked_values = [];
        this.deleteSelectedValues(level);
        this.options_checked = this.deleteObj(this.options_checked, level);
        this.updateChecked();
        $(".wen-selected-box-" + this.uniqueKey).siblings('span').find('li[title="' + dataset.label + '"]').children('span').click();
        if (Object.keys(this.options_checked).length) {
            $('body>.select2-container--open ul').html('');
        }
        console.log(this);
    });

    /**
     * 选项复选框值改变事件
     */
    $(document).on('change', '.sub-box-wen input[type=checkbox]', (e) => {
        let level = e.currentTarget.dataset.level.split('-');
        this.checked_values = [];
        if (e.currentTarget.checked) {
            this.options_checked = this.addObj(this.options_cache, level, this.options_checked);
            $(e.currentTarget).parent().parent().siblings('.sub-options-box').find('input[type="checkbox"]').prop('checked', true);
        } else {
            this.deleteObj(this.options_checked, level);
            $(e.currentTarget).parent().parent().siblings('ul').find('input[type="checkbox"]').removeAttr('checked')
        }
        this.updateChecked();
        console.log(this);
    });

    /**
     * select复选下拉框值改变事件
     */
    $(".wen-selected-box-" + this.uniqueKey).on('change', (e) => {
        this.loadSubOptions(true);
    });
    return this;
}