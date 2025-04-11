<form class="form-horizontal" action="{{ route('payment_method.update') }}" method="POST">
    @csrf
    <input type="hidden" name="payment_method" value="ziina">
    <div class="form-group row">
        <input type="hidden" name="types[]" value="ZIINA_API_KEY">
        <div class="col-md-4">
            <label class="col-from-label" for="ZIINA_API_KEY">{{ translate('Ziina api key') }}</label>
        </div>
        <div class="col-md-8">
            <input type="text" class="form-control" name="ZIINA_API_KEY" id="ZIINA_API_KEY"
                value="{{ env('ZIINA_API_KEY') }}" placeholder="{{ translate('Ziina api key') }}" required />
        </div>
    </div>
    <div class="form-group row">
        <input type="hidden" name="types[]" value="ZIINA_MODE">
        <div class="col-md-4">
            <label class="col-from-label" for="ZIINA_MODE">{{ translate('Enable Test Mode') }}</label>
        </div>
        <div class="col-md-8 d-flex justify-content-start align-items-center">
            <input type="hidden" name="ZIINA_MODE" id="ZIINA_MODE" value="{{ env('ZIINA_MODE') }}">
            <label class="aiz-switch aiz-switch-success mb-0 float-right">
                <input type="checkbox" onchange="updatePaymentSettings(this, 'ZIINA_MODE',0)"
                    @if (env('ZIINA_MODE')) checked @endif>
                <span class="slider round"></span>
            </label>
        </div>
    </div>
    <div class="form-group mb-0 text-right">
        <button type="submit" class="btn btn-sm btn-primary">{{ translate('Save') }}</button>
    </div>
</form>
