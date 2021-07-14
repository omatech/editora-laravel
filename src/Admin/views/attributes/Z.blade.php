{{-- niceurl --}}
@if($p_mode=='V')
    <div class="column column-text">
        <div class="form-group">
            <label for="{{$attribute_name}}" class="form-label">{{$attribute['caption']}} {!! _attributeInfo($attribute['id'], $attribute['name'], $attribute['type']) !!}</label>
            <div class="input-group">
                <input type="text" class="form-control" name="{{$attribute_name}}" value="@isset($attribute['niceurl']){{$attribute['niceurl']}}@endisset" disabled="disabled">
                <span class="btn-square clr-default">
                    <a href="/{{$attribute['language']}}/{{$attribute['niceurl']}}?req_info=1" target="_blank"><i class="icon-eye"></i></a>
                </span>
            </div>
        </div>
    </div>
@elseif($p_mode=='U' || $p_mode=='I')
    <div class="column column-text">
        <div class="form-group">
            <label for="{{$attribute_name}}" class="form-label">{{$attribute['caption']}}</label>
            <input type="text" class="form-control" name="{{$attribute_name}}" value="@isset($attribute['niceurl']){{$attribute['niceurl']}}@endisset">
        </div>
    </div>
@endif