@extends('editora::base')

@section('body')
<main id="main">
	<section class="table-view-header">
		<div class="container">
			<span class="data">
				<h3 class="tit">{{$class['class_name']}}</h3>
			</span>
			@if($p_mode!='R')
				<a href="{{route('editora.action', 'new_instance/?p_class_id='.$class['id'])}}"><span class="btn clr-secondary" style="font-size:14px">{{__('editora_lang::messages.info_create_object')}}: {{$class['class_name']}}</span></a>
			@endif

			@if(file_exists(public_path().'/vendor/editora/extras/classes_sample/'.$class['class_internal_name'].'.jpg'))
				<a href="#" id="showClassSample" role="button" data-toggle="modal" class="btn-square clr-gray"><span class="mdi mdi-file-eye-outline" style="font-size:30px"></span></a>
			@endif

			@if($p_mode=='R' && isset($params_relation))
				<form class="form" method="get" action="{{ route('editora.action', $p_action) }}">
					<span class="input-group">
						<input type="text" class="form-control" id="p_search_query" name="p_search_query" placeholder="Buscar">
						<span class="input-addon">
							<i class="icon-magnify"></i>
						</span>
					</span>
					<input type="hidden" name="p_pagina" value="1"/>
					@isset($params_relation['class_id'])<input type="hidden" name="p_class_id" value="{{$params_relation['class_id']}}"/>@endisset
					@isset($params_relation['relation_id'])<input type="hidden" name="p_relation_id" value="{{$params_relation['relation_id']}}"/>@endisset
					@isset($params_relation['inst_id'])<input type="hidden" name="p_inst_id" value="{{$params_relation['inst_id']}}"/>@endisset
					@isset($params_relation['parent_inst_id'])<input type="hidden" name="p_parent_inst_id" value="{{$params_relation['parent_inst_id']}}"/>@endisset
					@isset($params_relation['parent_class_id'])<input type="hidden" name="p_parent_class_id" value="{{$params_relation['parent_class_id']}}"/>@endisset
					@isset($params_relation['child_class_id'])<input type="hidden" name="p_child_class_id" value="{{$params_relation['child_class_id']}}"/>@endisset
				</form>
			@else
				<form action="{{ route('editora.action', 'search') }}">
					<span class="input-group">
						@if(isset($class['id']))<input type="hidden" id="p_class_id" name="p_class_id" value="{{$class['id']}}">@endif
						<input type="text" class="form-control" id="p_search_query" name="p_search_query" placeholder="Buscar">
						<span class="input-addon">
							<i class="icon-magnify"></i>
						</span>
					</span>
				</form>
			@endif
				
		</div>
	</section>
	<section class="table-view">
		<div class="container">
			@include('editora::templates.instances_list')
		</div>
	</section>
</main>

@include('editora::modals.classes_sample')

@endsection
