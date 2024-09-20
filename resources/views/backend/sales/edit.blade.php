@extends('backend.layouts.app')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap">

@section('content')
<div class="page-content">
    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <!-- Titlebar -->
    <div class="aiz-titlebar text-left mt-3 pb-3 px-3 px-md-4 border-bottom border-gray">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="h3 font-weight-bold">{{ translate('Edit Order') }}</h1>
            </div>
        </div>
    </div>
    @if (!$order->shipping_cost_status)
        <form action="{{ route('order.update', $order->id) }}" method="POST" enctype="multipart/form-data" id="choice_form" class="mt-4 p-4 bg-white rounded shadow-sm">    
            @csrf   
            @method('GET')  
            <input type="hidden" name="_method" value="GET">    
            <input type="hidden" name="id" value="{{ $order->id }}">    

            <div class="products-info py-4">
                <h5 class="mb-4 fs-17 fw-700 border-bottom pb-3" style="border-color: #dee2e6;">
                    <i class="fas fa-box"></i> {{ translate('Products Information') }}
                </h5>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="bg-light">
                            <tr>
                                <th>{{ translate('Product ID') }}</th>
                                <th>{{ translate('Product Name') }}</th>
                                <th>{{ translate('Quantity') }}</th>
                                <th>{{ translate('Price') }}</th>
                                <th>{{ translate('Total') }}</th>
                                <th>{{ translate('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->orderDetails as $orderDetail)
                                <tr>
                                    <td>{{ $orderDetail->product->id }}</td>
                                    <td>{{ $orderDetail->product->name }}</td>
                                    <td>
                                        <input type="number" class="form-control" name="quantities[{{ $orderDetail->id }}]" value="{{ $orderDetail->quantity }}" min="1">
                                    </td>
                                    <td>{{ $orderDetail->price }}</td>
                                    <td>{{ $orderDetail->price * $orderDetail->quantity}}</td>
                                    <td>
                                        <button type="button" class="btn btn-danger btn-sm" onclick="showDeleteForm({{ $orderDetail->id }})">
                                            <i class="fas fa-trash-alt"></i> {{ translate('Remove') }}
                                        </button>                                   
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 text-right">
                    <button type="submit" name="update_products" class="btn btn-primary w-230px btn-md rounded-pill fs-15 fw-700 shadow-primary action-btn">
                        <i class="fas fa-edit"></i> {{ translate('Update Products') }}
                    </button>
                </div>
            </div>
        </form>
    @endif

    <form action="{{ route('shipping.cost.order.update', $order->id) }}" method="POST" enctype="multipart/form-data" id="choice_form" class="mt-4 p-4 bg-white rounded shadow-sm">
        @csrf
        @method('GET')
        <div class="shipping-info py-4">
            <h5 class="mb-4 fs-17 fw-700 border-bottom pb-3" style="border-color: #dee2e6;">
                <i class="fas fa-shipping-fast"></i> {{ translate('Shipping Information') }}
            </h5>

            <div class="form-group row align-items-center">
                <label class="col-xxl-3 col-form-label fs-14">
                    {{ translate('Shipping Cost') }} <span class="text-danger">*</span>
                </label>
                <div class="col-xxl-9">
                    <input type="number" step="0.01" class="form-control @error('shipping_cost') is-invalid @enderror" name="shipping_cost" placeholder="{{ translate('Enter Shipping Cost') }}" value="{{ old('shipping_cost', $order->shipping_cost ?? '0.0') }}">
                    @error('shipping_cost')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mt-4 text-right">
                <button type="submit" name="update_shipping" class="btn btn-success w-230px btn-md rounded-pill fs-15 fw-700 shadow-success action-btn">
                    <i class="fas fa-edit"></i> {{ translate('Update Shipping') }}
                </button>
            </div>
        </div>
    </form>

    <div class="invoice-summary py-4 mt-5">
        <table class="table table-bordered table-striped">
            <tbody>
                <tr>
                    <td class="text-left"><i class="fas fa-shipping-fast"></i> <strong>{{ translate('Shipping Cost') }}</strong></td>
                    <td class="text-right">{{ number_format($order->shipping_cost, 2) }} {{ translate('USD') }}</td>
                </tr>
                <tr>
                    <td class="text-left"><i class="fas fa-receipt"></i> <strong>{{ translate('Total Invoice Amount') }}</strong></td>
                    <td class="text-right">{{ number_format($order->total, 2) }} {{ translate('USD') }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    @foreach($order->orderDetails as $orderDetail)
        <form id="delete-form-{{ $orderDetail->id }}" action="{{ route('orders.removeProduct', ['orderId' => $order->id, 'orderDetailId' => $orderDetail->id ]) }}" method="POST" style="display: none;">
            @csrf
            @method('DELETE')
        </form>
    @endforeach
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
    function showDeleteForm(orderDetailId) {
        if (confirm('{{ translate('Are you sure you want to remove this product from the order?') }}')) {
            document.getElementById('delete-form-' + orderDetailId).submit();
        }
    }
</script>
    
@endsection

<style>
    body {
        font-family: 'Poppins', sans-serif;
        background-color: #f8f9fa;
    }

    .page-content {
        padding: 20px;
    }

    h1, h5 {
        color: #333;
    }

    .action-btn {
        transition: all 0.3s ease;
        border: none;
        border-radius: 50px;
    }

    .action-btn:hover {
        opacity: 0.9;
        transform: scale(1.05);
    }

    .btn-primary {
        background-color: #4CAF50;
        border-color: #4CAF50;
    }

    .btn-primary:hover {
        background-color: #3e8e41;
    }

    .btn-success {
        background-color: #FF5733;
        border-color: #FF5733;
    }

    .btn-success:hover {
        background-color: #c0392b;
    }

    .table-bordered {
        border: 1px solid #ddd;
        border-radius: 10px;
        overflow: hidden;
    }

    .table-hover tbody tr:hover {
        background-color: #f9f9f9;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .form-control {
        border: 1px solid #ccc;
        border-radius: 10px;
    }

    .form-control:focus {
        box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        border-color: #007bff;
    }

    .invoice-summary {
        background-color: #fff;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }

    .form-group {
        margin-bottom: 20px;
    }

    h5 {
        margin-bottom: 30px;
    }

    .table-responsive {
        margin-top: 20px;
    }
</style>
