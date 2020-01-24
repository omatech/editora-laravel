@if(file_exists(public_path().'/vendor/editora/extras/classes_sample/'.$class['class_internal_name'].'.jpg'))

    <div class="modal fade bd-example-modal-lg show" id="imagemodal" role="dialog" style="">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">

                    {{getMessage('classes_sample_modal')}}
                    @if(isset($class) && isset($class['class_name']))
                        {{$class['class_name']}}
                    @endif

                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body" id="dynamic-content">
                    <img src="" id="imagepreview" class="img-fluid" alt=""/>
                </div>
            </div>
        </div>
    </div>
@endif


@section('scripts')
    @parent
    <script type="text/javascript">
        @if(file_exists(public_path().'/vendor/editora/extras/classes_sample/'.$class['class_internal_name'].'.jpg'))
		$("#showClassSample").on("click", function() {
            var route_img = "{{ url('/vendor/editora/extras/classes_sample/'.$class['class_internal_name'].'.jpg') }}";

            $('#imagepreview').attr('src', route_img );
            $('#imagemodal').modal('show');
        });
        @endif

    </script>
@endsection