const domain = window.location.host;
const trustarc = drupalSettings.trustarc;

if (typeof trustarc !== 'undefined' && (trustarc.gcmEnabled || trustarc.standardEventListener)) {
    window.dataLayer = window.dataLayer || [];
}

if (typeof trustarc !== 'undefined' && trustarc.gcmEnabled) {
    /* 
    GOOGLE CONSENT MODE INTEGRATION
    */
    function gtag() { dataLayer.push(arguments); }

    gtag('js', new Date());
    gtag('set', 'developer_id.dNTIxZG', true);

    if (trustarc.gaMeasurementId) {
        gtag('config', trustarc.gaMeasurementId);
    }

    gtag('set', 'ads_data_redaction', trustarc.dataRedaction === 'true');
    gtag('set', 'url_passthrough', trustarc.urlPassthrough === 'true');

    // Consent Mode Status
    const ConsentType = {
        DENIED: 'denied',
        GRANTED: 'granted',
    };

    const booleanToConsentStatus = (boolean) => boolean ? ConsentType.GRANTED : ConsentType.DENIED;

    // Bucket Mapping
    const consentTypesMapped = JSON.parse(trustarc.googleConsentFields);

    const getConsentState = (prefCookie) => {
        var consentStates = {};

        var noticeBehavior = window.truste.util.readCookie('notice_behavior');
        var impliedLocation = noticeBehavior && noticeBehavior.includes(trustarc.impliedLocation);

        for (const consentType in consentTypesMapped) {
            var id = consentTypesMapped[consentType];

            if (prefCookie && !prefCookie.includes(0)) {
                consentStates[consentType] = booleanToConsentStatus(prefCookie.includes(id));
            } else {
                consentStates[consentType] = booleanToConsentStatus(impliedLocation);
            }
        }

        consentStates['wait_for_update'] = trustarc.waitForUpdate === 'true';
        return consentStates;
    };

    var runOnceGCM = 0;
    function handleConsentDecisionForGCM(consent) {
        const consentStates = getConsentState(consent.consentDecision);
        var defaultOrUpdate = runOnceGCM === 0 ? 'default' : 'update';
        runOnceGCM++;

        gtag('consent', defaultOrUpdate, consentStates);
    }

    var _taInterval;
    var _taAttempts = 0;
    var _taGoogleTagWasSetLate = function () {
        if (_taAttempts > 50) clearInterval(_taInterval);

        if (window.google_tag_data && window.google_tag_data.ics && window.google_tag_data.ics.wasSetLate) {
            console.warn('WARNING: Tags are firing before consent is initialized. Please ensure that the consent mode default is initialized before firing tags.');
            clearInterval(_taInterval);
        }

        _taAttempts++;
    }
    _taInterval = setInterval(_taGoogleTagWasSetLate, 200);
}

if (typeof trustarc !== 'undefined' && trustarc.wpConsentApiEnabled) {
    /*
    WP Consent API Integration
    */
    const WPConsentType = {
        ALLOW: 'allow',
        DENY: 'deny',
    };

    const wpConsentTypesMapped = JSON.parse(trustarc.wpConsentApiMapping);

    const booleanToAllowOrDeny = (boolean) => boolean ? WPConsentType.ALLOW : WPConsentType.DENY;

    const getWPConsentState = (prefCookie) => {
        var consentStates = {};

        var noticeBehavior = window.truste.util.readCookie('notice_behavior');
        var impliedLocation = noticeBehavior && noticeBehavior.includes(trustarc.impliedLocation);

        for (const consentType in wpConsentTypesMapped) {
            var id = wpConsentTypesMapped[consentType];

            if (prefCookie && !prefCookie.includes(0)) {
                consentStates[consentType] = booleanToAllowOrDeny(prefCookie.includes(id));
            } else {
                consentStates[consentType] = booleanToAllowOrDeny(impliedLocation);
            }
        }

        return consentStates;
    };

    function handleConsentDecisionForWP(consent) {
        var noticeBehavior = window.truste.util.readCookie('notice_behavior');
        var impliedLocation = noticeBehavior && noticeBehavior.includes(trustarc.impliedLocation);

        if (typeof window.wp_consent_type !== 'undefined')
            window.wp_consent_type = impliedLocation ? 'optin' : 'optout';

        const consentStates = getWPConsentState(consent.consentDecision);

        for (const consentType in consentStates) {
            var value = consentStates[consentType];
            if (typeof wp_set_consent === 'function') {
                wp_set_consent(consentType, value);
            }
        }
    }
}

/* 
    Event Listener
*/
var __dispatched__ = {}; // Map of previously dispatched preference levels
function handleConsentDecisionForTA(consent) {
    consent.consentDecision && consent.consentDecision.forEach(function(label) {
        if (!__dispatched__[label]) {
            self.dataLayer && self.dataLayer.push({
                "event": "GDPR Pref Allows " + label
            });
            __dispatched__[label] = 1;
        }
    });
}

var interval = setInterval(() => {
    if (window.truste && truste.cma && truste.cma.callApi) {
        var consentDecision = truste.cma.callApi('getGDPRConsentDecision', domain);
        if (typeof trustarc !== 'undefined') {
            if (trustarc.gcmEnabled) {
                handleConsentDecisionForGCM(consentDecision);
            }
            if (trustarc.wpConsentApiEnabled) {
                handleConsentDecisionForWP(consentDecision);
            }
            if (trustarc.standardEventListener) {
                handleConsentDecisionForTA(consentDecision);
            }
        }
        clearInterval(interval);
    }
}, 100);

// Start listening to when users submit their preferences
window.addEventListener('message', (event) => {
    let eventDataJson = null;
    try {
        eventDataJson = JSON.parse(event.data);
    } catch {
        // Some other event that is not JSON.
    }

    if (eventDataJson && eventDataJson.source === 'preference_manager') {
        if (eventDataJson.message === 'submit_preferences') {
            setTimeout(() => {
                var consentDecision = truste.cma.callApi('getGDPRConsentDecision', domain);

                if (typeof trustarc !== 'undefined') {
                    if (trustarc.gcmEnabled) {
                        handleConsentDecisionForGCM(consentDecision);
                    }
                    if (trustarc.wpConsentApiEnabled) {
                        handleConsentDecisionForWP(consentDecision);
                    }
                    if (trustarc.standardEventListener) {
                        handleConsentDecisionForTA(consentDecision);
                    }
                }
            }, 500);
        }
    }
}, false);
