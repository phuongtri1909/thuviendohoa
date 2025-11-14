<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Set;
use App\Models\Blog;

class MeilisearchIndexCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'meilisearch:index {model?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Index all data to Meilisearch';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $model = $this->argument('model');

        if (!$model || $model === 'all') {
            $this->info('Indexing all models...');
            $this->indexSets();
            $this->indexBlogs();
            $this->info('✓ All models indexed successfully!');
        } elseif ($model === 'sets') {
            $this->indexSets();
        } elseif ($model === 'blogs') {
            $this->indexBlogs();
        } else {
            $this->error('Invalid model. Use: sets, blogs, or all');
            return 1;
        }

        return 0;
    }

    private function indexSets()
    {
        $this->info('Indexing Sets...');
        $bar = $this->output->createProgressBar(Set::count());

        Set::chunk(100, function ($sets) use ($bar) {
            foreach ($sets as $set) {
                $set->searchable();
                $bar->advance();
            }
        });

        $bar->finish();
        $this->newLine();
        $this->info('✓ Sets indexed successfully!');
    }

    private function indexBlogs()
    {
        $this->info('Indexing Blogs...');
        $bar = $this->output->createProgressBar(Blog::count());

        Blog::chunk(100, function ($blogs) use ($bar) {
            foreach ($blogs as $blog) {
                $blog->searchable();
                $bar->advance();
            }
        });

        $bar->finish();
        $this->newLine();
        $this->info('✓ Blogs indexed successfully!');
    }
}

