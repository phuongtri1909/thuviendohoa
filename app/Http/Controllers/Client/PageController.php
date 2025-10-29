<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\SeoSetting;
use Illuminate\Http\Request;
use Artesaos\SEOTools\Facades\OpenGraph;
use Artesaos\SEOTools\Facades\TwitterCard;
use Artesaos\SEOTools\Facades\SEOTools;
use Artesaos\SEOTools\Facades\SEOMeta;

class PageController extends Controller
{
    public function show($slug)
    {
        $page = Page::where('slug', $slug)
            ->where('status', 1)
            ->firstOrFail();

        // SEO for custom page
        $title = $page->title . ' - ' . config('app.name');
        $description = strip_tags($page->content);
        $description = strlen($description) > 160 ? substr($description, 0, 160) . '...' : $description;
        $keywords = $page->title . ', ' . config('app.name');
        
        SEOTools::setTitle($title);
        SEOTools::setDescription($description);
        SEOMeta::setKeywords($keywords);
        SEOTools::setCanonical(url()->current());

        OpenGraph::setTitle($title);
        OpenGraph::setDescription($description);
        OpenGraph::setUrl(url()->current());
        OpenGraph::addProperty('type', 'article');

        TwitterCard::setTitle($title);
        TwitterCard::setDescription($description);

        return view('client.pages.page', compact('page'));
    }
}
