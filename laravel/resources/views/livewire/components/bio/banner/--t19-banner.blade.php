<div class="banner-t19 mb-5" :class="{
	'pattern-image --in-img': site.settings.__pattern_theme !== 'default'
}">
	
	<div class="flex flex-col items-center" :class="{
		'items-center': site.settings.align == 'center' || !site.settings.align,
		'items-start': site.settings.align == 'left',
		'items-end': site.settings.align == 'right',
		'-align-left':  site.settings.align == 'left',
		'-align-right':  site.settings.align == 'right',
	}">
		<div class="relative">
		
			<template x-if="!site.logo">
				<div class="w-[100%] h-full p-3 !block bg-[var(--c-mix-1)] ">
					{!! __i('--ie', 'image-picture', 'text-gray-300') !!}
				</div>
			</template>
			<template x-if="site.logo">
				<img class="--avatar" :class="{
					'!rounded-none': site.settings.corners == 'straight',
					'!rounded-xl': site.settings.corners == 'round',
					'!rounded-full': site.settings.corners == 'rounded',
					'no-border-radius': site.settings.__pattern_theme !== 'default',
					[`--${site.settings.__pattern_theme}`]: site.settings.__pattern_theme !== 'default'
				}" :src="$store.builder.getMedia(site.logo)" :style="{
							'height': site.settings.avatar_size + 'px',
							'width': site.settings.avatar_size + 'px',
						}">
			</template>
		</div>
	
		<div class="--name inline-flex items-center gap-1 text-black" :class="{
			'!text-[15px]': site.settings.header_fontsize == 's',
			'!text-[18px]': site.settings.header_fontsize == 'm',
			'!text-[21px]': site.settings.header_fontsize == 'l',
		}">
			<span x-text="site.name"></span>
		</div>
		<div class="--bio text-black" :class="{
			'!text-[11px]': site.settings.header_fontsize == 's',
			'!text-[14px]': site.settings.header_fontsize == 'm',
			'!text-[16px]': site.settings.header_fontsize == 'l',
		}" x-text="site.bio"></div>
	</div>


	<div class="-banner-cover">
		@for ($i = 0; $i < 3; $i++)
			<div class="--cover-item" :class="{
				'-no-banner': !site.settings.enable_cover
			}">
				<div class="--cover-padding-conatiner">
					<div class="--padding"></div>
				</div>
				<template x-if="site.banner">
					<img :src="$store.builder.getMedia(site.banner)">
				</template>
			</div>
		@endfor
	</div>
</div>