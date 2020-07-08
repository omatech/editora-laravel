{{-- Relation --}}

@if($p_mode=='V')
    <ul class="block-items-list">
        <li class="block-item block-item-group expanded">
            <header class="block-item-header" data-toggle="collapse" data-target="#collapseRelPanel-{{$attribute['id']}}" aria-expanded="true" aria-controls="collapseRelPanel-{{$attribute['id']}}">
                <div class="container">
                    <span class="block-item-tit">
                        <h3 class="item-tit">{{$attribute['caption']}}</h3>
                    </span>
                    <ul class="block-item-actions">
                        <li>
                            <span class="btn-square clr-transparent collapse-trigger" ><i class="icon-chevron-down"></i></span>
                        </li>
                    </ul>
                </div>
            </header>
            <div class="col_item alert list alert_right hidden">
                <span></span>
                <div><p></p></div>
                <p class="btn_close"><a title="Tancar" class="close">Tancar</a></p>
            </div>
            <div class="collapse show" id="collapseRelPanel-{{$attribute['id']}}">
                <div class="container">
                    <div class="rel-group">
                        <div class="rel-controls">
                            <ul class="controls-list">
                                @if(session('user_type')=='O' && session('rol_id')==1 )
                                    <li>
                                        {!! _attributeInfo($attribute['id'], $attribute['name'], $attribute['type']) !!}
                                    </li>

                                @endif
                                @php
                                    if ($attribute['max_length']!=0){
                                        $classes_id = $attribute['related_instances']['info']['child_class_id'];
                                    }else {
                                        $classes_id = 0;
                                        $multiple_child_classes = $attribute['related_instances']['info']['multiple_child_class_id'];
                                        $child_classes = explode(',', $multiple_child_classes);
                                    }
                                @endphp
                                <li><a href="{{route('editora.action', 'join/?p_class_id='.$classes_id.'&p_inst_id='.$instance['id'].'&p_relation_id='.$attribute['id'].'&p_inst_id='.$instance['id'].'&p_parent_inst_id='.$instance['id'].'&p_parent_class_id='.$instance['class_id'].'&p_child_class_id='.$classes_id) }}" class="btn-square clr-default"><i class="icon-link-rel"></i><span class="hide-txt">{{getMessage('info_word_join')}}</span></a></li>

                                @if ($attribute['max_length']!=0)
                                    <li><a href="{{route('editora.action', 'add_and_join/?p_pagina=1&p_relation_id='.$attribute['id'].'&p_inst_id='.$instance['id'].'&p_parent_class_id='.$instance['class_id'].'&p_child_class_id='.$classes_id.'&p_tab=1') }}" class="btn-square clr-default"><i class="icon-plus-box"></i><span class="hide-txt">{{getMessage('info_word_addjoin')}}</span></a></li>
                                @else
                                    <li><a href="" data-toggle="modal" data-target="#modal{{$attribute['id']}}" class="btn-square clr-default"><i class="icon-plus-box"></i><span class="hide-txt">{{getMessage('info_word_addjoin')}}</span></a></li>

                                    @section('modals')
                                    @parent

                                    @if( file_exists( public_path().'/vendor/editora/extras/classes_sample' ) )
                                        {{--Modal with Image Sample--}}
                                        <div class="modal modal-related fade" id="modal{{$attribute['id']}}" tabindex="-1" role="dialog" aria-labelledby="ModalLabel{{$attribute['id']}}" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">{{$attribute['caption']}}</h5>
                                                        <button type="button" class="btn-ico" data-dismiss="modal" aria-label="Close">
                                                            <i class="icon-close"></i>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">

                                                        <div class="square-proportions">
                                                            <div class="relations-preview-list">
                                                                @foreach($child_classes as $item)
                                                                    <div class="relations-preview-item"
                                                                         data-preview="@if(file_exists(public_path().'/vendor/editora/extras/classes_sample/'.getClassNameInternalName($item).'.jpg')) {{ url('/vendor/editora/extras/classes_sample/'.getClassNameInternalName($item).'.jpg')}} @endif">

                                                                        <a href='{{route('editora.action', 'add_and_join/?p_pagina=1&p_relation_id='.$attribute['id'].'&p_inst_id='.$instance['id'].'&p_parent_class_id='.$instance['class_id'].'&p_child_class_id='.$item.'&p_tab=1') }}'><span class="tit" style="cursor:pointer!important">{{getClassName($item)}}</span></a>
                                                                        <a class="btn-square clr-default" href='{{route('editora.action', 'add_and_join/?p_pagina=1&p_relation_id='.$attribute['id'].'&p_inst_id='.$instance['id'].'&p_parent_class_id='.$instance['class_id'].'&p_child_class_id='.$item.'&p_tab=1') }}' ><i class="icon-plus"></i></a>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                        <div class="square-proportions">
                                                            <figure class="preview-area">
                                                                <div class="no-image-placeholder">
                                                                    <img src="{{ asset('/vendor/editora/img/img_no_available.png') }}" alt="">
                                                                    <span>{{getMessage('classes_modal_not_image')}}</span>
                                                                </div>
                                                                <div class="preview-image-holder">
                                                                    <img src="" alt="" style="display: none;">
                                                                </div>
                                                            </figure>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @else

                                        <div class="modal modal-related fade" id="modal{{$attribute['id']}}" tabindex="-1" role="dialog" aria-labelledby="ModalLabel{{$attribute['id']}}" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">{{$attribute['caption']}}</h5>
                                                        <button type="button" class="btn-ico" data-dismiss="modal" aria-label="Close">
                                                            <i class="icon-close"></i>
                                                        </button>
                                                    </div>
                                                    <div class="">

                                                        <div class="relations-preview-list">
                                                            @foreach($child_classes as $item)
                                                                <div class="relations-preview-item nav-button">
                                                                    <a href='{{route('editora.action', 'add_and_join/?p_pagina=1&p_relation_id='.$attribute['id'].'&p_inst_id='.$instance['id'].'&p_parent_class_id='.$instance['class_id'].'&p_child_class_id='.$item.'&p_tab=1') }}'><span class="tit" style="cursor:pointer!important">{{getClassName($item)}}</span></a>
                                                                    <a class="btn-square clr-default" href='{{route('editora.action', 'add_and_join/?p_pagina=1&p_relation_id='.$attribute['id'].'&p_inst_id='.$instance['id'].'&p_parent_class_id='.$instance['class_id'].'&p_child_class_id='.$item.'&p_tab=1') }}' ><i class="icon-plus"></i></a>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    @endsection

                                @endif
                            </ul>
                            <div class="search-rel">
                                <input type="text" class="form-control rel-searcher" name="autocomplete-{{$attribute['id']}}" id="autocomplete-{{$attribute['id']}}">
                                <i class="icon-magnify"></i>
                            </div>
                        </div>
                        <section class="table-view" id="divrel{{$attribute['id']}}" data-instid="{{$instance['id']}}">
                            <table class="table" id="relation-{{$attribute['id']}}">
                                <thead>
                                <tr>
                                    <th class="sort"><span class="hidden">{{getMessage('info_word_order')}}</span></th>
                                    <th class="status"><span>{{getMessage('info_word_status')}}</span></th>
                                    <th class="id"><span>{{getMessage('info_word_ID')}}</span></th>
                                    <th class="tit"><span>{{getMessage('info_word_keyword')}}</span></th>
                                    <th class="type"><span>{{getMessage('info_word_type')}}</span></th>
                                    <th class="actions"><span class="hidden">{{getMessage('acciones')}}</span></th>
                                </tr>
                                </thead>
                                <tbody id="tabrel{{$attribute['id']}}">
                                @if(!empty($attribute['related_instances']['instances']))
                                    @foreach($attribute['related_instances']['instances'] as $item)
                                        <tr class="published" id="{{$item['inst_id']}}">
                                            <td class="sort"><span class="drag-area"><i class="icon-drag"></i></span></td>

                                            <td class="status">
                                                <button class="btn-square clr-transparent">
                                                    @if($item['status']=="O")
                                                        <span class="status-ball clr-published"></span>
                                                    @elseif($item['status']=="V")
                                                        <span class="status-ball clr-pending"></span>
                                                    @else
                                                        <span class="status-ball clr-unpublished"></span>
                                                    @endif
                                                </button>
                                            </td>

                                            <td class="id"><a href="{{ route('editora.action', 'view_instance?p_class_id='.$item['child_class_id'].'&p_inst_id='.$item['inst_id']) }}">{{$item['inst_id']}}</a></td>
                                            <td class="tit"><a href="{{ route('editora.action', 'view_instance?p_class_id='.$item['child_class_id'].'&p_inst_id='.$item['inst_id']) }}">{{$item['key_fields']}}</a></td>
                                            <td class="type">
                                                <a href="{{ route('editora.action', 'list_instances?p_class_id=' . $item['child_class_id']) }}">
                                                    {{$item['class_realname']}}
                                                </a>
                                            </td>
                                            <td class="actions">
                                                <ul>
                                                    <li><a onclick="reldelete('{{route('editora.action', 'delete_relation_instance?p_relation_id='.$item['id'].'&p_class_id='.$item['parent_class_id'].'&parent_inst_id='.$instance['id'].'&p_inst_id='.$item['inst_id'].'&p_tab='.$tab['id'].'&p_rel_id='.$attribute['id'])}}')" class="btn-square clr-default"><i class="icon-unlink-rel"></i><span class="hide-txt">{{getMessage('unlink')}}</span></a></li>
                                                    <li><a href="{{ route('editora.action', 'edit_instance?p_class_id='.$item['child_class_id'].'&p_inst_id='.$item['inst_id'])}}" class="btn-square clr-default"><i class="icon-pencil"></i><span class="hide-txt">{{getMessage('info_word_edit')}}</span></a></li>
                                                </ul>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr><td></td><td></td><td></td><td>{{getMessage('not_related_instances')}}</td><td></td><td></td></tr>
                                @endif
                                </tbody>
                            </table>
                        </section>
                    </div>
                </div>
            </div>
        </li>
    </ul>
