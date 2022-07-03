@extends(BaseHelper::getAdminMasterLayoutTemplate())
@section('content')
    {!! Form::open(['route' => ['slug.settings']]) !!}
        <div>
            <div class="row">

                <div class="col-12">
                    <div class="annotated-section-title">
                        <h2>{{ trans('packages/slug::slug.settings.title') }}</h2>
                    </div>
                    <div class="annotated-section-description p-none-t">
                        <p class="color-note">{{ trans('packages/slug::slug.settings.description') }}</p>
                    </div>
                </div>

                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            @foreach(SlugHelper::supportedModels() as $model => $name)
                                <div class="form-group mb-3">
                                    <label class="text-title-field" for="{{ SlugHelper::getPermalinkSettingKey($model) }}">{{ trans('packages/slug::slug.prefix_for', ['name' => $name]) }}</label>
                                    <input type="text" class="next-input form-control {{ $errors->has(SlugHelper::getPermalinkSettingKey($model)) ? 'is-invalid' : ''}}" name="{{ SlugHelper::getPermalinkSettingKey($model) }}" id="{{ SlugHelper::getPermalinkSettingKey($model) }}"
                                        value="{{ ltrim(rtrim(old(SlugHelper::getPermalinkSettingKey($model), setting(SlugHelper::getPermalinkSettingKey($model), SlugHelper::getPrefix($model))), '/'), '/') }}">
                                    <input type="hidden" name="{{ SlugHelper::getPermalinkSettingKey($model) }}-model-key" value="{{ $model }}">
                                    @if ($errors->has(SlugHelper::getPermalinkSettingKey($model)))
                                        <span class="invalid-feedback">
                                            <strong>{{ $errors->first(SlugHelper::getPermalinkSettingKey($model)) }}</strong>
                                        </span>
                                    @endif
                                    <span class="alert-help">
                                        {{ trans('packages/slug::slug.settings.preview') }}: <a href="javascript:void(0)">{{ url((string)setting(SlugHelper::getPermalinkSettingKey($model), SlugHelper::getPrefix($model))) }}/{{ Str::slug('your url here') }}</a>
                                    </span>
                                </div>
                            @endforeach

                            <hr>

                            <div class="form-group mb-3">
                                <label class="text-title-field"
                                    for="slug_turn_off_automatic_url_translation_into_latin">{{ trans('packages/slug::slug.settings.turn_off_automatic_url_translation_into_latin') }}
                                </label>
                                <label class="me-2">
                                    <input type="radio" name="slug_turn_off_automatic_url_translation_into_latin"
                                        value="1"
                                        @if (SlugHelper::turnOffAutomaticUrlTranslationIntoLatin()) checked @endif>{{ trans('core/setting::setting.general.yes') }}
                                </label>
                                <label>
                                    <input type="radio" name="slug_turn_off_automatic_url_translation_into_latin"
                                        value="0"
                                        @if (!SlugHelper::turnOffAutomaticUrlTranslationIntoLatin()) checked @endif>{{ trans('core/setting::setting.general.no') }}
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="row" style="border: none">
                <div class="col-12">
                    &nbsp;
                </div>
                <div class="col-12">
                    <button class="btn btn-primary" type="submit">{{ trans('core/setting::setting.save_settings') }}</button>
                </div>
            </div>
        </div>
    {!! Form::close() !!}
@endsection
