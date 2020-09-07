{{-- Video Youtube - Vimeo --}}
@if($p_mode=='V')
    <div class="column column-text">
        <div class="form-group">
            <label for="{{$attribute_name}}" class="form-label">{{$attribute['caption']}} {!! _attributeInfo($attribute['id'], $attribute['name'], $attribute['type']) !!}</label>
            <input type="text" name="{{$attribute_name}}" class="form-control" value="{{$attribute['atrib_values'][0]['text_val']}}" disabled="disabled">
        </div>
    </div>
    @if(!empty($attribute['atrib_values'][0]['text_val']))
    <div class="column column-media">
        <div class="field-video">
            <div class="top">
                <span class="form-label">{{__('editora_lang::messages.video_preview')}}</span>
            </div>
            <figure class="video-preview ratio16by9">
                <iframe src="{{_videoembed($attribute['atrib_values'][0]['text_val'])}}" allowfullscreen></iframe>
            </figure>
        </div>
    </div>
    @endif
@elseif($p_mode=='U' || $p_mode=='I')
    <div class="column column-text">
        <div class="form-group">
            <label for="{{$attribute_name}}" class="form-label">{{$attribute['caption']}}</label>
            <input type="text" class="form-control" name="{{$attribute_name}}" value="{{$attribute['atrib_values'][0]['text_val']}}">
        </div>
    </div>
@endif
