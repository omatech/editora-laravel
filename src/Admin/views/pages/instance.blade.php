@extends('editora::base')

@section('body')
<main id="main">
	<div id="toolbar" class="container-fluid">
		<span class="toolbar-left">
			@isset($instance['class_id'])
			<a href="{{route('editora.action', 'list_instances?p_pagina=1&p_class_id='.$instance['class_id'])}}" class="btn-square clr-dark">
				<span class="icon-arrow-left"></span>
			</a>
			@endisset
			<ul id="instancetabs" class="language-tabs nav nav-tabs">
			@php $count=0  @endphp
			@foreach($instance['instance_tabs'] as $tab)
				<li @if($count==0) class="active" @endif><a class="tab-lang" data-toggle="tab" href="#tab-{{$tab['id']}}">{{$tab['caption']}}</a></li>
				@php $count++ @endphp
			@endforeach
			@includeIf('editora::extraTabMenu')
			</ul>
		</span>

		<div class="toolbar-right">
			<ul class="actions-list">
				@if(isset($parents) && !empty($parents))
					<li><button class="btn-square clr-dark" id="btn-toggle-relations"><i class="icon-link-rel"></i><span class="sr-only">{{getMessage('container_objetos_padre')}}</span></button></li>
				@endif


				@if(session('user_type')=='O' && session('rol_id')==1 && $p_mode!='I' )
					<li class="dropdown related-dropdown">
						<button class="btn-square clr-dark" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="icon-information-outline"></i></button>
						<div class="dropdown-menu">
							<div>
								<p class="dropdown-item">Class Name: {{$instance['class_name'] }}</p>
								<p class="dropdown-item">Class Id: {{$instance['class_id'] }}</p>
								<p class="dropdown-item">Instance Id: {{$instance['id'] }}</p>
								<p class="dropdown-item">Created at: {{$instance['creation_date']}}</p>
							</div>
						</div>
					</li>
				@endif

				@if($p_mode=='V')
					<li class="dropdown related-dropdown">
						<button class="btn-square clr-dark" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="icon-settings"></i></button>
						<div class="dropdown-menu">
							<div>
								<a href="{{route('editora.action', 'add_favorite?p_pagina=1&p_class_id='.$instance['class_id'].'&p_inst_id='.$instance['id'])}}" class="dropdown-item"><i class="icon-star"></i>{{getMessage('info_word_addfavorites')}}</a>
								<a href="{{route('editora.action', 'clone_instance?p_pagina=1&p_class_id='.$instance['class_id'].'&p_inst_id='.$instance['id'])}}" class="dropdown-item"><i class="icon-content-copy"></i>{{getMessage('info_word_clone')}}</a>
								@if($instance['status']!="O")
								<a href="{{route('editora.action', 'delete_instance?p_pagina=1&p_class_id='.$instance['class_id'].'&p_inst_id='.$instance['id'])}}" class="dropdown-item"><i class="icon-delete"></i>{{getMessage('info_word_delete')}}</a>
								@endif
								@if (config('editora-admin.curl-refresh-command')!='')
								<a onclick="refreshView({{$instance['id']}});" class="dropdown-item"><i class="fa fa-refresh fa-lg" style="margin-left:5px; margin-right: 10px; color: #8A909C;"></i>{{getMessage('clean_cache')}}</a>
								@endif
								@includeIf('editora::extraMenuInstance')
							</div>
						</div>
					</li>
				@endif
			</ul>
			<div class="save-block">
				@if($p_mode=='V')
					<a href="{{route('editora.action', 'edit_instance?p_pagina=1&p_class_id='.$instance['class_id'].'&p_inst_id='.$instance['id'])}}" class="btn clr-secondary"><span class="btn-text">{{getMessage('info_word_edit')}}</span></a>
				@elseif($p_mode=='U' || $p_mode=='I')
					<a onclick="document.getElementById('Form1').submit(); $(this).attr('disabled','disabled');" class="btn clr-secondary"><span class="btn-text">{{getMessage('save')}}</span></a>
				@endif
			</div>
		</div>
	</div>
	@if(false && isset($parents) && !empty($parents))
	<div id="parents-menu">
		<header class="side-menu-header">
			<span class="tit">{{getMessage('container_objetos_padre')}}</span>
			<button class="btn-square clr-gray" id="btn-hide-modifications"><i class="icon-close"></i></button>
		</header>
		<div class="side-menu-content">
			<ul class="last-modifications-list">
			@foreach($parents as $item)
				<li class="last-modifications-list-item">
					<p class="tit"><a href="{{route('editora.action', 'view_instance?p_pagina=1&p_class_id='.$item['class_id'].'&p_inst_id='.$item['id'])}}">{{$item['key_fields']}}</a></p>
					<p class="data">
						<time class="date">{{$item['fecha']}}</time>
					</p>
				</li>
			@endforeach
			</ul>
		</div>
	</div>
	@endif
	<form name="Form1" id="Form1" method="post" @if($p_mode=='U') action="{{route('editora.action', "edit_instance2")}}" @elseif($p_mode=='I') action="{{route('editora.action', "new_instance2")}}" @else action="" @endif>
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
						@if(isset($instance['external_id']) && !empty($instance['external_id']))
							<span style="font-weight: bold; padding-left:50px"> External id: </span>{{$instance['external_id']}}
						@endif
						@if(session('user_type')=='O' && session('rol_id')==1 && isset($instance['batch_id']) && !empty($instance['batch_id']))
							<span style="font-weight: bold; padding-left:25px"> Batch id: </span>{{$instance['batch_id']}}
						@endif
					</span>
					<div class="publish-info">
						@if($instance['publishing_ends']!=null)
							<p class="date">{{getMessage('published_from')}} <time>{{$instance['publishing_begins']}}</time> {{getMessage('published_to')}} <time>{{$instance['publishing_ends']}}</time></p>
						@else
							<p class="date">{{getMessage('published_on')}} <time>{{$instance['publishing_begins']}}</time></p>
						@endif
						<div class="publish-row">
							<p class="status">
								@if($instance['status']=="O")
									{{getMessage('info_word_status_published')}} <span class="status-ball clr-published"></span>
								@elseif($instance['status']=="V")
									{{getMessage('info_word_status_reviewed')}} <span class="status-ball clr-pending"></span>
								@else
									{{getMessage('info_word_status_pending')}} <span class="status-ball clr-unpublished"></span>
								@endif
							</p>
							@if($p_mode=='V')
							<a href="{{route('editora.action', 'edit_instance?p_pagina=1&p_class_id='.$instance['class_id'].'&p_inst_id='.$instance['id'])}}" class="btn-square clr-default"><i class="icon-pencil"></i></a>
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
							<label for="p_status" class="form-label">{{getMessage('info_word_status')}}</label>
							<select class="form-control" name="p_status">
								@php
									if(!isset($instance['status'])){
										$instance['status'] = 'P';
									}
								@endphp
							@if($p_mode=='U' && ( $instance['status'] == "P" && $status_list['status1']==0 || $instance['status'] == "V" && $status_list['status2']==0 || $instance['status'] == "O" && $status_list['status3']==0) )
								@if($instance['status'] == "P")
									<option value="P" @if($instance['status']=="P") selected @endif>{{getMessage('info_word_status_pending')}}</option>
								@endif
								@if($instance['status'] == "V")
									<option value="V" @if($instance['status']=="V") selected @endif>{{getMessage('info_word_status_reviewed')}}</option>
								@endif
								@if($instance['status'] == "O")
									<option value="O" @if($instance['status']=="O") selected @endif>{{getMessage('info_word_status_published')}}</option>
								@endif
							@else
								@if($status_list['status1']==1 || $instance['status'] == "P" ||  $status_list['status1']==0 && $status_list['status2']==0 && $status_list['status3']==0 && $p_mode=='I' )
									<option value="P" @if($instance['status']=="P") selected @endif>{{getMessage('info_word_status_pending')}}</option>
								@endif
								@if($status_list['status2']==1 || $instance['status'] == "V")
									<option value="V" @if($instance['status']=="V") selected @endif>{{getMessage('info_word_status_reviewed')}}</option>
								@endif
								@if($status_list['status3']==1 || $instance['status'] == "O")
									<option value="O" @if($instance['status']=="O") selected @endif>{{getMessage('info_word_status_published')}}</option>
								@endif
							@endif
							</select>
						</div>
						<div class="form-group">
							@php
								$attribute = $instance['instance_tabs'][0]['elsatribs'][0];
								$attribute_name=_attributeName($attribute);
							@endphp
							<label for="{{$attribute_name}}" class="form-label">{{$attribute['caption']}}</label>
                            <input type="text" class="form-control" name="{{$attribute_name}}" value="@isset($attribute['atrib_values'][0]){{$attribute['atrib_values'][0]['text_val']}}@endisset">

                            @if(session('user_type')=='O' && session('rol_id')==1 )
                                <label for="external_id" class="form-label">External id</label>
                                <input type="text" class="form-control" name="external_id" value="@isset($instance['external_id']){{$instance['external_id']}}@endisset">
                            @elseif(isset($instance['external_id']) && !empty($instance['external_id']))
                                <input type="hidden" name="external_id" value="{{$instance['external_id']}}">
                            @endif
						</div>
					</div>
					<div class="form-row date-row">
						<div class="form-group">
							<label for="date_s1" class="form-label">{{getMessage('info_word_publishing_begins')}}</label>
							<span class="input-group">
								<input type="text" length="35" name="p_publishing_begins" value="@isset($instance['publishing_begins']){{$instance['publishing_begins']}}@endisset" id="date1" class="form-control datepicker" autocomplete="off">
								<span class="input-addon">
									<i class="icon-calendar"></i>
								</span>
							</span>
						</div>
						<div class="form-group">
							<label for="date_s1" class="form-label">{{getMessage('info_word_publishing_ends')}}</label>
							<span class="input-group">
								<input type="text" length="35" name="p_publishing_ends" value="@isset($instance['publishing_ends']){{$instance['publishing_ends']}}@endisset" id="date2" class="form-control datepicker" autocomplete="off">
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
				@php
					$count=0;
					$mandatories='';
				@endphp
				@foreach($instance['instance_tabs'] as $tab)
					@php $exist_relations=false; @endphp
					<div id="tab-{{$tab['id']}}" class="tab-pane  @if($count==0)in active @endif">
						@php $count++; @endphp
						<section class="fixed-block open-items" style="padding-top:20px">
							<ul class="block-items-list">
								<li class="block-item expanded">
									<div class="collapse show" id="collapseItem5">
										<div class="container">
										@foreach($tab['elsatribs'] as $attribute)
											<div class="form default-block-form">
												@if($attribute['type']!='R' && $attribute['columna']<1000)
													@php $attribute_name=_attributeName($attribute); @endphp
													@if($attribute['mandatory']=='Y')
														@php $mandatories.=$attribute['id'].','; @endphp
                                                    @endif
                                                    @if(isset($attribute['params']->only_root) && $attribute['params']->only_root==true)
                                                        @if(session('user_type')=='O' && session('rol_id')==1 )
                                                            @includeIf('editora::attributes.'.$attribute['type'])
                                                        @endif
                                                    @else
                                                        @includeIf('editora::attributes.'.$attribute['type'])
                                                    @endif
												@elseif($attribute['type']=='R')
													@php $exist_relations=true; @endphp
												@endif
											</div>
										@endforeach
										</div>
									</div>
								</li>
							</ul>
						</section>

						@if($tab['id']==100)
							@foreach($instance['instance_tabs'] as $tablang)
							@if($tablang['id'] >1000)
								<section class="fixed-block">
									<header class="block-header">
										<div class="container">
											<span class="data">
												<h2 class="tit">{{ $tablang['caption'] }}</h2>
											</span>
										</div>
									</header>
									<ul class="block-items-list">
										<li class="block-item expanded">
											<div class="collapse show" id="collapseItem5">
												<div class="container">
												@foreach($tab['elsatribs'] as $attribute)
													<div class="form default-block-form">
														@if($attribute['type']!='R' && $attribute['columna']==$tablang['id'])
															@php $attribute_name=_attributeName($attribute); @endphp
															@if($attribute['mandatory']=='Y')
																@php $mandatories.=$attribute['id'].','; @endphp
															@endif
															@includeIf('editora::attributes.'.$attribute['type'])
														@endif
													</div>
												@endforeach
												</div>
											</div>
										</li>
									</ul>
								</section>
								@endif
							@endforeach
						@endif

						@if(isset($exist_relations) && $exist_relations == true)
							<section class="fixed-block">
								<header class="block-header">
									<div class="container">
										<span class="data">
											<h2 class="tit">{{getMessage('info_word_relations')}}</h2>
										</span>
									</div>
								</header>
								@foreach($tab['elsatribs'] as $attribute)
									@if($attribute['type']=='R')
										@php $attribute_name=_attributeName($attribute); @endphp
										@includeIf('editora::attributes.R')
									@endif
								@endforeach
							</section>
						@endif
					</div>
				@endforeach
				<input type="hidden" name="p_mandatories" value="{{substr($mandatories,0,strlen($mandatories)-1)}}"/>
				@includeIf('editora::extraTabInstance')
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
			info = 'ajax=refresh_view&inst_id='+inst_id+'&rand='+Math.random();
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