import { replaceUrl, updateCurrentPageHtmlInHistoryStateForLaterBackButtonClicks, updateCurrentPageHtmlInSnapshotCacheForLaterBackButtonClicks, updateUrlAndStoreLatestHtmlForFutureBackButtons, whenTheBackOrForwardButtonIsClicked } from "./history"
import { getPretchedHtmlOr, prefetchHtml, storeThePrefetchedHtmlForWhenALinkIsClicked, deletePrefetchedUrlAndPrefetch } from "./prefetch"
import { createUrlObjectFromString, extractDestinationFromLink, whenThisLinkIsHoveredFor, whenThisLinkIsPressed } from "./links"
import { isTeleportTarget, packUpPersistedTeleports, removeAnyLeftOverStaleTeleportTargets, unPackPersistedTeleports } from "./teleport"
import { restoreScrollPositionOrScrollToTop, storeScrollInformationInHtmlBeforeNavigatingAway } from "./scroll"
import { isPersistedElement, putPersistantElementsBack, storePersistantElementsForLater } from "./persist"
import { finishAndHideProgressBar, removeAnyLeftOverStaleProgressBars, showAndStartProgressBar } from "./bar"
import { swapCurrentPageWithNewHtml } from "./page"
import { fetchHtml } from "./fetch"

let enablePersist = true
let showProgressBar = true
let restoreScroll = true
let autofocus = false

