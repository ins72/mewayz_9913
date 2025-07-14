
<div class="rounded-3xl flex flex-col gap-3">
    <div class="form-input">
        <label>
            {{ __('Your key id') }}
        </label>
        <input type="text" name="settings[payment_razor][client]" class="" value="{{ settings('payment_razor.client') }}">
    </div>

    <div class="form-input">
        <label>
            {{ __('Your secret key') }}
        </label>
        <input type="text" name="settings[payment_razor][secret]" class="" value="{{ settings('payment_razor.secret') }}">
    </div>
    <div class="form-input">
        <label>
            {{ __('Your webhook secret') }}
        </label>
        <input type="text" name="settings[payment_razor][webhook_secret]" class="" value="{{ settings('payment_razor.webhook_secret') }}">
    </div>
    <div class="form-input">
        <label>
            {{ __('Webhook url') }}
        </label>
        <input type="text" onclick="this.select();" readonly="readonly" value="{{ route('yena-payments-razor-webhook') }}">
    </div>
    <div class="flex items-center gap-2">
        <div class="bg-[var(--yena-colors-gray-200)] px-2 py-1 text-xs rounded-md">payment_link.paid</div>
        <div class="bg-[var(--yena-colors-gray-200)] px-2 py-1 text-xs rounded-md">subscription.charged</div>
    </div>
</div>