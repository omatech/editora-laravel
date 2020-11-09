{{-- File --}}
@if($p_mode=='V')
    @php($file = $attribute['atrib_values'][0]['text_val'])
    <div class="column column-text">
        <div class="form-group">
            <label for="{{$attribute_name}}" class="form-label"><i class="fa fa-lock"></i><span class="hide-txt">private</span> {{$attribute['caption']}}
                <a class="clr-default" data-toggle="popover" data-placement="top" data-trigger="hover" data-content="<p>Extension: {{_fileExtension($file)}} - Size: {{_getFileSize($file)}}</p>">
                    <i class="icon-information-outline"></i><span class="hide-txt">Info</span>
                </a>
                {!! _attributeInfo($attribute['id'], $attribute['name'], $attribute['type']) !!}
            </label>
            @if(_fileExtension($file)=='PDF' || _fileExtension($file)=='PNG' || _fileExtension($file)=='JPG')
                <div class="input-group">
                    <input type="text" class="form-control" name="{{$attribute_name}}" value="{{$file}}" disabled="disabled">
                    <span class="btn-square clr-default">
                        <a href="{{$file}}" target="_blank"><i class="icon-eye"></i></a>
                    </span>
                </div>
            @else
                <input type="text" class="form-control" name="{{$attribute_name}}" value="{{$file}}" disabled="disabled">
            @endif
        </div>
    </div>
@elseif($p_mode=='U' || $p_mode=='I')
    @php($file = $attribute['atrib_values'][0]['text_val'])
    <div class="column column-text">
        <div class="form-group">
            <label for="{{$attribute_name}}" class="form-label"><i class="fa fa-lock"></i><span class="hide-txt">private</span> {{$attribute['caption']}}
                <i class="icon-private"></i><span class="hide-txt">private</span>
                @if(isset($file) && !empty($file))
                <a class="clr-default" data-toggle="popover" data-placement="top" data-trigger="hover" data-content="<p>Extension: {{_fileExtension($file)}} - Size: {{_getFileSize($file)}}</p>">
                    <i class="icon-information-outline"></i><span class="hide-txt">Info</span>
                </a>
                @endif
            </label>
            <input id="input_{{$attribute_name}}" type="text" class="form-control" name="{{$attribute_name}}" value="{{$attribute['atrib_values'][0]['text_val']}}">
        </div>
        <div class="form-group">
            <div id="file_{{$attribute_name}}" class="dropzone fallback"></div>
        </div>
    </div>
    
    @section('scripts')
        @parent
    <script>
        Dropzone.autoDiscover = false;

        $(document).ready(function() {
            const dropzone_{{$attribute_name}} = new Dropzone("div#file_{{$attribute_name}}", {
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                autoProcessQueue: true,
                url: "{{ ADMIN_URL }}/upload_private_crop",
                maxFiles: 1,
                dictDefaultMessage: "Añadir fichero", //"{{__('strings.attributes.F.dropzone_add_file')}}",
                dictRemoveFile: "Eliminar fichero", //"{{__('strings.attributes.F.dropzone_delete_file')}}",
                addRemoveLinks : false,
                createImageThumbnails: false,
                @isset($attribute['params']->maxFilesize) maxFilesize:  {{$attribute['params']->maxFilesize}}, @endif
                acceptedFiles: @isset($attribute['params']->acceptedFiles) "{{$attribute['params']->acceptedFiles}}" @else null @endif,
                init: function () {
                    this.on("success", function(file, response) {
                        $('#input_{{$attribute_name}}').val(response.accessUrl);
                    })
                },
                maxfilesexceeded: function(file) {
                    this.removeAllFiles();
                    this.addFile(file);
                }
            });
        });

    </script>
    @endsection
@endif