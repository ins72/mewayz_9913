<div class="banner-winn relative" :class="{
	'pattern-image --in-img': site.settings.__pattern_theme !== 'default'
}">
	<div class="banner-style-creative-winn">
            
        <div :class="site.settings.align ? `-align-${site.settings.align}` : '-align-center'">
            <div class="yetti-cover-wrapper" :class="{
				'opacity-0': !site.settings.enable_cover
			}">
                <div class="yetti-photo">
					<template x-if="site.banner">
						<img :src="$store.builder.getMedia(site.banner)" class="h-full object-cover">
					</template>
                </div>
            </div>
			<div class="profile-wrapper">

                <div class="picture-wrapper pattern-image relative">
                    <div class="picture  -- --in-img" :class="{
						'!rounded-none': site.settings.corners == 'straight',
						'!rounded-3xl': site.settings.corners == 'round',
						'!rounded-full': site.settings.corners == 'rounded',
						'no-border-radius': site.settings.__pattern_theme !== 'default',
						[`--${site.settings.__pattern_theme}`]: site.settings.__pattern_theme !== 'default'
					}">
					<template x-if="!site.logo">
						<div class="w-[100%] h-full p-3 !block bg-[var(--c-mix-1)] ">
							{!! __i('--ie', 'image-picture', 'text-gray-300') !!}
						</div>
					</template>
					<template x-if="site.logo">
						<img :src="$store.builder.getMedia(site.logo)" :style="{
							'height': site.settings.avatar_size + 'px',
							'width': site.settings.avatar_size + 'px',
						}" alt="">
					</template>
                    </div>
                </div>
				<div class="name-bio-wrapper">
					<div class="name" :class="{
						'!text-[60px]': site.settings.header_fontsize == 's',
						'!text-[70px]': site.settings.header_fontsize == 'm',
						'!text-[80px]': site.settings.header_fontsize == 'l',
					}">
						<div class="content inline-flex items-center gap-1" x-text="site.name"></div>
					</div>
					
					<template x-if="site.location">
						<p class="bio-username-text flex items-center justify-center gap-1 !text-white --location" :class="{
							'justify-start': site.settings.align == 'left',
							'justify-center': site.settings.align == 'center' || !site.settings.align,
							'justify-end': site.settings.align == 'right',

							'!text-[11px]': site.settings.header_fontsize == 's',
							'!text-[14px]': site.settings.header_fontsize == 'm',
							'!text-[16px]': site.settings.header_fontsize == 'l',
						}">
							{!! __i('Maps, Navigation', 'map-pin-location-circle', 'w-4 h-4') !!}
							<span x-text="site.location"></span>
						</p>
					</template>

                    <div class="bio" :class="{
						'!text-[11px]': site.settings.header_fontsize == 's',
						'!text-[14px]': site.settings.header_fontsize == 'm',
						'!text-[16px]': site.settings.header_fontsize == 'l',
					}">
						<div class="content" x-text="site.bio"></div>
                    </div>
				</div>
			</div>
		</div>
	</div>
	
</div>