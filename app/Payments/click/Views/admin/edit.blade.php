
<div class="flex flex-col gap-3">
    <div class="form-input">
        <label>
            {{ __('Merchant id') }}
        </label>
        <input type="text" name="settings[payment_click][merchant_id]" class="" value="{{ settings('payment_click.merchant_id') }}">
    </div>

    <div class="form-input">
        <label>
            {{ __('Secret key') }}
        </label>
        <input type="text" name="settings[payment_click][secret]" class="" value="{{ settings('payment_click.secret') }}">
    </div>
    <div class="form-input">
        <label>
            {{ __('Service id') }}
        </label>
        <input type="text" name="settings[payment_click][service_id]" class="" value="{{ settings('payment_click.service_id') }}">
    </div>
    <div class="form-input">
        <label>
            {{ __('Merchant user id') }}
        </label>
        <input type="text" name="settings[payment_click][merchant_user_id]" class="" value="{{ settings('payment_click.merchant_user_id') }}">
    </div>
</div>