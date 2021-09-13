@if(file_exists(public_path().'/vendor/editora/extras/classes_sample/'.$class['class_internal_name'].'.jpg') && isset($instance))
    <div class="modal modal-related fade" id="imagemodal" tabindex="-1" role="dialog" aria-labelledby="ModalLabel{{$instance['id']}}" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        {{getMessage('classes_sample_modal')}}
                        @if(isset($class) && isset($class['class_name']))
                            {{$class['class_name']}}
                        @endif
                    </h5>
                    <button type="button" class="btn-ico" data-dismiss="modal" aria-label="Close">
                        <i class="icon-close"></i>
                    </button>
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
        @if(file_exists(public_path().'/vendor/editora/extras/classes_sample/'.$class['class_internal_name'].'.jpg') && isset($instance))
		$("#showClassSample").on("click", function() {
            var route_img = "{{ url('/vendor/editora/extras/classes_sample/'.$class['class_internal_name'].'.jpg') }}";

            $('#imagepreview').attr('src', route_img );
            $('#imagemodal').modal('show');
        });
        @endif
    </script>
@endsection