@section('scripts')
    @parent
    @php
        if ($attribute['max_length']!=0){
            $classes_id = $attribute['related_instances']['info']['child_class_id'];
        }else {
            $classes_id = $attribute['related_instances']['info']['multiple_child_class_id'];
        }

    @endphp

    <script type="text/javascript">
        new autoComplete({
            selector: 'input[name="autocomplete-{{$attribute['id']}}"]',
            source: function(term, response){
                var link = '{!! route('editora.action', 'autocomplete?p_relation_id='.$attribute['id'].'&p_inst_id='.$instance['id'].'&p_parent_class_id='.$instance['class_id'].'&p_child_class_id='.$classes_id.'&p_tab=1') !!}';

                $.getJSON(link, { term: term }, function(data){ response(data); });
            },
            renderItem: function (item, search){
                search = search.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&');
                var re = new RegExp("(" + search.split(' ').join('|') + ")", "gi");
                return '<div class="autocomplete-suggestion" data-id="'+item['id']+'">'+item['label'].replace(re, "<b>$1</b>")+' ('+item['className']+')</div>';
            },
            onSelect: function(e, term, item){
                var link = '{!! route('editora.action', 'autocomplete_add?p_rel_id='.$attribute['id'].'&p_parent_inst_id='.$instance['id'].'&p_tab=1&p_child_inst_id=') !!}'+item.getAttribute('data-id');
                $.ajax({
                    url: link,
                    type: "GET",
                    dataType: "html",
                    success: function (html) {
                        $("#relation-{{$attribute['id']}}").html(html);
                        activeSortable();
                    }
                });
                e.preventDefault();
            }
        });
        $(document).ready( function() {
            $('.collapse').on('show.bs.collapse', function(){
                $(this).parent().addClass('expanded');
            });
            $('.collapse').on('hide.bs.collapse', function(){
                $(this).parent().removeClass('expanded');
            })
        });

        @if( file_exists( public_path().'/vendor/editora/extras/classes_sample' ) )
        $('[data-preview]').on('mouseenter', function(){
            var newImage = $(this).data('preview');
            $('.preview-area .preview-image-holder img').attr('src', newImage);
            if (newImage !== '') {
                $('.preview-area .preview-image-holder img').fadeIn();
                $('.preview-area .no-image-placeholder').hide();
            }
            else {
                $('.preview-area .preview-image-holder img').hide();
                $('.preview-area .no-image-placeholder').fadeIn();
            }
        });
        $('.relations-preview-list').on('mouseleave', function(){
            $('.preview-area .preview-image-holder img').fadeOut();
            $('.preview-area .no-image-placeholder').fadeIn();
        });
        @endif
    </script>


