@extends('editora::base')

@section('body')
	<main id="main">
		<div class="status-bar">
			<div class="item-parameters">
				<div class="container">
					<span class="item-tit">
						<span class="section-name">{{getMessage('settings')}}</span>
					</span>
				</div>
			</div>
		</div>
		<div class="container-fluid">
			<form method="post" action="{{route('editora.action', 'configure')}}" name="form_user" id="form_user">
				{{ csrf_field() }}
				<input type="hidden" id="hiddencheck" name="hiddencheck" value="change_user" />
				<section class="fixed-block open-items">
					<header class="block-header">
						<div class="container">
							<div class="data">
								<h2 class="tit">{{getMessage('user_data')}}</h2>
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
												<label for="username" class="form-label">{{getMessage('user')}}</label>
												<input type="text" name="username" id="username" class="form-control" value="{{$user['username']}}" required>
											</div>

											<div class="form-group">
												<label for="complete_name" class="form-label">{{getMessage('name')}}</label>
												<input type="text" name="complete_name" id="complete_name" class="form-control" value="{{$user['complete_name']}}" required>
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
												<span class="btn-text">{{getMessage('save')}}</span>
											</button>
										</div>
									</div>
								</div>
							</div>
						</li>
					</ul>
				</section>
			</form>
			<form method="post" action="{{route('editora.action', 'configure')}}" name="form_changepassword" id="form_changepassword">
				{{ csrf_field() }}
				<input type="hidden" id="hiddencheck" name="hiddencheck" value="change_password" />
				<section class="fixed-block open-items">
					<header class="block-header">
						<div class="container">
							<div class="data">
								<h2 class="tit">{{getMessage('change_password')}}</h2>
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
												<label for="old_password" class="form-label">{{getMessage('current_password')}}</label>
												<input type="password" name="old_password" id="old_password" class="form-control">
											</div>
											<div class="form-group">
												<label for="password" class="form-label">{{getMessage('new_password')}}</label>
												<input type="password" name="password" id="password" class="form-control">
											</div>
											<div class="form-group">
												<label for="repeat_password" class="form-label">{{getMessage('repeat_password')}}</label>
												<input type="password" name="repeat_password" id="repeat_password" class="form-control">
											</div>
										</div>
										<div class="column column-media">
											<div class="form-group">
												@if(isset($messages['ok_pass']))
													<span class="btn clr-danger">{{$messages['ok_pass']}}</span>
												@elseif(isset($messages['ko_pass']))
													<span class="btn clr-danger">{{$messages['ko_pass']}}</span>
												@endif
											</div>
										</div>
										<div class="button-row">
											<button type="submit" class="btn clr-secondary">
												<span class="btn-text">{{getMessage('save_password')}}</span>
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
