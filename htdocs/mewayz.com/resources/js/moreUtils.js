import { gsap } from "gsap";
import { Observer } from "gsap/Observer";
import { ScrollTrigger } from "gsap/ScrollTrigger";

const select = (e) => document.querySelector(e);
const selectAll = (e) => document.querySelectorAll(e);
const selectFrom = (e, el) => el.querySelector(e);
const selectAllFrom = (e, el) => el.querySelectorAll(e);
const getBounding = (el) => el.getBoundingClientRect();
const useEventListener = (el, event, callback) => {
    el.addEventListener(event, callback);
    return function () {
        el.removeEventListener(event, callback);
    };
};
function toggleScroll(val) {
    if (val) {
        let scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        let scrollLeft = window.pageXOffset || document.documentElement.scrollLeft;
        // if any scroll is attempted, set this to the previous value
        window.onscroll = function () {
            window.scrollTo(scrollLeft, scrollTop);
        };
        window.ontouchmove = (e) => {
            e.preventDefault;
        };
    } else {
        window.onscroll = null;
        window.ontouchmove = null;
    }
}

function setupScrollIndicator() {
    const indicatorInnerEl = select(".scroll-indicator__inner");
    if (indicatorInnerEl || true) {
        const container = select("body .container");
        window.addEventListener("scroll", () => {
            indicatorInnerEl.style.width =
                (window.scrollY / (container.scrollHeight - window.innerHeight)) * 100 + "%";
        });
    }
}

// setupScrollIndicator();

