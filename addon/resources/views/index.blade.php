@extends(BaseHelper::getAdminMasterLayoutTemplate())
@section('content')
    <div id="addon-list" class="row">
        @foreach ($list as $addon)
            <div class="col-sm-6 col-md-4">
                <div class="card mb-2 @if ($addon->status) bg-success text-white @endif app-{{ $addon->path }}">
                    <div class="card-body">
                        <div class="app-details">
                            <h4 class="app-name">{{ $addon->extra['name'] }}</h4>
                        </div>
                        <div class="app-footer">
                            <div class="app-description" title="{{ $addon->description }}">{{ $addon->description }}</div>
                            <div class="my-2">
                                @foreach ($addon->keywords as $keyword)
                                    <span class="px-1
                                        @if ($addon->status)
                                            bg-white text-primary
                                        @else
                                            bg-primary text-white
                                        @endif
                                    ">{{ $keyword }}</span>
                                @endforeach
                            </div>
                            <div class="app-author">
                                {{ trans('packages/addon::addon.author') }}:
                                <span class="text-underline @if ($addon->status) text-white @endif">
                                    {{ $addon->authors[0]['name'] }}
                                </span>
                            </div>
                            <div class="app-version">{{ trans('packages/addon::addon.version') }}: {{ $addon->version }}</div>
                            <div class="app-actions mt-3">
                                @if (Auth::user()->hasPermission('addons.edit'))
                                    <button class="btn @if ($addon->status) btn-danger @else btn-primary @endif btn-trigger-change-status" data-addon="{{ $addon->path }}" data-status="{{ $addon->status }}">@if ($addon->status) {{ trans('packages/addon::addon.deactivate') }} @else {{ trans('packages/addon::addon.activate') }} @endif</button>
                                @endif
                                @if ($addon->homepage)
                                    <a title="View homepage" target="_blank" href="{{ $addon->homepage }}" class="btn btn-primary">
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
    {!! Form::modalAction('remove-addon-modal', trans('packages/addon::addon.remove_addon'), 'danger', trans('packages/addon::addon.remove_addon_confirm_message'), 'confirm-remove-addon-button', trans('packages/addon::addon.remove_addon_confirm_yes')) !!}
@stop
