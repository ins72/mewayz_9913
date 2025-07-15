<div class="banner-t20 mb-5" :class="`-align-` + site.settings.align">
	<div class="--name inline-flex items-center gap-1 text-black">
		<span x-text="site.name"></span>
	</div>
	<div class="--bio text-black">
		@for ($i = 0; $i < 5; $i++)
			<div class="--bio-inner" x-text="site.bio"></div>
		@endfor
	</div>
	

	<div class="-banner-cover" :class="{
		'pattern-image --in-img': site.settings.__pattern_theme !== 'default'
	}">
		<div class="--cover-item relative">

			<template x-if="!site.logo">
				<div class="w-[380px] max-w-[100%] h-[25rem] p-3 !block bg-[var(--c-mix-1)] ">
					{!! __i('--ie', 'image-picture', 'text-gray-300') !!}
				</div>
			</template>
			<template x-if="site.logo">
				<img :src="$store.builder.getMedia(site.logo)" :class="{
					[`--${site.settings.__pattern_theme}`]: site.settings.__pattern_theme !== 'default'
				}" alt="">
			</template>
			<div>
				{{-- {!! $_o->verified('avatar', 'h-8 w-8 absolute -bottom-2 -right-2 z-50') !!} --}}
			</div>
		</div>
	</div>
</div>