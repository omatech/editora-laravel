@extends('editora::base')

@section('body')
    <main id="main">
        <section class="table-view-header">
            <div class="container">
			<span class="data">
				<h3 class="tit">{{getMessage('results_for')}}: "{{$term}}"</h3>
			</span>
                <form action="{{ route('editora.action', 'search') }}">
                    <div class="form-row">
                        <div class="form-group" style="margin-bottom:0; margin-right:5px; min-width:140px;">
                            <select class="form-control" name="p_search_state">
                                <option value="">{{ getMessage('info_word_status') }}</option>
                                <option value="O" @if($status === 'O') selected @endif>
                                    {{getMessage('info_word_status_published')}}
                                </option>
                                <option value="P" @if($status === 'P') selected @endif>
                                    {{getMessage('info_word_status_pending')}}
                                </option>
                                <option value="V" @if($status === 'V') selected @endif>
                                    {{getMessage('info_word_status_reviewed')}}
                                </option>
                            </select>
                        </div>
                        <div class="form-group" style="margin-bottom:0;">
                            <span class="input-group">
                                @if(isset($class['id']))
                                    <input type="hidden" id="p_class_id" name="p_class_id" value="{{$class['id']}}">
                                @elseif(isset($class_id))
                                    <input type="hidden" id="p_class_id" name="p_class_id" value="{{$class_id}}">
                                @endif
                                <input type="text" class="form-control" id="p_search_query" name="p_search_query"
                                       placeholder="Buscar" value="{{ $term ?? null }}">
                                <button type="submit" class="input-addon">
                                    <i class="icon-magnify"></i>
                                </button>
                            </span>
                        </div>
                    </div>
                </form>
            </div>
        </section>
        <section class="table-view">
            <div class="container">
                @include('editora::templates.instances_list')
            </div>
        </section>
    </main>
@endsection

@section('scripts')
    @parent
    <script type="text/javascript" src="{{ asset('/vendor/editora/js/jquery.selectric.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            $('select').selectric();
        });
    </script>
@endsection