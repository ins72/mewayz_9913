
<div class="flex flex-col gap-3">
    <div class="form-input">
        <label class="initial">
            {{ __('Mode') }}
        </label>
        <select name="settings[payment_paypal][mode]" class="">
            <option value="live" {{ settings('payment_paypal.mode') == 'live' ? 'selected' : '' }}>{{ __('Live') }}</option>
            <option value="sandbox" {{ settings('payment_paypal.mode') == 'sandbox' ? 'selected' : '' }}>{{ __('SandBox') }}</option>
        </select>
    </div>

    <div class="form-input">
        <label>
            {{ __('Your Client Id') }}
        </label>
        <input type="text" name="settings[payment_paypal][client]" class="" value="{{ settings('payment_paypal.client') }}">
    </div>

    <div class="form-input">
        <label>
            {{ __('Your secret key') }}
        </label>
        <input type="text" name="settings[payment_paypal][secret]" class="" value="{{ settings('payment_paypal.secret') }}">
    </div>
    <div class="form-input">
        <label>
            {{ __('Webhook url') }}
        </label>
        <input type="text" onclick="this.select();" readonly="readonly" value="{{ route('yena-payments-paypal-webhook') }}">
    </div>
    <div class="flex items-center gap-2">
        <div class="bg-[var(--yena-colors-gray-200)] px-2 py-1 text-xs rounded-md">CHECKOUT.ORDER.APPROVED</div>
        <div class="bg-[var(--yena-colors-gray-200)] px-2 py-1 text-xs rounded-md">PAYMENT.SALE.COMPLETED</div>
    </div>
</div>