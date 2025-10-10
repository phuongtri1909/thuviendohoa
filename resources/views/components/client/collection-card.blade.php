@props(['title', 'images' => []])

<div class="collection-wrapper">
    <div class="collection-card">
        <div class="collection-inner">
            @foreach($images as $image)
                <img src="{{ $image }}" alt="{{ $title }}">
            @endforeach
        </div>
    </div>
    <div class="label-collection">{{ $title }}</div>
</div>
