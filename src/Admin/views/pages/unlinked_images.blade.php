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
					<th class="id"><span>{{getMessage('path')}}</span></th>
					<th class="type"><span>{{getMessage('size')}}</span></th>
					<th class="tit"><span>{{getMessage('image')}}</span></th>
					<th class="creation-date"><span>{{getMessage('info_word_creation_date')}}</span></th>
					<th class="actions"><span class="hidden">{{getMessage('info_word_delete')}}</span></th>
				</tr>
				</thead>
				<tbody>
				@if(isset($instances))
					@foreach($instances as $item)
						<tr>
							<td class="id">{{$item['url']}}</td>
							<td class="type">{{$item['size']}}</td>
							<td class="tit">
							@if(in_array($item['extension'], ['PNG', 'JPG', 'JPEG', 'GIF', 'WEBP']))
								<img src="{{$item['url']}}" style="max-height: 75px">
							@else
								{{$item['extension']}}
							@endif
							</td>
							<td class="creation-date"><time class="date">{{$item['date']}}</time></td>
							<td class="actions">
								<a onclick="return confirm('¿Está seguro de borrar el fichero?')" href="{{route('editora.action', 'delete_image2').'?'.http_build_query(['image_full' => $item['full_url']])}}"  class="btn-square clr-default" ><i class="icon-delete"></i></a>
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