@extends('editora::base')

@section('body')
<main id="main">
	<section class="table-view-header">
		<div class="container">
			<span class="data">
				<h3 class="tit">{{getMessage('view_unlinked_files')}}</h3>
			</span>
		</div>
	</section>
	<section class="table-view">
		<div class="container">
			<table class="table main-table" id="pages-table">
				<thead>
				<tr>
					<th class="id">Instance</span></th>
					<th class="tit">URL</span></th>
				</tr>
				</thead>
				<tbody>
				@if(isset($instances))
					@foreach($instances as $item)
                        @php
                            if(strlen($item['url'])>80){
                                $item['url'] = substr($item['url'],0,80).'...';
                            }
                        @endphp
						<tr>
							<td class="id">{{$item['inst_id']}}</td>
							<td class="tit">
                                <a href="{{ APP_BASE }}view_instance?p_pagina=1&p_class_id={{ $item['class_id'] }}&p_inst_id={{ $item['inst_id'] }}">{{ $item['url'] }}</a>
							</td>
						</tr>
					@endforeach
				@endif
				</tbody>
			</table>
		</div>
	</section>
</main>
@endsection
