//classe para o cookie

export default class Cookie {
    constructor() {
    }

    set(name, value, days) {
        var expires = ""
        if (days) {
            var date = new Date()
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000))
            expires = "; expires=" + date.toUTCString()
        }
        const domain = window.location.hostname
        document.cookie = `${name}=${value};expires=${expires.toUTCString()};path=/;domain=${domain}`;
    }

    get(name) {
        var nameEQ = name + "="
        var ca = document.cookie.split(';')
        for (var i = 0; i < ca.length; i++) {
            var c = ca[i]
            while (c.charAt(0) === ' ') c = c.substring(1, c.length)
            if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length, c.length)
        }
        return null
    }

    erase(name) {
        document.cookie = name + '=; Max-Age=-99999999;'
    }
}
