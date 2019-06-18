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
			@include('editora::templates.instances_list')
		</div>
	</section>
</main>
@endsection