export default function (Alpine) {

    Alpine.navigate = (url) => {
        let destination = createUrlObjectFromString(url)

        let prevented = fireEventForOtherLibariesToHookInto('alpine:navigate', {
            url: destination, history: false, cached: false,
         })

        if (prevented) return

        navigateTo(destination)
    }

    Alpine.navigate.disableProgressBar = () => {
        showProgressBar = false
    }

    Alpine.addInitSelector(() => `[${Alpine.prefixed('navigate')}]`)

    // document.addEventListener('mousemove', function(event) {
    //   const cursorX = event.clientX;
    //   const cursorY = event.clientY;
    
    //   document.querySelectorAll('a[x-link\\.prefetch]').forEach(function(anchor) {
    //     const rect = anchor.getBoundingClientRect();
    //     const anchorX = rect.left + rect.width / 2;
    //     const anchorY = rect.top + rect.height / 2;
    
    //     const distance = Math.sqrt((anchorX - cursorX) ** 2 + (anchorY - cursorY) ** 2);
    
    //     if (distance <= 200) {
    //         let des = extractDestinationFromLink(anchor)
    //         prefetchHtml(des, (html, finalDestination) => {
    //             storeThePrefetchedHtmlForWhenALinkIsClicked(html, des, finalDestination)
    //         });
    //     }
    //   });
    // });
    let refreshCount = 0;
    let lastRefreshCount = 0;
    const refreshInterval = 500; // Adjust the interval as needed (in milliseconds)
    let refreshTimeout;
    let lastDestination; // To store the last destination

    function pileup(destination) {
        let url = new URL(window.location.href, document.baseURI);
        deletePrefetchedUrlAndPrefetch(url)
    }

    function checkRefreshCompletion() {
        if (refreshCount === lastRefreshCount) {
            pileup(lastDestination);
        } else {
            lastRefreshCount = refreshCount;
            refreshTimeout = setTimeout(checkRefreshCompletion, refreshInterval);
        }
    }

    function refreshPage(component, destination) {
        if (checkComponentName(component)) {
            // console.log('refreshPage', component);
            refreshCount++;
            lastDestination = destination; // Update the last destination
            
            // Clear and reset the timeout each time the function is called
            clearTimeout(refreshTimeout);
            refreshTimeout = setTimeout(checkRefreshCompletion, refreshInterval);
        }
    }

    let detectMobile = function() {
        return ( ( window.innerWidth <= 800 ) );
    };
    let checkComponentName = function(component) {
        const name = component.name;
        return name.startsWith("components.") && name.endsWith(".page") && name.includes("console");
    }
    Alpine.directive('link', (el, { modifiers }) => {
        let shouldPrefetchOnHover = modifiers.includes('hover');
        let destination = extractDestinationFromLink(el)

        let shouldPrefetchOnPrefetch = modifiers.includes('prefetch');
        Livewire.hook('commit', ({ component, commit, respond, succeed, fail }) => {
            // Runs immediately before a commit's payload is sent to the server...
         
            respond(() => {
                // Runs after a response is received but before it's processed...
            })
         
            succeed(({ snapshot, effect }) => {
                refreshPage(component, destination);
                // Runs after a successful response is received and processed
                // with a new snapshot and list of effects...
            })
         
            fail(() => {
                // Runs if some part of the request failed...
            })
        });

        if(shouldPrefetchOnPrefetch){
            // document.addEventListener('livewire:init', () => {
            //     console.log('done')
            // });
            let des = extractDestinationFromLink(el)
            // console.log(el)

            setTimeout(() => {
                prefetchHtml(des, (html, finalDestination) => {
                    storeThePrefetchedHtmlForWhenALinkIsClicked(html, des, finalDestination)
                });
            }, 200);

            shouldPrefetchOnHover = true;
        }

        shouldPrefetchOnHover && whenThisLinkIsHoveredFor(el, 60, () => {
            let destination = extractDestinationFromLink(el)

            // prefetchHtml(destination, (html, finalDestination) => {
            //     storeThePrefetchedHtmlForWhenALinkIsClicked(html, destination, finalDestination)
            // })
        })

        whenThisLinkIsPressed(el, (whenItIsReleased) => {
            let destination = extractDestinationFromLink(el)

            prefetchHtml(destination, (html, finalDestination) => {
                storeThePrefetchedHtmlForWhenALinkIsClicked(html, destination, finalDestination)
            })

            whenItIsReleased(() => {
                let prevented = fireEventForOtherLibariesToHookInto('alpine:navigate', {
                    url: destination, history: false, cached: false,
                 })

                if (prevented) return

                navigateTo(destination);
            })
        })
    })

    function navigateTo(destination, shouldPushToHistoryState = true) {
        showProgressBar && showAndStartProgressBar()

        // console.log('zheyy', document.referrer, destination.href)
        fetchHtmlOrUsePrefetchedHtml(destination, (html, finalDestination) => {
            fireEventForOtherLibariesToHookInto('alpine:navigating')

            restoreScroll && storeScrollInformationInHtmlBeforeNavigatingAway()

            showProgressBar && finishAndHideProgressBar()

            cleanupAlpineElementsOnThePageThatArentInsideAPersistedElement()

            updateCurrentPageHtmlInHistoryStateForLaterBackButtonClicks()

            preventAlpineFromPickingUpDomChanges(Alpine, andAfterAllThis => {
                enablePersist && storePersistantElementsForLater(persistedEl => {
                    packUpPersistedTeleports(persistedEl)
                })

                if (shouldPushToHistoryState) {
                    updateUrlAndStoreLatestHtmlForFutureBackButtons(html, finalDestination)
                } else {
                    replaceUrl(finalDestination, html)
                }

                swapCurrentPageWithNewHtml(html, (afterNewScriptsAreDoneLoading) => {
                    removeAnyLeftOverStaleTeleportTargets(document.body)

                    enablePersist && putPersistantElementsBack((persistedEl, newStub) => {
                        unPackPersistedTeleports(persistedEl)
                    })

                    restoreScrollPositionOrScrollToTop()

                    afterNewScriptsAreDoneLoading(() => {
                        andAfterAllThis(() => {
                            setTimeout(() => {
                                autofocus && autofocusElementsWithTheAutofocusAttribute()
                            })

                            nowInitializeAlpineOnTheNewPage(Alpine)

                            fireEventForOtherLibariesToHookInto('alpine:navigated')
                            
                            fireEventForOtherLibariesToHookInto('alpine:navigatedComplete')

                            deletePrefetchedUrlAndPrefetch(destination)
                        })
                    })
                })
            })
        })
    }

    whenTheBackOrForwardButtonIsClicked(
        (ifThePageBeingVisitedHasntBeenCached) => {
            ifThePageBeingVisitedHasntBeenCached((url) => {
                let destination = createUrlObjectFromString(url)

                let prevented = fireEventForOtherLibariesToHookInto('alpine:navigate', {
                    url: destination, history: true, cached: false,
                 })

                if (prevented) return

                let shouldPushToHistoryState = false

                navigateTo(destination, shouldPushToHistoryState)
            })
        },
        (html, url, currentPageUrl, currentPageKey) => {
            let destination = createUrlObjectFromString(url)

            let prevented = fireEventForOtherLibariesToHookInto('alpine:navigate', {
                url: destination, history: true, cached: true,
            })

            if (prevented) return

            // @todo: see if there's a way to update the current HTML BEFORE
            // the back button is hit, and not AFTER:
            storeScrollInformationInHtmlBeforeNavigatingAway()

            // This ensures the current HTML has the latest snapshot
            fireEventForOtherLibariesToHookInto('alpine:navigating')

            // Only update the snapshot and not the history state as the history state
            // has already changed to the new page due to the popstate event
            updateCurrentPageHtmlInSnapshotCacheForLaterBackButtonClicks(currentPageUrl, currentPageKey)

            preventAlpineFromPickingUpDomChanges(Alpine, andAfterAllThis => {
                enablePersist && storePersistantElementsForLater(persistedEl => {
                    packUpPersistedTeleports(persistedEl)
                })

                swapCurrentPageWithNewHtml(html, () => {
                    removeAnyLeftOverStaleProgressBars()

                    removeAnyLeftOverStaleTeleportTargets(document.body)

                    enablePersist && putPersistantElementsBack((persistedEl, newStub) => {
                        unPackPersistedTeleports(persistedEl)
                    })

                    restoreScrollPositionOrScrollToTop()

                    andAfterAllThis(() => {
                        autofocus && autofocusElementsWithTheAutofocusAttribute()

                        nowInitializeAlpineOnTheNewPage(Alpine)

                        fireEventForOtherLibariesToHookInto('alpine:navigated')
                        fireEventForOtherLibariesToHookInto('alpine:navigatedComplete')

                        
                        deletePrefetchedUrlAndPrefetch(destination)
                    })
                })
            })
        },
    )

    // Because DOMContentLoaded is fired on first load,
    // we should fire alpine:navigated as a replacement as well...
    setTimeout(() => {
        fireEventForOtherLibariesToHookInto('alpine:navigated')
    })
}

