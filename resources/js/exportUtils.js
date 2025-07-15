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

setupCarouselArrows();

function setupParallax() {
    const parallaxFooter = select("div.box.v_2-footer.parallax");

    if (parallaxFooter) {
        const footerSpacer = select(".footer-spacer");
        footerSpacer.style.height = `${parallaxFooter.clientHeight}px`;
        footerSpacer.style.minHeight = `${parallaxFooter.clientHeight}px`;
    }
}
setupParallax();

function setupFooterMenu() {
    const v2Footer = select(".v_2-footer");
    if (v2Footer) {
        let currentOpen = null;
        let mobileFooter = window.innerWidth < 1024;
        const linkGroups = [...selectAll(".v_2-footer .links .link-group:not(.empty-group)")];
        linkGroups.forEach((lg) => {
            const openButton = lg.querySelector(".group__heading");
            openButton.addEventListener("click", () => {
                if (mobileFooter) {
                    if (currentOpen !== lg) {
                        currentOpen?.classList.remove("open");
                        lg.classList.add("open");
                        currentOpen = lg;
                    } else {
                        lg.classList.remove("open");
                        currentOpen = null;
                    }
                }
            });
        });
        window.addEventListener("resize", () => {
            mobileFooter = window.innerWidth < 1024;
        });
    }
}

setupFooterMenu();

// function to scroll to anchor tag
function checkForTarget() {
    const hasTarget = location.hash !== "";

    if (hasTarget && !menuOpen) {
        const targetElement = select(location.hash);
        if (targetElement) {
            const navBar = select(".navbar-box");
            window.scroll(0, window.scrollY + targetElement.getBoundingClientRect().top - navBar.clientHeight - 30);
        }
    }
}

let useAutoHide = false;
let menuOpen;

// function to show menu
function setupMenu() {
    let windowHeight = window.innerHeight;
    function onResize() {
        if (windowHeight === window.innerHeight || window.innerWidth > 1024) {
            document.body.style.overflowY = "scroll";
            select(".mobile-nav-overlay").classList.remove("open");
            select(".menu-icon .site-menu-icon").classList.remove("open");
            select(".navbar-box").classList.remove("mobile-open");
            menuOpen = false;
            window.removeEventListener("resize", onResize);
            window.removeEventListener("hashchange", onResize);
            toggleScroll(false);
        }
    }
    const toggleButton = select(".site-menu-icon-container");
    const openButton = select(".menu-icon #mobileHam");
    if (toggleButton) {
        menuOpen = false;
        const navBar = select(".navbar-box");
        const miniHeader = [...navBar.classList].includes("header-mini");
        toggleButton.addEventListener("click", function () {
            menuOpen = !menuOpen;
            document.body.style.overflowY = menuOpen ? "hidden" : "scroll";
            select(".mobile-nav-overlay").classList.toggle("open");
            select(".menu-icon .site-menu-icon").classList.toggle("open");
            if (miniHeader && false) {
                gsap.registerPlugin(Flip);
                const state = Flip.getState(navBar);
                Flip.from(state, {duration: 0.6, ease: "power1.inOut"});
            }
            navBar.classList.toggle("mobile-open");
            menuOpen && window.addEventListener("resize", onResize);
            menuOpen && window.addEventListener("hashchange", onResize);
            toggleScroll(menuOpen);
        });
    } else {
        openButton?.addEventListener("click", function () {
            document.body.style.overflowY = "hidden";
            document.querySelector(".mobile-nav-overlay").classList.add("open");
            toggleScroll(true);
            window.addEventListener("resize", onResize);
        });
        var closeMenu = document.querySelector(".close-icon");
        closeMenu?.addEventListener("click", function () {
            document.body.style.overflowY = "scroll";
            document.querySelector(".mobile-nav-overlay").classList.remove("open");
            toggleScroll(false);
        });
        document.querySelector(".mobile-nav-overlay")?.addEventListener("click", (e) => {
            if (e.target.classList.contains("mobile-nav-overlay")) {
                e.target.classList.remove("open");
                document.body.style.overflowY = "scroll";
                toggleScroll(false);
            }
        });
    }
}
setupMenu();


