@extends('editora::base')

@section('body')
	<main id="main">
		<div id="toolbar" class="container-fluid">
			<span class="toolbar-left">
				<ul class="language-tabs nav nav-tabs">
						
					<li @if($selected_language=='ALL') class="active" @endif><a href="{{route('editora.action', "static_text")}}">{{__('editora_lang::messages.all')}}</a></li>
					@foreach($languages as $lang)
						<li @if($selected_language==$lang) class="active" @endif><a href="{{route('editora.action', 'static_text?text_lang='.$lang)}}">{{$lang}}</a></li>
					@endforeach
				</ul>
			</span>
		</div>
		<section class="table-view-header">
			<div class="container">
				<span class="data">
					<h3 class="tit">{{__('editora_lang::messages.static_text')}}</h3>
				</span>
				<div class="form">
					<div class="form-group">
						<span class="input-group">
							<input id="search_instances" type="text" class="form-control" placeholder="Buscar">
							<span class="input-addon">
								<i class="icon-magnify"></i>
							</span>
						</span>
					</div>
				</div>
			</div>
		</section>
		<section class="table-view">

			<div class="container">
				<table class="table" id="pages-table">
					<thead>
					<tr>
						<th class="tit"><span>{{__('editora_lang::messages.info_word_keyword')}}</span></th>
						<th class="tit"><span>{{__('editora_lang::messages.info_word_language')}}</span></th>
						<th class="type"><span>{{__('editora_lang::messages.info_word_text')}}</span></th>
						<th class="favorite"><span class="hidden">{{__('editora_lang::messages.acciones')}}</span></th>
					</tr>
					</thead>
					<tbody>
					@if(isset($static_texts))
						@foreach($static_texts as $item)
							<tr class="published favorited">
								<td class="tit">{{$item['text_key']}}</td>
								<td class="tit">{{$item['language']}}</td>
								<td class="tit">{{$item['text_value']}}</td>
								<td class="favorite">
                        			<span>
                        			    <a href="{{route('editora.action', 'edit_static_text?key='.$item['text_key'])}}" class="btn-square clr-default"><i class="icon-pencil"></i></a>
                        			</span>
								</td>
							</tr>
						@endforeach
					@endif
					</tbody>
				</table>

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
