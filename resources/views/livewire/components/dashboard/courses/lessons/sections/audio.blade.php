<div>
    
    <div class="form-input w-full !bg-transparent">
        <label>{{ __('Link') }}</label>
        <input type="text" name="data[link]" x-model="item.lesson_data.link">
        <p class="mt-2 text-xs">{!! __t('Make sure it\'s a direct url to the audio or it wont work. Your audo url should play like <a href="https://interactive-examples.mdn.mozilla.net/media/cc0-audio/t-rex-roar.mp3" class="!underline !text-blue-400" target="_blank">this</a> in your browser if visited. If not, it cant be used.') !!}</p>
    </div>
</div>