@props(['sets', 'title'])

@if($sets && $sets->count() > 0)
    <div class="mt-3">
        <x-client.image-slider :title="$title" id="sliderWrapper1" prevId="prevBtn1" nextId="nextBtn1" :slides="$sets->map(function($set) {
            return [
                'src' => $set->photos && $set->photos->count() > 0 ? '/storage/' . $set->photos->first()->path : '/storage/' . $set->image,
                'title' => $set->name,
                'width' => 300,
                'height' => 300,
                'link' => url('/search?set=' . $set->id)
            ];
        })" />
    </div>
@endif
