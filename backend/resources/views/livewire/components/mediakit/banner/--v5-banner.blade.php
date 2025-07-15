<div class="banner-v5 relative overflow-hidden" :class="{
	'pattern-image --in-img': site.settings.__pattern_theme !== 'default'
}">

	<div class="overlay-awesome overflow-hidden">
		<div class="absolute pointer-events-none select-none top-0 right-0 left-0 bottom-0">
			<img src="{{ gs('assets/image/others/oQ65CY9Qk1W3v2c4ykiPXXB3og.png') }}" alt="">
		</div>
	</div>
	<style>
		.builder-body{

		}

		.builder-body::before{
			content: initial !important;
		}
		.page-stories-section{
			display: none !important;
		}
	</style>
	
	<div class="banner-style-v5">
            
		<div class="-align--{{-- ao($_o->page->settings, 'align') --}} --v5-content px-5 md:px-10">
			<div class="profile-wrapper">
				<div class="">
					<div class="picture-wrapper pattern-image base-picture-o is-story relative">
						<div class="picture -- --in-img" :style="{
							'height': site.settings.avatar_size + 'px',
							'width': site.settings.avatar_size + 'px',
						}" :class="{
							'!rounded-none': site.settings.corners == 'straight',
							'!rounded-xl': site.settings.corners == 'round',
							'!rounded-full': site.settings.corners == 'rounded',
							[`--${site.settings.__pattern_theme}`]: site.settings.__pattern_theme !== 'default',
							'!border-0': site.settings.__pattern_theme !== 'default'
						}">
						<template x-if="!site.logo">
							<div class="w-[100%] h-full p-6 !block bg-[var(--c-mix-1)] ">
								{!! __i('--ie', 'image-picture', 'text-gray-300') !!}
							</div>
						</template>
						<template x-if="site.logo">
							<img :src="$store.builder.getMedia(site.logo)" alt="">
						</template>
						</div>
					</div>
				</div>
				<div class="name-bio-wrapper">
					<div class="name theme-text-color">
						<div class="content inline-flex items-center gap-1 lg:pb-2 pt-1" x-text="site.name"></div>
					</div>

					<template x-if="site.location">
						<p class="--tagline flex items-center gap-1 --location">
							{!! __i('Maps, Navigation', 'map-pin-location-circle', 'w-4 h-4') !!}
							<span x-text="site.location"></span>
						</p>
					</template>
				</div>
			</div>
			
			<div class="name-bio-wrapper">
				<div class="bio">
					<div class="content" x-text="site.bio"></div>
				</div>
			</div>
		</div>
	</div>
</div>