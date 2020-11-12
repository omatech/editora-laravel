@extends('editora::base')

@section('body')
<main id="main">
	<section class="table-view-header">
		<div class="container">
			<span class="data">
				<h3 class="tit">Users</h3>
			</span>
		</div>
	</section>
	<section class="table-view">
		<div class="container">
			<table class="table main-table" id="pages-table">
				<thead>
				<tr>
					<th class="id"><span>ID</span></th>
					<th class="type"><span>Username</span></th>
					<th class="tit"><span>Complete name</span></th>
					<th class="tit"><span>Type</span></th>
					<th class="tit"><span>Rol Id</span></th>
					<th class="tit"><span>Rol name</span></th>
				</tr>
				</thead>
				<tbody>
				@if(isset($users))
					@foreach($users as $item)
						<tr>
							<td class="id">{{$item['id']}}</td>
							<td class="type">{{$item['username']}}</td>
							<td class="tit">{{$item['complete_name']}}</td>
							<td class="tit">{{$item['tipus']}}</td>
							<td class="tit">{{$item['rol_id']}}</td>
							<td class="tit">{{$item['rol_name']}}</td>
						</tr>
					@endforeach
				@endif
				</tbody>
			</table>
		</div>
	</section>
	
	@isset($roles_classes)
		@foreach ($roles_classes as $key=>$rol)
			
			<section class="table-view-header">
				<div class="container">
					<span class="data">
						<h3 class="tit">Rol {{ $key }} @isset($roles[$key]) : {{ $roles[$key] }} @endisset</h3>
					</span>
				</div>
			</section>	
			<section class="table-view">
				<div class="container">
					<table class="table main-table" id="pages-table">
						<thead>
						<tr>
							<th class="id"><span>Class Name</span></th>
							<th class="tit"><span>Class Id</span></th>
							<th class="tit"><span>browseable</span></th>
							<th class="tit"><span>insertable</span></th>
							<th class="tit"><span>editable</span></th>
							<th class="tit"><span>deleteable</span></th>
							<th class="tit"><span>permisos</span></th>
							<th class="tit"><span>status 1</span></th>
							<th class="tit"><span>status 2</span></th>
							<th class="tit"><span>status 3</span></th>
							<th class="tit"><span>status 4</span></th>
							<th class="tit"><span>status 5</span></th>
						</tr>
						</thead>
						<tbody>
							@foreach($rol as $item)
								<tr>
									<td class="id">{{$item['name']}}</td>
									<td class="tit">{{$item['class_id']}}</td>
									<td class="tit">{{$item['browseable']}}</td>
									<td class="tit">{{$item['insertable']}}</td>
									<td class="tit">{{$item['editable']}}</td>
									<td class="tit">{{$item['deleteable']}}</td>
									<td class="tit">{{$item['permisos']}}</td>
									<td class="tit">{{$item['status1']}}</td>
									<td class="tit">{{$item['status2']}}</td>
									<td class="tit">{{$item['status3']}}</td>
									<td class="tit">{{$item['status4']}}</td>
									<td class="tit">{{$item['status5']}}</td>
								</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			</section>
		@endforeach
	@endif
</main>
@endsection