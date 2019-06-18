{{-- Text Area CKEDITOR --}}
@if($p_mode=='V')
    <div class="column">
        <div class="form-group">
            <label for="{{$attribute_name}}" class="form-label">{{$attribute['caption']}} {!! _attributeInfo($attribute['id'], $attribute['name'], $attribute['type']) !!}</label>
            <textarea name="{{$attribute_name}}" class="form-control" disabled>{{$attribute['atrib_values'][0]['text_val']}}</textarea>
        </div>
    </div>
@elseif($p_mode=='U' || $p_mode=='I')
    <div class="column">
        <div class="form-group">
            <label for="{{$attribute_name}}" class="form-label">{{$attribute['caption']}}</label>
            <textarea name="{{$attribute_name}}" class="form-control">{{$attribute['atrib_values'][0]['text_val']}}</textarea>
        </div>
    </div>
@endif
@section('scripts')
    @parent
    <script type="text/javascript">CKEDITOR.replace( '{{$attribute_name}}');</script>
@endsection
