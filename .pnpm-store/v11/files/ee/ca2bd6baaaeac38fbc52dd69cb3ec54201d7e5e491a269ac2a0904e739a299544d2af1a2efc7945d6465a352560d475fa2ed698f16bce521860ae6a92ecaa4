import { Player as e, Player as t } from "@lordicon/web";
//#region src/parsers.ts
var n = {
	aliceblue: "#f0f8ff",
	antiquewhite: "#faebd7",
	aqua: "#00ffff",
	aquamarine: "#7fffd4",
	azure: "#f0ffff",
	beige: "#f5f5dc",
	bisque: "#ffe4c4",
	black: "#000000",
	blanchedalmond: "#ffebcd",
	blue: "#0000ff",
	blueviolet: "#8a2be2",
	brown: "#a52a2a",
	burlywood: "#deb887",
	cadetblue: "#5f9ea0",
	chartreuse: "#7fff00",
	chocolate: "#d2691e",
	coral: "#ff7f50",
	cornflowerblue: "#6495ed",
	cornsilk: "#fff8dc",
	crimson: "#dc143c",
	cyan: "#00ffff",
	darkblue: "#00008b",
	darkcyan: "#008b8b",
	darkgoldenrod: "#b8860b",
	darkgray: "#a9a9a9",
	darkgreen: "#006400",
	darkkhaki: "#bdb76b",
	darkmagenta: "#8b008b",
	darkolivegreen: "#556b2f",
	darkorange: "#ff8c00",
	darkorchid: "#9932cc",
	darkred: "#8b0000",
	darksalmon: "#e9967a",
	darkseagreen: "#8fbc8f",
	darkslateblue: "#483d8b",
	darkslategray: "#2f4f4f",
	darkturquoise: "#00ced1",
	darkviolet: "#9400d3",
	deeppink: "#ff1493",
	deepskyblue: "#00bfff",
	dimgray: "#696969",
	dodgerblue: "#1e90ff",
	firebrick: "#b22222",
	floralwhite: "#fffaf0",
	forestgreen: "#228b22",
	fuchsia: "#ff00ff",
	gainsboro: "#dcdcdc",
	ghostwhite: "#f8f8ff",
	gold: "#ffd700",
	goldenrod: "#daa520",
	gray: "#808080",
	green: "#008000",
	greenyellow: "#adff2f",
	honeydew: "#f0fff0",
	hotpink: "#ff69b4",
	"indianred ": "#cd5c5c",
	indigo: "#4b0082",
	ivory: "#fffff0",
	khaki: "#f0e68c",
	lavender: "#e6e6fa",
	lavenderblush: "#fff0f5",
	lawngreen: "#7cfc00",
	lemonchiffon: "#fffacd",
	lightblue: "#add8e6",
	lightcoral: "#f08080",
	lightcyan: "#e0ffff",
	lightgoldenrodyellow: "#fafad2",
	lightgrey: "#d3d3d3",
	lightgreen: "#90ee90",
	lightpink: "#ffb6c1",
	lightsalmon: "#ffa07a",
	lightseagreen: "#20b2aa",
	lightskyblue: "#87cefa",
	lightslategray: "#778899",
	lightsteelblue: "#b0c4de",
	lightyellow: "#ffffe0",
	lime: "#00ff00",
	limegreen: "#32cd32",
	linen: "#faf0e6",
	magenta: "#ff00ff",
	maroon: "#800000",
	mediumaquamarine: "#66cdaa",
	mediumblue: "#0000cd",
	mediumorchid: "#ba55d3",
	mediumpurple: "#9370d8",
	mediumseagreen: "#3cb371",
	mediumslateblue: "#7b68ee",
	mediumspringgreen: "#00fa9a",
	mediumturquoise: "#48d1cc",
	mediumvioletred: "#c71585",
	midnightblue: "#191970",
	mintcream: "#f5fffa",
	mistyrose: "#ffe4e1",
	moccasin: "#ffe4b5",
	navajowhite: "#ffdead",
	navy: "#000080",
	oldlace: "#fdf5e6",
	olive: "#808000",
	olivedrab: "#6b8e23",
	orange: "#ffa500",
	orangered: "#ff4500",
	orchid: "#da70d6",
	palegoldenrod: "#eee8aa",
	palegreen: "#98fb98",
	paleturquoise: "#afeeee",
	palevioletred: "#d87093",
	papayawhip: "#ffefd5",
	peachpuff: "#ffdab9",
	peru: "#cd853f",
	pink: "#ffc0cb",
	plum: "#dda0dd",
	powderblue: "#b0e0e6",
	purple: "#800080",
	rebeccapurple: "#663399",
	red: "#ff0000",
	rosybrown: "#bc8f8f",
	royalblue: "#4169e1",
	saddlebrown: "#8b4513",
	salmon: "#fa8072",
	sandybrown: "#f4a460",
	seagreen: "#2e8b57",
	seashell: "#fff5ee",
	sienna: "#a0522d",
	silver: "#c0c0c0",
	skyblue: "#87ceeb",
	slateblue: "#6a5acd",
	slategray: "#708090",
	snow: "#fffafa",
	springgreen: "#00ff7f",
	steelblue: "#4682b4",
	tan: "#d2b48c",
	teal: "#008080",
	thistle: "#d8bfd8",
	tomato: "#ff6347",
	turquoise: "#40e0d0",
	violet: "#ee82ee",
	wheat: "#f5deb3",
	white: "#ffffff",
	whitesmoke: "#f5f5f5",
	yellow: "#ffff00",
	yellowgreen: "#9acd32"
};
function r(e) {
	return e.startsWith("#") ? e.length === 4 ? `#${e[1]}${e[1]}${e[2]}${e[2]}${e[3]}${e[3]}` : e : n[e.toLowerCase()] || "#000000";
}
function i(e) {
	if (!(!e || typeof e != "string")) return e.split(",").filter((e) => e).map((e) => e.split(":")).filter((e) => e.length == 2).reduce((e, t) => {
		let n = t[0];
		return e[n.toLowerCase()] = r(t[1]), e;
	}, {});
}
function a(e) {
	if (e === "light" || e === 1 || e === "1") return 1;
	if (e === "regular" || e === 2 || e === "2") return 2;
	if (e === "bold" || e === 3 || e === "3") return 3;
}
function o(e) {
	if (typeof e == "string") return e;
}
//#endregion
//#region \0@oxc-project+runtime@0.133.0/helpers/esm/typeof.js
function s(e) {
	"@babel/helpers - typeof";
	return s = typeof Symbol == "function" && typeof Symbol.iterator == "symbol" ? function(e) {
		return typeof e;
	} : function(e) {
		return e && typeof Symbol == "function" && e.constructor === Symbol && e !== Symbol.prototype ? "symbol" : typeof e;
	}, s(e);
}
//#endregion
//#region \0@oxc-project+runtime@0.133.0/helpers/esm/toPrimitive.js
function c(e, t) {
	if (s(e) != "object" || !e) return e;
	var n = e[Symbol.toPrimitive];
	if (n !== void 0) {
		var r = n.call(e, t || "default");
		if (s(r) != "object") return r;
		throw TypeError("@@toPrimitive must return a primitive value.");
	}
	return (t === "string" ? String : Number)(e);
}
//#endregion
//#region \0@oxc-project+runtime@0.133.0/helpers/esm/toPropertyKey.js
function l(e) {
	var t = c(e, "string");
	return s(t) == "symbol" ? t : t + "";
}
//#endregion
//#region \0@oxc-project+runtime@0.133.0/helpers/esm/defineProperty.js
function u(e, t, n) {
	return (t = l(t)) in e ? Object.defineProperty(e, t, {
		value: n,
		enumerable: !0,
		configurable: !0,
		writable: !0
	}) : e[t] = n, e;
}
//#endregion
//#region \0@oxc-project+runtime@0.133.0/helpers/esm/asyncToGenerator.js
function d(e, t, n, r, i, a, o) {
	try {
		var s = e[a](o), c = s.value;
	} catch (e) {
		n(e);
		return;
	}
	s.done ? t(c) : Promise.resolve(c).then(r, i);
}
function f(e) {
	return function() {
		var t = this, n = arguments;
		return new Promise(function(r, i) {
			var a = e.apply(t, n);
			function o(e) {
				d(a, r, i, o, s, "next", e);
			}
			function s(e) {
				d(a, r, i, o, s, "throw", e);
			}
			o(void 0);
		});
	};
}
//#endregion
//#region src/element.ts
var p = [
	"click",
	"mouseenter",
	"mouseleave"
], m = "adoptedStyleSheets" in Document.prototype && "replace" in CSSStyleSheet.prototype, h = "\n    :host {\n        position: relative;\n        display: inline-block;\n        width: 32px;\n        height: 32px;\n        transform: translate3d(0px, 0px, 0px);\n    }\n\n    :host(.current-color) svg path[fill] {\n        fill: currentColor;\n    }\n\n    :host(.current-color) svg path[stroke] {\n        stroke: currentColor;\n    }\n\n    svg {\n        position: absolute;\n        pointer-events: none;\n        display: block;\n        transform: unset!important;\n    }\n\n    ::slotted(*) {\n        position: absolute;\n        left: 0;\n        top: 0;\n        width: 100%;\n        height: 100%;\n    }\n", g = null, _ = [
	"colors",
	"src",
	"state",
	"trigger",
	"loading",
	"target",
	"stroke",
	"speed"
], v = class e extends HTMLElement {
	constructor(...e) {
		super(...e), u(this, "_root", void 0), u(this, "_isConnected", !1), u(this, "_ready", !1), u(this, "_assignedIconData", void 0), u(this, "_loadedIconData", void 0), u(this, "_triggerInstance", void 0), u(this, "_playerInstance", void 0), u(this, "_animationContainer", void 0), u(this, "delayedLoading", null);
	}
	static get version() {
		return "__BUILD_VERSION__";
	}
	static get observedAttributes() {
		return _;
	}
	static defineTrigger(t, n) {
		e._definedTriggers.set(t, n);
	}
	attributeChangedCallback(e, t, n) {
		this[`${e}Changed`].call(this);
	}
	connectedCallback() {
		if (this._root || this.createElements(), this.loading === "lazy") {
			let e;
			this.delayedLoading = (t) => {
				e.unobserve(this), e = void 0, this.delayedLoading = null, t || this.createPlayer();
			}, e = new IntersectionObserver((t, n) => {
				t.forEach((t) => {
					t.isIntersecting && e && this.delayedLoading && this.delayedLoading();
				});
			}), e.observe(this);
		} else if (this.loading === "interaction") {
			let e;
			this.delayedLoading = (r) => {
				for (let e of p) (t || this).removeEventListener(e, n);
				this.delayedLoading = null, r || this.createPlayer().then(() => {
					e && (t || this).dispatchEvent(new Event(e));
				});
			};
			let t = this.target ? this.findTarget(this.target) : null, n = (t) => {
				let n = t == null ? void 0 : t.type;
				e ? e = n : (e = n, this.delayedLoading && this.delayedLoading());
			};
			n = n.bind(this);
			for (let e of p) (t || this).addEventListener(e, n);
		} else if (this.loading === "delay") {
			this.delayedLoading = (e) => {
				this.delayedLoading = null, e || this.createPlayer();
			};
			let e = this.hasAttribute("loading-delay") ? +this.getAttribute("loading-delay") : 0;
			setTimeout(() => {
				this.delayedLoading && this.delayedLoading();
			}, e);
		} else this.createPlayer();
		this._isConnected = !0;
	}
	disconnectedCallback() {
		this.delayedLoading && this.delayedLoading(!0), this.destroyPlayer(), this._isConnected = !1;
	}
	findTarget(e) {
		let t = this.closest(e);
		if (t) return t;
		let n = this.getRootNode();
		return n instanceof ShadowRoot && n.host ? this.findTargetAcrossShadowBoundaries(n.host, e) : null;
	}
	findTargetAcrossShadowBoundaries(e, t) {
		let n = e;
		for (; n;) {
			if (n.nodeType === Node.ELEMENT_NODE && n.matches(t)) return n;
			if (n.parentNode) n = n.parentNode;
			else {
				let e = n.getRootNode();
				if (e instanceof ShadowRoot) n = e.host;
				else break;
			}
		}
		return null;
	}
	createElements() {
		if (this._root = this.attachShadow({ mode: "open" }), m) g || (g = new CSSStyleSheet(), g.replaceSync(h)), this._root.adoptedStyleSheets = [g];
		else {
			let e = document.createElement("style");
			e.innerHTML = h, this._root.appendChild(e);
		}
		let e = document.createElement("div");
		e.classList.add("body"), this._root.appendChild(e), this._animationContainer = e, this.createSlot();
	}
	createSlot() {
		let e = document.createElement("slot");
		this._root.appendChild(e);
	}
	destroySlot() {
		let e = this._root.querySelector("slot");
		e && this._root.removeChild(e);
	}
	playerFactory(e, n, r) {
		return new t(e, n, r, { autoInit: !1 });
	}
	createPlayer() {
		var e = this;
		return f(function* () {
			if (e.delayedLoading) return;
			let t = yield e.loadIconData();
			if (!t) return;
			e._playerInstance = e.playerFactory(e.animationContainer, t, {
				state: o(e.state),
				stroke: a(e.stroke),
				colors: i(e.colors),
				scale: parseFloat("" + e.getAttribute("scale") || ""),
				axisX: parseFloat("" + e.getAttribute("axis-x") || ""),
				axisY: parseFloat("" + e.getAttribute("axis-y") || "")
			});
			let n = Object.entries(e._playerInstance.colors || {});
			if (n.length) {
				let t = "";
				for (let [e, r] of n) t += `
                    :host(:not(.current-color)) svg path[fill].${e} {
                        fill: var(--lord-icon-${e}, var(--lord-icon-${e}-base, #000));
                    }
        
                    :host(:not(.current-color)) svg path[stroke].${e} {
                        stroke: var(--lord-icon-${e}, var(--lord-icon-${e}-base, #000));
                    }
                `;
				let r = document.createElement("style");
				r.innerHTML = t, e.animationContainer.appendChild(r);
			}
			e._playerInstance.init(), e._playerInstance.addEventListener("ready", () => {
				e._triggerInstance && e._triggerInstance.onReady && e._triggerInstance.onReady();
			}), e._playerInstance.addEventListener("refresh", () => {
				e.refresh(), e._triggerInstance && e._triggerInstance.onRefresh && e._triggerInstance.onRefresh();
			}), e._playerInstance.addEventListener("complete", () => {
				e._triggerInstance && e._triggerInstance.onComplete && e._triggerInstance.onComplete();
			}), e._playerInstance.addEventListener("frame", () => {
				e._triggerInstance && e._triggerInstance.onFrame && e._triggerInstance.onFrame();
			}), e.refresh(), e.triggerChanged(), yield new Promise((t, n) => {
				e._playerInstance.ready ? t() : e._playerInstance.addEventListener("ready", t);
			}), e.destroySlot(), e._ready = !0, e.dispatchEvent(new CustomEvent("ready"));
		})();
	}
	destroyPlayer() {
		this._ready = !1, this._loadedIconData = void 0, this._triggerInstance && (this._triggerInstance.onDisconnected && this._triggerInstance.onDisconnected(), this._triggerInstance = void 0), this._playerInstance && (this._playerInstance.destroy(), this._playerInstance = void 0, this.createSlot());
	}
	loadIconData() {
		var e = this;
		return f(function* () {
			let t = e.icon;
			return !t && e.src && (e._loadedIconData = t = yield (yield fetch(e.src)).json()), t;
		})();
	}
	refresh() {
		this.movePaletteToCssVariables();
	}
	movePaletteToCssVariables() {
		for (let [e, t] of Object.entries(this._playerInstance.colors || {})) t ? this.animationContainer.style.setProperty(`--lord-icon-${e}-base`, t) : this.animationContainer.style.removeProperty(`--lord-icon-${e}-base`);
	}
	targetChanged() {
		this.triggerChanged();
	}
	loadingChanged() {}
	triggerChanged() {
		if (this._triggerInstance) {
			var t;
			this._triggerInstance.onDisconnected && this._triggerInstance.onDisconnected(), this._triggerInstance = void 0, (t = this._playerInstance) == null || t.pause();
		}
		if (!this.trigger || !this._playerInstance) return;
		let n = e._definedTriggers.get(this.trigger);
		if (!n) throw Error(`Can't use unregistered trigger: '${this.trigger}'!`);
		let r = this.target ? this.findTarget(this.target) : null;
		this._triggerInstance = new n(this._playerInstance, this, r || this), this._triggerInstance.onConnected && this._triggerInstance.onConnected(), this._playerInstance.ready && this._triggerInstance.onReady && this._triggerInstance.onReady();
	}
	colorsChanged() {
		this._playerInstance && (this._playerInstance.colors = i(this.colors) || null);
	}
	strokeChanged() {
		this._playerInstance && (this._playerInstance.stroke = a(this.stroke) || null);
	}
	speedChanged() {
		if (!this._playerInstance) return;
		let e = this.getAttribute("speed");
		if (e) {
			let t = parseFloat(e);
			isNaN(t) ? this._playerInstance.speed = 1 : this._playerInstance.speed = t;
		} else this._playerInstance.speed = 1;
	}
	stateChanged() {
		var e, t;
		this._playerInstance && (this._playerInstance.state = this.state, (e = this._triggerInstance) == null || (t = e.onState) == null || t.call(e));
	}
	iconChanged() {
		this._isConnected && (this.destroyPlayer(), this.createPlayer());
	}
	srcChanged() {
		this._isConnected && (this.destroyPlayer(), this.createPlayer());
	}
	set icon(e) {
		e !== this._assignedIconData && (this._assignedIconData = e, this._loadedIconData = void 0, this.iconChanged());
	}
	get icon() {
		return this._assignedIconData || this._loadedIconData;
	}
	set src(e) {
		e ? this.setAttribute("src", e) : this.removeAttribute("src");
	}
	get src() {
		return this.getAttribute("src");
	}
	set state(e) {
		e ? this.setAttribute("state", e) : this.removeAttribute("state");
	}
	get state() {
		return this.getAttribute("state");
	}
	set colors(e) {
		e ? this.setAttribute("colors", e) : this.removeAttribute("colors");
	}
	get colors() {
		return this.getAttribute("colors");
	}
	set trigger(e) {
		e ? this.setAttribute("trigger", e) : this.removeAttribute("trigger");
	}
	get trigger() {
		return this.getAttribute("trigger");
	}
	set loading(e) {
		e ? this.setAttribute("loading", e) : this.removeAttribute("loading");
	}
	get loading() {
		if (this.getAttribute("loading")) {
			let e = this.getAttribute("loading").toLowerCase();
			if (e === "lazy") return "lazy";
			if (e === "interaction") return "interaction";
			if (e === "delay") return "delay";
		}
		return null;
	}
	set target(e) {
		e ? this.setAttribute("target", e) : this.removeAttribute("target");
	}
	get target() {
		return this.getAttribute("target");
	}
	set stroke(e) {
		e ? this.setAttribute("stroke", e) : this.removeAttribute("stroke");
	}
	get stroke() {
		return this.hasAttribute("stroke") ? this.getAttribute("stroke") : null;
	}
	set speed(e) {
		e ? this.setAttribute("speed", String(e)) : this.removeAttribute("speed");
	}
	get speed() {
		let e = this.getAttribute("speed");
		if (e) {
			let t = parseFloat(e);
			if (!isNaN(t)) return t;
		}
		return 1;
	}
	get ready() {
		return this._ready;
	}
	get readyPromise() {
		return this._ready ? Promise.resolve() : new Promise((e) => {
			this.addEventListener("ready", () => {
				e();
			}, { once: !0 });
		});
	}
	get playerInstance() {
		return this._playerInstance;
	}
	get triggerInstance() {
		return this._triggerInstance;
	}
	get animationContainer() {
		return this._animationContainer;
	}
};
u(v, "_definedTriggers", /* @__PURE__ */ new Map());
//#endregion
//#region src/triggers/boomerang.ts
var y = class {
	constructor(e, t, n) {
		u(this, "player", void 0), u(this, "element", void 0), u(this, "targetElement", void 0), u(this, "segments", void 0), u(this, "queue", []), u(this, "connected", !1), u(this, "targetState", void 0), u(this, "delayTimer", null), u(this, "intersectionObserver", void 0), this.player = e, this.element = t, this.targetElement = n, this.onClick = this.onClick.bind(this), this.onMouseEnter = this.onMouseEnter.bind(this), this.handleState(), this.replay();
	}
	onConnected() {
		this.connected = !0, this.targetElement.addEventListener("click", this.onClick), this.targetElement.addEventListener("mouseenter", this.onMouseEnter), this.targetState && (this.loading ? this.play(!0) : this.initIntersectionObserver());
	}
	onDisconnected() {
		this.connected = !1, this.targetElement.removeEventListener("click", this.onClick), this.targetElement.removeEventListener("mouseenter", this.onMouseEnter), this.cleanup();
	}
	onMouseEnter() {
		this.queue.push(0), this.queue.push(1), this.handleQueue();
	}
	onComplete() {
		this.targetState ? this.resetState() : this.handleQueue();
	}
	onState() {
		this.handleState();
	}
	onClick() {
		this.clickToReplay && this.replay();
	}
	play(e) {
		this.player.playing || this.delayTimer || (e && this.delay > 0 ? this.scheduleDelayedPlay() : this.player.playFromStart());
	}
	replay() {
		this.player.playing || !this.player.state || !this.intro || (this.targetState = this.player.state, this.player.state = this.intro, this.connected && this.play());
	}
	scheduleDelayedPlay() {
		this.resetDelayTimer(), this.delayTimer = setTimeout(() => {
			this.player.playFromStart(), this.delayTimer = null;
		}, this.delay);
	}
	handleQueue() {
		if (this.player.playing || !this.queue.length) return;
		let e = this.queue.shift();
		if (this.segments) {
			var t;
			let n = (t = this.segments) == null ? void 0 : t[e];
			this.player.direction = 1, this.player.switchSegment(n);
		} else this.player.direction = e === 0 ? 1 : -1;
		this.player.play();
	}
	handleState() {
		this.segments = void 0;
		let e = this.player.availableStates.find((e) => e.name === this.player.state);
		if (!e) return;
		let t = 0;
		if (e.params.length) {
			let n = parseFloat(e.params[0]);
			!isNaN(n) && n > 0 && n <= 1 && (t = n);
		}
		if (!t) return;
		let n = [e.time, e.time + Math.floor((e.duration + 1) * t)], r = [n[1], e.time + e.duration + 1];
		this.segments = [n, r];
	}
	initIntersectionObserver() {
		if (this.intersectionObserver) return;
		let e = (e) => {
			e.forEach((e) => {
				e.isIntersecting && (this.play(!0), this.resetIntersectionObserver());
			});
		};
		this.intersectionObserver = new IntersectionObserver(e, { threshold: .5 }), this.intersectionObserver.observe(this.element);
	}
	resetIntersectionObserver() {
		this.intersectionObserver && (this.intersectionObserver.unobserve(this.element), this.intersectionObserver = void 0);
	}
	resetDelayTimer() {
		this.delayTimer && (clearTimeout(this.delayTimer), this.delayTimer = null);
	}
	resetState() {
		return this.targetState ? (this.player.state = this.targetState, this.targetState = void 0, !0) : !1;
	}
	resetPlayer() {
		this.player.direction = 1, this.segments && (this.player.switchSegment([this.segments[0][0], this.segments[1][1]]), this.segments = void 0, this.queue = []);
	}
	cleanup() {
		this.resetPlayer(), this.resetIntersectionObserver(), this.resetDelayTimer(), this.resetState();
	}
	get intro() {
		if (!this.element.hasAttribute("intro")) return null;
		let e = this.element.getAttribute("intro"), t = this.player.availableStates.find((t) => t.name === e);
		return t || (t = this.player.availableStates.find((e) => e.name.startsWith("in-"))), (t == null ? void 0 : t.name) || null;
	}
	get delay() {
		let e = this.element.hasAttribute("delay") ? +(this.element.getAttribute("delay") || 0) : 0;
		return Math.max(e, 0);
	}
	get loading() {
		return this.element.hasAttribute("loading");
	}
	get clickToReplay() {
		return this.element.hasAttribute("click-to-replay");
	}
}, b = class {
	constructor(e, t, n) {
		u(this, "player", void 0), u(this, "element", void 0), u(this, "targetElement", void 0), u(this, "connected", !1), u(this, "targetState", void 0), u(this, "delayTimer", null), u(this, "intersectionObserver", void 0), this.player = e, this.element = t, this.targetElement = n, this.onClick = this.onClick.bind(this), this.replay();
	}
	onConnected() {
		this.connected = !0, this.targetElement.addEventListener("click", this.onClick), this.targetState && (this.loading ? this.play(!0) : this.initIntersectionObserver());
	}
	onDisconnected() {
		this.connected = !1, this.targetElement.removeEventListener("click", this.onClick), this.cleanup();
	}
	onComplete() {
		this.resetState();
	}
	onClick() {
		this.player.playing || this.player.playFromStart();
	}
	play(e) {
		this.player.playing || this.delayTimer || (e && this.delay > 0 ? this.scheduleDelayedPlay() : this.player.playFromStart());
	}
	replay() {
		this.player.playing || !this.player.state || !this.intro || (this.targetState = this.player.state, this.player.state = this.intro, this.connected && this.play());
	}
	scheduleDelayedPlay() {
		this.resetDelayTimer(), this.delayTimer = setTimeout(() => {
			this.player.playFromStart(), this.delayTimer = null;
		}, this.delay);
	}
	initIntersectionObserver() {
		if (this.intersectionObserver) return;
		let e = (e) => {
			e.forEach((e) => {
				e.isIntersecting && (this.play(!0), this.resetIntersectionObserver());
			});
		};
		this.intersectionObserver = new IntersectionObserver(e, { threshold: .5 }), this.intersectionObserver.observe(this.element);
	}
	resetIntersectionObserver() {
		this.intersectionObserver && (this.intersectionObserver.unobserve(this.element), this.intersectionObserver = void 0);
	}
	resetDelayTimer() {
		this.delayTimer && (clearTimeout(this.delayTimer), this.delayTimer = null);
	}
	resetState() {
		this.targetState && (this.player.state = this.targetState, this.targetState = void 0);
	}
	cleanup() {
		this.resetIntersectionObserver(), this.resetDelayTimer(), this.resetState();
	}
	get intro() {
		if (!this.element.hasAttribute("intro")) return null;
		let e = this.element.getAttribute("intro"), t = this.player.availableStates.find((t) => t.name === e);
		return t || (t = this.player.availableStates.find((e) => e.name.startsWith("in-"))), (t == null ? void 0 : t.name) || null;
	}
	get delay() {
		let e = this.element.hasAttribute("delay") ? +(this.element.getAttribute("delay") || 0) : 0;
		return Math.max(e, 0);
	}
	get loading() {
		return this.element.hasAttribute("loading");
	}
}, x = class {
	constructor(e, t, n) {
		u(this, "player", void 0), u(this, "element", void 0), u(this, "targetElement", void 0), u(this, "connected", !1), u(this, "targetState", void 0), u(this, "delayTimer", null), u(this, "intersectionObserver", void 0), this.player = e, this.element = t, this.targetElement = n, this.onHover = this.onHover.bind(this), this.onClick = this.onClick.bind(this), this.replay();
	}
	onConnected() {
		this.connected = !0, this.targetElement.addEventListener("click", this.onClick), this.targetElement.addEventListener("mouseenter", this.onHover), this.targetState && (this.loading ? this.play(!0) : this.initIntersectionObserver());
	}
	onDisconnected() {
		this.connected = !1, this.targetElement.removeEventListener("click", this.onClick), this.targetElement.removeEventListener("mouseenter", this.onHover), this.cleanup();
	}
	onComplete() {
		this.resetState();
	}
	onHover() {
		this.targetState || this.play();
	}
	onClick() {
		this.clickToReplay && this.replay();
	}
	play(e) {
		this.player.playing || this.delayTimer || (e && this.delay > 0 ? this.scheduleDelayedPlay() : this.player.playFromStart());
	}
	replay() {
		this.player.playing || !this.player.state || !this.intro || (this.targetState = this.player.state, this.player.state = this.intro, this.connected && this.play());
	}
	scheduleDelayedPlay() {
		this.resetDelayTimer(), this.delayTimer = setTimeout(() => {
			this.player.playFromStart(), this.delayTimer = null;
		}, this.delay);
	}
	initIntersectionObserver() {
		if (this.intersectionObserver) return;
		let e = (e) => {
			e.forEach((e) => {
				e.isIntersecting && (this.play(!0), this.resetIntersectionObserver());
			});
		};
		this.intersectionObserver = new IntersectionObserver(e, { threshold: .5 }), this.intersectionObserver.observe(this.element);
	}
	resetIntersectionObserver() {
		this.intersectionObserver && (this.intersectionObserver.unobserve(this.element), this.intersectionObserver = void 0);
	}
	resetDelayTimer() {
		this.delayTimer && (clearTimeout(this.delayTimer), this.delayTimer = null);
	}
	resetState() {
		this.targetState && (this.player.state = this.targetState, this.targetState = void 0);
	}
	cleanup() {
		this.resetIntersectionObserver(), this.resetDelayTimer(), this.resetState();
	}
	get intro() {
		if (!this.element.hasAttribute("intro")) return null;
		let e = this.element.getAttribute("intro"), t = this.player.availableStates.find((t) => t.name === e);
		return t || (t = this.player.availableStates.find((e) => e.name.startsWith("in-"))), (t == null ? void 0 : t.name) || null;
	}
	get delay() {
		let e = this.element.hasAttribute("delay") ? +(this.element.getAttribute("delay") || 0) : 0;
		return Math.max(e, 0);
	}
	get loading() {
		return this.element.hasAttribute("loading");
	}
	get clickToReplay() {
		return this.element.hasAttribute("click-to-replay");
	}
}, S = class {
	constructor(e, t, n) {
		u(this, "player", void 0), u(this, "element", void 0), u(this, "targetElement", void 0), u(this, "connected", !1), u(this, "delayTimer", null), u(this, "intersectionObserver", void 0), this.player = e, this.element = t, this.targetElement = n, this.onClick = this.onClick.bind(this);
	}
	onConnected() {
		this.connected = !0, this.targetElement.addEventListener("click", this.onClick), this.loading ? this.play(!0) : this.initIntersectionObserver();
	}
	onDisconnected() {
		this.connected = !1, this.targetElement.removeEventListener("click", this.onClick), this.cleanup();
	}
	onClick() {
		this.clickToReplay && this.play();
	}
	play(e) {
		this.player.playing || this.delayTimer || (e && this.delay > 0 ? this.scheduleDelayedPlay() : this.player.playFromStart());
	}
	scheduleDelayedPlay() {
		this.resetDelayTimer(), this.delayTimer = setTimeout(() => {
			this.player.playFromStart(), this.delayTimer = null;
		}, this.delay);
	}
	initIntersectionObserver() {
		if (this.intersectionObserver) return;
		let e = (e) => {
			e.forEach((e) => {
				e.isIntersecting && (this.play(!0), this.resetIntersectionObserver());
			});
		};
		this.intersectionObserver = new IntersectionObserver(e, { threshold: .5 }), this.intersectionObserver.observe(this.element);
	}
	resetIntersectionObserver() {
		this.intersectionObserver && (this.intersectionObserver.unobserve(this.element), this.intersectionObserver = void 0);
	}
	resetDelayTimer() {
		this.delayTimer && (clearTimeout(this.delayTimer), this.delayTimer = null);
	}
	cleanup() {
		this.resetIntersectionObserver(), this.resetDelayTimer();
	}
	get delay() {
		let e = this.element.hasAttribute("delay") ? +(this.element.getAttribute("delay") || 0) : 0;
		return Math.max(e, 0);
	}
	get loading() {
		return this.element.hasAttribute("loading");
	}
	get clickToReplay() {
		return this.element.hasAttribute("click-to-replay");
	}
}, C = class {
	constructor(e, t, n) {
		u(this, "player", void 0), u(this, "element", void 0), u(this, "targetElement", void 0), u(this, "delayTimer", null), this.player = e, this.element = t, this.targetElement = n;
	}
	onReady() {
		this.play();
	}
	onComplete() {
		this.play();
	}
	onDisconnected() {
		this.resetDelayTimer();
	}
	play() {
		this.player.playing || this.delayTimer || (this.delay > 0 ? this.scheduleDelayedPlay() : this.player.playFromStart());
	}
	scheduleDelayedPlay() {
		this.resetDelayTimer(), this.delayTimer = setTimeout(() => {
			this.player.playFromStart(), this.delayTimer = null;
		}, this.delay);
	}
	resetDelayTimer() {
		this.delayTimer && (clearTimeout(this.delayTimer), this.delayTimer = null);
	}
	get delay() {
		let e = this.element.hasAttribute("delay") ? +(this.element.getAttribute("delay") || 0) : 0;
		return Math.max(e, 0);
	}
}, w = class {
	constructor(e, t, n) {
		u(this, "player", void 0), u(this, "element", void 0), u(this, "targetElement", void 0), u(this, "delayTimer", null), u(this, "mouseIn", !1), this.player = e, this.element = t, this.targetElement = n, this.onMouseEnter = this.onMouseEnter.bind(this), this.onMouseLeave = this.onMouseLeave.bind(this);
	}
	onConnected() {
		this.targetElement.addEventListener("mouseenter", this.onMouseEnter), this.targetElement.addEventListener("mouseleave", this.onMouseLeave);
	}
	onDisconnected() {
		this.targetElement.removeEventListener("mouseenter", this.onMouseEnter), this.targetElement.removeEventListener("mouseleave", this.onMouseLeave), this.resetDelayTimer();
	}
	onMouseEnter() {
		this.mouseIn = !0, this.play();
	}
	onMouseLeave() {
		this.mouseIn = !1, this.resetDelayTimer();
	}
	onComplete() {
		this.play();
	}
	play() {
		this.player.playing || this.delayTimer || this.mouseIn && (this.delay > 0 ? this.scheduleDelayedPlay() : this.player.playFromStart());
	}
	scheduleDelayedPlay() {
		this.resetDelayTimer(), this.delayTimer = setTimeout(() => {
			this.player.playFromStart(), this.delayTimer = null;
		}, this.delay);
	}
	resetDelayTimer() {
		this.delayTimer && (clearTimeout(this.delayTimer), this.delayTimer = null);
	}
	get delay() {
		let e = this.element.hasAttribute("delay") ? +(this.element.getAttribute("delay") || 0) : 0;
		return Math.max(e, 0);
	}
}, T = {
	attributes: !0,
	childList: !1,
	subtree: !1,
	attributeOldValue: !0
}, E = class {
	constructor(e, t, n) {
		u(this, "player", void 0), u(this, "element", void 0), u(this, "targetElement", void 0), u(this, "segments", void 0), u(this, "queue", []), u(this, "mouseIn", !1), u(this, "connected", !1), u(this, "targetState", void 0), u(this, "delayTimer", null), u(this, "mutationTimer", null), u(this, "intersectionObserver", void 0), u(this, "observer", void 0), this.player = e, this.element = t, this.targetElement = n, this.onClick = this.onClick.bind(this), this.onMouseEnter = this.onMouseEnter.bind(this), this.onMouseLeave = this.onMouseLeave.bind(this), this.handleState(), this.replay();
	}
	onConnected() {
		this.connected = !0, this.targetElement.addEventListener("click", this.onClick), this.targetElement.addEventListener("mouseenter", this.onMouseEnter), this.targetElement.addEventListener("mouseleave", this.onMouseLeave), this.mode[0] === "class" && this.initMutationObserver(), this.targetState && (this.loading ? this.play(!0) : this.initIntersectionObserver());
	}
	onDisconnected() {
		this.connected = !1, this.targetElement.removeEventListener("click", this.onClick), this.targetElement.removeEventListener("mouseenter", this.onMouseEnter), this.targetElement.removeEventListener("mouseleave", this.onMouseLeave), this.cleanup();
	}
	onMouseEnter() {
		this.mode[0] === "hover" && (this.mouseIn = !0, this.triggerEnter());
	}
	onMouseLeave() {
		this.mode[0] === "hover" && (this.mouseIn = !1, this.triggerLeave());
	}
	onComplete() {
		this.targetState ? (this.resetState(), this.mouseIn && (this.queue.push(0), this.handleQueue())) : this.handleQueue();
	}
	onState() {
		this.handleState();
	}
	onClick() {
		this.clickToReplay && this.replay();
	}
	play(e) {
		this.player.playing || this.delayTimer || (e && this.delay > 0 ? this.scheduleDelayedPlay() : this.player.playFromStart());
	}
	replay() {
		this.player.playing || !this.player.state || !this.intro || (this.targetState = this.player.state, this.player.state = this.intro, this.connected && this.play());
	}
	triggerEnter() {
		this.queue.push(0), this.handleQueue();
	}
	triggerLeave() {
		this.queue.push(1), this.handleQueue();
	}
	scheduleDelayedPlay() {
		this.resetDelayTimer(), this.delayTimer = setTimeout(() => {
			this.player.playFromStart(), this.delayTimer = null;
		}, this.delay);
	}
	handleQueue() {
		if (this.player.playing) return;
		if (this.queue.length >= 2) {
			let e = Math.floor(this.queue.length / 2) * 2;
			for (let t = 0; t < e; t++) this.queue.shift();
		}
		if (!this.queue.length) return;
		let e = this.queue.shift();
		if (this.segments) {
			var t;
			let n = (t = this.segments) == null ? void 0 : t[e];
			this.player.direction = 1, this.player.switchSegment(n);
		} else this.player.direction = e === 0 ? 1 : -1;
		this.player.play();
	}
	handleState() {
		this.segments = void 0;
		let e = this.player.availableStates.find((e) => e.name === this.player.state);
		if (!e) return;
		let t = 0;
		if (e.params.length) {
			let n = parseFloat(e.params[0]);
			!isNaN(n) && n > 0 && n <= 1 && (t = n);
		}
		if (!t) return;
		let n = [e.time, e.time + Math.floor((e.duration + 1) * t)], r = [n[1], e.time + e.duration + 1];
		this.segments = [n, r];
		let i = this.mode;
		i[0] === "class" && this.targetElement.classList.contains(i[1]) && (this.player.switchSegment(n), this.player.frame = n[0]);
	}
	initIntersectionObserver() {
		if (this.intersectionObserver) return;
		let e = (e) => {
			e.forEach((e) => {
				e.isIntersecting && (this.play(!0), this.resetIntersectionObserver());
			});
		};
		this.intersectionObserver = new IntersectionObserver(e, { threshold: .5 }), this.intersectionObserver.observe(this.element);
	}
	resetIntersectionObserver() {
		this.intersectionObserver && (this.intersectionObserver.unobserve(this.element), this.intersectionObserver = void 0);
	}
	initMutationObserver() {
		this.observer || (this.observer = new MutationObserver((e) => {
			let t = this.mode;
			if (t[0] !== "class") return;
			let n = t[1] || "";
			for (let t of e) if (t.type === "attributes" && ["class"].includes(t.attributeName)) {
				let e = (t.oldValue || "").split(" ").includes(n), r = (this.targetElement.getAttribute("class") || "").split(" ").includes(n);
				e !== r && (clearTimeout(this.mutationTimer), this.mutationTimer = setTimeout(() => {
					r ? this.triggerEnter() : this.triggerLeave();
				}, 10));
			}
		})), this.observer.observe(this.targetElement, T);
	}
	resetMutationObserver() {
		clearTimeout(this.mutationTimer), this.mutationTimer = null, this.observer && (this.observer.disconnect(), this.observer = void 0);
	}
	resetDelayTimer() {
		this.delayTimer && (clearTimeout(this.delayTimer), this.delayTimer = null);
	}
	resetState() {
		return this.targetState ? (this.player.state = this.targetState, this.targetState = void 0, !0) : !1;
	}
	resetPlayer() {
		this.player.direction = 1, this.segments && (this.player.switchSegment([this.segments[0][0], this.segments[1][1]]), this.segments = void 0, this.queue = []);
	}
	cleanup() {
		this.resetPlayer(), this.resetIntersectionObserver(), this.resetMutationObserver(), this.resetDelayTimer(), this.resetState();
	}
	get intro() {
		if (!this.element.hasAttribute("intro")) return null;
		let e = this.element.getAttribute("intro"), t = this.player.availableStates.find((t) => t.name === e);
		return t || (t = this.player.availableStates.find((e) => e.name.startsWith("in-"))), (t == null ? void 0 : t.name) || null;
	}
	get delay() {
		let e = this.element.hasAttribute("delay") ? +(this.element.getAttribute("delay") || 0) : 0;
		return Math.max(e, 0);
	}
	get loading() {
		return this.element.hasAttribute("loading");
	}
	get clickToReplay() {
		return this.element.hasAttribute("click-to-replay");
	}
	get mode() {
		if (this.element.hasAttribute("mode")) {
			let e = this.element.getAttribute("mode"), t = (e == null ? void 0 : e.split(":")) || [];
			if (t.length > 0 && [
				"hover",
				"class",
				"manual"
			].includes(t[0])) return t[0] === "class" ? [t[0], t[1] || "active"] : [t[0]];
		}
		return ["hover"];
	}
}, D = /^\d*(\.\d+)?$/, O = {
	attributes: !0,
	childList: !1,
	subtree: !1
}, k = class {
	constructor(e, t, n) {
		u(this, "player", void 0), u(this, "element", void 0), u(this, "targetElement", void 0), u(this, "sequenceIndex", 0), u(this, "frameState", null), u(this, "frameDelayFirst", null), u(this, "frameDelayLast", null), u(this, "timer", void 0), u(this, "observer", void 0), this.player = e, this.element = t, this.targetElement = n, this.observer = new MutationObserver((e) => {
			for (let t of e) t.type === "attributes" && ["sequence", "speed"].includes(t.attributeName) && (this.reset(), this.step());
		});
	}
	onReady() {
		this.step();
	}
	onComplete() {
		this.timer = setTimeout(() => {
			this.timer = null, this.frameDelayLast = null, this.step();
		}, this.frameDelayLast || 0);
	}
	onConnected() {
		this.observer.observe(this.element, O), this.player.speed = this.speed;
	}
	onDisconnected() {
		this.observer.disconnect(), this.timer && (clearTimeout(this.timer), this.timer = null), this.player.speed = 1;
	}
	reset() {
		this.player.pause(), this.player.speed = this.speed, this.sequenceIndex = 0, this.frameState = this.frameDelayFirst = this.frameDelayLast = null, this.timer && (clearTimeout(this.timer), this.timer = null);
	}
	takeStep() {
		let e = this.sequence.split(","), t = e[this.sequenceIndex];
		this.sequenceIndex++, this.sequenceIndex >= e.length && (this.sequenceIndex = 0);
		let [n, ...r] = t.split(":");
		return {
			action: n,
			params: r
		};
	}
	handleStep(e, t) {
		if (e === "play") this.frameState !== null && (this.player.state = this.frameState, this.frameState = null), t.includes("reverse") ? (this.player.seekToEnd(), this.player.direction = -1) : (this.player.seekToStart(), this.player.direction = 1), this.timer = setTimeout(() => {
			this.timer = null, this.frameDelayFirst = null, this.player.play();
		}, this.frameDelayFirst || 0);
		else if (e === "frame") {
			this.frameState !== null && (this.player.state = this.frameState, this.frameState = null);
			let e = 0, n = 0;
			t.length >= 1 && t[0].match(D) && (e = +t[0]), n = t.length >= 2 && t[1].match(D) ? Math.max(0, e, +t[1]) : e;
			let r = [e, n], i = this.player.availableStates.find((e) => e.name === this.player.state);
			i && (r[0] += i.time, r[1] += i.time), e === n ? (this.player.frame = e, this.timer = setTimeout(() => {
				this.timer = null, this.frameDelayFirst = null, this.step();
			}, this.frameDelayFirst || 0)) : this.timer = setTimeout(() => {
				this.timer = null, this.frameDelayFirst = null, this.player.switchSegment(r), this.player.play();
			}, this.frameDelayFirst || 0);
		} else if (e === "state") this.frameState = t[0] || null, this.step();
		else if (e === "delay") {
			let e = null;
			for (let n of t) n && n.match(D) && (e = +n);
			e && e > 0 && (t.includes("first") && t.includes("last") ? (this.frameDelayFirst = e, this.frameDelayLast = e) : t.includes("first") ? this.frameDelayFirst = e : t.includes("last") ? this.frameDelayLast = e : this.frameDelayFirst = e), this.step();
		} else if (e !== "idle") throw Error(`Invalid sequence action: ${e}`);
	}
	step() {
		let { action: e, params: t } = this.takeStep();
		e && this.handleStep(e, t);
	}
	get sequence() {
		return this.element.getAttribute("sequence") || "";
	}
	get speed() {
		return this.element.hasAttribute("speed") ? +(this.element.getAttribute("speed") || 1) : 1;
	}
};
//#endregion
//#region src/index.ts
function A() {
	v.defineTrigger("in", S), v.defineTrigger("click", b), v.defineTrigger("hover", x), v.defineTrigger("loop", C), v.defineTrigger("loop-on-hover", w), v.defineTrigger("morph", E), v.defineTrigger("boomerang", y), v.defineTrigger("sequence", k), (!customElements.get || !customElements.get("lord-icon")) && customElements.define("lord-icon", v);
}
//#endregion
export { y as Boomerang, b as Click, v as Element, x as Hover, S as In, C as Loop, w as LoopOnHover, E as Morph, e as Player, k as Sequence, A as defineElement };
