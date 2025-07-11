<div>
    <div class="names-input">
        <template x-if="section.form.first_name_enable">
            <input name="firstname" type="text" class="shape" :placeholder="section.form.first_name">
        </template>
        <template x-if="section.form.last_name_enable">
            <input name="lastname" type="text" class="shape" :placeholder="section.form.last_name">
        </template>
    </div>
    
    <input name="email" type="text" class="shape" :placeholder="section.form.email">
    
    <template x-if="section.form.phone_enable">
        <input name="phone" type="number" class="shape" :placeholder="section.form.phone">
    </template>
    <template x-if="section.form.message_enable">
        <textarea name="message" class="shape mt-[10px]" :placeholder="section.form.message"></textarea>
    </template>
    
    <button class="site-btn t-1 shape mt-2" x-text="section.form.button_name"></button>
    <div class="screen"></div>
</div>
