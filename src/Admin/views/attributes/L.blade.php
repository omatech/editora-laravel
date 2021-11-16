{{-- Lookup --}}
@if($p_mode=='V')
  @php
    $default_id=$attribute['lookup_info']['info']['default_id'];
    $lookup_value=$attribute['params']->lookup->$default_id[0];  
    if (isset($attribute['lookup_info']['selected_values'][0]['label']))
    {
        $lookup_value=$attribute['lookup_info']['selected_values'][0]['label'];
    }  
  @endphp

    <div class="column column-text">
        <div class="form-group">
            <label for="{{$attribute_name}}" class="form-label">{{$attribute['caption']}} {!! _attributeInfo($attribute['id'], $attribute['name'], $attribute['type']) !!}</label>
            <input type="text" class="form-control" name="{{$attribute_name}}" value="{{$lookup_value}}" disabled="disabled">
        </div>
    </div>
@elseif($p_mode=='U' || $p_mode=='I')
    <div class="column column-text">
        <div class="form-group">
            <label for="{{$attribute_name}}" class="form-label">{{$attribute['caption']}}</label>
            <select class="form-control" name="{{$attribute_name}}">
                @foreach($attribute['lookup_info']['lookup_all_values'] as $item)
                    <option value="{{$item['lookup_value_id']}}" {!! _selectedLookup($attribute, $item['lookup_value_id']) !!}>{{$item['label']}}</option>
                @endforeach
            </select>
        </div>
    </div>
@endif
