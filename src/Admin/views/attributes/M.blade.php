{{-- Geoposicionament amb google Map --}}
@if($p_mode=='V')
    @php
      $lat='41.387156';
      $lng='2.167172';
      if (isset($attribute['atrib_values'][0]['text_val']))
      {
        $pos2 = explode("@", $attribute['atrib_values'][0]['text_val']);
        $pos = explode(":",$pos2[0]);
        $lat = $pos[0];
        $lng = $pos[1];
      }
    @endphp
    <div class="column column-text">
        <div class="form-group">
            <label for="{{$attribute_name}}" class="form-label">{{$attribute['caption']}} {!! _attributeInfo($attribute['id'], $attribute['name'], $attribute['type']) !!}</label>
            <input name="{{$attribute_name}}" type="text" class="form-control" value="{{$pos[0]}}, {{$pos[1]}} @if($pos2[1]!="") @ {{$pos2[1]}} @endif" disabled="disabled">
        </div>
    </div>
    <div class="column column-media">
        <div class="field-video">
            <div id="{{$attribute['id']}}" style="height:220px;"></div>
        </div>
    </div>
    @section('scripts')
        @parent
        <script type="text/javascript">
            $(document).ready(function(){
                posicionar({{$lat}}, {{$lng}}, {{$attribute['id']}});
            });
        </script>

    @endsection
@elseif($p_mode=='I' || $p_mode=='U')
    @if($p_mode=='U')
        @php
            $lat='41.387156';
            $lng='2.167172';
            if (isset($attribute['atrib_values'][0]['text_val']))
            {
                $pos2 = explode("@", $attribute['atrib_values'][0]['text_val']);
                $pos = explode(":",$pos2[0]);
                $lat = $pos[0];
                $lng = $pos[1];
            }
        @endphp
    @else
        @php
            $lat = '41.387917';
            $lng = '2.1699187';
        @endphp
    @endif

    <div class="column column-text">
        <div class="form-group">
            <label for="cerca_posicio" class="form-label">{{$attribute['caption']}}</label>
            <input type="text" class="form-control" name="cerca_posicio" id="cerca_posicio_{{$attribute['id']}}"  value="@isset($attribute['atrib_values'][0]){{$attribute['atrib_values'][0]['text_val']}}@endisset">
            <input type="hidden" name="{{$attribute_name}}" id="position_lat_long_{{$attribute['id']}}" value="@isset($attribute['atrib_values'][0]){{$attribute['atrib_values'][0]['text_val']}}@endisset">
            <div class="button-row">
                <span class="btn-square clr-default" onclick="recalc_gmaps({{$attribute['id']}});"><i class="icon-arrow-right"></i></span>
            </div>
        </div>
        <div class="form-group">
            <input type="text" class="form-control" disabled="disabled" id="latitud_{{$attribute['id']}}" value="">
        </div>
        <div class="form-group">
            <input type="text" class="form-control" disabled="disabled" id="longitud_{{$attribute['id']}}" value="">
        </div>
    </div>
    <div class="column column-media">
        <div class="field-video">
            <div id="{{$attribute['id']}}" style="height:220px;"></div>
        </div>
    </div>
    @section('scripts')
        @parent
        <script type="text/javascript">
            $(document).ready(function(){
                posicionar({{$lat}}, {{$lng}}, {{$attribute['id']}});
            });
        </script>
    @endsection
@endif