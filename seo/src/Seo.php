<?php

namespace BlackCMS\Seo;

use Arr;
use BlackCMS\Base\Models\BaseModel;
use BlackCMS\Seo\Contracts\SeoContract;
use BlackCMS\Seo\Contracts\SeoMetaContract;
use BlackCMS\Seo\Contracts\SeoOpenGraphContract;
use BlackCMS\Seo\Contracts\SeoTwitterContract;
use Exception;
use Illuminate\Http\Request;
use MetaBox;

class Seo implements SeoContract
{
    /**
     * The SeoMeta instance.
     *
     * @var SeoMetaContract
     */
    protected $seoMeta;

    /**
     * The SeoOpenGraph instance.
     *
     * @var SeoOpenGraphContract
     */
    protected $seoOpenGraph;

    /**
     * The SeoTwitter instance.
     *
     * @var SeoTwitterContract
     */
    protected $seoTwitter;

    /**
     * Make Seo instance.
     *
     * @param SeoMetaContract $seoMeta
     * @param SeoOpenGraphContract $seoOpenGraph
     * @param SeoTwitterContract $seoTwitter
     */
    public function __construct(
        SeoMetaContract $seoMeta,
        SeoOpenGraphContract $seoOpenGraph,
        SeoTwitterContract $seoTwitter
    ) {
        $this->setSeoMeta($seoMeta);
        $this->setSeoOpenGraph($seoOpenGraph);
        $this->setSeoTwitter($seoTwitter);
        $this->openGraph()->addProperty("type", "website");
    }

    /**
     * Set SeoMeta instance.
     *
     * @param SeoMetaContract $seoMeta
     *
     * @return Seo
     */
    public function setSeoMeta(SeoMetaContract $seoMeta)
    {
        $this->seoMeta = $seoMeta;

        return $this;
    }

    /**
     * Get SeoOpenGraph instance.
     *
     * @param SeoOpenGraphContract $seoOpenGraph
     *
     * @return Seo
     */
    public function setSeoOpenGraph(SeoOpenGraphContract $seoOpenGraph)
    {
        $this->seoOpenGraph = $seoOpenGraph;

        return $this;
    }

    /**
     * Set SeoTwitter instance.
     *
     * @param SeoTwitterContract $seoTwitter
     *
     * @return Seo
     */
    public function setSeoTwitter(SeoTwitterContract $seoTwitter)
    {
        $this->seoTwitter = $seoTwitter;

        return $this;
    }

    /**
     * Get SeoOpenGraph instance.
     *
     * @return SeoOpenGraphContract
     */
    public function openGraph()
    {
        return $this->seoOpenGraph;
    }

    /**
     * Set title.
     *
     * @param string $title
     * @param string|null $siteName
     * @param string|null $separator
     *
     * @return Seo
     */
    public function setTitle($title, $siteName = null, $separator = null)
    {
        $this->meta()->setTitle($title, $siteName, $separator);
        $this->openGraph()->setTitle($title);
        if ($siteName) {
            $this->openGraph()->setSiteName($siteName);
        }
        $this->twitter()->setTitle($title);

        return $this;
    }

    /**
     * Get SeoMeta instance.
     *
     * @return SeoMetaContract
     */
    public function meta()
    {
        return $this->seoMeta;
    }

    /**
     * Get SeoTwitter instance.
     *
     * @return SeoTwitterContract
     */
    public function twitter()
    {
        return $this->seoTwitter;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->meta()->getTitle();
    }

    /**
     * Set description.
     *
     * @param string $description
     *
     * @return SeoContract
     */
    public function setDescription($description)
    {
        $this->meta()->setDescription($description);
        $this->openGraph()->setDescription($description);
        $this->twitter()->setDescription($description);

        return $this;
    }

    /**
     * Render the tag.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->render();
    }

    /**
     * Render all seo tags.
     *
     * @return string
     */
    public function render()
    {
        return implode(
            PHP_EOL,
            array_filter([
                $this->meta()->render(),
                $this->openGraph()->render(),
                $this->twitter()->render(),
            ])
        );
    }

    /**
     * @param string $screen
     * @param Request $request
     * @param BaseModel $object
     * @return bool
     */
    public function saveMetaData($screen, $request, $object)
    {
        if (in_array(
            get_class($object),
            config("packages.seo.general.supported", [])
        ) &&
            $request->has("seo_meta")
        ) {
            try {
                if (empty($request->input("seo_meta"))) {
                    MetaBox::deleteMetaData($object, "seo_meta");
                    return false;
                }

                $seoMeta = $request->input("seo_meta", []);

                if (!Arr::get($seoMeta, "seo_title")) {
                    Arr::forget($seoMeta, "seo_title");
                }

                if (!Arr::get($seoMeta, "seo_description")) {
                    Arr::forget($seoMeta, "seo_description");
                }

                if (!empty($seoMeta)) {
                    MetaBox::saveMetaBoxData($object, "seo_meta", $seoMeta);
                } else {
                    MetaBox::deleteMetaData($object, "seo_meta");
                }

                return true;
            } catch (Exception $exception) {
                return false;
            }
        }

        return false;
    }

    /**
     * @param string $screen
     * @param BaseModel $object
     * @return bool
     */
    public function deleteMetaData($screen, $object)
    {
        try {
            if (in_array(
                get_class($object),
                config("packages.seo.general.supported", [])
            )
            ) {
                MetaBox::deleteMetaData($object, "seo_meta");
            }

            return true;
        } catch (Exception $ex) {
            return false;
        }
    }

    /**
     * @param string | array $model
     * @return $this
     */
    public function registerModule($model)
    {
        if (!is_array($model)) {
            $model = [$model];
        }

        config([
            "packages.seo.general.supported" => array_merge(
                config("packages.seo.general.supported", []),
                $model
            ),
        ]);

        return $this;
    }
}
