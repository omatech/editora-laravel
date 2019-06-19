@extends('editora::base')

@section('body')
<main id="main">
	<section class="table-view-header">
		<div class="container">
			<span class="data">
				<h3 class="tit">{{$class['class_name']}}</h3>
			</span>
			@if($p_mode!='R')
				<a href="{{route('editora.action', 'new_instance/?p_class_id='.$class['id'])}}"><span class="btn clr-secondary">Crear nuevo elemento: {{$class['class_name']}}</span></a>
			@endif
			<form action="{{ route('editora.action', 'search') }}">
				<span class="input-group">
					@if(isset($class['id']))<input type="hidden" id="p_class_id" name="p_class_id" value="{{$class['id']}}">@endif
					<input type="text" class="form-control" id="p_search_query" name="p_search_query" placeholder="Buscar">
					<span class="input-addon">
						<i class="icon-magnify"></i>
					</span>
				</span>
			</form>
		</div>
	</section>
	<section class="table-view">
		<div class="container">
			@include('editora::templates.instances_list')
		</div>
	</section>
</main>
@endsection
