@php
    if (!isset($p_mode)) $p_mode='D';
    if (!isset($page)) $page=1;
    if (!isset($class['id'])) $class['id']=1;
@endphp
@extends('editora::base')

@section('body')
    <main id="main">
        <section class="table-view-header">
            <div class="container">
			<span class="data">
				<span class="holder-icon">
					<i class="icon-clock"></i>
				</span>
				<h3 class="tit">{{getMessage('info_word_last_translations')}}</h3>
			</span>
                <div class="form-group" style="margin-bottom:0;">
                    <form action="{{ route('editora.action', 'search') }}">
					<span class="input-group">
						<input type="text" class="form-control" id="p_search_query" name="p_search_query"
                               placeholder="Buscar" value="{{ $term ?? null }}">
						<button type="submit" class="input-addon">
							<i class="icon-magnify"></i>
						</button>
					</span>
                    </form>
                </div>
            </div>
        </section>
        <section class="table-view">
            <div class="container">
                <table class="table main-table" id="pages-table">
                    <thead>
                    <tr>
                        <th class="status"><span>{{getMessage('info_word_status')}}</span></th>
                        <th class="id"><span>{{getMessage('info_word_ID')}}</span></th>
                        <th class="picture"><span>{{getMessage('info_word_img')}}</span></th>
                        <th class="tit"><span>{{getMessage('info_word_keyword')}}</span></th>
                        <th class="type"><span>{{getMessage('info_word_type')}}</span></th>
                        <th class="creation-date"><span>{{getMessage('info_word_update_date')}}</span></th>
                        <th class="date-condition"><span
                                class="hidden">{{getMessage('info_word_date_condition')}}</span></th>
                        <th class="favorite"><span class="hidden">{{getMessage('acciones')}}</span></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($last_translated as $item)
                        @php($link=route('editora.action', 'view_instance?p_pagina=1&p_class_id='.$item['class_id'].'&p_inst_id='.$item['id']))
                        <tr>
                            <td class="status">
                                <button class="btn-square">
                                    {!! _instanceStatus($item['status']) !!}
                                </button>
                            </td>
                            <td class="id">
                                @if(isset($p_mode) && $p_mode=='R')
                                    <span class="btn-favorite">
                               <input type="checkbox" name="rel_chb[]" id="table-add-{{$item['id']}}"
                                      value="{{$item['id']}}">
                               <label for="table-add-{{$item['id']}}">{{$item['id']}}</label>
                                    </span>
                                @else
                                    <a href="{!! $link !!}">{{$item['id']}}</a>
                                @endif
                            </td>
                            <td class="picture">
                                <figure class="pic">{!! getListImage($item['id']) !!}</figure>
                            </td>
                            <td class="tit"><a href="{!! $link !!}">{{$item['key_fields']}} </a></td>
                            <td class="type">
                                <a href="{{ route('editora.action', 'list_instances?p_class_id=' . $item['class_id']) }}">
                                    {{$item['class_name'] ?? ''}}
                                </a>
                            </td>
                            <td class="creation-date">
                                <time class="date">{{_formatDate($item['cd_ordre'] ?? '')}}</time>
                            </td>
                            <td class="date-condition">
                                <button
                                    class="btn-square {!! _activeDate($item['publishing_begins'], $item['publishing_ends']) !!}"
                                    data-toggle="popover" data-trigger="hover" data-placement="top" data-content=" @if($item['publishing_ends']!=null)
                                        <p>{{ getMessage('published_from') }} <time>{{$item['publishing_begins']}}</time> {{ getMessage('published_to') }} <time>{{$item['publishing_ends']}}</time></p>
                                            @else
                                        <p>{{ getMessage('published_on') }} <time>{{$item['publishing_begins']}}</time></p> @endif">
                                    <i class="icon-calendar-multiple"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                @isset($paginator)
                    {{ $paginator->links() }}
                @endisset
                @section('scripts')
                    @parent
                    <script type="text/javascript">
                        $(document).ready(function() {
                            oTable = $('#pages-table').DataTable({
                                ordering: false,
                                paging: false,
                                searching:true,
                                sDom:"ltipr"
                            });

                            $('#search_instances').keyup(function(){
                                oTable.search($(this).val()).draw() ;
                            })
                        } );
                    </script>
                @endsection
            </div>
        </section>
    </main>
@endsection



