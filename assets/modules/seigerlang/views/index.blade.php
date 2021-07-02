@extends('manager::template.page')

@push('scripts.bot')
    <script>
        var trans = {!! json_encode($unlockTranslations, JSON_UNESCAPED_UNICODE) !!},
            mraTrans = {!! json_encode($mraTranslations, JSON_UNESCAPED_UNICODE) !!};
    </script>
    <script src="media/script/jquery.quicksearch.js"></script>
    <script src="media/script/jquery.nucontextmenu.js"></script>
    <script src="media/script/bootstrap/js/bootstrap.min.js"></script>
    <script src="actions/resources/functions.js"></script>
@endpush

@section('content')
    <div class="notifier"><div class="notifier-txt"></div></div>
    <h1><i class="fa fa-globe-americas"></i> <?=$_lang['slang_title'];?></h1>
    <p style="margin-left:15px;"><?=$_lang['slang_description'];?></p>

    <div class="sectionBody">
        <div class="tab-pane" id="resourcesPane">
            <script>tpResources = new WebFXTabPane(document.getElementById('resourcesPane'), false);</script>

            <div class="tab-page translatesTab" id="translatesTab">
                <h2 class="tab"><a href="<?=$url?>&get=translates"><span><i class="fa fa-language"></i> <?=$_lang['slang_dictionary'];?></span></a></h2>
                <script>tpResources.addTabPage(document.getElementById('translatesTab'));</script>
                @if($get == 'translates')
                    @include('translatesTab')
                @endif
            </div>

            <div class="tab-page settingsTab" id="settingsTab">
                <h2 class="tab"><a href="<?=$url?>&get=settings"><span><i class="fa fa-cogs"></i> <?=$_lang['slang_settings'];?></span></a></h2>
                <script>tpResources.addTabPage(document.getElementById('settingsTab'));</script>
                @if($get == 'settings')
                    @include('settingsTab')
                @endif
            </div>

            <script>tpResources.setSelectedTab('{{$get}}Tab');</script>
        </div>
    </div>
@endsection
