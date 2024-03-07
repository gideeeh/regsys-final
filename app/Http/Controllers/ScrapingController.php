<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\HttpClient\HttpClient;

class ScrapingController extends Controller
{
    public function scrape()
    {
        $browser = new HttpBrowser(HttpClient::create());
        // Target the specific page where the news stories are listed
        $crawler = $browser->request('GET', 'https://informatics.edu.ph/category/news/');

        // Use the CSS selector to find the story titles
        $titles = $crawler->filter('article .card-body .entry-header h2')->each(function ($node) {
            // Return the text of the node, which is the title of the story
            return $node->filter('a')->text();
        });

        // Return the titles as a JSON response
        // return response()->json($titles);
        return $titles;
    }

    public function scrapeDailyQuotes()
    {
        $browser = new HttpBrowser(HttpClient::create());
        $crawler = $browser->request('GET', 'https://altl.io/');
        $quote= $crawler->filter('main h1.cover-heading')->text();

        $author = substr($crawler->filter('main p.lead')->text(),2);
        return response()->json([
            'quote' => $quote,
            'author' => $author,
        ]);

    }
}
