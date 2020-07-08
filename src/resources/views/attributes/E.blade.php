{{-- Date ordenable a l'extracció --}}
@if($p_mode=='V')
    <div class="column column-text">
        <div class="form-group">
            <label for="{{$attribute_name}}" class="form-label">{{$attribute['caption']}} {!! _attributeInfo($attribute['id'], $attribute['name'], $attribute['type']) !!}</label>
            <input type="text" name="{{$attribute_name}}" class="form-control" value="{{$attribute['atrib_values'][0]['date_val']}}" disabled="disabled">
        </div>
    </div>
@elseif($p_mode=='U' || $p_mode=='I')
    <div class="column column-text">
        <div class="form-group">
            <label for="{{$attribute_name}}" class="form-label">{{$attribute['caption']}}</label>
            <input type="text" length="35" name="{{$attribute_name}}" value="{{$attribute['atrib_values'][0]['date_val']}}" id="{{$attribute_name}}" class="form-control datepicker" autocomplete="off">
        </div>
    </div>
@endif