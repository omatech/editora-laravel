{{-- Text Area CKEDITOR --}}
@if($p_mode=='V')
    <div class="column">
        <div class="form-group">
            <label for="{{$attribute_name}}" class="form-label">{{$attribute['caption']}} {!! _attributeInfo($attribute['id'], $attribute['name'], $attribute['type']) !!}</label>
            <textarea name="{{$attribute_name}}" class="form-control editor" data-language="{{$attribute['language']}}"> disabled>@isset($attribute['atrib_values'][0]){{$attribute['atrib_values'][0]['text_val']}}@endisset</textarea>
        </div>
    </div>
@elseif($p_mode=='U' || $p_mode=='I')
    <div class="column">
        <div class="form-group">
            <label for="{{$attribute_name}}" class="form-label">{{$attribute['caption']}}</label>
            <textarea name="{{$attribute_name}}" class="form-control editor" data-language="{{$attribute['language']}}">>@isset($attribute['atrib_values'][0]){{$attribute['atrib_values'][0]['text_val']}}@endisset</textarea>
        </div>
    </div>
@endif