<style>
    #{{$tableKey}} .asterisk:before{
        content: '*';
        color: red;
        position: absolute;
        top: 30%;
        left: 2px;
        z-index: 100;
    }
    #{{$tableKey}} tr.active{
        border-left: 2px solid red;
    }
</style>
<div class="{{$viewClass['form-group']}} {!! !$errors->has($errorKey) ? '' : 'has-error' !!} select2-box-wen">
    <label for="{{$id}}" class="{{$viewClass['label']}} control-label">{{$label}}</label>
    <div class="{{$viewClass['field']}} box-body" style="padding: 0px 10px 5px; overflow-x: auto;">
        @include('admin::form.error')
        <!---multi_list_table_start--->
        {!! $__content__ !!}
        <!---multi_list_table_end--->
        @include('admin::form.help-block')
    </div>
</div>
<script>
    $(function () {
        $(document).on('click', '.grid-expand-grid-row', function (e) {
            if (e.currentTarget.attributes['aria-expanded'].value == 'true') {
                $(this).closest("td").siblings('td').find('.fa-angle-double-up').trigger('click')
                $(this).find('a i').addClass('fa-angle-double-up');
                $(this).find('a i').removeClass('fa-angle-double-down');
            } else {
                $(this).find('a i').addClass('fa-angle-double-down');
                $(this).find('a i').removeClass('fa-angle-double-up');
            }
        });
    });
</script>
