<div class="box-border banner-avatar">
    <div class="screen"></div>
    <div class="avatar-details">
        <div class="avatar-image" :class="{
            'circle': post.settings.shape == 'circle'
        }">
            <div class="default-image"><svg width="24" height="24"
                    viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" data-v-29a39510="">
                    <path 
                        d="M2 2V22H22V2H2ZM17 5.3C17.9 5.3 18.7 6 18.7 7C18.7 7.9 18 8.7 17 8.7C16.1 8.7 15.3 8 15.3 7C15.3 6.1 16.1 5.3 17 5.3ZM5 16.2L9.9 9.2L14.1 14.8L16.2 12.7L19 16.2H5Z"
                        fill="var(--c-mix-10)" data-v-29a39510=""></path>
                </svg></div>
        </div>
        <div class="avatar-description vertical-align">
            <p class="t-0 author-name">Jeff Joladd</p>
            <p class="pre-line timestamp t-0">1 min </p>
        </div>
    </div>
    <div class="share" x-init="console.log(post)" :class="{
        'circle': post.settings.shape == 'circle'
    }">
        <div class="social-media-share-container"><button class="social-media-share mobile-share"
                ><svg class="copy-svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    xmlns="http://www.w3.org/2000/svg" >
                    <path fill-rule="evenodd" clip-rule="evenodd"
                        d="M16.0444 2H2V16.0444H7.95555V22H22V7.95556H16.0444V2ZM13.8222 7.95556H7.95555V13.8222H4.22222V4.22222H13.8222V7.95556Z"
                        fill="var(--foreground)" ></path>
                </svg><svg fill="none" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" >
                    <path
                        d="m17 1.25c-1.933 0-3.5 1.567-3.5 3.5 0 0.39227 0.0645 0.76947 0.1836 1.1216l-5.6989 3.6634c-0.63413-0.63917-1.5132-1.035-2.4847-1.035-1.933 0-3.5 1.567-3.5 3.5s1.567 3.5 3.5 3.5c0.97149 0 1.8505-0.3958 2.4847-1.035l5.6989 3.6635c-0.1191 0.352-0.1836 0.7292-0.1836 1.1215 0 1.933 1.567 3.5 3.5 3.5s3.5-1.567 3.5-3.5-1.567-3.5-3.5-3.5c-0.8146 0-1.5642 0.2783-2.1589 0.745l-5.9102-3.7993c0.04534-0.2249 0.06914-0.4575 0.06914-0.6957s-0.02379-0.4708-0.06914-0.6957l5.9102-3.7993c0.5947 0.46668 1.3443 0.74496 2.1589 0.74496 1.933 0 3.5-1.567 3.5-3.5s-1.567-3.5-3.5-3.5z"
                        fill="var(--foreground)" ></path>
                </svg></button></div>
        <!--v-if-->
    </div>
    <div class="share-dropdown display-none" id="share-dropdown">
        <div class="screen display-none" id="backdrop"></div>
        <ul >
            <li id="copy-link-text-for-post" data-url="https://blank367.vzy.io/zzuntitled-post"><a
                    href="#">Copy link</a></li>
            <li ><a target="_blank"
                    href="https://www.facebook.com/sharer/sharer.php?u=https://blank367.vzy.io/zzuntitled-post">
                    Facebook </a></li>
            <li ><a target="_blank"
                    href="https://twitter.com/intent/tweet?url=https://blank367.vzy.io/zzuntitled-post&amp;text="> X
                </a></li>
            <li ><a target="_blank"
                    href="https://api.whatsapp.com/send?text=https://blank367.vzy.io/zzuntitled-post">Whatsapp</a></li>
            <!-- https://www.linkedin.com/sharing/share-offsite/?url= -->
            <li ><a target="_blank"
                    href="https://www.linkedin.com/shareArticle?mini=true&amp;url=https://blank367.vzy.io/zzuntitled-post">Linkedin</a>
            </li>
            <li ><a 
                    href="mailto:?subject=Shared from blank367.vzy.io: zzUntitled Post&amp;body=Use this subtitle to grab your readers' attention and give them a preview of your post content. A great subtitle should be simple and engaging, adding to your post title.. Read more: https://blank367.vzy.io/zzuntitled-post">Send
                    Email</a></li>
        </ul>
    </div>
</div>
