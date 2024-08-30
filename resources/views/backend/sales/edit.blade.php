@extends('backend.layouts.app')

@section('content')
<div class="page-content">
    <!-- Titlebar -->
    <div class="aiz-titlebar text-left mt-2 pb-2 px-3 px-md-2rem border-bottom border-gray">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="h3">{{ translate('Edit Order') }}</h1>
            </div>
        </div>
    </div>

    <!-- Edit Order Form -->
    <form action="{{ route('order.update', $order->id) }}" method="POST" enctype="multipart/form-data" id="choice_form">
        @csrf
        <input type="hidden" name="_method" value="GET">
        <input type="hidden" name="id" value="{{ $order->id }}">

        <div class="tab-content">
            <!-- Shipping Information -->
            <br>
            <div class="shipping-info">
                <h5 class="mb-3 pb-3 fs-17 fw-700 border-bottom" style="border-color: #e4e5eb;">
                    {{ translate('Shipping Information') }}
                </h5>

                <div class="w-100">
                    <div class="row">
                        <!-- Shipping Cost Input -->
                        <div class="col-xxl-7 col-xl-6">
                            <div class="form-group row">
                                <label class="col-xxl-3 col-form-label fs-13">
                                    {{ translate('Shipping Cost') }} <span class="text-danger">*</span>
                                </label>
                                <div class="col-xxl-9">
                                    <input type="number" step="0.01" class="form-control @error('shipping_cost') is-invalid @enderror" name="shipping_cost" placeholder="{{ translate('Enter Shipping Cost') }}" value="{{ old('shipping_cost', $order->shipping_cost ?? '') }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

            <!-- Update Button -->
                    <div class="mt-4 text-right">
                        <button type="submit"  name="button" class="mx-2 btn btn-success w-230px btn-md rounded-2 fs-14 fw-700 shadow-success action-btn">{{ translate('Update') }}</button>
                    </div>

        
    </form>
</div>

@endsection

@section('modal')
    <!-- Delete modal -->
    @include('modals.delete_modal')
    <!-- Bulk Delete modal -->
    @include('modals.bulk_delete_modal')
@endsection

@section('script')
    <script type="text/javascript">
        $(document).on("change", ".check-all", function() {
            if (this.checked) {
                // Iterate each checkbox
                $('.check-one:checkbox').each(function() {
                    this.checked = true;
                });
            } else {
                $('.check-one:checkbox').each(function() {
                    this.checked = false;
                });
            }

        });
        
        function bulk_delete() {
            var data = new FormData($('#sort_orders')[0]);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{ route('bulk-order-delete') }}",
                type: 'POST',
                data: data,
                cache: false,
                contentType: false,
                processData: false,
                success: function(response) {
                    if (response == 1) {
                        location.reload();
                    }
                }
            });
        }
        
        function order_bulk_export (){
            var url = '{{route('order-bulk-export')}}';
            $("#sort_orders").attr("action", url);
            $('#sort_orders').submit();
            $("#sort_orders").attr("action", '');
        }
    </script>
@endsection
