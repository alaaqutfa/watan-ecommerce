<form class="form-horizontal" action="{{ route('payment_method.update') }}" method="POST">
    @csrf
    <input type="hidden" name="payment_method" value="stripe">
    <div class="form-group row">
        <input type="hidden" name="types[]" value="Magnati_KEY">
        <div class="col-md-4">
            <label class="col-from-label">{{ translate('Magnati Shared Secret') }}</label>
        </div>
        <div class="col-md-8">
            <input type="text" class="form-control" name="MAGNATI_SHARED_SECRET"
                value="{{ env('MAGNATI_SHARED_SECRET') }}" placeholder="{{ translate('Magnati SHARED SECRET') }}"
                required>
        </div>
    </div>
    <div class="form-group row">
        <input type="hidden" name="types[]" value="MAGNATI_STORE_NAME">
        <div class="col-md-4">
            <label class="col-from-label">{{ translate('Magnati Store Name') }}</label>
        </div>
        <div class="col-md-8">
            <input type="text" class="form-control" name="MAGNATI_STORE_NAME"
                value="{{ env('MAGNATI_STORE_NAME') }}" placeholder="{{ translate('Magnati Store Name') }}"
                required>
        </div>
    </div>
    <div class="form-group mb-0 text-right">
        <button type="submit" class="btn btn-sm btn-primary">{{ translate('Save') }}</button>
    </div>
</form>
