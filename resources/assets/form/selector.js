var Selector = function ({
                             column, // 字段名称
                             label, // 显示名称
                             placeholder = '', // 提示内容
                             uniqueKey = '', // 唯一标识
                             values = [], // 默认值
                             options = {}, // 默认选项
                             url = "", // 搜索请求api
                             method = "GET", // 请求方式
                             attach = {}, // 请求附加数据
                             data = {}, // 请求参数
                             isMultiple = false, // 是否多选
                             changed = null, // 值发生变化事件
                             updated = null, // 更新选项后事件
                             beforeUpdate = null, // 更新选项前事件
                             beforeClear = null, // 清空默认值前事件
                             cleared = null, // 清空默认值后事件
                             defaultSelect = false, // 是否启用默认选中功能，true表示如果options中有且只有一个选项，默认选中该选项
                             pagination = false, // 是否启用分页功能
                             perPage = 20, // 每页显示个数，只针对非api，如果设置了url则不起作用，
                             totalPages = 0, // 设置总页数
                         }) {
    this.uniqueKey = uniqueKey;
    this.column = column;
    this.label = label;
    this.placeholder = placeholder ? placeholder : label;
    this.originOptions = options;
    this.perPage = perPage;
    this.pagination = pagination;
    this.totalPages = totalPages;
    if (this.pagination && !url) {
        this.initPagination();
    } else {
        this.options = options;
    }
    this.values = values;
    this.url = url;
    this.method = method;
    this.attach = attach;
    this.data = data;
    this.isMultiple = isMultiple;
    this.changed = changed;
    this.updated = updated;
    this.beforeUpdate = beforeUpdate;
    this.beforeClear = beforeClear;
    this.cleared = cleared;
    this.defaultSelect = defaultSelect;
    this.init().initOptions().listen();
}

Selector.prototype = {
    defaultSelect: false,
    show: false,
    isChange: false,
    values: [],
    labels: {},
    url: '',
    method: 'GET',
    attach: {},
    data: {},
    originOptions: {},
    options: {},
    originApiResult: {},
    apiResult: {},
    keyword: null,
    queryWord: null,
    isMultiple: false,
    uniqueKey: '',
    column: null,
    label: '',
    placeholder: '',
    changed: null,
    updated: null,
    beforeUpdate: null,
    beforeClear: null,
    cleared: null,
    page: 1,
    pagination: true,
    totalPages: 0,
    perPage: 0,
    matchResult: {},
}

Selector.prototype.init = function () {
    this.labels = {};
    this.matchResult = {};
    this.apiResult = {};
    this.originApiResult = {};
    this.page = 1;
    return this;
}

Selector.prototype.initPagination = function () {
    var page = 0, values = Object.keys(this.originOptions);
    this.matchResult = {}, this.page = 1;
    if (values.length) {
        values.forEach((val, key) => {
            if (key % this.perPage == 0) {
                page++;
                this.matchResult[page] = {};
            }
            this.matchResult[page][val] = this.originOptions[val];
        });
        this.options = this.matchResult[this.page];
    }
    this.totalPages = page;
    return this;
}

/**
 * 初始化选项值
 * @returns {Selector}
 */
Selector.prototype.initValues = function () {
    if (this.values.length) {
        this.isChange = true;
    }
    this.values = [];
    this.labels = {};
    this.show = false;
    if (this.defaultSelect && Object.keys(this.options).length == 1) {
        this.options = [];
        this.originOptions = [];
    }
    return this;
}
/**
 * 显示选项列表
 * @returns {Selector}
 */
Selector.prototype.showOptions = function () {
    this.show = true;
    $(".form-group-" + this.uniqueKey + " .select2-container__options").show();
    $(".form-group-" + this.uniqueKey + " .select2-search__field").focus();
    this.presentation(180);
    return this;
}

/**
 * 隐藏选项列表
 * @returns {Selector}
 */
Selector.prototype.hideOptions = function () {
    this.show = false;
    $(".form-group-" + this.uniqueKey + " .select2-container__options").hide();
    this.presentation(0);
    this.keyword = null;
    this.queryWord = null;
    $(".form-group-" + this.uniqueKey + " .select2-search__field").val('');
    return this;
}
/**
 * 单选--icon转动
 * @param deg
 * @returns {Selector}
 */

