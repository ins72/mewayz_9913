
<div class="flex flex-col gap-3">
    <div class="form-input">
        <label>
            {{ __('Your public key') }}
        </label>
        <input type="text" name="settings[payment_stripe][client]" class="" value="{{ settings('payment_stripe.client') }}">
    </div>

    <div class="form-input">
        <label>
            {{ __('Your secret key') }}
        </label>
        <input type="text" name="settings[payment_stripe][secret]" class="" value="{{ settings('payment_stripe.secret') }}">
    </div>
    <div class="form-input">
        <label>
            {{ __('Your webhook secret') }}
        </label>
        <input type="text" name="settings[payment_stripe][webhook_secret]" class="" value="{{ settings('payment_stripe.webhook_secret') }}">
    </div>
    <div class="form-input">
        <label>
            {{ __('Webhook url') }}
        </label>
        <input type="text" onclick="this.select();" readonly="readonly" value="{{ route('yena-payments-stripe-webhook') }}">
    </div>
    <div class="flex items-center gap-2">
        <div class="bg-[var(--yena-colors-gray-200)] px-2 py-1 text-xs rounded-md">invoice.paid</div>
        <div class="bg-[var(--yena-colors-gray-200)] px-2 py-1 text-xs rounded-md">checkout.session.completed</div>
    </div>
</div>