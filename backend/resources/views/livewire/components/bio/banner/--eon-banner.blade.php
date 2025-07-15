<div class="banner-eon relative mb-5">
	<div class="banner-style-creative-winn" :class="{
		'pattern-image --in-img': site.settings.__pattern_theme !== 'default'
	}">
            
		<div class="-align-">
            <div class="yetti-cover-wrapper" :class="{
				'opacity-0': !site.settings.enable_cover
			}">
                <div class="yetti-photo">
					<template x-if="site.banner">
						<img :src="$store.builder.getMedia(site.banner)" class="h-full !w-full object-cover">
					</template>
                </div>
            </div>
			<div class="profile-wrapper">

                <div class="picture-wrapper pattern-image relative">
                    <div class="picture  -- --in-img" :class="{
						'!rounded-none': site.settings.corners == 'straight',
						'!rounded-xl': site.settings.corners == 'round',
						'!rounded-3xl': site.settings.corners == 'rounded',
						'no-border-radius': site.settings.__pattern_theme !== 'default',
						[`--${site.settings.__pattern_theme}`]: site.settings.__pattern_theme !== 'default'
					}">
						<img :src="$store.builder.getMedia(site.logo)" alt="">
                    </div>
                </div>
				<div class="name-bio-wrapper">
					<div class="-name name">
						@for ($i = 0; $i < 5; $i++)
							<div class="--name-inner" :class="{
								'-no-banner': !site.settings.enable_cover
							}">
								<div class="--p inline-flex items-center gap-1">
									<span x-text="site.name"></span>
								</div>
							</div>
						@endfor
					</div>
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