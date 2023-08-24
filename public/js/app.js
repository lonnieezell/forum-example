// modules are defined as an array
// [ module function, map of requires ]
//
// map of requires is short require name -> numeric require
//
// anything defined in a previous bundle is accessed via the
// orig method which is the require for previous bundles

(function (modules, entry, mainEntry, parcelRequireName, globalName) {
  /* eslint-disable no-undef */
  var globalObject =
    typeof globalThis !== 'undefined'
      ? globalThis
      : typeof self !== 'undefined'
      ? self
      : typeof window !== 'undefined'
      ? window
      : typeof global !== 'undefined'
      ? global
      : {};
  /* eslint-enable no-undef */

  // Save the require from previous bundle to this closure if any
  var previousRequire =
    typeof globalObject[parcelRequireName] === 'function' &&
    globalObject[parcelRequireName];

  var cache = previousRequire.cache || {};
  // Do not use `require` to prevent Webpack from trying to bundle this call
  var nodeRequire =
    typeof module !== 'undefined' &&
    typeof module.require === 'function' &&
    module.require.bind(module);

  function newRequire(name, jumped) {
    if (!cache[name]) {
      if (!modules[name]) {
        // if we cannot find the module within our internal map or
        // cache jump to the current global require ie. the last bundle
        // that was added to the page.
        var currentRequire =
          typeof globalObject[parcelRequireName] === 'function' &&
          globalObject[parcelRequireName];
        if (!jumped && currentRequire) {
          return currentRequire(name, true);
        }

        // If there are other bundles on this page the require from the
        // previous one is saved to 'previousRequire'. Repeat this as
        // many times as there are bundles until the module is found or
        // we exhaust the require chain.
        if (previousRequire) {
          return previousRequire(name, true);
        }

        // Try the node require function if it exists.
        if (nodeRequire && typeof name === 'string') {
          return nodeRequire(name);
        }

        var err = new Error("Cannot find module '" + name + "'");
        err.code = 'MODULE_NOT_FOUND';
        throw err;
      }

      localRequire.resolve = resolve;
      localRequire.cache = {};

      var module = (cache[name] = new newRequire.Module(name));

      modules[name][0].call(
        module.exports,
        localRequire,
        module,
        module.exports,
        this
      );
    }

    return cache[name].exports;

    function localRequire(x) {
      var res = localRequire.resolve(x);
      return res === false ? {} : newRequire(res);
    }

    function resolve(x) {
      var id = modules[name][1][x];
      return id != null ? id : x;
    }
  }

  function Module(moduleName) {
    this.id = moduleName;
    this.bundle = newRequire;
    this.exports = {};
  }

  newRequire.isParcelRequire = true;
  newRequire.Module = Module;
  newRequire.modules = modules;
  newRequire.cache = cache;
  newRequire.parent = previousRequire;
  newRequire.register = function (id, exports) {
    modules[id] = [
      function (require, module) {
        module.exports = exports;
      },
      {},
    ];
  };

  Object.defineProperty(newRequire, 'root', {
    get: function () {
      return globalObject[parcelRequireName];
    },
  });

  globalObject[parcelRequireName] = newRequire;

  for (var i = 0; i < entry.length; i++) {
    newRequire(entry[i]);
  }

  if (mainEntry) {
    // Expose entry point to Node, AMD or browser globals
    // Based on https://github.com/ForbesLindesay/umd/blob/master/template.js
    var mainExports = newRequire(mainEntry);

    // CommonJS
    if (typeof exports === 'object' && typeof module !== 'undefined') {
      module.exports = mainExports;

      // RequireJS
    } else if (typeof define === 'function' && define.amd) {
      define(function () {
        return mainExports;
      });

      // <script>
    } else if (globalName) {
      this[globalName] = mainExports;
    }
  }
})({"aQYcG":[function(require,module,exports) {
var global = arguments[3];
var HMR_HOST = null;
var HMR_PORT = null;
var HMR_SECURE = false;
var HMR_ENV_HASH = "d6ea1d42532a7575";
module.bundle.HMR_BUNDLE_ID = "465e81a769f79fbb";
"use strict";
/* global HMR_HOST, HMR_PORT, HMR_ENV_HASH, HMR_SECURE, chrome, browser, globalThis, __parcel__import__, __parcel__importScripts__, ServiceWorkerGlobalScope */ /*::
import type {
  HMRAsset,
  HMRMessage,
} from '@parcel/reporter-dev-server/src/HMRServer.js';
interface ParcelRequire {
  (string): mixed;
  cache: {|[string]: ParcelModule|};
  hotData: {|[string]: mixed|};
  Module: any;
  parent: ?ParcelRequire;
  isParcelRequire: true;
  modules: {|[string]: [Function, {|[string]: string|}]|};
  HMR_BUNDLE_ID: string;
  root: ParcelRequire;
}
interface ParcelModule {
  hot: {|
    data: mixed,
    accept(cb: (Function) => void): void,
    dispose(cb: (mixed) => void): void,
    // accept(deps: Array<string> | string, cb: (Function) => void): void,
    // decline(): void,
    _acceptCallbacks: Array<(Function) => void>,
    _disposeCallbacks: Array<(mixed) => void>,
  |};
}
interface ExtensionContext {
  runtime: {|
    reload(): void,
    getURL(url: string): string;
    getManifest(): {manifest_version: number, ...};
  |};
}
declare var module: {bundle: ParcelRequire, ...};
declare var HMR_HOST: string;
declare var HMR_PORT: string;
declare var HMR_ENV_HASH: string;
declare var HMR_SECURE: boolean;
declare var chrome: ExtensionContext;
declare var browser: ExtensionContext;
declare var __parcel__import__: (string) => Promise<void>;
declare var __parcel__importScripts__: (string) => Promise<void>;
declare var globalThis: typeof self;
declare var ServiceWorkerGlobalScope: Object;
*/ var OVERLAY_ID = "__parcel__error__overlay__";
var OldModule = module.bundle.Module;
function Module(moduleName) {
    OldModule.call(this, moduleName);
    this.hot = {
        data: module.bundle.hotData[moduleName],
        _acceptCallbacks: [],
        _disposeCallbacks: [],
        accept: function(fn) {
            this._acceptCallbacks.push(fn || function() {});
        },
        dispose: function(fn) {
            this._disposeCallbacks.push(fn);
        }
    };
    module.bundle.hotData[moduleName] = undefined;
}
module.bundle.Module = Module;
module.bundle.hotData = {};
var checkedAssets, assetsToDispose, assetsToAccept /*: Array<[ParcelRequire, string]> */ ;
function getHostname() {
    return HMR_HOST || (location.protocol.indexOf("http") === 0 ? location.hostname : "localhost");
}
function getPort() {
    return HMR_PORT || location.port;
} // eslint-disable-next-line no-redeclare
var parent = module.bundle.parent;
if ((!parent || !parent.isParcelRequire) && typeof WebSocket !== "undefined") {
    var hostname = getHostname();
    var port = getPort();
    var protocol = HMR_SECURE || location.protocol == "https:" && !/localhost|127.0.0.1|0.0.0.0/.test(hostname) ? "wss" : "ws";
    var ws = new WebSocket(protocol + "://" + hostname + (port ? ":" + port : "") + "/"); // Web extension context
    var extCtx = typeof chrome === "undefined" ? typeof browser === "undefined" ? null : browser : chrome; // Safari doesn't support sourceURL in error stacks.
    // eval may also be disabled via CSP, so do a quick check.
    var supportsSourceURL = false;
    try {
        (0, eval)('throw new Error("test"); //# sourceURL=test.js');
    } catch (err) {
        supportsSourceURL = err.stack.includes("test.js");
    } // $FlowFixMe
    ws.onmessage = async function(event) {
        checkedAssets = {} /*: {|[string]: boolean|} */ ;
        assetsToAccept = [];
        assetsToDispose = [];
        var data = JSON.parse(event.data);
        if (data.type === "update") {
            // Remove error overlay if there is one
            if (typeof document !== "undefined") removeErrorOverlay();
            let assets = data.assets.filter((asset)=>asset.envHash === HMR_ENV_HASH); // Handle HMR Update
            let handled = assets.every((asset)=>{
                return asset.type === "css" || asset.type === "js" && hmrAcceptCheck(module.bundle.root, asset.id, asset.depsByBundle);
            });
            if (handled) {
                console.clear(); // Dispatch custom event so other runtimes (e.g React Refresh) are aware.
                if (typeof window !== "undefined" && typeof CustomEvent !== "undefined") window.dispatchEvent(new CustomEvent("parcelhmraccept"));
                await hmrApplyUpdates(assets); // Dispose all old assets.
                let processedAssets = {} /*: {|[string]: boolean|} */ ;
                for(let i = 0; i < assetsToDispose.length; i++){
                    let id = assetsToDispose[i][1];
                    if (!processedAssets[id]) {
                        hmrDispose(assetsToDispose[i][0], id);
                        processedAssets[id] = true;
                    }
                } // Run accept callbacks. This will also re-execute other disposed assets in topological order.
                processedAssets = {};
                for(let i = 0; i < assetsToAccept.length; i++){
                    let id = assetsToAccept[i][1];
                    if (!processedAssets[id]) {
                        hmrAccept(assetsToAccept[i][0], id);
                        processedAssets[id] = true;
                    }
                }
            } else fullReload();
        }
        if (data.type === "error") {
            // Log parcel errors to console
            for (let ansiDiagnostic of data.diagnostics.ansi){
                let stack = ansiDiagnostic.codeframe ? ansiDiagnostic.codeframe : ansiDiagnostic.stack;
                console.error("\uD83D\uDEA8 [parcel]: " + ansiDiagnostic.message + "\n" + stack + "\n\n" + ansiDiagnostic.hints.join("\n"));
            }
            if (typeof document !== "undefined") {
                // Render the fancy html overlay
                removeErrorOverlay();
                var overlay = createErrorOverlay(data.diagnostics.html); // $FlowFixMe
                document.body.appendChild(overlay);
            }
        }
    };
    ws.onerror = function(e) {
        console.error(e.message);
    };
    ws.onclose = function() {
        console.warn("[parcel] \uD83D\uDEA8 Connection to the HMR server was lost");
    };
}
function removeErrorOverlay() {
    var overlay = document.getElementById(OVERLAY_ID);
    if (overlay) {
        overlay.remove();
        console.log("[parcel] ✨ Error resolved");
    }
}
function createErrorOverlay(diagnostics) {
    var overlay = document.createElement("div");
    overlay.id = OVERLAY_ID;
    let errorHTML = '<div style="background: black; opacity: 0.85; font-size: 16px; color: white; position: fixed; height: 100%; width: 100%; top: 0px; left: 0px; padding: 30px; font-family: Menlo, Consolas, monospace; z-index: 9999;">';
    for (let diagnostic of diagnostics){
        let stack = diagnostic.frames.length ? diagnostic.frames.reduce((p, frame)=>{
            return `${p}
<a href="/__parcel_launch_editor?file=${encodeURIComponent(frame.location)}" style="text-decoration: underline; color: #888" onclick="fetch(this.href); return false">${frame.location}</a>
${frame.code}`;
        }, "") : diagnostic.stack;
        errorHTML += `
      <div>
        <div style="font-size: 18px; font-weight: bold; margin-top: 20px;">
          🚨 ${diagnostic.message}
        </div>
        <pre>${stack}</pre>
        <div>
          ${diagnostic.hints.map((hint)=>"<div>\uD83D\uDCA1 " + hint + "</div>").join("")}
        </div>
        ${diagnostic.documentation ? `<div>📝 <a style="color: violet" href="${diagnostic.documentation}" target="_blank">Learn more</a></div>` : ""}
      </div>
    `;
    }
    errorHTML += "</div>";
    overlay.innerHTML = errorHTML;
    return overlay;
}
function fullReload() {
    if ("reload" in location) location.reload();
    else if (extCtx && extCtx.runtime && extCtx.runtime.reload) extCtx.runtime.reload();
}
function getParents(bundle, id) /*: Array<[ParcelRequire, string]> */ {
    var modules = bundle.modules;
    if (!modules) return [];
    var parents = [];
    var k, d, dep;
    for(k in modules)for(d in modules[k][1]){
        dep = modules[k][1][d];
        if (dep === id || Array.isArray(dep) && dep[dep.length - 1] === id) parents.push([
            bundle,
            k
        ]);
    }
    if (bundle.parent) parents = parents.concat(getParents(bundle.parent, id));
    return parents;
}
function updateLink(link) {
    var newLink = link.cloneNode();
    newLink.onload = function() {
        if (link.parentNode !== null) // $FlowFixMe
        link.parentNode.removeChild(link);
    };
    newLink.setAttribute("href", link.getAttribute("href").split("?")[0] + "?" + Date.now()); // $FlowFixMe
    link.parentNode.insertBefore(newLink, link.nextSibling);
}
var cssTimeout = null;
function reloadCSS() {
    if (cssTimeout) return;
    cssTimeout = setTimeout(function() {
        var links = document.querySelectorAll('link[rel="stylesheet"]');
        for(var i = 0; i < links.length; i++){
            // $FlowFixMe[incompatible-type]
            var href = links[i].getAttribute("href");
            var hostname = getHostname();
            var servedFromHMRServer = hostname === "localhost" ? new RegExp("^(https?:\\/\\/(0.0.0.0|127.0.0.1)|localhost):" + getPort()).test(href) : href.indexOf(hostname + ":" + getPort());
            var absolute = /^https?:\/\//i.test(href) && href.indexOf(location.origin) !== 0 && !servedFromHMRServer;
            if (!absolute) updateLink(links[i]);
        }
        cssTimeout = null;
    }, 50);
}
function hmrDownload(asset) {
    if (asset.type === "js") {
        if (typeof document !== "undefined") {
            let script = document.createElement("script");
            script.src = asset.url + "?t=" + Date.now();
            if (asset.outputFormat === "esmodule") script.type = "module";
            return new Promise((resolve, reject)=>{
                var _document$head;
                script.onload = ()=>resolve(script);
                script.onerror = reject;
                (_document$head = document.head) === null || _document$head === void 0 || _document$head.appendChild(script);
            });
        } else if (typeof importScripts === "function") {
            // Worker scripts
            if (asset.outputFormat === "esmodule") return import(asset.url + "?t=" + Date.now());
            else return new Promise((resolve, reject)=>{
                try {
                    importScripts(asset.url + "?t=" + Date.now());
                    resolve();
                } catch (err) {
                    reject(err);
                }
            });
        }
    }
}
async function hmrApplyUpdates(assets) {
    global.parcelHotUpdate = Object.create(null);
    let scriptsToRemove;
    try {
        // If sourceURL comments aren't supported in eval, we need to load
        // the update from the dev server over HTTP so that stack traces
        // are correct in errors/logs. This is much slower than eval, so
        // we only do it if needed (currently just Safari).
        // https://bugs.webkit.org/show_bug.cgi?id=137297
        // This path is also taken if a CSP disallows eval.
        if (!supportsSourceURL) {
            let promises = assets.map((asset)=>{
                var _hmrDownload;
                return (_hmrDownload = hmrDownload(asset)) === null || _hmrDownload === void 0 ? void 0 : _hmrDownload.catch((err)=>{
                    // Web extension bugfix for Chromium
                    // https://bugs.chromium.org/p/chromium/issues/detail?id=1255412#c12
                    if (extCtx && extCtx.runtime && extCtx.runtime.getManifest().manifest_version == 3) {
                        if (typeof ServiceWorkerGlobalScope != "undefined" && global instanceof ServiceWorkerGlobalScope) {
                            extCtx.runtime.reload();
                            return;
                        }
                        asset.url = extCtx.runtime.getURL("/__parcel_hmr_proxy__?url=" + encodeURIComponent(asset.url + "?t=" + Date.now()));
                        return hmrDownload(asset);
                    }
                    throw err;
                });
            });
            scriptsToRemove = await Promise.all(promises);
        }
        assets.forEach(function(asset) {
            hmrApply(module.bundle.root, asset);
        });
    } finally{
        delete global.parcelHotUpdate;
        if (scriptsToRemove) scriptsToRemove.forEach((script)=>{
            if (script) {
                var _document$head2;
                (_document$head2 = document.head) === null || _document$head2 === void 0 || _document$head2.removeChild(script);
            }
        });
    }
}
function hmrApply(bundle, asset) {
    var modules = bundle.modules;
    if (!modules) return;
    if (asset.type === "css") reloadCSS();
    else if (asset.type === "js") {
        let deps = asset.depsByBundle[bundle.HMR_BUNDLE_ID];
        if (deps) {
            if (modules[asset.id]) {
                // Remove dependencies that are removed and will become orphaned.
                // This is necessary so that if the asset is added back again, the cache is gone, and we prevent a full page reload.
                let oldDeps = modules[asset.id][1];
                for(let dep in oldDeps)if (!deps[dep] || deps[dep] !== oldDeps[dep]) {
                    let id = oldDeps[dep];
                    let parents = getParents(module.bundle.root, id);
                    if (parents.length === 1) hmrDelete(module.bundle.root, id);
                }
            }
            if (supportsSourceURL) // Global eval. We would use `new Function` here but browser
            // support for source maps is better with eval.
            (0, eval)(asset.output);
             // $FlowFixMe
            let fn = global.parcelHotUpdate[asset.id];
            modules[asset.id] = [
                fn,
                deps
            ];
        } else if (bundle.parent) hmrApply(bundle.parent, asset);
    }
}
function hmrDelete(bundle, id) {
    let modules = bundle.modules;
    if (!modules) return;
    if (modules[id]) {
        // Collect dependencies that will become orphaned when this module is deleted.
        let deps = modules[id][1];
        let orphans = [];
        for(let dep in deps){
            let parents = getParents(module.bundle.root, deps[dep]);
            if (parents.length === 1) orphans.push(deps[dep]);
        } // Delete the module. This must be done before deleting dependencies in case of circular dependencies.
        delete modules[id];
        delete bundle.cache[id]; // Now delete the orphans.
        orphans.forEach((id)=>{
            hmrDelete(module.bundle.root, id);
        });
    } else if (bundle.parent) hmrDelete(bundle.parent, id);
}
function hmrAcceptCheck(bundle, id, depsByBundle) {
    if (hmrAcceptCheckOne(bundle, id, depsByBundle)) return true;
     // Traverse parents breadth first. All possible ancestries must accept the HMR update, or we'll reload.
    let parents = getParents(module.bundle.root, id);
    let accepted = false;
    while(parents.length > 0){
        let v = parents.shift();
        let a = hmrAcceptCheckOne(v[0], v[1], null);
        if (a) // If this parent accepts, stop traversing upward, but still consider siblings.
        accepted = true;
        else {
            // Otherwise, queue the parents in the next level upward.
            let p = getParents(module.bundle.root, v[1]);
            if (p.length === 0) {
                // If there are no parents, then we've reached an entry without accepting. Reload.
                accepted = false;
                break;
            }
            parents.push(...p);
        }
    }
    return accepted;
}
function hmrAcceptCheckOne(bundle, id, depsByBundle) {
    var modules = bundle.modules;
    if (!modules) return;
    if (depsByBundle && !depsByBundle[bundle.HMR_BUNDLE_ID]) {
        // If we reached the root bundle without finding where the asset should go,
        // there's nothing to do. Mark as "accepted" so we don't reload the page.
        if (!bundle.parent) return true;
        return hmrAcceptCheck(bundle.parent, id, depsByBundle);
    }
    if (checkedAssets[id]) return true;
    checkedAssets[id] = true;
    var cached = bundle.cache[id];
    assetsToDispose.push([
        bundle,
        id
    ]);
    if (!cached || cached.hot && cached.hot._acceptCallbacks.length) {
        assetsToAccept.push([
            bundle,
            id
        ]);
        return true;
    }
}
function hmrDispose(bundle, id) {
    var cached = bundle.cache[id];
    bundle.hotData[id] = {};
    if (cached && cached.hot) cached.hot.data = bundle.hotData[id];
    if (cached && cached.hot && cached.hot._disposeCallbacks.length) cached.hot._disposeCallbacks.forEach(function(cb) {
        cb(bundle.hotData[id]);
    });
    delete bundle.cache[id];
}
function hmrAccept(bundle, id) {
    // Execute the module.
    bundle(id); // Run the accept callbacks in the new version of the module.
    var cached = bundle.cache[id];
    if (cached && cached.hot && cached.hot._acceptCallbacks.length) cached.hot._acceptCallbacks.forEach(function(cb) {
        var assetsToAlsoAccept = cb(function() {
            return getParents(module.bundle.root, id);
        });
        if (assetsToAlsoAccept && assetsToAccept.length) {
            assetsToAlsoAccept.forEach(function(a) {
                hmrDispose(a[0], a[1]);
            }); // $FlowFixMe[method-unbinding]
            assetsToAccept.push.apply(assetsToAccept, assetsToAlsoAccept);
        }
    });
}

},{}],"l35qe":[function(require,module,exports) {
var _htmxOrg = require("htmx.org");
var _ajaxHeader = require("htmx.org/dist/ext/ajax-header");
var _alpinejs = require("alpinejs");

},{"htmx.org":"kwetm","htmx.org/dist/ext/ajax-header":"jIgyh","alpinejs":"69hXP"}],"kwetm":[function(require,module,exports) {
(function(e, t) {
    if (typeof define === "function" && define.amd) define([], t);
    else e.htmx = e.htmx || t();
})(typeof self !== "undefined" ? self : this, function() {
    return function() {
        "use strict";
        var z = {
            onLoad: t,
            process: mt,
            on: D,
            off: X,
            trigger: ee,
            ajax: or,
            find: C,
            findAll: R,
            closest: A,
            values: function(e, t) {
                var r = Bt(e, t || "post");
                return r.values;
            },
            remove: O,
            addClass: q,
            removeClass: T,
            toggleClass: L,
            takeClass: H,
            defineExtension: dr,
            removeExtension: vr,
            logAll: E,
            logger: null,
            config: {
                historyEnabled: true,
                historyCacheSize: 10,
                refreshOnHistoryMiss: false,
                defaultSwapStyle: "innerHTML",
                defaultSwapDelay: 0,
                defaultSettleDelay: 20,
                includeIndicatorStyles: true,
                indicatorClass: "htmx-indicator",
                requestClass: "htmx-request",
                addedClass: "htmx-added",
                settlingClass: "htmx-settling",
                swappingClass: "htmx-swapping",
                allowEval: true,
                inlineScriptNonce: "",
                attributesToSettle: [
                    "class",
                    "style",
                    "width",
                    "height"
                ],
                withCredentials: false,
                timeout: 0,
                wsReconnectDelay: "full-jitter",
                wsBinaryType: "blob",
                disableSelector: "[hx-disable], [data-hx-disable]",
                useTemplateFragments: false,
                scrollBehavior: "smooth",
                defaultFocusScroll: false,
                getCacheBusterParam: false
            },
            parseInterval: v,
            _: e,
            createEventSource: function(e) {
                return new EventSource(e, {
                    withCredentials: true
                });
            },
            createWebSocket: function(e) {
                var t = new WebSocket(e, []);
                t.binaryType = z.config.wsBinaryType;
                return t;
            },
            version: "1.8.5"
        };
        var r = {
            addTriggerHandler: ft,
            bodyContains: re,
            canAccessLocalStorage: S,
            filterValues: Wt,
            hasAttribute: o,
            getAttributeValue: J,
            getClosestMatch: h,
            getExpressionVars: rr,
            getHeaders: _t,
            getInputValues: Bt,
            getInternalData: K,
            getSwapSpecification: Gt,
            getTriggerSpecs: Xe,
            getTarget: se,
            makeFragment: f,
            mergeObjects: ne,
            makeSettleInfo: Zt,
            oobSwap: V,
            selectAndSwap: Oe,
            settleImmediately: At,
            shouldCancel: Ve,
            triggerEvent: ee,
            triggerErrorEvent: Q,
            withExtensions: wt
        };
        var n = [
            "get",
            "post",
            "put",
            "delete",
            "patch"
        ];
        var i = n.map(function(e) {
            return "[hx-" + e + "], [data-hx-" + e + "]";
        }).join(", ");
        function v(e) {
            if (e == undefined) return undefined;
            if (e.slice(-2) == "ms") return parseFloat(e.slice(0, -2)) || undefined;
            if (e.slice(-1) == "s") return parseFloat(e.slice(0, -1)) * 1e3 || undefined;
            if (e.slice(-1) == "m") return parseFloat(e.slice(0, -1)) * 60000 || undefined;
            return parseFloat(e) || undefined;
        }
        function G(e, t) {
            return e.getAttribute && e.getAttribute(t);
        }
        function o(e, t) {
            return e.hasAttribute && (e.hasAttribute(t) || e.hasAttribute("data-" + t));
        }
        function J(e, t) {
            return G(e, t) || G(e, "data-" + t);
        }
        function u(e) {
            return e.parentElement;
        }
        function $() {
            return document;
        }
        function h(e, t) {
            while(e && !t(e))e = u(e);
            return e ? e : null;
        }
        function a(e, t, r) {
            var n = J(t, r);
            var i = J(t, "hx-disinherit");
            if (e !== t && i && (i === "*" || i.split(" ").indexOf(r) >= 0)) return "unset";
            else return n;
        }
        function Z(t, r) {
            var n = null;
            h(t, function(e) {
                return n = a(t, e, r);
            });
            if (n !== "unset") return n;
        }
        function d(e, t) {
            var r = e.matches || e.matchesSelector || e.msMatchesSelector || e.mozMatchesSelector || e.webkitMatchesSelector || e.oMatchesSelector;
            return r && r.call(e, t);
        }
        function s(e) {
            var t = /<([a-z][^\/\0>\x20\t\r\n\f]*)/i;
            var r = t.exec(e);
            if (r) return r[1].toLowerCase();
            else return "";
        }
        function l(e, t) {
            var r = new DOMParser;
            var n = r.parseFromString(e, "text/html");
            var i = n.body;
            while(t > 0){
                t--;
                i = i.firstChild;
            }
            if (i == null) i = $().createDocumentFragment();
            return i;
        }
        function f(e) {
            if (z.config.useTemplateFragments) {
                var t = l("<body><template>" + e + "</template></body>", 0);
                return t.querySelector("template").content;
            } else {
                var r = s(e);
                switch(r){
                    case "thead":
                    case "tbody":
                    case "tfoot":
                    case "colgroup":
                    case "caption":
                        return l("<table>" + e + "</table>", 1);
                    case "col":
                        return l("<table><colgroup>" + e + "</colgroup></table>", 2);
                    case "tr":
                        return l("<table><tbody>" + e + "</tbody></table>", 2);
                    case "td":
                    case "th":
                        return l("<table><tbody><tr>" + e + "</tr></tbody></table>", 3);
                    case "script":
                        return l("<div>" + e + "</div>", 1);
                    default:
                        return l(e, 0);
                }
            }
        }
        function te(e) {
            if (e) e();
        }
        function g(e, t) {
            return Object.prototype.toString.call(e) === "[object " + t + "]";
        }
        function p(e) {
            return g(e, "Function");
        }
        function m(e) {
            return g(e, "Object");
        }
        function K(e) {
            var t = "htmx-internal-data";
            var r = e[t];
            if (!r) r = e[t] = {};
            return r;
        }
        function y(e) {
            var t = [];
            if (e) for(var r = 0; r < e.length; r++)t.push(e[r]);
            return t;
        }
        function Y(e, t) {
            if (e) for(var r = 0; r < e.length; r++)t(e[r]);
        }
        function x(e) {
            var t = e.getBoundingClientRect();
            var r = t.top;
            var n = t.bottom;
            return r < window.innerHeight && n >= 0;
        }
        function re(e) {
            if (e.getRootNode && e.getRootNode() instanceof ShadowRoot) return $().body.contains(e.getRootNode().host);
            else return $().body.contains(e);
        }
        function b(e) {
            return e.trim().split(/\s+/);
        }
        function ne(e, t) {
            for(var r in t)if (t.hasOwnProperty(r)) e[r] = t[r];
            return e;
        }
        function w(e) {
            try {
                return JSON.parse(e);
            } catch (e) {
                St(e);
                return null;
            }
        }
        function S() {
            var e = "htmx:localStorageTest";
            try {
                localStorage.setItem(e, e);
                localStorage.removeItem(e);
                return true;
            } catch (e) {
                return false;
            }
        }
        function e(e) {
            return Qt($().body, function() {
                return eval(e);
            });
        }
        function t(t) {
            var e = z.on("htmx:load", function(e) {
                t(e.detail.elt);
            });
            return e;
        }
        function E() {
            z.logger = function(e, t, r) {
                if (console) console.log(t, e, r);
            };
        }
        function C(e, t) {
            if (t) return e.querySelector(t);
            else return C($(), e);
        }
        function R(e, t) {
            if (t) return e.querySelectorAll(t);
            else return R($(), e);
        }
        function O(e, t) {
            e = M(e);
            if (t) setTimeout(function() {
                O(e);
            }, t);
            else e.parentElement.removeChild(e);
        }
        function q(e, t, r) {
            e = M(e);
            if (r) setTimeout(function() {
                q(e, t);
            }, r);
            else e.classList && e.classList.add(t);
        }
        function T(e, t, r) {
            e = M(e);
            if (r) setTimeout(function() {
                T(e, t);
            }, r);
            else if (e.classList) {
                e.classList.remove(t);
                if (e.classList.length === 0) e.removeAttribute("class");
            }
        }
        function L(e, t) {
            e = M(e);
            e.classList.toggle(t);
        }
        function H(e, t) {
            e = M(e);
            Y(e.parentElement.children, function(e) {
                T(e, t);
            });
            q(e, t);
        }
        function A(e, t) {
            e = M(e);
            if (e.closest) return e.closest(t);
            else {
                do {
                    if (e == null || d(e, t)) return e;
                }while (e = e && u(e));
                return null;
            }
        }
        function N(e, t) {
            if (t.indexOf("closest ") === 0) return [
                A(e, t.substr(8))
            ];
            else if (t.indexOf("find ") === 0) return [
                C(e, t.substr(5))
            ];
            else if (t.indexOf("next ") === 0) return [
                I(e, t.substr(5))
            ];
            else if (t.indexOf("previous ") === 0) return [
                k(e, t.substr(9))
            ];
            else if (t === "document") return [
                document
            ];
            else if (t === "window") return [
                window
            ];
            else return $().querySelectorAll(t);
        }
        var I = function(e, t) {
            var r = $().querySelectorAll(t);
            for(var n = 0; n < r.length; n++){
                var i = r[n];
                if (i.compareDocumentPosition(e) === Node.DOCUMENT_POSITION_PRECEDING) return i;
            }
        };
        var k = function(e, t) {
            var r = $().querySelectorAll(t);
            for(var n = r.length - 1; n >= 0; n--){
                var i = r[n];
                if (i.compareDocumentPosition(e) === Node.DOCUMENT_POSITION_FOLLOWING) return i;
            }
        };
        function ie(e, t) {
            if (t) return N(e, t)[0];
            else return N($().body, e)[0];
        }
        function M(e) {
            if (g(e, "String")) return C(e);
            else return e;
        }
        function P(e, t, r) {
            if (p(t)) return {
                target: $().body,
                event: e,
                listener: t
            };
            else return {
                target: M(e),
                event: t,
                listener: r
            };
        }
        function D(t, r, n) {
            pr(function() {
                var e = P(t, r, n);
                e.target.addEventListener(e.event, e.listener);
            });
            var e = p(r);
            return e ? r : n;
        }
        function X(t, r, n) {
            pr(function() {
                var e = P(t, r, n);
                e.target.removeEventListener(e.event, e.listener);
            });
            return p(r) ? r : n;
        }
        var ae = $().createElement("output");
        function F(e, t) {
            var r = Z(e, t);
            if (r) {
                if (r === "this") return [
                    oe(e, t)
                ];
                else {
                    var n = N(e, r);
                    if (n.length === 0) {
                        St('The selector "' + r + '" on ' + t + " returned no matches!");
                        return [
                            ae
                        ];
                    } else return n;
                }
            }
        }
        function oe(e, t) {
            return h(e, function(e) {
                return J(e, t) != null;
            });
        }
        function se(e) {
            var t = Z(e, "hx-target");
            if (t) {
                if (t === "this") return oe(e, "hx-target");
                else return ie(e, t);
            } else {
                var r = K(e);
                if (r.boosted) return $().body;
                else return e;
            }
        }
        function B(e) {
            var t = z.config.attributesToSettle;
            for(var r = 0; r < t.length; r++){
                if (e === t[r]) return true;
            }
            return false;
        }
        function j(t, r) {
            Y(t.attributes, function(e) {
                if (!r.hasAttribute(e.name) && B(e.name)) t.removeAttribute(e.name);
            });
            Y(r.attributes, function(e) {
                if (B(e.name)) t.setAttribute(e.name, e.value);
            });
        }
        function U(e, t) {
            var r = gr(t);
            for(var n = 0; n < r.length; n++){
                var i = r[n];
                try {
                    if (i.isInlineSwap(e)) return true;
                } catch (e) {
                    St(e);
                }
            }
            return e === "outerHTML";
        }
        function V(e, i, a) {
            var t = "#" + i.id;
            var o = "outerHTML";
            if (e === "true") ;
            else if (e.indexOf(":") > 0) {
                o = e.substr(0, e.indexOf(":"));
                t = e.substr(e.indexOf(":") + 1, e.length);
            } else o = e;
            var r = $().querySelectorAll(t);
            if (r) {
                Y(r, function(e) {
                    var t;
                    var r = i.cloneNode(true);
                    t = $().createDocumentFragment();
                    t.appendChild(r);
                    if (!U(o, e)) t = r;
                    var n = {
                        shouldSwap: true,
                        target: e,
                        fragment: t
                    };
                    if (!ee(e, "htmx:oobBeforeSwap", n)) return;
                    e = n.target;
                    if (n["shouldSwap"]) Ce(o, e, e, t, a);
                    Y(a.elts, function(e) {
                        ee(e, "htmx:oobAfterSwap", n);
                    });
                });
                i.parentNode.removeChild(i);
            } else {
                i.parentNode.removeChild(i);
                Q($().body, "htmx:oobErrorNoTarget", {
                    content: i
                });
            }
            return e;
        }
        function _(e, t, r) {
            var n = Z(e, "hx-select-oob");
            if (n) {
                var i = n.split(",");
                for(let e = 0; e < i.length; e++){
                    var a = i[e].split(":", 2);
                    var o = a[0];
                    if (o.indexOf("#") === 0) o = o.substring(1);
                    var s = a[1] || "true";
                    var l = t.querySelector("#" + o);
                    if (l) V(s, l, r);
                }
            }
            Y(R(t, "[hx-swap-oob], [data-hx-swap-oob]"), function(e) {
                var t = J(e, "hx-swap-oob");
                if (t != null) V(t, e, r);
            });
        }
        function W(e) {
            Y(R(e, "[hx-preserve], [data-hx-preserve]"), function(e) {
                var t = J(e, "id");
                var r = $().getElementById(t);
                if (r != null) e.parentNode.replaceChild(r, e);
            });
        }
        function le(n, e, i) {
            Y(e.querySelectorAll("[id]"), function(e) {
                if (e.id && e.id.length > 0) {
                    var t = n.querySelector(e.tagName + "[id='" + e.id + "']");
                    if (t && t !== n) {
                        var r = e.cloneNode();
                        j(e, t);
                        i.tasks.push(function() {
                            j(e, r);
                        });
                    }
                }
            });
        }
        function ue(e) {
            return function() {
                T(e, z.config.addedClass);
                mt(e);
                ht(e);
                fe(e);
                ee(e, "htmx:load");
            };
        }
        function fe(e) {
            var t = "[autofocus]";
            var r = d(e, t) ? e : e.querySelector(t);
            if (r != null) r.focus();
        }
        function ce(e, t, r, n) {
            le(e, r, n);
            while(r.childNodes.length > 0){
                var i = r.firstChild;
                q(i, z.config.addedClass);
                e.insertBefore(i, t);
                if (i.nodeType !== Node.TEXT_NODE && i.nodeType !== Node.COMMENT_NODE) n.tasks.push(ue(i));
            }
        }
        function he(e, t) {
            var r = 0;
            while(r < e.length)t = (t << 5) - t + e.charCodeAt(r++) | 0;
            return t;
        }
        function de(e) {
            var t = 0;
            if (e.attributes) for(var r = 0; r < e.attributes.length; r++){
                var n = e.attributes[r];
                if (n.value) {
                    t = he(n.name, t);
                    t = he(n.value, t);
                }
            }
            return t;
        }
        function ve(e) {
            var t = K(e);
            if (t.webSocket) t.webSocket.close();
            if (t.sseEventSource) t.sseEventSource.close();
            if (t.listenerInfos) Y(t.listenerInfos, function(e) {
                if (e.on) e.on.removeEventListener(e.trigger, e.listener);
            });
        }
        function ge(e) {
            ee(e, "htmx:beforeCleanupElement");
            ve(e);
            if (e.children) Y(e.children, function(e) {
                ge(e);
            });
        }
        function pe(e, t, r) {
            if (e.tagName === "BODY") return Se(e, t, r);
            else {
                var n;
                var i = e.previousSibling;
                ce(u(e), e, t, r);
                if (i == null) n = u(e).firstChild;
                else n = i.nextSibling;
                K(e).replacedWith = n;
                r.elts = [];
                while(n && n !== e){
                    if (n.nodeType === Node.ELEMENT_NODE) r.elts.push(n);
                    n = n.nextElementSibling;
                }
                ge(e);
                u(e).removeChild(e);
            }
        }
        function me(e, t, r) {
            return ce(e, e.firstChild, t, r);
        }
        function ye(e, t, r) {
            return ce(u(e), e, t, r);
        }
        function xe(e, t, r) {
            return ce(e, null, t, r);
        }
        function be(e, t, r) {
            return ce(u(e), e.nextSibling, t, r);
        }
        function we(e, t, r) {
            ge(e);
            return u(e).removeChild(e);
        }
        function Se(e, t, r) {
            var n = e.firstChild;
            ce(e, n, t, r);
            if (n) {
                while(n.nextSibling){
                    ge(n.nextSibling);
                    e.removeChild(n.nextSibling);
                }
                ge(n);
                e.removeChild(n);
            }
        }
        function Ee(e, t) {
            var r = Z(e, "hx-select");
            if (r) {
                var n = $().createDocumentFragment();
                Y(t.querySelectorAll(r), function(e) {
                    n.appendChild(e);
                });
                t = n;
            }
            return t;
        }
        function Ce(e, t, r, n, i) {
            switch(e){
                case "none":
                    return;
                case "outerHTML":
                    pe(r, n, i);
                    return;
                case "afterbegin":
                    me(r, n, i);
                    return;
                case "beforebegin":
                    ye(r, n, i);
                    return;
                case "beforeend":
                    xe(r, n, i);
                    return;
                case "afterend":
                    be(r, n, i);
                    return;
                case "delete":
                    we(r, n, i);
                    return;
                default:
                    var a = gr(t);
                    for(var o = 0; o < a.length; o++){
                        var f = a[o];
                        try {
                            var s = f.handleSwap(e, r, n, i);
                            if (s) {
                                if (typeof s.length !== "undefined") for(var l = 0; l < s.length; l++){
                                    var u = s[l];
                                    if (u.nodeType !== Node.TEXT_NODE && u.nodeType !== Node.COMMENT_NODE) i.tasks.push(ue(u));
                                }
                                return;
                            }
                        } catch (e) {
                            St(e);
                        }
                    }
                    if (e === "innerHTML") Se(r, n, i);
                    else Ce(z.config.defaultSwapStyle, t, r, n, i);
            }
        }
        function Re(e) {
            if (e.indexOf("<title") > -1) {
                var t = e.replace(/<svg(\s[^>]*>|>)([\s\S]*?)<\/svg>/gim, "");
                var r = t.match(/<title(\s[^>]*>|>)([\s\S]*?)<\/title>/im);
                if (r) return r[2];
            }
        }
        function Oe(e, t, r, n, i) {
            i.title = Re(n);
            var a = f(n);
            if (a) {
                _(r, a, i);
                a = Ee(r, a);
                W(a);
                return Ce(e, r, t, a, i);
            }
        }
        function qe(e, t, r) {
            var n = e.getResponseHeader(t);
            if (n.indexOf("{") === 0) {
                var i = w(n);
                for(var a in i)if (i.hasOwnProperty(a)) {
                    var o = i[a];
                    if (!m(o)) o = {
                        value: o
                    };
                    ee(r, a, o);
                }
            } else ee(r, n, []);
        }
        var Te = /\s/;
        var Le = /[\s,]/;
        var He = /[_$a-zA-Z]/;
        var Ae = /[_$a-zA-Z0-9]/;
        var Ne = [
            '"',
            "'",
            "/"
        ];
        var Ie = /[^\s]/;
        function ke(e) {
            var t = [];
            var r = 0;
            while(r < e.length){
                if (He.exec(e.charAt(r))) {
                    var n = r;
                    while(Ae.exec(e.charAt(r + 1)))r++;
                    t.push(e.substr(n, r - n + 1));
                } else if (Ne.indexOf(e.charAt(r)) !== -1) {
                    var i = e.charAt(r);
                    var n = r;
                    r++;
                    while(r < e.length && e.charAt(r) !== i){
                        if (e.charAt(r) === "\\") r++;
                        r++;
                    }
                    t.push(e.substr(n, r - n + 1));
                } else {
                    var a = e.charAt(r);
                    t.push(a);
                }
                r++;
            }
            return t;
        }
        function Me(e, t, r) {
            return He.exec(e.charAt(0)) && e !== "true" && e !== "false" && e !== "this" && e !== r && t !== ".";
        }
        function Pe(e, t, r) {
            if (t[0] === "[") {
                t.shift();
                var n = 1;
                var i = " return (function(" + r + "){ return (";
                var a = null;
                while(t.length > 0){
                    var o = t[0];
                    if (o === "]") {
                        n--;
                        if (n === 0) {
                            if (a === null) i = i + "true";
                            t.shift();
                            i += ")})";
                            try {
                                var s = Qt(e, function() {
                                    return Function(i)();
                                }, function() {
                                    return true;
                                });
                                s.source = i;
                                return s;
                            } catch (e) {
                                Q($().body, "htmx:syntax:error", {
                                    error: e,
                                    source: i
                                });
                                return null;
                            }
                        }
                    } else if (o === "[") n++;
                    if (Me(o, a, r)) i += "((" + r + "." + o + ") ? (" + r + "." + o + ") : (window." + o + "))";
                    else i = i + o;
                    a = t.shift();
                }
            }
        }
        function c(e, t) {
            var r = "";
            while(e.length > 0 && !e[0].match(t))r += e.shift();
            return r;
        }
        var De = "input, textarea, select";
        function Xe(e) {
            var t = J(e, "hx-trigger");
            var r = [];
            if (t) {
                var n = ke(t);
                do {
                    c(n, Ie);
                    var f = n.length;
                    var i = c(n, /[,\[\s]/);
                    if (i !== "") {
                        if (i === "every") {
                            var a = {
                                trigger: "every"
                            };
                            c(n, Ie);
                            a.pollInterval = v(c(n, /[,\[\s]/));
                            c(n, Ie);
                            var o = Pe(e, n, "event");
                            if (o) a.eventFilter = o;
                            r.push(a);
                        } else if (i.indexOf("sse:") === 0) r.push({
                            trigger: "sse",
                            sseEvent: i.substr(4)
                        });
                        else {
                            var s = {
                                trigger: i
                            };
                            var o = Pe(e, n, "event");
                            if (o) s.eventFilter = o;
                            while(n.length > 0 && n[0] !== ","){
                                c(n, Ie);
                                var l = n.shift();
                                if (l === "changed") s.changed = true;
                                else if (l === "once") s.once = true;
                                else if (l === "consume") s.consume = true;
                                else if (l === "delay" && n[0] === ":") {
                                    n.shift();
                                    s.delay = v(c(n, Le));
                                } else if (l === "from" && n[0] === ":") {
                                    n.shift();
                                    var u = c(n, Le);
                                    if (u === "closest" || u === "find" || u === "next" || u === "previous") {
                                        n.shift();
                                        u += " " + c(n, Le);
                                    }
                                    s.from = u;
                                } else if (l === "target" && n[0] === ":") {
                                    n.shift();
                                    s.target = c(n, Le);
                                } else if (l === "throttle" && n[0] === ":") {
                                    n.shift();
                                    s.throttle = v(c(n, Le));
                                } else if (l === "queue" && n[0] === ":") {
                                    n.shift();
                                    s.queue = c(n, Le);
                                } else if ((l === "root" || l === "threshold") && n[0] === ":") {
                                    n.shift();
                                    s[l] = c(n, Le);
                                } else Q(e, "htmx:syntax:error", {
                                    token: n.shift()
                                });
                            }
                            r.push(s);
                        }
                    }
                    if (n.length === f) Q(e, "htmx:syntax:error", {
                        token: n.shift()
                    });
                    c(n, Ie);
                }while (n[0] === "," && n.shift());
            }
            if (r.length > 0) return r;
            else if (d(e, "form")) return [
                {
                    trigger: "submit"
                }
            ];
            else if (d(e, 'input[type="button"]')) return [
                {
                    trigger: "click"
                }
            ];
            else if (d(e, De)) return [
                {
                    trigger: "change"
                }
            ];
            else return [
                {
                    trigger: "click"
                }
            ];
        }
        function Fe(e) {
            K(e).cancelled = true;
        }
        function Be(e, t, r) {
            var n = K(e);
            n.timeout = setTimeout(function() {
                if (re(e) && n.cancelled !== true) {
                    if (!We(r, xt("hx:poll:trigger", {
                        triggerSpec: r,
                        target: e
                    }))) t(e);
                    Be(e, t, r);
                }
            }, r.pollInterval);
        }
        function je(e) {
            return location.hostname === e.hostname && G(e, "href") && G(e, "href").indexOf("#") !== 0;
        }
        function Ue(t, r, e) {
            if (t.tagName === "A" && je(t) && (t.target === "" || t.target === "_self") || t.tagName === "FORM") {
                r.boosted = true;
                var n, i;
                if (t.tagName === "A") {
                    n = "get";
                    i = G(t, "href");
                } else {
                    var a = G(t, "method");
                    n = a ? a.toLowerCase() : "get";
                    n;
                    i = G(t, "action");
                }
                e.forEach(function(e) {
                    ze(t, function(e, t) {
                        lr(n, i, e, t);
                    }, r, e, true);
                });
            }
        }
        function Ve(e, t) {
            if (e.type === "submit" || e.type === "click") {
                if (t.tagName === "FORM") return true;
                if (d(t, 'input[type="submit"], button') && A(t, "form") !== null) return true;
                if (t.tagName === "A" && t.href && (t.getAttribute("href") === "#" || t.getAttribute("href").indexOf("#") !== 0)) return true;
            }
            return false;
        }
        function _e(e, t) {
            return K(e).boosted && e.tagName === "A" && t.type === "click" && (t.ctrlKey || t.metaKey);
        }
        function We(e, t) {
            var r = e.eventFilter;
            if (r) try {
                return r(t) !== true;
            } catch (e) {
                Q($().body, "htmx:eventFilter:error", {
                    error: e,
                    source: r.source
                });
                return true;
            }
            return false;
        }
        function ze(a, o, e, s, l) {
            var t;
            if (s.from) t = N(a, s.from);
            else t = [
                a
            ];
            Y(t, function(n) {
                var i = function(e) {
                    if (!re(a)) {
                        n.removeEventListener(s.trigger, i);
                        return;
                    }
                    if (_e(a, e)) return;
                    if (l || Ve(e, a)) e.preventDefault();
                    if (We(s, e)) return;
                    var t = K(e);
                    t.triggerSpec = s;
                    if (t.handledFor == null) t.handledFor = [];
                    var r = K(a);
                    if (t.handledFor.indexOf(a) < 0) {
                        t.handledFor.push(a);
                        if (s.consume) e.stopPropagation();
                        if (s.target && e.target) {
                            if (!d(e.target, s.target)) return;
                        }
                        if (s.once) {
                            if (r.triggeredOnce) return;
                            else r.triggeredOnce = true;
                        }
                        if (s.changed) {
                            if (r.lastValue === a.value) return;
                            else r.lastValue = a.value;
                        }
                        if (r.delayed) clearTimeout(r.delayed);
                        if (r.throttle) return;
                        if (s.throttle) {
                            if (!r.throttle) {
                                o(a, e);
                                r.throttle = setTimeout(function() {
                                    r.throttle = null;
                                }, s.throttle);
                            }
                        } else if (s.delay) r.delayed = setTimeout(function() {
                            o(a, e);
                        }, s.delay);
                        else o(a, e);
                    }
                };
                if (e.listenerInfos == null) e.listenerInfos = [];
                e.listenerInfos.push({
                    trigger: s.trigger,
                    listener: i,
                    on: n
                });
                n.addEventListener(s.trigger, i);
            });
        }
        var Ge = false;
        var Je = null;
        function $e() {
            if (!Je) {
                Je = function() {
                    Ge = true;
                };
                window.addEventListener("scroll", Je);
                setInterval(function() {
                    if (Ge) {
                        Ge = false;
                        Y($().querySelectorAll("[hx-trigger='revealed'],[data-hx-trigger='revealed']"), function(e) {
                            Ze(e);
                        });
                    }
                }, 200);
            }
        }
        function Ze(t) {
            if (!o(t, "data-hx-revealed") && x(t)) {
                t.setAttribute("data-hx-revealed", "true");
                var e = K(t);
                if (e.initHash) ee(t, "revealed");
                else t.addEventListener("htmx:afterProcessNode", function(e) {
                    ee(t, "revealed");
                }, {
                    once: true
                });
            }
        }
        function Ke(e, t, r) {
            var n = b(r);
            for(var i = 0; i < n.length; i++){
                var a = n[i].split(/:(.+)/);
                if (a[0] === "connect") Ye(e, a[1], 0);
                if (a[0] === "send") et(e);
            }
        }
        function Ye(s, r, n) {
            if (!re(s)) return;
            if (r.indexOf("/") == 0) {
                var e = location.hostname + (location.port ? ":" + location.port : "");
                if (location.protocol == "https:") r = "wss://" + e + r;
                else if (location.protocol == "http:") r = "ws://" + e + r;
            }
            var t = z.createWebSocket(r);
            t.onerror = function(e) {
                Q(s, "htmx:wsError", {
                    error: e,
                    socket: t
                });
                Qe(s);
            };
            t.onclose = function(e) {
                if ([
                    1006,
                    1012,
                    1013
                ].indexOf(e.code) >= 0) {
                    var t = tt(n);
                    setTimeout(function() {
                        Ye(s, r, n + 1);
                    }, t);
                }
            };
            t.onopen = function(e) {
                n = 0;
            };
            K(s).webSocket = t;
            t.addEventListener("message", function(e) {
                if (Qe(s)) return;
                var t = e.data;
                wt(s, function(e) {
                    t = e.transformResponse(t, null, s);
                });
                var r = Zt(s);
                var n = f(t);
                var i = y(n.children);
                for(var a = 0; a < i.length; a++){
                    var o = i[a];
                    V(J(o, "hx-swap-oob") || "true", o, r);
                }
                At(r.tasks);
            });
        }
        function Qe(e) {
            if (!re(e)) {
                K(e).webSocket.close();
                return true;
            }
        }
        function et(u) {
            var f = h(u, function(e) {
                return K(e).webSocket != null;
            });
            if (f) u.addEventListener(Xe(u)[0].trigger, function(e) {
                var t = K(f).webSocket;
                var r = _t(u, f);
                var n = Bt(u, "post");
                var i = n.errors;
                var a = n.values;
                var o = rr(u);
                var s = ne(a, o);
                var l = Wt(s, u);
                l["HEADERS"] = r;
                if (i && i.length > 0) {
                    ee(u, "htmx:validation:halted", i);
                    return;
                }
                t.send(JSON.stringify(l));
                if (Ve(e, u)) e.preventDefault();
            });
            else Q(u, "htmx:noWebSocketSourceError");
        }
        function tt(e) {
            var t = z.config.wsReconnectDelay;
            if (typeof t === "function") return t(e);
            if (t === "full-jitter") {
                var r = Math.min(e, 6);
                var n = 1e3 * Math.pow(2, r);
                return n * Math.random();
            }
            St('htmx.config.wsReconnectDelay must either be a function or the string "full-jitter"');
        }
        function rt(e, t, r) {
            var n = b(r);
            for(var i = 0; i < n.length; i++){
                var a = n[i].split(/:(.+)/);
                if (a[0] === "connect") nt(e, a[1]);
                if (a[0] === "swap") it(e, a[1]);
            }
        }
        function nt(t, e) {
            var r = z.createEventSource(e);
            r.onerror = function(e) {
                Q(t, "htmx:sseError", {
                    error: e,
                    source: r
                });
                ot(t);
            };
            K(t).sseEventSource = r;
        }
        function it(a, o) {
            var s = h(a, st);
            if (s) {
                var l = K(s).sseEventSource;
                var u = function(e) {
                    if (ot(s)) {
                        l.removeEventListener(o, u);
                        return;
                    }
                    var t = e.data;
                    wt(a, function(e) {
                        t = e.transformResponse(t, null, a);
                    });
                    var r = Gt(a);
                    var n = se(a);
                    var i = Zt(a);
                    Oe(r.swapStyle, a, n, t, i);
                    At(i.tasks);
                    ee(a, "htmx:sseMessage", e);
                };
                K(a).sseListener = u;
                l.addEventListener(o, u);
            } else Q(a, "htmx:noSSESourceError");
        }
        function at(e, t, r) {
            var n = h(e, st);
            if (n) {
                var i = K(n).sseEventSource;
                var a = function() {
                    if (!ot(n)) {
                        if (re(e)) t(e);
                        else i.removeEventListener(r, a);
                    }
                };
                K(e).sseListener = a;
                i.addEventListener(r, a);
            } else Q(e, "htmx:noSSESourceError");
        }
        function ot(e) {
            if (!re(e)) {
                K(e).sseEventSource.close();
                return true;
            }
        }
        function st(e) {
            return K(e).sseEventSource != null;
        }
        function lt(e, t, r, n) {
            var i = function() {
                if (!r.loaded) {
                    r.loaded = true;
                    t(e);
                }
            };
            if (n) setTimeout(i, n);
            else i();
        }
        function ut(t, i, e) {
            var a = false;
            Y(n, function(r) {
                if (o(t, "hx-" + r)) {
                    var n1 = J(t, "hx-" + r);
                    a = true;
                    i.path = n1;
                    i.verb = r;
                    e.forEach(function(e) {
                        ft(t, e, i, function(e, t) {
                            lr(r, n1, e, t);
                        });
                    });
                }
            });
            return a;
        }
        function ft(n, e, t, r) {
            if (e.sseEvent) at(n, r, e.sseEvent);
            else if (e.trigger === "revealed") {
                $e();
                ze(n, r, t, e);
                Ze(n);
            } else if (e.trigger === "intersect") {
                var i = {};
                if (e.root) i.root = ie(n, e.root);
                if (e.threshold) i.threshold = parseFloat(e.threshold);
                var a = new IntersectionObserver(function(e) {
                    for(var t = 0; t < e.length; t++){
                        var r = e[t];
                        if (r.isIntersecting) {
                            ee(n, "intersect");
                            break;
                        }
                    }
                }, i);
                a.observe(n);
                ze(n, r, t, e);
            } else if (e.trigger === "load") {
                if (!We(e, xt("load", {
                    elt: n
                }))) lt(n, r, t, e.delay);
            } else if (e.pollInterval) {
                t.polling = true;
                Be(n, r, e);
            } else ze(n, r, t, e);
        }
        function ct(e) {
            if (e.type === "text/javascript" || e.type === "module" || e.type === "") {
                var t = $().createElement("script");
                Y(e.attributes, function(e) {
                    t.setAttribute(e.name, e.value);
                });
                t.textContent = e.textContent;
                t.async = false;
                if (z.config.inlineScriptNonce) t.nonce = z.config.inlineScriptNonce;
                var r = e.parentElement;
                try {
                    r.insertBefore(t, e);
                } catch (e) {
                    St(e);
                } finally{
                    if (e.parentElement) e.parentElement.removeChild(e);
                }
            }
        }
        function ht(e) {
            if (d(e, "script")) ct(e);
            Y(R(e, "script"), function(e) {
                ct(e);
            });
        }
        function dt() {
            return document.querySelector("[hx-boost], [data-hx-boost]");
        }
        function vt(e) {
            if (e.querySelectorAll) {
                var t = dt() ? ", a, form" : "";
                var r = e.querySelectorAll(i + t + ", [hx-sse], [data-hx-sse], [hx-ws]," + " [data-hx-ws], [hx-ext], [data-hx-ext]");
                return r;
            } else return [];
        }
        function gt(n) {
            var e = function(e) {
                var t = A(e.target, "button, input[type='submit']");
                if (t !== null) {
                    var r = K(n);
                    r.lastButtonClicked = t;
                }
            };
            n.addEventListener("click", e);
            n.addEventListener("focusin", e);
            n.addEventListener("focusout", function(e) {
                var t = K(n);
                t.lastButtonClicked = null;
            });
        }
        function pt(e) {
            if (e.closest && e.closest(z.config.disableSelector)) return;
            var t = K(e);
            if (t.initHash !== de(e)) {
                t.initHash = de(e);
                ve(e);
                ee(e, "htmx:beforeProcessNode");
                if (e.value) t.lastValue = e.value;
                var r = Xe(e);
                var n = ut(e, t, r);
                if (!n && Z(e, "hx-boost") === "true") Ue(e, t, r);
                if (e.tagName === "FORM") gt(e);
                var i = J(e, "hx-sse");
                if (i) rt(e, t, i);
                var a = J(e, "hx-ws");
                if (a) Ke(e, t, a);
                ee(e, "htmx:afterProcessNode");
            }
        }
        function mt(e) {
            e = M(e);
            pt(e);
            Y(vt(e), function(e) {
                pt(e);
            });
        }
        function yt(e) {
            return e.replace(/([a-z0-9])([A-Z])/g, "$1-$2").toLowerCase();
        }
        function xt(e, t) {
            var r;
            if (window.CustomEvent && typeof window.CustomEvent === "function") r = new CustomEvent(e, {
                bubbles: true,
                cancelable: true,
                detail: t
            });
            else {
                r = $().createEvent("CustomEvent");
                r.initCustomEvent(e, true, true, t);
            }
            return r;
        }
        function Q(e, t, r) {
            ee(e, t, ne({
                error: t
            }, r));
        }
        function bt(e) {
            return e === "htmx:afterProcessNode";
        }
        function wt(e, t) {
            Y(gr(e), function(e) {
                try {
                    t(e);
                } catch (e) {
                    St(e);
                }
            });
        }
        function St(e) {
            if (console.error) console.error(e);
            else if (console.log) console.log("ERROR: ", e);
        }
        function ee(e, t, r) {
            e = M(e);
            if (r == null) r = {};
            r["elt"] = e;
            var n = xt(t, r);
            if (z.logger && !bt(t)) z.logger(e, t, r);
            if (r.error) {
                St(r.error);
                ee(e, "htmx:error", {
                    errorInfo: r
                });
            }
            var i = e.dispatchEvent(n);
            var a = yt(t);
            if (i && a !== t) {
                var o = xt(a, n.detail);
                i = i && e.dispatchEvent(o);
            }
            wt(e, function(e) {
                i = i && e.onEvent(t, n) !== false;
            });
            return i;
        }
        var Et = location.pathname + location.search;
        function Ct() {
            var e = $().querySelector("[hx-history-elt],[data-hx-history-elt]");
            return e || $().body;
        }
        function Rt(e, t, r, n) {
            if (!S()) return;
            var i = w(localStorage.getItem("htmx-history-cache")) || [];
            for(var a = 0; a < i.length; a++)if (i[a].url === e) {
                i.splice(a, 1);
                break;
            }
            var o = {
                url: e,
                content: t,
                title: r,
                scroll: n
            };
            ee($().body, "htmx:historyItemCreated", {
                item: o,
                cache: i
            });
            i.push(o);
            while(i.length > z.config.historyCacheSize)i.shift();
            while(i.length > 0)try {
                localStorage.setItem("htmx-history-cache", JSON.stringify(i));
                break;
            } catch (e) {
                Q($().body, "htmx:historyCacheError", {
                    cause: e,
                    cache: i
                });
                i.shift();
            }
        }
        function Ot(e) {
            if (!S()) return null;
            var t = w(localStorage.getItem("htmx-history-cache")) || [];
            for(var r = 0; r < t.length; r++){
                if (t[r].url === e) return t[r];
            }
            return null;
        }
        function qt(e) {
            var t = z.config.requestClass;
            var r = e.cloneNode(true);
            Y(R(r, "." + t), function(e) {
                T(e, t);
            });
            return r.innerHTML;
        }
        function Tt() {
            var e = Ct();
            var t = Et || location.pathname + location.search;
            var r = $().querySelector('[hx-history="false" i],[data-hx-history="false" i]');
            if (!r) {
                ee($().body, "htmx:beforeHistorySave", {
                    path: t,
                    historyElt: e
                });
                Rt(t, qt(e), $().title, window.scrollY);
            }
            if (z.config.historyEnabled) history.replaceState({
                htmx: true
            }, $().title, window.location.href);
        }
        function Lt(e) {
            if (z.config.getCacheBusterParam) {
                e = e.replace(/org\.htmx\.cache-buster=[^&]*&?/, "");
                if (e.endsWith("&") || e.endsWith("?")) e = e.slice(0, -1);
            }
            if (z.config.historyEnabled) history.pushState({
                htmx: true
            }, "", e);
            Et = e;
        }
        function Ht(e) {
            if (z.config.historyEnabled) history.replaceState({
                htmx: true
            }, "", e);
            Et = e;
        }
        function At(e) {
            Y(e, function(e) {
                e.call();
            });
        }
        function Nt(a) {
            var e = new XMLHttpRequest;
            var o = {
                path: a,
                xhr: e
            };
            ee($().body, "htmx:historyCacheMiss", o);
            e.open("GET", a, true);
            e.setRequestHeader("HX-History-Restore-Request", "true");
            e.onload = function() {
                if (this.status >= 200 && this.status < 400) {
                    ee($().body, "htmx:historyCacheMissLoad", o);
                    var e = f(this.response);
                    e = e.querySelector("[hx-history-elt],[data-hx-history-elt]") || e;
                    var t = Ct();
                    var r = Zt(t);
                    var n = Re(this.response);
                    if (n) {
                        var i = C("title");
                        if (i) i.innerHTML = n;
                        else window.document.title = n;
                    }
                    Se(t, e, r);
                    At(r.tasks);
                    Et = a;
                    ee($().body, "htmx:historyRestore", {
                        path: a,
                        cacheMiss: true,
                        serverResponse: this.response
                    });
                } else Q($().body, "htmx:historyCacheMissLoadError", o);
            };
            e.send();
        }
        function It(e) {
            Tt();
            e = e || location.pathname + location.search;
            var t = Ot(e);
            if (t) {
                var r = f(t.content);
                var n = Ct();
                var i = Zt(n);
                Se(n, r, i);
                At(i.tasks);
                document.title = t.title;
                window.scrollTo(0, t.scroll);
                Et = e;
                ee($().body, "htmx:historyRestore", {
                    path: e,
                    item: t
                });
            } else if (z.config.refreshOnHistoryMiss) window.location.reload(true);
            else Nt(e);
        }
        function kt(e) {
            var t = F(e, "hx-indicator");
            if (t == null) t = [
                e
            ];
            Y(t, function(e) {
                var t = K(e);
                t.requestCount = (t.requestCount || 0) + 1;
                e.classList["add"].call(e.classList, z.config.requestClass);
            });
            return t;
        }
        function Mt(e) {
            Y(e, function(e) {
                var t = K(e);
                t.requestCount = (t.requestCount || 0) - 1;
                if (t.requestCount === 0) e.classList["remove"].call(e.classList, z.config.requestClass);
            });
        }
        function Pt(e, t) {
            for(var r = 0; r < e.length; r++){
                var n = e[r];
                if (n.isSameNode(t)) return true;
            }
            return false;
        }
        function Dt(e) {
            if (e.name === "" || e.name == null || e.disabled) return false;
            if (e.type === "button" || e.type === "submit" || e.tagName === "image" || e.tagName === "reset" || e.tagName === "file") return false;
            if (e.type === "checkbox" || e.type === "radio") return e.checked;
            return true;
        }
        function Xt(t, r, n, e, i) {
            if (e == null || Pt(t, e)) return;
            else t.push(e);
            if (Dt(e)) {
                var a = G(e, "name");
                var o = e.value;
                if (e.multiple) o = y(e.querySelectorAll("option:checked")).map(function(e) {
                    return e.value;
                });
                if (e.files) o = y(e.files);
                if (a != null && o != null) {
                    var s = r[a];
                    if (s !== undefined) {
                        if (Array.isArray(s)) {
                            if (Array.isArray(o)) r[a] = s.concat(o);
                            else s.push(o);
                        } else if (Array.isArray(o)) r[a] = [
                            s
                        ].concat(o);
                        else r[a] = [
                            s,
                            o
                        ];
                    } else r[a] = o;
                }
                if (i) Ft(e, n);
            }
            if (d(e, "form")) {
                var l = e.elements;
                Y(l, function(e) {
                    Xt(t, r, n, e, i);
                });
            }
        }
        function Ft(e, t) {
            if (e.willValidate) {
                ee(e, "htmx:validation:validate");
                if (!e.checkValidity()) {
                    t.push({
                        elt: e,
                        message: e.validationMessage,
                        validity: e.validity
                    });
                    ee(e, "htmx:validation:failed", {
                        message: e.validationMessage,
                        validity: e.validity
                    });
                }
            }
        }
        function Bt(e, t) {
            var r = [];
            var n = {};
            var i = {};
            var a = [];
            var o = K(e);
            var s = d(e, "form") && e.noValidate !== true || J(e, "hx-validate") === "true";
            if (o.lastButtonClicked) s = s && o.lastButtonClicked.formNoValidate !== true;
            if (t !== "get") Xt(r, i, a, A(e, "form"), s);
            Xt(r, n, a, e, s);
            if (o.lastButtonClicked) {
                var l = G(o.lastButtonClicked, "name");
                if (l) n[l] = o.lastButtonClicked.value;
            }
            var u = F(e, "hx-include");
            Y(u, function(e) {
                Xt(r, n, a, e, s);
                if (!d(e, "form")) Y(e.querySelectorAll(De), function(e) {
                    Xt(r, n, a, e, s);
                });
            });
            n = ne(n, i);
            return {
                errors: a,
                values: n
            };
        }
        function jt(e, t, r) {
            if (e !== "") e += "&";
            if (String(r) === "[object Object]") r = JSON.stringify(r);
            var n = encodeURIComponent(r);
            e += encodeURIComponent(t) + "=" + n;
            return e;
        }
        function Ut(e) {
            var t = "";
            for(var r in e)if (e.hasOwnProperty(r)) {
                var n = e[r];
                if (Array.isArray(n)) Y(n, function(e) {
                    t = jt(t, r, e);
                });
                else t = jt(t, r, n);
            }
            return t;
        }
        function Vt(e) {
            var t = new FormData;
            for(var r in e)if (e.hasOwnProperty(r)) {
                var n = e[r];
                if (Array.isArray(n)) Y(n, function(e) {
                    t.append(r, e);
                });
                else t.append(r, n);
            }
            return t;
        }
        function _t(e, t, r) {
            var n = {
                "HX-Request": "true",
                "HX-Trigger": G(e, "id"),
                "HX-Trigger-Name": G(e, "name"),
                "HX-Target": J(t, "id"),
                "HX-Current-URL": $().location.href
            };
            Yt(e, "hx-headers", false, n);
            if (r !== undefined) n["HX-Prompt"] = r;
            if (K(e).boosted) n["HX-Boosted"] = "true";
            return n;
        }
        function Wt(t, e) {
            var r = Z(e, "hx-params");
            if (r) {
                if (r === "none") return {};
                else if (r === "*") return t;
                else if (r.indexOf("not ") === 0) {
                    Y(r.substr(4).split(","), function(e) {
                        e = e.trim();
                        delete t[e];
                    });
                    return t;
                } else {
                    var n = {};
                    Y(r.split(","), function(e) {
                        e = e.trim();
                        n[e] = t[e];
                    });
                    return n;
                }
            } else return t;
        }
        function zt(e) {
            return G(e, "href") && G(e, "href").indexOf("#") >= 0;
        }
        function Gt(e, t) {
            var r = t ? t : Z(e, "hx-swap");
            var n = {
                swapStyle: K(e).boosted ? "innerHTML" : z.config.defaultSwapStyle,
                swapDelay: z.config.defaultSwapDelay,
                settleDelay: z.config.defaultSettleDelay
            };
            if (K(e).boosted && !zt(e)) n["show"] = "top";
            if (r) {
                var i = b(r);
                if (i.length > 0) {
                    n["swapStyle"] = i[0];
                    for(var a = 1; a < i.length; a++){
                        var o = i[a];
                        if (o.indexOf("swap:") === 0) n["swapDelay"] = v(o.substr(5));
                        if (o.indexOf("settle:") === 0) n["settleDelay"] = v(o.substr(7));
                        if (o.indexOf("scroll:") === 0) {
                            var s = o.substr(7);
                            var l = s.split(":");
                            var f = l.pop();
                            var u = l.length > 0 ? l.join(":") : null;
                            n["scroll"] = f;
                            n["scrollTarget"] = u;
                        }
                        if (o.indexOf("show:") === 0) {
                            var c = o.substr(5);
                            var l = c.split(":");
                            var h = l.pop();
                            var u = l.length > 0 ? l.join(":") : null;
                            n["show"] = h;
                            n["showTarget"] = u;
                        }
                        if (o.indexOf("focus-scroll:") === 0) {
                            var d = o.substr(13);
                            n["focusScroll"] = d == "true";
                        }
                    }
                }
            }
            return n;
        }
        function Jt(e) {
            return Z(e, "hx-encoding") === "multipart/form-data" || d(e, "form") && G(e, "enctype") === "multipart/form-data";
        }
        function $t(t, r, n) {
            var i = null;
            wt(r, function(e) {
                if (i == null) i = e.encodeParameters(t, n, r);
            });
            if (i != null) return i;
            else {
                if (Jt(r)) return Vt(n);
                else return Ut(n);
            }
        }
        function Zt(e) {
            return {
                tasks: [],
                elts: [
                    e
                ]
            };
        }
        function Kt(e, t) {
            var r = e[0];
            var n = e[e.length - 1];
            if (t.scroll) {
                var i = null;
                if (t.scrollTarget) i = ie(r, t.scrollTarget);
                if (t.scroll === "top" && (r || i)) {
                    i = i || r;
                    i.scrollTop = 0;
                }
                if (t.scroll === "bottom" && (n || i)) {
                    i = i || n;
                    i.scrollTop = i.scrollHeight;
                }
            }
            if (t.show) {
                var i = null;
                if (t.showTarget) {
                    var a = t.showTarget;
                    if (t.showTarget === "window") a = "body";
                    i = ie(r, a);
                }
                if (t.show === "top" && (r || i)) {
                    i = i || r;
                    i.scrollIntoView({
                        block: "start",
                        behavior: z.config.scrollBehavior
                    });
                }
                if (t.show === "bottom" && (n || i)) {
                    i = i || n;
                    i.scrollIntoView({
                        block: "end",
                        behavior: z.config.scrollBehavior
                    });
                }
            }
        }
        function Yt(e, t, r, n) {
            if (n == null) n = {};
            if (e == null) return n;
            var i = J(e, t);
            if (i) {
                var a = i.trim();
                var o = r;
                if (a === "unset") return null;
                if (a.indexOf("javascript:") === 0) {
                    a = a.substr(11);
                    o = true;
                } else if (a.indexOf("js:") === 0) {
                    a = a.substr(3);
                    o = true;
                }
                if (a.indexOf("{") !== 0) a = "{" + a + "}";
                var s;
                if (o) s = Qt(e, function() {
                    return Function("return (" + a + ")")();
                }, {});
                else s = w(a);
                for(var l in s){
                    if (s.hasOwnProperty(l)) {
                        if (n[l] == null) n[l] = s[l];
                    }
                }
            }
            return Yt(u(e), t, r, n);
        }
        function Qt(e, t, r) {
            if (z.config.allowEval) return t();
            else {
                Q(e, "htmx:evalDisallowedError");
                return r;
            }
        }
        function er(e, t) {
            return Yt(e, "hx-vars", true, t);
        }
        function tr(e, t) {
            return Yt(e, "hx-vals", false, t);
        }
        function rr(e) {
            return ne(er(e), tr(e));
        }
        function nr(t, r, n) {
            if (n !== null) try {
                t.setRequestHeader(r, n);
            } catch (e) {
                t.setRequestHeader(r, encodeURIComponent(n));
                t.setRequestHeader(r + "-URI-AutoEncoded", "true");
            }
        }
        function ir(t) {
            if (t.responseURL && typeof URL !== "undefined") try {
                var e = new URL(t.responseURL);
                return e.pathname + e.search;
            } catch (e) {
                Q($().body, "htmx:badResponseUrl", {
                    url: t.responseURL
                });
            }
        }
        function ar(e, t) {
            return e.getAllResponseHeaders().match(t);
        }
        function or(e, t, r) {
            e = e.toLowerCase();
            if (r) {
                if (r instanceof Element || g(r, "String")) return lr(e, t, null, null, {
                    targetOverride: M(r),
                    returnPromise: true
                });
                else return lr(e, t, M(r.source), r.event, {
                    handler: r.handler,
                    headers: r.headers,
                    values: r.values,
                    targetOverride: M(r.target),
                    swapOverride: r.swap,
                    returnPromise: true
                });
            } else return lr(e, t, null, null, {
                returnPromise: true
            });
        }
        function sr(e) {
            var t = [];
            while(e){
                t.push(e);
                e = e.parentElement;
            }
            return t;
        }
        function lr(e, t, n, r, i, f) {
            var c = null;
            var h = null;
            i = i != null ? i : {};
            if (i.returnPromise && typeof Promise !== "undefined") var d = new Promise(function(e, t) {
                c = e;
                h = t;
            });
            if (n == null) n = $().body;
            var v = i.handler || fr;
            if (!re(n)) return;
            var g = i.targetOverride || se(n);
            if (g == null || g == ae) {
                Q(n, "htmx:targetError", {
                    target: J(n, "hx-target")
                });
                return;
            }
            if (!f) {
                var p = function() {
                    return lr(e, t, n, r, i, true);
                };
                var m = {
                    target: g,
                    elt: n,
                    path: t,
                    verb: e,
                    triggeringEvent: r,
                    etc: i,
                    issueRequest: p
                };
                if (ee(n, "htmx:confirm", m) === false) return;
            }
            var y = n;
            var a = K(n);
            var x = Z(n, "hx-sync");
            var b = null;
            var w = false;
            if (x) {
                var S = x.split(":");
                var E = S[0].trim();
                if (E === "this") y = oe(n, "hx-sync");
                else y = ie(n, E);
                x = (S[1] || "drop").trim();
                a = K(y);
                if (x === "drop" && a.xhr && a.abortable !== true) return;
                else if (x === "abort") {
                    if (a.xhr) return;
                    else w = true;
                } else if (x === "replace") ee(y, "htmx:abort");
                else if (x.indexOf("queue") === 0) {
                    var C = x.split(" ");
                    b = (C[1] || "last").trim();
                }
            }
            if (a.xhr) {
                if (a.abortable) ee(y, "htmx:abort");
                else {
                    if (b == null) {
                        if (r) {
                            var R = K(r);
                            if (R && R.triggerSpec && R.triggerSpec.queue) b = R.triggerSpec.queue;
                        }
                        if (b == null) b = "last";
                    }
                    if (a.queuedRequests == null) a.queuedRequests = [];
                    if (b === "first" && a.queuedRequests.length === 0) a.queuedRequests.push(function() {
                        lr(e, t, n, r, i);
                    });
                    else if (b === "all") a.queuedRequests.push(function() {
                        lr(e, t, n, r, i);
                    });
                    else if (b === "last") {
                        a.queuedRequests = [];
                        a.queuedRequests.push(function() {
                            lr(e, t, n, r, i);
                        });
                    }
                    return;
                }
            }
            var o = new XMLHttpRequest;
            a.xhr = o;
            a.abortable = w;
            var s = function() {
                a.xhr = null;
                a.abortable = false;
                if (a.queuedRequests != null && a.queuedRequests.length > 0) {
                    var e = a.queuedRequests.shift();
                    e();
                }
            };
            var O = Z(n, "hx-prompt");
            if (O) {
                var q = prompt(O);
                if (q === null || !ee(n, "htmx:prompt", {
                    prompt: q,
                    target: g
                })) {
                    te(c);
                    s();
                    return d;
                }
            }
            var T = Z(n, "hx-confirm");
            if (T) {
                if (!confirm(T)) {
                    te(c);
                    s();
                    return d;
                }
            }
            var L = _t(n, g, q);
            if (i.headers) L = ne(L, i.headers);
            var H = Bt(n, e);
            var A = H.errors;
            var N = H.values;
            if (i.values) N = ne(N, i.values);
            var I = rr(n);
            var k = ne(N, I);
            var M = Wt(k, n);
            if (e !== "get" && !Jt(n)) L["Content-Type"] = "application/x-www-form-urlencoded";
            if (z.config.getCacheBusterParam && e === "get") M["org.htmx.cache-buster"] = G(g, "id") || "true";
            if (t == null || t === "") t = $().location.href;
            var P = Yt(n, "hx-request");
            var D = K(n).boosted;
            var l = {
                boosted: D,
                parameters: M,
                unfilteredParameters: k,
                headers: L,
                target: g,
                verb: e,
                errors: A,
                withCredentials: i.credentials || P.credentials || z.config.withCredentials,
                timeout: i.timeout || P.timeout || z.config.timeout,
                path: t,
                triggeringEvent: r
            };
            if (!ee(n, "htmx:configRequest", l)) {
                te(c);
                s();
                return d;
            }
            t = l.path;
            e = l.verb;
            L = l.headers;
            M = l.parameters;
            A = l.errors;
            if (A && A.length > 0) {
                ee(n, "htmx:validation:halted", l);
                te(c);
                s();
                return d;
            }
            var X = t.split("#");
            var F = X[0];
            var B = X[1];
            var j = null;
            if (e === "get") {
                j = F;
                var U = Object.keys(M).length !== 0;
                if (U) {
                    if (j.indexOf("?") < 0) j += "?";
                    else j += "&";
                    j += Ut(M);
                    if (B) j += "#" + B;
                }
                o.open("GET", j, true);
            } else o.open(e.toUpperCase(), t, true);
            o.overrideMimeType("text/html");
            o.withCredentials = l.withCredentials;
            o.timeout = l.timeout;
            if (P.noHeaders) ;
            else {
                for(var V in L)if (L.hasOwnProperty(V)) {
                    var _ = L[V];
                    nr(o, V, _);
                }
            }
            var u = {
                xhr: o,
                target: g,
                requestConfig: l,
                etc: i,
                boosted: D,
                pathInfo: {
                    requestPath: t,
                    finalRequestPath: j || t,
                    anchor: B
                }
            };
            o.onload = function() {
                try {
                    var e = sr(n);
                    u.pathInfo.responsePath = ir(o);
                    v(n, u);
                    Mt(W);
                    ee(n, "htmx:afterRequest", u);
                    ee(n, "htmx:afterOnLoad", u);
                    if (!re(n)) {
                        var t = null;
                        while(e.length > 0 && t == null){
                            var r = e.shift();
                            if (re(r)) t = r;
                        }
                        if (t) {
                            ee(t, "htmx:afterRequest", u);
                            ee(t, "htmx:afterOnLoad", u);
                        }
                    }
                    te(c);
                    s();
                } catch (e) {
                    Q(n, "htmx:onLoadError", ne({
                        error: e
                    }, u));
                    throw e;
                }
            };
            o.onerror = function() {
                Mt(W);
                Q(n, "htmx:afterRequest", u);
                Q(n, "htmx:sendError", u);
                te(h);
                s();
            };
            o.onabort = function() {
                Mt(W);
                Q(n, "htmx:afterRequest", u);
                Q(n, "htmx:sendAbort", u);
                te(h);
                s();
            };
            o.ontimeout = function() {
                Mt(W);
                Q(n, "htmx:afterRequest", u);
                Q(n, "htmx:timeout", u);
                te(h);
                s();
            };
            if (!ee(n, "htmx:beforeRequest", u)) {
                te(c);
                s();
                return d;
            }
            var W = kt(n);
            Y([
                "loadstart",
                "loadend",
                "progress",
                "abort"
            ], function(t) {
                Y([
                    o,
                    o.upload
                ], function(e) {
                    e.addEventListener(t, function(e) {
                        ee(n, "htmx:xhr:" + t, {
                            lengthComputable: e.lengthComputable,
                            loaded: e.loaded,
                            total: e.total
                        });
                    });
                });
            });
            ee(n, "htmx:beforeSend", u);
            o.send(e === "get" ? null : $t(o, n, M));
            return d;
        }
        function ur(e, t) {
            var r = t.xhr;
            var n = null;
            var i = null;
            if (ar(r, /HX-Push:/i)) {
                n = r.getResponseHeader("HX-Push");
                i = "push";
            } else if (ar(r, /HX-Push-Url:/i)) {
                n = r.getResponseHeader("HX-Push-Url");
                i = "push";
            } else if (ar(r, /HX-Replace-Url:/i)) {
                n = r.getResponseHeader("HX-Replace-Url");
                i = "replace";
            }
            if (n) {
                if (n === "false") return {};
                else return {
                    type: i,
                    path: n
                };
            }
            var a = t.pathInfo.finalRequestPath;
            var o = t.pathInfo.responsePath;
            var s = Z(e, "hx-push-url");
            var f = Z(e, "hx-replace-url");
            var c = K(e).boosted;
            var l = null;
            var u = null;
            if (s) {
                l = "push";
                u = s;
            } else if (f) {
                l = "replace";
                u = f;
            } else if (c) {
                l = "push";
                u = o || a;
            }
            if (u) {
                if (u === "false") return {};
                if (u === "true") u = o || a;
                if (t.pathInfo.anchor && u.indexOf("#") === -1) u = u + "#" + t.pathInfo.anchor;
                return {
                    type: l,
                    path: u
                };
            } else return {};
        }
        function fr(s, l) {
            var u = l.xhr;
            var f = l.target;
            var n = l.etc;
            if (!ee(s, "htmx:beforeOnLoad", l)) return;
            if (ar(u, /HX-Trigger:/i)) qe(u, "HX-Trigger", s);
            if (ar(u, /HX-Location:/i)) {
                Tt();
                var e = u.getResponseHeader("HX-Location");
                var c;
                if (e.indexOf("{") === 0) {
                    c = w(e);
                    e = c["path"];
                    delete c["path"];
                }
                or("GET", e, c).then(function() {
                    Lt(e);
                });
                return;
            }
            if (ar(u, /HX-Redirect:/i)) {
                location.href = u.getResponseHeader("HX-Redirect");
                return;
            }
            if (ar(u, /HX-Refresh:/i)) {
                if ("true" === u.getResponseHeader("HX-Refresh")) {
                    location.reload();
                    return;
                }
            }
            if (ar(u, /HX-Retarget:/i)) l.target = $().querySelector(u.getResponseHeader("HX-Retarget"));
            var h = ur(s, l);
            var i = u.status >= 200 && u.status < 400 && u.status !== 204;
            var d = u.response;
            var t = u.status >= 400;
            var r = ne({
                shouldSwap: i,
                serverResponse: d,
                isError: t
            }, l);
            if (!ee(f, "htmx:beforeSwap", r)) return;
            f = r.target;
            d = r.serverResponse;
            t = r.isError;
            l.failed = t;
            l.successful = !t;
            if (r.shouldSwap) {
                if (u.status === 286) Fe(s);
                wt(s, function(e) {
                    d = e.transformResponse(d, u, s);
                });
                if (h.type) Tt();
                var a = n.swapOverride;
                if (ar(u, /HX-Reswap:/i)) a = u.getResponseHeader("HX-Reswap");
                var c = Gt(s, a);
                f.classList.add(z.config.swappingClass);
                var o = function() {
                    try {
                        var e = document.activeElement;
                        var t = {};
                        try {
                            t = {
                                elt: e,
                                start: e ? e.selectionStart : null,
                                end: e ? e.selectionEnd : null
                            };
                        } catch (e) {}
                        var n = Zt(f);
                        Oe(c.swapStyle, f, s, d, n);
                        if (t.elt && !re(t.elt) && t.elt.id) {
                            var r = document.getElementById(t.elt.id);
                            var i = {
                                preventScroll: c.focusScroll !== undefined ? !c.focusScroll : !z.config.defaultFocusScroll
                            };
                            if (r) {
                                if (t.start && r.setSelectionRange) try {
                                    r.setSelectionRange(t.start, t.end);
                                } catch (e) {}
                                r.focus(i);
                            }
                        }
                        f.classList.remove(z.config.swappingClass);
                        Y(n.elts, function(e) {
                            if (e.classList) e.classList.add(z.config.settlingClass);
                            ee(e, "htmx:afterSwap", l);
                        });
                        if (ar(u, /HX-Trigger-After-Swap:/i)) {
                            var a = s;
                            if (!re(s)) a = $().body;
                            qe(u, "HX-Trigger-After-Swap", a);
                        }
                        var o = function() {
                            Y(n.tasks, function(e) {
                                e.call();
                            });
                            Y(n.elts, function(e) {
                                if (e.classList) e.classList.remove(z.config.settlingClass);
                                ee(e, "htmx:afterSettle", l);
                            });
                            if (h.type) {
                                if (h.type === "push") {
                                    Lt(h.path);
                                    ee($().body, "htmx:pushedIntoHistory", {
                                        path: h.path
                                    });
                                } else {
                                    Ht(h.path);
                                    ee($().body, "htmx:replacedInHistory", {
                                        path: h.path
                                    });
                                }
                            }
                            if (l.pathInfo.anchor) {
                                var e = C("#" + l.pathInfo.anchor);
                                if (e) e.scrollIntoView({
                                    block: "start",
                                    behavior: "auto"
                                });
                            }
                            if (n.title) {
                                var t = C("title");
                                if (t) t.innerHTML = n.title;
                                else window.document.title = n.title;
                            }
                            Kt(n.elts, c);
                            if (ar(u, /HX-Trigger-After-Settle:/i)) {
                                var r = s;
                                if (!re(s)) r = $().body;
                                qe(u, "HX-Trigger-After-Settle", r);
                            }
                        };
                        if (c.settleDelay > 0) setTimeout(o, c.settleDelay);
                        else o();
                    } catch (e) {
                        Q(s, "htmx:swapError", l);
                        throw e;
                    }
                };
                if (c.swapDelay > 0) setTimeout(o, c.swapDelay);
                else o();
            }
            if (t) Q(s, "htmx:responseError", ne({
                error: "Response Status Error Code " + u.status + " from " + l.pathInfo.requestPath
            }, l));
        }
        var cr = {};
        function hr() {
            return {
                init: function(e) {
                    return null;
                },
                onEvent: function(e, t) {
                    return true;
                },
                transformResponse: function(e, t, r) {
                    return e;
                },
                isInlineSwap: function(e) {
                    return false;
                },
                handleSwap: function(e, t, r, n) {
                    return false;
                },
                encodeParameters: function(e, t, r) {
                    return null;
                }
            };
        }
        function dr(e, t) {
            if (t.init) t.init(r);
            cr[e] = ne(hr(), t);
        }
        function vr(e) {
            delete cr[e];
        }
        function gr(e, r, n) {
            if (e == undefined) return r;
            if (r == undefined) r = [];
            if (n == undefined) n = [];
            var t = J(e, "hx-ext");
            if (t) Y(t.split(","), function(e) {
                e = e.replace(/ /g, "");
                if (e.slice(0, 7) == "ignore:") {
                    n.push(e.slice(7));
                    return;
                }
                if (n.indexOf(e) < 0) {
                    var t = cr[e];
                    if (t && r.indexOf(t) < 0) r.push(t);
                }
            });
            return gr(u(e), r, n);
        }
        function pr(e) {
            if ($().readyState !== "loading") e();
            else $().addEventListener("DOMContentLoaded", e);
        }
        function mr() {
            if (z.config.includeIndicatorStyles !== false) $().head.insertAdjacentHTML("beforeend", "<style>                      ." + z.config.indicatorClass + "{opacity:0;transition: opacity 200ms ease-in;}                      ." + z.config.requestClass + " ." + z.config.indicatorClass + "{opacity:1}                      ." + z.config.requestClass + "." + z.config.indicatorClass + "{opacity:1}                    </style>");
        }
        function yr() {
            var e = $().querySelector('meta[name="htmx-config"]');
            if (e) return w(e.content);
            else return null;
        }
        function xr() {
            var e = yr();
            if (e) z.config = ne(z.config, e);
        }
        pr(function() {
            xr();
            mr();
            var e = $().body;
            mt(e);
            var t = $().querySelectorAll("[hx-trigger='restored'],[data-hx-trigger='restored']");
            e.addEventListener("htmx:abort", function(e) {
                var t = e.target;
                var r = K(t);
                if (r && r.xhr) r.xhr.abort();
            });
            window.onpopstate = function(e) {
                if (e.state && e.state.htmx) {
                    It();
                    Y(t, function(e) {
                        ee(e, "htmx:restored", {
                            document: $(),
                            triggerEvent: ee
                        });
                    });
                }
            };
            setTimeout(function() {
                ee(e, "htmx:load", {});
            }, 0);
        });
        return z;
    }();
});

},{}],"jIgyh":[function(require,module,exports) {
htmx.defineExtension("ajax-header", {
    onEvent: function(name, evt) {
        if (name === "htmx:configRequest") evt.detail.headers["X-Requested-With"] = "XMLHttpRequest";
    }
});

},{}],"69hXP":[function(require,module,exports) {
// packages/alpinejs/src/scheduler.js
var parcelHelpers = require("@parcel/transformer-js/src/esmodule-helpers.js");
parcelHelpers.defineInteropFlag(exports);
parcelHelpers.export(exports, "default", ()=>module_default);
var flushPending = false;
var flushing = false;
var queue = [];
function scheduler(callback) {
    queueJob(callback);
}
function queueJob(job) {
    if (!queue.includes(job)) queue.push(job);
    queueFlush();
}
function dequeueJob(job) {
    let index = queue.indexOf(job);
    if (index !== -1) queue.splice(index, 1);
}
function queueFlush() {
    if (!flushing && !flushPending) {
        flushPending = true;
        queueMicrotask(flushJobs);
    }
}
function flushJobs() {
    flushPending = false;
    flushing = true;
    for(let i = 0; i < queue.length; i++)queue[i]();
    queue.length = 0;
    flushing = false;
}
// packages/alpinejs/src/reactivity.js
var reactive;
var effect;
var release;
var raw;
var shouldSchedule = true;
function disableEffectScheduling(callback) {
    shouldSchedule = false;
    callback();
    shouldSchedule = true;
}
function setReactivityEngine(engine) {
    reactive = engine.reactive;
    release = engine.release;
    effect = (callback)=>engine.effect(callback, {
            scheduler: (task)=>{
                if (shouldSchedule) scheduler(task);
                else task();
            }
        });
    raw = engine.raw;
}
function overrideEffect(override) {
    effect = override;
}
function elementBoundEffect(el) {
    let cleanup2 = ()=>{};
    let wrappedEffect = (callback)=>{
        let effectReference = effect(callback);
        if (!el._x_effects) {
            el._x_effects = new Set();
            el._x_runEffects = ()=>{
                el._x_effects.forEach((i)=>i());
            };
        }
        el._x_effects.add(effectReference);
        cleanup2 = ()=>{
            if (effectReference === void 0) return;
            el._x_effects.delete(effectReference);
            release(effectReference);
        };
        return effectReference;
    };
    return [
        wrappedEffect,
        ()=>{
            cleanup2();
        }
    ];
}
// packages/alpinejs/src/mutation.js
var onAttributeAddeds = [];
var onElRemoveds = [];
var onElAddeds = [];
function onElAdded(callback) {
    onElAddeds.push(callback);
}
function onElRemoved(el, callback) {
    if (typeof callback === "function") {
        if (!el._x_cleanups) el._x_cleanups = [];
        el._x_cleanups.push(callback);
    } else {
        callback = el;
        onElRemoveds.push(callback);
    }
}
function onAttributesAdded(callback) {
    onAttributeAddeds.push(callback);
}
function onAttributeRemoved(el, name, callback) {
    if (!el._x_attributeCleanups) el._x_attributeCleanups = {};
    if (!el._x_attributeCleanups[name]) el._x_attributeCleanups[name] = [];
    el._x_attributeCleanups[name].push(callback);
}
function cleanupAttributes(el, names) {
    if (!el._x_attributeCleanups) return;
    Object.entries(el._x_attributeCleanups).forEach(([name, value])=>{
        if (names === void 0 || names.includes(name)) {
            value.forEach((i)=>i());
            delete el._x_attributeCleanups[name];
        }
    });
}
var observer = new MutationObserver(onMutate);
var currentlyObserving = false;
function startObservingMutations() {
    observer.observe(document, {
        subtree: true,
        childList: true,
        attributes: true,
        attributeOldValue: true
    });
    currentlyObserving = true;
}
function stopObservingMutations() {
    flushObserver();
    observer.disconnect();
    currentlyObserving = false;
}
var recordQueue = [];
var willProcessRecordQueue = false;
function flushObserver() {
    recordQueue = recordQueue.concat(observer.takeRecords());
    if (recordQueue.length && !willProcessRecordQueue) {
        willProcessRecordQueue = true;
        queueMicrotask(()=>{
            processRecordQueue();
            willProcessRecordQueue = false;
        });
    }
}
function processRecordQueue() {
    onMutate(recordQueue);
    recordQueue.length = 0;
}
function mutateDom(callback) {
    if (!currentlyObserving) return callback();
    stopObservingMutations();
    let result = callback();
    startObservingMutations();
    return result;
}
var isCollecting = false;
var deferredMutations = [];
function deferMutations() {
    isCollecting = true;
}
function flushAndStopDeferringMutations() {
    isCollecting = false;
    onMutate(deferredMutations);
    deferredMutations = [];
}
function onMutate(mutations) {
    if (isCollecting) {
        deferredMutations = deferredMutations.concat(mutations);
        return;
    }
    let addedNodes = [];
    let removedNodes = [];
    let addedAttributes = new Map();
    let removedAttributes = new Map();
    for(let i = 0; i < mutations.length; i++){
        if (mutations[i].target._x_ignoreMutationObserver) continue;
        if (mutations[i].type === "childList") {
            mutations[i].addedNodes.forEach((node)=>node.nodeType === 1 && addedNodes.push(node));
            mutations[i].removedNodes.forEach((node)=>node.nodeType === 1 && removedNodes.push(node));
        }
        if (mutations[i].type === "attributes") {
            let el = mutations[i].target;
            let name = mutations[i].attributeName;
            let oldValue = mutations[i].oldValue;
            let add2 = ()=>{
                if (!addedAttributes.has(el)) addedAttributes.set(el, []);
                addedAttributes.get(el).push({
                    name,
                    value: el.getAttribute(name)
                });
            };
            let remove = ()=>{
                if (!removedAttributes.has(el)) removedAttributes.set(el, []);
                removedAttributes.get(el).push(name);
            };
            if (el.hasAttribute(name) && oldValue === null) add2();
            else if (el.hasAttribute(name)) {
                remove();
                add2();
            } else remove();
        }
    }
    removedAttributes.forEach((attrs, el)=>{
        cleanupAttributes(el, attrs);
    });
    addedAttributes.forEach((attrs, el)=>{
        onAttributeAddeds.forEach((i)=>i(el, attrs));
    });
    for (let node of removedNodes){
        if (addedNodes.includes(node)) continue;
        onElRemoveds.forEach((i)=>i(node));
        if (node._x_cleanups) while(node._x_cleanups.length)node._x_cleanups.pop()();
    }
    addedNodes.forEach((node)=>{
        node._x_ignoreSelf = true;
        node._x_ignore = true;
    });
    for (let node of addedNodes){
        if (removedNodes.includes(node)) continue;
        if (!node.isConnected) continue;
        delete node._x_ignoreSelf;
        delete node._x_ignore;
        onElAddeds.forEach((i)=>i(node));
        node._x_ignore = true;
        node._x_ignoreSelf = true;
    }
    addedNodes.forEach((node)=>{
        delete node._x_ignoreSelf;
        delete node._x_ignore;
    });
    addedNodes = null;
    removedNodes = null;
    addedAttributes = null;
    removedAttributes = null;
}
// packages/alpinejs/src/scope.js
function scope(node) {
    return mergeProxies(closestDataStack(node));
}
function addScopeToNode(node, data2, referenceNode) {
    node._x_dataStack = [
        data2,
        ...closestDataStack(referenceNode || node)
    ];
    return ()=>{
        node._x_dataStack = node._x_dataStack.filter((i)=>i !== data2);
    };
}
function refreshScope(element, scope2) {
    let existingScope = element._x_dataStack[0];
    Object.entries(scope2).forEach(([key, value])=>{
        existingScope[key] = value;
    });
}
function closestDataStack(node) {
    if (node._x_dataStack) return node._x_dataStack;
    if (typeof ShadowRoot === "function" && node instanceof ShadowRoot) return closestDataStack(node.host);
    if (!node.parentNode) return [];
    return closestDataStack(node.parentNode);
}
function mergeProxies(objects) {
    let thisProxy = new Proxy({}, {
        ownKeys: ()=>{
            return Array.from(new Set(objects.flatMap((i)=>Object.keys(i))));
        },
        has: (target, name)=>{
            return objects.some((obj)=>obj.hasOwnProperty(name));
        },
        get: (target, name)=>{
            return (objects.find((obj)=>{
                if (obj.hasOwnProperty(name)) {
                    let descriptor = Object.getOwnPropertyDescriptor(obj, name);
                    if (descriptor.get && descriptor.get._x_alreadyBound || descriptor.set && descriptor.set._x_alreadyBound) return true;
                    if ((descriptor.get || descriptor.set) && descriptor.enumerable) {
                        let getter = descriptor.get;
                        let setter = descriptor.set;
                        let property = descriptor;
                        getter = getter && getter.bind(thisProxy);
                        setter = setter && setter.bind(thisProxy);
                        if (getter) getter._x_alreadyBound = true;
                        if (setter) setter._x_alreadyBound = true;
                        Object.defineProperty(obj, name, {
                            ...property,
                            get: getter,
                            set: setter
                        });
                    }
                    return true;
                }
                return false;
            }) || {})[name];
        },
        set: (target, name, value)=>{
            let closestObjectWithKey = objects.find((obj)=>obj.hasOwnProperty(name));
            if (closestObjectWithKey) closestObjectWithKey[name] = value;
            else objects[objects.length - 1][name] = value;
            return true;
        }
    });
    return thisProxy;
}
// packages/alpinejs/src/interceptor.js
function initInterceptors(data2) {
    let isObject2 = (val)=>typeof val === "object" && !Array.isArray(val) && val !== null;
    let recurse = (obj, basePath = "")=>{
        Object.entries(Object.getOwnPropertyDescriptors(obj)).forEach(([key, { value , enumerable  }])=>{
            if (enumerable === false || value === void 0) return;
            let path = basePath === "" ? key : `${basePath}.${key}`;
            if (typeof value === "object" && value !== null && value._x_interceptor) obj[key] = value.initialize(data2, path, key);
            else if (isObject2(value) && value !== obj && !(value instanceof Element)) recurse(value, path);
        });
    };
    return recurse(data2);
}
function interceptor(callback, mutateObj = ()=>{}) {
    let obj = {
        initialValue: void 0,
        _x_interceptor: true,
        initialize (data2, path, key) {
            return callback(this.initialValue, ()=>get(data2, path), (value)=>set(data2, path, value), path, key);
        }
    };
    mutateObj(obj);
    return (initialValue)=>{
        if (typeof initialValue === "object" && initialValue !== null && initialValue._x_interceptor) {
            let initialize = obj.initialize.bind(obj);
            obj.initialize = (data2, path, key)=>{
                let innerValue = initialValue.initialize(data2, path, key);
                obj.initialValue = innerValue;
                return initialize(data2, path, key);
            };
        } else obj.initialValue = initialValue;
        return obj;
    };
}
function get(obj, path) {
    return path.split(".").reduce((carry, segment)=>carry[segment], obj);
}
function set(obj, path, value) {
    if (typeof path === "string") path = path.split(".");
    if (path.length === 1) obj[path[0]] = value;
    else if (path.length === 0) throw error;
    else {
        if (obj[path[0]]) return set(obj[path[0]], path.slice(1), value);
        else {
            obj[path[0]] = {};
            return set(obj[path[0]], path.slice(1), value);
        }
    }
}
// packages/alpinejs/src/magics.js
var magics = {};
function magic(name, callback) {
    magics[name] = callback;
}
function injectMagics(obj, el) {
    Object.entries(magics).forEach(([name, callback])=>{
        Object.defineProperty(obj, `$${name}`, {
            get () {
                let [utilities, cleanup2] = getElementBoundUtilities(el);
                utilities = {
                    interceptor,
                    ...utilities
                };
                onElRemoved(el, cleanup2);
                return callback(el, utilities);
            },
            enumerable: false
        });
    });
    return obj;
}
// packages/alpinejs/src/utils/error.js
function tryCatch(el, expression, callback, ...args) {
    try {
        return callback(...args);
    } catch (e) {
        handleError(e, el, expression);
    }
}
function handleError(error2, el, expression) {
    Object.assign(error2, {
        el,
        expression
    });
    console.warn(`Alpine Expression Error: ${error2.message}

${expression ? 'Expression: "' + expression + '"\n\n' : ""}`, el);
    setTimeout(()=>{
        throw error2;
    }, 0);
}
// packages/alpinejs/src/evaluator.js
var shouldAutoEvaluateFunctions = true;
function dontAutoEvaluateFunctions(callback) {
    let cache = shouldAutoEvaluateFunctions;
    shouldAutoEvaluateFunctions = false;
    callback();
    shouldAutoEvaluateFunctions = cache;
}
function evaluate(el, expression, extras = {}) {
    let result;
    evaluateLater(el, expression)((value)=>result = value, extras);
    return result;
}
function evaluateLater(...args) {
    return theEvaluatorFunction(...args);
}
var theEvaluatorFunction = normalEvaluator;
function setEvaluator(newEvaluator) {
    theEvaluatorFunction = newEvaluator;
}
function normalEvaluator(el, expression) {
    let overriddenMagics = {};
    injectMagics(overriddenMagics, el);
    let dataStack = [
        overriddenMagics,
        ...closestDataStack(el)
    ];
    if (typeof expression === "function") return generateEvaluatorFromFunction(dataStack, expression);
    let evaluator = generateEvaluatorFromString(dataStack, expression, el);
    return tryCatch.bind(null, el, expression, evaluator);
}
function generateEvaluatorFromFunction(dataStack, func) {
    return (receiver = ()=>{}, { scope: scope2 = {} , params =[]  } = {})=>{
        let result = func.apply(mergeProxies([
            scope2,
            ...dataStack
        ]), params);
        runIfTypeOfFunction(receiver, result);
    };
}
var evaluatorMemo = {};
function generateFunctionFromString(expression, el) {
    if (evaluatorMemo[expression]) return evaluatorMemo[expression];
    let AsyncFunction = Object.getPrototypeOf(async function() {}).constructor;
    let rightSideSafeExpression = /^[\n\s]*if.*\(.*\)/.test(expression) || /^(let|const)\s/.test(expression) ? `(async()=>{ ${expression} })()` : expression;
    const safeAsyncFunction = ()=>{
        try {
            return new AsyncFunction([
                "__self",
                "scope"
            ], `with (scope) { __self.result = ${rightSideSafeExpression} }; __self.finished = true; return __self.result;`);
        } catch (error2) {
            handleError(error2, el, expression);
            return Promise.resolve();
        }
    };
    let func = safeAsyncFunction();
    evaluatorMemo[expression] = func;
    return func;
}
function generateEvaluatorFromString(dataStack, expression, el) {
    let func = generateFunctionFromString(expression, el);
    return (receiver = ()=>{}, { scope: scope2 = {} , params =[]  } = {})=>{
        func.result = void 0;
        func.finished = false;
        let completeScope = mergeProxies([
            scope2,
            ...dataStack
        ]);
        if (typeof func === "function") {
            let promise = func(func, completeScope).catch((error2)=>handleError(error2, el, expression));
            if (func.finished) {
                runIfTypeOfFunction(receiver, func.result, completeScope, params, el);
                func.result = void 0;
            } else promise.then((result)=>{
                runIfTypeOfFunction(receiver, result, completeScope, params, el);
            }).catch((error2)=>handleError(error2, el, expression)).finally(()=>func.result = void 0);
        }
    };
}
function runIfTypeOfFunction(receiver, value, scope2, params, el) {
    if (shouldAutoEvaluateFunctions && typeof value === "function") {
        let result = value.apply(scope2, params);
        if (result instanceof Promise) result.then((i)=>runIfTypeOfFunction(receiver, i, scope2, params)).catch((error2)=>handleError(error2, el, value));
        else receiver(result);
    } else if (typeof value === "object" && value instanceof Promise) value.then((i)=>receiver(i));
    else receiver(value);
}
// packages/alpinejs/src/directives.js
var prefixAsString = "x-";
function prefix(subject = "") {
    return prefixAsString + subject;
}
function setPrefix(newPrefix) {
    prefixAsString = newPrefix;
}
var directiveHandlers = {};
function directive(name, callback) {
    directiveHandlers[name] = callback;
    return {
        before (directive2) {
            if (!directiveHandlers[directive2]) {
                console.warn("Cannot find directive `${directive}`. `${name}` will use the default order of execution");
                return;
            }
            const pos = directiveOrder.indexOf(directive2) ?? directiveOrder.indexOf("DEFAULT");
            if (pos >= 0) directiveOrder.splice(pos, 0, name);
        }
    };
}
function directives(el, attributes, originalAttributeOverride) {
    attributes = Array.from(attributes);
    if (el._x_virtualDirectives) {
        let vAttributes = Object.entries(el._x_virtualDirectives).map(([name, value])=>({
                name,
                value
            }));
        let staticAttributes = attributesOnly(vAttributes);
        vAttributes = vAttributes.map((attribute)=>{
            if (staticAttributes.find((attr)=>attr.name === attribute.name)) return {
                name: `x-bind:${attribute.name}`,
                value: `"${attribute.value}"`
            };
            return attribute;
        });
        attributes = attributes.concat(vAttributes);
    }
    let transformedAttributeMap = {};
    let directives2 = attributes.map(toTransformedAttributes((newName, oldName)=>transformedAttributeMap[newName] = oldName)).filter(outNonAlpineAttributes).map(toParsedDirectives(transformedAttributeMap, originalAttributeOverride)).sort(byPriority);
    return directives2.map((directive2)=>{
        return getDirectiveHandler(el, directive2);
    });
}
function attributesOnly(attributes) {
    return Array.from(attributes).map(toTransformedAttributes()).filter((attr)=>!outNonAlpineAttributes(attr));
}
var isDeferringHandlers = false;
var directiveHandlerStacks = new Map();
var currentHandlerStackKey = Symbol();
function deferHandlingDirectives(callback) {
    isDeferringHandlers = true;
    let key = Symbol();
    currentHandlerStackKey = key;
    directiveHandlerStacks.set(key, []);
    let flushHandlers = ()=>{
        while(directiveHandlerStacks.get(key).length)directiveHandlerStacks.get(key).shift()();
        directiveHandlerStacks.delete(key);
    };
    let stopDeferring = ()=>{
        isDeferringHandlers = false;
        flushHandlers();
    };
    callback(flushHandlers);
    stopDeferring();
}
function getElementBoundUtilities(el) {
    let cleanups = [];
    let cleanup2 = (callback)=>cleanups.push(callback);
    let [effect3, cleanupEffect] = elementBoundEffect(el);
    cleanups.push(cleanupEffect);
    let utilities = {
        Alpine: alpine_default,
        effect: effect3,
        cleanup: cleanup2,
        evaluateLater: evaluateLater.bind(evaluateLater, el),
        evaluate: evaluate.bind(evaluate, el)
    };
    let doCleanup = ()=>cleanups.forEach((i)=>i());
    return [
        utilities,
        doCleanup
    ];
}
function getDirectiveHandler(el, directive2) {
    let noop = ()=>{};
    let handler3 = directiveHandlers[directive2.type] || noop;
    let [utilities, cleanup2] = getElementBoundUtilities(el);
    onAttributeRemoved(el, directive2.original, cleanup2);
    let fullHandler = ()=>{
        if (el._x_ignore || el._x_ignoreSelf) return;
        handler3.inline && handler3.inline(el, directive2, utilities);
        handler3 = handler3.bind(handler3, el, directive2, utilities);
        isDeferringHandlers ? directiveHandlerStacks.get(currentHandlerStackKey).push(handler3) : handler3();
    };
    fullHandler.runCleanups = cleanup2;
    return fullHandler;
}
var startingWith = (subject, replacement)=>({ name , value  })=>{
        if (name.startsWith(subject)) name = name.replace(subject, replacement);
        return {
            name,
            value
        };
    };
var into = (i)=>i;
function toTransformedAttributes(callback = ()=>{}) {
    return ({ name , value  })=>{
        let { name: newName , value: newValue  } = attributeTransformers.reduce((carry, transform)=>{
            return transform(carry);
        }, {
            name,
            value
        });
        if (newName !== name) callback(newName, name);
        return {
            name: newName,
            value: newValue
        };
    };
}
var attributeTransformers = [];
function mapAttributes(callback) {
    attributeTransformers.push(callback);
}
function outNonAlpineAttributes({ name  }) {
    return alpineAttributeRegex().test(name);
}
var alpineAttributeRegex = ()=>new RegExp(`^${prefixAsString}([^:^.]+)\\b`);
function toParsedDirectives(transformedAttributeMap, originalAttributeOverride) {
    return ({ name , value  })=>{
        let typeMatch = name.match(alpineAttributeRegex());
        let valueMatch = name.match(/:([a-zA-Z0-9\-:]+)/);
        let modifiers = name.match(/\.[^.\]]+(?=[^\]]*$)/g) || [];
        let original = originalAttributeOverride || transformedAttributeMap[name] || name;
        return {
            type: typeMatch ? typeMatch[1] : null,
            value: valueMatch ? valueMatch[1] : null,
            modifiers: modifiers.map((i)=>i.replace(".", "")),
            expression: value,
            original
        };
    };
}
var DEFAULT = "DEFAULT";
var directiveOrder = [
    "ignore",
    "ref",
    "data",
    "id",
    "radio",
    "tabs",
    "switch",
    "disclosure",
    "menu",
    "listbox",
    "combobox",
    "bind",
    "init",
    "for",
    "mask",
    "model",
    "modelable",
    "transition",
    "show",
    "if",
    DEFAULT,
    "teleport"
];
function byPriority(a, b) {
    let typeA = directiveOrder.indexOf(a.type) === -1 ? DEFAULT : a.type;
    let typeB = directiveOrder.indexOf(b.type) === -1 ? DEFAULT : b.type;
    return directiveOrder.indexOf(typeA) - directiveOrder.indexOf(typeB);
}
// packages/alpinejs/src/utils/dispatch.js
function dispatch(el, name, detail = {}) {
    el.dispatchEvent(new CustomEvent(name, {
        detail,
        bubbles: true,
        composed: true,
        cancelable: true
    }));
}
// packages/alpinejs/src/utils/walk.js
function walk(el, callback) {
    if (typeof ShadowRoot === "function" && el instanceof ShadowRoot) {
        Array.from(el.children).forEach((el2)=>walk(el2, callback));
        return;
    }
    let skip = false;
    callback(el, ()=>skip = true);
    if (skip) return;
    let node = el.firstElementChild;
    while(node){
        walk(node, callback, false);
        node = node.nextElementSibling;
    }
}
// packages/alpinejs/src/utils/warn.js
function warn(message, ...args) {
    console.warn(`Alpine Warning: ${message}`, ...args);
}
// packages/alpinejs/src/lifecycle.js
function start() {
    if (!document.body) warn("Unable to initialize. Trying to load Alpine before `<body>` is available. Did you forget to add `defer` in Alpine's `<script>` tag?");
    dispatch(document, "alpine:init");
    dispatch(document, "alpine:initializing");
    startObservingMutations();
    onElAdded((el)=>initTree(el, walk));
    onElRemoved((el)=>destroyTree(el));
    onAttributesAdded((el, attrs)=>{
        directives(el, attrs).forEach((handle)=>handle());
    });
    let outNestedComponents = (el)=>!closestRoot(el.parentElement, true);
    Array.from(document.querySelectorAll(allSelectors())).filter(outNestedComponents).forEach((el)=>{
        initTree(el);
    });
    dispatch(document, "alpine:initialized");
}
var rootSelectorCallbacks = [];
var initSelectorCallbacks = [];
function rootSelectors() {
    return rootSelectorCallbacks.map((fn)=>fn());
}
function allSelectors() {
    return rootSelectorCallbacks.concat(initSelectorCallbacks).map((fn)=>fn());
}
function addRootSelector(selectorCallback) {
    rootSelectorCallbacks.push(selectorCallback);
}
function addInitSelector(selectorCallback) {
    initSelectorCallbacks.push(selectorCallback);
}
function closestRoot(el, includeInitSelectors = false) {
    return findClosest(el, (element)=>{
        const selectors = includeInitSelectors ? allSelectors() : rootSelectors();
        if (selectors.some((selector)=>element.matches(selector))) return true;
    });
}
function findClosest(el, callback) {
    if (!el) return;
    if (callback(el)) return el;
    if (el._x_teleportBack) el = el._x_teleportBack;
    if (!el.parentElement) return;
    return findClosest(el.parentElement, callback);
}
function isRoot(el) {
    return rootSelectors().some((selector)=>el.matches(selector));
}
var initInterceptors2 = [];
function interceptInit(callback) {
    initInterceptors2.push(callback);
}
function initTree(el, walker = walk, intercept = ()=>{}) {
    deferHandlingDirectives(()=>{
        walker(el, (el2, skip)=>{
            intercept(el2, skip);
            initInterceptors2.forEach((i)=>i(el2, skip));
            directives(el2, el2.attributes).forEach((handle)=>handle());
            el2._x_ignore && skip();
        });
    });
}
function destroyTree(root) {
    walk(root, (el)=>cleanupAttributes(el));
}
// packages/alpinejs/src/nextTick.js
var tickStack = [];
var isHolding = false;
function nextTick(callback = ()=>{}) {
    queueMicrotask(()=>{
        isHolding || setTimeout(()=>{
            releaseNextTicks();
        });
    });
    return new Promise((res)=>{
        tickStack.push(()=>{
            callback();
            res();
        });
    });
}
function releaseNextTicks() {
    isHolding = false;
    while(tickStack.length)tickStack.shift()();
}
function holdNextTicks() {
    isHolding = true;
}
// packages/alpinejs/src/utils/classes.js
function setClasses(el, value) {
    if (Array.isArray(value)) return setClassesFromString(el, value.join(" "));
    else if (typeof value === "object" && value !== null) return setClassesFromObject(el, value);
    else if (typeof value === "function") return setClasses(el, value());
    return setClassesFromString(el, value);
}
function setClassesFromString(el, classString) {
    let split = (classString2)=>classString2.split(" ").filter(Boolean);
    let missingClasses = (classString2)=>classString2.split(" ").filter((i)=>!el.classList.contains(i)).filter(Boolean);
    let addClassesAndReturnUndo = (classes)=>{
        el.classList.add(...classes);
        return ()=>{
            el.classList.remove(...classes);
        };
    };
    classString = classString === true ? classString = "" : classString || "";
    return addClassesAndReturnUndo(missingClasses(classString));
}
function setClassesFromObject(el, classObject) {
    let split = (classString)=>classString.split(" ").filter(Boolean);
    let forAdd = Object.entries(classObject).flatMap(([classString, bool])=>bool ? split(classString) : false).filter(Boolean);
    let forRemove = Object.entries(classObject).flatMap(([classString, bool])=>!bool ? split(classString) : false).filter(Boolean);
    let added = [];
    let removed = [];
    forRemove.forEach((i)=>{
        if (el.classList.contains(i)) {
            el.classList.remove(i);
            removed.push(i);
        }
    });
    forAdd.forEach((i)=>{
        if (!el.classList.contains(i)) {
            el.classList.add(i);
            added.push(i);
        }
    });
    return ()=>{
        removed.forEach((i)=>el.classList.add(i));
        added.forEach((i)=>el.classList.remove(i));
    };
}
// packages/alpinejs/src/utils/styles.js
function setStyles(el, value) {
    if (typeof value === "object" && value !== null) return setStylesFromObject(el, value);
    return setStylesFromString(el, value);
}
function setStylesFromObject(el, value) {
    let previousStyles = {};
    Object.entries(value).forEach(([key, value2])=>{
        previousStyles[key] = el.style[key];
        if (!key.startsWith("--")) key = kebabCase(key);
        el.style.setProperty(key, value2);
    });
    setTimeout(()=>{
        if (el.style.length === 0) el.removeAttribute("style");
    });
    return ()=>{
        setStyles(el, previousStyles);
    };
}
function setStylesFromString(el, value) {
    let cache = el.getAttribute("style", value);
    el.setAttribute("style", value);
    return ()=>{
        el.setAttribute("style", cache || "");
    };
}
function kebabCase(subject) {
    return subject.replace(/([a-z])([A-Z])/g, "$1-$2").toLowerCase();
}
// packages/alpinejs/src/utils/once.js
function once(callback, fallback = ()=>{}) {
    let called = false;
    return function() {
        if (!called) {
            called = true;
            callback.apply(this, arguments);
        } else fallback.apply(this, arguments);
    };
}
// packages/alpinejs/src/directives/x-transition.js
directive("transition", (el, { value , modifiers , expression  }, { evaluate: evaluate2  })=>{
    if (typeof expression === "function") expression = evaluate2(expression);
    if (!expression) registerTransitionsFromHelper(el, modifiers, value);
    else registerTransitionsFromClassString(el, expression, value);
});
function registerTransitionsFromClassString(el, classString, stage) {
    registerTransitionObject(el, setClasses, "");
    let directiveStorageMap = {
        enter: (classes)=>{
            el._x_transition.enter.during = classes;
        },
        "enter-start": (classes)=>{
            el._x_transition.enter.start = classes;
        },
        "enter-end": (classes)=>{
            el._x_transition.enter.end = classes;
        },
        leave: (classes)=>{
            el._x_transition.leave.during = classes;
        },
        "leave-start": (classes)=>{
            el._x_transition.leave.start = classes;
        },
        "leave-end": (classes)=>{
            el._x_transition.leave.end = classes;
        }
    };
    directiveStorageMap[stage](classString);
}
function registerTransitionsFromHelper(el, modifiers, stage) {
    registerTransitionObject(el, setStyles);
    let doesntSpecify = !modifiers.includes("in") && !modifiers.includes("out") && !stage;
    let transitioningIn = doesntSpecify || modifiers.includes("in") || [
        "enter"
    ].includes(stage);
    let transitioningOut = doesntSpecify || modifiers.includes("out") || [
        "leave"
    ].includes(stage);
    if (modifiers.includes("in") && !doesntSpecify) modifiers = modifiers.filter((i, index)=>index < modifiers.indexOf("out"));
    if (modifiers.includes("out") && !doesntSpecify) modifiers = modifiers.filter((i, index)=>index > modifiers.indexOf("out"));
    let wantsAll = !modifiers.includes("opacity") && !modifiers.includes("scale");
    let wantsOpacity = wantsAll || modifiers.includes("opacity");
    let wantsScale = wantsAll || modifiers.includes("scale");
    let opacityValue = wantsOpacity ? 0 : 1;
    let scaleValue = wantsScale ? modifierValue(modifiers, "scale", 95) / 100 : 1;
    let delay = modifierValue(modifiers, "delay", 0);
    let origin = modifierValue(modifiers, "origin", "center");
    let property = "opacity, transform";
    let durationIn = modifierValue(modifiers, "duration", 150) / 1e3;
    let durationOut = modifierValue(modifiers, "duration", 75) / 1e3;
    let easing = `cubic-bezier(0.4, 0.0, 0.2, 1)`;
    if (transitioningIn) {
        el._x_transition.enter.during = {
            transformOrigin: origin,
            transitionDelay: delay,
            transitionProperty: property,
            transitionDuration: `${durationIn}s`,
            transitionTimingFunction: easing
        };
        el._x_transition.enter.start = {
            opacity: opacityValue,
            transform: `scale(${scaleValue})`
        };
        el._x_transition.enter.end = {
            opacity: 1,
            transform: `scale(1)`
        };
    }
    if (transitioningOut) {
        el._x_transition.leave.during = {
            transformOrigin: origin,
            transitionDelay: delay,
            transitionProperty: property,
            transitionDuration: `${durationOut}s`,
            transitionTimingFunction: easing
        };
        el._x_transition.leave.start = {
            opacity: 1,
            transform: `scale(1)`
        };
        el._x_transition.leave.end = {
            opacity: opacityValue,
            transform: `scale(${scaleValue})`
        };
    }
}
function registerTransitionObject(el, setFunction, defaultValue = {}) {
    if (!el._x_transition) el._x_transition = {
        enter: {
            during: defaultValue,
            start: defaultValue,
            end: defaultValue
        },
        leave: {
            during: defaultValue,
            start: defaultValue,
            end: defaultValue
        },
        in (before = ()=>{}, after = ()=>{}) {
            transition(el, setFunction, {
                during: this.enter.during,
                start: this.enter.start,
                end: this.enter.end
            }, before, after);
        },
        out (before = ()=>{}, after = ()=>{}) {
            transition(el, setFunction, {
                during: this.leave.during,
                start: this.leave.start,
                end: this.leave.end
            }, before, after);
        }
    };
}
window.Element.prototype._x_toggleAndCascadeWithTransitions = function(el, value, show, hide) {
    const nextTick2 = document.visibilityState === "visible" ? requestAnimationFrame : setTimeout;
    let clickAwayCompatibleShow = ()=>nextTick2(show);
    if (value) {
        if (el._x_transition && (el._x_transition.enter || el._x_transition.leave)) el._x_transition.enter && (Object.entries(el._x_transition.enter.during).length || Object.entries(el._x_transition.enter.start).length || Object.entries(el._x_transition.enter.end).length) ? el._x_transition.in(show) : clickAwayCompatibleShow();
        else el._x_transition ? el._x_transition.in(show) : clickAwayCompatibleShow();
        return;
    }
    el._x_hidePromise = el._x_transition ? new Promise((resolve, reject)=>{
        el._x_transition.out(()=>{}, ()=>resolve(hide));
        el._x_transitioning.beforeCancel(()=>reject({
                isFromCancelledTransition: true
            }));
    }) : Promise.resolve(hide);
    queueMicrotask(()=>{
        let closest = closestHide(el);
        if (closest) {
            if (!closest._x_hideChildren) closest._x_hideChildren = [];
            closest._x_hideChildren.push(el);
        } else nextTick2(()=>{
            let hideAfterChildren = (el2)=>{
                let carry = Promise.all([
                    el2._x_hidePromise,
                    ...(el2._x_hideChildren || []).map(hideAfterChildren)
                ]).then(([i])=>i());
                delete el2._x_hidePromise;
                delete el2._x_hideChildren;
                return carry;
            };
            hideAfterChildren(el).catch((e)=>{
                if (!e.isFromCancelledTransition) throw e;
            });
        });
    });
};
function closestHide(el) {
    let parent = el.parentNode;
    if (!parent) return;
    return parent._x_hidePromise ? parent : closestHide(parent);
}
function transition(el, setFunction, { during , start: start2 , end  } = {}, before = ()=>{}, after = ()=>{}) {
    if (el._x_transitioning) el._x_transitioning.cancel();
    if (Object.keys(during).length === 0 && Object.keys(start2).length === 0 && Object.keys(end).length === 0) {
        before();
        after();
        return;
    }
    let undoStart, undoDuring, undoEnd;
    performTransition(el, {
        start () {
            undoStart = setFunction(el, start2);
        },
        during () {
            undoDuring = setFunction(el, during);
        },
        before,
        end () {
            undoStart();
            undoEnd = setFunction(el, end);
        },
        after,
        cleanup () {
            undoDuring();
            undoEnd();
        }
    });
}
function performTransition(el, stages) {
    let interrupted, reachedBefore, reachedEnd;
    let finish = once(()=>{
        mutateDom(()=>{
            interrupted = true;
            if (!reachedBefore) stages.before();
            if (!reachedEnd) {
                stages.end();
                releaseNextTicks();
            }
            stages.after();
            if (el.isConnected) stages.cleanup();
            delete el._x_transitioning;
        });
    });
    el._x_transitioning = {
        beforeCancels: [],
        beforeCancel (callback) {
            this.beforeCancels.push(callback);
        },
        cancel: once(function() {
            while(this.beforeCancels.length)this.beforeCancels.shift()();
            finish();
        }),
        finish
    };
    mutateDom(()=>{
        stages.start();
        stages.during();
    });
    holdNextTicks();
    requestAnimationFrame(()=>{
        if (interrupted) return;
        let duration = Number(getComputedStyle(el).transitionDuration.replace(/,.*/, "").replace("s", "")) * 1e3;
        let delay = Number(getComputedStyle(el).transitionDelay.replace(/,.*/, "").replace("s", "")) * 1e3;
        if (duration === 0) duration = Number(getComputedStyle(el).animationDuration.replace("s", "")) * 1e3;
        mutateDom(()=>{
            stages.before();
        });
        reachedBefore = true;
        requestAnimationFrame(()=>{
            if (interrupted) return;
            mutateDom(()=>{
                stages.end();
            });
            releaseNextTicks();
            setTimeout(el._x_transitioning.finish, duration + delay);
            reachedEnd = true;
        });
    });
}
function modifierValue(modifiers, key, fallback) {
    if (modifiers.indexOf(key) === -1) return fallback;
    const rawValue = modifiers[modifiers.indexOf(key) + 1];
    if (!rawValue) return fallback;
    if (key === "scale") {
        if (isNaN(rawValue)) return fallback;
    }
    if (key === "duration") {
        let match = rawValue.match(/([0-9]+)ms/);
        if (match) return match[1];
    }
    if (key === "origin") {
        if ([
            "top",
            "right",
            "left",
            "center",
            "bottom"
        ].includes(modifiers[modifiers.indexOf(key) + 2])) return [
            rawValue,
            modifiers[modifiers.indexOf(key) + 2]
        ].join(" ");
    }
    return rawValue;
}
// packages/alpinejs/src/clone.js
var isCloning = false;
function skipDuringClone(callback, fallback = ()=>{}) {
    return (...args)=>isCloning ? fallback(...args) : callback(...args);
}
function onlyDuringClone(callback) {
    return (...args)=>isCloning && callback(...args);
}
function clone(oldEl, newEl) {
    if (!newEl._x_dataStack) newEl._x_dataStack = oldEl._x_dataStack;
    isCloning = true;
    dontRegisterReactiveSideEffects(()=>{
        cloneTree(newEl);
    });
    isCloning = false;
}
function cloneTree(el) {
    let hasRunThroughFirstEl = false;
    let shallowWalker = (el2, callback)=>{
        walk(el2, (el3, skip)=>{
            if (hasRunThroughFirstEl && isRoot(el3)) return skip();
            hasRunThroughFirstEl = true;
            callback(el3, skip);
        });
    };
    initTree(el, shallowWalker);
}
function dontRegisterReactiveSideEffects(callback) {
    let cache = effect;
    overrideEffect((callback2, el)=>{
        let storedEffect = cache(callback2);
        release(storedEffect);
        return ()=>{};
    });
    callback();
    overrideEffect(cache);
}
// packages/alpinejs/src/utils/bind.js
function bind(el, name, value, modifiers = []) {
    if (!el._x_bindings) el._x_bindings = reactive({});
    el._x_bindings[name] = value;
    name = modifiers.includes("camel") ? camelCase(name) : name;
    switch(name){
        case "value":
            bindInputValue(el, value);
            break;
        case "style":
            bindStyles(el, value);
            break;
        case "class":
            bindClasses(el, value);
            break;
        default:
            bindAttribute(el, name, value);
            break;
    }
}
function bindInputValue(el, value) {
    if (el.type === "radio") {
        if (el.attributes.value === void 0) el.value = value;
        if (window.fromModel) el.checked = checkedAttrLooseCompare(el.value, value);
    } else if (el.type === "checkbox") {
        if (Number.isInteger(value)) el.value = value;
        else if (!Number.isInteger(value) && !Array.isArray(value) && typeof value !== "boolean" && ![
            null,
            void 0
        ].includes(value)) el.value = String(value);
        else if (Array.isArray(value)) el.checked = value.some((val)=>checkedAttrLooseCompare(val, el.value));
        else el.checked = !!value;
    } else if (el.tagName === "SELECT") updateSelect(el, value);
    else {
        if (el.value === value) return;
        el.value = value;
    }
}
function bindClasses(el, value) {
    if (el._x_undoAddedClasses) el._x_undoAddedClasses();
    el._x_undoAddedClasses = setClasses(el, value);
}
function bindStyles(el, value) {
    if (el._x_undoAddedStyles) el._x_undoAddedStyles();
    el._x_undoAddedStyles = setStyles(el, value);
}
function bindAttribute(el, name, value) {
    if ([
        null,
        void 0,
        false
    ].includes(value) && attributeShouldntBePreservedIfFalsy(name)) el.removeAttribute(name);
    else {
        if (isBooleanAttr(name)) value = name;
        setIfChanged(el, name, value);
    }
}
function setIfChanged(el, attrName, value) {
    if (el.getAttribute(attrName) != value) el.setAttribute(attrName, value);
}
function updateSelect(el, value) {
    const arrayWrappedValue = [].concat(value).map((value2)=>{
        return value2 + "";
    });
    Array.from(el.options).forEach((option)=>{
        option.selected = arrayWrappedValue.includes(option.value);
    });
}
function camelCase(subject) {
    return subject.toLowerCase().replace(/-(\w)/g, (match, char)=>char.toUpperCase());
}
function checkedAttrLooseCompare(valueA, valueB) {
    return valueA == valueB;
}
function isBooleanAttr(attrName) {
    const booleanAttributes = [
        "disabled",
        "checked",
        "required",
        "readonly",
        "hidden",
        "open",
        "selected",
        "autofocus",
        "itemscope",
        "multiple",
        "novalidate",
        "allowfullscreen",
        "allowpaymentrequest",
        "formnovalidate",
        "autoplay",
        "controls",
        "loop",
        "muted",
        "playsinline",
        "default",
        "ismap",
        "reversed",
        "async",
        "defer",
        "nomodule"
    ];
    return booleanAttributes.includes(attrName);
}
function attributeShouldntBePreservedIfFalsy(name) {
    return ![
        "aria-pressed",
        "aria-checked",
        "aria-expanded",
        "aria-selected"
    ].includes(name);
}
function getBinding(el, name, fallback) {
    if (el._x_bindings && el._x_bindings[name] !== void 0) return el._x_bindings[name];
    let attr = el.getAttribute(name);
    if (attr === null) return typeof fallback === "function" ? fallback() : fallback;
    if (attr === "") return true;
    if (isBooleanAttr(name)) return !![
        name,
        "true"
    ].includes(attr);
    return attr;
}
// packages/alpinejs/src/utils/debounce.js
function debounce(func, wait) {
    var timeout;
    return function() {
        var context = this, args = arguments;
        var later = function() {
            timeout = null;
            func.apply(context, args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}
// packages/alpinejs/src/utils/throttle.js
function throttle(func, limit) {
    let inThrottle;
    return function() {
        let context = this, args = arguments;
        if (!inThrottle) {
            func.apply(context, args);
            inThrottle = true;
            setTimeout(()=>inThrottle = false, limit);
        }
    };
}
// packages/alpinejs/src/plugin.js
function plugin(callback) {
    callback(alpine_default);
}
// packages/alpinejs/src/store.js
var stores = {};
var isReactive = false;
function store(name, value) {
    if (!isReactive) {
        stores = reactive(stores);
        isReactive = true;
    }
    if (value === void 0) return stores[name];
    stores[name] = value;
    if (typeof value === "object" && value !== null && value.hasOwnProperty("init") && typeof value.init === "function") stores[name].init();
    initInterceptors(stores[name]);
}
function getStores() {
    return stores;
}
// packages/alpinejs/src/binds.js
var binds = {};
function bind2(name, bindings) {
    let getBindings = typeof bindings !== "function" ? ()=>bindings : bindings;
    if (name instanceof Element) applyBindingsObject(name, getBindings());
    else binds[name] = getBindings;
}
function injectBindingProviders(obj) {
    Object.entries(binds).forEach(([name, callback])=>{
        Object.defineProperty(obj, name, {
            get () {
                return (...args)=>{
                    return callback(...args);
                };
            }
        });
    });
    return obj;
}
function applyBindingsObject(el, obj, original) {
    let cleanupRunners = [];
    while(cleanupRunners.length)cleanupRunners.pop()();
    let attributes = Object.entries(obj).map(([name, value])=>({
            name,
            value
        }));
    let staticAttributes = attributesOnly(attributes);
    attributes = attributes.map((attribute)=>{
        if (staticAttributes.find((attr)=>attr.name === attribute.name)) return {
            name: `x-bind:${attribute.name}`,
            value: `"${attribute.value}"`
        };
        return attribute;
    });
    directives(el, attributes, original).map((handle)=>{
        cleanupRunners.push(handle.runCleanups);
        handle();
    });
}
// packages/alpinejs/src/datas.js
var datas = {};
function data(name, callback) {
    datas[name] = callback;
}
function injectDataProviders(obj, context) {
    Object.entries(datas).forEach(([name, callback])=>{
        Object.defineProperty(obj, name, {
            get () {
                return (...args)=>{
                    return callback.bind(context)(...args);
                };
            },
            enumerable: false
        });
    });
    return obj;
}
// packages/alpinejs/src/alpine.js
var Alpine = {
    get reactive () {
        return reactive;
    },
    get release () {
        return release;
    },
    get effect () {
        return effect;
    },
    get raw () {
        return raw;
    },
    version: "3.11.1",
    flushAndStopDeferringMutations,
    dontAutoEvaluateFunctions,
    disableEffectScheduling,
    startObservingMutations,
    stopObservingMutations,
    setReactivityEngine,
    closestDataStack,
    skipDuringClone,
    onlyDuringClone,
    addRootSelector,
    addInitSelector,
    addScopeToNode,
    deferMutations,
    mapAttributes,
    evaluateLater,
    interceptInit,
    setEvaluator,
    mergeProxies,
    findClosest,
    closestRoot,
    destroyTree,
    interceptor,
    transition,
    setStyles,
    mutateDom,
    directive,
    throttle,
    debounce,
    evaluate,
    initTree,
    nextTick,
    prefixed: prefix,
    prefix: setPrefix,
    plugin,
    magic,
    store,
    start,
    clone,
    bound: getBinding,
    $data: scope,
    walk,
    data,
    bind: bind2
};
var alpine_default = Alpine;
// node_modules/@vue/shared/dist/shared.esm-bundler.js
function makeMap(str, expectsLowerCase) {
    const map = Object.create(null);
    const list = str.split(",");
    for(let i = 0; i < list.length; i++)map[list[i]] = true;
    return expectsLowerCase ? (val)=>!!map[val.toLowerCase()] : (val)=>!!map[val];
}
var PatchFlagNames = {
    [1]: `TEXT`,
    [2]: `CLASS`,
    [4]: `STYLE`,
    [8]: `PROPS`,
    [16]: `FULL_PROPS`,
    [32]: `HYDRATE_EVENTS`,
    [64]: `STABLE_FRAGMENT`,
    [128]: `KEYED_FRAGMENT`,
    [256]: `UNKEYED_FRAGMENT`,
    [512]: `NEED_PATCH`,
    [1024]: `DYNAMIC_SLOTS`,
    [2048]: `DEV_ROOT_FRAGMENT`,
    [-1]: `HOISTED`,
    [-2]: `BAIL`
};
var slotFlagsText = {
    [1]: "STABLE",
    [2]: "DYNAMIC",
    [3]: "FORWARDED"
};
var specialBooleanAttrs = `itemscope,allowfullscreen,formnovalidate,ismap,nomodule,novalidate,readonly`;
var isBooleanAttr2 = /* @__PURE__ */ makeMap(specialBooleanAttrs + `,async,autofocus,autoplay,controls,default,defer,disabled,hidden,loop,open,required,reversed,scoped,seamless,checked,muted,multiple,selected`);
var EMPTY_OBJ = Object.freeze({});
var EMPTY_ARR = Object.freeze([]);
var extend = Object.assign;
var hasOwnProperty = Object.prototype.hasOwnProperty;
var hasOwn = (val, key)=>hasOwnProperty.call(val, key);
var isArray = Array.isArray;
var isMap = (val)=>toTypeString(val) === "[object Map]";
var isString = (val)=>typeof val === "string";
var isSymbol = (val)=>typeof val === "symbol";
var isObject = (val)=>val !== null && typeof val === "object";
var objectToString = Object.prototype.toString;
var toTypeString = (value)=>objectToString.call(value);
var toRawType = (value)=>{
    return toTypeString(value).slice(8, -1);
};
var isIntegerKey = (key)=>isString(key) && key !== "NaN" && key[0] !== "-" && "" + parseInt(key, 10) === key;
var cacheStringFunction = (fn)=>{
    const cache = Object.create(null);
    return (str)=>{
        const hit = cache[str];
        return hit || (cache[str] = fn(str));
    };
};
var camelizeRE = /-(\w)/g;
var camelize = cacheStringFunction((str)=>{
    return str.replace(camelizeRE, (_, c)=>c ? c.toUpperCase() : "");
});
var hyphenateRE = /\B([A-Z])/g;
var hyphenate = cacheStringFunction((str)=>str.replace(hyphenateRE, "-$1").toLowerCase());
var capitalize = cacheStringFunction((str)=>str.charAt(0).toUpperCase() + str.slice(1));
var toHandlerKey = cacheStringFunction((str)=>str ? `on${capitalize(str)}` : ``);
var hasChanged = (value, oldValue)=>value !== oldValue && (value === value || oldValue === oldValue);
// node_modules/@vue/reactivity/dist/reactivity.esm-bundler.js
var targetMap = new WeakMap();
var effectStack = [];
var activeEffect;
var ITERATE_KEY = Symbol("iterate");
var MAP_KEY_ITERATE_KEY = Symbol("Map key iterate");
function isEffect(fn) {
    return fn && fn._isEffect === true;
}
function effect2(fn, options = EMPTY_OBJ) {
    if (isEffect(fn)) fn = fn.raw;
    const effect3 = createReactiveEffect(fn, options);
    if (!options.lazy) effect3();
    return effect3;
}
function stop(effect3) {
    if (effect3.active) {
        cleanup(effect3);
        if (effect3.options.onStop) effect3.options.onStop();
        effect3.active = false;
    }
}
var uid = 0;
function createReactiveEffect(fn, options) {
    const effect3 = function reactiveEffect() {
        if (!effect3.active) return fn();
        if (!effectStack.includes(effect3)) {
            cleanup(effect3);
            try {
                enableTracking();
                effectStack.push(effect3);
                activeEffect = effect3;
                return fn();
            } finally{
                effectStack.pop();
                resetTracking();
                activeEffect = effectStack[effectStack.length - 1];
            }
        }
    };
    effect3.id = uid++;
    effect3.allowRecurse = !!options.allowRecurse;
    effect3._isEffect = true;
    effect3.active = true;
    effect3.raw = fn;
    effect3.deps = [];
    effect3.options = options;
    return effect3;
}
function cleanup(effect3) {
    const { deps  } = effect3;
    if (deps.length) {
        for(let i = 0; i < deps.length; i++)deps[i].delete(effect3);
        deps.length = 0;
    }
}
var shouldTrack = true;
var trackStack = [];
function pauseTracking() {
    trackStack.push(shouldTrack);
    shouldTrack = false;
}
function enableTracking() {
    trackStack.push(shouldTrack);
    shouldTrack = true;
}
function resetTracking() {
    const last = trackStack.pop();
    shouldTrack = last === void 0 ? true : last;
}
function track(target, type, key) {
    if (!shouldTrack || activeEffect === void 0) return;
    let depsMap = targetMap.get(target);
    if (!depsMap) targetMap.set(target, depsMap = new Map());
    let dep = depsMap.get(key);
    if (!dep) depsMap.set(key, dep = new Set());
    if (!dep.has(activeEffect)) {
        dep.add(activeEffect);
        activeEffect.deps.push(dep);
        if (activeEffect.options.onTrack) activeEffect.options.onTrack({
            effect: activeEffect,
            target,
            type,
            key
        });
    }
}
function trigger(target, type, key, newValue, oldValue, oldTarget) {
    const depsMap = targetMap.get(target);
    if (!depsMap) return;
    const effects = new Set();
    const add2 = (effectsToAdd)=>{
        if (effectsToAdd) effectsToAdd.forEach((effect3)=>{
            if (effect3 !== activeEffect || effect3.allowRecurse) effects.add(effect3);
        });
    };
    if (type === "clear") depsMap.forEach(add2);
    else if (key === "length" && isArray(target)) depsMap.forEach((dep, key2)=>{
        if (key2 === "length" || key2 >= newValue) add2(dep);
    });
    else {
        if (key !== void 0) add2(depsMap.get(key));
        switch(type){
            case "add":
                if (!isArray(target)) {
                    add2(depsMap.get(ITERATE_KEY));
                    if (isMap(target)) add2(depsMap.get(MAP_KEY_ITERATE_KEY));
                } else if (isIntegerKey(key)) add2(depsMap.get("length"));
                break;
            case "delete":
                if (!isArray(target)) {
                    add2(depsMap.get(ITERATE_KEY));
                    if (isMap(target)) add2(depsMap.get(MAP_KEY_ITERATE_KEY));
                }
                break;
            case "set":
                if (isMap(target)) add2(depsMap.get(ITERATE_KEY));
                break;
        }
    }
    const run = (effect3)=>{
        if (effect3.options.onTrigger) effect3.options.onTrigger({
            effect: effect3,
            target,
            key,
            type,
            newValue,
            oldValue,
            oldTarget
        });
        if (effect3.options.scheduler) effect3.options.scheduler(effect3);
        else effect3();
    };
    effects.forEach(run);
}
var isNonTrackableKeys = /* @__PURE__ */ makeMap(`__proto__,__v_isRef,__isVue`);
var builtInSymbols = new Set(Object.getOwnPropertyNames(Symbol).map((key)=>Symbol[key]).filter(isSymbol));
var get2 = /* @__PURE__ */ createGetter();
var shallowGet = /* @__PURE__ */ createGetter(false, true);
var readonlyGet = /* @__PURE__ */ createGetter(true);
var shallowReadonlyGet = /* @__PURE__ */ createGetter(true, true);
var arrayInstrumentations = {};
[
    "includes",
    "indexOf",
    "lastIndexOf"
].forEach((key)=>{
    const method = Array.prototype[key];
    arrayInstrumentations[key] = function(...args) {
        const arr = toRaw(this);
        for(let i = 0, l = this.length; i < l; i++)track(arr, "get", i + "");
        const res = method.apply(arr, args);
        if (res === -1 || res === false) return method.apply(arr, args.map(toRaw));
        else return res;
    };
});
[
    "push",
    "pop",
    "shift",
    "unshift",
    "splice"
].forEach((key)=>{
    const method = Array.prototype[key];
    arrayInstrumentations[key] = function(...args) {
        pauseTracking();
        const res = method.apply(this, args);
        resetTracking();
        return res;
    };
});
function createGetter(isReadonly = false, shallow = false) {
    return function get3(target, key, receiver) {
        if (key === "__v_isReactive") return !isReadonly;
        else if (key === "__v_isReadonly") return isReadonly;
        else if (key === "__v_raw" && receiver === (isReadonly ? shallow ? shallowReadonlyMap : readonlyMap : shallow ? shallowReactiveMap : reactiveMap).get(target)) return target;
        const targetIsArray = isArray(target);
        if (!isReadonly && targetIsArray && hasOwn(arrayInstrumentations, key)) return Reflect.get(arrayInstrumentations, key, receiver);
        const res = Reflect.get(target, key, receiver);
        if (isSymbol(key) ? builtInSymbols.has(key) : isNonTrackableKeys(key)) return res;
        if (!isReadonly) track(target, "get", key);
        if (shallow) return res;
        if (isRef(res)) {
            const shouldUnwrap = !targetIsArray || !isIntegerKey(key);
            return shouldUnwrap ? res.value : res;
        }
        if (isObject(res)) return isReadonly ? readonly(res) : reactive2(res);
        return res;
    };
}
var set2 = /* @__PURE__ */ createSetter();
var shallowSet = /* @__PURE__ */ createSetter(true);
function createSetter(shallow = false) {
    return function set3(target, key, value, receiver) {
        let oldValue = target[key];
        if (!shallow) {
            value = toRaw(value);
            oldValue = toRaw(oldValue);
            if (!isArray(target) && isRef(oldValue) && !isRef(value)) {
                oldValue.value = value;
                return true;
            }
        }
        const hadKey = isArray(target) && isIntegerKey(key) ? Number(key) < target.length : hasOwn(target, key);
        const result = Reflect.set(target, key, value, receiver);
        if (target === toRaw(receiver)) {
            if (!hadKey) trigger(target, "add", key, value);
            else if (hasChanged(value, oldValue)) trigger(target, "set", key, value, oldValue);
        }
        return result;
    };
}
function deleteProperty(target, key) {
    const hadKey = hasOwn(target, key);
    const oldValue = target[key];
    const result = Reflect.deleteProperty(target, key);
    if (result && hadKey) trigger(target, "delete", key, void 0, oldValue);
    return result;
}
function has(target, key) {
    const result = Reflect.has(target, key);
    if (!isSymbol(key) || !builtInSymbols.has(key)) track(target, "has", key);
    return result;
}
function ownKeys(target) {
    track(target, "iterate", isArray(target) ? "length" : ITERATE_KEY);
    return Reflect.ownKeys(target);
}
var mutableHandlers = {
    get: get2,
    set: set2,
    deleteProperty,
    has,
    ownKeys
};
var readonlyHandlers = {
    get: readonlyGet,
    set (target, key) {
        console.warn(`Set operation on key "${String(key)}" failed: target is readonly.`, target);
        return true;
    },
    deleteProperty (target, key) {
        console.warn(`Delete operation on key "${String(key)}" failed: target is readonly.`, target);
        return true;
    }
};
var shallowReactiveHandlers = extend({}, mutableHandlers, {
    get: shallowGet,
    set: shallowSet
});
var shallowReadonlyHandlers = extend({}, readonlyHandlers, {
    get: shallowReadonlyGet
});
var toReactive = (value)=>isObject(value) ? reactive2(value) : value;
var toReadonly = (value)=>isObject(value) ? readonly(value) : value;
var toShallow = (value)=>value;
var getProto = (v)=>Reflect.getPrototypeOf(v);
function get$1(target, key, isReadonly = false, isShallow = false) {
    target = target["__v_raw"];
    const rawTarget = toRaw(target);
    const rawKey = toRaw(key);
    if (key !== rawKey) !isReadonly && track(rawTarget, "get", key);
    !isReadonly && track(rawTarget, "get", rawKey);
    const { has: has2  } = getProto(rawTarget);
    const wrap = isShallow ? toShallow : isReadonly ? toReadonly : toReactive;
    if (has2.call(rawTarget, key)) return wrap(target.get(key));
    else if (has2.call(rawTarget, rawKey)) return wrap(target.get(rawKey));
    else if (target !== rawTarget) target.get(key);
}
function has$1(key, isReadonly = false) {
    const target = this["__v_raw"];
    const rawTarget = toRaw(target);
    const rawKey = toRaw(key);
    if (key !== rawKey) !isReadonly && track(rawTarget, "has", key);
    !isReadonly && track(rawTarget, "has", rawKey);
    return key === rawKey ? target.has(key) : target.has(key) || target.has(rawKey);
}
function size(target, isReadonly = false) {
    target = target["__v_raw"];
    !isReadonly && track(toRaw(target), "iterate", ITERATE_KEY);
    return Reflect.get(target, "size", target);
}
function add(value) {
    value = toRaw(value);
    const target = toRaw(this);
    const proto = getProto(target);
    const hadKey = proto.has.call(target, value);
    if (!hadKey) {
        target.add(value);
        trigger(target, "add", value, value);
    }
    return this;
}
function set$1(key, value) {
    value = toRaw(value);
    const target = toRaw(this);
    const { has: has2 , get: get3  } = getProto(target);
    let hadKey = has2.call(target, key);
    if (!hadKey) {
        key = toRaw(key);
        hadKey = has2.call(target, key);
    } else checkIdentityKeys(target, has2, key);
    const oldValue = get3.call(target, key);
    target.set(key, value);
    if (!hadKey) trigger(target, "add", key, value);
    else if (hasChanged(value, oldValue)) trigger(target, "set", key, value, oldValue);
    return this;
}
function deleteEntry(key) {
    const target = toRaw(this);
    const { has: has2 , get: get3  } = getProto(target);
    let hadKey = has2.call(target, key);
    if (!hadKey) {
        key = toRaw(key);
        hadKey = has2.call(target, key);
    } else checkIdentityKeys(target, has2, key);
    const oldValue = get3 ? get3.call(target, key) : void 0;
    const result = target.delete(key);
    if (hadKey) trigger(target, "delete", key, void 0, oldValue);
    return result;
}
function clear() {
    const target = toRaw(this);
    const hadItems = target.size !== 0;
    const oldTarget = isMap(target) ? new Map(target) : new Set(target);
    const result = target.clear();
    if (hadItems) trigger(target, "clear", void 0, void 0, oldTarget);
    return result;
}
function createForEach(isReadonly, isShallow) {
    return function forEach(callback, thisArg) {
        const observed = this;
        const target = observed["__v_raw"];
        const rawTarget = toRaw(target);
        const wrap = isShallow ? toShallow : isReadonly ? toReadonly : toReactive;
        !isReadonly && track(rawTarget, "iterate", ITERATE_KEY);
        return target.forEach((value, key)=>{
            return callback.call(thisArg, wrap(value), wrap(key), observed);
        });
    };
}
function createIterableMethod(method, isReadonly, isShallow) {
    return function(...args) {
        const target = this["__v_raw"];
        const rawTarget = toRaw(target);
        const targetIsMap = isMap(rawTarget);
        const isPair = method === "entries" || method === Symbol.iterator && targetIsMap;
        const isKeyOnly = method === "keys" && targetIsMap;
        const innerIterator = target[method](...args);
        const wrap = isShallow ? toShallow : isReadonly ? toReadonly : toReactive;
        !isReadonly && track(rawTarget, "iterate", isKeyOnly ? MAP_KEY_ITERATE_KEY : ITERATE_KEY);
        return {
            next () {
                const { value , done  } = innerIterator.next();
                return done ? {
                    value,
                    done
                } : {
                    value: isPair ? [
                        wrap(value[0]),
                        wrap(value[1])
                    ] : wrap(value),
                    done
                };
            },
            [Symbol.iterator] () {
                return this;
            }
        };
    };
}
function createReadonlyMethod(type) {
    return function(...args) {
        {
            const key = args[0] ? `on key "${args[0]}" ` : ``;
            console.warn(`${capitalize(type)} operation ${key}failed: target is readonly.`, toRaw(this));
        }
        return type === "delete" ? false : this;
    };
}
var mutableInstrumentations = {
    get (key) {
        return get$1(this, key);
    },
    get size () {
        return size(this);
    },
    has: has$1,
    add,
    set: set$1,
    delete: deleteEntry,
    clear,
    forEach: createForEach(false, false)
};
var shallowInstrumentations = {
    get (key) {
        return get$1(this, key, false, true);
    },
    get size () {
        return size(this);
    },
    has: has$1,
    add,
    set: set$1,
    delete: deleteEntry,
    clear,
    forEach: createForEach(false, true)
};
var readonlyInstrumentations = {
    get (key) {
        return get$1(this, key, true);
    },
    get size () {
        return size(this, true);
    },
    has (key) {
        return has$1.call(this, key, true);
    },
    add: createReadonlyMethod("add"),
    set: createReadonlyMethod("set"),
    delete: createReadonlyMethod("delete"),
    clear: createReadonlyMethod("clear"),
    forEach: createForEach(true, false)
};
var shallowReadonlyInstrumentations = {
    get (key) {
        return get$1(this, key, true, true);
    },
    get size () {
        return size(this, true);
    },
    has (key) {
        return has$1.call(this, key, true);
    },
    add: createReadonlyMethod("add"),
    set: createReadonlyMethod("set"),
    delete: createReadonlyMethod("delete"),
    clear: createReadonlyMethod("clear"),
    forEach: createForEach(true, true)
};
var iteratorMethods = [
    "keys",
    "values",
    "entries",
    Symbol.iterator
];
iteratorMethods.forEach((method)=>{
    mutableInstrumentations[method] = createIterableMethod(method, false, false);
    readonlyInstrumentations[method] = createIterableMethod(method, true, false);
    shallowInstrumentations[method] = createIterableMethod(method, false, true);
    shallowReadonlyInstrumentations[method] = createIterableMethod(method, true, true);
});
function createInstrumentationGetter(isReadonly, shallow) {
    const instrumentations = shallow ? isReadonly ? shallowReadonlyInstrumentations : shallowInstrumentations : isReadonly ? readonlyInstrumentations : mutableInstrumentations;
    return (target, key, receiver)=>{
        if (key === "__v_isReactive") return !isReadonly;
        else if (key === "__v_isReadonly") return isReadonly;
        else if (key === "__v_raw") return target;
        return Reflect.get(hasOwn(instrumentations, key) && key in target ? instrumentations : target, key, receiver);
    };
}
var mutableCollectionHandlers = {
    get: createInstrumentationGetter(false, false)
};
var shallowCollectionHandlers = {
    get: createInstrumentationGetter(false, true)
};
var readonlyCollectionHandlers = {
    get: createInstrumentationGetter(true, false)
};
var shallowReadonlyCollectionHandlers = {
    get: createInstrumentationGetter(true, true)
};
function checkIdentityKeys(target, has2, key) {
    const rawKey = toRaw(key);
    if (rawKey !== key && has2.call(target, rawKey)) {
        const type = toRawType(target);
        console.warn(`Reactive ${type} contains both the raw and reactive versions of the same object${type === `Map` ? ` as keys` : ``}, which can lead to inconsistencies. Avoid differentiating between the raw and reactive versions of an object and only use the reactive version if possible.`);
    }
}
var reactiveMap = new WeakMap();
var shallowReactiveMap = new WeakMap();
var readonlyMap = new WeakMap();
var shallowReadonlyMap = new WeakMap();
function targetTypeMap(rawType) {
    switch(rawType){
        case "Object":
        case "Array":
            return 1;
        case "Map":
        case "Set":
        case "WeakMap":
        case "WeakSet":
            return 2;
        default:
            return 0;
    }
}
function getTargetType(value) {
    return value["__v_skip"] || !Object.isExtensible(value) ? 0 : targetTypeMap(toRawType(value));
}
function reactive2(target) {
    if (target && target["__v_isReadonly"]) return target;
    return createReactiveObject(target, false, mutableHandlers, mutableCollectionHandlers, reactiveMap);
}
function readonly(target) {
    return createReactiveObject(target, true, readonlyHandlers, readonlyCollectionHandlers, readonlyMap);
}
function createReactiveObject(target, isReadonly, baseHandlers, collectionHandlers, proxyMap) {
    if (!isObject(target)) {
        console.warn(`value cannot be made reactive: ${String(target)}`);
        return target;
    }
    if (target["__v_raw"] && !(isReadonly && target["__v_isReactive"])) return target;
    const existingProxy = proxyMap.get(target);
    if (existingProxy) return existingProxy;
    const targetType = getTargetType(target);
    if (targetType === 0) return target;
    const proxy = new Proxy(target, targetType === 2 ? collectionHandlers : baseHandlers);
    proxyMap.set(target, proxy);
    return proxy;
}
function toRaw(observed) {
    return observed && toRaw(observed["__v_raw"]) || observed;
}
function isRef(r) {
    return Boolean(r && r.__v_isRef === true);
}
// packages/alpinejs/src/magics/$nextTick.js
magic("nextTick", ()=>nextTick);
// packages/alpinejs/src/magics/$dispatch.js
magic("dispatch", (el)=>dispatch.bind(dispatch, el));
// packages/alpinejs/src/magics/$watch.js
magic("watch", (el, { evaluateLater: evaluateLater2 , effect: effect3  })=>(key, callback)=>{
        let evaluate2 = evaluateLater2(key);
        let firstTime = true;
        let oldValue;
        let effectReference = effect3(()=>evaluate2((value)=>{
                JSON.stringify(value);
                if (!firstTime) queueMicrotask(()=>{
                    callback(value, oldValue);
                    oldValue = value;
                });
                else oldValue = value;
                firstTime = false;
            }));
        el._x_effects.delete(effectReference);
    });
// packages/alpinejs/src/magics/$store.js
magic("store", getStores);
// packages/alpinejs/src/magics/$data.js
magic("data", (el)=>scope(el));
// packages/alpinejs/src/magics/$root.js
magic("root", (el)=>closestRoot(el));
// packages/alpinejs/src/magics/$refs.js
magic("refs", (el)=>{
    if (el._x_refs_proxy) return el._x_refs_proxy;
    el._x_refs_proxy = mergeProxies(getArrayOfRefObject(el));
    return el._x_refs_proxy;
});
function getArrayOfRefObject(el) {
    let refObjects = [];
    let currentEl = el;
    while(currentEl){
        if (currentEl._x_refs) refObjects.push(currentEl._x_refs);
        currentEl = currentEl.parentNode;
    }
    return refObjects;
}
// packages/alpinejs/src/ids.js
var globalIdMemo = {};
function findAndIncrementId(name) {
    if (!globalIdMemo[name]) globalIdMemo[name] = 0;
    return ++globalIdMemo[name];
}
function closestIdRoot(el, name) {
    return findClosest(el, (element)=>{
        if (element._x_ids && element._x_ids[name]) return true;
    });
}
function setIdRoot(el, name) {
    if (!el._x_ids) el._x_ids = {};
    if (!el._x_ids[name]) el._x_ids[name] = findAndIncrementId(name);
}
// packages/alpinejs/src/magics/$id.js
magic("id", (el)=>(name, key = null)=>{
        let root = closestIdRoot(el, name);
        let id = root ? root._x_ids[name] : findAndIncrementId(name);
        return key ? `${name}-${id}-${key}` : `${name}-${id}`;
    });
// packages/alpinejs/src/magics/$el.js
magic("el", (el)=>el);
// packages/alpinejs/src/magics/index.js
warnMissingPluginMagic("Focus", "focus", "focus");
warnMissingPluginMagic("Persist", "persist", "persist");
function warnMissingPluginMagic(name, magicName, slug) {
    magic(magicName, (el)=>warn(`You can't use [$${directiveName}] without first installing the "${name}" plugin here: https://alpinejs.dev/plugins/${slug}`, el));
}
// packages/alpinejs/src/entangle.js
function entangle({ get: outerGet , set: outerSet  }, { get: innerGet , set: innerSet  }) {
    let firstRun = true;
    let outerHash, innerHash, outerHashLatest, innerHashLatest;
    let reference = effect(()=>{
        let outer, inner;
        if (firstRun) {
            outer = outerGet();
            innerSet(outer);
            inner = innerGet();
            firstRun = false;
        } else {
            outer = outerGet();
            inner = innerGet();
            outerHashLatest = JSON.stringify(outer);
            innerHashLatest = JSON.stringify(inner);
            if (outerHashLatest !== outerHash) {
                inner = innerGet();
                innerSet(outer);
                inner = outer;
            } else {
                outerSet(inner);
                outer = inner;
            }
        }
        outerHash = JSON.stringify(outer);
        innerHash = JSON.stringify(inner);
    });
    return ()=>{
        release(reference);
    };
}
// packages/alpinejs/src/directives/x-modelable.js
directive("modelable", (el, { expression  }, { effect: effect3 , evaluateLater: evaluateLater2 , cleanup: cleanup2  })=>{
    let func = evaluateLater2(expression);
    let innerGet = ()=>{
        let result;
        func((i)=>result = i);
        return result;
    };
    let evaluateInnerSet = evaluateLater2(`${expression} = __placeholder`);
    let innerSet = (val)=>evaluateInnerSet(()=>{}, {
            scope: {
                __placeholder: val
            }
        });
    let initialValue = innerGet();
    innerSet(initialValue);
    queueMicrotask(()=>{
        if (!el._x_model) return;
        el._x_removeModelListeners["default"]();
        let outerGet = el._x_model.get;
        let outerSet = el._x_model.set;
        let releaseEntanglement = entangle({
            get () {
                return outerGet();
            },
            set (value) {
                outerSet(value);
            }
        }, {
            get () {
                return innerGet();
            },
            set (value) {
                innerSet(value);
            }
        });
        cleanup2(releaseEntanglement);
    });
});
// packages/alpinejs/src/directives/x-teleport.js
var teleportContainerDuringClone = document.createElement("div");
directive("teleport", (el, { modifiers , expression  }, { cleanup: cleanup2  })=>{
    if (el.tagName.toLowerCase() !== "template") warn("x-teleport can only be used on a <template> tag", el);
    let target = skipDuringClone(()=>{
        return document.querySelector(expression);
    }, ()=>{
        return teleportContainerDuringClone;
    })();
    if (!target) warn(`Cannot find x-teleport element for selector: "${expression}"`);
    let clone2 = el.content.cloneNode(true).firstElementChild;
    el._x_teleport = clone2;
    clone2._x_teleportBack = el;
    if (el._x_forwardEvents) el._x_forwardEvents.forEach((eventName)=>{
        clone2.addEventListener(eventName, (e)=>{
            e.stopPropagation();
            el.dispatchEvent(new e.constructor(e.type, e));
        });
    });
    addScopeToNode(clone2, {}, el);
    mutateDom(()=>{
        if (modifiers.includes("prepend")) target.parentNode.insertBefore(clone2, target);
        else if (modifiers.includes("append")) target.parentNode.insertBefore(clone2, target.nextSibling);
        else target.appendChild(clone2);
        initTree(clone2);
        clone2._x_ignore = true;
    });
    cleanup2(()=>clone2.remove());
});
// packages/alpinejs/src/directives/x-ignore.js
var handler = ()=>{};
handler.inline = (el, { modifiers  }, { cleanup: cleanup2  })=>{
    modifiers.includes("self") ? el._x_ignoreSelf = true : el._x_ignore = true;
    cleanup2(()=>{
        modifiers.includes("self") ? delete el._x_ignoreSelf : delete el._x_ignore;
    });
};
directive("ignore", handler);
// packages/alpinejs/src/directives/x-effect.js
directive("effect", (el, { expression  }, { effect: effect3  })=>effect3(evaluateLater(el, expression)));
// packages/alpinejs/src/utils/on.js
function on(el, event, modifiers, callback) {
    let listenerTarget = el;
    let handler3 = (e)=>callback(e);
    let options = {};
    let wrapHandler = (callback2, wrapper)=>(e)=>wrapper(callback2, e);
    if (modifiers.includes("dot")) event = dotSyntax(event);
    if (modifiers.includes("camel")) event = camelCase2(event);
    if (modifiers.includes("passive")) options.passive = true;
    if (modifiers.includes("capture")) options.capture = true;
    if (modifiers.includes("window")) listenerTarget = window;
    if (modifiers.includes("document")) listenerTarget = document;
    if (modifiers.includes("prevent")) handler3 = wrapHandler(handler3, (next, e)=>{
        e.preventDefault();
        next(e);
    });
    if (modifiers.includes("stop")) handler3 = wrapHandler(handler3, (next, e)=>{
        e.stopPropagation();
        next(e);
    });
    if (modifiers.includes("self")) handler3 = wrapHandler(handler3, (next, e)=>{
        e.target === el && next(e);
    });
    if (modifiers.includes("away") || modifiers.includes("outside")) {
        listenerTarget = document;
        handler3 = wrapHandler(handler3, (next, e)=>{
            if (el.contains(e.target)) return;
            if (e.target.isConnected === false) return;
            if (el.offsetWidth < 1 && el.offsetHeight < 1) return;
            if (el._x_isShown === false) return;
            next(e);
        });
    }
    if (modifiers.includes("once")) handler3 = wrapHandler(handler3, (next, e)=>{
        next(e);
        listenerTarget.removeEventListener(event, handler3, options);
    });
    handler3 = wrapHandler(handler3, (next, e)=>{
        if (isKeyEvent(event)) {
            if (isListeningForASpecificKeyThatHasntBeenPressed(e, modifiers)) return;
        }
        next(e);
    });
    if (modifiers.includes("debounce")) {
        let nextModifier = modifiers[modifiers.indexOf("debounce") + 1] || "invalid-wait";
        let wait = isNumeric(nextModifier.split("ms")[0]) ? Number(nextModifier.split("ms")[0]) : 250;
        handler3 = debounce(handler3, wait);
    }
    if (modifiers.includes("throttle")) {
        let nextModifier = modifiers[modifiers.indexOf("throttle") + 1] || "invalid-wait";
        let wait = isNumeric(nextModifier.split("ms")[0]) ? Number(nextModifier.split("ms")[0]) : 250;
        handler3 = throttle(handler3, wait);
    }
    listenerTarget.addEventListener(event, handler3, options);
    return ()=>{
        listenerTarget.removeEventListener(event, handler3, options);
    };
}
function dotSyntax(subject) {
    return subject.replace(/-/g, ".");
}
function camelCase2(subject) {
    return subject.toLowerCase().replace(/-(\w)/g, (match, char)=>char.toUpperCase());
}
function isNumeric(subject) {
    return !Array.isArray(subject) && !isNaN(subject);
}
function kebabCase2(subject) {
    if ([
        " ",
        "_"
    ].includes(subject)) return subject;
    return subject.replace(/([a-z])([A-Z])/g, "$1-$2").replace(/[_\s]/, "-").toLowerCase();
}
function isKeyEvent(event) {
    return [
        "keydown",
        "keyup"
    ].includes(event);
}
function isListeningForASpecificKeyThatHasntBeenPressed(e, modifiers) {
    let keyModifiers = modifiers.filter((i)=>{
        return ![
            "window",
            "document",
            "prevent",
            "stop",
            "once"
        ].includes(i);
    });
    if (keyModifiers.includes("debounce")) {
        let debounceIndex = keyModifiers.indexOf("debounce");
        keyModifiers.splice(debounceIndex, isNumeric((keyModifiers[debounceIndex + 1] || "invalid-wait").split("ms")[0]) ? 2 : 1);
    }
    if (keyModifiers.includes("throttle")) {
        let debounceIndex = keyModifiers.indexOf("throttle");
        keyModifiers.splice(debounceIndex, isNumeric((keyModifiers[debounceIndex + 1] || "invalid-wait").split("ms")[0]) ? 2 : 1);
    }
    if (keyModifiers.length === 0) return false;
    if (keyModifiers.length === 1 && keyToModifiers(e.key).includes(keyModifiers[0])) return false;
    const systemKeyModifiers = [
        "ctrl",
        "shift",
        "alt",
        "meta",
        "cmd",
        "super"
    ];
    const selectedSystemKeyModifiers = systemKeyModifiers.filter((modifier)=>keyModifiers.includes(modifier));
    keyModifiers = keyModifiers.filter((i)=>!selectedSystemKeyModifiers.includes(i));
    if (selectedSystemKeyModifiers.length > 0) {
        const activelyPressedKeyModifiers = selectedSystemKeyModifiers.filter((modifier)=>{
            if (modifier === "cmd" || modifier === "super") modifier = "meta";
            return e[`${modifier}Key`];
        });
        if (activelyPressedKeyModifiers.length === selectedSystemKeyModifiers.length) {
            if (keyToModifiers(e.key).includes(keyModifiers[0])) return false;
        }
    }
    return true;
}
function keyToModifiers(key) {
    if (!key) return [];
    key = kebabCase2(key);
    let modifierToKeyMap = {
        ctrl: "control",
        slash: "/",
        space: " ",
        spacebar: " ",
        cmd: "meta",
        esc: "escape",
        up: "arrow-up",
        down: "arrow-down",
        left: "arrow-left",
        right: "arrow-right",
        period: ".",
        equal: "=",
        minus: "-",
        underscore: "_"
    };
    modifierToKeyMap[key] = key;
    return Object.keys(modifierToKeyMap).map((modifier)=>{
        if (modifierToKeyMap[modifier] === key) return modifier;
    }).filter((modifier)=>modifier);
}
// packages/alpinejs/src/directives/x-model.js
directive("model", (el, { modifiers , expression  }, { effect: effect3 , cleanup: cleanup2  })=>{
    let scopeTarget = el;
    if (modifiers.includes("parent")) scopeTarget = el.parentNode;
    let evaluateGet = evaluateLater(scopeTarget, expression);
    let evaluateSet;
    if (typeof expression === "string") evaluateSet = evaluateLater(scopeTarget, `${expression} = __placeholder`);
    else if (typeof expression === "function" && typeof expression() === "string") evaluateSet = evaluateLater(scopeTarget, `${expression()} = __placeholder`);
    else evaluateSet = ()=>{};
    let getValue = ()=>{
        let result;
        evaluateGet((value)=>result = value);
        return isGetterSetter(result) ? result.get() : result;
    };
    let setValue = (value)=>{
        let result;
        evaluateGet((value2)=>result = value2);
        if (isGetterSetter(result)) result.set(value);
        else evaluateSet(()=>{}, {
            scope: {
                __placeholder: value
            }
        });
    };
    if (typeof expression === "string" && el.type === "radio") mutateDom(()=>{
        if (!el.hasAttribute("name")) el.setAttribute("name", expression);
    });
    var event = el.tagName.toLowerCase() === "select" || [
        "checkbox",
        "radio"
    ].includes(el.type) || modifiers.includes("lazy") ? "change" : "input";
    let removeListener = on(el, event, modifiers, (e)=>{
        setValue(getInputValue(el, modifiers, e, getValue()));
    });
    if (!el._x_removeModelListeners) el._x_removeModelListeners = {};
    el._x_removeModelListeners["default"] = removeListener;
    cleanup2(()=>el._x_removeModelListeners["default"]());
    if (el.form) {
        let removeResetListener = on(el.form, "reset", [], (e)=>{
            nextTick(()=>el._x_model && el._x_model.set(el.value));
        });
        cleanup2(()=>removeResetListener());
    }
    el._x_model = {
        get () {
            return getValue();
        },
        set (value) {
            setValue(value);
        }
    };
    el._x_forceModelUpdate = (value)=>{
        value = value === void 0 ? getValue() : value;
        if (value === void 0 && typeof expression === "string" && expression.match(/\./)) value = "";
        window.fromModel = true;
        mutateDom(()=>bind(el, "value", value));
        delete window.fromModel;
    };
    effect3(()=>{
        let value = getValue();
        if (modifiers.includes("unintrusive") && document.activeElement.isSameNode(el)) return;
        el._x_forceModelUpdate(value);
    });
});
function getInputValue(el, modifiers, event, currentValue) {
    return mutateDom(()=>{
        if (event instanceof CustomEvent && event.detail !== void 0) return typeof event.detail != "undefined" ? event.detail : event.target.value;
        else if (el.type === "checkbox") {
            if (Array.isArray(currentValue)) {
                let newValue = modifiers.includes("number") ? safeParseNumber(event.target.value) : event.target.value;
                return event.target.checked ? currentValue.concat([
                    newValue
                ]) : currentValue.filter((el2)=>!checkedAttrLooseCompare2(el2, newValue));
            } else return event.target.checked;
        } else if (el.tagName.toLowerCase() === "select" && el.multiple) return modifiers.includes("number") ? Array.from(event.target.selectedOptions).map((option)=>{
            let rawValue = option.value || option.text;
            return safeParseNumber(rawValue);
        }) : Array.from(event.target.selectedOptions).map((option)=>{
            return option.value || option.text;
        });
        else {
            let rawValue = event.target.value;
            return modifiers.includes("number") ? safeParseNumber(rawValue) : modifiers.includes("trim") ? rawValue.trim() : rawValue;
        }
    });
}
function safeParseNumber(rawValue) {
    let number = rawValue ? parseFloat(rawValue) : null;
    return isNumeric2(number) ? number : rawValue;
}
function checkedAttrLooseCompare2(valueA, valueB) {
    return valueA == valueB;
}
function isNumeric2(subject) {
    return !Array.isArray(subject) && !isNaN(subject);
}
function isGetterSetter(value) {
    return value !== null && typeof value === "object" && typeof value.get === "function" && typeof value.set === "function";
}
// packages/alpinejs/src/directives/x-cloak.js
directive("cloak", (el)=>queueMicrotask(()=>mutateDom(()=>el.removeAttribute(prefix("cloak")))));
// packages/alpinejs/src/directives/x-init.js
addInitSelector(()=>`[${prefix("init")}]`);
directive("init", skipDuringClone((el, { expression  }, { evaluate: evaluate2  })=>{
    if (typeof expression === "string") return !!expression.trim() && evaluate2(expression, {}, false);
    return evaluate2(expression, {}, false);
}));
// packages/alpinejs/src/directives/x-text.js
directive("text", (el, { expression  }, { effect: effect3 , evaluateLater: evaluateLater2  })=>{
    let evaluate2 = evaluateLater2(expression);
    effect3(()=>{
        evaluate2((value)=>{
            mutateDom(()=>{
                el.textContent = value;
            });
        });
    });
});
// packages/alpinejs/src/directives/x-html.js
directive("html", (el, { expression  }, { effect: effect3 , evaluateLater: evaluateLater2  })=>{
    let evaluate2 = evaluateLater2(expression);
    effect3(()=>{
        evaluate2((value)=>{
            mutateDom(()=>{
                el.innerHTML = value;
                el._x_ignoreSelf = true;
                initTree(el);
                delete el._x_ignoreSelf;
            });
        });
    });
});
// packages/alpinejs/src/directives/x-bind.js
mapAttributes(startingWith(":", into(prefix("bind:"))));
directive("bind", (el, { value , modifiers , expression , original  }, { effect: effect3  })=>{
    if (!value) {
        let bindingProviders = {};
        injectBindingProviders(bindingProviders);
        let getBindings = evaluateLater(el, expression);
        getBindings((bindings)=>{
            applyBindingsObject(el, bindings, original);
        }, {
            scope: bindingProviders
        });
        return;
    }
    if (value === "key") return storeKeyForXFor(el, expression);
    let evaluate2 = evaluateLater(el, expression);
    effect3(()=>evaluate2((result)=>{
            if (result === void 0 && typeof expression === "string" && expression.match(/\./)) result = "";
            mutateDom(()=>bind(el, value, result, modifiers));
        }));
});
function storeKeyForXFor(el, expression) {
    el._x_keyExpression = expression;
}
// packages/alpinejs/src/directives/x-data.js
addRootSelector(()=>`[${prefix("data")}]`);
directive("data", skipDuringClone((el, { expression  }, { cleanup: cleanup2  })=>{
    expression = expression === "" ? "{}" : expression;
    let magicContext = {};
    injectMagics(magicContext, el);
    let dataProviderContext = {};
    injectDataProviders(dataProviderContext, magicContext);
    let data2 = evaluate(el, expression, {
        scope: dataProviderContext
    });
    if (data2 === void 0) data2 = {};
    injectMagics(data2, el);
    let reactiveData = reactive(data2);
    initInterceptors(reactiveData);
    let undo = addScopeToNode(el, reactiveData);
    reactiveData["init"] && evaluate(el, reactiveData["init"]);
    cleanup2(()=>{
        reactiveData["destroy"] && evaluate(el, reactiveData["destroy"]);
        undo();
    });
}));
// packages/alpinejs/src/directives/x-show.js
directive("show", (el, { modifiers , expression  }, { effect: effect3  })=>{
    let evaluate2 = evaluateLater(el, expression);
    if (!el._x_doHide) el._x_doHide = ()=>{
        mutateDom(()=>{
            el.style.setProperty("display", "none", modifiers.includes("important") ? "important" : void 0);
        });
    };
    if (!el._x_doShow) el._x_doShow = ()=>{
        mutateDom(()=>{
            if (el.style.length === 1 && el.style.display === "none") el.removeAttribute("style");
            else el.style.removeProperty("display");
        });
    };
    let hide = ()=>{
        el._x_doHide();
        el._x_isShown = false;
    };
    let show = ()=>{
        el._x_doShow();
        el._x_isShown = true;
    };
    let clickAwayCompatibleShow = ()=>setTimeout(show);
    let toggle = once((value)=>value ? show() : hide(), (value)=>{
        if (typeof el._x_toggleAndCascadeWithTransitions === "function") el._x_toggleAndCascadeWithTransitions(el, value, show, hide);
        else value ? clickAwayCompatibleShow() : hide();
    });
    let oldValue;
    let firstTime = true;
    effect3(()=>evaluate2((value)=>{
            if (!firstTime && value === oldValue) return;
            if (modifiers.includes("immediate")) value ? clickAwayCompatibleShow() : hide();
            toggle(value);
            oldValue = value;
            firstTime = false;
        }));
});
// packages/alpinejs/src/directives/x-for.js
directive("for", (el, { expression  }, { effect: effect3 , cleanup: cleanup2  })=>{
    let iteratorNames = parseForExpression(expression);
    let evaluateItems = evaluateLater(el, iteratorNames.items);
    let evaluateKey = evaluateLater(el, el._x_keyExpression || "index");
    el._x_prevKeys = [];
    el._x_lookup = {};
    effect3(()=>loop(el, iteratorNames, evaluateItems, evaluateKey));
    cleanup2(()=>{
        Object.values(el._x_lookup).forEach((el2)=>el2.remove());
        delete el._x_prevKeys;
        delete el._x_lookup;
    });
});
function loop(el, iteratorNames, evaluateItems, evaluateKey) {
    let isObject2 = (i)=>typeof i === "object" && !Array.isArray(i);
    let templateEl = el;
    evaluateItems((items)=>{
        if (isNumeric3(items) && items >= 0) items = Array.from(Array(items).keys(), (i)=>i + 1);
        if (items === void 0) items = [];
        let lookup = el._x_lookup;
        let prevKeys = el._x_prevKeys;
        let scopes = [];
        let keys = [];
        if (isObject2(items)) items = Object.entries(items).map(([key, value])=>{
            let scope2 = getIterationScopeVariables(iteratorNames, value, key, items);
            evaluateKey((value2)=>keys.push(value2), {
                scope: {
                    index: key,
                    ...scope2
                }
            });
            scopes.push(scope2);
        });
        else for(let i = 0; i < items.length; i++){
            let scope2 = getIterationScopeVariables(iteratorNames, items[i], i, items);
            evaluateKey((value)=>keys.push(value), {
                scope: {
                    index: i,
                    ...scope2
                }
            });
            scopes.push(scope2);
        }
        let adds = [];
        let moves = [];
        let removes = [];
        let sames = [];
        for(let i = 0; i < prevKeys.length; i++){
            let key = prevKeys[i];
            if (keys.indexOf(key) === -1) removes.push(key);
        }
        prevKeys = prevKeys.filter((key)=>!removes.includes(key));
        let lastKey = "template";
        for(let i = 0; i < keys.length; i++){
            let key = keys[i];
            let prevIndex = prevKeys.indexOf(key);
            if (prevIndex === -1) {
                prevKeys.splice(i, 0, key);
                adds.push([
                    lastKey,
                    i
                ]);
            } else if (prevIndex !== i) {
                let keyInSpot = prevKeys.splice(i, 1)[0];
                let keyForSpot = prevKeys.splice(prevIndex - 1, 1)[0];
                prevKeys.splice(i, 0, keyForSpot);
                prevKeys.splice(prevIndex, 0, keyInSpot);
                moves.push([
                    keyInSpot,
                    keyForSpot
                ]);
            } else sames.push(key);
            lastKey = key;
        }
        for(let i = 0; i < removes.length; i++){
            let key = removes[i];
            if (!!lookup[key]._x_effects) lookup[key]._x_effects.forEach(dequeueJob);
            lookup[key].remove();
            lookup[key] = null;
            delete lookup[key];
        }
        for(let i = 0; i < moves.length; i++){
            let [keyInSpot, keyForSpot] = moves[i];
            let elInSpot = lookup[keyInSpot];
            let elForSpot = lookup[keyForSpot];
            let marker = document.createElement("div");
            mutateDom(()=>{
                elForSpot.after(marker);
                elInSpot.after(elForSpot);
                elForSpot._x_currentIfEl && elForSpot.after(elForSpot._x_currentIfEl);
                marker.before(elInSpot);
                elInSpot._x_currentIfEl && elInSpot.after(elInSpot._x_currentIfEl);
                marker.remove();
            });
            refreshScope(elForSpot, scopes[keys.indexOf(keyForSpot)]);
        }
        for(let i = 0; i < adds.length; i++){
            let [lastKey2, index] = adds[i];
            let lastEl = lastKey2 === "template" ? templateEl : lookup[lastKey2];
            if (lastEl._x_currentIfEl) lastEl = lastEl._x_currentIfEl;
            let scope2 = scopes[index];
            let key = keys[index];
            let clone2 = document.importNode(templateEl.content, true).firstElementChild;
            addScopeToNode(clone2, reactive(scope2), templateEl);
            mutateDom(()=>{
                lastEl.after(clone2);
                initTree(clone2);
            });
            if (typeof key === "object") warn("x-for key cannot be an object, it must be a string or an integer", templateEl);
            lookup[key] = clone2;
        }
        for(let i = 0; i < sames.length; i++)refreshScope(lookup[sames[i]], scopes[keys.indexOf(sames[i])]);
        templateEl._x_prevKeys = keys;
    });
}
function parseForExpression(expression) {
    let forIteratorRE = /,([^,\}\]]*)(?:,([^,\}\]]*))?$/;
    let stripParensRE = /^\s*\(|\)\s*$/g;
    let forAliasRE = /([\s\S]*?)\s+(?:in|of)\s+([\s\S]*)/;
    let inMatch = expression.match(forAliasRE);
    if (!inMatch) return;
    let res = {};
    res.items = inMatch[2].trim();
    let item = inMatch[1].replace(stripParensRE, "").trim();
    let iteratorMatch = item.match(forIteratorRE);
    if (iteratorMatch) {
        res.item = item.replace(forIteratorRE, "").trim();
        res.index = iteratorMatch[1].trim();
        if (iteratorMatch[2]) res.collection = iteratorMatch[2].trim();
    } else res.item = item;
    return res;
}
function getIterationScopeVariables(iteratorNames, item, index, items) {
    let scopeVariables = {};
    if (/^\[.*\]$/.test(iteratorNames.item) && Array.isArray(item)) {
        let names = iteratorNames.item.replace("[", "").replace("]", "").split(",").map((i)=>i.trim());
        names.forEach((name, i)=>{
            scopeVariables[name] = item[i];
        });
    } else if (/^\{.*\}$/.test(iteratorNames.item) && !Array.isArray(item) && typeof item === "object") {
        let names = iteratorNames.item.replace("{", "").replace("}", "").split(",").map((i)=>i.trim());
        names.forEach((name)=>{
            scopeVariables[name] = item[name];
        });
    } else scopeVariables[iteratorNames.item] = item;
    if (iteratorNames.index) scopeVariables[iteratorNames.index] = index;
    if (iteratorNames.collection) scopeVariables[iteratorNames.collection] = items;
    return scopeVariables;
}
function isNumeric3(subject) {
    return !Array.isArray(subject) && !isNaN(subject);
}
// packages/alpinejs/src/directives/x-ref.js
function handler2() {}
handler2.inline = (el, { expression  }, { cleanup: cleanup2  })=>{
    let root = closestRoot(el);
    if (!root._x_refs) root._x_refs = {};
    root._x_refs[expression] = el;
    cleanup2(()=>delete root._x_refs[expression]);
};
directive("ref", handler2);
// packages/alpinejs/src/directives/x-if.js
directive("if", (el, { expression  }, { effect: effect3 , cleanup: cleanup2  })=>{
    let evaluate2 = evaluateLater(el, expression);
    let show = ()=>{
        if (el._x_currentIfEl) return el._x_currentIfEl;
        let clone2 = el.content.cloneNode(true).firstElementChild;
        addScopeToNode(clone2, {}, el);
        mutateDom(()=>{
            el.after(clone2);
            initTree(clone2);
        });
        el._x_currentIfEl = clone2;
        el._x_undoIf = ()=>{
            walk(clone2, (node)=>{
                if (!!node._x_effects) node._x_effects.forEach(dequeueJob);
            });
            clone2.remove();
            delete el._x_currentIfEl;
        };
        return clone2;
    };
    let hide = ()=>{
        if (!el._x_undoIf) return;
        el._x_undoIf();
        delete el._x_undoIf;
    };
    effect3(()=>evaluate2((value)=>{
            value ? show() : hide();
        }));
    cleanup2(()=>el._x_undoIf && el._x_undoIf());
});
// packages/alpinejs/src/directives/x-id.js
directive("id", (el, { expression  }, { evaluate: evaluate2  })=>{
    let names = evaluate2(expression);
    names.forEach((name)=>setIdRoot(el, name));
});
// packages/alpinejs/src/directives/x-on.js
mapAttributes(startingWith("@", into(prefix("on:"))));
directive("on", skipDuringClone((el, { value , modifiers , expression  }, { cleanup: cleanup2  })=>{
    let evaluate2 = expression ? evaluateLater(el, expression) : ()=>{};
    if (el.tagName.toLowerCase() === "template") {
        if (!el._x_forwardEvents) el._x_forwardEvents = [];
        if (!el._x_forwardEvents.includes(value)) el._x_forwardEvents.push(value);
    }
    let removeListener = on(el, value, modifiers, (e)=>{
        evaluate2(()=>{}, {
            scope: {
                $event: e
            },
            params: [
                e
            ]
        });
    });
    cleanup2(()=>removeListener());
}));
// packages/alpinejs/src/directives/index.js
warnMissingPluginDirective("Collapse", "collapse", "collapse");
warnMissingPluginDirective("Intersect", "intersect", "intersect");
warnMissingPluginDirective("Focus", "trap", "focus");
warnMissingPluginDirective("Mask", "mask", "mask");
function warnMissingPluginDirective(name, directiveName2, slug) {
    directive(directiveName2, (el)=>warn(`You can't use [x-${directiveName2}] without first installing the "${name}" plugin here: https://alpinejs.dev/plugins/${slug}`, el));
}
// packages/alpinejs/src/index.js
alpine_default.setEvaluator(normalEvaluator);
alpine_default.setReactivityEngine({
    reactive: reactive2,
    effect: effect2,
    release: stop,
    raw: toRaw
});
var src_default = alpine_default;
// packages/alpinejs/builds/module.js
var module_default = src_default;

},{"@parcel/transformer-js/src/esmodule-helpers.js":"gkKU3"}],"gkKU3":[function(require,module,exports) {
exports.interopDefault = function(a) {
    return a && a.__esModule ? a : {
        default: a
    };
};
exports.defineInteropFlag = function(a) {
    Object.defineProperty(a, "__esModule", {
        value: true
    });
};
exports.exportAll = function(source, dest) {
    Object.keys(source).forEach(function(key) {
        if (key === "default" || key === "__esModule" || dest.hasOwnProperty(key)) return;
        Object.defineProperty(dest, key, {
            enumerable: true,
            get: function() {
                return source[key];
            }
        });
    });
    return dest;
};
exports.export = function(dest, destName, get) {
    Object.defineProperty(dest, destName, {
        enumerable: true,
        get: get
    });
};

},{}]},["aQYcG","l35qe"], "l35qe", "parcelRequire7131")

//# sourceMappingURL=app.js.map
