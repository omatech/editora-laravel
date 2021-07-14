{{-- Selector color --}}
@if($p_mode=='V')
    <div class="column column-text">
        <div class="form-group">
            <label for="{{$attribute_name}}" class="form-label">{{$attribute['caption']}} {!! _attributeInfo($attribute['id'], $attribute['name'], $attribute['type']) !!}</label>
            <input type="text" class="form-control" name="{{$attribute_name}}" value="@isset($attribute['atrib_values'][0]){{$attribute['atrib_values'][0]['text_val']}}@endisset" disabled="disabled" style="background-color:@isset($attribute['atrib_values'][0]) {{$attribute['atrib_values'][0]['text_val']}} @endisset">
        </div>
    </div>
@elseif($p_mode=='U' || $p_mode=='I')
    <div class="column column-text">
        <div class="form-group">
            <label for="{{$attribute_name}}" class="form-label">{{$attribute['caption']}}</label>
            <input type="text" class="form-control" id="{{$attribute_name}}" name="{{$attribute_name}}" value="@isset($attribute['atrib_values'][0]){{$attribute['atrib_values'][0]['text_val']}}@endisset">
        </div>
    </div>
@section('scripts')
    @parent
    <script type="text/javascript">
        // var picker = new CP(document.querySelector('input[type="text"]'));
        var picker = new CP(document.getElementById('{{$attribute_name}}'));
        picker.on("change", function(color) {
            this.source.value = '#' + color;
        });
    </script>
@endsection
@endif