{{-- Imatge --}}
@if($p_mode=='V')
    @php
        $file = '';
        if(isset($attribute['atrib_values'][0])){
            $file = $attribute['atrib_values'][0]['text_val'];
        }
    @endphp
    <div class="column column-media">
        <span class="form-label">{{$attribute['caption']}} {!! _attributeInfo($attribute['id'], $attribute['name'], $attribute['type']) !!}</span>
        <div class="media-group">
            @if(isset($file) && !empty($file))
                <figure class="media-preview">
                    <img src="{{$file}}" alt="{{$file}}">
                </figure>
                <div class="media-info">
                    <dl class="media-dada">
                        <dt class="media-param">{{getMessage('theoric_size')}}:</dt>
                        <dd class="media-value">{{$attribute['img_w']}}x{{$attribute['img_h']}}&nbsp;</dd>
                        <dt class="media-param">{{getMessage('real_size')}}:</dt>
                        <dd class="media-value">{{Str::replaceFirst('.','x',$attribute['atrib_values'][0]['img_info'])}}
                            &nbsp;
                        </dd>
                        <dt class="media-param">{{getMessage('preview_format')}}:</dt>
                        <dd class="media-value">{{_fileExtension($file)}}&nbsp;</dd>
                        <dt class="media-param">{{getMessage('size')}}:</dt>
                        <dd class="media-value">{{ _getFileSize($file) }}&nbsp;</dd>
                        <dt class="media-param">{{getMessage('path')}}:</dt>
                        <dd class="media-value">@isset($attribute['atrib_values'][0]) {{$attribute['atrib_values'][0]['text_val']}} @endisset&nbsp;</dd>
                    </dl>
                    <ul class="controls-list">
                        <li>
                            <a class="btn-square clr-default" data-toggle="modal" data-target="#i-modal-{{$attribute['id']}}">
                                <i class="icon-eye"></i>
                                <span class="sr-only">
                                {{getMessage('preview')}}
                            </span>
                            </a>
                        </li>
                    </ul>
                </div>

            @else
                <figure class="media-preview">
                    <img src="{{ asset('/vendor/editora/img/img_no_available.png') }}" alt="no image">
                </figure>
                <div class="media-info">
                    <dl class="media-dada">
                        <dt class="media-param">{{getMessage('theoric_size')}}:</dt>
                        <dd class="media-value">{{$attribute['img_w']}}x{{$attribute['img_h']}}&nbsp;</dd>
                    </dl>
                </div>
            @endif

        </div>
    </div>

    <div id="i-modal-{{$attribute['id']}}" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="{{$attribute_name}}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 1020px; max-height: 670px;">
            <div class="modal-content" style="margin: 50px;">
                <div class="modal-body" style="display: flex; justify-content: center; padding: 50px;">
                    <img src="{{$file}}" style="max-width: 1000px; max-height: 650px">
                </div>
            </div>
        </div>
    </div>
