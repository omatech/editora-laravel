{{-- Text Area HTML --}}
@if($p_mode=='V')
    <div class="column column-text">
        <div class="form-group">
            <label for="" class="form-label">{{$attribute['caption']}} {!! _attributeInfo($attribute['id'], $attribute['name'], $attribute['type']) !!}</label>
            <textarea class="form-control" disabled>{{$attribute['atrib_values'][0]['text_val']}}</textarea>
        </div>
    </div>
@endif