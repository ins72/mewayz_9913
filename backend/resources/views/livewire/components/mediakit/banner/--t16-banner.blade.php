<div class="banner-t16 mb-5" :class="{
	'pattern-image --in-img': site.settings.__pattern_theme !== 'default'
}">
	<div class="-banner-cover" :class="{
		'opacity-0': !site.settings.enable_cover
	}">
		<template x-if="site.banner">
			<img :src="$store.builder.getMedia(site.banner)">
		</template>
	</div>

	<div class="-banner-overlay" :class="{
		'opacity-0': !site.settings.enable_cover
	}"></div>
	<div class="-name">
		@for ($i = 0; $i < 5; $i++)
			<div class="--name-inner" :class="{
				'-no-banner': !site.settings.enable_cover
			}">
				<div class="--p inline-flex items-center gap-1">
					<span x-text="site.name"></span>
				</div>
				<div class="--deco">⭑</div>
			</div>
		@endfor
	</div>

	<div class="-thumb-avatar">
		<div class="relative">
			
			<template x-if="!site.logo">
				<div class="w-[140px] h-[140px] p-6 !block bg-[var(--c-mix-1)] ">
					{!! __i('--ie', 'image-picture', 'text-gray-300') !!}
				</div>
			</template>
			<template x-if="site.logo">
				<img :src="$store.builder.getMedia(site.logo)" :class="{
					[`--${site.settings.__pattern_theme}`]: site.settings.__pattern_theme !== 'default'
				}" alt="">
			</template>
		</div>
		<div class="--bio" :class="{
			'-no-banner': !site.settings.enable_cover
		}" x-text="site.bio"></div>
	</div>
</div>