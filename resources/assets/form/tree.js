function YnTree(ele, options, {
    hideCheckBox = false,
    spread = false,
    spreadChecked = true,
    uniqueKey = '',
}) {
    if (!ele || !ele.nodeName || ele.nodeType != 1) {
        throw"YnTree 第一个参数必须是一个元素！"
    }
    this.configs = {
        hideCheckBox,
        spreadChecked,
        spread,
    };
    this.uniqueKey = uniqueKey;
    this.inputFlag = this.inputFlag + '_' + this.uniqueKey;
    this.ele = ele;
    var type = this.getType(options);
    if (type != "object") {
        throw"YnTree 第二个参数必须是一个对象！"
    }
    this.options = options;
    !this.options.data ? (this.options.data = []) : "";
    if (this.getType(this.options.data) == "object") {
        this.options.data = [this.options.data]
    }
    this.data = [];
    this.parallel = [];
    this.id = "yn_tree" + (++this.count) + "_" + this.inputFlag;
    this.tree = this.createDomByString(`<ul class="yn-tree" id="${this.id}" style="padding: 0px;"></ul>`);
    this._init()
}

YnTree.prototype = {
    count: 0,
    inputCount: 0,
    classNameCfg: {
        spread: "spread",
        shrink: "shrink"
    },
    inputFlag: Math.ceil(Math.random() * 1000) + '_' + (new Date()).valueOf(),
    version: "1.0.0",
    uniqueKey: '',
    _init: function () {
        if (this.options.data.length > 0) {
            this._copyData(this.options.data, this.data);
            this._createDom(this.data);
            this._assemblyDom(this.tree, this.data)
        }
        this.ele.appendChild(this.tree);
        return this
    },
    _copyData: function (data, parent) {
        data = data || this.options.data;
        this.getType(data) != "array" ? (data = [data]) : "";
        this.forEach(data, (index, item) => {
            if (item.sub) {
                var obj = new CompositeLeaf(this, item, "composite", this.id);
                parent.push(obj);
                this.parallel.push(obj);
                this._copyData(item.sub, obj.sub)
            } else {
                var obj = new CompositeLeaf(this, item, "leaf", this.id);
                parent.push(obj);
                this.parallel.push(obj)
            }
        });
        return this
    },
    _createDom: function (data, parent) {
        var spread = this.configs.spread,
            spreadChecked = this.configs.spreadChecked,
            hideCheckBox = this.configs.hideCheckBox;
        this.forEach(data, (index, item) => {
            var html = "",
                id = "yn_tree_input" + (++this.inputCount) + '_' + this.inputFlag,
                nameVal = item.name ? item.name : "",
                htmlStr = '',
                inputStr = '',
                ulStr = '',
                val = item.value ? item.value : "",
                checked = item.checked ? item.checked : false,
                disabled = item.disabled ? item.disabled : false,
                className = item.className ? item.className : "",
                spreadOrShrink = spread ? (item.sub && item.sub.length ? 'spread' : 'shrink')
                    : (!spreadChecked ? 'shrink'
                        : (!checked ? 'shrink'
                            : (item.sub && item.sub.length ? 'spread' : 'shrink')));
            if (item.sub && item.sub.length) {
                htmlStr = `<span class="arrow arrow-right"></span>`;
                ulStr += `<ul class="yn-tree" style="padding: 0px;"></ul>`;
                item.type = "composite";
            } else {
                htmlStr = `<span class="no-arrow"></span>`;
                item.type = "leaf";
            }
            if (disabled) {
                inputStr = `<input type="checkbox" name="${nameVal}[]" value="${val}" ${checked ? 'checked="checked"' : ""} style="display: none;">`;
            }
            html += `<li class="yn-tree-li ${spreadOrShrink}" id="${id}_li" ${parent ? 'pid="' + parent.id + '"' : ""}>
                <div class="checkbox">
                    ${htmlStr}
                    <label>
                        <span class="text">${item.text}</span>
                        <input type="${hideCheckBox ? 'hidden' : 'checkbox'}" 
                               class="yn-tree-input ${className}" 
                               id="${id}" 
                               ${checked ? 'checked="checked"' : ""} 
                               ${disabled ? 'disabled="disabled"' : ""} 
                               ${parent && parent.id ? ' pid="' + parent.id + '"' : ""} 
                               name="${nameVal}[]" 
                               value="${val}" />
                        ${inputStr}
                    </label>
                </div>
                ${ulStr}
            </li>`;
            item.id = id;
            item.pid = parent ? parent.id : null;
            item.dom = this.createDomByString(html);
            this.bindChangeEvent(item.dom.querySelector(".yn-tree-input"), item);
            if (item.sub && item.sub.length) {
                this.arrowBindClickEvent(item.dom.querySelector(".arrow-right"), item);
                this._createDom(item.sub, item)
            }
        });
        return this
    },
    _assemblyDom: function (parent, data) {
        if (!parent && !data) {
            return this
        }
        this.getType(data) != "array" ? (data = [data]) : data;
        this.forEach(data, (index, item) => {
            parent.appendChild(item.dom);
            if (item.sub && item.sub.length) {
                this._assemblyDom(item.dom.querySelector(".yn-tree"), item.sub)
            }
        });
        return this
    },
    select: function (condition, flag, {up = true, down = true} = {}) {
        var dataItem = null;
        this.forEach(this.parallel, (index, item) => {
            var curInput = document.getElementById(item.id);
            if (condition === item.id || condition === item.text || condition === item.value || condition === curInput) {
                dataItem = item;
                return false
            }
        });
        if (dataItem) {
            dataItem.select(flag, {down, up});
        }
        return this
    },
    disable: function (condition, flag) {
        var dataItem = null;
        this.forEach(this.parallel, (index, item) => {
            var curInput = document.getElementById(item.id);
            if (condition === item.id || condition === item.text || condition === item.value || condition === curInput) {
                dataItem = item;
                return false
            }
        });
        dataItem.disable(flag);
        return this
    },
    getCheckedInputs: function () {
        var checkedInput = [];
        YnTree.forEach(this.parallel, (index, item) => {
            if (item.checked) {
                checkedInput.push(document.getElementById(item.id))
            }
        });
        return checkedInput
    },
    getValues: function () {
        var checkedVals = [];
        YnTree.forEach(this.getCheckedInputs(), (index, item) => {
            checkedVals.push(item.value)
        });
        return checkedVals
    },
    reInit: function (data) {
        if (data && this.getType(data) == "array") {
            this.options.data = data
        }
        if (this.options.data.length > 0) {
            this.ele.removeChild(this.tree);
            this.tree = this.createDomByString('<ul class="yn-tree" id="' + this.id + '" style="padding: 0px"></ul>');
            this.data = [];
            this._copyData(this.options.data, this.data);
            this._createDom(this.data);
            this._assemblyDom(this.tree, this.data);
            this.ele.appendChild(this.tree)
        }
        console.log("重新初始化");
        return this
    },
    destroy: function () {
        this.ele.removeChild(this.tree);
        this.options = null;
        this.parallel = null;
        this.tree = null;
        this.id = null;
        this.data = null;
        this.ele = null;
        console.info("YnTree销毁完毕，建议您将YnTree的实例置为null，如：\r\n var ynTree = new YnTree(...);\r\n ynTree.destroy();\r\n ynTree = null;")
    },
    spread: function (condition, flag) {
        var dataItem = null;
        YnTree.forEach(this.parallel, (index, item) => {
            var curInput = document.getElementById(item.id);
            if (condition === item.id || condition === item.text || condition === curInput) {
                dataItem = item;
                return false
            }
        });
        if (dataItem.type == "leaf") {
            return this
        }
        dataItem.spread(flag);
        return this
    },
    createDomByString: function (htmlStr) {
        var ele = document.createElement("div"), dom;
        ele.innerHTML = htmlStr;
        dom = ele.children;
        ele = null;
        return dom[0]
    },
    getType: function (data) {
        var type = Object.prototype.toString.call(data);
        return type.replace("[", "").replace("]", "").split(" ")[1].toLowerCase()
    },
    forEach: function (arr, fn) {
        if (this.getType(arr) != "array") {
            return arr
        }
        for (var i = 0, len = arr.length; i < len; i++) {
            var val = fn.call(arr[i], i, arr[i]);
            if (val === false) {
                break
            }
        }
        return arr
    },
    on: function (ele, type, fn) {
        if (document.addEventListener) {
            ele.addEventListener(type, fn, false)
        } else {
            if (window.attachEvent) {
                if (!ele["_" + type + "_event"]) {
                    var arr = [fn];
                    ele["_" + type + "_event"] = arr
                } else {
                    ele["_" + type + "_event"].push(fn)
                }
                ele.attachEvent("on" + type, function () {
                    var e = window.event;
                    e.preventDefault = function () {
                        e.returnValue = false
                    };
                    e.stopPropagation = function () {
                        e.calcleBubble = true
                    };
                    e.target = e.srcElement;
                    fn.call(ele, e)
                })
            }
        }
        return ele
    },
    extend: function (target, obj) {
        for (var attr in obj) {
            if (obj.hasOwnProperty(attr)) {
                target[attr] = obj[attr]
            }
        }
        return target
    },
    bindChangeEvent: function (input, currentData) {
        this.on(input, "change", function (e) {
            var curInput = e.target;
            currentData.select(curInput.checked, {up: curInput.checked})
        })
    },
    arrowBindClickEvent: function (arrowEle, currentData) {
        this.on(arrowEle, "click", function (e) {
            currentData.spread()
        })
    }
};


