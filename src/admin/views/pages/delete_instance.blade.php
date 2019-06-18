@extends('editora::base')

@section('body')
<main id="main">
	<section class="table-view-header">
		<div class="container">
			<span class="data">
				<h3 class="tit">Borrar instancia</h3>
			</span>
		</div>
	</section>
	@if(!empty($related_instances['pares']) || !empty($related_instances['fills']))
	<section class="table-view">
		<div class="container">
				

			<h3 class="tit">No es posible el borrado, existen una o más relaciones con el objeto que desea eliminar! <a href="{{ route('editora.action', 'view_instance/?p_pagina=1&p_class_id='.$params['param1'].'&p_inst_id='.$params['param2']) }}">Volver.</a></h3>
			<h3 class="tit">Objetos relacionados encontrados:</h3>
			<table class="table main-table" id="pages-table">
				<thead>
				<tr>
					<th class="id"><span>ID</span></th>
					<th class="tit"><span>Clave</span></th>
					<th class="type"><span>Tipo</span></th>
					<th class="status"><span>Estado</span></th>
				</tr>
				</thead>
				<tbody>
				@foreach($related_instances['pares'] as $item)
					@php($link=route('editora.action', 'view_instance?p_pagina=1&p_class_id='.$item['class_id'].'&p_inst_id='.$item['id']))
					<tr>
						<td class="id"><a href="{!! $link !!}">{{$item['id']}}</a></td>
						<td class="tit"><a href="{!! $link !!}">{{$item['key_fields']}}</a></td>
						<td class="type">{{$item['class_realname']}}</td>
						<td class="status">
							<button class="btn-square">
								@if($item['status']=="O")
									<span class="status-ball clr-published"></span>
								@elseif($item['status']=="V")
									<span class="status-ball clr-pending"></span>
								@else
									<span class="status-ball clr-unpublished"></span>
								@endif
							</button>
						</td>
					</tr>
				@endforeach
				@foreach($related_instances['fills'] as $item)
					@php($link=route('editora.action', 'view_instance/?p_pagina=1&p_class_id='.$item['class_id'].'&p_inst_id='.$item['id']))
					<tr>
						<td class="id"><a href="{!! $link !!}">{{$item['id']}}</a></td>
						<td class="tit"><a href="{!! $link !!}">{{$item['key_fields']}}</a></td>
						<td class="type">{{$item['class_realname']}}</td>
						<td class="status">
							<button class="btn-square">
								@if($item['status']=="O")
									<span class="status-ball clr-published"></span>
								@elseif($item['status']=="V")
									<span class="status-ball clr-pending"></span>
								@else
									<span class="status-ball clr-unpublished"></span>
								@endif
							</button>
						</td>
					</tr>
				@endforeach
				</tbody>
			</table>
		</div>
	</section>
    @else
		<div class="container">
			<span class="data">
				<h2 class="tit">¿Está seguro? <a href="{{route('editora.action', 'delete_instance2/?p_pagina=1&p_class_id='.$params['param1'].'&p_inst_id='.$params['param2'])}}">Sí</a> <a href="{{route('editora.action', 'view_instance/?p_pagina=1&p_class_id='.$params['param1'].'&p_inst_id='.$params['param2'])}}">No</a></h2>
			</span>
		</div>
	@endif
</main>
@endsection