Selector.prototype.presentation = function (deg) {
    $(".form-group-" + this.uniqueKey + " .presentation").css('transform', 'rotate(' + deg + 'deg)');
    $(".form-group-" + this.uniqueKey + " .presentation").css('-ms-transform', 'rotate(' + deg + 'deg)');
    $(".form-group-" + this.uniqueKey + " .presentation").css('-moz-transform', 'rotate(' + deg + 'deg)');
    $(".form-group-" + this.uniqueKey + " .presentation").css('-webkit-transform', 'rotate(' + deg + 'deg)');
    $(".form-group-" + this.uniqueKey + " .presentation").css('-o-transform', 'rotate(' + deg + 'deg)');
    return this;
}
/**
 *
 * @returns {Selector}
 */
Selector.prototype.checkDefaultSelect = function () {
    if (this.defaultSelect && Object.keys(this.options).length == 1) {
        this.setValue(Object.keys(this.options).pop()).hideOptions();
    }
    return this;
}

/**
 * 初始化选项列表
 * @returns {Selector}
 */
Selector.prototype.initOptions = function () {
    return this.checkDefaultSelect()
        .fnCall(this.beforeUpdate, 'beforeUpdate', {
            apiResult: this.apiResult,
            options: this.options,
        })
        .updateOptions()
        .initSelectionOptions()
        .fnCall(this.updated, 'updated')
        .checkChangeEvent();
}
/**
 * 更新选项列表
 * @returns {Selector}
 */

Selector.prototype.updateOptions = function () {
    let optionStr = '<option value=""></option>',
        liStr = '',
        options = this.options,
        optionObj = document.querySelector('.form-group-' + this.uniqueKey + ' .select2-results__options'),
        selector = document.querySelector("select#" + this.column),
        values = Object.keys(options);
    values.forEach((val) => {
        let label = options[val],
            selected = '',
            highlighted = '',
            selectedBool = 'false';
        if (this.checkIfSelected(val)) {
            if (!this.labels[val]) {
                this.labels[val] = label;
                this.isChange = true;
            }
            selected = 'selected';
            highlighted = 'select2-results__option--highlighted';
            selectedBool = 'true';
        }
        optionStr += `<option value="${val}" ${selected}>${label}</option>`;
        liStr += `<li class="select2-results__option ${highlighted}" aria-selected="${selectedBool}" data-value="${val}" data-uniquekey="${this.uniqueKey}" data-label="${label}">${label}</li>`;
    })
    if (!liStr) {
        liStr = `<li class="select2-results__option select2-results__message" aria-selected="false" data-uniquekey="${this.uniqueKey}">No results found</li>`;
    } else if (this.pagination) {//&& values.length == this.perPage
        liStr += `<li class="select2-results__option select2-results__message" aria-selected="false" data-uniquekey="${this.uniqueKey}" style="text-align: right;">`;
        liStr += `<span class="select2-results__option-page" data-uniquekey="${this.uniqueKey}" style="margin-right: 10px;">第 ${this.page} 页，共 ${this.totalPages} 页</span>`;
        liStr += `<span class="select2-results__option-page btn btn-xs btn-info" data-uniquekey="${this.uniqueKey}" data-page="first" style="margin-right: 10px;">首页</span>`;
        liStr += `<span class="select2-results__option-page btn btn-xs btn-info" data-uniquekey="${this.uniqueKey}" data-page="up" style="margin-right: 10px;">上一页</span>`;
        liStr += `<span class="select2-results__option-page btn btn-xs btn-info" data-uniquekey="${this.uniqueKey}" data-page="down" style="margin-right: 10px;">下一页</span>`;
        liStr += `<span class="select2-results__option-page btn btn-xs btn-info" data-uniquekey="${this.uniqueKey}" data-page="last">尾页</span>`;
        liStr += `</li>`;
    }
    if (!optionStr) {
        optionStr = '';
    }
    selector.innerHTML = optionStr;
    optionObj.innerHTML = liStr;
    return this;
}
/**
 * 检测是否选中
 * @param val
 * @returns {boolean}
 */
