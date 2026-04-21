@props(['id', 'title', 'action' => null, 'method' => 'POST', 'hasFile' => false, 'submitText' => 'Simpan'])

<div class="modal fade" id="{{ $id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 20px;">
            @if($action)
            <form action="{{ $action }}" method="{{ $method == 'GET' ? 'GET' : 'POST' }}" {{ $hasFile ? 'enctype=multipart/form-data' : '' }}>
                @csrf
                @if(!in_array($method, ['GET', 'POST']))
                    @method($method)
                @endif
            @endif

                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">{{ $title }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body py-4">
                    {{ $slot }}
                </div>

                <div class="modal-footer border-0 pt-0 d-flex justify-content-between">
                    @if(isset($footerLeft))
                        <div>{{ $footerLeft }}</div>
                    @else
                        <div></div>
                    @endif
                    
                    <div>
                        <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                        @if($action)
                            <button type="submit" class="btn btn-primary rounded-pill px-4">{{ $submitText }}</button>
                        @endif
                    </div>
                </div>

            @if($action)
            </form>
            @endif
        </div>
    </div>
</div>
