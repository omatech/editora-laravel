{{-- Grid Imatge --}}
@if($p_mode=='V')
    @php($file = $attribute['atrib_values'][0]['text_val'])
    <div class="column column-media">
        <span class="form-label">{{$attribute['caption']}} {!! _attributeInfo($attribute['id'], $attribute['name'], $attribute['type']) !!}</span>
        <div class="media-group">
            <figure class="media-preview">
                @if(isset($file) && !empty($file))
                    <img src="{{$file}}" alt="{{$file}}">
                @else
                    <img src="{{ asset('/vendor/editora/img/img_no_available.png') }}" alt="no image">
                @endif
            </figure>
            <div class="media-info">
                <dl class="media-dada">
                    <dt class="media-param">{{getMessage('theoric_size')}}:</dt>
                    <dd class="media-value">{{$attribute['img_w']}}x{{$attribute['img_h']}}&nbsp;</dd>
                    <dt class="media-param">{{getMessage('real_size')}}:</dt>
                    <dd class="media-value">{{Str::replaceFirst('.','x',$attribute['atrib_values'][0]['img_info'])}}&nbsp;</dd>
                    <dt class="media-param">{{getMessage('preview_format')}}:</dt>
                    <dd class="media-value">{{_fileExtension($file)}}&nbsp;</dd>
                    <dt class="media-param">{{getMessage('size')}}:</dt>
                    <dd class="media-value">{{ _getFileSize($file) }}&nbsp;</dd>
                    <dt class="media-param">{{getMessage('path')}}:</dt>
                    <dd class="media-value">{{$attribute['atrib_values'][0]['text_val']}}&nbsp;</dd>
                    
                </dl>
                <ul class="controls-list">
                    <li><a class="btn-square clr-default" href="{{route('editora.action', 'view_grid_image?p_inst_id='.$instance['id'].'&p_pagina='.$attribute['id'])}}" target="_blank"><i class="icon-eye"></i></a></li>
                </ul>
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
            <div id="file_{{$attribute_name}}" class="dropzone fallback" ></div>
        </div>
    </div>

    <div class="column column-media">
        <div class="field-image">
            <div class="top">
                <span class="form-label">{{$attribute['caption']}}</span>
            </div>
            @if(isset($file) && !empty($file))
                <figure class="image-preview">
                    <img id="img_{{$attribute_name}}" src="{{$file}}" alt="{{$file}}">
                </figure>
            @endif

            <span class="properties">
                <span class="dimensions">{{getMessage('theoric_size')}}: {{$attribute['img_w']}}x{{$attribute['img_h']}}</span>
            </span>
        </div>
    </div>

    <div id="cropModal_{{$attribute_name}}" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="cropModal" aria-hidden="true">
        <div class="modal-dialog" style="max-width: 750px">
            <div class="modal-content">
                <div class="modal-body">
                </div>
                <div class="modal-footer">
                    <a href="" class="btn clr-secondary" id="btnCrop_{{$attribute_name}}"><span class="btn-text">{{getMessage('save')}}</span></a>
                </div>
            </div>
        </div>
    </div>

