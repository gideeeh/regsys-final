<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ScrapeWebsiteContent extends DuskTestCase
{
    public function testScrapingPostBoxContent()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('https://ched.gov.ph/') // Change to the actual page you need to scrape
                    ->waitFor('.post-box') // Wait for the post-box elements to load
                    ->with('.post-box', function ($box) {
                        // Assuming you want to scrape the title from each post-box
                        $titles = $box->all('.entry-title > a')->each(function ($link) {
                            return $link->getAttribute('title');
                        });

                        // Do something with the scraped titles
                        // For example, printing them out
                        foreach ($titles as $title) {
                            dump($title);
                        }
                    });
        });
    }
}
