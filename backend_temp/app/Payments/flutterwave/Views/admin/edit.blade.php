
<div class="p-0 rounded-3xl flex flex-col gap-3">

    <div class="form-input">
        <label>
            {{ __('Your secret key') }}
        </label>
        <input type="text" name="settings[payment_flutterwave][secret]" class="-" value="{{ settings('payment_flutterwave.secret') }}">
    </div>
    <div class="form-input">
        <label>
            {{ __('Webhook url') }}
        </label>
        <input type="text" onclick="this.select();" readonly="readonly" value="{{ route('yena-payments-flutterwave-webhook') }}">
    </div>
</div>