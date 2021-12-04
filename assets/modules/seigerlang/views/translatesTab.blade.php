<form action="{!!$url!!}&get=translates&action=save" method="post">
    <p>{!! $_lang['slang_example_usage'] !!}</p>
    <table class="table table-condensed table-striped table-bordered table-hover sectionTrans">
        <thead>
        <tr>
            <td style="text-align:center !important;"><b>KEY</b></td>
            @foreach($sLang->langConfig() as $langConfig)
                <td style="text-align:center !important;"><b>{{strtoupper($langConfig)}}</b></td>
            @endforeach
        </tr>
        </thead>
        <tbody>
        @foreach($sLang->dictionary() as $dictionary)
            <tr>
                <td>{{$dictionary['key']}}</td>
                @foreach($sLang->langConfig() as $langConfig)
                    <td data-id="{{$dictionary['id']}}" data-lang="{{$langConfig}}">
                        @if($langConfig == $sLang->langDefault())
                            <input type="text" class="form-control" name="sLang[{{$dictionary['id']}}][{{$langConfig}}]" value="{{$dictionary[$langConfig]}}" />
                        @else
                            <div class="input-group">
                                <input type="text" class="form-control" name="sLang[{{$dictionary['id']}}][{{$langConfig}}]" value="{{$dictionary[$langConfig]}}" />
                                <span class="input-group-btn">
                                    <button style="padding: 0 5px;" class="btn btn-light js_translate" type="button" title="{{$_lang['slang_auto_translate']}} {{strtoupper($sLang->langDefault())}} => {{strtoupper($langConfig)}}" title="{{$_lang['slang_auto_translate']}}">
                                        <i class="fa fa-language" style="font-size: xx-large;"></i>
                                    </button>
                                </span>
                            </div>
                        @endif
                    </td>
                @endforeach
            </tr>
        @endforeach
        </tbody>
    </table>
</form>

@push('scripts.bot')
    <div id="actions">
        <div class="btn-group">
            <a href="{!!$url!!}&get=translates&action=synchronize" class="btn btn-success" title="{{$_lang["slang_synchronize_help"]}}">
                <i class="fa fa-save"></i>&emsp;<span>{{$_lang["slang_synchronize"]}}</span>
            </a>
        </div>
    </div>
    <script>
        $(document).on("click", ".js_translate", function () {
            var _this = $(this).parents('td');
            var source = _this.data('id');
            var target = _this.data('lang');

            $.ajax({
                url: '{!!$url!!}&get=translates&action=translate',
                type: 'POST',
                data: 'source=' + source + '&target=' + target,
                success: function (ajax) {
                    _this.find('input').val(ajax);
                }
            });
        });

        jQuery(".sectionTrans").on("blur", "input", function () {
            var _this = $(this).parents('td');
            var source = _this.data('id');
            var target = _this.data('lang');
            var _value = _this.find('input').val();

            jQuery.ajax({
                url: '{!!$url!!}&get=translates&action=update',
                type: 'POST',
                data: 'source=' + source + '&target=' + target + '&value=' + _value,
            });
        });
    </script>
@endpush