<div class="row">
    <div class="col-12">
        <div class="annotated-section-title">
            <h2>{{ trans('packages/optimize::optimize.settings.title') }}</h2>
        </div>
        <div class="annotated-section-description p-none-t">
            <p class="color-note">{{ trans('packages/optimize::optimize.settings.description') }}</p>
        </div>
    </div>

    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="form-group mb-3">
                    <label class="text-title-field"
                        for="optimize_page_speed_enable">{{ trans('packages/optimize::optimize.settings.enable') }}
                    </label>
                    <label class="me-2">
                        <input type="radio" name="optimize_page_speed_enable"
                            value="1"
                            @if (setting('optimize_page_speed_enable')) checked @endif>{{ trans('core/setting::setting.general.yes') }}
                    </label>
                    <label>
                        <input type="radio" name="optimize_page_speed_enable"
                            value="0"
                            @if (!setting('optimize_page_speed_enable')) checked @endif>{{ trans('core/setting::setting.general.no') }}
                    </label>
                </div>
            </div>
        </div>
    </div>
</div>
