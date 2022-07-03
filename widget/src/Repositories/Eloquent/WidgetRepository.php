<?php

namespace BlackCMS\Widget\Repositories\Eloquent;

use BlackCMS\Support\Repositories\Eloquent\RepositoriesAbstract;
use BlackCMS\Widget\Repositories\Interfaces\WidgetInterface;

class WidgetRepository extends RepositoriesAbstract implements WidgetInterface
{
    /**
     * {@inheritDoc}
     */
    public function getByTheme($theme)
    {
        $data = $this->model->where("theme", $theme)->get();
        $this->resetModel();

        return $data;
    }
}
