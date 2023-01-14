{{-- Call this to get a custom modal --}}

<div class="modal fade" id="{{ $modal_id }}" aria-labelledby="{{ $modal_id }}Label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            {{-- Use this if modal is very custom --}}
            @if (isset($rawModal))
                {!! $rawModal !!}
            @endif
            {{-- Set hasHeader to true if you want the modal to have a header --}}
            @if (isset($hasHeader))
                <div class="modal-header">
                    @if (!isset($rawHeader))
                        <h5 class="modal-title">{{ $modalTitle }}</h5>
                    @else
                        {!! $rawHeader !!}
                    @endif
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            @endif
            {{-- Set hasBody to true to use the body --}}
            @if (isset($hasBody))
                <div class="modal-body">
                    {{-- Show a message at the end of the modal [message => 'message', classes => 'list of classes'] --}}
                    @if (isset($messages))
                        @foreach($messages as $message)
                            <label class="{{ $message['classes'] }}">{{ $message['message'] }}</label><br />
                        @endforeach
                    @endif
                    {{-- Set hasForm to true to have a form --}}
                    @if (isset($inputs))
                        {{-- Send array with following values input => [label, type, id, placeholder (optional), 'optional' => true (optional)] --}}
                        @foreach ($inputs as $input)
                            <label>{{ $input['label'] }}</label>
                            @if (isset($input['optional']))
                                <br /><span class="text-muted" style="font-size: 15px">(optional)</span>
                            @endif
                            <input type="{{ $input['type'] }}"
                                @if (isset($input['placeholder'])) placeholder="{{ $input['placeholder'] }}" @endif
                                class="form-control mb-3" id="{{ $input['id'] }}" autocomplete="off">
                        @endforeach
                    @endif
                    {{-- for custom body --}}
                    @if (isset($rawBody))
                        {!! $rawBody !!}
                    @endif
                </div>
            @endif
            {{-- Footer --}}
            @if (isset($hasFooter))
                <div class="modal-footer">
                    @if (isset($rawFooter))
                        {!! $rawFooter !!}
                    @endif
                    @if (isset($footerMessage))
                        <span class="text-muted">{{ $footerMessage }}</span>
                    @endif
                    {{-- Buttons on footer send array has [b1 => [label => 'button label', id => 'idOfBtn', 'class' => 'btn btn-primary', 'dismiss' => true (optional)]] --}}
                    @if (isset($buttons))
                        @foreach ($buttons as $button)
                            <button id="{{ $button['id'] }}" class="{{ $button['class'] }}"
                                @if (isset($button['dismiss'])) data-bs-dismiss="modal" @endif>{{ $button['label'] }}</button>
                        @endforeach
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
