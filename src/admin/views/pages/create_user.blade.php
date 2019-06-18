@extends('editora::base')

@section('body')
	<main id="main">
		<div class="status-bar">
			<div class="item-parameters">
				<div class="container">
					<span class="item-tit">
						<span class="section-name">Crear usuarios</span>
					</span>
				</div>
			</div>
		</div>
		<div class="container-fluid">
			<form method="post" action="{{route('editora.action', 'create_users')}}" name="form_create_user" id="form_create_user">
				{{ csrf_field() }}
				<input type="hidden" id="hiddencheck" name="hiddencheck" value="create_user" />
				<section class="fixed-block open-items">
					<header class="block-header">
						<div class="container">
							<div class="data">
								<h2 class="tit">Nuevo usuario</h2>
							</div>
						</div>
					</header>
					<ul class="block-items-list">
						<li class="block-item expanded">
							<div class="collapse show" id="collapseItem5">
								<div class="container">
									<div class="form default-block-form">
										<div class="column column-text">
											<div class="form-group">
												<label for="username" class="form-label">Usuario</label>
												<input type="text" name="username" id="username" class="form-control" required>
											</div>
											<div class="form-group">
												<label for="complete_name" class="form-label">Nombre completo</label>
												<input type="text" name="complete_name" id="complete_name" class="form-control"  required>
											</div>
											<div class="form-group">
												<label for="rol" class="form-label">Rol</label>
												<select class="form-control" name="rol">
													@foreach($roles as $item)
														<option value="{{$item['id']}}">{{$item['rol_name']}}</option>
													@endforeach
												</select>
											</div>
										</div>
										<div class="column column-media">
											<div class="form-group">
												@if(isset($messages['ok_user']))
													<span class="btn clr-danger">{{$messages['ok_user']}}</span>
												@elseif(isset($messages['ko_user']))
													<span class="btn clr-danger">{{$messages['ko_user']}}</span>
												@endif
											</div>
										</div>
										<div class="button-row">
											<button type="submit" class="btn clr-secondary">
												<span class="btn-text">Crear</span>
											</button>
										</div>
									</div>
								</div>
							</div>
						</li>
					</ul>
				</section>
			</form>
		</div>
	</main>
@endsection

@section('scripts')
	@parent
	<script src="/vendor/editora/js/jquery.selectric.js"></script>
	<script type="text/javascript">
        $(document).ready( function() {
            $('select').selectric();
        });
	</script>
@endsection