Selector.prototype.checkIfSelected = function (val) {
    let selected = false;
    try {
        this.values.forEach(item => {
            if (item == val) {
                selected = true;
                throw new Error('break')
            }
        })
    } catch (e) {
    }
    return selected;
}
/**
 * 更新选中值
 * @returns {*}
 */
Selector.prototype.initSelectionOptions = function () {
    if (this.isMultiple) {
        return this.multipleSelectionOptions();
    }
    let str = '', inputStr = '';
    Object.keys(this.labels).forEach((val) => {
        str += `<span class="select2-selection__clear" data-uniquekey="${this.uniqueKey}">×</span>${this.labels[val]}`;
        inputStr += `<input type="hidden" name="${this.column}" value="${val}" />`;
    })
    if (!inputStr) {
        inputStr = `<input type="hidden" name="${this.column}" />`;
    }
    if (!str) {
        str = `<span class="select2-selection__placeholder" data-uniqueKey="${this.uniqueKey}">${this.placeholder}</span>`;
    }
    document.querySelector('.form-group-' + this.uniqueKey + ' .select2-selection-rendered').innerHTML = str;
    document.querySelector('.form-group-' + this.uniqueKey + ' .input-box').innerHTML = inputStr;
    return this;
}

/**
 * 多选 -- 更新选中值
 * @returns {Selector}
 */
Selector.prototype.multipleSelectionOptions = function () {
    let str = '', inputStr = '', keys = Object.keys(this.labels);
    if (keys.length) {
        str += `<ul class="select2-selection__rendered " data-uniquekey="${this.uniqueKey}"><span class="select2-selection__clear" data-uniquekey="${this.uniqueKey}">×</span>`;
        keys.forEach((val) => {
            str += `<li class="select2-selection__choice" ><span class="select2-selection__choice__remove" data-uniquekey="${this.uniqueKey}" data-value="${val}">×</span>${this.labels[val]}</li>`;
            inputStr += `<input type="hidden" name="${this.column}[]" value="${val}" />`;
        })
        str += `</ul>`;
    } else {
        str = `<span class="select2-selection__placeholder" style="padding: 0px 10px;">${this.placeholder}</span>`
        inputStr = `<input type="hidden" name="${this.column}[]" />`
    }
    document.querySelector('.form-group-' + this.uniqueKey + ' .select2-selection-rendered').innerHTML = str;
    document.querySelector('.form-group-' + this.uniqueKey + ' .input-box').innerHTML = inputStr;
    return this;
}

/**
 * 查询匹配
 * @param e
 * @returns {Selector}
 */
Selector.prototype.queryRequest = function (e) {
    this.keyword = e.currentTarget.value;
    this.doQuery()
    return this;
}

/**
 * 查询匹配
 * @returns {*}
 */
Selector.prototype.doQuery = function () {
    if (this.keyword == this.queryWord) {
        return false;
    }
    this.queryWord = this.keyword;
    this.page = 1;
    this.matchResult = {};
    if (this.url) {
        this.queryByApi();
    } else {
        this.queryByMatch().initOptions();
    }
    return this;
}

/**
 * 只对已有的选项进行匹配
 * @returns {Selector}
 */
Selector.prototype.queryByMatch = function () {
    if (this.keyword) {
        this.changeQueryStatus();
        if (this.pagination) {
            let page = 0, key = 0;
            Object.keys(this.originOptions).forEach((val) => {
                if (this.originOptions[val].indexOf(this.keyword) >= 0) {
                    if (key % this.perPage == 0) {
                        page++;
                        this.matchResult[page] = {};
                    }
                    this.matchResult[page][val] = this.originOptions[val];
                    key++;
                }
            });
            this.options = this.matchResult[this.page] ? this.matchResult[this.page] : {};
            this.totalPages = page;
        } else {
            this.options = {};
            Object.keys(this.originOptions).forEach((val) => {
                if (this.originOptions[val].indexOf(this.keyword) >= 0) {
                    this.options[val] = this.originOptions[val];
                }
            });
        }
    } else {
        if (this.pagination) {
            this.initPagination();
        } else {
            this.options = this.originOptions;
        }
        this.apiResult = this.originApiResult;
    }
    return this;
}