function setupGallery() {
    const allGalleries = [...selectAll(".gallery-box")];
    if (!allGalleries.length) {
        return;
    }

    gsap.registerPlugin(Observer);

    class Gallery {
        constructor(galleryEl) {
            this.galleryEl = galleryEl;
            this.galleryItemsContainer = selectFrom(".gallery-container__items", galleryEl);
            this.autoScroll = [...this.galleryItemsContainer.classList].includes("auto-scroll");
            this.horizontalScroll = [...galleryEl.classList].includes("horizontal-scroll");
            this.galleryItems = [...selectAllFrom(".gallery-container__item", this.galleryItemsContainer)];
            this.galleryViewer = galleryEl.nextElementSibling;
            this.galleryViewerItems = [...selectAllFrom(".gallery-viewer__image", this.galleryViewer)];
            this.closeViewerBtn = selectFrom(".top__close-btn", this.galleryViewer);
            this.galleryViewerControllers = selectFrom(".gallery-viewer__controllers", this.galleryViewer);
            this.galleryViewerControllerRight = selectFrom(
                ".gallery-viewer__controllers .controllers__right",
                this.galleryViewer
            );
            this.galleryViewerControllerLeft = selectFrom(
                ".gallery-viewer__controllers .controllers__left",
                this.galleryViewer
            );
            this.galleryViewerControllerBottom = selectFrom(
                ".gallery-viewer__controllers .controllers__bottom",
                this.galleryViewer
            );
            this.galleryBottom = selectFrom(".gallery-viewer__bottom", this.galleryViewer);
            this.galleryThumbnails = [...selectAllFrom(".bottom__thumbnail", this.galleryBottom)];
            this.galleryLength = this.galleryViewerItems.length;
            this.activeThumbnail = null;
            this.currentIndex = 0;
            this.scrolling = false;
            this.tlTemp = null;
            this.keyListener = null;
            this.indexerEl = selectFrom(".top__indexer", this.galleryViewer);
            this.stripEl = selectFrom(".counter__strip", this.indexerEl);
            this.indexerFiller = selectFrom(".counter__filler", this.indexerEl);
            this.realIndexTemp = null;
        }

        setCurrentIndex(index) {
            index = Number(index);
            if (index < 0) {
                index = 0;
            } else if (index >= this.galleryLength) {
                index = this.galleryLength - 1;
            }

            this.currentIndex = index;
        }

        setViewerScroll(behavior = "smooth", scrollThumb = true) {
            this.galleryViewer.scroll({
                left: this.currentIndex * window.innerWidth,
                top: 0,
                behavior,
            });
            this.setThumbnailActive(scrollThumb);
            this.indexerFiller.textContent = this.currentIndex + 1;
            this.indexerEl.style.setProperty("--strip-y", `-${this.currentIndex * this.indexerEl.clientHeight}px`);
        }

        setupHorizontalScroll() {
            if (!this.horizontalScroll || true) {
                return;
            }
            gsap.registerPlugin(ScrollTrigger);
            const scrollTl = gsap.timeline({
                scrollTrigger: {
                    trigger: this.galleryEl,
                    scrub: true,
                    pin: true,
                    start: "top 70%",
                },
            });
            scrollTl.to(this.galleryItems, {
                xPercent: -200,
                duration: 6,
            });
        }

        setThumbnailActive(scrollThumb = true) {
            this.activeThumbnail && this.activeThumbnail.classList.remove("active");
            this.activeThumbnail = this.galleryThumbnails[this.random ? this.realIndexTemp : this.currentIndex];
            this.activeThumbnail.classList.add("active");
            scrollThumb &&
                this.galleryBottom.scroll({
                    left: this.currentIndex * 150 - window.innerWidth / 2,
                    top: 0,
                    behavior: "smooth",
                });
        }

        openGallery(index = 0) {
            if (this.random) {
                this.realIndexTemp = index;
                index = this.randomizedIndexes[index];
            }

            if (this.autoScroll) {
                const halfItems = this.galleryItems.length / 2;
                if (index > halfItems) {
                    index = index - halfItems;
                }
            }

            let $bioHeader = document.querySelector('.builder-layout-root-main-content');
            if($bioHeader){
                $bioHeader.classList.add('!hidden');
            }

            this.setCurrentIndex(index);
            this.setViewerScroll("instant");
            this.galleryViewer.style.visibility = "visible";
            this.galleryViewer.style.opacity = 1;
            this.galleryItems.forEach((i) => {
                i.style.visibility = "hidden";
                i.style.opacity = 0;
            });
            toggleScroll(true);
            this.keyListener = useEventListener(
                window,
                "keydown",
                function (e) {
                    switch (e.keyCode) {
                        case 37:
                            toggleScroll();
                            this.goToImage(this.currentIndex - 1);
                            break;
                        case 38:
                            toggleScroll();
                            this.goToImage(this.currentIndex - 1);
                            break;
                        case 39:
                            toggleScroll();
                            this.goToImage(this.currentIndex + 1);
                            break;
                        case 40:
                            toggleScroll();
                            this.goToImage(this.currentIndex + 1);
                            break;
                        case 27:
                            this.closeGallery();
                            break;
                    }
                }.bind(this)
            );
        }

        closeGallery() {
            // this.useTlTemp().getTl().reverse();
            this.galleryViewer.style.visibility = "hidden";
            this.galleryViewer.style.opacity = 0;
            this.galleryItems.forEach((i) => {
                i.style.visibility = "visible";
                i.style.opacity = 1;
            });
            toggleScroll(false);
            this.keyListener?.();
            this.keyListener = null;
            let $bioHeader = document.querySelector('.builder-layout-root-main-content');
            if($bioHeader){
                $bioHeader.classList.remove('!hidden');
            }
        }

        goToImage(index = 0, fromThumb = false) {
            if (this.random && !fromThumb) {
                this.realIndexTemp = this.randomizedIndexes.findIndex((value) => Number(value) === index);
                if (this.realIndexTemp == -1) return;
            }
            this.setCurrentIndex(index);
            this.setViewerScroll("smooth", !fromThumb);
        }

        showGalleryBottom() {
            this.galleryBottom.style.transform = "translateY(0)";
            this.galleryViewer.style.setProperty("--image-template-rows", "100px 1fr 200px");
        }

        hideGalleryBottom() {
            this.galleryBottom.style.transform = "translateY(100%)";
            this.galleryViewer.style.setProperty("--image-template-rows", "100px 1fr 100px");
        }

        toggleScrolling() {
            this.scrolling = true;
            setTimeout(
                function () {
                    this.scrolling = false;
                }.bind(this),
                1000
            );
        }

        startObserver() {
            this.observer = Observer.create({
                target: this.galleryViewerControllers,
                type: "wheel,touch",
                onUp: () => {
                    if (!this.scrolling) {
                        this.toggleScrolling();
                        this.goToImage(this.currentIndex - 1);
                    }
                },
                onDown: () => {
                    if (!this.scrolling) {
                        this.toggleScrolling();
                        this.goToImage(this.currentIndex + 1);
                    }
                },
                onRight: () => {
                    if (!this.scrolling) {
                        this.toggleScrolling();
                        this.goToImage(this.currentIndex + 1);
                    }
                },
                onLeft: () => {
                    if (!this.scrolling) {
                        this.toggleScrolling();
                        this.goToImage(this.currentIndex - 1);
                    }
                },
            });
        }

        setupRandom() {
            this.random = [...this.galleryItemsContainer.classList].includes("random");
            if (this.random) {
                const arrayOfIndexes = Object.keys(this.galleryItems);
                this.randomizedIndexes = arrayOfIndexes;
                // shuffleArray
                for (let i = arrayOfIndexes.length - 1; i > 0; i--) {
                    const j = Math.floor(Math.random() * (i + 1));
                    [arrayOfIndexes[i], arrayOfIndexes[j]] = [arrayOfIndexes[j], arrayOfIndexes[i]];
                }

                arrayOfIndexes.forEach((orderNum, i) => {
                    this.galleryItems[i].style.setProperty("--data-order", Number(orderNum));
                    this.galleryThumbnails[i].style.setProperty("--data-order", Number(orderNum));
                    this.galleryViewerItems[i].style.setProperty("--data-order", Number(orderNum));
                });
            }
        }

        init() {
            this.galleryItems.forEach((item, i) => {
                item.addEventListener(
                    "click",
                    function (e) {
                        this.openGallery(i);
                    }.bind(this)
                );
            });
            this.galleryThumbnails.forEach((item, i) => {
                item.addEventListener(
                    "click",
                    function () {
                        if (this.random) {
                            this.realIndexTemp = i;
                            let index = this.randomizedIndexes[i];
                            this.goToImage(index, true);
                        } else {
                            this.goToImage(i, true);
                        }
                    }.bind(this)
                );
            });
            this.closeViewerBtn.addEventListener("click", this.closeGallery.bind(this));
            this.galleryViewerControllerLeft.addEventListener(
                "click",
                function () {
                    this.goToImage(this.currentIndex - 1);
                }.bind(this)
            );
            this.galleryViewerControllerRight.addEventListener(
                "click",
                function () {
                    this.goToImage(this.currentIndex + 1);
                }.bind(this)
            );
            this.galleryViewerControllerBottom.addEventListener(
                "mouseenter",
                function () {
                    this.showGalleryBottom();
                }.bind(this)
            );
            this.galleryBottom.addEventListener(
                "mouseleave",
                function () {
                    this.hideGalleryBottom();
                }.bind(this)
            );
            window.onresize = function () {
                this.setViewerScroll("instant");
            }.bind(this);
            this.startObserver();
            this.setupHorizontalScroll();
            this.setupRandom();
        }
    }

    allGalleries.forEach((gallery) => {
        const galleryFunctionality = new Gallery(gallery);
        galleryFunctionality.init();
    });
}

