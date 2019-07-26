@extends('editora::base')

@section('body')
<main id="main">
	<section class="table-view-header">
		<div class="container">
			<span class="data">
				<h3 class="tit">{{getMessage('info_word_delete_instance')}}</h3>
			</span>
		</div>
	</section>
	@if(!empty($related_instances['pares']) || !empty($related_instances['fills']))
	<section class="table-view">
		<div class="container">
				

			<h3 class="tit">{{getMessage('delete_not_possible')}} <a href="{{ route('editora.action', 'view_instance/?p_pagina=1&p_class_id='.$params['param1'].'&p_inst_id='.$params['param2']) }}">{{getMessage('navigation_back')}}</a></h3>
			<h3 class="tit">{{getMessage('related_objects')}}</h3>
			<table class="table main-table" id="pages-table">
				<thead>
				<tr>
					<th class="id"><span>{{getMessage('info_word_ID')}}</span></th>
					<th class="tit"><span>{{getMessage('info_word_keyword')}}</span></th>
					<th class="type"><span>{{getMessage('info_word_type')}}</span></th>
					<th class="status"><span>{{getMessage('info_word_status')}}</span></th>
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
				<h2 class="tit">{{getMessage('info_word_areyousure')}} <a href="{{route('editora.action', 'delete_instance2/?p_pagina=1&p_class_id='.$params['param1'].'&p_inst_id='.$params['param2'])}}">{{getMessage('info_word_yes')}}</a> <a href="{{route('editora.action', 'view_instance/?p_pagina=1&p_class_id='.$params['param1'].'&p_inst_id='.$params['param2'])}}">{{getMessage('info_word_no')}}</a></h2>
			</span>
		</div>
	@endif
</main>
@endsection