/**
 * 通过api进行查询
 * @returns {*}
 */
Selector.prototype.queryByApi = function () {
    if (!this.keyword) {
        this.options = this.originOptions;
        this.apiResult = this.originApiResult;
        return this.initOptions();
    }
    if (this.matchResult[this.page]) {
        this.options = this.matchResult[this.page].options;
        this.apiResult = this.matchResult[this.page].apiResult;
        this.totalPages = this.matchResult[this.page].totalPages;
        return this.initOptions();
    }
    this.changeQueryStatus();
    let params = {
        q: this.keyword,
        attach: this.attach,
        page: this.page,
        pagination: this.pagination,
    };
    params = Object.assign(params, this.data);
    $.ajax({
        url: this.url,
        method: this.method,
        data: params,
        success: (rst) => {
            try {
                rst = JSON.parse(rst);
            } catch (e) {
                throw new Error('api返回结果必须是json字符串')
            }
            this.apiResult = rst;
            if (rst.options) {
                this.options = rst.options;
            } else {
                this.options = rst;
            }
            if (this.pagination) {
                let totalPages = Number(rst.totalPages);
                if (!totalPages) {
                    totalPages = 1;
                }
                this.matchResult[this.page] = {
                    options: this.options,
                    apiResult: this.apiResult,
                    totalPages: totalPages,
                }
                this.totalPages = totalPages;
            }
            this.initOptions();
        },
        fail: (err) => {
            this.changeQueryStatus('查询失败')
        }
    })
    return this;
}
/**
 * 更新查询状态
 * @returns {Selector}
 */

Selector.prototype.changeQueryStatus = function (msg = '正在查询......') {
    document.querySelector('.form-group-' + this.uniqueKey + ' .select2-results__options').innerHTML = `<li class="select2-results__option select2-results__message" aria-selected="false" data-uniquekey="${this.uniqueKey}">${msg}</li>`;
    return this;
}
/**
 * 触发改变事件
 */
Selector.prototype.checkChangeEvent = function () {
    if (this.isChange) {
        this.isChange = false;
        this.fnCall(this.changed, 'changed')
    }
    return this;
}
/**
 * 选中或移除选项
 * @param e
 * @returns {Selector}
 */
Selector.prototype.makeChoice = function (e) {
    let val = e.target.dataset.value;
    if (this.isMultiple) {
        if (e.target.attributes['aria-selected'].value == 'true') {
            this.remove(val)
        } else {
            this.select(val);
        }
    } else {
        this.select(val);
    }
    return this;
}
/**
 * 选中
 * @param val
 * @returns {*|void}
 */
Selector.prototype.select = function (val) {
    return this.setValue(val).initOptions().hideOptions();
}
/**
 * 设置选中值
 * @param val
 * @returns {Selector}
 */
Selector.prototype.setValue = function (val) {
    if (this.isMultiple) {
        this.values.push(val);
    } else {
        this.labels = {};
        this.values = [val];
    }
    this.originOptions = this.options;
    this.originApiResult = this.apiResult;
    return this;
}
/**
 * 移除选项
 * @param val
 * @returns {*|void}
 */
Selector.prototype.remove = function (val) {
    this.values.splice(this.values.indexOf(val), 1);
    delete this.labels[val];
    return this.initOptions().hideOptions();
}
/**
 * 移除选项
 * @param e
 * @returns {Selector}
 */
Selector.prototype.removeOption = function (e) {
    let val = e.target.dataset.value;
    return this.remove(val);
}
/**
 * 执行回调
 * @param fn
 * @param period
 * @param params
 */
Selector.prototype.fnCall = function (fn, period, params = {}) {
    if (fn) {
        fn.call(this, Object.assign({
            period,
            options: this.originOptions,
            value: this.labels,
            apiResult: this.originApiResult,
            data: this.data,
            attach: this.attach,
        }, params));
    }
    return this;
}
/**
 * 事件监听
 * @returns {Selector}
 */
