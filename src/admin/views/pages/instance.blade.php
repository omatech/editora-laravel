@extends('editora::base')

@section('body')
<main id="main">
	<div id="toolbar" class="container-fluid">
		<span class="toolbar-left">
			<a href="{{route('editora.action', 'list_instances/?p_pagina=1&p_class_id='.$instance['class_id'])}}" class="btn-square clr-dark">
				<span class="icon-arrow-left"></span>
			</a>
			<ul id="instancetabs" class="language-tabs nav nav-tabs">
			@php($count=0)
			@foreach($instance['instance_tabs'] as $tab)
				<li @if($count==0) class="active" @endif><a class="tab-lang" data-toggle="tab" href="#tab-{{$tab['id']}}">{{$tab['caption']}}</a></li>
				@php($count++)
			@endforeach
			</ul>
		</span>

		<div class="toolbar-right">
			<ul class="actions-list">
				@if(isset($parents) && !empty($parents))
					<li><button class="btn-square clr-dark" id="btn-toggle-relations"><i class="icon-link-rel"></i><span class="sr-only">Objetos padre</span></button></li>
				@endif


				@if($_SESSION['user_type']=='O' && $_SESSION['rol_id']==1 )
					{{-- <li><button class="btn-square clr-dark"><i class="icon-information-outline"></i><span class="sr-only">Información</span></button></li> --}}
				@endif

				{{--<li><button class="btn-square clr-dark"><i class="icon-eye"></i><span class="sr-only">Previsionalizar</span></button></li>--}}
				@if($p_mode=='V')
					<li class="dropdown related-dropdown">
						<button class="btn-square clr-dark" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="icon-settings"></i></button>
						<div class="dropdown-menu">
							<div>
								<a href="{{route('editora.action', 'add_favorite/?p_pagina=1&p_class_id='.$instance['class_id'].'&p_inst_id='.$instance['id'])}}" class="dropdown-item"><i class="icon-star"></i>Añadir a favoritos</a>
								<a href="{{route('editora.action', 'clone_instance/?p_pagina=1&p_class_id='.$instance['class_id'].'&p_inst_id='.$instance['id'])}}" class="dropdown-item"><i class="icon-content-copy"></i>Clonar</a>
								@if($instance['status']!="O")
								<a href="{{route('editora.action', 'delete_instance/?p_pagina=1&p_class_id='.$instance['class_id'].'&p_inst_id='.$instance['id'])}}" class="dropdown-item"><i class="icon-delete"></i>Eliminar</a>
								@endif
								
								<a onclick="refreshView({{$instance['id']}});" class="dropdown-item"><i class="icon-refresh"></i>Refresh View</a>


								
							</div>
						</div>
					</li>
				@endif
			</ul>
			<div class="save-block">
				@if($p_mode=='V')
					<a href="{{route('editora.action', 'edit_instance/?p_pagina=1&p_class_id='.$instance['class_id'].'&p_inst_id='.$instance['id'])}}" class="btn clr-secondary"><span class="btn-text">Editar</span></a>
				@elseif($p_mode=='U' || $p_mode=='I')
					<a onclick="document.getElementById('Form1').submit(); $(this).attr('disabled','disabled');" class="btn clr-secondary"><span class="btn-text">Guardar</span></a>
				@endif
			</div>
		</div>
	</div>
	@if(false && isset($parents) && !empty($parents))
	<div id="parents-menu">
		<header class="side-menu-header">
			<span class="tit">Objetos padre</span>
			<button class="btn-square clr-gray" id="btn-hide-modifications"><i class="icon-close"></i></button>
		</header>
		<div class="side-menu-content">
			<ul class="last-modifications-list">
			@foreach($parents as $item)
				<li class="last-modifications-list-item">
					<p class="tit"><a href="{{route('editora.action', 'view_instance/?p_pagina=1&p_class_id='.$item['class_id'].'&p_inst_id='.$item['id'])}}">{{$item['key_fields']}}</a></p>
					<p class="data">
						<time class="date">{{$item['fecha']}}</time>
					</p>
				</li>
			@endforeach
			</ul>
		</div>
	</div>
	@endif
	<form name="Form1" id="Form1" method="post" @if($p_mode=='U') action="{{route('editora.action', "edit_instance2")}}" @elseif($p_mode=='I') action="	{{route('editora.action', "new_instance2")}}" @else action="" @endif>
		{{ csrf_field() }}
		@if(isset($instance['form_relation']))
			<input type="hidden" name="p_parent_class_id" value="{{$instance['form_relation']['param10']}}">
			<input type="hidden" id="p_tab" name="p_tab" class="input_tabs" value="{{$instance['form_relation']['param14']}}">
			<input type="hidden" name="p_relation_id" value="{{$instance['form_relation']['param9']}}">
			<input type="hidden" name="p_parent_inst_id" value="{{$instance['form_relation']['p_inst_id']}}">
		@endif
		@if(isset($instance['id']) && !empty($instance['id']))
			<input type="hidden" name="p_inst_id" value="{{$instance['id']}}">
		@endif
		@if(isset($instance['class_id']) && !empty($instance['class_id']))
			<input type="hidden" name="p_class_id" value="{{$instance['class_id']}}">
		@endif

		<div class="status-bar">
		@if($p_mode=='V')
			<div class="item-parameters">
				<div class="container">
					<span class="item-tit">
						<span class="section-name">@if(isset($class['class_id']) && !empty($class['class_id'])){{getClassName($class['class_id'])}}@else {{getClassName($instance['class_id'])}} @endif:</span>
						<h1 class="tit">{{$instance['key_fields']}}</h1>
					</span>
					<div class="publish-info">
						@if($instance['publishing_ends']!=null)
							<p class="date">Publicada del <time>{{$instance['publishing_begins']}}</time> al <time>{{$instance['publishing_ends']}}</time></p>
						@else
							<p class="date">Publicada el <time>{{$instance['publishing_begins']}}</time></p>
						@endif
						<div class="publish-row">
							<p class="status">
								@if($instance['status']=="O")
									Publicada <span class="status-ball clr-published"></span>
								@elseif($instance['status']=="V")
									Revisada <span class="status-ball clr-pending"></span>
								@else
									Pendiente <span class="status-ball clr-unpublished"></span>
								@endif
							</p>
							@if($p_mode=='V')
							<a href="{{route('editora.action', 'edit_instance/?p_pagina=1&p_class_id='.$instance['class_id'].'&p_inst_id='.$instance['id'])}}" class="btn-square clr-default"><i class="icon-pencil"></i></a>
							@endif
						</div>
					</div>
				</div>
			</div>
		@elseif($p_mode=='U' || $p_mode=='I')
			<div class="form edit-item-parameters">
				<div class="container">
					<div class="form-row top">
						<div class="form-group">
							<label for="p_status" class="form-label">Estado</label>
							<select class="form-control" name="p_status">
								<option value="P" @if($instance['status']=="P") selected @endif>Pendiente</option>
								<option value="V" @if($instance['status']=="V") selected @endif>Revisada</option>
								<option value="O"  @if($instance['status']=="O") selected @endif>Publicada</option>
							</select>
						</div>
						<div class="form-group">
							@php($attribute = $instance['instance_tabs'][0]['elsatribs'][0])
							@php($attribute_name=_attributeName($attribute))
							<label for="{{$attribute_name}}" class="form-label">{{$attribute['caption']}}</label>
							<input type="text" class="form-control" name="{{$attribute_name}}" value="{{$attribute['atrib_values'][0]['text_val']}}">
						</div>
					</div>
					<div class="form-row date-row">
						<div class="form-group">
							<label for="date_s1" class="form-label">Inicio de publicación</label>
							<span class="input-group">
								<input type="text" length="35" name="p_publishing_begins" value="{{$instance['publishing_begins']}}" id="date1" class="form-control datepicker" autocomplete="off">
								<span class="input-addon">
									<i class="icon-calendar"></i>
								</span>
							</span>
						</div>
						<div class="form-group">
							<label for="date_s1" class="form-label">Final de publicación</label>
							<span class="input-group">
								<input type="text" length="35" name="p_publishing_ends" value="{{$instance['publishing_ends']}}" id="date2" class="form-control datepicker" autocomplete="off">
								<span class="input-addon">
									<i class="icon-calendar"></i>
								</span>
							</span>
						</div>
					</div>
				</div>
			</div>
		@endif
		</div>

		<div class="container-fluid">
			<div class="tab-content">
				@foreach($instance['instance_tabs'] as $tab)
					@php($exist_relations=false)
					<div id="tab-{{$tab['id']}}" class="tab-pane  @if($tab['id']==1)in active @endif">
						<section class="fixed-block open-items">
							<ul class="block-items-list">
								<li class="block-item expanded">
									<div class="collapse show" id="collapseItem5">
										<div class="container">
										@foreach($tab['elsatribs'] as $attribute)
											<div class="form default-block-form">
												@if($attribute['type']!='R')
													@php($attribute_name=_attributeName($attribute))
													@includeIf('editora::attributes.'.$attribute['type'])
												@elseif($attribute['type']=='R')
													@php($exist_relations=true)
												@endif
											</div>
										@endforeach
										</div>
									</div>
								</li>
							</ul>
						</section>
						@if(isset($exist_relations) && $exist_relations == true)
							<section class="fixed-block">
								<header class="block-header">
									<div class="container">
										<span class="data">
											<h2 class="tit">Relaciones</h2>
										</span>
									</div>
								</header>
								@foreach($tab['elsatribs'] as $attribute)
									@if($attribute['type']=='R')
										@php($attribute_name=_attributeName($attribute))
										@includeIf('editora::attributes.R')
									@endif
								@endforeach
							</section>
						@endif
					</div>
				@endforeach
			</div>
		</div>
	</form>
</main>
@endsection
@section('scripts')
	@parent
	<script type="text/javascript" src="{{ asset('/vendor/editora/js/jquery.selectric.js') }}"></script>
	<script type="text/javascript">
        $(document).ready( function() {
            $('select').selectric();

            $('.tab-lang').on('show.bs.tab', function(){
                $("#instancetabs>li.active").removeClass("active");
                $(this).parent().addClass('active');
            });
        });



		function refreshView(inst_id) { 
			info = 'ajax=refresh_view&inst_id='+inst_id;
			$.ajax({
				url: '{{route('editora.action', 'ajax_actions')}}',
				type: "GET",
				data: info,
				success: function(data){
					alert( "Refresh ok" );
				}
			});
		}
    
	</script>

	
@endsection