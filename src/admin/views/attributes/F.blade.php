{{-- File --}}
@if($p_mode=='V')
    @php($file = $attribute['atrib_values'][0]['text_val'])
    <div class="column column-text">
        <div class="form-group">
            <label for="{{$attribute_name}}" class="form-label">{{$attribute['caption']}} {!! _attributeInfo($attribute['id'], $attribute['name'], $attribute['type']) !!}</label>
            <input type="text" class="form-control" name="{{$attribute_name}}" value="{{$file}}" disabled="disabled">
        </div>
    </div>
    <div class="column column-media">
        <div class="field-image">
            <div class="top">
                <span class="form-label">{{$attribute['caption']}}</span>
            </div>
            <div class="bottom">
                <div class="image-properties">
                    <span class="properties">
                        <span class="extension">{{_fileExtension($file)}}</span>
                        <span class="size">{{_getFileSize($file)}}</span>
                    </span>
                </div>
            </div>
        </div>
    </div>

@elseif($p_mode=='U' || $p_mode=='I')
    @php($file = $attribute['atrib_values'][0]['text_val'])
    <div class="column column-text">
        <div class="form-group">
            <label for="{{$attribute_name}}" class="form-label">{{$attribute['caption']}}</label>
            <input id="input_{{$attribute_name}}" type="text" class="form-control" name="{{$attribute_name}}" value="{{$attribute['atrib_values'][0]['text_val']}}">
        </div>
        <div class="form-group">
            <div id="file_{{$attribute_name}}" class="dropzone fallback"></div>
        </div>
    </div>
    <div class="column column-media">
        <div class="field-image">
            <div class="top">
                <span class="form-label">{{$attribute['caption']}}</span>
            </div>
            <div class="bottom">
                <div class="image-properties">
                    <span class="properties">
                    @if(isset($file) && !empty($file))
                        <span class="extension">{{_fileExtension($file)}}</span>
                        <span class="size">{{_getFileSize($file)}}</span>
                    @endif
                    </span>
                </div>
            </div>
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
                url: "{{ ADMIN_URL }}/upload_crop",
                maxFiles: 1,
                dictDefaultMessage: "{{__('strings.attributes.F.dropzone_add_file')}}",
                dictRemoveFile: "{{__('strings.attributes.F.dropzone_delete_file')}}",
                addRemoveLinks : false,
                createImageThumbnails: false,

                init: function () {
                    this.on("success", function(file, response) {
                        $('#input_{{$attribute_name}}').val(response.name);
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