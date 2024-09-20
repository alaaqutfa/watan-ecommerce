@extends('frontend.layouts.user_panel')

@section('panel_content')
    <div class="card shadow-none rounded-0 border">
        <div class="card-header border-bottom-0">
            <h5 class="mb-0 fs-20 fw-700 text-dark">{{ translate('Purchase History') }}</h5>
        </div>
        <div class="card-body">
            <table class="table aiz-table mb-0">
                <thead class="text-gray fs-12">
                    <tr>
                        <th class="pl-0">{{ translate('Code') }}</th>
                        <th data-breakpoints="md">{{ translate('Date') }}</th>
                        <th>{{ translate('Amount') }}</th>
                        <th data-breakpoints="md">{{ translate('Delivery Status') }}</th>
                        <th data-breakpoints="md">{{ translate('Payment Status') }}</th>

                        @if(env('MANUAL_PAYMENT'))
                            <th data-breakpoints="md">{{ translate('Shipping Cost Status') }}</th>
                        @endif

                        <th class="text-center pr-0">{{ translate('Options') }}</th>
                    </tr>
                </thead>
                <tbody class="fs-14">
                    @foreach ($orders as $key => $order)
                        @if (count($order->orderDetails) > 0)
                            <tr>
                                <!-- Code -->
                                <td class="pl-0">
                                    <a
                                        href="{{ route('purchase_history.details', encrypt($order->id)) }}">{{ $order->code }}</a>
                                </td>
                                <!-- Date -->
                                <td class="text-secondary">{{ date('d-m-Y', $order->date) }}</td>
                                <!-- Amount -->
                                <td class="fw-700">
                                    {{ single_price($order->grand_total) }}
                                </td>
                                <!-- Delivery Status -->
                                <td class="fw-700">
                                    {{ translate(ucfirst(str_replace('_', ' ', $order->delivery_status))) }}
                                    @if ($order->delivery_viewed == 0)
                                        <span class="ml-2" style="color:green"><strong>*</strong></span>
                                    @endif
                                </td>
                                <!-- Payment Status -->
                                <td>
                                    @if ($order->payment_status == 'paid')
                                        <span class="badge badge-inline badge-success p-3 fs-12"
                                            style="border-radius: 25px; min-width: 80px !important;">{{ translate('Paid') }}</span>
                                    @else
                                        <span class="badge badge-inline badge-danger p-3 fs-12"
                                            style="border-radius: 25px; min-width: 80px !important;">{{ translate('Unpaid') }}</span>
                                    @endif
                                    @if ($order->payment_status_viewed == 0)
                                        <span class="ml-2" style="color:green"><strong>*</strong></span>
                                    @endif
                                </td>
                                
                                @if(env('MANUAL_PAYMENT'))
                                    <!-- Shipping Cost Status -->
                                    <td>
                                        @if ($order->shipping_cost_status)
                                            <span class="badge badge-inline badge-success p-3 fs-12" style="border-radius: 25px; min-width: 80px !important;">{{translate('Modified')}}</span>
                                        @else
                                            <span class="badge badge-inline badge-danger p-3 fs-12"
                                                style="border-radius: 25px; min-width: 80px !important;">{{ translate('Not Modified') }}</span>
                                        @endif
                                        @if ($order->payment_status_viewed == 0)
                                            <span class="ml-2" style="color:green"><strong>*</strong></span>
                                        @endif
                                    </td>
                                @endif
                                <!-- Options -->
                                <td class="text-right pr-0">
                                    <div class="d-flex justify-content-around align-items-center" style="gap: 0.5rem;">
                                        <!-- Details -->
                                        <a href="{{ route('purchase_history.details', encrypt($order->id)) }}"
                                            class="d-flex justify-content-center align-items-center btn btn-soft-info btn-icon btn-circle btn-sm hov-svg-white mt-2 mt-sm-0"
                                            title="{{ translate('Order Details') }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="10"
                                                viewBox="0 0 12 10">
                                                <g id="Group_24807" data-name="Group 24807"
                                                    transform="translate(-1339 -422)">
                                                    <rect id="Rectangle_18658" data-name="Rectangle 18658" width="12"
                                                        height="1" transform="translate(1339 422)" fill="#3490f3" />
                                                    <rect id="Rectangle_18659" data-name="Rectangle 18659" width="12"
                                                        height="1" transform="translate(1339 425)" fill="#3490f3" />
                                                    <rect id="Rectangle_18660" data-name="Rectangle 18660" width="12"
                                                        height="1" transform="translate(1339 428)" fill="#3490f3" />
                                                    <rect id="Rectangle_18661" data-name="Rectangle 18661" width="12"
                                                        height="1" transform="translate(1339 431)" fill="#3490f3" />
                                                </g>
                                            </svg>
                                        </a>
                                        <!-- Make Payment -->
                                        @if ($order->payment_status == 'unpaid')
                                            @if ($order->shipping_cost_status || env('MANUAL_PAYMENT'))
                                                <a href="javascript:void(0)" title="{{ translate('Make Payment') }}">
                                                    <form action="{{ route('order.re_payment') }}" method="post">
                                                        @csrf
                                                        <input type="hidden" name="order_id" value="{{ $order->id }}" />
                                                        <input type="hidden" name="payment_option" value="{{ $order->payment_type }}" />
                                                        <button type="submit" class="d-flex align-items-center btn btn-soft-secondary-base btn-icon btn-circle btn-sm hov-svg-white mt-2 mt-sm-0">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                                viewBox="0 0 24 24" fill="none">
                                                                <path fill="green"
                                                                    d="M13.5061 0.1875L13.3301 2.40234H11.2656V9.77869H11.0352C9.65275 9.77869 8.39809 10.5876 7.82762 11.8468L2.40625 23.8125H9.78906L10.2644 22.2289C13.6997 21.1823 14.6023 17.9062 14.6023 17.9062H19.3867V16.2075L20.3738 16.286L21.6016 0.830719L13.5061 0.1875ZM18.6484 15.6193C17.9212 15.8159 17.3643 16.419 17.235 17.168H13.0381C12.7608 18.5858 11.7119 19.8758 10.2983 20.4533C10.2527 20.4719 10.2053 20.4807 10.1588 20.4807C10.0133 20.4807 9.87541 20.3941 9.81691 20.2511C9.73984 20.0624 9.83031 19.847 10.019 19.7698C11.4048 19.2038 12.3727 17.8302 12.3727 16.4297C12.3727 16.429 12.3729 16.0605 12.3729 16.0605H16.1259C16.6835 16.0605 17.1617 15.6242 17.1716 15.0667C17.1817 14.4989 16.7249 14.0358 16.1594 14.0348L9.51194 14.0303C9.30813 14.0303 9.14275 13.865 9.14275 13.6611C9.14275 13.4572 9.30803 13.2919 9.51194 13.2919H12.0038V4.61588C12.7537 4.46287 13.3433 3.88116 13.5132 3.14062H17.1682C17.3361 3.87469 17.9156 4.44759 18.6484 4.6095V15.6193ZM19.8189 13.9477C19.6682 13.9759 19.5237 14.0223 19.3867 14.0832V2.40234H14.3244C14.9672 2.23284 15.4864 1.74244 15.6881 1.10147L19.3316 1.39097C19.4409 2.136 19.973 2.75297 20.6908 2.97234L19.8189 13.9477ZM17.9285 10.0691C17.9312 8.65566 16.8025 7.46625 15.3324 7.46353C13.9189 7.46091 12.7293 8.64609 12.7268 10.0597C12.7242 11.4731 13.8528 12.6625 15.3229 12.6652C16.7364 12.6679 17.9259 11.4827 17.9285 10.0691ZM13.8007 10.2878L13.3484 10.287L13.3493 9.77813L13.7451 9.77888C13.7455 9.55266 13.8591 9.27019 13.916 9.04416L14.4812 9.21488C14.4244 9.32784 14.3674 9.55387 14.3668 9.89316C14.3663 10.1758 14.479 10.3457 14.6487 10.3461C14.8184 10.3464 14.9317 10.1769 15.1021 9.7815C15.3292 9.21647 15.5559 8.93419 16.0082 8.93513C16.404 8.99241 16.7425 9.33225 16.8549 9.78478L17.3073 9.78562L17.3063 10.2945L16.9105 10.2938C16.91 10.52 16.7961 10.972 16.7393 11.1415L16.1742 10.9708C16.2876 10.8014 16.4012 10.4624 16.4018 10.1797C16.4024 9.84037 16.2897 9.67059 16.0635 9.67022C15.8939 9.66984 15.7805 9.78281 15.6102 10.1783C15.4397 10.6868 15.1562 11.0821 14.7039 11.0812C14.2516 11.0802 13.9128 10.7968 13.8007 10.2878Z" />
                                                            </svg>
                                                        </button>
                                                    </form>
                                                </a>
                                            @endif
                                        @endif
                                        <!-- Re-order -->
                                        <a class="d-flex justify-content-center align-items-center btn btn-soft-white btn-icon btn-circle btn-sm mt-2 mt-sm-0"
                                            href="{{ route('re_order', encrypt($order->id)) }}"
                                            title="{{ translate('Reorder') }}">
                                            <svg width="9.202" height="12" viewBox="0 0 14 16" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M1 8.00005C0.999486 6.62988 1.43507 5.29784 2.23917 4.21066C3.04326 3.12348 4.17088 2.34195 5.44705 1.98736C6.72321 1.63277 8.07655 1.72496 9.29704 2.2496C10.5175 2.77425 11.5369 3.70202 12.197 4.88893"
                                                    stroke="#ffffff" stroke-width="1.5" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                                <path d="M8.5 4.8889H12.2498V1" stroke="#ffffff" stroke-width="1.5"
                                                    stroke-linecap="round" stroke-linejoin="round" />
                                                <path
                                                    d="M12.9977 8C12.9983 9.37017 12.5627 10.7022 11.7586 11.7894C10.9545 12.8766 9.82687 13.6581 8.5507 14.0127C7.27453 14.3673 5.9212 14.2751 4.70071 13.7504C3.48023 13.2258 2.46085 12.298 1.80078 11.1111"
                                                    stroke="#ffffff" stroke-width="1.5" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                                <path d="M5.49982 11.1111H1.75V15" stroke="#ffffff" stroke-width="1.5"
                                                    stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                        </a>
                                        <!-- Invoice -->
                                        <a class="d-flex justify-content-center align-items-center btn btn-soft-secondary-base btn-icon btn-circle btn-sm hov-svg-white mt-2 mt-sm-0"
                                            href="{{ route('invoice.download', $order->id) }}"
                                            title="{{ translate('Download Invoice') }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12.001"
                                                viewBox="0 0 12 12.001">
                                                <g id="Group_24807" data-name="Group 24807"
                                                    transform="translate(-1341 -424.999)">
                                                    <path id="Union_17" data-name="Union 17"
                                                        d="M13936.389,851.5l.707-.707,2.355,2.355V846h1v7.1l2.306-2.306.707.707-3.538,3.538Z"
                                                        transform="translate(-12592.95 -421)" fill="#f3af3d" />
                                                    <rect id="Rectangle_18661" data-name="Rectangle 18661" width="12"
                                                        height="1" transform="translate(1341 436)" fill="#f3af3d" />
                                                </g>
                                            </svg>
                                        </a>
                                        <!-- Cancel -->
                                        @if ($order->delivery_status == 'pending' && $order->payment_status == 'unpaid')
                                            <a href="javascript:void(0)"
                                                class="d-flex justify-content-center align-items-center btn btn-soft-danger btn-icon btn-circle btn-sm hov-svg-white mt-2 mt-sm-0 confirm-delete"
                                                data-href="{{ route('purchase_history.destroy', $order->id) }}"
                                                title="{{ translate('Cancel') }}">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="9.202" height="12"
                                                    viewBox="0 0 9.202 12">
                                                    <path id="Path_28714" data-name="Path 28714"
                                                        d="M15.041,7.608l-.193,5.85a1.927,1.927,0,0,1-1.933,1.864H9.243A1.927,1.927,0,0,1,7.31,13.46L7.117,7.608a.483.483,0,0,1,.966-.032l.193,5.851a.966.966,0,0,0,.966.929h3.672a.966.966,0,0,0,.966-.931l.193-5.849a.483.483,0,1,1,.966.032Zm.639-1.947a.483.483,0,0,1-.483.483H6.961a.483.483,0,1,1,0-.966h1.5a.617.617,0,0,0,.615-.555,1.445,1.445,0,0,1,1.442-1.3h1.126a1.445,1.445,0,0,1,1.442,1.3.617.617,0,0,0,.615.555h1.5a.483.483,0,0,1,.483.483ZM9.913,5.178h2.333a1.6,1.6,0,0,1-.123-.456.483.483,0,0,0-.48-.435H10.516a.483.483,0,0,0-.48.435,1.6,1.6,0,0,1-.124.456ZM10.4,12.5V8.385a.483.483,0,0,0-.966,0V12.5a.483.483,0,1,0,.966,0Zm2.326,0V8.385a.483.483,0,0,0-.966,0V12.5a.483.483,0,1,0,.966,0Z"
                                                        transform="translate(-6.478 -3.322)" fill="#d43533" />
                                                </svg>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
            <!-- Pagination -->
            <div class="aiz-pagination mt-2">
                {{ $orders->links() }}
            </div>
        </div>
    </div>

@endsection

@section('modal')
    <!-- Delete modal -->
    @include('modals.delete_modal')
@endsection
