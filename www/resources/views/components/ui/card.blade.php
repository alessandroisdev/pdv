<div class="card {{ $attributes->get('class') ?? '' }}">
    @if(isset($header))
        <div class="card-header">
            <h3>{{ $header }}</h3>
        </div>
    @endif
    
    <div class="card-body">
        {{ $slot }}
    </div>
</div>