function fetchHtmlOrUsePrefetchedHtml(fromDestination, callback) {
    getPretchedHtmlOr(fromDestination, callback, () => {
        fetchHtml(fromDestination, callback)
    })
}

function preventAlpineFromPickingUpDomChanges(Alpine, callback) {
    Alpine.stopObservingMutations()

    callback((afterAllThis) => {
        Alpine.startObservingMutations()

        queueMicrotask(() => {
            afterAllThis()
        })
    })
}

function fireEventForOtherLibariesToHookInto(name, detail) {
    let event = new CustomEvent(name, {
        cancelable: true,
        bubbles: true,
        detail,
    })

    document.dispatchEvent(event)

    return event.defaultPrevented
}

function nowInitializeAlpineOnTheNewPage(Alpine) {
    Alpine.initTree(document.body, undefined, (el, skip) => {
        if (el._x_wasPersisted) skip()
    })
}

function autofocusElementsWithTheAutofocusAttribute() {
    document.querySelector('[autofocus]') && document.querySelector('[autofocus]').focus()
}

function cleanupAlpineElementsOnThePageThatArentInsideAPersistedElement() {
    // Create a new tree walker that skips persisted elements...
    let walker = function (root, callback) {
        Alpine.walk(root, (el, skip) => {
            if (isPersistedElement(el)) skip()
            if (isTeleportTarget(el)) skip()
            else callback(el, skip)
        })
    }

    // Set Alpine in motion to destroy itself on the page. If this proves
    // to be a performance issue at some point (walking the DOM tree),
    // we can be more surgical about cleaning up x-for/if instead...
    Alpine.destroyTree(document.body, walker)
}
