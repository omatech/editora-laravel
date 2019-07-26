@extends('editora::base')

@section('body')
	<main id="main">
		<div class="status-bar">
			<div class="item-parameters">
				<div class="container">
					<span class="item-tit">
						<span class="section-name">{{getMessage('static_text')}}:</span>
						<h1 class="tit">{{$key}}</h1>
					</span>
					<div class="publish-info">
						<div class="publish-row">
							<a onclick="document.getElementById('form_st').submit();" class="btn clr-secondary"><span class="btn-text">{{getMessage('save')}}</span></a>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="container-fluid">
			<form method="post" action="#form_st" name="form_st" id="form_st">
				{{ csrf_field() }}
				<input type="hidden" id="hiddencheck" name="hiddencheck" value="key" />
				<input type="hidden" id="key" name="key" value="{{$key}}" />
				<section class="fixed-block open-items">
					<ul class="block-items-list">
						<li class="block-item expanded">
							<div class="collapse show" id="collapseItem5">
								<div class="container">
									<div class="form default-block-form">
									@foreach($stext_lg as $item)
										<div class="column column-text">
											<div class="form-group">
												<label for="lang_{{$item['language']}}" class="form-label">{{$item['language']}}</label>
												<input type="text" class="form-control" name="lang_{{$item['language']}}" value="{{$item['text_value']}}">
											</div>
										</div>
									@endforeach
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
