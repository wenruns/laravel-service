const mergeColspan = function ({
                                   tableSelector,
                                   mergeInput = 0,
                                   columns = [],
                                   fn = false,
                               }) {
    this.tableBody = document.querySelector(tableSelector + " tbody");
    this.fixedTableBody = this.tableBody.parentElement.parentElement.parentElement.querySelector('.table-fixed-right tbody');
    this.prevTDs = {};
    this.merge = {};
    this.prevTrIndex = {};
    this.prevForms = {};
    this.inputElements = {};
    this.mergeInput = mergeInput;
    this.columns = columns;
    this.fn = fn;

    this.Trim = function (str) {
        return str.replace(/(^\s*)|(\s*$)/g, "");
    }
    this.isComplexEle = (ele) => {
        if (ele.inputmask) {
            return true;
        }
        if (ele.tagName == 'SELECT') {
            return true;
        }
        if (Array.from(ele.classList).indexOf('initialized') >= 0) {
            return true;
        }
        return false;
    }
    this.checkInput = (ele, j, i) => {
        if (ele.children.length) {
            Array.from(ele.children).forEach((ele, k) => {
                let name = ele.getAttribute("name");
                if (name && ['_token', '_method', '_previous_'].indexOf(name) < 0) {
                    if (!this.inputElements[i]) {
                        this.inputElements[i] = {};
                    }
                    this.inputElements[i][name] = ele;
                    if (this.merge[i]) {
                        if (this.mergeInput) {
                            // 方式1合并，只是组合各项value，用英文逗号隔开
                            if (this.mergeInput == 1) {
                                let prevEle = this.inputElements[this.prevTrIndex[j]][name];
                                let prevValue = prevEle.value.split(',');
                                prevValue.push(ele.value);
                                prevEle.value = prevValue.join(',');
                            }
                            // 方式2合并，去重复并且过滤空值合并各项的值
                            else if (this.mergeInput == 2 && ele.value) {
                                let prevEle = this.inputElements[this.prevTrIndex[j]][name];
                                let prevValue = prevEle.value.split(',').filter(function (item) {
                                    return item;
                                });
                                if (prevValue.indexOf(ele.value) < 0) {
                                    prevValue.push(ele.value);
                                    prevEle.value = prevValue.join(',');
                                }
                            }
                        } else { // 不启用合并方式，只追加输入框的形式
                            ele.setAttribute('name', name + '[]');
                            let preEle = this.inputElements[this.prevTrIndex[j]][name];
                            preEle.setAttribute('name', name + '[]');
                            if (this.isComplexEle(ele)) {
                                if (ele.tagName == 'SELECT') {
                                    let parentEle = preEle.parentElement;
                                    if (parentEle.classList.value.indexOf('merge-input') < 0) {
                                        let parentDom = document.createElement('div');
                                        parentDom.classList.value = parentEle.classList.value;
                                        parentEle.classList.add('merge-input')
                                        parentEle.parentElement.append(parentDom);
                                        parentDom.append(parentEle);
                                        parentEle.classList.value = '';
                                    }
                                    ele.parentElement.classList.value = '';
                                    $(ele.parentElement).insertAfter(preEle.parentElement);
                                } else {
                                    $(ele.parentElement).insertAfter(preEle.parentElement);
                                }
                            } else {
                                $(ele).insertAfter(preEle);
                            }
                        }
                    }
                } else if (ele.children.length) {
                    if (ele.tagName == 'FORM' && !this.prevForms[this.prevTrIndex[j]]) {
                        this.prevForms[this.prevTrIndex[j]] = ele;
                    }
                    this.checkInput(ele, j, i);
                }
            });
        }
    };

    Array.from(this.tableBody.children).forEach((ele, i) => {
        if (this.fn) {
            this.merge[i] = this.fn.call(this, ele, i);
        }
        Array.from(ele.children).forEach((td, j) => {
            if (!td.style['border-right']) {
                td.style['border-right'] = '1px solid #f4f4f4';
            }
            if (this.columns[0] != '*') {
                let classColumn = td.classList.value.replace('column-', '');
                if (this.columns.indexOf(classColumn) < 0) {
                    return false;
                }
            }
            let prevTd = this.prevTDs[j];
            if (!this.fn) {
                this.merge[i] = prevTd && this.Trim(prevTd.innerHTML) == this.Trim(td.innerHTML);
            }
            if (this.merge[i]) {
                let rowspan = Number(prevTd.getAttribute("rowspan"));
                prevTd.setAttribute('rowspan', rowspan ? (rowspan + 1) : 2);
                prevTd.style['vertical-align'] = 'middle';
                td.remove();
            } else {
                this.prevTDs[j] = td;
                this.prevTrIndex[j] = i;
            }
            this.checkInput(td, j, i);
        });
        if (this.fixedTableBody) {
            if (!this.resizeObserver) {
                this.resizeObserver = {};
            }
            if (typeof ResizeObserver == 'undefined') {
                console.warn('由于无法监控表格高度变化，请不要使用grid->fixedColumn()方法，否则可能会出现页面位置错乱！！！');
            } else {
                this.resizeObserver[i] = new ResizeObserver(entries => {
                    let h = entries[0].contentRect.height;
                    this.fixedTableBody.children[i].style.height = h + 'px';
                });
                this.resizeObserver[i].observe(ele);
            }
        }
    });
}

