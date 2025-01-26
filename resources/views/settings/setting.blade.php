@include('component.header')
@include('component.sidebar')

@php
    $packageId = \App\Helpers\ManagementHelper::activePackageId();
@endphp
<!-- main content -->
<div class="main-panel">
    <div class="content">
        <div class="page-inner">
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="card">
                        <div class="card-body" style="padding: 15px 15px 33px 15px;">
                            <!-- card-body -->

                            <ul class="nav my_nave nav-tabs" id="myTab2">
                                <li class="active"><a data-toggle="tab" href="#branch">شعبه</a></li>
                                <li><a data-toggle="tab" data-id="0" href="#warehouse">گدام</a></li>
                            </ul>

                            <div class="tab-content">
                                <!-- branch -->
                                 <div id="branch" class="tab-pane fade in active">
                                        <br>
                                        @include('settings.branch.add')
                                        <br>
                                        @include('settings.branch.list')
                                    </div> 
                                <!-- / branch -->

                                <!-- warehouse -->
                                <div id="warehouse" class="tab-pane fade">
                                    <br>
                                    @if(auth()->user()->hasAccess('settings', 'create_records'))
                                        @if($packageId >= 2)
                                            @include('settings.warehouse.add')
                                        @endif
                                    @endif
                                    <br>
                                    @include('settings.warehouse.list')
                                </div>
                                <!-- / warehouse -->

                              
                            </div>
                        </div>
                        <!-- / card-body -->
                    </div>
                </div>
                <!-- / row -->
            </div>
        </div>
    </div>

    <!-- footer -->
    @include('component.footer-text')
    <!-- / footer -->
</div>
<!-- / main content -->

<script>
$(document).ready(function() {
    // When the submit button is clicked
    $('#submitBtn').on('click', function() {
        var name = $('#name').val();
        var nameError = $('#nameError');
        var loading = $('#loading');
        
        // Reset error messages
        nameError.text('');
        
        // Show loading spinner
        loading.show();
        
        // Send AJAX request
        $.ajax({
            url: "{{ route('addData') }}",
            type: 'POST',
            data: {
                name: name,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                // Hide loading spinner
                loading.hide();
                
                // Show success message
                alert(response.message);
                
                // Close the modal
                $('#addModal').modal('hide');
                
                // Optionally, you can reset the form
                $('#branchForm')[0].reset();
            },
            error: function(xhr) {
                // Hide loading spinner
                loading.hide();
                
                // Handle validation errors
                var errors = xhr.responseJSON.errors;
                if (errors.name) {
                    nameError.text(errors.name[0]);
                }
            }
        });
    });
});
</script>


@include('component.footer')