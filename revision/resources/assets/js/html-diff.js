!(function () {
    var e, n, t, r, i, _, f, o, a, s, u, h, l, c, d, b, p;
    (a = function (e) {
        return ">" === e;
    }),
        (s = function (e) {
            return "<" === e;
        }),
        (h = function (e) {
            return /^\s+$/.test(e);
        }),
        (u = function (e) {
            return /^\s*<[^>]+>\s*$/.test(e);
        }),
        (l = function (e) {
            return !u(e);
        }),
        (e = function (e, n, t) {
            (this.start_in_before = e),
                (this.start_in_after = n),
                (this.length = t),
                (this.end_in_before = this.start_in_before + this.length - 1),
                (this.end_in_after = this.start_in_after + this.length - 1);
        }),
        (o = function (e) {
            var n, t, r, i, _, f;
            for (_ = "char", t = "", f = [], r = 0, i = e.length; r < i; r++)
                switch (((n = e[r]), _)) {
                    case "tag":
                        a(n)
                            ? ((t += ">"),
                              f.push(t),
                              (t = ""),
                              (_ = h(n) ? "whitespace" : "char"))
                            : (t += n);
                        break;
                    case "char":
                        s(n)
                            ? (t && f.push(t), (t = "<"), (_ = "tag"))
                            : /\s/.test(n)
                            ? (t && f.push(t), (t = n), (_ = "whitespace"))
                            : /[\w\#@]+/i.test(n)
                            ? (t += n)
                            : (t && f.push(t), (t = n));
                        break;
                    case "whitespace":
                        s(n)
                            ? (t && f.push(t), (t = "<"), (_ = "tag"))
                            : h(n)
                            ? (t += n)
                            : (t && f.push(t), (t = n), (_ = "char"));
                        break;
                    default:
                        throw new Error("Unknown mode " + _);
                }
            return t && f.push(t), f;
        }),
        (_ = function (n, t, r, i, _, f, o) {
            var a, s, u, h, l, c, d, b, p, g, w, v, m, k, y;
            for (
                s = i, a = f, u = 0, w = {}, c = h = k = i, y = _;
                k <= y ? h < y : h > y;
                c = k <= y ? ++h : --h
            ) {
                for (m = {}, d = 0, b = (p = r[n[c]]).length; d < b; d++)
                    if (!((l = p[d]) < f)) {
                        if (l >= o) break;
                        null == w[l - 1] && (w[l - 1] = 0),
                            (v = w[l - 1] + 1),
                            (m[l] = v),
                            v > u &&
                                ((s = c - v + 1), (a = l - v + 1), (u = v));
                    }
                w = m;
            }
            return 0 !== u && (g = new e(s, a, u)), g;
        }),
        (d = function (e, n, t, r, i, f, o, a) {
            var s;
            return (
                null != (s = _(e, 0, t, r, i, f, o)) &&
                    (r < s.start_in_before &&
                        f < s.start_in_after &&
                        d(
                            e,
                            n,
                            t,
                            r,
                            s.start_in_before,
                            f,
                            s.start_in_after,
                            a
                        ),
                    a.push(s),
                    s.end_in_before <= i &&
                        s.end_in_after <= o &&
                        d(
                            e,
                            n,
                            t,
                            s.end_in_before + 1,
                            i,
                            s.end_in_after + 1,
                            o,
                            a
                        )),
                a
            );
        }),
        (r = function (e) {
            var n, t, r, i, _, f;
            if (null == e.find_these)
                throw new Error("params must have find_these key");
            if (null == e.in_these)
                throw new Error("params must have in_these key");
            for (r = {}, n = 0, i = (_ = e.find_these).length; n < i; n++)
                for (r[(f = _[n])] = [], t = e.in_these.indexOf(f); -1 !== t; )
                    r[f].push(t), (t = e.in_these.indexOf(f, t + 1));
            return r;
        }),
        (f = function (e, n) {
            var t, i;
            return (
                (i = []),
                (t = r({ find_these: e, in_these: n })),
                d(e, n, t, 0, e.length, 0, n.length, i)
            );
        }),
        (n = function (n, t) {
            var r, i, _, o, a, s, u, h, l, c, d, b, p, g, w, v;
            if (null == n) throw new Error("before_tokens?");
            if (null == t) throw new Error("after_tokens?");
            for (
                w = g = 0,
                    p = [],
                    r = {
                        "false,false": "replace",
                        "true,false": "insert",
                        "false,true": "delete",
                        "true,true": "none",
                    },
                    (d = f(n, t)).push(new e(n.length, t.length, 0)),
                    o = _ = 0,
                    h = d.length;
                _ < h;
                o = ++_
            )
                "none" !==
                    (i =
                        r[
                            [
                                w === (c = d[o]).start_in_before,
                                g === c.start_in_after,
                            ].toString()
                        ]) &&
                    p.push({
                        action: i,
                        start_in_before: w,
                        end_in_before:
                            "insert" !== i ? c.start_in_before - 1 : void 0,
                        start_in_after: g,
                        end_in_after:
                            "delete" !== i ? c.start_in_after - 1 : void 0,
                    }),
                    0 !== c.length &&
                        p.push({
                            action: "equal",
                            start_in_before: c.start_in_before,
                            end_in_before: c.end_in_before,
                            start_in_after: c.start_in_after,
                            end_in_after: c.end_in_after,
                        }),
                    (w = c.end_in_before + 1),
                    (g = c.end_in_after + 1);
            for (
                v = [],
                    u = { action: "none" },
                    a = function (e) {
                        return (
                            "equal" === e.action &&
                            e.end_in_before - e.start_in_before == 0 &&
                            /^\s$/.test(
                                n.slice(
                                    e.start_in_before,
                                    +e.end_in_before + 1 || 9e9
                                )
                            )
                        );
                    },
                    s = 0,
                    l = p.length;
                s < l;
                s++
            )
                (a((b = p[s])) && "replace" === u.action) ||
                ("replace" === b.action && "replace" === u.action)
                    ? ((u.end_in_before = b.end_in_before),
                      (u.end_in_after = b.end_in_after))
                    : (v.push(b), (u = b));
            return v;
        }),
        (t = function (e, n, t) {
            var r, i, _, f, o;
            for (
                f = void 0,
                    _ = i = 0,
                    o = (n = n.slice(e, +n.length + 1 || 9e9)).length;
                i < o && (!0 === (r = t(n[_])) && (f = _), !1 !== r);
                _ = ++i
            );
            return null != f ? n.slice(0, +f + 1 || 9e9) : [];
        }),
        (p = function (e, n) {
            var r, i, _, f, o;
            for (
                f = "", _ = 0, r = n.length;
                !(
                    _ >= r ||
                    ((_ += (i = t(_, n, l)).length),
                    0 !== i.length &&
                        (f += "<" + e + ">" + i.join("") + "</" + e + ">"),
                    _ >= r)
                );

            )
                (_ += (o = t(_, n, u)).length), (f += o.join(""));
            return f;
        }),
        ((c = {
            equal: function (e, n, t) {
                return n
                    .slice(e.start_in_before, +e.end_in_before + 1 || 9e9)
                    .join("");
            },
            insert: function (e, n, t) {
                var r;
                return (
                    (r = t.slice(e.start_in_after, +e.end_in_after + 1 || 9e9)),
                    p("ins", r)
                );
            },
            delete: function (e, n, t) {
                var r;
                return (
                    (r = n.slice(
                        e.start_in_before,
                        +e.end_in_before + 1 || 9e9
                    )),
                    p("del", r)
                );
            },
        }).replace = function (e, n, t) {
            return c.delete(e, n, t) + c.insert(e, n, t);
        }),
        (b = function (e, n, t) {
            var r, i, _, f;
            for (f = "", r = 0, i = t.length; r < i; r++)
                (_ = t[r]), (f += c[_.action](_, e, n));
            return f;
        }),
        ((i = function (e, t) {
            var r;
            return e === t
                ? e
                : ((e = o(e)), (t = o(t)), (r = n(e, t)), b(e, t, r));
        }).html_to_tokens = o),
        (i.find_matching_blocks = f),
        (f.find_match = _),
        (f.create_index = r),
        (i.calculate_operations = n),
        (i.render_operations = b),
        "function" == typeof define
            ? define([], function () {
                  return i;
              })
            : "undefined" != typeof module && null !== module
            ? (module.exports = i)
            : "undefined" != typeof window && (window.htmldiff = i);
})();