@elseif($p_mode=='U' || $p_mode=='I')
    @php
        $file = '';
        if(isset($attribute['atrib_values']) && isset($attribute['atrib_values'][0])){
            $file = $attribute['atrib_values'][0]['text_val'];
        }
    @endphp
    <div class="column column-text">
        <div class="form-group">
            <label for="{{$attribute_name}}" class="form-label">{{$attribute['caption']}}</label>
            <input id="input_{{$attribute_name}}" type="text" class="form-control" name="{{$attribute_name}}" value="@isset($attribute['atrib_values'][0]){{$attribute['atrib_values'][0]['text_val']}}@endisset">
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
            @if(isset($file) && !empty($file))
                <figure class="image-preview">
                    <img id="img_{{$attribute_name}}" src="{{$file}}" alt="{{$file}}" style="@if($attribute['img_h']!='') max-height: {{$attribute['img_h']}}px;@endif @if($attribute['img_w']!='') max-width: {{$attribute['img_w']}}px;@endif">
                </figure>
            @endif

            <span class="properties">
                <span class="dimensions">Teórico: {{$attribute['img_w']}}x{{$attribute['img_h']}}</span>
            </span>
        </div>
    </div>

    <div id="cropModal_{{$attribute_name}}" class="modal fade" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="cropModal" aria-hidden="true">
        <div class="modal-dialog" style="max-width: 750px">
            <div class="modal-content">
                <div class="modal-body" style="max-width: 720px">
                </div>
                <div class="modal-footer">
                        <input type="range" id="scale" name="scale" min="1" max="10" value="1" /><span id="scale-value"></span>
                        <a href="" class="btn clr-secondary" id="btnRotate_L_{{$attribute_name}}"><span class="btn-text"><i class="fa fa-undo" aria-hidden="true"></i></span></a>
                        <a href="" class="btn clr-secondary" id="btnRotate_R_{{$attribute_name}}"><span class="btn-text"><i class="fa fa-repeat" aria-hidden="true"></i></span></a>
                        <a href="" class="btn clr-secondary" id="btnInvertX_{{$attribute_name}}"><span class="btn-text"><i class="fa fa-arrows-h" aria-hidden="true"></i></span></a>
                        <a href="" class="btn clr-secondary" id="btnReset_{{$attribute_name}}"><span class="btn-text"><i class="fa fa-refresh" aria-hidden="true"></i></span></a>
                        <a href="" class="btn clr-danger" data-dismiss="modal"><span class="btn-text">{{getMessage('close')}}</span></a>
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

        $(document).ready(function () {
            let scale = 1;
            let maxScale = 100;

            $('#scale').on('change', function () {
                scale = $(this).val();
                $('#scale-value').text('Scale: ' + scale + ' width: ' + (attribW_{{$attribute_name}} * scale) + ' height: ' + (attribH_{{$attribute_name}} * scale));
            });

            Dropzone.autoDiscover = false;
            var $cropper_{{$attribute_name}} = null;
            const dropzone_{{$attribute_name}} = new Dropzone("div#file_{{$attribute_name}}", {
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                autoProcessQueue: false,
                url: "{{ ADMIN_URL }}/upload_crop",
                maxFiles: 1,
                dictDefaultMessage: "Añadir fichero",
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
            let loadedImage = new Image();
            function addedFile(file) {
                if (dropzone_{{$attribute_name}}.getAcceptedFiles().length >= 1) {
                    let files = dropzone_{{$attribute_name}}.getAcceptedFiles();
                    dropzone_{{$attribute_name}}.removeFile(files[0]);
                }
                let _URL = window.URL || window.webkitURL;
                loadedImage.src = _URL.createObjectURL(file);
                loadedImage.dataset.name = file.name;
                loadedImage.dataset.type = file.type;
                loadedImage.onload = function () {
                    if (file.name.endsWith('.svg')) {
                        dropzone_{{$attribute_name}}.processQueue();
                        return;
                    }

                    if (attribH_{{$attribute_name}} == this.height && attribW_{{$attribute_name}} == this.width) {
                        dropzone_{{$attribute_name}}.processQueue();
                        return;
                    }

                    if (attribH_{{$attribute_name}} === '' && attribW_{{$attribute_name}} === '') {
                        dropzone_{{$attribute_name}}.processQueue();
                        return;
                    }

                    if (attribH_{{$attribute_name}} !== '' && attribW_{{$attribute_name}} !== '') {
                        if (this.width !== attribW_{{$attribute_name}} || this.height !== attribH_{{$attribute_name}}) {
                            cropImg();
                        }
                        return;
                    }

                    if ((attribH_{{$attribute_name}} != '' || attribW_{{$attribute_name}} != '') &&
                        ((attribH_{{$attribute_name}} != '' && attribH_{{$attribute_name}} != this.height) ||
                            (attribW_{{$attribute_name}} != '' && attribW_{{$attribute_name}} != this.width))) {
                        autoResizeImg(loadedImage);
                        return;
                    }

                    dropzone_{{$attribute_name}}.processQueue();
                }
            }

            function cropImg(img) {
                $cropModal_{{$attribute_name}}.modal();
            }
            $cropModal_{{$attribute_name}}.on('shown.bs.modal', instanceCrop);
            $cropModal_{{$attribute_name}}.on('hidden.bs.modal', destroyCrop);
            function instanceCrop() {
                let image = document.createElement('img');
                image.src = loadedImage.src;
                image.name = "{{$attribute_name}}";
                image.id = "{{$attribute_name}}";
                image.style = "width: 100%; max-width: 100%;";
                let modalBody = document.querySelector("#cropModal_{{$attribute_name}} .modal-body");
                modalBody.append(image);

                $cropper_{{$attribute_name}} = new Cropper(image, {
                    viewMode: 2,
                    aspectRatio: calcRatio(attribW_{{$attribute_name}}, attribH_{{$attribute_name}}),
                    maxCanvasWidth: 680,
                    autoCropArea: 1.0
                });

                image.addEventListener('ready', function () {
                    const data = $cropper_{{$attribute_name}}.getCanvasData();
                    const scaleWidth = Math.floor(data.naturalWidth / attribW_{{$attribute_name}}) || 1;
                    const scaleHeight = Math.floor(data.naturalHeight / attribH_{{$attribute_name}}) || 1;
                    maxScale = Math.min(scaleWidth, scaleHeight);
                    $('#scale').attr({max: maxScale});
                });


            }

            function calcRatio(numerator, denominator) {
                var gcd, temp, divisor;
                gcd = function (a, b) {
                    if (b === 0) return a;
                    return gcd(b, a % b);
                }
                if (numerator === denominator) {
                    return 1 / 1;
                }
                if (+numerator < +denominator) {
                    temp = numerator;
                    numerator = denominator;
                    denominator = temp;
                }
                divisor = gcd(+numerator, +denominator);
                return 'undefined' === typeof temp ? ((numerator / divisor) / (denominator / divisor)) : ((denominator / divisor) / (numerator / divisor));
            }

            function destroyCrop() {
                $cropper_{{$attribute_name}}.destroy();
                $cropper_{{$attribute_name}} = null;
                document.querySelector("#cropModal_{{$attribute_name}} .modal-body").innerHTML = '';
                if (modalStatus_{{$attribute_name}} === false) {
                    dropzone_{{$attribute_name}}.removeAllFiles();
                }
                modalStatus_{{$attribute_name}} = false;
            }

            $('#btnRotate_L_{{$attribute_name}}').on('click', function (e) {
                e.preventDefault();
                $cropper_{{$attribute_name}}.rotate(-90);
            });

            $('#btnRotate_R_{{$attribute_name}}').on('click', function (e) {
                e.preventDefault();
                $cropper_{{$attribute_name}}.rotate(90);
            });
            $('#btnInvertX_{{$attribute_name}}').on('click', function (e) {
                e.preventDefault();
                $cropper_{{$attribute_name}}.scaleX(-1);
            });
            $('#btnReset_{{$attribute_name}}').on('click', function (e) {
                e.preventDefault();
                $cropper_{{$attribute_name}}.reset();
            });
            $('#btnCrop_{{$attribute_name}}').on('click', function (e) {
                e.preventDefault();
                let canvas = $cropper_{{$attribute_name}}.getCroppedCanvas({
                    width: attribW_{{$attribute_name}} * scale,
                    height: attribH_{{$attribute_name}} * scale,
                    maxWidth: attribW_{{$attribute_name}} * maxScale,
                    maxHeight: attribH_{{$attribute_name}} * maxScale,
                    imageSmoothingEnabled: true,
                    imageSmoothingQuality: 'high',
                });
                canvas.toBlob((blob) => {
                    pushToDrop(loadedImage, blob);
                }, loadedImage.dataset.type, 0.9);
            });

            function autoResizeImg(img) {
                var scaleFactor, height, width;
                if (attribH_{{$attribute_name}} !== '') {
                    scaleFactor = attribH_{{$attribute_name}} / img.height;
                    height = attribH_{{$attribute_name}};
                    width = img.width * scaleFactor;
                } else if (attribW_{{$attribute_name}} !== '') {
                    scaleFactor = attribW_{{$attribute_name}} / img.width;
                    width = attribW_{{$attribute_name}};
                    height = img.height * scaleFactor;
                }

                const elem = document.createElement('canvas');
                elem.width = width;
                elem.height = height;
                const ctx = elem.getContext('2d');
                ctx.drawImage(img, 0, 0, width, height);

                ctx.canvas.toBlob(function (blob) {
                    pushToDrop(img, blob);
                }, img.dataset.type, 1);
            }

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

            function successCall(file, response) {
                $('#input_{{$attribute_name}}').val(response.accessUrl);
            }
        });
    </script>
@endsection
@endif
