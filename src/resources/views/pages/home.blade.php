@extends('editora::base')

@section('body')
<main id="main">
	<section class="table-view-header">
		<div class="container">
			<span class="data">
				<span class="holder-icon">
					<i class="icon-clock"></i>
				</span>
				<h3 class="tit">{{__('editora_lang::messages.container_ultimos_objetos')}}</h3>
			</span>
			<div class="form-group" style="margin-bottom:0;">
				<form action="{{ route('editora.search') }}">
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
			@include('editora::templates.instances_list')
		</div>
	</section>
</main>
@endsection