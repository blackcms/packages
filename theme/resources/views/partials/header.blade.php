@if (theme_option('favicon'))
    <link rel="shortcut icon" href="{{ MediaManagement::getImageUrl(theme_option('favicon')) }}">
@endif

{!! Seo::render() !!}

{!! Theme::asset()->styles() !!}
{!! Theme::asset()->container('after_header')->styles() !!}
{!! Theme::asset()->container('header')->scripts() !!}

{!! apply_filters(THEME_FRONT_HEADER, null) !!}