@endsection
@elseif($p_mode=='U' || $p_mode=='I')
    <ul class="block-items-list">
        <li class="block-item block-item-group">
            <header class="block-item-header" data-toggle="collapse" data-target="#collapseRelPanel-{{$attribute['id']}}" aria-expanded="false" aria-controls="collapseRelPanel-{{$attribute['id']}}">
                <div class="container">
                    <span class="block-item-tit">
                        <h3 class="item-tit">{{$attribute['caption']}}</h3>
                    </span>
                    <ul class="block-item-actions">
                        <li>
                            <span class="btn-square clr-transparent collapse-trigger" ><i class="icon-chevron-down"></i></span>
                        </li>
                    </ul>
                </div>
            </header>
            <div class="col_item alert list alert_right hidden">
                <span></span>
                <div><p></p></div>
                <p class="btn_close"><a title="Tancar" class="close">Tancar</a></p>
            </div>
            <div class="collapse" id="collapseRelPanel-{{$attribute['id']}}">
                <div class="container">
                    <div class="rel-group">
                        <section class="table-view">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th class="status"><span>{{getMessage('info_word_status')}}</span></th>
                                    <th class="id"><span>{{getMessage('info_word_ID')}}</span></th>
                                    <th class="tit"><span>{{getMessage('info_word_keyword')}}</span></th>
                                    <th class="type"><span>{{getMessage('info_word_type')}}</span></th>
                                    <th class="actions"><span class="hidden">{{getMessage('acciones')}}</span></th>
                                </tr>
                                </thead>
                                <tbody>
                                @if(isset($attribute['related_instances']))
                                    @foreach($attribute['related_instances']['instances'] as $item)
                                        <tr class="published" id="{{$item['inst_id']}}">
                                            <td class="status">
                                            <span class="btn-square clr-transparent">
                                                @if($item['status']=="O")
                                                    <span class="status-ball clr-published"></span>
                                                @elseif($item['status']=="V")
                                                    <span class="status-ball clr-pending"></span>
                                                @else
                                                    <span class="status-ball clr-unpublished"></span>
                                                @endif
                                            </span>
                                            </td>
                                            <td class="id">{{$item['inst_id']}}</td>
                                            <td class="tit">{{$item['key_fields']}}</td>
                                            <td class="type">{{$item['class_realname']}}</td>
                                        </tr>
                                    @endforeach
                                @endif
                                </tbody>
                            </table>
                        </section>
                    </div>
                </div>
            </div>
        </li>
    </ul>
@section('scripts')
    @parent
    <script type="text/javascript">
        $(document).ready( function() {
            $('.collapse').on('show.bs.collapse', function(){
                $(this).parent().addClass('expanded');
            });
            $('.collapse').on('hide.bs.collapse', function(){
                $(this).parent().removeClass('expanded');
            })
        });

    </script>
@endsection
@endif