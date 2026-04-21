/**
 * UTF-8 Fix - Correction automatique des caractères mal encodés
 * À placer dans : /public/js/utf8-fix.js
 */
(function() {
    'use strict';
    
    // Table de conversion complète
    const map = {
        // Minuscules
        'Ã©':'é', 'Ã¨':'è', 'Ãª':'ê', 'Ã«':'ë',
        'Ã ':'à', 'Ã¢':'â', 'Ã¤':'ä',
        'Ã§':'ç',
        'Ã´':'ô', 'Ã¶':'ö',
        'Ã¹':'ù', 'Ã»':'û', 'Ã¼':'ü',
        'Ã®':'î', 'Ã¯':'ï',
        // Majuscules
        'Ã‰':'É', 'Ãˆ':'È', 'ÃŠ':'Ê', 'Ã‹':'Ë',
        'Ã€':'À', 'Ã‚':'Â', 'Ã„':'Ä',
        'Ã‡':'Ç',
        'Ã"':'Ô', 'Ã–':'Ö',
        'Ã™':'Ù', 'Ã›':'Û', 'Ãœ':'Ü',
        'ÃŽ':'Î', 'Ã':'Ï',
        // Spéciaux
        'Â«':'«', 'Â»':'»', 'Â°':'°', 'Â':'  '
    };
    
    function fix(txt) {
        if (!txt || typeof txt !== 'string') return txt;
        for (let [bad, good] of Object.entries(map)) {
            txt = txt.split(bad).join(good);
        }
        return txt;
    }
    
    function fixNode(el) {
        const walk = document.createTreeWalker(el, NodeFilter.SHOW_TEXT, null, false);
        let nodes = [], n;
        while (n = walk.nextNode()) {
            if (n.parentElement && !['SCRIPT','STYLE'].includes(n.parentElement.tagName)) {
                if (/Ã|Â/.test(n.textContent)) nodes.push(n);
            }
        }
        nodes.forEach(n => n.textContent = fix(n.textContent));
    }
    
    function fixAttrs(el) {
        ['placeholder','title','alt','value','aria-label'].forEach(a => {
            el.querySelectorAll('['+a+']').forEach(e => {
                let v = e.getAttribute(a);
                if (v && /Ã|Â/.test(v)) e.setAttribute(a, fix(v));
            });
        });
    }
    
    function init() {
        fixNode(document.body);
        fixAttrs(document.body);
        
        // Observer pour contenu dynamique
        new MutationObserver(muts => {
            muts.forEach(m => {
                m.addedNodes.forEach(n => {
                    if (n.nodeType === 1) {
                        fixNode(n);
                        fixAttrs(n);
                    } else if (n.nodeType === 3 && /Ã|Â/.test(n.textContent)) {
                        n.textContent = fix(n.textContent);
                    }
                });
            });
        }).observe(document.body, {childList:true, subtree:true});
    }
    
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
    
    window.fixUTF8 = fix; // Export pour usage manuel
})();
