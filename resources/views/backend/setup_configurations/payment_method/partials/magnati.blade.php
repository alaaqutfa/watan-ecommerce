<form class="form-horizontal" action="{{ route('payment_method.update') }}" method="POST">
    @csrf
    <input type="hidden" name="payment_method" value="stripe">
    <div class="form-group row">
        <input type="hidden" name="types[]" value="Magnati_KEY">
        <div class="col-md-4">
            <label class="col-from-label">{{ translate('Magnati Key') }}</label>
        </div>
        <div class="col-md-8">
            <input type="text" class="form-control" name="Magnati_KEY"
                value="{{ env('Magnati_KEY') }}" placeholder="{{ translate('Magnati KEY') }}"
                required>
        </div>
    </div>
    <div class="form-group row">
        <input type="hidden" name="types[]" value="Magnati_SECRET">
        <div class="col-md-4">
            <label class="col-from-label">{{ translate('Magnati Secret') }}</label>
        </div>
        <div class="col-md-8">
            <input type="text" class="form-control" name="Magnati_SECRET"
                value="{{ env('Magnati_SECRET') }}" placeholder="{{ translate('Magnati SECRET') }}"
                required>
        </div>
    </div>
    <div class="form-group mb-0 text-right">
        <button type="submit" class="btn btn-sm btn-primary">{{ translate('Save') }}</button>
    </div>
</form>