setTimeout(() => {
    setupGallery();
}, 1000);
window.addEventListener("setupGalleryo", () => {
    setTimeout(() => {
        setupGallery();
    }, 1000);
});

function setupCarouselArrows() {
    const allCarouselsHeaders = [...selectAll(".w-boxed:not(.carousel-container) .card-header.carousel-mode")];

    if (allCarouselsHeaders) {
        allCarouselsHeaders.forEach((ch) => {
            // cc => card-container
            // ch => card-header
            const cc = ch.nextElementSibling;
            const leftButton = selectFrom(".carousel-scroller__left", ch);
            const rightButton = selectFrom(".carousel-scroller__right", ch);

            const allCards = [...selectAllFrom(".card", cc)];
            const firstCard = allCards[0];

            const singleCardWidth = () =>
                firstCard.clientWidth + Number(window.getComputedStyle(firstCard).marginRight.replace("px", ""));

            function scrollContainer(direction) {
                const currentFirstCard = Math.round(cc.scrollLeft / singleCardWidth());
                if (direction === "left") {
                    cc.scroll((currentFirstCard - 1) * singleCardWidth(), 0);
                } else {
                    cc.scroll((currentFirstCard + 1) * singleCardWidth(), 0);
                }
            }

            leftButton.addEventListener("click", () => {
                scrollContainer("left");
            });

            rightButton.addEventListener("click", () => {
                scrollContainer("right");
            });
        });
    }
}

// setupCarouselArrows();

