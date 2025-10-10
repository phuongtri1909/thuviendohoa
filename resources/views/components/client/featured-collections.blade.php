@props(['title', 'collections' => []])

<div>
    <h3 class="mb-0 font-xl text-center py-2">{{ $title }}</h3>
    <div class="row g-3">
        @foreach($collections as $collection)
            <div class="col-lg-3 col-md-6 col-sm-12">
                <x-client.collection-card 
                    :title="$collection['title']" 
                    :images="$collection['images']" 
                />
            </div>
        @endforeach
    </div>
</div>
