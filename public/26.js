(window['webpackJsonp'] = window['webpackJsonp'] || []).push([[26], {

    /***/
    './node_modules/vue-slider-component/dist/vue-slider-component.umd.min.js':
    /*!********************************************************************************!*\
  !*** ./node_modules/vue-slider-component/dist/vue-slider-component.umd.min.js ***!
  \********************************************************************************/
    /*! no static exports found */
    /***/ (function (module, exports, __webpack_require__) {

        (function (t, e) {
            true ? module.exports = e(__webpack_require__(/*! vue */ './node_modules/vue/dist/vue.common.js')) : undefined
        })('undefined' !== typeof self ? self : this, (function (t) {
            return function (t) {
                var e = {}

                function r(n) {
                    if (e[n]) return e[n].exports
                    var o = e[n] = {
                        i: n,
                        l: !1,
                        exports: {}
                    }
                    return t[n].call(o.exports, o, o.exports, r), o.l = !0, o.exports
                }

                return r.m = t, r.c = e, r.d = function (t, e, n) {
                    r.o(t, e) || Object.defineProperty(t, e, {
                        enumerable: !0,
                        get: n
                    })
                }, r.r = function (t) {
                    'undefined' !== typeof Symbol && Symbol.toStringTag && Object.defineProperty(t, Symbol.toStringTag, { value: 'Module' }), Object.defineProperty(t, '__esModule', { value: !0 })
                }, r.t = function (t, e) {
                    if (1 & e && (t = r(t)), 8 & e) return t
                    if (4 & e && 'object' === typeof t && t && t.__esModule) return t
                    var n = Object.create(null)
                    if (r.r(n), Object.defineProperty(n, 'default', {
                        enumerable: !0,
                        value: t
                    }), 2 & e && 'string' != typeof t) {
                        for (var o in t) {
                            r.d(n, o, function (e) {
                                return t[e]
                            }.bind(null, o))
                        }
                    }
                    return n
                }, r.n = function (t) {
                    var e = t && t.__esModule ? function () {
                        return t['default']
                    } : function () {
                        return t
                    }
                    return r.d(e, 'a', e), e
                }, r.o = function (t, e) {
                    return Object.prototype.hasOwnProperty.call(t, e)
                }, r.p = '', r(r.s = 'fb15')
            }({
                '091b': function (t, e, r) {
                    var n = r('24fb')
                    e = n(!1), e.push([t.i, '.vue-slider-dot{position:absolute;-webkit-transition:all 0s;transition:all 0s;z-index:5}.vue-slider-dot:focus{outline:none}.vue-slider-dot-tooltip{position:absolute;visibility:hidden}.vue-slider-dot-hover:hover .vue-slider-dot-tooltip{visibility:visible}.vue-slider-dot-tooltip-show{visibility:visible}.vue-slider-dot-tooltip-top{top:-10px;left:50%;-webkit-transform:translate(-50%,-100%);transform:translate(-50%,-100%)}.vue-slider-dot-tooltip-bottom{bottom:-10px;left:50%;-webkit-transform:translate(-50%,100%);transform:translate(-50%,100%)}.vue-slider-dot-tooltip-left{left:-10px;top:50%;-webkit-transform:translate(-100%,-50%);transform:translate(-100%,-50%)}.vue-slider-dot-tooltip-right{right:-10px;top:50%;-webkit-transform:translate(100%,-50%);transform:translate(100%,-50%)}', '']), t.exports = e
                },
                '24fb': function (t, e, r) {
                    'use strict'

                    function n(t, e) {
                        var r = t[1] || '',
                            n = t[3]
                        if (!n) return r
                        if (e && 'function' === typeof btoa) {
                            var i = o(n),
                                a = n.sources.map((function (t) {
                                    return '/*# sourceURL='.concat(n.sourceRoot || '')
                                        .concat(t, ' */')
                                }))
                            return [r].concat(a)
                                .concat([i])
                                .join('\n')
                        }
                        return [r].join('\n')
                    }

                    function o(t) {
                        var e = btoa(unescape(encodeURIComponent(JSON.stringify(t)))),
                            r = 'sourceMappingURL=data:application/json;charset=utf-8;base64,'.concat(e)
                        return '/*# '.concat(r, ' */')
                    }

                    t.exports = function (t) {
                        var e = []
                        return e.toString = function () {
                            return this.map((function (e) {
                                var r = n(e, t)
                                return e[2] ? '@media '.concat(e[2], ' {')
                                    .concat(r, '}') : r
                            }))
                                .join('')
                        }, e.i = function (t, r, n) {
                            'string' === typeof t && (t = [[null, t, '']])
                            var o = {}
                            if (n) {
                                for (var i = 0; i < this.length; i++) {
                                    var a = this[i][0]
                                    null != a && (o[a] = !0)
                                }
                            }
                            for (var s = 0; s < t.length; s++) {
                                var u = [].concat(t[s])
                                n && o[u[0]] || (r && (u[2] ? u[2] = ''.concat(r, ' and ')
                                    .concat(u[2]) : u[2] = r), e.push(u))
                            }
                        }, e
                    }
                },
                2638: function (t, e, r) {
                    'use strict'

                    function n() {
                        return n = Object.assign || function (t) {
                            for (var e, r = 1; r < arguments.length; r++) for (var n in e = arguments[r], e) Object.prototype.hasOwnProperty.call(e, n) && (t[n] = e[n])
                            return t
                        }, n.apply(this, arguments)
                    }

                    var o = ['attrs', 'props', 'domProps'],
                        i = ['class', 'style', 'directives'],
                        a = ['on', 'nativeOn'],
                        s = function (t) {
                            return t.reduce((function (t, e) {
                                for (var r in e) {
                                    if (t[r]) {
                                        if (-1 !== o.indexOf(r)) {
                                            t[r] = n({}, t[r], e[r])
                                        } else if (-1 !== i.indexOf(r)) {
                                            var s = t[r] instanceof Array ? t[r] : [t[r]],
                                                l = e[r] instanceof Array ? e[r] : [e[r]]
                                            t[r] = s.concat(l)
                                        } else if (-1 !== a.indexOf(r)) {
                                            for (var c in e[r]) {
                                                if (t[r][c]) {
                                                    var d = t[r][c] instanceof Array ? t[r][c] : [t[r][c]],
                                                        f = e[r][c] instanceof Array ? e[r][c] : [e[r][c]]
                                                    t[r][c] = d.concat(f)
                                                } else {
                                                    t[r][c] = e[r][c]
                                                }
                                            }
                                        } else if ('hook' == r) for (var h in e[r]) t[r][h] = t[r][h] ? u(t[r][h], e[r][h]) : e[r][h] else t[r] = e[r]
                                    } else {
                                        t[r] = e[r]
                                    }
                                }
                                return t
                            }), {})
                        },
                        u = function (t, e) {
                            return function () {
                                t && t.apply(this, arguments), e && e.apply(this, arguments)
                            }
                        }
                    t.exports = s
                },
                '499e': function (t, e, r) {
                    'use strict'

                    function n(t, e) {
                        for (var r = [], n = {}, o = 0; o < e.length; o++) {
                            var i = e[o],
                                a = i[0],
                                s = i[1],
                                u = i[2],
                                l = i[3],
                                c = {
                                    id: t + ':' + o,
                                    css: s,
                                    media: u,
                                    sourceMap: l
                                }
                            n[a] ? n[a].parts.push(c) : r.push(n[a] = {
                                id: a,
                                parts: [c]
                            })
                        }
                        return r
                    }

                    r.r(e), r.d(e, 'default', (function () {
                        return p
                    }))
                    var o = 'undefined' !== typeof document
                    if ('undefined' !== typeof DEBUG && DEBUG && !o) throw new Error('vue-style-loader cannot be used in a non-browser environment. Use { target: \'node\' } in your Webpack config to indicate a server-rendering environment.')
                    var i = {},
                        a = o && (document.head || document.getElementsByTagName('head')[0]),
                        s = null,
                        u = 0,
                        l = !1,
                        c = function () {
                        },
                        d = null,
                        f = 'data-vue-ssr-id',
                        h = 'undefined' !== typeof navigator && /msie [6-9]\b/.test(navigator.userAgent.toLowerCase())

                    function p(t, e, r, o) {
                        l = r, d = o || {}
                        var a = n(t, e)
                        return y(a), function (e) {
                            for (var r = [], o = 0; o < a.length; o++) {
                                var s = a[o],
                                    u = i[s.id]
                                u.refs--, r.push(u)
                            }
                            e ? (a = n(t, e), y(a)) : a = []
                            for (o = 0; o < r.length; o++) {
                                u = r[o]
                                if (0 === u.refs) {
                                    for (var l = 0; l < u.parts.length; l++) u.parts[l]()
                                    delete i[u.id]
                                }
                            }
                        }
                    }

                    function y(t) {
                        for (var e = 0; e < t.length; e++) {
                            var r = t[e],
                                n = i[r.id]
                            if (n) {
                                n.refs++
                                for (var o = 0; o < n.parts.length; o++) n.parts[o](r.parts[o])
                                for (; o < r.parts.length; o++) n.parts.push(m(r.parts[o]))
                                n.parts.length > r.parts.length && (n.parts.length = r.parts.length)
                            } else {
                                var a = []
                                for (o = 0; o < r.parts.length; o++) a.push(m(r.parts[o]))
                                i[r.id] = {
                                    id: r.id,
                                    refs: 1,
                                    parts: a
                                }
                            }
                        }
                    }

                    function v() {
                        var t = document.createElement('style')
                        return t.type = 'text/css', a.appendChild(t), t
                    }

                    function m(t) {
                        var e,
                            r,
                            n = document.querySelector('style[' + f + '~="' + t.id + '"]')
                        if (n) {
                            if (l) return c
                            n.parentNode.removeChild(n)
                        }
                        if (h) {
                            var o = u++
                            n = s || (s = v()), e = g.bind(null, n, o, !1), r = g.bind(null, n, o, !0)
                        } else {
                            n = v(), e = k.bind(null, n), r = function () {
                                n.parentNode.removeChild(n)
                            }
                        }
                        return e(t), function (n) {
                            if (n) {
                                if (n.css === t.css && n.media === t.media && n.sourceMap === t.sourceMap) return
                                e(t = n)
                            } else {
                                r()
                            }
                        }
                    }

                    var b = function () {
                        var t = []
                        return function (e, r) {
                            return t[e] = r, t.filter(Boolean)
                                .join('\n')
                        }
                    }()

                    function g(t, e, r, n) {
                        var o = r ? '' : n.css
                        if (t.styleSheet) {
                            t.styleSheet.cssText = b(e, o)
                        } else {
                            var i = document.createTextNode(o),
                                a = t.childNodes
                            a[e] && t.removeChild(a[e]), a.length ? t.insertBefore(i, a[e]) : t.appendChild(i)
                        }
                    }

                    function k(t, e) {
                        var r = e.css,
                            n = e.media,
                            o = e.sourceMap
                        if (n && t.setAttribute('media', n), d.ssrId && t.setAttribute(f, e.id), o && (r += '\n/*# sourceURL=' + o.sources[0] + ' */', r += '\n/*# sourceMappingURL=data:application/json;base64,' + btoa(unescape(encodeURIComponent(JSON.stringify(o)))) + ' */'), t.styleSheet) {
                            t.styleSheet.cssText = r
                        } else {
                            while (t.firstChild) t.removeChild(t.firstChild)
                            t.appendChild(document.createTextNode(r))
                        }
                    }
                },
                '4abb': function (t, e, r) {
                    var n = r('7a57')
                    'string' === typeof n && (n = [[t.i, n, '']]), n.locals && (t.exports = n.locals)
                    var o = r('499e').default
                    o('b2af7572', n, !0, {
                        sourceMap: !1,
                        shadowMode: !1
                    })
                },
                '4ed8': function (t, e, r) {
                    var n = r('091b')
                    'string' === typeof n && (n = [[t.i, n, '']]), n.locals && (t.exports = n.locals)
                    var o = r('499e').default
                    o('2f6bee1a', n, !0, {
                        sourceMap: !1,
                        shadowMode: !1
                    })
                },
                '556c': function (t, e, r) {
                    var n = r('eef2')
                    'string' === typeof n && (n = [[t.i, n, '']]), n.locals && (t.exports = n.locals)
                    var o = r('499e').default
                    o('1209fd47', n, !0, {
                        sourceMap: !1,
                        shadowMode: !1
                    })
                },
                '65d9': function (t, e, r) {
                    'use strict'

                    /**
                     * vue-class-component v7.0.1
                     * (c) 2015-present Evan You
                     * @license MIT
                     */function n(t) {
                        return t && 'object' === typeof t && 'default' in t ? t['default'] : t
                    }

                    Object.defineProperty(e, '__esModule', { value: !0 })
                    var o = n(r('8bbf')),
                        i = 'undefined' !== typeof Reflect && Reflect.defineMetadata && Reflect.getOwnMetadataKeys

                    function a(t, e) {
                        s(t, e), Object.getOwnPropertyNames(e.prototype)
                            .forEach((function (r) {
                                s(t.prototype, e.prototype, r)
                            })), Object.getOwnPropertyNames(e)
                            .forEach((function (r) {
                                s(t, e, r)
                            }))
                    }

                    function s(t, e, r) {
                        var n = r ? Reflect.getOwnMetadataKeys(e, r) : Reflect.getOwnMetadataKeys(e)
                        n.forEach((function (n) {
                            var o = r ? Reflect.getOwnMetadata(n, e, r) : Reflect.getOwnMetadata(n, e)
                            r ? Reflect.defineMetadata(n, o, t, r) : Reflect.defineMetadata(n, o, t)
                        }))
                    }

                    var u = { __proto__: [] },
                        l = u instanceof Array

                    function c(t) {
                        return function (e, r, n) {
                            var o = 'function' === typeof e ? e : e.constructor
                            o.__decorators__ || (o.__decorators__ = []), 'number' !== typeof n && (n = void 0), o.__decorators__.push((function (e) {
                                return t(e, r, n)
                            }))
                        }
                    }

                    function d() {
                        for (var t = [], e = 0; e < arguments.length; e++) t[e] = arguments[e]
                        return o.extend({ mixins: t })
                    }

                    function f(t) {
                        var e = typeof t
                        return null == t || 'object' !== e && 'function' !== e
                    }

                    function h(t, e) {
                        var r = e.prototype._init
                        e.prototype._init = function () {
                            var e = this,
                                r = Object.getOwnPropertyNames(t)
                            if (t.$options.props) for (var n in t.$options.props) t.hasOwnProperty(n) || r.push(n)
                            r.forEach((function (r) {
                                '_' !== r.charAt(0) && Object.defineProperty(e, r, {
                                    get: function () {
                                        return t[r]
                                    },
                                    set: function (e) {
                                        t[r] = e
                                    },
                                    configurable: !0
                                })
                            }))
                        }
                        var n = new e
                        e.prototype._init = r
                        var o = {}
                        return Object.keys(n)
                            .forEach((function (t) {
                                void 0 !== n[t] && (o[t] = n[t])
                            })), o
                    }

                    var p = ['data', 'beforeCreate', 'created', 'beforeMount', 'mounted', 'beforeDestroy', 'destroyed', 'beforeUpdate', 'updated', 'activated', 'deactivated', 'render', 'errorCaptured', 'serverPrefetch']

                    function y(t, e) {
                        void 0 === e && (e = {}), e.name = e.name || t._componentTag || t.name
                        var r = t.prototype
                        Object.getOwnPropertyNames(r)
                            .forEach((function (t) {
                                if ('constructor' !== t) {
                                    if (p.indexOf(t) > -1) {
                                        e[t] = r[t]
                                    } else {
                                        var n = Object.getOwnPropertyDescriptor(r, t)
                                        void 0 !== n.value ? 'function' === typeof n.value ? (e.methods || (e.methods = {}))[t] = n.value : (e.mixins || (e.mixins = [])).push({
                                            data: function () {
                                                var e
                                                return e = {}, e[t] = n.value, e
                                            }
                                        }) : (n.get || n.set) && ((e.computed || (e.computed = {}))[t] = {
                                            get: n.get,
                                            set: n.set
                                        })
                                    }
                                }
                            })), (e.mixins || (e.mixins = [])).push({
                            data: function () {
                                return h(this, t)
                            }
                        })
                        var n = t.__decorators__
                        n && (n.forEach((function (t) {
                            return t(e)
                        })), delete t.__decorators__)
                        var s = Object.getPrototypeOf(t.prototype),
                            u = s instanceof o ? s.constructor : o,
                            l = u.extend(e)
                        return v(l, t, u), i && a(l, t), l
                    }

                    function v(t, e, r) {
                        Object.getOwnPropertyNames(e)
                            .forEach((function (n) {
                                if ('prototype' !== n) {
                                    var o = Object.getOwnPropertyDescriptor(t, n)
                                    if (!o || o.configurable) {
                                        var i = Object.getOwnPropertyDescriptor(e, n)
                                        if (!l) {
                                            if ('cid' === n) return
                                            var a = Object.getOwnPropertyDescriptor(r, n)
                                            if (!f(i.value) && a && a.value === i.value) return
                                        }
                                        0, Object.defineProperty(t, n, i)
                                    }
                                }
                            }))
                    }

                    function m(t) {
                        return 'function' === typeof t ? y(t) : function (e) {
                            return y(e, t)
                        }
                    }

                    m.registerHooks = function (t) {
                        p.push.apply(p, t)
                    }, e.default = m, e.createDecorator = c, e.mixins = d
                },
                '7a57': function (t, e, r) {
                    var n = r('24fb')
                    e = n(!1), e.push([t.i, '.vue-slider{position:relative;-webkit-box-sizing:content-box;box-sizing:content-box;-webkit-user-select:none;-moz-user-select:none;-ms-user-select:none;user-select:none;display:block;-webkit-tap-highlight-color:rgba(0,0,0,0)}.vue-slider-rail{position:relative;width:100%;height:100%;-webkit-transition-property:width,height,left,right,top,bottom;transition-property:width,height,left,right,top,bottom}.vue-slider-process{position:absolute;z-index:1}', '']), t.exports = e
                },
                8875: function (t, e, r) {
                    var n,
                        o,
                        i;
                    (function (r, a) {
                        o = [], n = a, i = 'function' === typeof n ? n.apply(e, o) : n, void 0 === i || (t.exports = i)
                    })('undefined' !== typeof self && self, (function () {
                        function t() {
                            var e = Object.getOwnPropertyDescriptor(document, 'currentScript')
                            if (!e && 'currentScript' in document && document.currentScript) return document.currentScript
                            if (e && e.get !== t && document.currentScript) return document.currentScript
                            try {
                                throw new Error
                            } catch (h) {
                                var r,
                                    n,
                                    o,
                                    i = /.*at [^(]*\((.*):(.+):(.+)\)$/gi,
                                    a = /@([^@]*):(\d+):(\d+)\s*$/gi,
                                    s = i.exec(h.stack) || a.exec(h.stack),
                                    u = s && s[1] || !1,
                                    l = s && s[2] || !1,
                                    c = document.location.href.replace(document.location.hash, ''),
                                    d = document.getElementsByTagName('script')
                                u === c && (r = document.documentElement.outerHTML, n = new RegExp('(?:[^\\n]+?\\n){0,' + (l - 2) + '}[^<]*<script>([\\d\\D]*?)<\\/script>[\\d\\D]*', 'i'), o = r.replace(n, '$1')
                                    .trim())
                                for (var f = 0; f < d.length; f++) {
                                    if ('interactive' === d[f].readyState) return d[f]
                                    if (d[f].src === u) return d[f]
                                    if (u === c && d[f].innerHTML && d[f].innerHTML.trim() === o) return d[f]
                                }
                                return null
                            }
                        }

                        return t
                    }))
                },
                '8bbf': function (e, r) {
                    e.exports = t
                },
                eef2: function (t, e, r) {
                    var n = r('24fb')
                    e = n(!1), e.push([t.i, '.vue-slider-marks{position:relative;width:100%;height:100%}.vue-slider-mark{position:absolute;z-index:1}.vue-slider-ltr .vue-slider-mark,.vue-slider-rtl .vue-slider-mark{width:0;height:100%;top:50%}.vue-slider-ltr .vue-slider-mark-step,.vue-slider-rtl .vue-slider-mark-step{top:0}.vue-slider-ltr .vue-slider-mark-label,.vue-slider-rtl .vue-slider-mark-label{top:100%;margin-top:10px}.vue-slider-ltr .vue-slider-mark{-webkit-transform:translate(-50%,-50%);transform:translate(-50%,-50%)}.vue-slider-ltr .vue-slider-mark-step{left:0}.vue-slider-ltr .vue-slider-mark-label{left:50%;-webkit-transform:translateX(-50%);transform:translateX(-50%)}.vue-slider-rtl .vue-slider-mark{-webkit-transform:translate(50%,-50%);transform:translate(50%,-50%)}.vue-slider-rtl .vue-slider-mark-step{right:0}.vue-slider-rtl .vue-slider-mark-label{right:50%;-webkit-transform:translateX(50%);transform:translateX(50%)}.vue-slider-btt .vue-slider-mark,.vue-slider-ttb .vue-slider-mark{width:100%;height:0;left:50%}.vue-slider-btt .vue-slider-mark-step,.vue-slider-ttb .vue-slider-mark-step{left:0}.vue-slider-btt .vue-slider-mark-label,.vue-slider-ttb .vue-slider-mark-label{left:100%;margin-left:10px}.vue-slider-btt .vue-slider-mark{-webkit-transform:translate(-50%,50%);transform:translate(-50%,50%)}.vue-slider-btt .vue-slider-mark-step{top:0}.vue-slider-btt .vue-slider-mark-label{top:50%;-webkit-transform:translateY(-50%);transform:translateY(-50%)}.vue-slider-ttb .vue-slider-mark{-webkit-transform:translate(-50%,-50%);transform:translate(-50%,-50%)}.vue-slider-ttb .vue-slider-mark-step{bottom:0}.vue-slider-ttb .vue-slider-mark-label{bottom:50%;-webkit-transform:translateY(50%);transform:translateY(50%)}.vue-slider-mark-label,.vue-slider-mark-step{position:absolute}', '']), t.exports = e
                },
                fb15: function (t, e, r) {
                    'use strict'
                    if (r.r(e), r.d(e, 'ERROR_TYPE', (function () {
                        return Z
                    })), r.d(e, 'VueSliderMark', (function () {
                        return $
                    })), r.d(e, 'VueSliderDot', (function () {
                        return A
                    })), 'undefined' !== typeof window) {
                        var n = window.document.currentScript,
                            o = r('8875')
                        n = o(), 'currentScript' in document || Object.defineProperty(document, 'currentScript', { get: o })
                        var i = n && n.src.match(/(.+\/)[^/]+\.js(\?.*)?$/)
                        i && (r.p = i[1])
                    }
                    var a = r('2638'),
                        s = r.n(a)

                    function u(t, e, r, n) {
                        var o,
                            i = arguments.length,
                            a = i < 3 ? e : null === n ? n = Object.getOwnPropertyDescriptor(e, r) : n
                        if ('object' === typeof Reflect && 'function' === typeof Reflect.decorate) a = Reflect.decorate(t, e, r, n) else for (var s = t.length - 1; s >= 0; s--) (o = t[s]) && (a = (i < 3 ? o(a) : i > 3 ? o(e, r, a) : o(e, r)) || a)
                        return i > 3 && a && Object.defineProperty(e, r, a), a
                    }

                    var l = r('8bbf'),
                        c = r.n(l),
                        d = r('65d9'),
                        f = r.n(d)
                    var h = 'undefined' !== typeof Reflect && 'undefined' !== typeof Reflect.getMetadata

                    function p(t, e, r) {
                        h && (Array.isArray(t) || 'function' === typeof t || 'undefined' !== typeof t.type || (t.type = Reflect.getMetadata('design:type', e, r)))
                    }

                    function y(t, e) {
                        return void 0 === e && (e = {}), function (r, n) {
                            p(e, r, n), Object(d['createDecorator'])((function (r, n) {
                                (r.props || (r.props = {}))[n] = e, r.model = {
                                    prop: n,
                                    event: t || n
                                }
                            }))(r, n)
                        }
                    }

                    function v(t) {
                        return void 0 === t && (t = {}), function (e, r) {
                            p(t, e, r), Object(d['createDecorator'])((function (e, r) {
                                (e.props || (e.props = {}))[r] = t
                            }))(e, r)
                        }
                    }

                    function m(t, e) {
                        void 0 === e && (e = {})
                        var r = e.deep,
                            n = void 0 !== r && r,
                            o = e.immediate,
                            i = void 0 !== o && o
                        return Object(d['createDecorator'])((function (e, r) {
                            'object' !== typeof e.watch && (e.watch = Object.create(null))
                            var o = e.watch
                            'object' !== typeof o[t] || Array.isArray(o[t]) ? 'undefined' === typeof o[t] && (o[t] = []) : o[t] = [o[t]], o[t].push({
                                handler: r,
                                deep: n,
                                immediate: i
                            })
                        }))
                    }

                    r('4ed8')

                    function b(t) {
                        return b = 'function' === typeof Symbol && 'symbol' === typeof Symbol.iterator ? function (t) {
                            return typeof t
                        } : function (t) {
                            return t && 'function' === typeof Symbol && t.constructor === Symbol && t !== Symbol.prototype ? 'symbol' : typeof t
                        }, b(t)
                    }

                    function g(t, e) {
                        if (!(t instanceof e)) throw new TypeError('Cannot call a class as a function')
                    }

                    function k(t, e) {
                        for (var r = 0; r < e.length; r++) {
                            var n = e[r]
                            n.enumerable = n.enumerable || !1, n.configurable = !0, 'value' in n && (n.writable = !0), Object.defineProperty(t, n.key, n)
                        }
                    }

                    function O(t, e, r) {
                        return e && k(t.prototype, e), r && k(t, r), t
                    }

                    function x(t, e) {
                        if ('function' !== typeof e && null !== e) throw new TypeError('Super expression must either be null or a function')
                        t.prototype = Object.create(e && e.prototype, {
                            constructor: {
                                value: t,
                                writable: !0,
                                configurable: !0
                            }
                        }), e && w(t, e)
                    }

                    function w(t, e) {
                        return w = Object.setPrototypeOf || function (t, e) {
                            return t.__proto__ = e, t
                        }, w(t, e)
                    }

                    function S(t) {
                        var e = R()
                        return function () {
                            var r,
                                n = j(t)
                            if (e) {
                                var o = j(this).constructor
                                r = Reflect.construct(n, arguments, o)
                            } else {
                                r = n.apply(this, arguments)
                            }
                            return P(this, r)
                        }
                    }

                    function P(t, e) {
                        return !e || 'object' !== b(e) && 'function' !== typeof e ? D(t) : e
                    }

                    function D(t) {
                        if (void 0 === t) throw new ReferenceError('this hasn\'t been initialised - super() hasn\'t been called')
                        return t
                    }

                    function R() {
                        if ('undefined' === typeof Reflect || !Reflect.construct) return !1
                        if (Reflect.construct.sham) return !1
                        if ('function' === typeof Proxy) return !0
                        try {
                            return Date.prototype.toString.call(Reflect.construct(Date, [], (function () {
                            }))), !0
                        } catch (t) {
                            return !1
                        }
                    }

                    function j(t) {
                        return j = Object.setPrototypeOf ? Object.getPrototypeOf : function (t) {
                            return t.__proto__ || Object.getPrototypeOf(t)
                        }, j(t)
                    }

                    var E = function (t) {
                        x(r, t)
                        var e = S(r)

                        function r() {
                            return g(this, r), e.apply(this, arguments)
                        }

                        return O(r, [{
                            key: 'dragStart',
                            value: function (t) {
                                if (this.disabled) return !1
                                this.$emit('drag-start')
                            }
                        }, {
                            key: 'render',
                            value: function () {
                                var t = arguments[0]
                                return t('div', {
                                    ref: 'dot',
                                    class: this.dotClasses,
                                    attrs: { 'aria-valuetext': this.tooltipValue },
                                    on: {
                                        mousedown: this.dragStart,
                                        touchstart: this.dragStart
                                    }
                                }, [this.$slots.dot || t('div', {
                                    class: this.handleClasses,
                                    style: this.dotStyle
                                }), 'none' !== this.tooltip ? t('div', { class: this.tooltipClasses }, [this.$slots.tooltip || t('div', {
                                    class: this.tooltipInnerClasses,
                                    style: this.tooltipStyle
                                }, [t('span', { class: 'vue-slider-dot-tooltip-text' }, [this.tooltipValue])])]) : null])
                            }
                        }, {
                            key: 'dotClasses',
                            get: function () {
                                return ['vue-slider-dot', {
                                    'vue-slider-dot-hover': 'hover' === this.tooltip || 'active' === this.tooltip,
                                    'vue-slider-dot-disabled': this.disabled,
                                    'vue-slider-dot-focus': this.focus
                                }]
                            }
                        }, {
                            key: 'handleClasses',
                            get: function () {
                                return ['vue-slider-dot-handle', {
                                    'vue-slider-dot-handle-disabled': this.disabled,
                                    'vue-slider-dot-handle-focus': this.focus
                                }]
                            }
                        }, {
                            key: 'tooltipClasses',
                            get: function () {
                                return ['vue-slider-dot-tooltip', ['vue-slider-dot-tooltip-'.concat(this.tooltipPlacement)], { 'vue-slider-dot-tooltip-show': this.showTooltip }]
                            }
                        }, {
                            key: 'tooltipInnerClasses',
                            get: function () {
                                return ['vue-slider-dot-tooltip-inner', ['vue-slider-dot-tooltip-inner-'.concat(this.tooltipPlacement)], {
                                    'vue-slider-dot-tooltip-inner-disabled': this.disabled,
                                    'vue-slider-dot-tooltip-inner-focus': this.focus
                                }]
                            }
                        }, {
                            key: 'showTooltip',
                            get: function () {
                                switch (this.tooltip) {
                                    case'always':
                                        return !0
                                    case'none':
                                        return !1
                                    case'focus':
                                    case'active':
                                        return !!this.focus
                                    default:
                                        return !1
                                }
                            }
                        }, {
                            key: 'tooltipValue',
                            get: function () {
                                return this.tooltipFormatter ? 'string' === typeof this.tooltipFormatter ? this.tooltipFormatter.replace(/\{value\}/, String(this.value)) : this.tooltipFormatter(this.value) : this.value
                            }
                        }]), r
                    }(c.a)
                    u([v({ default: 0 })], E.prototype, 'value', void 0), u([v()], E.prototype, 'tooltip', void 0), u([v()], E.prototype, 'dotStyle', void 0), u([v()], E.prototype, 'tooltipStyle', void 0), u([v({
                        type: String,
                        validator: function (t) {
                            return ['top', 'right', 'bottom', 'left'].indexOf(t) > -1
                        },
                        required: !0
                    })], E.prototype, 'tooltipPlacement', void 0), u([v({ type: [String, Function] })], E.prototype, 'tooltipFormatter', void 0), u([v({
                        type: Boolean,
                        default: !1
                    })], E.prototype, 'focus', void 0), u([v({ default: !1 })], E.prototype, 'disabled', void 0), E = u([f()({ name: 'VueSliderDot' })], E)
                    var A = E
                    r('556c')

                    function V(t) {
                        return V = 'function' === typeof Symbol && 'symbol' === typeof Symbol.iterator ? function (t) {
                            return typeof t
                        } : function (t) {
                            return t && 'function' === typeof Symbol && t.constructor === Symbol && t !== Symbol.prototype ? 'symbol' : typeof t
                        }, V(t)
                    }

                    function M(t, e) {
                        if (!(t instanceof e)) throw new TypeError('Cannot call a class as a function')
                    }

                    function _(t, e) {
                        for (var r = 0; r < e.length; r++) {
                            var n = e[r]
                            n.enumerable = n.enumerable || !1, n.configurable = !0, 'value' in n && (n.writable = !0), Object.defineProperty(t, n.key, n)
                        }
                    }

                    function C(t, e, r) {
                        return e && _(t.prototype, e), r && _(t, r), t
                    }

                    function I(t, e) {
                        if ('function' !== typeof e && null !== e) throw new TypeError('Super expression must either be null or a function')
                        t.prototype = Object.create(e && e.prototype, {
                            constructor: {
                                value: t,
                                writable: !0,
                                configurable: !0
                            }
                        }), e && L(t, e)
                    }

                    function L(t, e) {
                        return L = Object.setPrototypeOf || function (t, e) {
                            return t.__proto__ = e, t
                        }, L(t, e)
                    }

                    function T(t) {
                        var e = z()
                        return function () {
                            var r,
                                n = H(t)
                            if (e) {
                                var o = H(this).constructor
                                r = Reflect.construct(n, arguments, o)
                            } else {
                                r = n.apply(this, arguments)
                            }
                            return B(this, r)
                        }
                    }

                    function B(t, e) {
                        return !e || 'object' !== V(e) && 'function' !== typeof e ? N(t) : e
                    }

                    function N(t) {
                        if (void 0 === t) throw new ReferenceError('this hasn\'t been initialised - super() hasn\'t been called')
                        return t
                    }

                    function z() {
                        if ('undefined' === typeof Reflect || !Reflect.construct) return !1
                        if (Reflect.construct.sham) return !1
                        if ('function' === typeof Proxy) return !0
                        try {
                            return Date.prototype.toString.call(Reflect.construct(Date, [], (function () {
                            }))), !0
                        } catch (t) {
                            return !1
                        }
                    }

                    function H(t) {
                        return H = Object.setPrototypeOf ? Object.getPrototypeOf : function (t) {
                            return t.__proto__ || Object.getPrototypeOf(t)
                        }, H(t)
                    }

                    var U = function (t) {
                        I(r, t)
                        var e = T(r)

                        function r() {
                            return M(this, r), e.apply(this, arguments)
                        }

                        return C(r, [{
                            key: 'labelClickHandle',
                            value: function (t) {
                                t.stopPropagation(), this.$emit('pressLabel', this.mark.pos)
                            }
                        }, {
                            key: 'render',
                            value: function () {
                                var t = arguments[0],
                                    e = this.mark
                                return t('div', { class: this.marksClasses }, [this.$slots.step || t('div', {
                                    class: this.stepClasses,
                                    style: [this.stepStyle, e.style, e.active ? this.stepActiveStyle : null, e.active ? e.activeStyle : null]
                                }), this.hideLabel ? null : this.$slots.label || t('div', {
                                    class: this.labelClasses,
                                    style: [this.labelStyle, e.labelStyle, e.active ? this.labelActiveStyle : null, e.active ? e.labelActiveStyle : null],
                                    on: { click: this.labelClickHandle }
                                }, [e.label])])
                            }
                        }, {
                            key: 'marksClasses',
                            get: function () {
                                return ['vue-slider-mark', { 'vue-slider-mark-active': this.mark.active }]
                            }
                        }, {
                            key: 'stepClasses',
                            get: function () {
                                return ['vue-slider-mark-step', { 'vue-slider-mark-step-active': this.mark.active }]
                            }
                        }, {
                            key: 'labelClasses',
                            get: function () {
                                return ['vue-slider-mark-label', { 'vue-slider-mark-label-active': this.mark.active }]
                            }
                        }]), r
                    }(c.a)
                    u([v({ required: !0 })], U.prototype, 'mark', void 0), u([v(Boolean)], U.prototype, 'hideLabel', void 0), u([v()], U.prototype, 'stepStyle', void 0), u([v()], U.prototype, 'stepActiveStyle', void 0), u([v()], U.prototype, 'labelStyle', void 0), u([v()], U.prototype, 'labelActiveStyle', void 0), U = u([f()({ name: 'VueSlideMark' })], U)
                    var F,
                        $ = U,
                        W = function (t) {
                            return 'number' === typeof t ? ''.concat(t, 'px') : t
                        },
                        G = function (t) {
                            var e = document.documentElement,
                                r = document.body,
                                n = t.getBoundingClientRect(),
                                o = {
                                    y: n.top + (window.pageYOffset || e.scrollTop) - (e.clientTop || r.clientTop || 0),
                                    x: n.left + (window.pageXOffset || e.scrollLeft) - (e.clientLeft || r.clientLeft || 0)
                                }
                            return o
                        },
                        X = function (t, e, r) {
                            var n = arguments.length > 3 && void 0 !== arguments[3] ? arguments[3] : 1,
                                o = 'targetTouches' in t ? t.targetTouches[0] : t,
                                i = G(e),
                                a = {
                                    x: o.pageX - i.x,
                                    y: o.pageY - i.y
                                }
                            return {
                                x: r ? e.offsetWidth * n - a.x : a.x,
                                y: r ? e.offsetHeight * n - a.y : a.y
                            }
                        };
                    (function (t) {
                        t[t['PAGE_UP'] = 33] = 'PAGE_UP', t[t['PAGE_DOWN'] = 34] = 'PAGE_DOWN', t[t['END'] = 35] = 'END', t[t['HOME'] = 36] = 'HOME', t[t['LEFT'] = 37] = 'LEFT', t[t['UP'] = 38] = 'UP', t[t['RIGHT'] = 39] = 'RIGHT', t[t['DOWN'] = 40] = 'DOWN'
                    })(F || (F = {}))
                    var q = function (t, e) {
                        if (e.hook) {
                            var r = e.hook(t)
                            if ('function' === typeof r) return r
                            if (!r) return null
                        }
                        switch (t.keyCode) {
                            case F.UP:
                                return function (t) {
                                    return 'ttb' === e.direction ? t - 1 : t + 1
                                }
                            case F.RIGHT:
                                return function (t) {
                                    return 'rtl' === e.direction ? t - 1 : t + 1
                                }
                            case F.DOWN:
                                return function (t) {
                                    return 'ttb' === e.direction ? t + 1 : t - 1
                                }
                            case F.LEFT:
                                return function (t) {
                                    return 'rtl' === e.direction ? t + 1 : t - 1
                                }
                            case F.END:
                                return function () {
                                    return e.max
                                }
                            case F.HOME:
                                return function () {
                                    return e.min
                                }
                            case F.PAGE_UP:
                                return function (t) {
                                    return t + 10
                                }
                            case F.PAGE_DOWN:
                                return function (t) {
                                    return t - 10
                                }
                            default:
                                return null
                        }
                    }

                    function K(t, e) {
                        if (!(t instanceof e)) throw new TypeError('Cannot call a class as a function')
                    }

                    function Y(t, e) {
                        for (var r = 0; r < e.length; r++) {
                            var n = e[r]
                            n.enumerable = n.enumerable || !1, n.configurable = !0, 'value' in n && (n.writable = !0), Object.defineProperty(t, n.key, n)
                        }
                    }

                    function J(t, e, r) {
                        return e && Y(t.prototype, e), r && Y(t, r), t
                    }

                    var Q,
                        Z,
                        tt = function () {
                            function t(e) {
                                K(this, t), this.num = e
                            }

                            return J(t, [{
                                key: 'decimal',
                                value: function (t, e) {
                                    var r = this.num,
                                        n = this.getDecimalLen(r),
                                        o = this.getDecimalLen(t),
                                        i = 0
                                    switch (e) {
                                        case'+':
                                            i = this.getExponent(n, o), this.num = (this.safeRoundUp(r, i) + this.safeRoundUp(t, i)) / i
                                            break
                                        case'-':
                                            i = this.getExponent(n, o), this.num = (this.safeRoundUp(r, i) - this.safeRoundUp(t, i)) / i
                                            break
                                        case'*':
                                            this.num = this.safeRoundUp(this.safeRoundUp(r, this.getExponent(n)), this.safeRoundUp(t, this.getExponent(o))) / this.getExponent(n + o)
                                            break
                                        case'/':
                                            i = this.getExponent(n, o), this.num = this.safeRoundUp(r, i) / this.safeRoundUp(t, i)
                                            break
                                        case'%':
                                            i = this.getExponent(n, o), this.num = this.safeRoundUp(r, i) % this.safeRoundUp(t, i) / i
                                            break
                                    }
                                    return this
                                }
                            }, {
                                key: 'plus',
                                value: function (t) {
                                    return this.decimal(t, '+')
                                }
                            }, {
                                key: 'minus',
                                value: function (t) {
                                    return this.decimal(t, '-')
                                }
                            }, {
                                key: 'multiply',
                                value: function (t) {
                                    return this.decimal(t, '*')
                                }
                            }, {
                                key: 'divide',
                                value: function (t) {
                                    return this.decimal(t, '/')
                                }
                            }, {
                                key: 'remainder',
                                value: function (t) {
                                    return this.decimal(t, '%')
                                }
                            }, {
                                key: 'toNumber',
                                value: function () {
                                    return this.num
                                }
                            }, {
                                key: 'getDecimalLen',
                                value: function (t) {
                                    var e = ''.concat(t)
                                        .split('e')
                                    return (''.concat(e[0])
                                        .split('.')[1] || '').length - (e[1] ? +e[1] : 0)
                                }
                            }, {
                                key: 'getExponent',
                                value: function (t, e) {
                                    return Math.pow(10, void 0 !== e ? Math.max(t, e) : t)
                                }
                            }, {
                                key: 'safeRoundUp',
                                value: function (t, e) {
                                    return Math.round(t * e)
                                }
                            }]), t
                        }()

                    function et(t, e) {
                        var r = Object.keys(t)
                        if (Object.getOwnPropertySymbols) {
                            var n = Object.getOwnPropertySymbols(t)
                            e && (n = n.filter((function (e) {
                                return Object.getOwnPropertyDescriptor(t, e).enumerable
                            }))), r.push.apply(r, n)
                        }
                        return r
                    }

                    function rt(t) {
                        for (var e = 1; e < arguments.length; e++) {
                            var r = null != arguments[e] ? arguments[e] : {}
                            e % 2 ? et(Object(r), !0)
                                .forEach((function (e) {
                                    vt(t, e, r[e])
                                })) : Object.getOwnPropertyDescriptors ? Object.defineProperties(t, Object.getOwnPropertyDescriptors(r)) : et(Object(r))
                                .forEach((function (e) {
                                    Object.defineProperty(t, e, Object.getOwnPropertyDescriptor(r, e))
                                }))
                        }
                        return t
                    }

                    function nt(t, e) {
                        return at(t) || it(t, e) || lt(t, e) || ot()
                    }

                    function ot() {
                        throw new TypeError('Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.')
                    }

                    function it(t, e) {
                        if ('undefined' !== typeof Symbol && Symbol.iterator in Object(t)) {
                            var r = [],
                                n = !0,
                                o = !1,
                                i = void 0
                            try {
                                for (var a, s = t[Symbol.iterator](); !(n = (a = s.next()).done); n = !0) if (r.push(a.value), e && r.length === e) break
                            } catch (u) {
                                o = !0, i = u
                            } finally {
                                try {
                                    n || null == s['return'] || s['return']()
                                } finally {
                                    if (o) throw i
                                }
                            }
                            return r
                        }
                    }

                    function at(t) {
                        if (Array.isArray(t)) return t
                    }

                    function st(t) {
                        return dt(t) || ct(t) || lt(t) || ut()
                    }

                    function ut() {
                        throw new TypeError('Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.')
                    }

                    function lt(t, e) {
                        if (t) {
                            if ('string' === typeof t) return ft(t, e)
                            var r = Object.prototype.toString.call(t)
                                .slice(8, -1)
                            return 'Object' === r && t.constructor && (r = t.constructor.name), 'Map' === r || 'Set' === r ? Array.from(t) : 'Arguments' === r || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(r) ? ft(t, e) : void 0
                        }
                    }

                    function ct(t) {
                        if ('undefined' !== typeof Symbol && Symbol.iterator in Object(t)) return Array.from(t)
                    }

                    function dt(t) {
                        if (Array.isArray(t)) return ft(t)
                    }

                    function ft(t, e) {
                        (null == e || e > t.length) && (e = t.length)
                        for (var r = 0, n = new Array(e); r < e; r++) n[r] = t[r]
                        return n
                    }

                    function ht(t, e) {
                        if (!(t instanceof e)) throw new TypeError('Cannot call a class as a function')
                    }

                    function pt(t, e) {
                        for (var r = 0; r < e.length; r++) {
                            var n = e[r]
                            n.enumerable = n.enumerable || !1, n.configurable = !0, 'value' in n && (n.writable = !0), Object.defineProperty(t, n.key, n)
                        }
                    }

                    function yt(t, e, r) {
                        return e && pt(t.prototype, e), r && pt(t, r), t
                    }

                    function vt(t, e, r) {
                        return e in t ? Object.defineProperty(t, e, {
                            value: r,
                            enumerable: !0,
                            configurable: !0,
                            writable: !0
                        }) : t[e] = r, t
                    }

                    (function (t) {
                        t[t['VALUE'] = 1] = 'VALUE', t[t['INTERVAL'] = 2] = 'INTERVAL', t[t['MIN'] = 3] = 'MIN', t[t['MAX'] = 4] = 'MAX', t[t['ORDER'] = 5] = 'ORDER'
                    })(Z || (Z = {}))
                    var mt = (Q = {}, vt(Q, Z.VALUE, 'The type of the "value" is illegal'), vt(Q, Z.INTERVAL, 'The prop "interval" is invalid, "(max - min)" must be divisible by "interval"'), vt(Q, Z.MIN, 'The "value" must be greater than or equal to the "min".'), vt(Q, Z.MAX, 'The "value" must be less than or equal to the "max".'), vt(Q, Z.ORDER, 'When "order" is false, the parameters "minRange", "maxRange", "fixed", "enabled" are invalid.'), Q),
                        bt = function () {
                            function t(e) {
                                ht(this, t), this.dotsPos = [], this.dotsValue = [], this.cacheRangeDir = {}, this.data = e.data, this.max = e.max, this.min = e.min, this.interval = e.interval, this.order = e.order, this.marks = e.marks, this.included = e.included, this.process = e.process, this.adsorb = e.adsorb, this.dotOptions = e.dotOptions, this.onError = e.onError, this.order ? (this.minRange = e.minRange || 0, this.maxRange = e.maxRange || 0, this.enableCross = e.enableCross, this.fixed = e.fixed) : ((e.minRange || e.maxRange || !e.enableCross || e.fixed) && this.emitError(Z.ORDER), this.minRange = 0, this.maxRange = 0, this.enableCross = !0, this.fixed = !1), this.setValue(e.value)
                            }

                            return yt(t, [{
                                key: 'setValue',
                                value: function (t) {
                                    var e = this
                                    this.setDotsValue(Array.isArray(t) ? this.order ? st(t)
                                        .sort((function (t, r) {
                                            return e.getIndexByValue(t) - e.getIndexByValue(r)
                                        })) : st(t) : [t], !0)
                                }
                            }, {
                                key: 'setDotsValue',
                                value: function (t, e) {
                                    this.dotsValue = t, e && this.syncDotsPos()
                                }
                            }, {
                                key: 'setDotsPos',
                                value: function (t) {
                                    var e = this,
                                        r = this.order ? st(t)
                                            .sort((function (t, e) {
                                                return t - e
                                            })) : t
                                    this.dotsPos = r, this.setDotsValue(r.map((function (t) {
                                        return e.getValueByPos(t)
                                    })), this.adsorb)
                                }
                            }, {
                                key: 'getValueByPos',
                                value: function (t) {
                                    var e = this.parsePos(t)
                                    if (this.included) {
                                        var r = 100
                                        this.markList.forEach((function (n) {
                                            var o = Math.abs(n.pos - t)
                                            o < r && (r = o, e = n.value)
                                        }))
                                    }
                                    return e
                                }
                            }, {
                                key: 'syncDotsPos',
                                value: function () {
                                    var t = this
                                    this.dotsPos = this.dotsValue.map((function (e) {
                                        return t.parseValue(e)
                                    }))
                                }
                            }, {
                                key: 'getRecentDot',
                                value: function (t) {
                                    var e = this,
                                        r = this.dotsPos.filter((function (t, r) {
                                            return !(e.getDotOption(r) && e.getDotOption(r).disabled)
                                        }))
                                            .map((function (e) {
                                                return Math.abs(e - t)
                                            }))
                                    return r.indexOf(Math.min.apply(Math, st(r)))
                                }
                            }, {
                                key: 'getIndexByValue',
                                value: function (t) {
                                    return this.data ? this.data.indexOf(t) : new tt(+t).minus(this.min)
                                        .divide(this.interval)
                                        .toNumber()
                                }
                            }, {
                                key: 'getValueByIndex',
                                value: function (t) {
                                    return t < 0 ? t = 0 : t > this.total && (t = this.total), this.data ? this.data[t] : new tt(t).multiply(this.interval)
                                        .plus(this.min)
                                        .toNumber()
                                }
                            }, {
                                key: 'setDotPos',
                                value: function (t, e) {
                                    t = this.getValidPos(t, e).pos
                                    var r = t - this.dotsPos[e]
                                    if (r) {
                                        var n = new Array(this.dotsPos.length)
                                        this.fixed ? n = this.getFixedChangePosArr(r, e) : this.minRange || this.maxRange ? n = this.getLimitRangeChangePosArr(t, r, e) : n[e] = r, this.setDotsPos(this.dotsPos.map((function (t, e) {
                                            return t + (n[e] || 0)
                                        })))
                                    }
                                }
                            }, {
                                key: 'getFixedChangePosArr',
                                value: function (t, e) {
                                    var r = this
                                    return this.dotsPos.forEach((function (n, o) {
                                        if (o !== e) {
                                            var i = r.getValidPos(n + t, o),
                                                a = i.pos,
                                                s = i.inRange
                                            s || (t = Math.min(Math.abs(a - n), Math.abs(t)) * (t < 0 ? -1 : 1))
                                        }
                                    })), this.dotsPos.map((function (e) {
                                        return t
                                    }))
                                }
                            }, {
                                key: 'getLimitRangeChangePosArr',
                                value: function (t, e, r) {
                                    var n = this,
                                        o = [{
                                            index: r,
                                            changePos: e
                                        }],
                                        i = e
                                    return [this.minRange, this.maxRange].forEach((function (a, s) {
                                        if (!a) return !1
                                        var u = 0 === s,
                                            l = e > 0,
                                            c = 0
                                        c = u ? l ? 1 : -1 : l ? -1 : 1
                                        var d = function (t, e) {
                                                var r = Math.abs(t - e)
                                                return u ? r < n.minRangeDir : r > n.maxRangeDir
                                            },
                                            f = r + c,
                                            h = n.dotsPos[f],
                                            p = t
                                        while (n.isPos(h) && d(h, p)) {
                                            var y = n.getValidPos(h + i, f),
                                                v = y.pos
                                            o.push({
                                                index: f,
                                                changePos: v - h
                                            }), f += c, p = v, h = n.dotsPos[f]
                                        }
                                    })), this.dotsPos.map((function (t, e) {
                                        var r = o.filter((function (t) {
                                            return t.index === e
                                        }))
                                        return r.length ? r[0].changePos : 0
                                    }))
                                }
                            }, {
                                key: 'isPos',
                                value: function (t) {
                                    return 'number' === typeof t
                                }
                            }, {
                                key: 'getValidPos',
                                value: function (t, e) {
                                    var r = this.valuePosRange[e],
                                        n = !0
                                    return t < r[0] ? (t = r[0], n = !1) : t > r[1] && (t = r[1], n = !1), {
                                        pos: t,
                                        inRange: n
                                    }
                                }
                            }, {
                                key: 'parseValue',
                                value: function (t) {
                                    if (this.data) {
                                        t = this.data.indexOf(t)
                                    } else if ('number' === typeof t || 'string' === typeof t) {
                                        if (t = +t, t < this.min) return this.emitError(Z.MIN), 0
                                        if (t > this.max) return this.emitError(Z.MAX), 0
                                        if ('number' !== typeof t || t !== t) return this.emitError(Z.VALUE), 0
                                        t = new tt(t).minus(this.min)
                                            .divide(this.interval)
                                            .toNumber()
                                    }
                                    var e = new tt(t).multiply(this.gap)
                                        .toNumber()
                                    return e < 0 ? 0 : e > 100 ? 100 : e
                                }
                            }, {
                                key: 'parsePos',
                                value: function (t) {
                                    var e = Math.round(t / this.gap)
                                    return this.getValueByIndex(e)
                                }
                            }, {
                                key: 'isActiveByPos',
                                value: function (t) {
                                    return this.processArray.some((function (e) {
                                        var r = nt(e, 2),
                                            n = r[0],
                                            o = r[1]
                                        return t >= n && t <= o
                                    }))
                                }
                            }, {
                                key: 'getValues',
                                value: function () {
                                    if (this.data) return this.data
                                    for (var t = [], e = 0; e <= this.total; e++) {
                                        t.push(new tt(e).multiply(this.interval)
                                            .plus(this.min)
                                            .toNumber())
                                    }
                                    return t
                                }
                            }, {
                                key: 'getRangeDir',
                                value: function (t) {
                                    return t ? new tt(t).divide(new tt(this.data ? this.data.length - 1 : this.max).minus(this.data ? 0 : this.min)
                                        .toNumber())
                                        .multiply(100)
                                        .toNumber() : 100
                                }
                            }, {
                                key: 'emitError',
                                value: function (t) {
                                    this.onError && this.onError(t, mt[t])
                                }
                            }, {
                                key: 'getDotOption',
                                value: function (t) {
                                    return Array.isArray(this.dotOptions) ? this.dotOptions[t] : this.dotOptions
                                }
                            }, {
                                key: 'getDotRange',
                                value: function (t, e, r) {
                                    if (!this.dotOptions) return r
                                    var n = this.getDotOption(t)
                                    return n && void 0 !== n[e] ? this.parseValue(n[e]) : r
                                }
                            }, {
                                key: 'markList',
                                get: function () {
                                    var t = this
                                    if (!this.marks) return []
                                    var e = function (e, r) {
                                        var n = t.parseValue(e)
                                        return rt({
                                            pos: n,
                                            value: e,
                                            label: e,
                                            active: t.isActiveByPos(n)
                                        }, r)
                                    }
                                    return !0 === this.marks ? this.getValues()
                                        .map((function (t) {
                                            return e(t)
                                        })) : '[object Object]' === Object.prototype.toString.call(this.marks) ? Object.keys(this.marks)
                                        .sort((function (t, e) {
                                            return +t - +e
                                        }))
                                        .map((function (r) {
                                            var n = t.marks[r]
                                            return e(r, 'string' !== typeof n ? n : { label: n })
                                        })) : Array.isArray(this.marks) ? this.marks.map((function (t) {
                                        return e(t)
                                    })) : 'function' === typeof this.marks ? this.getValues()
                                        .map((function (e) {
                                            return {
                                                value: e,
                                                result: t.marks(e)
                                            }
                                        }))
                                        .filter((function (t) {
                                            var e = t.result
                                            return !!e
                                        }))
                                        .map((function (t) {
                                            var r = t.value,
                                                n = t.result
                                            return e(r, n)
                                        })) : []
                                }
                            }, {
                                key: 'processArray',
                                get: function () {
                                    if (this.process) {
                                        if ('function' === typeof this.process) return this.process(this.dotsPos)
                                        if (1 === this.dotsPos.length) return [[0, this.dotsPos[0]]]
                                        if (this.dotsPos.length > 1) return [[Math.min.apply(Math, st(this.dotsPos)), Math.max.apply(Math, st(this.dotsPos))]]
                                    }
                                    return []
                                }
                            }, {
                                key: 'total',
                                get: function () {
                                    var t = 0
                                    return t = this.data ? this.data.length - 1 : new tt(this.max).minus(this.min)
                                        .divide(this.interval)
                                        .toNumber(), t - Math.floor(t) !== 0 ? (this.emitError(Z.INTERVAL), 0) : t
                                }
                            }, {
                                key: 'gap',
                                get: function () {
                                    return 100 / this.total
                                }
                            }, {
                                key: 'minRangeDir',
                                get: function () {
                                    return this.cacheRangeDir[this.minRange] ? this.cacheRangeDir[this.minRange] : this.cacheRangeDir[this.minRange] = this.getRangeDir(this.minRange)
                                }
                            }, {
                                key: 'maxRangeDir',
                                get: function () {
                                    return this.cacheRangeDir[this.maxRange] ? this.cacheRangeDir[this.maxRange] : this.cacheRangeDir[this.maxRange] = this.getRangeDir(this.maxRange)
                                }
                            }, {
                                key: 'valuePosRange',
                                get: function () {
                                    var t = this,
                                        e = this.dotsPos,
                                        r = []
                                    return e.forEach((function (n, o) {
                                        r.push([Math.max(t.minRange ? t.minRangeDir * o : 0, t.enableCross ? 0 : e[o - 1] || 0, t.getDotRange(o, 'min', 0)), Math.min(t.minRange ? 100 - t.minRangeDir * (e.length - 1 - o) : 100, t.enableCross ? 100 : e[o + 1] || 100, t.getDotRange(o, 'max', 100))])
                                    })), r
                                }
                            }, {
                                key: 'dotsIndex',
                                get: function () {
                                    var t = this
                                    return this.dotsValue.map((function (e) {
                                        return t.getIndexByValue(e)
                                    }))
                                }
                            }]), t
                        }()

                    function gt(t, e) {
                        if (!(t instanceof e)) throw new TypeError('Cannot call a class as a function')
                    }

                    function kt(t, e) {
                        for (var r = 0; r < e.length; r++) {
                            var n = e[r]
                            n.enumerable = n.enumerable || !1, n.configurable = !0, 'value' in n && (n.writable = !0), Object.defineProperty(t, n.key, n)
                        }
                    }

                    function Ot(t, e, r) {
                        return e && kt(t.prototype, e), r && kt(t, r), t
                    }

                    var xt = function () {
                        function t(e) {
                            gt(this, t), this.states = 0, this.map = e
                        }

                        return Ot(t, [{
                            key: 'add',
                            value: function (t) {
                                this.states |= t
                            }
                        }, {
                            key: 'delete',
                            value: function (t) {
                                this.states &= ~t
                            }
                        }, {
                            key: 'toggle',
                            value: function (t) {
                                this.has(t) ? this.delete(t) : this.add(t)
                            }
                        }, {
                            key: 'has',
                            value: function (t) {
                                return !!(this.states & t)
                            }
                        }]), t
                    }()
                    r('4abb')

                    function wt(t, e) {
                        return Dt(t) || Pt(t, e) || Mt(t, e) || St()
                    }

                    function St() {
                        throw new TypeError('Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.')
                    }

                    function Pt(t, e) {
                        if ('undefined' !== typeof Symbol && Symbol.iterator in Object(t)) {
                            var r = [],
                                n = !0,
                                o = !1,
                                i = void 0
                            try {
                                for (var a, s = t[Symbol.iterator](); !(n = (a = s.next()).done); n = !0) if (r.push(a.value), e && r.length === e) break
                            } catch (u) {
                                o = !0, i = u
                            } finally {
                                try {
                                    n || null == s['return'] || s['return']()
                                } finally {
                                    if (o) throw i
                                }
                            }
                            return r
                        }
                    }

                    function Dt(t) {
                        if (Array.isArray(t)) return t
                    }

                    function Rt(t, e) {
                        var r = Object.keys(t)
                        if (Object.getOwnPropertySymbols) {
                            var n = Object.getOwnPropertySymbols(t)
                            e && (n = n.filter((function (e) {
                                return Object.getOwnPropertyDescriptor(t, e).enumerable
                            }))), r.push.apply(r, n)
                        }
                        return r
                    }

                    function jt(t) {
                        for (var e = 1; e < arguments.length; e++) {
                            var r = null != arguments[e] ? arguments[e] : {}
                            e % 2 ? Rt(Object(r), !0)
                                .forEach((function (e) {
                                    Et(t, e, r[e])
                                })) : Object.getOwnPropertyDescriptors ? Object.defineProperties(t, Object.getOwnPropertyDescriptors(r)) : Rt(Object(r))
                                .forEach((function (e) {
                                    Object.defineProperty(t, e, Object.getOwnPropertyDescriptor(r, e))
                                }))
                        }
                        return t
                    }

                    function Et(t, e, r) {
                        return e in t ? Object.defineProperty(t, e, {
                            value: r,
                            enumerable: !0,
                            configurable: !0,
                            writable: !0
                        }) : t[e] = r, t
                    }

                    function At(t) {
                        return Ct(t) || _t(t) || Mt(t) || Vt()
                    }

                    function Vt() {
                        throw new TypeError('Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.')
                    }

                    function Mt(t, e) {
                        if (t) {
                            if ('string' === typeof t) return It(t, e)
                            var r = Object.prototype.toString.call(t)
                                .slice(8, -1)
                            return 'Object' === r && t.constructor && (r = t.constructor.name), 'Map' === r || 'Set' === r ? Array.from(t) : 'Arguments' === r || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(r) ? It(t, e) : void 0
                        }
                    }

                    function _t(t) {
                        if ('undefined' !== typeof Symbol && Symbol.iterator in Object(t)) return Array.from(t)
                    }

                    function Ct(t) {
                        if (Array.isArray(t)) return It(t)
                    }

                    function It(t, e) {
                        (null == e || e > t.length) && (e = t.length)
                        for (var r = 0, n = new Array(e); r < e; r++) n[r] = t[r]
                        return n
                    }

                    function Lt(t) {
                        return Lt = 'function' === typeof Symbol && 'symbol' === typeof Symbol.iterator ? function (t) {
                            return typeof t
                        } : function (t) {
                            return t && 'function' === typeof Symbol && t.constructor === Symbol && t !== Symbol.prototype ? 'symbol' : typeof t
                        }, Lt(t)
                    }

                    function Tt(t, e) {
                        if (!(t instanceof e)) throw new TypeError('Cannot call a class as a function')
                    }

                    function Bt(t, e) {
                        for (var r = 0; r < e.length; r++) {
                            var n = e[r]
                            n.enumerable = n.enumerable || !1, n.configurable = !0, 'value' in n && (n.writable = !0), Object.defineProperty(t, n.key, n)
                        }
                    }

                    function Nt(t, e, r) {
                        return e && Bt(t.prototype, e), r && Bt(t, r), t
                    }

                    function zt(t, e) {
                        if ('function' !== typeof e && null !== e) throw new TypeError('Super expression must either be null or a function')
                        t.prototype = Object.create(e && e.prototype, {
                            constructor: {
                                value: t,
                                writable: !0,
                                configurable: !0
                            }
                        }), e && Ht(t, e)
                    }

                    function Ht(t, e) {
                        return Ht = Object.setPrototypeOf || function (t, e) {
                            return t.__proto__ = e, t
                        }, Ht(t, e)
                    }

                    function Ut(t) {
                        var e = Wt()
                        return function () {
                            var r,
                                n = Gt(t)
                            if (e) {
                                var o = Gt(this).constructor
                                r = Reflect.construct(n, arguments, o)
                            } else {
                                r = n.apply(this, arguments)
                            }
                            return Ft(this, r)
                        }
                    }

                    function Ft(t, e) {
                        return !e || 'object' !== Lt(e) && 'function' !== typeof e ? $t(t) : e
                    }

                    function $t(t) {
                        if (void 0 === t) throw new ReferenceError('this hasn\'t been initialised - super() hasn\'t been called')
                        return t
                    }

                    function Wt() {
                        if ('undefined' === typeof Reflect || !Reflect.construct) return !1
                        if (Reflect.construct.sham) return !1
                        if ('function' === typeof Proxy) return !0
                        try {
                            return Date.prototype.toString.call(Reflect.construct(Date, [], (function () {
                            }))), !0
                        } catch (t) {
                            return !1
                        }
                    }

                    function Gt(t) {
                        return Gt = Object.setPrototypeOf ? Object.getPrototypeOf : function (t) {
                            return t.__proto__ || Object.getPrototypeOf(t)
                        }, Gt(t)
                    }

                    var Xt = {
                            None: 0,
                            Drag: 2,
                            Focus: 4
                        },
                        qt = 4,
                        Kt = function (t) {
                            zt(r, t)
                            var e = Ut(r)

                            function r() {
                                var t
                                return Tt(this, r), t = e.apply(this, arguments), t.states = new xt(Xt), t.scale = 1, t.focusDotIndex = 0, t
                            }

                            return Nt(r, [{
                                key: 'isObjectData',
                                value: function (t) {
                                    return !!t && '[object Object]' === Object.prototype.toString.call(t)
                                }
                            }, {
                                key: 'isObjectArrayData',
                                value: function (t) {
                                    return !!t && Array.isArray(t) && t.length > 0 && 'object' === Lt(t[0])
                                }
                            }, {
                                key: 'onValueChanged',
                                value: function () {
                                    this.control && !this.states.has(Xt.Drag) && this.isNotSync && (this.control.setValue(this.value), this.syncValueByPos())
                                }
                            }, {
                                key: 'created',
                                value: function () {
                                    this.initControl()
                                }
                            }, {
                                key: 'mounted',
                                value: function () {
                                    this.bindEvent()
                                }
                            }, {
                                key: 'beforeDestroy',
                                value: function () {
                                    this.unbindEvent()
                                }
                            }, {
                                key: 'bindEvent',
                                value: function () {
                                    document.addEventListener('touchmove', this.dragMove, { passive: !1 }), document.addEventListener('touchend', this.dragEnd, { passive: !1 }), document.addEventListener('mousedown', this.blurHandle), document.addEventListener('mousemove', this.dragMove, { passive: !1 }), document.addEventListener('mouseup', this.dragEnd), document.addEventListener('mouseleave', this.dragEnd), document.addEventListener('keydown', this.keydownHandle)
                                }
                            }, {
                                key: 'unbindEvent',
                                value: function () {
                                    document.removeEventListener('touchmove', this.dragMove), document.removeEventListener('touchend', this.dragEnd), document.removeEventListener('mousedown', this.blurHandle), document.removeEventListener('mousemove', this.dragMove), document.removeEventListener('mouseup', this.dragEnd), document.removeEventListener('mouseleave', this.dragEnd), document.removeEventListener('keydown', this.keydownHandle)
                                }
                            }, {
                                key: 'setScale',
                                value: function () {
                                    var t = new tt(Math.floor(this.isHorizontal ? this.$refs.rail.offsetWidth : this.$refs.rail.offsetHeight))
                                    void 0 !== this.zoom && t.multiply(this.zoom), t.divide(100), this.scale = t.toNumber()
                                }
                            }, {
                                key: 'initControl',
                                value: function () {
                                    var t = this
                                    this.control = new bt({
                                        value: this.value,
                                        data: this.sliderData,
                                        enableCross: this.enableCross,
                                        fixed: this.fixed,
                                        max: this.max,
                                        min: this.min,
                                        interval: this.interval,
                                        minRange: this.minRange,
                                        maxRange: this.maxRange,
                                        order: this.order,
                                        marks: this.sliderMarks,
                                        included: this.included,
                                        process: this.process,
                                        adsorb: this.adsorb,
                                        dotOptions: this.dotOptions,
                                        onError: this.emitError
                                    }), this.syncValueByPos(), ['data', 'enableCross', 'fixed', 'max', 'min', 'interval', 'minRange', 'maxRange', 'order', 'marks', 'process', 'adsorb', 'included', 'dotOptions'].forEach((function (e) {
                                        t.$watch(e, (function (r) {
                                            if ('data' === e && Array.isArray(t.control.data) && Array.isArray(r) && t.control.data.length === r.length && r.every((function (e, r) {
                                                return e === t.control.data[r]
                                            }))) {
                                                return !1
                                            }
                                            switch (e) {
                                                case'data':
                                                case'dataLabel':
                                                case'dataValue':
                                                    t.control.data = t.sliderData
                                                    break
                                                case'mark':
                                                    t.control.marks = t.sliderMarks
                                                    break
                                                default:
                                                    t.control[e] = r
                                            }
                                            ['data', 'max', 'min', 'interval'].indexOf(e) > -1 && t.control.syncDotsPos()
                                        }))
                                    }))
                                }
                            }, {
                                key: 'syncValueByPos',
                                value: function () {
                                    var t = this.control.dotsValue
                                    this.isDiff(t, Array.isArray(this.value) ? this.value : [this.value]) && this.$emit('change', 1 === t.length ? t[0] : At(t), this.focusDotIndex)
                                }
                            }, {
                                key: 'isDiff',
                                value: function (t, e) {
                                    return t.length !== e.length || t.some((function (t, r) {
                                        return t !== e[r]
                                    }))
                                }
                            }, {
                                key: 'emitError',
                                value: function (t, e) {
                                    this.silent || console.error('[VueSlider error]: '.concat(e)), this.$emit('error', t, e)
                                }
                            }, {
                                key: 'dragStartOnProcess',
                                value: function (t) {
                                    if (this.dragOnClick) {
                                        this.setScale()
                                        var e = this.getPosByEvent(t),
                                            r = this.control.getRecentDot(e)
                                        if (this.dots[r].disabled) return
                                        this.dragStart(r), this.control.setDotPos(e, this.focusDotIndex), this.lazy || this.syncValueByPos()
                                    }
                                }
                            }, {
                                key: 'dragStart',
                                value: function (t) {
                                    this.focusDotIndex = t, this.setScale(), this.states.add(Xt.Drag), this.states.add(Xt.Focus), this.$emit('drag-start', this.focusDotIndex)
                                }
                            }, {
                                key: 'dragMove',
                                value: function (t) {
                                    if (!this.states.has(Xt.Drag)) return !1
                                    t.preventDefault()
                                    var e = this.getPosByEvent(t)
                                    this.isCrossDot(e), this.control.setDotPos(e, this.focusDotIndex), this.lazy || this.syncValueByPos()
                                    var r = this.control.dotsValue
                                    this.$emit('dragging', 1 === r.length ? r[0] : At(r), this.focusDotIndex)
                                }
                            }, {
                                key: 'isCrossDot',
                                value: function (t) {
                                    if (this.canSort) {
                                        var e = this.focusDotIndex,
                                            r = t
                                        if (r > this.dragRange[1] ? (r = this.dragRange[1], this.focusDotIndex++) : r < this.dragRange[0] && (r = this.dragRange[0], this.focusDotIndex--), e !== this.focusDotIndex) {
                                            var n = this.$refs['dot-'.concat(this.focusDotIndex)]
                                            n && n.$el && n.$el.focus(), this.control.setDotPos(r, e)
                                        }
                                    }
                                }
                            }, {
                                key: 'dragEnd',
                                value: function (t) {
                                    var e = this
                                    if (!this.states.has(Xt.Drag)) return !1
                                    setTimeout((function () {
                                        e.lazy && e.syncValueByPos(), e.included && e.isNotSync ? e.control.setValue(e.value) : e.control.syncDotsPos(), e.states.delete(Xt.Drag), e.useKeyboard && !('targetTouches' in t) || e.states.delete(Xt.Focus), e.$emit('drag-end', e.focusDotIndex)
                                    }))
                                }
                            }, {
                                key: 'blurHandle',
                                value: function (t) {
                                    if (!this.states.has(Xt.Focus) || !this.$refs.container || this.$refs.container.contains(t.target)) return !1
                                    this.states.delete(Xt.Focus)
                                }
                            }, {
                                key: 'clickHandle',
                                value: function (t) {
                                    if (!this.clickable || this.disabled) return !1
                                    if (!this.states.has(Xt.Drag)) {
                                        this.setScale()
                                        var e = this.getPosByEvent(t)
                                        this.setValueByPos(e)
                                    }
                                }
                            }, {
                                key: 'focus',
                                value: function () {
                                    var t = arguments.length > 0 && void 0 !== arguments[0] ? arguments[0] : 0
                                    this.states.add(Xt.Focus), this.focusDotIndex = t
                                }
                            }, {
                                key: 'blur',
                                value: function () {
                                    this.states.delete(Xt.Focus)
                                }
                            }, {
                                key: 'getValue',
                                value: function () {
                                    var t = this.control.dotsValue
                                    return 1 === t.length ? t[0] : t
                                }
                            }, {
                                key: 'getIndex',
                                value: function () {
                                    var t = this.control.dotsIndex
                                    return 1 === t.length ? t[0] : t
                                }
                            }, {
                                key: 'setValue',
                                value: function (t) {
                                    this.control.setValue(Array.isArray(t) ? At(t) : [t]), this.syncValueByPos()
                                }
                            }, {
                                key: 'setIndex',
                                value: function (t) {
                                    var e = this,
                                        r = Array.isArray(t) ? t.map((function (t) {
                                            return e.control.getValueByIndex(t)
                                        })) : this.control.getValueByIndex(t)
                                    this.setValue(r)
                                }
                            }, {
                                key: 'setValueByPos',
                                value: function (t) {
                                    var e = this,
                                        r = this.control.getRecentDot(t)
                                    if (this.disabled || this.dots[r].disabled) return !1
                                    this.focusDotIndex = r, this.control.setDotPos(t, r), this.syncValueByPos(), this.useKeyboard && this.states.add(Xt.Focus), setTimeout((function () {
                                        e.included && e.isNotSync ? e.control.setValue(e.value) : e.control.syncDotsPos()
                                    }))
                                }
                            }, {
                                key: 'keydownHandle',
                                value: function (t) {
                                    var e = this
                                    if (!this.useKeyboard || !this.states.has(Xt.Focus)) return !1
                                    var r = this.included && this.marks,
                                        n = q(t, {
                                            direction: this.direction,
                                            max: r ? this.control.markList.length - 1 : this.control.total,
                                            min: 0,
                                            hook: this.keydownHook
                                        })
                                    if (n) {
                                        t.preventDefault()
                                        var o = -1,
                                            i = 0
                                        r ? (this.control.markList.some((function (t, r) {
                                            return t.value === e.control.dotsValue[e.focusDotIndex] && (o = n(r), !0)
                                        })), o < 0 ? o = 0 : o > this.control.markList.length - 1 && (o = this.control.markList.length - 1), i = this.control.markList[o].pos) : (o = n(this.control.getIndexByValue(this.control.dotsValue[this.focusDotIndex])), i = this.control.parseValue(this.control.getValueByIndex(o))), this.isCrossDot(i), this.control.setDotPos(i, this.focusDotIndex), this.syncValueByPos()
                                    }
                                }
                            }, {
                                key: 'getPosByEvent',
                                value: function (t) {
                                    return X(t, this.$refs.rail, this.isReverse, this.zoom)[this.isHorizontal ? 'x' : 'y'] / this.scale
                                }
                            }, {
                                key: 'renderSlot',
                                value: function (t, e, r, n) {
                                    var o = this.$createElement,
                                        i = this.$scopedSlots[t]
                                    return i ? n ? i(e) : o('template', { slot: t }, [i(e)]) : r
                                }
                            }, {
                                key: 'render',
                                value: function () {
                                    var t = this,
                                        e = arguments[0]
                                    return e('div', s()([{
                                        ref: 'container',
                                        class: this.containerClasses,
                                        style: this.containerStyles,
                                        on: {
                                            click: this.clickHandle,
                                            touchstart: this.dragStartOnProcess,
                                            mousedown: this.dragStartOnProcess
                                        }
                                    }, this.$attrs]), [e('div', {
                                        ref: 'rail',
                                        class: 'vue-slider-rail',
                                        style: this.railStyle
                                    }, [this.processArray.map((function (r, n) {
                                        return t.renderSlot('process', r, e('div', {
                                            class: 'vue-slider-process',
                                            key: 'process-'.concat(n),
                                            style: r.style
                                        }), !0)
                                    })), this.sliderMarks ? e('div', { class: 'vue-slider-marks' }, [this.control.markList.map((function (r, n) {
                                        var o
                                        return t.renderSlot('mark', r, e('vue-slider-mark', {
                                            key: 'mark-'.concat(n),
                                            attrs: {
                                                mark: r,
                                                hideLabel: t.hideLabel,
                                                stepStyle: t.stepStyle,
                                                stepActiveStyle: t.stepActiveStyle,
                                                labelStyle: t.labelStyle,
                                                labelActiveStyle: t.labelActiveStyle
                                            },
                                            style: (o = {}, Et(o, t.isHorizontal ? 'height' : 'width', '100%'), Et(o, t.isHorizontal ? 'width' : 'height', t.tailSize), Et(o, t.mainDirection, ''.concat(r.pos, '%')), o),
                                            on: {
                                                pressLabel: function (e) {
                                                    return t.clickable && t.setValueByPos(e)
                                                }
                                            }
                                        }, [t.renderSlot('step', r, null), t.renderSlot('label', r, null)]), !0)
                                    }))]) : null, this.dots.map((function (r, n) {
                                        var o
                                        return e('vue-slider-dot', {
                                            ref: 'dot-'.concat(n),
                                            key: 'dot-'.concat(n),
                                            attrs: jt({
                                                value: r.value,
                                                disabled: r.disabled,
                                                focus: r.focus,
                                                'dot-style': [r.style, r.disabled ? r.disabledStyle : null, r.focus ? r.focusStyle : null],
                                                tooltip: r.tooltip || t.tooltip,
                                                'tooltip-style': [t.tooltipStyle, r.tooltipStyle, r.disabled ? r.tooltipDisabledStyle : null, r.focus ? r.tooltipFocusStyle : null],
                                                'tooltip-formatter': Array.isArray(t.sliderTooltipFormatter) ? t.sliderTooltipFormatter[n] : t.sliderTooltipFormatter,
                                                'tooltip-placement': t.tooltipDirections[n],
                                                role: 'slider',
                                                'aria-valuenow': r.value,
                                                'aria-valuemin': t.min,
                                                'aria-valuemax': t.max,
                                                'aria-orientation': t.isHorizontal ? 'horizontal' : 'vertical',
                                                tabindex: '0'
                                            }, t.dotAttrs),
                                            style: [t.dotBaseStyle, (o = {}, Et(o, t.mainDirection, ''.concat(r.pos, '%')), Et(o, 'transition', ''.concat(t.mainDirection, ' ')
                                                .concat(t.animateTime, 's')), o)],
                                            on: {
                                                'drag-start': function () {
                                                    return t.dragStart(n)
                                                }
                                            },
                                            nativeOn: {
                                                focus: function () {
                                                    return !r.disabled && t.focus(n)
                                                },
                                                blur: function () {
                                                    return t.blur()
                                                }
                                            }
                                        }, [t.renderSlot('dot', r, null), t.renderSlot('tooltip', r, null)])
                                    })), this.renderSlot('default', { value: this.getValue() }, null, !0)])])
                                }
                            }, {
                                key: 'tailSize',
                                get: function () {
                                    return W((this.isHorizontal ? this.height : this.width) || qt)
                                }
                            }, {
                                key: 'containerClasses',
                                get: function () {
                                    return ['vue-slider', ['vue-slider-'.concat(this.direction)], { 'vue-slider-disabled': this.disabled }]
                                }
                            }, {
                                key: 'containerStyles',
                                get: function () {
                                    var t = Array.isArray(this.dotSize) ? this.dotSize : [this.dotSize, this.dotSize],
                                        e = wt(t, 2),
                                        r = e[0],
                                        n = e[1],
                                        o = this.width ? W(this.width) : this.isHorizontal ? 'auto' : W(qt),
                                        i = this.height ? W(this.height) : this.isHorizontal ? W(qt) : 'auto'
                                    return {
                                        padding: this.contained ? ''.concat(n / 2, 'px ')
                                            .concat(r / 2, 'px') : this.isHorizontal ? ''.concat(n / 2, 'px 0') : '0 '.concat(r / 2, 'px'),
                                        width: o,
                                        height: i
                                    }
                                }
                            }, {
                                key: 'processArray',
                                get: function () {
                                    var t = this
                                    return this.control.processArray.map((function (e, r) {
                                        var n,
                                            o = wt(e, 3),
                                            i = o[0],
                                            a = o[1],
                                            s = o[2]
                                        if (i > a) {
                                            var u = [a, i]
                                            i = u[0], a = u[1]
                                        }
                                        var l = t.isHorizontal ? 'width' : 'height'
                                        return {
                                            start: i,
                                            end: a,
                                            index: r,
                                            style: jt(jt((n = {}, Et(n, t.isHorizontal ? 'height' : 'width', '100%'), Et(n, t.isHorizontal ? 'top' : 'left', 0), Et(n, t.mainDirection, ''.concat(i, '%')), Et(n, l, ''.concat(a - i, '%')), Et(n, 'transitionProperty', ''.concat(l, ',')
                                                .concat(t.mainDirection)), Et(n, 'transitionDuration', ''.concat(t.animateTime, 's')), n), t.processStyle), s)
                                        }
                                    }))
                                }
                            }, {
                                key: 'dotBaseStyle',
                                get: function () {
                                    var t,
                                        e = Array.isArray(this.dotSize) ? this.dotSize : [this.dotSize, this.dotSize],
                                        r = wt(e, 2),
                                        n = r[0],
                                        o = r[1]
                                    return t = this.isHorizontal ? Et({
                                        transform: 'translate('.concat(this.isReverse ? '50%' : '-50%', ', -50%)'),
                                        '-WebkitTransform': 'translate('.concat(this.isReverse ? '50%' : '-50%', ', -50%)'),
                                        top: '50%'
                                    }, 'ltr' === this.direction ? 'left' : 'right', '0') : Et({
                                        transform: 'translate(-50%, '.concat(this.isReverse ? '50%' : '-50%', ')'),
                                        '-WebkitTransform': 'translate(-50%, '.concat(this.isReverse ? '50%' : '-50%', ')'),
                                        left: '50%'
                                    }, 'btt' === this.direction ? 'bottom' : 'top', '0'), jt({
                                        width: ''.concat(n, 'px'),
                                        height: ''.concat(o, 'px')
                                    }, t)
                                }
                            }, {
                                key: 'mainDirection',
                                get: function () {
                                    switch (this.direction) {
                                        case'ltr':
                                            return 'left'
                                        case'rtl':
                                            return 'right'
                                        case'btt':
                                            return 'bottom'
                                        case'ttb':
                                            return 'top'
                                    }
                                }
                            }, {
                                key: 'isHorizontal',
                                get: function () {
                                    return 'ltr' === this.direction || 'rtl' === this.direction
                                }
                            }, {
                                key: 'isReverse',
                                get: function () {
                                    return 'rtl' === this.direction || 'btt' === this.direction
                                }
                            }, {
                                key: 'tooltipDirections',
                                get: function () {
                                    var t = this.tooltipPlacement || (this.isHorizontal ? 'top' : 'left')
                                    return Array.isArray(t) ? t : this.dots.map((function () {
                                        return t
                                    }))
                                }
                            }, {
                                key: 'dots',
                                get: function () {
                                    var t = this
                                    return this.control.dotsPos.map((function (e, r) {
                                        return jt({
                                            pos: e,
                                            index: r,
                                            value: t.control.dotsValue[r],
                                            focus: t.states.has(Xt.Focus) && t.focusDotIndex === r,
                                            disabled: t.disabled,
                                            style: t.dotStyle
                                        }, (Array.isArray(t.dotOptions) ? t.dotOptions[r] : t.dotOptions) || {})
                                    }))
                                }
                            }, {
                                key: 'animateTime',
                                get: function () {
                                    return this.states.has(Xt.Drag) ? 0 : this.duration
                                }
                            }, {
                                key: 'canSort',
                                get: function () {
                                    return this.order && !this.minRange && !this.maxRange && !this.fixed && this.enableCross
                                }
                            }, {
                                key: 'sliderData',
                                get: function () {
                                    var t = this
                                    return this.isObjectArrayData(this.data) ? this.data.map((function (e) {
                                        return e[t.dataValue]
                                    })) : this.isObjectData(this.data) ? Object.keys(this.data) : this.data
                                }
                            }, {
                                key: 'sliderMarks',
                                get: function () {
                                    var t = this
                                    return this.marks ? this.marks : this.isObjectArrayData(this.data) ? function (e) {
                                        var r = { label: e }
                                        return t.data.some((function (n) {
                                            return n[t.dataValue] === e && (r.label = n[t.dataLabel], !0)
                                        })), r
                                    } : this.isObjectData(this.data) ? this.data : void 0
                                }
                            }, {
                                key: 'sliderTooltipFormatter',
                                get: function () {
                                    var t = this
                                    if (this.tooltipFormatter) return this.tooltipFormatter
                                    if (this.isObjectArrayData(this.data)) {
                                        return function (e) {
                                            var r = '' + e
                                            return t.data.some((function (n) {
                                                return n[t.dataValue] === e && (r = n[t.dataLabel], !0)
                                            })), r
                                        }
                                    }
                                    if (this.isObjectData(this.data)) {
                                        var e = this.data
                                        return function (t) {
                                            return e[t]
                                        }
                                    }
                                }
                            }, {
                                key: 'isNotSync',
                                get: function () {
                                    var t = this.control.dotsValue
                                    return Array.isArray(this.value) ? this.value.length !== t.length || this.value.some((function (e, r) {
                                        return e !== t[r]
                                    })) : this.value !== t[0]
                                }
                            }, {
                                key: 'dragRange',
                                get: function () {
                                    var t = this.dots[this.focusDotIndex - 1],
                                        e = this.dots[this.focusDotIndex + 1]
                                    return [t ? t.pos : -1 / 0, e ? e.pos : 1 / 0]
                                }
                            }]), r
                        }(c.a)
                    u([y('change', { default: 0 })], Kt.prototype, 'value', void 0), u([v({
                        type: Boolean,
                        default: !1
                    })], Kt.prototype, 'silent', void 0), u([v({
                        default: 'ltr',
                        validator: function (t) {
                            return ['ltr', 'rtl', 'ttb', 'btt'].indexOf(t) > -1
                        }
                    })], Kt.prototype, 'direction', void 0), u([v({ type: [Number, String] })], Kt.prototype, 'width', void 0), u([v({ type: [Number, String] })], Kt.prototype, 'height', void 0), u([v({ default: 14 })], Kt.prototype, 'dotSize', void 0), u([v({ default: !1 })], Kt.prototype, 'contained', void 0), u([v({
                        type: Number,
                        default: 0
                    })], Kt.prototype, 'min', void 0), u([v({
                        type: Number,
                        default: 100
                    })], Kt.prototype, 'max', void 0), u([v({
                        type: Number,
                        default: 1
                    })], Kt.prototype, 'interval', void 0), u([v({
                        type: Boolean,
                        default: !1
                    })], Kt.prototype, 'disabled', void 0), u([v({
                        type: Boolean,
                        default: !0
                    })], Kt.prototype, 'clickable', void 0), u([v({
                        type: Boolean,
                        default: !1
                    })], Kt.prototype, 'dragOnClick', void 0), u([v({
                        type: Number,
                        default: .5
                    })], Kt.prototype, 'duration', void 0), u([v({ type: [Object, Array] })], Kt.prototype, 'data', void 0), u([v({
                        type: String,
                        default: 'value'
                    })], Kt.prototype, 'dataValue', void 0), u([v({
                        type: String,
                        default: 'label'
                    })], Kt.prototype, 'dataLabel', void 0), u([v({
                        type: Boolean,
                        default: !1
                    })], Kt.prototype, 'lazy', void 0), u([v({
                        type: String,
                        validator: function (t) {
                            return ['none', 'always', 'focus', 'hover', 'active'].indexOf(t) > -1
                        },
                        default: 'active'
                    })], Kt.prototype, 'tooltip', void 0), u([v({
                        type: [String, Array],
                        validator: function (t) {
                            return (Array.isArray(t) ? t : [t]).every((function (t) {
                                return ['top', 'right', 'bottom', 'left'].indexOf(t) > -1
                            }))
                        }
                    })], Kt.prototype, 'tooltipPlacement', void 0), u([v({ type: [String, Array, Function] })], Kt.prototype, 'tooltipFormatter', void 0), u([v({
                        type: Boolean,
                        default: !0
                    })], Kt.prototype, 'useKeyboard', void 0), u([v(Function)], Kt.prototype, 'keydownHook', void 0), u([v({
                        type: Boolean,
                        default: !0
                    })], Kt.prototype, 'enableCross', void 0), u([v({
                        type: Boolean,
                        default: !1
                    })], Kt.prototype, 'fixed', void 0), u([v({
                        type: Boolean,
                        default: !0
                    })], Kt.prototype, "order", void 0), u([v(Number)], Kt.prototype, "minRange", void 0), u([v(Number)], Kt.prototype, "maxRange", void 0), u([v({
                        type: [Boolean, Object, Array, Function],
                        default: !1
                    })], Kt.prototype, "marks", void 0), u([v({
                        type: [Boolean, Function],
                        default: !0
                    })], Kt.prototype, "process", void 0), u([v({ type: [Number] })], Kt.prototype, "zoom", void 0), u([v(Boolean)], Kt.prototype, "included", void 0), u([v(Boolean)], Kt.prototype, "adsorb", void 0), u([v(Boolean)], Kt.prototype, "hideLabel", void 0), u([v()], Kt.prototype, "dotOptions", void 0), u([v()], Kt.prototype, "dotAttrs", void 0), u([v()], Kt.prototype, "railStyle", void 0), u([v()], Kt.prototype, "processStyle", void 0), u([v()], Kt.prototype, "dotStyle", void 0), u([v()], Kt.prototype, "tooltipStyle", void 0), u([v()], Kt.prototype, "stepStyle", void 0), u([v()], Kt.prototype, "stepActiveStyle", void 0), u([v()], Kt.prototype, "labelStyle", void 0), u([v()], Kt.prototype, "labelActiveStyle", void 0), u([m("value")], Kt.prototype, "onValueChanged", null), Kt = u([f()({
                        name: "VueSlider",
                        data: function () {
                            return { control: null }
                        },
                        components: {
                            VueSliderDot: A,
                            VueSliderMark: $
                        }
                    })], Kt);
                    var Yt = Kt;
                    Yt.VueSliderMark = $, Yt.VueSliderDot = A;
                    var Jt = Yt;
                    e["default"] = Jt
                }
            })["default"]
        }));
//# sourceMappingURL=vue-slider-component.umd.min.js.map

        /***/
    })

}]);
