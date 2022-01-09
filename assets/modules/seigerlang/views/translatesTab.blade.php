<form action="{!!$url!!}&get=translates&action=save" method="post">
    <p>{!! $_lang['slang_example_usage'] !!}</p>
    <div class="table-responsive langTable">
        <table class="table table-condensed table-hover sectionTrans">
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
                        <td data-tid="{{$dictionary['tid']}}" data-lang="{{$langConfig}}">
                            @if($langConfig == $sLang->langDefault())
                                <input type="text" class="form-control" name="sLang[{{$dictionary['tid']}}][{{$langConfig}}]" value="{{$dictionary[$langConfig]}}" />
                            @else
                                <div class="input-group">
                                    <input type="text" class="form-control" name="sLang[{{$dictionary['tid']}}][{{$langConfig}}]" value="{{$dictionary[$langConfig]}}" />
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
    </div>
</form>
{{ $sLang->dictionary()->render() }}

@push('scripts.bot')
    <div id="actions">
        <div class="btn-group">
            <a href="{!!$url!!}&get=translates&action=synchronize" class="btn btn-success" title="{{$_lang["slang_synchronize_help"]}}">
                <i class="fa fa-sync-alt"></i>&emsp;<span>{{$_lang["slang_synchronize"]}}</span>
            </a>
        </div>
    </div>
    <script>
        $(document).on("click", ".js_translate", function () {
            var _this = $(this).parents('td');
            var source = _this.data('tid');
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
            var source = _this.data('tid');
            var target = _this.data('lang');
            var _value = _this.find('input').val();

            jQuery.ajax({
                url: '{!!$url!!}&get=translates&action=update',
                type: 'POST',
                data: 'source=' + source + '&target=' + target + '&value=' + _value,
            });
        });

        $('.langTable tbody td:first-child').each(function () {
            hgs = Math.ceil($(this).outerHeight());
            if (hgs > 70) {
                $(this).parent().attr('style', 'height: '+(hgs/2-10)+'px;');
            }
        });
    </script>
    <style>
        .langTable {margin-left: 16%; width: 84%;}
        .langTable table {width: {{count($sLang->langConfig())*25+35}}%;}
        .langTable td:first-child {vertical-align: middle; position: absolute; width: 16%; margin-left: -16%;}
        .langTable tbody td:first-child {padding-top: 10px;}
    </style>
@endpush