<div class="row">
    <div class="col-12">
        <div class="annotated-section-title">
            <h2>{{ trans('packages/theme::theme.settings.title') }}</h2>
        </div>
        <div class="annotated-section-description p-none-t">
            <p class="color-note">{{ trans('packages/theme::theme.settings.description') }}</p>
        </div>
    </div>

    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="form-group mb-3">
                    <label class="text-title-field"
                        for="google_site_verification">{{ trans('core/setting::setting.general.google_site_verification') }}</label>
                    <input data-counter="120" type="text" class="next-input" name="google_site_verification"
                        id="google_site_verification" value="{{ setting('google_site_verification') }}">
                </div>
                <div class="form-group mb-3">
                    <label class="text-title-field"
                        for="cache_time_site_map">{{ trans('core/setting::setting.general.cache_time_site_map') }}</label>
                    <input type="number" class="next-input" name="cache_time_site_map" id="cache_time_site_map"
                        value="{{ setting('cache_time_site_map', 3600) }}">
                </div>
                <div class="form-group mb-3">
                    <div class="mt5">
                        <input type="hidden" name="show_admin_bar" value="0">
                        <label>
                            <input type="checkbox" value="1" @if (setting('show_admin_bar', 1)) checked @endif name="show_admin_bar"> {{ trans('packages/theme::theme.show_admin_bar') }} </label>
                    </div>
                </div>

                <div class="form-group">
                    <label class="text-title-field"
                        for="redirect_404_to_homepage">{{ trans('packages/theme::theme.settings.redirect_404_to_homepage') }}
                    </label>
                    <label class="me-2">
                        <input type="radio" name="redirect_404_to_homepage"
                            value="1"
                            @if (setting('redirect_404_to_homepage', 0)) checked @endif>{{ trans('core/setting::setting.general.yes') }}
                    </label>
                    <label>
                        <input type="radio" name="redirect_404_to_homepage"
                            value="0"
                            @if (!setting('redirect_404_to_homepage', 0)) checked @endif>{{ trans('core/setting::setting.general.no') }}
                    </label>
                </div>
            </div>
        </div>
    </div>
</div>
