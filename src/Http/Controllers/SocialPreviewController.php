<?php

namespace Stillat\SocialMediaImageKit\Http\Controllers;

use Statamic\Facades\Cascade;
use Statamic\Facades\Entry;
use Statamic\Http\Controllers\Controller;
use Stillat\StatamicTemplateResolver\StringTemplateManager;

class SocialPreviewController extends Controller
{
    protected StringTemplateManager $templateManager;

    public function __construct()
    {
        $this->templateManager = new StringTemplateManager(config('social_media_image_kit.generation.template_path', resource_path('views/social-media-image-kit')));
    }

    public function index($id)
    {
        $entry = Entry::find($id);

        if ($entry === null) {
            abort(404);
        }

        $collection = $entry->collection()?->handle();
        $blueprint = $entry->blueprint()?->handle();

        if (! $collection || ! $blueprint) {
            abort(404);
        }

        $cascade = Cascade::instance()->hydrate()->toArray();
        $data = array_merge($cascade, $entry->toArray());
        $html = $this->templateManager->render($collection, $blueprint, $data);

        return view('social-media-image-kit::preview', [
            'content' => $html,
        ]);
    }
}