@section('scripts')
    @parent
    <script>
        const attribW_{{$attribute_name}} = "{{$attribute['img_w']}}";
        const attribH_{{$attribute_name}} = "{{$attribute['img_h']}}";
        const $cropModal_{{$attribute_name}} = $('#cropModal_{{$attribute_name}}');
        let modalStatus_{{$attribute_name}} = false;

        $(document).ready(function() {
            /**
             * @type {boolean} Dropzone avoid to autoload.
             */
            Dropzone.autoDiscover = false;

            /**
             * Storage variable for cropper instance.
             */
            var $cropper_{{$attribute_name}} = null;

            /**
             * @type {Dropzone} Dropzone instance.
             */
            const dropzone_{{$attribute_name}} = new Dropzone("div#file_{{$attribute_name}}", {
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                autoProcessQueue: false,
                url: "{{ ADMIN_URL }}/upload_crop",
                maxFiles: 1,
                dictDefaultMessage: "Añade aquí el fichero",
                dictRemoveFile: "Eliminar fichero",
                addRemoveLinks: false,
                createImageThumbnails: true,
                @isset($attribute['params']->maxFilesize) maxFilesize:  {{$attribute['params']->maxFilesize}}, @endif
                acceptedFiles: @isset($attribute['params']->acceptedFiles) "{{$attribute['params']->acceptedFiles}}" @else "image/*" @endif,
                init: function () {
                    this.on('addedfile', addedFile);
                    this.on('success', successCall);
                }
            });

            /**
             * @type {Image} Unique instance for cropped image.
             */
            let loadedImage = new Image();

            /**
             * Addedfile callback function, get image data to process it.
             * @param file
             */
            function addedFile(file) {
                if(dropzone_{{$attribute_name}}.getAcceptedFiles().length >= 1) {
                    let files = dropzone_{{$attribute_name}}.getAcceptedFiles();
                    dropzone_{{$attribute_name}}.removeFile(files[0]);
                }

                let _URL = window.URL || window.webkitURL;

                loadedImage.src = _URL.createObjectURL(file);
                loadedImage.dataset.name = file.name;
                loadedImage.dataset.type = file.type;
                loadedImage.onload = function () {
                    if (attribH_{{$attribute_name}} === '' && attribW_{{$attribute_name}} === '') {
                        dropzone_{{$attribute_name}}.processQueue();
                    }
                    else if (attribH_{{$attribute_name}} !== '' && attribW_{{$attribute_name}} !== '') {
                        if (this.width !== attribW_{{$attribute_name}} || this.height !== attribH_{{$attribute_name}}) {
                            cropImg();
                        }
                    }
                    else if ((attribH_{{$attribute_name}} != '' || attribW_{{$attribute_name}} != '') &&
                        ((attribH_{{$attribute_name}} != '' && attribH_{{$attribute_name}} != this.height) ||
                            (attribW_{{$attribute_name}} != '' && attribW_{{$attribute_name}} != this.width))) {
                        autoResizeImg(loadedImage);
                    }
                }
            }

            /**
             * Launch crop modal to process the image. Modal events.
             * @param img
             */
            function cropImg(img) {
                $cropModal_{{$attribute_name}}.modal();
            }

            /**
             * Modal events
             */
            $cropModal_{{$attribute_name}}.on('shown.bs.modal', instanceCrop);
            $cropModal_{{$attribute_name}}.on('hidden.bs.modal', destroyCrop);

            /**
             * Create an instance of croppie with the dropzone image.
             */
            function instanceCrop() {
                let image = document.createElement('img');
                image.src = loadedImage.src;
                image.name = "{{$attribute_name}}";
                image.id = "{{$attribute_name}}";

                let modalBody = document.querySelector("#cropModal_{{$attribute_name}} .modal-body");
                modalBody.append(image);

                $cropper_{{$attribute_name}} = new Croppie(image, {
                    viewport: {
                        width: attribW_{{$attribute_name}},
                        height: attribH_{{$attribute_name}},
                        type: 'square'
                    },
                    boundary: {
                        width: 720,
                        height: 480
                    }
                });
            }

            /**
             * Destroy crop.
             */
            function destroyCrop() {
                $cropper_{{$attribute_name}}.destroy();
                $cropper_{{$attribute_name}} = null;
                document.querySelector("#cropModal_{{$attribute_name}} .modal-body").innerHTML = '';
                if(modalStatus_{{$attribute_name}} === false) {
                    dropzone_{{$attribute_name}}.removeAllFiles();
                }
                modalStatus_{{$attribute_name}} = false;
            }

            /**
             * Crop image and push to dropzone.
             */
            $('#btnCrop_{{$attribute_name}}').on('click', function(e) {
                e.preventDefault();

                $cropper_{{$attribute_name}}.result({
                    type: 'blob',
                    size: 'viewport',
                    circle:'false',
                    quality: 0.9
                }).then(function (blob) {
                    pushToDrop(loadedImage, blob);
                });
            });

            /**
             * Resize image with given only one side and push to dropzone
             */
            function autoResizeImg(img)
            {
                var scaleFactor, height, width;

                if(attribH_{{$attribute_name}} !== '') {
                    scaleFactor = attribH_{{$attribute_name}} / img.height;
                    height = attribH_{{$attribute_name}};
                    width = img.width * scaleFactor;
                } else if(attribW_{{$attribute_name}} !== '') {
                    scaleFactor = attribW_{{$attribute_name}} / img.width;
                    width = attribW_{{$attribute_name}};
                    height = img.height * scaleFactor;
                }

                const elem = document.createElement('canvas');
                elem.width = width;
                elem.height = height;
                const ctx = elem.getContext('2d');
                ctx.drawImage(img, 0, 0, width, height);

                ctx.canvas.toBlob(function(blob) {
                    pushToDrop(img, blob);
                }, img.dataset.type, 1);
            }

            /**
             * Push to dropzone and process queue.
             */
            function pushToDrop(img, blob) {
                const file = new File([blob], img.dataset.name, {
                    type: img.dataset.type,
                    lastModified: Date.now()
                });

                dropzone_{{$attribute_name}}.removeAllFiles();
                dropzone_{{$attribute_name}}.addFile(file);
                dropzone_{{$attribute_name}}.processQueue();
                modalStatus_{{$attribute_name}} = 'Upload';
                $cropModal_{{$attribute_name}}.modal('hide');
            }

            /**
             * Get the link to the image.
             */
            function successCall(file, response) {
                $('#input_{{$attribute_name}}').val(response.accessUrl);
            }
        });
    </script>
@endsection
@endif
