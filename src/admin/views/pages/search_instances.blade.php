@extends('editora::base')

@section('body')
<main id="main">
	<section class="table-view-header">
		<div class="container">
			<span class="data">
				<h3 class="tit">Resultados para: "{{$term}}"</h3>
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
			@include('editora::templates.instances_list')
		</div>
	</section>
</main>
@endsection