function CompositeLeaf(ynTree, options, type, ynTreeId) {
    this.type = type || "";
    ynTree.extend(this, options || {});
    if (this.sub) {
        this.sub = [];
    }
    this.ynTreeId = ynTreeId;
    this.ynTreeObject = ynTree;
}

CompositeLeaf.prototype = {
    constructor: CompositeLeaf,
    selectDown: function (flag, {
        spread = false,
        spreadChecked = true,
        up = true,
        down = true,
    }) {
        flag = !!flag;
        if (this.sub && this.sub.length) {
            this.ynTreeObject.forEach(this.sub, (index, item) => {
                if (!item.disabled) {
                    item.checked = flag;
                    item.dom.querySelector(".yn-tree-input").checked = flag
                    if (flag && spreadChecked) {
                        item.dom.classList.remove('shrink');
                        item.dom.classList.add('spread');
                    } else if (!flag && !spread) {
                        item.dom.classList.remove('spread');
                        item.dom.classList.add('shrink');
                    }
                }
                if (item.sub && item.sub.length) {
                    this.ynTreeObject.forEach(item.sub, function (dex, val) {
                        val.select(flag, {up, down});
                    });
                }
            })
        }
        return this
    },
    selectUp: function (flag, {
        spread = false,
        spreadChecked = true,
    }) {
        var parent = null;
        flag = !!flag;
        if (!this.pid) {
            return this
        }
        this.ynTreeObject.forEach(this.ynTreeObject.parallel, (index, item) => {
            if (item.id === this.pid) {
                parent = item;
                return false
            }
        });
        if (flag) {
            parent.checked = flag;
            document.getElementById(parent.id).checked = flag
            if (spreadChecked) {
                parent.dom.classList.add('spread');
                parent.dom.classList.remove('shrink');
            }
        } else {
            var allChildNotChecked = true;
            this.ynTreeObject.forEach(parent.sub, (index, item) => {
                if (item.checked) {
                    allChildNotChecked = false;
                    return false
                }
            });
            if (allChildNotChecked) {
                parent.checked = flag;
                document.getElementById(parent.id).checked = flag
                if (!spread) {
                    parent.dom.classList.add('shrink');
                    parent.dom.classList.remove('spread');
                }
            }
        }
        if (parent.pid) {
            parent.selectUp(flag, {
                spread,
                spreadChecked,
            })
        }
        return this
    },
    select: function (flag, {down = true, up = true}) {
        var input = document.getElementById(this.id),
            ynTree = this.ynTreeObject,
            spreadChecked = ynTree.configs.spreadChecked,
            spread = ynTree.configs.spread;
        flag = !!flag;
        if (this.disabled) {
            input.nextElementSibling.checked = flag;
        } else {
            this.checked = flag;
            input.checked = flag
        }
        ynTree.options.onchange && ynTree.getType(ynTree.options.onchange) == "function" && ynTree.options.onchange.call(this, input, ynTree);
        if (typeof ynTree.options.checkStrictly == "undefined" || ynTree.options.checkStrictly === true) {
            if (this.type == "composite" && down) {
                this.selectDown(flag, {
                    spreadChecked,
                    spread,
                    up,
                    down,
                })
            }
            if (this.pid && up) {
                this.selectUp(flag, {
                    spreadChecked,
                    spread,
                    up,
                    down,
                })
            }
        }
        // 展开选中选项
        if (this.dom.childNodes[2]) {
            if (flag && spreadChecked) {
                this.dom.classList.add('spread');
                this.dom.classList.remove('shrink');
            } else if (!flag && !spread) {
                this.dom.classList.remove('spread');
                this.dom.classList.add('shrink');
            }
        }

        return this
    },
    disable: function (flag) {
        var input = document.getElementById(this.id),
            ynTree = this.ynTreeObject;
        flag = !!flag;
        this.disabled = flag;
        input.disabled = flag;
        return this
    },
    spread: function (flag) {
        var curLi = document.getElementById(this.id + "_li"),
            ynTree = this.ynTreeObject,
            classNameArr = curLi.className.split(" "),
            hasSpreadClass = false,
            spreadClassIndex = -1,
            hasShrinkClass = false,
            shrinkClassIndex = -1;


        if (this.type == "leaf") {
            return this
        }
        for (var i = 0, len = classNameArr.length; i < len; i++) {
            if (classNameArr[i] === ynTree.classNameCfg.spread) {
                hasSpreadClass = true;
                spreadClassIndex = i
            }
            if (classNameArr[i] === ynTree.classNameCfg.shrink) {
                hasShrinkClass = true;
                shrinkClassIndex = i
            }
        }
        if (typeof flag == "undefined") {
            if (hasSpreadClass) {
                flag = false
            } else {
                if (hasShrinkClass) {
                    flag = true
                }
            }
        }
        flag = !!flag;
        if (flag) {
            if (hasSpreadClass) {
                return this
            }
            if (hasShrinkClass) {
                classNameArr.splice(shrinkClassIndex, 1)
            }
            classNameArr.push(ynTree.classNameCfg.spread);
            curLi.className = classNameArr.join(" ")
        } else {
            if (hasShrinkClass) {
                return this
            }
            if (hasSpreadClass) {
                classNameArr.splice(spreadClassIndex, 1)
            }
            classNameArr.push(ynTree.classNameCfg.shrink);
            curLi.className = classNameArr.join(" ")
        }
        return this
    },
};