@extends(BaseHelper::getAdminMasterLayoutTemplate())
@section('content')
    <div class="row">
        @foreach(ThemeManager::getThemes() as $key => $theme)
            <div class="col-sm-6 col-md-4">
                <div class="card mb-2 @if (setting('theme') && Theme::getThemeName() == $key) bg-success text-white @endif">
                    <div class="card-body">
                        <div class="app-details">
                            <h4 class="app-name">{{ $theme['extra']['name'] }}</h4>
                        </div>
                        <div class="app-footer">
                            <div class="app-description">
                                {{ $theme['description'] }}
                            </div>
                            <div class="my-2">
                                @foreach ($theme['keywords'] as $keyword)
                                    <span class="px-1
                                        @if (setting('theme') && Theme::getThemeName())
                                            bg-white text-primary
                                        @else
                                            bg-primary text-white
                                        @endif
                                    ">{{ $keyword }}</span>
                                @endforeach
                            </div>
                            <div class="app-author">
                                {{ trans('packages/theme::theme.author') }}:
                                @if( @isset($theme['url']) && $theme['url'] != '')
                                    <a class="text-underline @if (setting('theme') && Theme::getThemeName() == $key) text-white @endif" href="{{ $theme['url'] }}" target="_blank">{{ $theme['author'] }}</a>
                                @else
                                    {{ $theme['authors'][0]['name'] }}
                                @endif
                            </div>
                            <div class="app-version">
                                {{ trans('packages/theme::theme.version') }}: {{ $theme['version'] }}
                            </div>
                            <div class="app-actions mt-3">
                                @if (setting('theme') && Theme::getThemeName() == $key)
                                    <a href="#" class="btn btn-primary" disabled="disabled">
                                        <i class="fa fa-check"></i>
                                        {{ trans('packages/theme::theme.activated') }}
                                    </a>
                                @else
                                    @if (Auth::user()->hasPermission('theme.activate'))
                                        <a href="#" class="btn btn-primary btn-trigger-active-theme" data-theme="{{ $key }}">{{ trans('packages/theme::theme.active') }}</a>
                                    @endif
                                    @if (Auth::user()->hasPermission('theme.remove'))
                                        <a href="#" class="btn btn-danger btn-trigger-remove-theme" data-theme="{{ $key }}">{{ trans('packages/theme::theme.remove') }}</a>
                                    @endif
                                @endif
                                @if ($theme['homepage'])
                                    <a title="View homepage" target="_blank" href="{{ $theme['homepage'] }}" class="btn btn-primary">
                                        <span class="fas fa-external-link-alt"></span> Website
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    {!! Form::modalAction('remove-theme-modal', trans('packages/theme::theme.remove_theme'), 'danger', trans('packages/theme::theme.remove_theme_confirm_message'), 'confirm-remove-theme-button', trans('packages/theme::theme.remove_theme_confirm_yes')) !!}
@stop