const switchTheme = (e) => {
    let theme = document.documentElement.getAttribute("data-theme");
    // console.log("before theme");
    // console.log(theme);
    if (theme === "light") {
        theme = "dark";
    } else {
        theme = "light";
    }
    // console.log("after theme");
    // console.log(theme);
    document.documentElement.setAttribute("data-theme", theme);
    localStorage.setItem("theme", theme);
    toggleTheme(theme);
    setupParallax();
};
const ThemeButtons = document.querySelectorAll(".theme-btn");
ThemeButtons.forEach((tB) => {
    tB.addEventListener("click", switchTheme, false);
});

const darkModeBtn = document.querySelectorAll(".dark-mode");
const lightModeBtn = document.querySelectorAll(".light-mode");

darkModeBtn.forEach((btn) => {
    btn.addEventListener("click", switchTheme);
});
lightModeBtn.forEach((btn) => {
    btn.addEventListener("click", switchTheme);
});

const toggleTheme = (theme) => {
    if (theme === "light") {
        darkModeBtn.forEach((btn) => {
            btn.classList.remove('!hidden');
        });
        lightModeBtn.forEach((btn) => {
            btn.classList.add('!hidden');
        });
    } else {
        darkModeBtn.forEach((btn) => {
            btn.classList.add('!hidden');
        });
        lightModeBtn.forEach((btn) => {
            btn.classList.remove('!hidden');
        });
    }
};

const presetTheme = () => {
    // if (document.documentElement.getAttribute("data-theme") !== "none") return;
    let theme = localStorage.getItem("theme");
    if (!theme) {
        if (window.matchMedia && window.matchMedia("(prefers-color-scheme: dark)").matches) {
            theme = "dark";
        } else {
            theme = "light";
        }
    }
    toggleTheme(theme);
    document.documentElement.setAttribute("data-theme", theme);
};
presetTheme();

// function to close announcement bar
function hideAnnouncementBarBlock() {
    const announcementBar = document.querySelector(".announcement-bar-block");
    announcementBar.style.display = "none";
}
// let announcementCloseButton = document.getElementById('announcement-close-button');
// announcementCloseButton.addEventListener('click', hideAnnouncementBarBlock);
function toggleAccordion(e) {
    // let accordion_items = e.currentTarget.parentElement.parentElement.children;
    let accordion_items = e.currentTarget.closest(".accordion-item").parentElement.children;
    let accordion_length = accordion_items.length;
    for (let x = 0; x < accordion_length; x++) {
        let accordion = accordion_items[x];
        if (
            e.currentTarget.closest(".accordion-item").dataset.index !==
            accordion.children[0].parentElement.dataset.index
        ) {
            if (e.currentTarget.closest(".accordion-item").dataset.icontype === "plus") {
                accordion.children[0].querySelector("#plus").style.display = "block";
                accordion.children[0].querySelector("#minus").style.display = "none";
            }
            // accordion.children[0].classList.remove("active");
            // accordion.children[1].classList.remove("active");
        }
    }
    // e.currentTarget.classList.toggle("active");
    // e.currentTarget.nextElementSibling.classList.toggle("active");
    if (e.target.closest(".accordion-item")) {
        e.target.classList.toggle("active");
        e.target.parentElement.classList.toggle("active");
        if (e.target.nextElementSibling) e.target.nextElementSibling.classList.toggle("active");
    }
    if (e.target.closest(".accordion-item").dataset.icontype === "plus" && e.target.classList.contains("active")) {
        e.target.closest(".accordion-item").querySelector("#minus").style.display = "block";
        e.target.closest(".accordion-item").querySelector("#plus").style.display = "none";
    } else if (e.target.closest(".accordion-item").dataset.icontype === "plus") {
        e.target.closest(".accordion-item").querySelector("#minus").style.display = "none";
        e.target.closest(".accordion-item").querySelector("#plus").style.display = "block";
    }
}
// let accordion_buttons = document.querySelectorAll('.accordion-header')
let accordion_buttons = document.querySelectorAll(".accordion-item");
accordion_buttons.forEach((button) => button.addEventListener("click", toggleAccordion));