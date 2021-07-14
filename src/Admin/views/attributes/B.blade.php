{{-- String d'una linea ordenable a l'extracció --}}
@if($p_mode=='V')
    <div class="column column-text">
        <div class="form-group">
            <label for="{{$attribute_name}}" class="form-label">{{$attribute['caption']}} {!! _attributeInfo($attribute['id'], $attribute['name'], $attribute['type']) !!}</label>
            <input type="text" class="form-control" name="{{$attribute_name}}" value="@isset($attribute['atrib_values'][0]) @isset($attribute['atrib_values'][0]){{$attribute['atrib_values'][0]['text_val']}}@endisset @endisset" disabled="disabled">
        </div>
    </div>
@elseif($p_mode=='U' || $p_mode=='I')
    <div class="column column-text">
        <div class="form-group">
            <label for="{{$attribute_name}}" class="form-label">{{$attribute['caption']}}</label>
            <input type="text" class="form-control" name="{{$attribute_name}}" value="@isset($attribute['atrib_values'][0]) @isset($attribute['atrib_values'][0]){{$attribute['atrib_values'][0]['text_val']}}@endisset @endisset">
        </div>
    </div>
@endif