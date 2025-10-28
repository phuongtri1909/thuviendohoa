@props(['title', 'albums' => []])

@push('styles')
    @vite('resources/assets/frontend/css/components/featured-collections.css')
@endpush

<div>
    <h3 class="featured-collections-title">{{ $title }}</h3>
    <div class="row g-3">
        @foreach($albums as $album)
            <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                <x-client.collection-card 
                    :title="$album->name" 
                    :image="$album->image"
                    :album-slug="$album->slug"
                />
            </div>
        @endforeach
    </div>
</div>
