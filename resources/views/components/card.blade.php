<div class="card border-0 shadow-sm {{ $class ?? '' }}">
    @if(isset($header))
        <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
            {{ $header }}
        </div>
    @endif
    
    <div class="card-body {{ $bodyClass ?? 'p-0' }}">
        {{ $slot }}
    </div>
</div>
