<div class="p-3">
    <div class="row">
        <div class="col-md-2 mt-md-2">
            <label>{{ translate('Name')}}</label>
        </div>
        <div class="col-md-10">
            <input class="form-control mb-3 rounded-0" placeholder="{{ translate('Your Name')}}" rows="2" name="name" required></input>
        </div>
    </div>

    <div class="row">
        <div class="col-md-2 mt-md-2">
            <label>{{ translate('Email')}}</label>
        </div>
        <div class="col-md-10">
            <input type="email" class="form-control mb-3 rounded-0" placeholder="{{ translate('Your Email')}}" name="email" value="" required>
        </div>
    </div>

    <!-- Address -->
    <div class="row">
        <div class="col-md-2 mt-md-2">
            <label>{{ translate('Address')}}</label>
        </div>
        <div class="col-md-10">
            <textarea class="form-control mb-3 rounded-0" placeholder="{{ translate('Your Address')}}" rows="2" name="address" required></textarea>
        </div>
    </div>

    <!-- Country -->
    <div class="row">
        <div class="col-md-2 mt-md-2">
            <label>{{ translate('Country')}}</label>
        </div>
        <div class="col-md-10">
            <div class="mb-3">
                <select class="form-control aiz-selectpicker rounded-0" data-live-search="true" data-placeholder="{{ translate('Select your country') }}" name="country_id" required>
                    <option value="">{{ translate('Select your country') }}</option>
                    @foreach (get_active_countries() as $key => $country)
                        <option value="{{ $country->id }}">{{ $country->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <!-- State -->
    <div class="row">
        <div class="col-md-2 mt-md-2">
            <label>{{ translate('State')}}</label>
        </div>
        <div class="col-md-10">
            <select class="form-control mb-3 aiz-selectpicker rounded-0" data-live-search="true" name="state_id" required>

            </select>
        </div>
    </div>

    <!-- City -->
    <div class="row">
        <div class="col-md-2 mt-md-2">
            <label>{{ translate('City')}}</label>
        </div>
        <div class="col-md-10">
            <select class="form-control mb-3 aiz-selectpicker rounded-0" data-live-search="true" name="city_id" required>

            </select>
        </div>
    </div>

    @if (get_setting('google_map') == 1)
        <!-- Google Map -->
        <div class="row mt-3 mb-3">
            <input id="searchInput" class="controls" type="text" placeholder="{{translate('Enter a location')}}">
            <div id="map"></div>
            <ul id="geoData">
                <li style="display: none;">Full Address: <span id="location"></span></li>
                <li style="display: none;">Postal Code: <span id="postal_code"></span></li>
                <li style="display: none;">Country: <span id="country"></span></li>
                <li style="display: none;">Latitude: <span id="lat"></span></li>
                <li style="display: none;">Longitude: <span id="lon"></span></li>
            </ul>
        </div>
        <!-- Longitude -->
        <div class="row">
            <div class="col-md-2" id="">
                <label for="exampleInputuname">{{ translate('Longitude')}}</label>
            </div>
            <div class="col-md-10" id="">
                <input type="text" class="form-control mb-3 rounded-0" id="longitude" name="longitude" readonly="">
            </div>
        </div>
        <!-- Latitude -->
        <div class="row">
            <div class="col-md-2" id="">
                <label for="exampleInputuname">{{ translate('Latitude')}}</label>
            </div>
            <div class="col-md-10" id="">
                <input type="text" class="form-control mb-3 rounded-0" id="latitude" name="latitude" readonly="">
            </div>
        </div>
    @endif

    <!-- Postal code -->
    <div class="row">
        <div class="col-md-2 mt-md-2">
            <label>{{ translate('Postal code')}}</label>
        </div>
        <div class="col-md-10">
            <input type="text" class="form-control mb-3 rounded-0" placeholder="{{ translate('Your Postal Code')}}" name="postal_code" value="" required>
        </div>
    </div>

    <!-- Phone -->
    <div class="row">
        <div class="col-md-2 mt-md-2">
            <label>{{ translate('Phone')}}</label>
        </div>
        <div class="col-md-10">
            <input type="tel" id="phone-code" class="form-control rounded-0" placeholder="" name="phone" autocomplete="off">
            <input type="hidden" name="country_code" value="">
        </div>
    </div>
</div>

<div class="px-3 pt-3 pb-4 row">
    <div class="col-md-2 mt-md-2"></div>
    <div class="col-md-10">
        <div class="bg-soft-info p-2">
            {{ translate('If you have already used the same mail address or phone number before, please ') }}
            <a href="javascript:void(0);" data-toggle="modal" data-target="#login_modal" class="fw-700 animate-underline-primary">{{ translate('Login') }}</a>
            {{ translate(' first to continue') }}
        </div>
    </div>
</div>

@section('script')
    <script type="text/javascript">

        function submitShippingInfoForm(el) {
            var email = $("input[name='email']").val();
            var phone = $("input[name='country_code']").val()+$("input[name='phone']").val();
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{route('guest_customer_info_check')}}",
                type: 'POST',
                data: {
                    email : email,
                    phone : phone
                },
                success: function (response) {
                    if(response ==  1){
                        $('#login_modal').modal();
                        AIZ.plugins.notify('warning', '{{ translate('You already have an account with this information. Please Login first.') }}');
                    }
                    else{
                        $('#shipping_info_form').submit();
                    }
                }
            });
        }

        $(document).on('change', '[name=country_id]', function() {
            var country_id = $(this).val();
            get_states(country_id);
        });

        $(document).on('change', '[name=state_id]', function() {
            var state_id = $(this).val();
            get_city(state_id);
        });
        
        function get_states(country_id) {
            $('[name="state"]').html("");
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{route('get-state')}}",
                type: 'POST',
                data: {
                    country_id  : country_id
                },
                success: function (response) {
                    var obj = JSON.parse(response);
                    if(obj != '') {
                        $('[name="state_id"]').html(obj);
                        AIZ.plugins.bootstrapSelect('refresh');
                    }
                }
            });
        }

        function get_city(state_id) {
            $('[name="city"]').html("");
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{route('get-city')}}",
                type: 'POST',
                data: {
                    state_id: state_id
                },
                success: function (response) {
                    var obj = JSON.parse(response);
                    if(obj != '') {
                        $('[name="city_id"]').html(obj);
                        AIZ.plugins.bootstrapSelect('refresh');
                    }
                }
            });
        }

    </script>

    
    @if (get_setting('google_map') == 1)
        @include('frontend.'.get_setting('homepage_select').'.partials.google_map')
    @endif

    
@endsection