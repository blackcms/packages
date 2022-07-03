<?php

namespace BlackCMS\Theme\Forms;

use BlackCMS\Base\Forms\FormAbstract;
use BlackCMS\Base\Models\BaseModel;
use BlackCMS\Theme\Http\Requests\CustomCssRequest;
use File;
use Theme;

class CustomCSSForm extends FormAbstract
{
    /**
     * {@inheritDoc}
     */
    public function buildForm()
    {
        $css = null;
        $file = Theme::getStyleIntegrationPath();
        if (File::exists($file)) {
            $css = get_file_data($file, false);
        }

        $this->setupModel(new BaseModel())
            ->setUrl(route("theme.custom-css.post"))
            ->setValidatorClass(CustomCssRequest::class)
            ->add("custom_css", "textarea", [
                "label" => trans("packages/theme::theme.custom_css"),
                "label_attr" => ["class" => "control-label"],
                "value" => $css,
                "help_block" => [
                    "text" => trans(
                        "packages/theme::theme.custom_css_placeholder"
                    ),
                ],
            ]);
    }
}