Selector.prototype.listen = function () {
    $("body").click((e) => {
        let classList = e.target.classList.value, uniqueKey = e.target.dataset.uniquekey;
        if (uniqueKey == this.uniqueKey) {
            if (classList.indexOf('select2-selection--single') >= 0
                || classList.indexOf('select2-selection--multiple') >= 0
                || classList.indexOf('select2-selection__rendered') >= 0
                || classList.indexOf('select2-selection__placeholder') >= 0
                || classList.indexOf('select2-selection__arrow') >= 0) {
                if (this.show) {
                    this.hideOptions();
                    return;
                }
                // 显示或隐藏选项列表
                if (this.pagination && !this.url && this.options != this.matchResult[this.page]) {
                    this.options = this.matchResult[this.page];
                    this.initOptions();
                } else if ((!this.pagination || this.url) && this.options != this.originOptions) {
                    this.options = this.originOptions;
                    this.initOptions();
                }
                this.showOptions();
            } else if (classList.indexOf('select2-selection__clear') >= 0) {
                // 清空按钮点击事件
                this.fnCall(this.beforeClear, 'beforeClear')
                    .initValues()
                    .initOptions()
                    .showOptions()
                    .fnCall(this.cleared, 'cleared');
            } else if (classList.indexOf('select2-results__option-page') >= 0) {
                let page = e.target.dataset.page;
                if (this.pagination) {
                    if (this.url) {
                        switch (page) {
                            case 'first':
                                this.page = 1;
                                break;
                            case 'up':
                                this.page -= 1;
                                if (this.page < 1) {
                                    this.page = 1;
                                }
                                break;
                            case 'down':
                                this.page += 1;
                                if (this.page > this.totalPages) {
                                    this.page = this.totalPages;
                                }
                                break;
                            case 'last':
                                this.page = this.totalPages;
                                break;
                            default:
                        }
                        this.queryByApi();
                    } else {
                        switch (page) {
                            case 'first':
                                this.page = 1;
                                break;
                            case 'up':
                                this.page -= 1;
                                if (this.page < 1) {
                                    this.page = 1;
                                }
                                break;
                            case 'down':
                                this.page += 1;
                                if (this.page > this.totalPages) {
                                    this.page = this.totalPages;
                                }
                                break;
                            case 'last':
                                this.page = this.totalPages;
                                break;
                            default:
                        }
                        this.options = this.matchResult[this.page] ? this.matchResult[this.page] : {};
                        this.initOptions();
                    }
                }
            } else if (classList.indexOf('select2-results__option') >= 0) {
                // 选择选项
                if (classList.indexOf('select2-results__message') >= 0) {
                    return false;
                }
                this.makeChoice(e);
            } else if (classList.indexOf('select2-search__field') >= 0) {
            } else if (classList.indexOf('select2-selection__choice__remove') >= 0) {
                this.removeOption(e);
            } else {
                this.hideOptions();
            }
        } else {
            this.hideOptions();
        }
    })

    // 提交搜索请求
    $(".form-group-" + this.uniqueKey).on('keydown', '.select2-search__field', (e) => {
        if (e.keyCode == 13) {
            this.queryRequest(e);
            return false;
        }
    });
    // keyup事件
    $(".form-group-" + this.uniqueKey).on('keyup', '.select2-search__field', (e) => {
        if (!e.target.value) {
            if (this.pagination && !this.url) {
                this.initPagination();
            } else {
                this.options = this.originOptions;
            }
            this.apiResult = this.originApiResult;
            this.queryWord = '';
            this.keyword = '';
            this.initOptions();
        }
    });

    // 鼠标经过事件
    $(".form-group-" + this.uniqueKey).on('mouseover', '.select2-results__option', (e) => {
        if (e.currentTarget.classList.value.indexOf('select2-results__message') < 0) {
            e.currentTarget.classList.add('select2-results__option--highlighted');
        }
    });
    // 鼠标离开事件
    $(".form-group-" + this.uniqueKey).on('mouseleave', '.select2-results__option', (e) => {
        if (e.currentTarget.attributes['aria-selected'].value == 'true') {
            return false;
        }
        e.currentTarget.classList.remove('select2-results__option--highlighted');
    });

    return this;
}