function setupGalleryCarouselArrows() {
    const allCarousels = [...selectAll(".gallery-container__wrapper .carousel")];
    allCarousels.forEach((aC) => {
        const wrapper = aC.parentElement;
        const carouselScroller = selectFrom(".carousel-scroller", wrapper);

        const allItems = [...selectAllFrom(".gallery-container__item", aC)];

        const leftButton = selectFrom(".carousel-scroller__left", carouselScroller);
        const rightButton = selectFrom(".carousel-scroller__right", carouselScroller);

        const firstItem = allItems[0];

        const singleItemWidth = () =>
            firstItem.clientWidth + Number(window.getComputedStyle(aC).columnGap.replace("px", ""));

        function scrollItems(direction) {
            const currentFirstItem = Math.round(aC.scrollLeft / singleItemWidth());
            if (direction === "left") {
                aC.scroll((currentFirstItem - 1) * singleItemWidth(), 0);
            } else {
                aC.scroll((currentFirstItem + 1) * singleItemWidth(), 0);
            }
        }

        leftButton.addEventListener("click", () => {
            scrollItems("left");
        });

        rightButton.addEventListener("click", () => {
            scrollItems("right");
        });

        aC.addEventListener("scroll", (e) => {
            if (aC.scrollLeft === 0) {
                carouselScroller.classList.remove("left");
            } else {
                carouselScroller.classList.add("left");
            }

            if (aC.scrollLeft === aC.scrollWidth - aC.clientWidth) {
                carouselScroller.classList.remove("right");
            } else {
                carouselScroller.classList.add("right");
            }
        });
        const scrollEvent = new Event("scroll");
        aC.dispatchEvent(scrollEvent);
    });
}
setTimeout(() => {
    setupGalleryCarouselArrows();
}, 1000);

function setupAllCarouselArrows() {
    const allCarousels = [...selectAll(".carousel-container")];
    allCarousels.forEach((container) => {
        const itemsContainer = selectFrom(".carousel-items-container", container);
        if ([...itemsContainer.classList].includes("auto-scroll")) {
            return;
        }
        const allItems = [...itemsContainer.children];

        const carouselScroller = selectFrom(".carousel-scroller", container);
        const leftButton = selectFrom(".carousel-scroller__left", carouselScroller);
        const rightButton = selectFrom(".carousel-scroller__right", carouselScroller);

        const firstItem = allItems[0];

        const singleItemWidth = () =>
            firstItem.clientWidth + Number(window.getComputedStyle(itemsContainer).columnGap.replace("px", ""));

        function scrollItems(direction) {
            const currentFirstItem = Math.round(itemsContainer.scrollLeft / singleItemWidth());
            if (direction === "left") {
                itemsContainer.scroll((currentFirstItem - 1) * singleItemWidth(), 0);
            } else {
                itemsContainer.scroll((currentFirstItem + 1) * singleItemWidth(), 0);
            }
        }

        leftButton.addEventListener("click", () => {
            scrollItems("left");
        });

        rightButton.addEventListener("click", () => {
            scrollItems("right");
        });

        itemsContainer.addEventListener("scroll", (e) => {
            if (itemsContainer.scrollLeft === 0) {
                carouselScroller.classList.remove("left");
            } else {
                carouselScroller.classList.add("left");
            }

            if (itemsContainer.scrollLeft === itemsContainer.scrollWidth - itemsContainer.clientWidth) {
                carouselScroller.classList.remove("right");
            } else {
                carouselScroller.classList.add("right");
            }
        });

        const scrollEvent = new Event("scroll");
        itemsContainer.dispatchEvent(scrollEvent);
    });
}

setTimeout(() => {
    setupAllCarouselArrows();
}, 1000);

function setupParallax() {
    const parallaxFooter = select("div.box.v_2-footer.parallax");

    if (parallaxFooter) {
        const footerSpacer = select(".footer-spacer");
        footerSpacer.style.height = `${parallaxFooter.clientHeight}px`;
        footerSpacer.style.minHeight = `${parallaxFooter.clientHeight}px`;
    }
}

setTimeout(() => {
    // setupParallax();
}, 1000);

let useAutoHide = false;
let menuOpen;

function autoHide() {
    const autoHideEl = select(".fixed.auto-hide");
    let hidden = false;

    if (autoHideEl) {
        useAutoHide = true;
        const documentEl = document.documentElement;
        let lastScrollY = 0;
        window.addEventListener("scroll", (e) => {
            const elScrollOffset = autoHideEl.clientHeight + autoHideEl.getBoundingClientRect().top;
            if (hidden && documentEl.scrollTop < lastScrollY) {
                autoHideEl.style.transform = "translateY(0)";
                document.documentElement.style.setProperty("--left-title-offset", `${autoHideEl.clientHeight}px`);
                hidden = false;
            } else if (!hidden && documentEl.scrollTop > lastScrollY && documentEl.scrollTop > elScrollOffset) {
                hidden = true;
                autoHideEl.style.transform = `translateY(-${elScrollOffset + 2}px)`;
                document.documentElement.style.setProperty("--left-title-offset", "0px");
            }
            lastScrollY = documentEl.scrollTop;
        });
    }
}

setTimeout(() => {
    autoHide();
}, 1000);
