<div class="flex flex-col gap-3">
    <div class="rounded-3xl">
        <div class="form-input mt-0">
            <label>
                {{ __('Your public key') }}
            </label>
            <input type="text" name="settings[payment_paystack][public]" class="" value="{{ settings('payment_paystack.public') }}">
        </div>
    </div>
    <div class="rounded-3xl">
        <div class="form-input mt-0">
            <label>
                {{ __('Your secret key') }}
            </label>
            <input type="text" name="settings[payment_paystack][secret]" class="" value="{{ settings('payment_paystack.secret') }}">
        </div>
    </div>
    <div class="rounded-3xl">
        <div class="form-input mt-0">
            <label>
                {{ __('Webhook url') }}
            </label>
            <input type="text" onclick="this.select();" readonly="readonly" value="{{ route('yena-payments-paystack-webhook') }}">
        </div>
    </div>
    <div class="flex items-center gap-2 !hidden">
        <div class="bg-[var(--yena-colors-gray-200)] px-2 py-1 text-xs rounded-md">charge.success</div>
    </div>
</div>