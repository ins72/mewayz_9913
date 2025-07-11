<div>
    
    <div class="form-input w-full !bg-transparent">
        <label>{{ __('Link') }}</label>
        <input type="text" name="data[embed]" x-model="item.lesson_data.embed">
        <p class="mt-2 text-xs">{!! __t('This link will be shown in an iframe. Not all url are supported.') !!}</p>
    </div>
</div>